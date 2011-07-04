<?php

defined('SYS_ROOT') or die('Access Denied!');

class hcaptcha_api
{
    public static $instance = null;
    
    public static function getImgUrl()
    {
        if (self::$instance === null) {
            self::$instance = new hcaptcha_image();
        }
    
        return self::$instance->getImage();
    }
    
    public static function isValid($word)
    {        
        if (self::$instance === null) {
            self::$instance = new hcaptcha_image();
        }

        return self::$instance->isValid($word);
    }
}

class hcaptcha_image
{
    /**
     * @var array Character sets
     */
    static $cs = array("a", "e", "i", "u", "y",
        "b","c","d","f","g","h","j","k","m","n","p","r","s","t","u","v","w","x","z",
        "2","3","4","5","6","7","8","9");
    
    protected $verifyKey;
    protected $verifyValue;
    protected $verifyString;
    
    protected $imageDir = null;

    protected $fontPath = null;
    protected $fontSize = 20;
    
    protected $cleanDays = 1;
    protected $cleanRand = 10;
    
    /**
     * Class constructor
     *
     * @access public
     * @param $db Internal database class pointer
     */
    public function hcaptcha_image()
    {
        $this->fontPath = SYS_ROOT .'/app/hcaptcha/fonts/coolveti/coolveti.ttf';
        //$this->fontPath = SYS_ROOT .'/app/hcaptcha/fonts/ttf-lyx/cmr10.ttf';
        
        $this->imageDir = SYS_ROOT .'/pub/data/captcha';
        //$this->db = new Kit_Db_Table_Verifycode();
    }
    
    /**
     * Get verify image's path
     *
     * @access public
     * @return string 
     */
    public function getImage()
    {
        $verifyKey = $this->_getWord();
        $this->verifyKey = $verifyKey;
        $gc = Hooto_Config_Array::get('global');

        return $gc['captchaurl'].'/'.$verifyKey.'.png';
    }
    
    /**
     * Check it
     *
     * @access public
     * @param string $word
     */
    public function isValid($word)
    {
        $verifyKey = $this->_getWord();
        $verifyValue = md5($word.$verifyKey);
        $db = Hooto_Data_Sql::getTable('hcaptcha');
        $ret = $db->fetch($verifyKey);

        $return = false;
        
        if (isset($ret['value']) && $ret['value'] == $verifyValue) {
            $return = true;
        }
        
        $this->_refresh($verifyKey);

        return $return;
    }

    /**
     * Get word verifyKey
     *
     * @access private
     * @return string $verifyKey
     */
    private function _getWord()
    {
        $verifyKey = @trim(addslashes($_COOKIE['hcaptcha_key']));
        if (strlen($verifyKey) != 32) {
            $verifyKey = @trim(addslashes($_SESSION['hcaptcha_key']));
        }

        if (strlen($verifyKey) != 32 || !file_exists($this->imageDir ."/$verifyKey.png")) {
            $verifyKey = $this->_createKey();
        }
        
        return $verifyKey;
    }
    
    /**
     * Create word
     *
     * @access private
     * @return string $verifyKey
     */
    private function _createKey()
    {
        $verifyKey      = md5(time().mt_rand());
        $verifyString   = '';
        for ($i = 0; $i < rand(4,6); $i++) {
            $verifyString .= self::$cs[array_rand(self::$cs)];
        }
        $verifyValue    = md5($verifyString.$verifyKey);
        
        $data = array(
            'id' => $verifyKey,
            'value' => $verifyValue,
            'created' => time()
        );
        $db = Hooto_Data_Sql::getTable('hcaptcha');
        $db->insert($data);

        @setcookie("hcaptcha_key", $verifyKey, time() + 36000, '/');
        //$_SESSION['hcaptcha_key'] = $verifyKey;
        
        $this->_createImage($verifyKey, $verifyString);

        
        if (time() % $this->cleanRand == 0) {
            $this->_autoClean();
        }
        
        return $verifyKey;
    }
    
    /**
     * Delete current word record in database and create new
     *
     * @access private
     * @param string $verifyKey
     */
    private function _refresh($verifyKey)
    {
        $db = Hooto_Data_Sql::getTable('hcaptcha');
        $db->delete($verifyKey);

        @setcookie("hcaptcha_key", "", time()-36000, '/');
        $_SESSION['hcaptcha_key'] = '';
        @unlink($this->imageDir ."/$verifyKey.png");

        $verifyKey = $this->_createKey();

        return true;
    }
    
       
    /**
     * Generate image captcha
     *
     * Override this function if you want different image generator
     * Wave transform from http://www.captcha.ru/captchas/multiwave/
     *
     * @param string $id Captcha ID
     * @param string $word Captcha word
     */
    private function _createImage($verifyKey, $word)
    {        
        $font  = $this->fontPath;
        
        $w     = 200;
        $h     = 80;
        $fsize = 30;
        
        $img_file   = $this->imageDir ."/$verifyKey.png";

        $img        = imagecreatetruecolor($w, $h);
        
        $text_color = imagecolorallocate($img, 0, 0, 0);
        $bg_color   = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w-1, $h-1, $bg_color);
        $textbox = imageftbbox($fsize, 0, $font, $word);
        $x = ($w - ($textbox[2] - $textbox[0])) / 2;
        $y = ($h - ($textbox[7] - $textbox[1])) / 2;
        imagefttext($img, $fsize, 0, $x, $y, $text_color, $font, $word);

        // transformed image
        $img2     = imagecreatetruecolor($w, $h);
        $bg_color = imagecolorallocate($img2, 255, 255, 255);
        imagefilledrectangle($img2, 0, 0, $w-1, $h-1, $bg_color);
        // apply wave transforms
        $freq1 = $this->_randomFreq();
        $freq2 = $this->_randomFreq();
        $freq3 = $this->_randomFreq();
        $freq4 = $this->_randomFreq();

        $ph1 = $this->_randomPhase();
        $ph2 = $this->_randomPhase();
        $ph3 = $this->_randomPhase();
        $ph4 = $this->_randomPhase();

        $szx = $this->_randomSize();
        $szy = $this->_randomSize();

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $sx = $x + (sin($x*$freq1 + $ph1) + sin($y*$freq3 + $ph3)) * $szx;
                $sy = $y + (sin($x*$freq2 + $ph2) + sin($y*$freq4 + $ph4)) * $szy;

                if ($sx < 0 || $sy < 0 || $sx >= $w - 1 || $sy >= $h - 1) {
                    continue;
                } else {
                    $color    = (imagecolorat($img, $sx, $sy) >> 16)         & 0xFF;
                    $color_x  = (imagecolorat($img, $sx + 1, $sy) >> 16)     & 0xFF;
                    $color_y  = (imagecolorat($img, $sx, $sy + 1) >> 16)     & 0xFF;
                    $color_xy = (imagecolorat($img, $sx + 1, $sy + 1) >> 16) & 0xFF;
                }
                if ($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255) {
                    // ignore background
                    continue;
                } elseif ($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0) {
                    // transfer inside of the image as-is
                    $newcolor = 0;
                } else {
                    // do antialiasing for border items
                    $frac_x  = $sx-floor($sx);
                    $frac_y  = $sy-floor($sy);
                    $frac_x1 = 1-$frac_x;
                    $frac_y1 = 1-$frac_y;

                    $newcolor = $color    * $frac_x1 * $frac_y1
                              + $color_x  * $frac_x  * $frac_y1
                              + $color_y  * $frac_x1 * $frac_y
                              + $color_xy * $frac_x  * $frac_y;
                }
                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newcolor, $newcolor, $newcolor));
            }
        }

        imagepng($img2, $img_file);
        imagedestroy($img);
        imagedestroy($img2);        
    }
    
    /**
     * Generate random frequency
     *
     * @return float
     */
    protected function _randomFreq()
    {
        return mt_rand(700000, 1000000) / 15000000;
    }

    /**
     * Generate random phase
     *
     * @return float
     */
    protected function _randomPhase()
    {
        // random phase from 0 to pi
        return mt_rand(0, 3141592) / 1000000;
    }

    /**
     * Generate random character size
     *
     * @return int
     */
    protected function _randomSize()
    {
        return mt_rand(300, 700) / 100;
    }
    
    
    /**
     * Clean images timing
     *
     * @access private
     */
    private function _autoClean()
    {
        // clean database tmp
        $cleanTime = time() - $this->cleanDays * 24 * 60 * 60;
        
        $db = Hooto_Data_Sql::getTable('hcaptcha');
        $db->deleteWhere(array('created < ?' => $cleanTime));
        
        // clean file tmp
        @$pDir = dir($this->imageDir);
        while (false !== ($fileName = $pDir->read())) {
            if ($fileName == '.' || $fileName == '..') {
                continue;
            }
            $filePath = $this->imageDir ."/". $fileName;
            if (is_dir($filePath)) {
                   continue;
            }
            if ((time() - @fileatime($filePath)) > ($this->cleanDays * 24 * 60 * 60)) {
                @unlink ($filePath);
            }
        }
        @$pDir->close();
    }
}
