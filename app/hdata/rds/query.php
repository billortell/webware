<?php


defined('SYS_ROOT') or die('Access Denied!');


class hdata_rds_query
{
    /**
     * Clause list
     *
     * @var array
     */
    protected $_clauses = array();

    /**
     * Generic clause
     *
     * You can use any clause by doing $query->foo('bar')
     * but concrete adapters should be able to recognise it
     *
     * The call will be iterpreted as clause 'foo' with argument 'bar'
     *
     * @param  string $name Clause/method name
     * @param  mixed $args
     * @return hdata_rds_query
     */
    public function __call($name, $args)
    {
        $this->_clauses[] = array(strtolower($name), $args);
        return $this;
    }

    /**
     * SELECT clause (fields to be selected)
     *
     * @param  null|string|array $select
     * @return hdata_rds_query
     */
    public function select($select = '*')
    {
        if (empty($select)) {
            return $this;
        }
        if (!is_string($select) && !is_array($select)) {
            throw new Exception("SELECT argument must be a string or an array of strings", 100);
        }
        if (is_string($select)) {
            $select = explode(',', $select);
        }
        $this->_clauses['select'] = array('select', $select);
        return $this;
    }

    /**
     * FROM clause
     *
     * @param string $name Field names
     * @return hdata_rds_query
     */
    public function from($name)
    {
        //if(!is_string($name)) {
        //    throw new Exception("FROM argument must be a string", 100);
        //}
        if (preg_match('/^[a-z][a-z0-9_]*$/i', $name) == false) {
            throw new Exception("FROM argument can contain only alphanumeric characters, _", 100);
        }
        $this->_clauses[] = array('from', $name);
        return $this;
    }

    /**
     * WHERE query
     *
     * @param string $cond Condition
     * @param array $args Arguments to substitute instead of ?'s in condition
     * @param string $op relation to other clauses - and/or
     * @return hdata_rds_query
     */
    public function where($cond, $value = null, $op = 'and')
    {
        if (!is_string($cond)) {
            throw new Exception("WHERE argument must be a string", 100);
        }
        $this->_clauses[] = array('where', array($cond, $value, $op));
        return $this;
    }

    /**
     * Select record or fields by ID
     *
     * @param  string|int $value Identifier to select by
     * @return hdata_rds_query
     */
    public function whereId($value)
    {
        if (!is_scalar($value)) {
            throw new Exception("WHEREID argument must be a scalar", 100);
        }
        $this->_clauses[] = array('whereid', $value);
        return $this;
    }

    /**
     * LIMIT clause (how many items to return)
     *
     * @param  int $limit
     * @return hdata_rds_query
     */
    /*public function limit($limit)
    {
        if ($limit != (int) $limit) {
            throw new Exception("LIMIT argument must be an integer", 100);
        }
        $this->_clauses[] = array('limit', $limit);
        return $this;
    }*/
    
    /**
     * Sets a limit count and offset to the query.
     *
     * @param int $count OPTIONAL The number of rows to return.
     * @param int $offset OPTIONAL Start returning after this many rows.
     * @return hdata_rds_query object.
     */
    public function limit($count = 10, $offset = 0)
    {
        //$this->_clauses[self::LIMIT_COUNT]  = (int) $count;
        //$this->_clauses[self::LIMIT_OFFSET] = (int) $offset;
        if ($offset > 1) {
            $limit = (int)$offset .','. (int)$count;
        } else {
            $limit = (int)$count;
        }
        $this->_clauses['limit'] = array('limit', $limit);
        return $this;
    }

    /**
     * ORDER clause; field or fields to sort by, and direction to sort
     *
     * @param  string|int|array $sort
     * @param  string $direction
     * @return hdata_rds_query
     */
    public function order($sort, $direction = 'asc')
    {
        $this->_clauses[] = array('order', array($sort, $direction));
        return $this;
    }
    
    /**
     * Adds grouping to the query.
     *
     * @param  string|int|array $sort
     * @param  string $direction
     * @return hdata_rds_query
     */
    public function group($sort, $direction = 'asc')
    {
        $this->_clauses[] = array('group', array($sort, $direction));
        return $this;
    }

    /**
     * Assemble the query into a format the adapter can utilize
     *
     * @var    string $tableName Name of table from which to select
     * @return string
     */
    public function assemble($tableName = null)
    {
        $select  = null;
        $from    = null;
        $where   = null;
        $order   = null;
        $group   = null;
        $limit   = null;
        
        foreach ($this->_clauses as $clause) {
        
            list($name, $args) = $clause;

            switch ($name) {
                case 'select':
                    //$select = $args[0];
                    if (null === $select) {
                        $select = implode(',', $args);
                    } else {
                        $select .= ', '. implode(',', $args);
                    }
                    break;
                case 'from':
                    if (null === $from) {
                        // Only allow setting FROM clause once
                        $from = $args;//$adapter->quoteName($args);
                    }
                    break;
                case 'where':
                    $statement = $this->_parseWhere($args[0], $args[1]);
                    if (null === $where) {
                        $where = $statement;
                    } else {
                        $operator = empty($args[2]) ? 'AND' : $args[2];
                        $where .= ' ' . $operator . ' ' . $statement;
                    }
                    break;
                case 'whereid':
                    $statement = $this->_parseWhere('ItemName() = ?', array($args));
                    if (null === $where) {
                        $where = $statement;
                    } else {
                        $operator = empty($args[2]) ? 'AND' : $args[2];
                        $where .= ' ' . $operator . ' ' . $statement;
                    }
                    break;
                case 'order':
                    if (null !== $order) {
                        $order .= ', ';
                    }
                    $order .= $args[0];//$adapter->quoteName($args[0]);
                    if (isset($args[1])) {
                        $order .= ' ' . $args[1];
                    }
                    break;
                case 'group':
                    if (null !== $group) {
                        $group .= ', ';
                    }
                    $group .= $args[0];//$adapter->quoteName($args[0]);
                    if (isset($args[1])) {
                        $group .= ' ' . $args[1];
                    }
                    break;
                case 'limit':
                    $limit = $args;
                    break;
                default:
                    // Ignore unknown clauses
                    break;
            }
        }

        if (empty($select)) {
            $select = "*";
        }
        if (empty($from)) {
            if (null === $tableName) {
                throw new Exception("Query requires a FROM clause");
            }
            $from = $tableName;//$adapter->quoteName($tableName);
        }
        $query = "SELECT $select FROM $from";
        if (!empty($where)) {
            $query .= " WHERE $where";
        }
        if (!empty($order)) {
            $query .= " ORDER BY $order";
        }
        if (!empty($group)) {
            $query .= " GROUP BY $group";
        }
        if (!empty($limit)) {
            $query .= " LIMIT $limit";
        }
        return $query;
    }
    
    public function reset($keys)
    {
        if (is_string($keys)) {
            $keys = array($keys);
        }

        foreach ($this->_clauses as $key => $clause) {
        
            //list($name, $args) = $clause;
            
            if (in_array($clause[0], $keys)) {
                unset($this->_clauses[$key]);
            }
        }

        
        return $this;
    }
    
    /**
     * Parse a where statement into service-specific language
     *
     * @todo   Ensure this fulfills the entire SimpleDB query specification for WHERE
     * @param  string $where
     * @param  array $args
     * @return string
     */
    protected function _parseWhere($where, $args)
    {
        if (!is_array($args)) {
            $args = (array) $args;
        }
        //---$adapter = $this->getAdapter()->getClient();
        $i = 0;
        while (false !== ($pos = strpos($where, '?'))) {

            $args[$i] = "'" . str_replace("'", "''", $args[$i]) . "'"; // quote
            //$where = substr_replace($where, $adapter->quote($args[$i]), $pos);

            $where = substr_replace($where, $args[$i], $pos, 1);

            ++$i;
        }
        if (('(' != $where[0]) || (')' != $where[strlen($where) - 1])) {
            $where = '(' . $where . ')';
        }
        return $where;
    }
    
}
