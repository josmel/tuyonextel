<?php

class Core_Form_Validate_ConfirmPassword extends Zend_Validate_Abstract {
    
    const NOT_MATCH = 'notMatch';
    private $_matchElement = null;
    public function __construct($element) {
       $this->_matchElement = $element;
    }

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );

    public function isValid($value) {
        $this->_setValue($value);

        if ($this->_matchElement != null) {
            if (($value != $this->_matchElement->getValue())) {
                $this->_error(self::NOT_MATCH);
                return false;
            }
        }
        
        return true;
    }
}

?>
