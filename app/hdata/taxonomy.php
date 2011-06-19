<?php

defined('SYS_ROOT') or die('Access Denied!');


class hdata_taxonomy
{
    public static $tmp = array();
    public static $tmp1 = array();
    
    public static function fetchTerms($taxon, $ids = null)
    {
        if (!is_array($taxon)) {
            $taxon = array('taxon' => $taxon);
        }
        
        $db = Hooto_Data_Sql::getTable('term_data');
        
        $query = new Hooto_Data_SqlQuery();
        
        $query = $query->select()
            ->from('term_data')
            ->limit(1000);
        
        if (isset($taxon['taxon'])) {
            $query->where('taxon = ?', $taxon['taxon']);
        }
        
        if (isset($taxon['gid'])) {
            $query->where('gid = ?', $taxon['gid']);
        }
        
        if (is_array($ids) && count($ids) > 0) {
            $arg = trim(str_repeat(',?', count($ids)), ',');
            $query->where("id IN ($arg)", $ids);
        }
//print_r($query);
        $ret = $db->query($query);
        
        $root = array();
        foreach ($ret as $key => $val) {
            if ($val['pid'] == 0) {
                $root[$val['id']] = $val;
            } else {
                self::$tmp[$val['pid']][$val['id']] = $val;
            }
        }
        unset($ret);
        
        self::$tmp1 = array();
        $level = '0';
        foreach ($root as $key => $val) {
            $val['_level'] = $level;
            self::$tmp1[$key] = $val;
            if (isset(self::$tmp[$key])) {
                self::termHierarchy($key, $level);
            }
        }
        self::$tmp = array();
        
        return self::$tmp1;
    }
    
    public static function termHierarchy($pid, $level)
    {
        $level++;
        
        foreach (self::$tmp[$pid] as $key => $val) {
            
            $val['_level'] = $level;
            self::$tmp1[$key] = $val;
            
            if (isset(self::$tmp[$key])) {
                self::termHierarchy($key, $level);
            }
        }
    }
    
    public static function replaceEntryTerms($taxonomy, $terms)
    {
        //print_r($taxonomy);
        $db = Hooto_Data_Sql::getTable('term_data');
        $query = new Hooto_Data_SqlQuery();
        
        if ($taxonomy['type'] == 'taxonomy_autocomplete') {
            
            $_terms = explode(",", $terms['terms']);
            $hashs = array();
            $term_data = array();
            
            foreach ($_terms as $val) {
                $val = trim($val);
                $hash = substr(md5(strtolower($val)), 0, 16);
                $hashs[] = $hash;
                $term_data[$hash] = array('hash' => $hash, 'name' => $val);
            }
        
            $arg = trim(str_repeat(',?', count($hashs)), ',');
            
            $query = $query->select();
            $query->from('term_data')
                ->where('gid = ?', $terms['gid'])
                ->where('taxon = ?', $taxonomy['id'])
                ->where("hash IN ($arg)", $hashs)
                ->limit(1000);

            $ret = $db->query($query);
            
            $exists = array();
            foreach ($ret as $val) {
                $exists[$val['hash']] = $val;
            }
            
            foreach ($term_data as $key => $val) {
            
                if (!isset($exists[$key])) {
                
                    $val['taxon'] = $taxonomy['id'];
                    $val['gid'] = $terms['gid'];
                
                    $db->insert($val);
                    
                }
                
                //unset($exists[$key]);
            }
            
            //foreach ($exists as $val) {
            //    $db->delete($val['id']);
            //}            
        }
    }
}
