<?php

defined('SYS_ROOT') or die('Access Denied!');


class hdata_entry
{
    public static $instance = null;
        
    public static $metadata = array();
    
    public static function setInstance($instance)
    {
        if (self::$instance == $instance) {
            return;
        }
        
        self::$instance  = $instance;
        self::$metadata = Hooto_Config_Array::get("hdata/entry{$instance}");
    }
    
    public static function select($var = null)
    {
        $query = new Hooto_Data_SqlQuery();
        
        return $query->select($var)
            ->from(self::$metadata['entry']['name'])
            ->where('instance = ?', self::$instance);
    }
    
    public static function query(Hooto_Data_SqlQuery $query)
    {
        $db = Hooto_Data_Sql::getTable(self::$metadata['entry']['name']);
        return $db->query($query);
    }
    
    public static function fetchEntry($id)
    {
        $db = Hooto_Data_Sql::getTable(self::$metadata['entry']['name']);
        return $db->fetch($id, self::$metadata['entry']['primary']);
    }
    
    public static function updateEntry($entry)
    {
        if (is_array($entry)) {
            $entry = (object)$entry;
        }
        $id = self::$metadata['entry']['primary'];
        if (!isset($entry->{$id})) {
            return false;
        }
        
        $where = array($id => $entry->{$id});
        unset($entry->{$id});
        
        foreach ($entry as $key => $val) {
            if (!isset(self::$metadata['entry']['metadata'][$key])) {
                unset($entry->{$key});
            }
        }
        
        $db = Hooto_Data_Sql::getTable(self::$metadata['entry']['name']);
        
        if (isset($entry->created)) {
            unset($entry->created);
        }
        
        $db->update($entry, $where);
    }
    
    public static function replaceEntry($entry)
    {
        $id = self::$metadata['entry']['primary'];
        $entry = (object)$entry;
        if (!isset($entry->{$id})) {
            return false;
        }
        
        foreach ($entry as $key => $val) {
            if (!isset(self::$metadata['entry']['metadata'][$key])) {
                unset($entry->{$key});
            }
        }
        
        $db = Hooto_Data_Sql::getTable(self::$metadata['entry']['name']);
        
        $entry0 = self::fetchEntry($entry->{$id});
        if (isset($entry0[$id])) {
        
            if (isset(self::$metadata['taxonomy'])) {
                foreach (self::$metadata['taxonomy'] as $key => $taxonomy) {
                    //print_r($entry);
                    if (isset($entry->{$key}) && $entry->{$key} !== $entry0[$key]) {
                        $entryterms = array(
                            'gid' => $entry0['uid'],
                            'terms' => $entry->{$key},
                            'terms_pre' => $entry0[$key],
                        );
                        hdata_taxonomy::replaceEntryTerms($taxonomy, $entryterms);
                    }
                }
            }
        
            $where = array($id => $entry->{$id});
            unset($entry->{$id});
            
            if (isset($entry->created)) {
                unset($entry->created);
            }

            $db->update($entry, $where);
        } else {
            if (isset(self::$metadata['taxonomy'])) {
                foreach (self::$metadata['taxonomy'] as $key => $taxonomy) {
                    //print_r($entry);
                    if (isset($entry->{$key})) {
                        $entryterms = array(
                            'gid' => $entry->uid,
                            'terms' => $entry->{$key},
                        );
                        hdata_taxonomy::replaceEntryTerms($taxonomy, $entryterms);
                    }
                }
            }
            
            $db->insert($entry);
        }
        
    }
    
    public static function deleteEntry($id)
    {
        $primary = self::$metadata['entry']['primary'];
        
        $db = Hooto_Data_Sql::getTable(self::$metadata['entry']['name']);
    
        //$db->delete($id, $primary);
        $db->update(array('status' => 0), array("$primary" => $id));
    }
}
