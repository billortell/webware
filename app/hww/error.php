<?php

class hww_error
{
    public static $found = FALSE;
    
    public static $lang = array(
	    E_ERROR				=> 'Error',
	    E_WARNING			=> 'Warning',
	    E_PARSE				=> 'Parsing Error',
	    E_NOTICE			=> 'Notice',
	    E_CORE_ERROR		=> 'Core Error',
	    E_CORE_WARNING		=> 'Core Warning',
	    E_COMPILE_ERROR		=> 'Compile Error',
	    E_COMPILE_WARNING	=> 'Compile Warning',
	    E_USER_ERROR		=> 'User Error',
	    E_USER_WARNING		=> 'User Warning',
	    E_USER_NOTICE		=> 'User Notice',
	    E_STRICT			=> 'Runtime Notice',
    );

    static function lang($l)
    {
        return isset(self::$lang[$l]) ? self::$lang[$l] : 'Internal Error';
    }

    public static function header()
    {
    	headers_sent() OR header('HTTP/1.0 500 Internal Server Error');
    }
    
    public static function fatal()
    {
    	if ($e=error_get_last()) { 
    	    self::exception(new ErrorException($e['message'],$e['type'],0,$e['file'],$e['line']));
        }
    }
    
    public static function handler($c,$e,$f=0,$l=0)
    {    	
    	if ((error_reporting() & $c) === 0)
    	    return true;
    	
    	self::$found = 1;
    	self::header();
    	
    	$v = new Hooto_Web_View();
    	$p = array(
    	    'title' => self::lang($c),
    	    'error' => $e,
    	    'file'  => $f,
    	    'line'  => $l,
    	);
    	print $v->render('pagelet/error', $p);
        
        return true;
    }
    
    public static function exception(Exception $e)
    {
        self::$found = 1;
        $m = "{$e->getMessage()} [{$e->getFile()}] ({$e->getLine()})";

        try {
            self::header();
            $v = new Hooto_Web_View();
            //$v->exception = $e;
            print $v->render('pagelet/exception', array('exception' => $e));
        } catch (Exception$e) {
            print $m;
        }
        exit(1);
    }
}
