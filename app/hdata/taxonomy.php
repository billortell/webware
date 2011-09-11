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
            ->order('weight', 'desc')
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
            $val['_paths'] = array();
            self::$tmp1[$key] = $val;
            if (isset(self::$tmp[$key])) {
                self::termHierarchy($key, $level, array($val['id']));
            }
        }
        self::$tmp = array();
        
        return self::$tmp1;
    }
    
    public static function termHierarchy($pid, $level, $paths)
    {
        $level++;
        
        foreach (self::$tmp[$pid] as $key => $val) {
            
            $val['_level'] = $level;
            $val['_paths'] = $paths;
            self::$tmp1[$key] = $val;
            
            if (isset(self::$tmp[$key])) {
                $_paths = array_merge($paths, array($val['id']));
                self::termHierarchy($key, $level, $_paths);
            }
        }
    }
    
    public static function replaceEntryTerms($taxonomy, $terms)
    {
        //print_r($taxonomy);
        $db = Hooto_Data_Sql::getTable('term_data');
        $query = new Hooto_Data_SqlQuery();
        
        if ($taxonomy['type'] == 'taxonomy_autocomplete') {
            
            $_terms_out = array();
            $_terms_old = array();
            $_terms_new = array();
            
            $_terms = explode(",", $terms['terms']);
            
            $term_data = array();
            
            if (isset($terms['terms_pre'])) {
            
                $tmp = explode(",", $terms['terms_pre']);
                
                foreach ($tmp as $key => $val) {
                    $val = trim($val);
                    if (strlen($val) > 0) {
                        $hash = substr(md5(strtolower($val)), 0, 16);
                        $_terms_out[$hash] = $val;
                    }
                }
            }
            
            foreach ($_terms as $val) {
            
                $val = trim($val);
                if (strlen($val) == 0) {
                    continue;
                }
                
                $hash = substr(md5(strtolower($val)), 0, 16);
                if (isset($term_data[$hash])) {
                    continue;
                }
                
                if (isset($_terms_out[$hash])) {
                    $_terms_old[] = $hash;
                    unset($_terms_out[$hash]);
                } else {
                    $_terms_new[] = $hash;
                }

                $term_data[$hash] = $val;
            }
        
            $hashs = array_merge(array_keys($_terms_out), $_terms_new);
            $ret = array();
            if (count($hashs) > 0) {
                $arg = trim(str_repeat(',?', count($hashs)), ',');

                $query = $query->select()->from('term_data')
                    ->where('gid = ?', $terms['gid'])
                    ->where('taxon = ?', $taxonomy['id'])
                    ->where("hash IN ($arg)", $hashs)
                    ->limit(1000);

                $ret = $db->query($query);
            }
            
            $exists = array();
            foreach ($ret as $val) {
                $exists[$val['hash']] = $val;
            }
            
            // append new
            foreach ($_terms_new as $key) {
            
                if (!isset($exists[$key])) {
                
                    $set = array(
                        'taxon' => $taxonomy['id'],
                        'gid' => $terms['gid'],
                        'rating' => 1,
                        'hash' => $key,
                        'name' => $term_data[$key],
                    );
                
                    $db->insert($set);
                    
                } else {

                    $set = array('rating' => ($exists[$key]['rating'] + 1));
                    $db->update($set, array('id' => $exists[$key]['id']));
                }
                
            }
            
            // Lower rating
            foreach ($_terms_out as $key => $val) {
                
                if (!isset($exists[$key])) {
                    continue;
                }
                
                $rating = ($rating = ($exists[$key]['rating'] - 1) < 0) ? 0 : $rating;
                
                $db->update(array('rating' => $rating), array('id' => $exists[$key]['id']));
            }
           
        }
    }
}
