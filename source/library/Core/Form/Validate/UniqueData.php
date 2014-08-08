<?php

class Core_Form_Validate_UniqueData extends Zend_Validate_Abstract {
    
    const NOT_UNIQUE = 'notUnique';
    
    private $_adapter = null;
    private $_table = null;
    private $_column = null;
    
    public function __construct($adapter, $table, $column) {
       $this->_adapter = $adapter;
       $this->_table = $table;
       $this->_column = $column;
    }

    protected $_messageTemplates = array(
        self::NOT_UNIQUE => 'This value has exists'
    );

    public function isValid($value) {
        $this->_setValue($value);

        try {
            $smt = $this->_adapter->select()->from(
                    $this->_table, 
                    array('count' => 'COUNT('.$this->_column.')')
                )->where($this->_column. ' = ?', $value)->query();

            $row = $smt->fetch();
            
            $smt->closeCursor();
        } catch (Exception $ex) {
            return false;
        }
        
        if ($row['count'] > 0) {
            if (($value != $this->_matchElement->getValue())) {
                $this->_error(self::NOT_UNIQUE);
                return false;
            }
        }
        
        return true;
    }
}

?>
