<?php
class Core_Controller_Action_Helper_FlashMessengerCustom
    extends Zend_Controller_Action_Helper_FlashMessenger
{
    
    const DEBUG = 'debug';
    const INFO = 'info';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'error';
    
    
    /**
     * Niveles de mensaje
     * 
     * @var array
     */
    private $_levels = array('debug', 'info', 'success', 'warning', 'error');

    /**
     * Agrega un mensaje
     * 
     * @param string $message Mensaje a mostrar en pantalla
     * @param string $level Nivel del mensaje
     * 
     * @return void
     */
    public function addMessage($message, $level = self::INFO, $type = 'FLASH')
    {
        $msg = new stdClass();
        $msg->message = $message;
        $msg->level = $level;    
        if($type == 'TEMP') {
            $tempFMessages = array();
            if (Zend_Registry::isRegistered('Temp_FMessages')) 
                $tempFMessages = Zend_Registry::get('Temp_FMessages');
            $tempFMessages[] = $msg;
            Zend_Registry::set('Temp_FMessages', $tempFMessages);
        }
        else parent::addMessage($msg);
    }
    
    /**
     * Permite llamadas dinámicas 
     * Ejem:
     *  - $this->info('Mensaje')
     *  - $this->success('Mensaje')
     *  - $this->error('Mensaje')
     *  
     * @param unknown_type $name
     * @param unknown_type $params
     */
    public function __call($name, $params)
    { 
        if (in_array($name, $this->_levels)) {
            $type = 'FLASH';
            if(isset($params[1])) $type = $params[1];
            if(is_array($params[0])) {
                foreach ($params[0] as $msg) {
                    $this->addMessage($msg, $name, $type);
                }
            } else { 
                $this->addMessage($params[0], $name, $type);
            }
        }
    }
    
    public function getTempMessages() {
        return $this->_tempMessages;
    }
}
