<?php

defined('SYS_ROOT') or die('Access Denied!');


class add38b6c_entry
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
    
    public function getArchives($where, $limit = null, $offset = null)
    {
       
        $gdb = Zend_Registry::get('gdb');
        $select = $gdb->select();
        $select->from('kit_node as node', 
                        'YEAR(created) AS year, MONTH(created) AS month, COUNT(*) as count');

        if (!isset($where['e.node.status']) && !isset($where['in.node.status'])) {
            $where['e.node.status'] = Kit_Node_Util::STATUS_PUBLISH;
        }

        Kit_Db_Util::buildSelectWhere($select, $where);

        $select->group('YEAR(created) , MONTH(created)');
        $select->order('created DESC');
        $select->limit((int)$limit, (int)$offset);

        $result = (array)$gdb->fetchAll($select->__toString());
    
        return $result;
    }
}
