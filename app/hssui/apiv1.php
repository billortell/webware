<?php

/**
 * hssui_media
 *
 */
class hssui_apiv1
{
    // resize thumbnail
    const RESIZE_THUMBNAIL_KEY = 'thumb';
    const RESIZE_THUMBNAIL_WIDTH = '150';
    const RESIZE_THUMBNAIL_HEIGHT = '150';
    const RESIZE_THUMBNAIL_CROP = '1';

    // resize medium
    const RESIZE_MEDIUM_KEY = 'medium';
    const RESIZE_MEDIUM_WIDTH = '400';
    const RESIZE_MEDIUM_HEIGHT = '400';
    const RESIZE_MEDIUM_CROP = '0';

    // resize large    
    const RESIZE_LARGE_KEY = 'large';
    const RESIZE_LARGE_WIDTH = '800';
    const RESIZE_LARGE_HEIGHT = '800';
    const RESIZE_LARGE_CROP = '0';
    
    // resize full
    const RESIZE_FULL_KEY = '';
    const RESIZE_FULL_WIDTH = '0';
    const RESIZE_FULL_HEIGHT = '0';
    const RESIZE_FULL_CROP = '0';
    
    // _cfg
    protected $_cfg;
    
    // uploadDir
    protected $_uploadDir = 'data';
    
    // uploadMediaDir
    protected $_uploadMediaDir = 'Y/m/d';
    
    // maxSize
    protected $_maxSize = '500000';
    
    // extLimit
    protected $_extLimit = array(
        'zip', 'rar', 'gz', 'png', 'jpg', 'gif'
    );
    
    // imageExtLimit
    protected $_imageExtLimit = array(
        'png', 'jpg', 'gif'
    );
    
    //
    protected $_fieldName = 'attachment';


    public function hssui_apiv1()
    {
        $this->_cfg = Hooto_Config_Array::get('hssui/global');
        $this->_cfg = new Hooto_Object($this->_cfg['v1']);
        
        
        if (isset($this->_cfg->uploadDir) && strlen($this->_cfg->uploadDir)) {
            $this->_uploadDir = $this->_cfg->uploadDir;
        }
        if (isset($this->_cfg->uploadMediaDir) && strlen($this->_cfg->uploadMediaDir)) {
            $this->_uploadMediaDir = $this->_cfg->uploadMediaDir;
        }
        if (isset($this->_cfg->maxSize) && strlen($this->_cfg->maxSize)) {
            $this->_maxSize = $this->_cfg->maxSize;
        }
        if (isset($this->_cfg->extLimit) && strlen($this->_cfg->extLimit)) {
            $this->_extLimit = explode(',', $this->_cfg->extLimit);
        }
        if (isset($this->_cfg->imageExtLimit) && strlen($this->_cfg->imageExtLimit)) {
            $this->_imageExtLimit = explode(',', $this->_cfg->imageExtLimit);
        }        
    }

    /**
     * Media upload method
     * 
     * @param $req = array(
     *      'field_name' =>
     * 		'uid' => '*',
     * 		'media_id' => '*',
     * );
     * @return $ret = array(
     * 		'errMsg' => '*',
     * 		'errNo' => '*',
     * );
     */
    public function upload($req = array(), &$ret = array()) 
    {
        $mediaTmp  = $_FILES[$this->_fieldName]['tmp_name'];
        $mediaName = $_FILES[$this->_fieldName]['name'];
        $mediaSize = $_FILES[$this->_fieldName]['size'];
        $mediaMime = $_FILES[$this->_fieldName]['type'];
        
        $mediaName = strtolower($mediaName);
        $mediaExt  = substr(strrchr($mediaName, '.'), 1);
        $uid       = isset($req['uid']) ? $req['uid'] : '0';
        
        // type checkuid
        if (! in_array($mediaExt, $this->_extLimit)) {
            $ret['errMsg'] = '只能上传 gif, jpg, png 格式的图片';
            return false;
        } elseif (is_uploaded_file($mediaTmp)) {
            $time = time();
            $mediaStored = date('YmdHis', $time).'-'.$uid.'-'.mt_rand().'.'.$mediaExt;
            $mediaDir = date($this->_uploadMediaDir, $time);
            
            $this->mkDir($this->_uploadDir.'/'.$mediaDir);
            
            $mediaStoredDir = $this->_uploadDir.'/'.$mediaDir.'/'.$mediaStored;

            @move_uploaded_file($mediaTmp, $mediaStoredDir);
            
            $mediaSizeStored = @filesize($mediaStoredDir);
            if ($mediaSizeStored > $this->_maxSize) {
                @unlink($mediaStoredDir);
                $maxSizeKb = $this->_maxSize / 1000;
                $ret['errMsg'] = "文件超过系统设置大小( $maxSizeKb Kb )! ";
                return false;
            } elseif ($mediaSizeStored != $mediaSize) {
                @unlink($mediaStoredDir);
                $ret['errMsg'] = '上传文件发生意外错误 !';
                return false;
            } elseif (in_array($mediaExt, array('gif', 'jpg', 'png'))) {
                if ($imgInfo = @getimagesize($mediaStoredDir)) {
                    if (!$imgInfo[2]) {
                        @unlink($mediaStoredDir);
                        $ret['errMsg'] = '图像文件校验失败,请确认文件的有效性 !';
                        return false;
                    }
                }
            }
            @chmod($mediaStoredDir, 0604);
            
            $_media = Hooto_Data_Sql::getTable('hss_v1');
            
            $data = array(
                'uid'           => $uid,
                'media_dir'     => $mediaDir,
                'media_name'    => $mediaName,
                'media_stored'  => $mediaStored,
                'media_ext'     => $mediaExt,
                'media_size'    => $mediaSize,
                'media_mime'    => $mediaMime,
                'created'       => date('Y-m-d H:i:s'),
                'updated'       => date('Y-m-d H:i:s'),
            );
            $_media->insert($data);
            
            //$ret['mediaid'] = $mediaid;
            $ret['stored'] = $mediaStoredDir;
            return true;
            
        } else { 
            $ret['errMsg'] = '文件上传失败 !';
            return false;
        }
    }

    public function mkDir($dir)
    {
        $dirs = explode('/', $dir);
        $tmpDir = '';
        foreach ($dirs as $value) {
            $tmpDir .= $value .'/';
            if ($value == '..' || $value == '.') {
                continue;
            } elseif (!is_dir($tmpDir)) {
                @mkdir($tmpDir, 0715);
                //$handle = fopen($tmpDir.'index.html', 'x');
                //fclose($handle);
            }
        }
        return true;
    }
    
    public function getImageResize($src, $key)
    {
        if (!file_exists($src)) {
            return false;
        }
        
        if ($key) {
            $im = preg_replace('/(.jpg|.png|.gif)/', '-'.$key.'.jpg', $src);
        } else {
            $im = $src;
        }
        
        if (file_exists($im)) {
            if ($ims = getimagesize($im)) {
                $size = array(
                    'width' => $ims[0],
                    'height' => $ims[1],
                    'key' => $key, 
                    'dst' => $im,
                );
                return $size;
            }
        }
        
        return false;
    }
    
    public function getImageResizes($src)
    {
        if (!file_exists($src)) {
            return false;
        }
        
        $sizes = false;
        
        // Thumbnail
        if ($size = $this->getImageResize($src, self::RESIZE_THUMBNAIL_KEY)) {
            $size['name'] = 'Thumbnail';
            $sizes[self::RESIZE_THUMBNAIL_KEY] = $size;
        }
        
        // Medium
        if ($size = $this->getImageResize($src, self::RESIZE_MEDIUM_KEY)) {
            $size['name'] = 'Medium';
            $sizes[self::RESIZE_MEDIUM_KEY] = $size;
        }
        
        // Large
        if ($size = $this->getImageResize($src, self::RESIZE_LARGE_KEY)) {
            $size['name'] = 'Large';
            $sizes[self::RESIZE_LARGE_KEY] = $size;
        }
        
        // Full
        if ($size = $this->getImageResize($src, self::RESIZE_FULL_KEY)) {
            $size['name'] = 'Full';
            $sizes['full'] = $size;
        }

        return $sizes;
    }
    
    public function deleteFile($src)
    {
        if (!file_exists($src)) {
            return false;
        } 
        
        $im = preg_replace('/(.jpg|.png|.gif)/', '-large.jpg', $src);
        if (file_exists($im)) {
            @unlink($im);
        }
        
        $im = preg_replace('/(.jpg|.png|.gif)/', '-medium.jpg', $src);
        if (file_exists($im)) {
            @unlink($im);
        }
        
        $im = preg_replace('/(.jpg|.png|.gif)/', '-thumb.jpg', $src);
        if (file_exists($im)) {
            @unlink($im);
        }
        
        @unlink($src);

        return true;
    }
}
