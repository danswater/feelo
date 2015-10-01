<?php

class Whcomments_Model_DbTable_Comments extends Engine_Db_Table {

    /**
     * Traversal tree information for
     * Modified Preorder Tree Traversal Model
     * 
     * http://www.sitepoint.com/print/hierarchical-data-database
     * 
     * Values:
     *  'left'          => column name for left value
     *  'right'         => column name for right value
     *  'column'        => column name for identifying row (primary key assumed)
     *  'refColumn'     => column name for parent id (if not set, will look in reference map for own table match)
     *  'order'         => order by for rebuilding tree (e.g. "`name` ASC, `age` DESC")
     *
     * @var array $_traversal
     */
    protected $_traversal = array(
        'left' => 'lt',
        'right' => 'rt',
        'column' => 'comment_id',
        'refColumn' => 'parent_id'
    );
    protected $_custom = false;

    /**
     * Automatically is set to true once traversal info is set and verified
     *
     * @var boolean $_isTraversable
     */
    protected $_isTraversable = false;

    /**
     * Modified to initialize traversal
     *
     */
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->_initTraversal();
    }

    /**
     * Returns columns names
     *
     * @return array columns
     */
    public function getColumns() {
        return $this->info(Zend_Db_Table_Abstract::COLS);
    }

    /**
     * Returns metadata value for index or entire array
     *
     * @param index $key
     * @return value | array
     */
    public function getMetadata($key = null) {
        if (null === $key)
            return $this->_metadata;
        if (!array_key_exists($key, $this->_metadata)) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Key '{$key}' not found in metadata");
        }
        return $this->_metadata[$key];
    }

    /**
     * Returns the table name and schema separated by a dot for use in sql queries
     *
     * @return string schema.name || name
     */
    public function getName() {
        return $this->_schema ? $this->_schema . '.' . $this->_name : $this->_name;
    }

    /**
     * Is Duplicate - Checks for a duplicate value in the database
     *
     * @param string $column - column name
     * @param string $value - value to search for
     * @return boolean
     */
    public function isDuplicate($column, $match) {
        $select = $this->select()->limit(1);

        if (is_string($match) OR is_numeric($match)) {
            $select->where("{$column} = ?", $match);
        } else if (is_array($match)) {
            $select->where("{$column} IN (?)", $match);
        } else {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Match value must be a string, numeric, or array");
        }

        return (null !== $this->fetchRow($select)) ? true : false;
    }

    /**
     * Fetches duplicate entries based on column name
     *
     * @param string $column - column name
     * @param string $match - optional match value
     * @return Zend_Db_Table_Rowset
     */
    public function fetchDuplicates($column, $match = null) {
        $select = $this->select()
                ->from(
                        $this->getName(), array(
                    'value' => $column,
                    'duplicates' => new Zend_Db_Expr('COUNT(*)')
                        )
                )
                ->group($column)
                ->having('duplicates > ?', 1)
        ;

        if (is_string($match) OR is_numeric($match)) {
            $select->where("{$column} = ?", $match);
        } else if (is_array($match)) {
            $select->where("{$column} IN (?)", $match);
        }

        return $this->fetchAll($select);
    }

    /**
     * Is Valid - Checks if a field is valid based on its validator
     *
     * @param string $field
     * @param string|int $value
     * @return boolean
     */
    public function isValid($field, $value) {
        if (!array_key_exists($field, $this->_validators))
            return true;

        foreach ($this->_validators[$field] as $validator) {
            if (!array_key_exists('name', $validator)) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Validators must contain a name.");
            }
            $name = $validator['name'];
            $arguments = array_key_exists('arguments', $validator) ? $validator['arguments'] : array();
            if (!Zend_Validate::is($value, $name, $arguments)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Counts the number of rows for a given select statement.
     * Accepts instances of Zend_Db_Table_Select, Zend_Db_Select,
     * an array of WHERE clauses, or null to return a total
     * count of all rows in the table.
     *
     * @param Zend_Db_Table_Select|string|array $select
     * @return int theCount
     */
    public function count($select = null) {
        // Count using instance of Zend_Db_Table_Select
        if ($select instanceof Zend_Db_Table_Select) {
            $_select = clone $select;
            $result = $this->_countSelect($_select);

            // Count using array or count all
        } else if (null === $select OR is_string($select) OR is_array($select)) {
            $result = $this->_countWhere($select);

            // Invalid parameter
        } else {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception('Invalid parameter passed to count() method');
        }

        return $result;
    }

    /**
     * Counts the number of rows using an instance of 
     * Zend_Db_Table_Select.
     *
     * @param Zend_Db_Table_Select $select
     * @return double theCount
     */
    protected function _countSelect(Zend_Db_Table_Select $select) {
        $s = clone $select;

        // Remove any existing limits, offsets, and orders
        $s->reset('order');
        $s->reset('limitcount');
        $s->reset('limitoffset');


        $_select = $this->getAdapter()
                ->select()
                ->from(
                array('c' => $s), array('theCount' => 'COUNT(*)')
                )
        ;

        $row = $this->getAdapter()->fetchRow($_select);

        return (double) $row['theCount'];
    }

    /**
     * Counts the number of rows using an array or string
     * of where clauses, or null to count all rows in the 
     * table.
     *
     * @param array|string $where
     * @return double theCount
     */
    protected function _countWhere($where = null) {
        $select = $this->select();
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                if (is_int($key)) {
                    $select->where($value);
                } else {
                    $select->where($key, $value);
                }
            }
        } else if (is_string($where)) {
            $select->where($where);
        }

        return (double) $this->_countSelect($select);
    }

    /**
     * Returns the number of rows from the last SQL_CALC_FOUND_ROWS query
     *
     * @return double - found rows
     */
    public function getCalcFoundRows() {
        $sql = "SELECT FOUND_ROWS() AS theCount";
        $stmt = $this->_db->query($sql);
        $row = $stmt->fetch();

        return (double) $row['theCount'];
    }

    /**
     * Pre-insert hook allows for data validation / filtering on a per-class basis
     *
     * @param array $data
     * @return array
     */
    public function preInsert($data) {
        return $data;
    }

    /**
     * Pre-update hook allows for data validation / filtering on a per-class basis
     *
     * @param array $data
     * @return array
     */
    public function preUpdate($data) {
        return $data;
    }

    /**
     * Override insert method to include pre-insert hook
     *
     * @param mixed $data
     * @return primary key
     */
    public function insert(array $data) {
        $data = $this->preInsert($data);

        return $this->_isTraversable ? $this->_insertTraversable($data) : parent::insert($data);
    }

    /**
     * Override update method to include pre-update hook
     *
     * @param mixed $data
     * @param mixed $where
     * @return int
     */
    public function update(array $data, $where) {
        $data = $this->preUpdate($data);

        return parent::update($data, $where);
    }

    /**
     * Factory method to return instances of reference tables
     *
     * @param string $name
     * @param array $options for constructor
     * @return Virgen_Db_Table $instance
     */
    public function getReferenceInstance($ruleKey, array $options = array()) {
        if (!array_key_exists($ruleKey, $this->_referenceMap)) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Reference key {$ruleKey} not found in " . __CLASS__);
        }

        $className = $this->_referenceMap[$ruleKey]['refTableClass'];

        // Check for self-references
        if (!array_key_exists($className, self::$_referenceInstances)) {
            self::$_referenceInstances[$className] = ($className == __CLASS__) ?
                    $this :
                    new $className($options);
        }

        return self::$_referenceInstances[$className];
    }

    /**
     * Factory method to return instances of dependent tables
     *
     * @param string $name - class name of dependent table
     * @param array $options - options to pass to constructor
     * @return Virgen_Db_Table $instance
     */
    public function getDependentInstance($className, array $options = array()) {
        if (!in_array($className, $this->_dependentTables)) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Dependent table {$className} not found in " . __CLASS__);
        }

        if (!array_key_exists($className, self::$_dependentInstances)) {
            self::$_dependentInstances[$className] = ($className == __CLASS__) ?
                    $this :
                    new $className($options);
        }

        return self::$_dependentInstances[$className];
    }

    /**
     * Returns all reference instances
     *
     * @return array - reference instances
     */
    public function getReferenceInstances() {
        return self::$_dependentInstances;
    }

    /**
     * Returns all dependent instances
     *
     * @return array - dependent instances
     */
    public function getDependentInstances() {
        return self::$_dependentInstances;
    }

    /**
     * Public function to rebuild tree traversal. The recursive function
     * _rebuildTreeTraversal() must be called without arguments.
     *
     * @return $this - Fluent interface
     */
    public function rebuildTreeTraversal() {
        $this->_rebuildTreeTraversal();

        return $this;
    }

    /**
     * Recursively rebuilds the modified preorder tree traversal
     * data based on a parent id column
     *
     * @param int $parentId
     * @param int $leftValue
     * @return int new right value
     */
    protected function _rebuildTreeTraversal($parentId = null, $leftValue = 0) {
        $this->_verifyTraversable();

        $select = $this->select();

        if ($parentId > 0) {
            $select->where("{$this->_traversal['refColumn']} = ?", $parentId);
        } else {
            $select->where("{$this->_traversal['refColumn']} IS NULL OR {$this->_traversal['refColumn']} = 0");
        }

        if (array_key_exists('order', $this->_traversal)) {
            $select->order($this->_traversal['order']);
        }

        $rightValue = $leftValue + 1;

        $rowset = $this->fetchAll($select);
        foreach ($rowset as $row) {
            $rightValue = $this->_rebuildTreeTraversal($row->{$this->_traversal['column']}, $rightValue);
        }

        if ($parentId > 0) {
            $node = $this->fetchRow($this->select()->where("{$this->_traversal['column']} = ?", $parentId));
            if (null !== $node) {
                $node->{$this->_traversal['left']} = $leftValue;
                $node->{$this->_traversal['right']} = $rightValue;
                $node->save();
            }
        }

        return $rightValue + 1;
    }

    /**
     * Calculates left and right values for new row and inserts it.
     * Also adjusts all rows to make room for the new row.
     *
     * @param array $data
     * @return int $id
     */
    protected function _insertTraversable($data) {
        $this->_verifyTraversable();

        // Disable traversable flag to prevent automatic traversable manipulation during updates.
        $isTraversable = $this->_isTraversable;
        $this->_isTraversable = false;

        if (array_key_exists($this->_traversal['refColumn'], $data) && $data[$this->_traversal['refColumn']] > 0) {
            // Find parent row
            $parent_id = $data[$this->_traversal['refColumn']];
            $parent = $this->find($parent_id)->current();
            if (null === $parent) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Traversable error: Parent id {$parent_id} not found");
            }

            $lt = (int) $parent->{$this->_traversal['left']};
            $rt = (int) $parent->{$this->_traversal['right']};

            // Make room for the new node
            parent::update(
                    array(
                $this->_traversal['left'] => new Zend_Db_Expr($this->getAdapter()->quoteInto("{$this->_traversal['left']} + ?", 2)),
                    ), array(
                $this->getAdapter()->quoteInto("{$this->_traversal['left']} > ?", $rt)
                    )
            );

            parent::update(
                    array(
                $this->_traversal['right'] => new Zend_Db_Expr($this->getAdapter()->quoteInto("{$this->_traversal['right']} + ?", 2)),
                    ), array(
                $this->getAdapter()->quoteInto("{$this->_traversal['right']} >= ?", $rt)
                    )
            );

            $data[$this->_traversal['left']] = $rt;
            $data[$this->_traversal['right']] = $rt + 1;
        } else {
            $maxRt = (double) $this->fetchRow($this->select()->from($this, array('theMax' => "MAX({$this->_traversal['right']})")))->theMax;
            $data[$this->_traversal['left']] = $maxRt + 1;
            $data[$this->_traversal['right']] = $maxRt + 2;
        }

        // Do insert
        $id = $this->insert($data);

        // Reset isTraversable flag to previous value.
        $this->_isTraversable = $isTraversable;

        return $id;
    }

    /**
     * Fetches all descendents of a given node
     *
     * @param Zend_Db_Table_Row_Abstract|string $row - Row object or value of row id
     * @param Zend_Db_Select $select - optional custom select object
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchAllDescendents($row, Zend_Db_Select $select = null) {
        $this->_verifyTraversable();

        if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $_row = $row;
        } else if (is_string($row) OR is_numeric($row)) {
            $_row = $this->fetchRow($this->select()->where($this->_traversal['column'] . ' = ?', $row));
            if (null === $_row) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Cannot find row '{$this->_traversal['column']}' = {$row}");
            }
        } else {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Expecting instance of Zend_Db_Table_Row_Abstract, a string, or numeric");
        }

        $left = $_row->{$this->_traversal['left']};
        $right = $_row->{$this->_traversal['right']};

        if (null === $select) {
            $select = $this->select();
        }

        $select->where("{$this->_traversal['left']} > ?", (double) $left)
                ->where("{$this->_traversal['left']} < ?", (double) $right)
        ;

        $orderPart = $select->getPart('order');
        if (empty($orderPart))
            $select->order($this->_traversal['left']);

        return $this->fetchAll($select);
    }

    /**
     * Fetches all descendents of a given node and returns them as a tree
     *
     * @param Zend_Db_Table_Row_Abstract|string|int $rows- Row object or value of row id or array of rows
     * @param Zend_Db_Select $select - optional select object
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchTree(Core_Model_Item_Abstract $resource, $row = null, Zend_Db_Select $select = null) {
        $this->_verifyTraversable();

        if (null === $select) {
            $select = $this->select();
        }

        $select->setIntegrityCheck(false)
                ->from(array('node' => $this->getName()))
                ->join(array('parent' => $this->getName()), null, array(
                    'tree_depth' => new Zend_Db_Expr("COUNT(parent.{$this->_traversal['refColumn']})")
                        )
                )
                ->group("node.{$this->_traversal['column']}")
        ;

        if (null !== $row) {
            if ($row instanceof Zend_Db_Table_Row_Abstract) {
                $_row = $row;
            } else if (is_string($row) OR is_numeric($row)) {
                $_row = $this->fetchRow($this->select()->where($this->_traversal['column'] . ' = ?', $row));
                if (null === $_row) {
                    require_once 'Zend/Db/Table/Exception.php';
                    throw new Zend_Db_Table_Exception("Cannot find row '{$this->_traversal['column']}' = {$row}");
                }
            } else {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Expecting instance of Zend_Db_Table_Row_Abstract, a string, or numeric");
            }

            $left = (double) $_row->{$this->_traversal['left']};
            $right = (double) $_row->{$this->_traversal['right']};

            if ($left > 0 AND $right > 0) {
                $select->where("node.{$this->_traversal['left']} >= {$left} AND node.{$this->_traversal['left']} < {$right}");
            } else {
                // Traversal information is bad, throw an exception
                $id = $_row->{$this->_traversal['column']};
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Left/right values for row '{$this->_traversal['column']}' = '{$id}' in table '{$this->_name}' must be greater than zero to fetch tree.");
            }
        }

        $select->where("node.{$this->_traversal['left']} BETWEEN parent.{$this->_traversal['left']} AND parent.{$this->_traversal['right']}");

        $orderPart = $select->getPart('order');
        if (empty($orderPart)) {
            $select->order("node.{$this->_traversal['left']}");
        }

        return $this->fetchAll($select);
    }

    /**
     * Fetches all ancestors of a given node
     *
     * @param Zend_Db_Table_Row_Abstract|string $row - Row object or value of row id
     * @param Zend_Db_Select $select - optional custom select object
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchAllAncestors($row, Zend_Db_Select $select = null) {
        $this->_verifyTraversable();

        if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $_row = $row;
        } else if (is_string($row) OR is_numeric($row)) {
            $_row = $this->fetchRow($this->select()->where($this->_traversal['column'] . ' = ?', $row));
            if (null === $_row) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Cannot find row '{$this->_traversal['column']}' = {$row}");
            }
        } else {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Expecting instance of Zend_Db_Table_Row_Abstract, a string, or numeric");
        }

        $left = $_row->{$this->_traversal['left']};
        $right = $_row->{$this->_traversal['left']};

        if (null === $select) {
            $select = $this->select();
        }

        $select->where("{$this->_traversal['left']} < ?", (double) $left)
                ->where("{$this->_traversal['right']} > ?", (double) $right)
        ;

        $orderPart = $select->getPart('order');
        if (empty($orderPart)) {
            $select->order($this->_traversal['left']);
        }

        return $this->fetchAll($select);
    }

    /**
     * Prepares the traversal information
     *
     */
    protected function _initTraversal() {
        if (empty($this->_traversal))
            return;

        $columns = $this->getColumns();

        // Verify 'left' value and column
        if (!isset($this->_traversal['left'])) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("'left' value must be specified for tree traversal");
        }

        if (!in_array($this->_traversal['left'], $columns)) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Column '" . $this->_traversal['left'] . "' not found in table for tree traversal");
        }

        // Verify 'right' value and column
        if (!isset($this->_traversal['right'])) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("'right' value must be specified for tree traversal");
        }

        if (!in_array($this->_traversal['right'], $columns)) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Column '" . $this->_traversal['right'] . "' not found in table for tree traversal");
        }

        // Check for identifying column
        if (!isset($this->_traversal['column'])) {
            if (!isset($this->_primary)) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Unable to determine primary key for tree traversal");
            }

            if (count($this->_primary) > 1) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Cannot use compound primary key as identifying column for tree traversal, please specify the column manually");
            }

            $this->_traversal['column'] = current((array) $this->_primary);
        }

        // Check for reference column
        if (!isset($this->_traversal['refColumn'])) {
            if (!array_key_exists('Parent', $this->_referenceMap)) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Unable to determine reference column for traversal, and did not find reference rule 'Parent' in reference map");
            }

            $refColumn = $this->_referenceMap['Parent']['refColumns'];
            if (!is_string($refColumn) AND count($refColumn) > 1) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Cannot use compound primary key as reference column for tree traversal, please specify the reference column manually");
            }

            $this->_traversal['refColumn'] = $refColumn;
        }

        $this->_isTraversable = true;
    }

    /**
     * Verifies that the current table is a traversable
     * 
     * @throws Zend_Db_Exception - Table is not traversable
     */
    protected function _verifyTraversable() {
        if (!$this->_isTraversable) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Table {$this->_name} is not traversable");
        }
    }

    public function addComment(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster, $body, $parent_id = null) {
        $row = $this->createRow();

        if (isset($row->resource_type)) {
            $row->resource_type = $resource->getType();
        }

        $row->resource_id = $resource->getIdentity();
        $row->poster_type = $poster->getType();
        $row->poster_id = $poster->getIdentity();
        $row->parent_id = $parent_id;

        $row->creation_date = date('Y-m-d H:i:s');
        $row->body = $body;
        $row->save();

        if (isset($resource->comment_count)) {
            $resource->comment_count++;
            $resource->save();
        }

        return $row;
    }

    public function getCommentSelect(Core_Model_Item_Abstract $resource) {
        $select = $this->select();

        if (!$this->_custom)
            $select->where('node.resource_type = ?', $resource->getType());

        $select->where('node.resource_id = ?', $resource->getIdentity());
        return $select;
    }

    public function getCommentCount(Core_Model_Item_Abstract $resource) {
        if (isset($resource->comment_count)) {
            return $resource->comment_count;
        }

        $select = new Zend_Db_Select($this->getAdapter());
        $select->from($this->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

        if (!$this->_custom) {
            $select->where('resource_type = ?', $resource->getType());
        }

        $select->where('resource_id = ?', $resource->getIdentity());

        $data = $select->query()->fetchAll();
        return (int) $data[0]['count'];
    }
    
    public function getCommentTable() {
        return $this;
    }

}
