<?php

defined('SYS_ROOT') or die('Access Denied!');


class jm42iwaf_entry
{
    const STATUS_PUBLISH = 1;
    const STATUS_DRAFT   = 2;
    const STATUS_PRIVATE = 3;
    
    public static function isValid(&$params, &$message)
    {
        $params->title = isset($params->title) ? trim($params->title) : null;
        
        if (is_null($params->title) || empty($params->title)) {
            $message = sprintf("%s can't be null", 'Title');
            return false;
        }
        
        $farr = array(
            "/<(\/?)(script|iframe|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU",
            "/(<[^>]*)on[a-zA-Z] \s*=([^>]*>)/isU",
        );
        $tarr = array(
            "&lt;\\1\\2\\3&gt;",
            "\\1\\2",
        );
        
        if (! isset($params->auto_summary) && isset($params->summary) && strlen(trim($params->summary))) {
            $params->summary = preg_replace($farr, $tarr, $params->summary);
        } else {
            $params->summary = null;
        }
    
		if (!isset($params->content) || is_null($params->content) || empty($params->content)) {
		    $message = sprintf("%s can't be null", 'Content');
            return false;
		}
		
		if (!isset($params->category) || (int)$params->category == 0) {
		    $message = sprintf("%s can't be null", 'Category');
            return false;
		}
		
		if (!isset($params->comment) 
		    || ((int)$params->comment != 0 && (int)$params->comment != 1)) {
		    $params->comment = 0;
		}
    
        return true;
    }
    
    public static function saveEntry($entry)
    {
    }
}
