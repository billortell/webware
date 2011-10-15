<?php

defined('SYS_ROOT') or die('Access Denied!');

final class hdata_rds_table
{
    private $_dbname = null;
    // * @param  string $tableName table name
    private $_table = null;
    
    public function __construct($dbname, $table)
    {
        $this->_dbname  = $dbname;
        $this->_table   = $table;
    }
    
    public function fetch($value, $key = 'id')
    {
        try {

            $query = $this->select()
                ->where("$key = ?", $value)
                ->assemble($this->_table);
            
            $cn = hdata_rds_service::getConn($this->_dbname);
            
            $sth = $cn->prepare($query);
            $sth->execute();
            
            $rs = $sth->fetch(PDO::FETCH_ASSOC);
        
        } catch(Exception $e) {
            throw $e;
        }

        return $rs;        
    }

    public function insert($entry)
    {
        $keys = array_keys((array)$entry);
        $bind = array();
        foreach ($keys as $k => $v) {
            $keys[$k] = "`$v`";
            $bind[] = '?';
        }
        $keys = implode(",", $keys);
        $bind = implode(",", $bind);
            
        $vals = array_values((array)$entry);
            
        $sql = "INSERT INTO `{$this->_table}` ($keys) VALUES ($bind)";

        try {

            $cn = hdata_rds_service::getConn($this->_dbname);

            $sth = $cn->prepare($sql);
            
            if (!$sth->execute($vals)) {
                $arr = $sth->errorInfo();
                throw new Exception($arr[2]);
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function replace($entry)
    {
        
    }
    
    public function update($entry, $where)
    {            
        $keys = array_keys((array)$entry);
        $sql = NULL;//
        foreach ($keys as $k => $v) {
            if ($sql == NULL) {
                $sql = " $v = ?";
            } else {
                $sql .= ",$v = ?";
            }
        }        
        $vals = array_values((array)$entry);
        
        $sqlw = NULL;//
        foreach ($where as $k => $v) {
            if ($sqlw == NULL) {
                $sqlw = " $k = ?";
            } else {
                $sqlw .= " AND $k = ?";
            }
            $vals[] = $v;
        }
        
        $sql = "UPDATE `{$this->_table}` SET {$sql} WHERE {$sqlw}";

        try {

            $cn = hdata_rds_service::getConn($this->_dbname);

            $sth = $cn->prepare($sql);
            
            if (!$sth->execute($vals)) {
                $arr = $sth->errorInfo();
                throw new Exception($arr[2]);
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function delete($key, $pid = 'id')
    {
        try {
            $ids = explode(",", $key);
                    
            $sql = "DELETE FROM `{$this->_table}` WHERE $pid IN (?)";
                    
            $cn = hdata_rds_service::getConn($this->_dbname);
            $sth = $cn->prepare($sql);
            $sth->execute($ids);
                        
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function deleteWhere($where)
    {
        $sqlw = NULL;//
        foreach ($where as $k => $v) {
            if ($sqlw == NULL) {
                $sqlw = " $k ";
            } else {
                $sqlw .= " AND $k";
            }
            $vals[] = $v;
        }
        
        try {
                    
            $sql = "DELETE FROM `{$this->_table}` WHERE {$sqlw}";
                    
            $cn = hdata_rds_service::getConn($this->_dbname);
            $sth = $cn->prepare($sql);
            $sth->execute($vals);
                        
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Query for documents stored in the document service. If a string is passed in
     * $query, the query string will be passed directly to the service.
     *
     * @param  string $query
     * @param  array $options
     * @return array hdata_rds_service
     */
    public function query($query, $options = null)
    {
        try {
        
            if ($query instanceof hdata_rds_query) {
                $query = $query->assemble($this->_table);
            }
            
            $sth = hdata_rds_service::getConn($this->_dbname)->query($query);
            
            $rs = $sth->fetchAll(PDO::FETCH_ASSOC);
        
        } catch(Exception $e) {
            throw $e;
        }

        return $rs;
    }
    
    /**
     * Create query statement
     *
     * @param  string $fields
     * @return hdata_rds_query
     */
    public function select($fields = null)
    {
        $query = new hdata_rds_query();
        $query->select($fields);
        
        return $query;
    }
}
