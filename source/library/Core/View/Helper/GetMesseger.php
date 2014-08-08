<?php
/**
 *
 * @author Marrselo
 */
class Core_View_Helper_GetMesseger extends Zend_View_Helper_Abstract {

    /**
     * @param  String
     * @return string
     */
    public function getMesseger() {
        $flashMessage = new Core_Controller_Action_Helper_FlashMessengerCustom();        
        $arrayClass = array(
            'info' => 'alert info',
            'success' => 'alert success',
            'warning' => 'alert',
            'error' => 'alert error');
        
        $arrayMsgs = array('info' => '', 'success' => '', 'warning' => '', 'error' => '');

        $messages = $flashMessage->getMessages();
        foreach($messages as $message) {
            $arrayMsgs[$message->level] .= 
                ($arrayMsgs[$message->level] == '' ? '' : '<br>').$message->message;
        }
        if(Zend_Registry::isRegistered('Temp_FMessages')) {
            $messages = Zend_Registry::get('Temp_FMessages');
            foreach($messages as $message) {
                $arrayMsgs[$message->level] .= 
                    ($arrayMsgs[$message->level] == '' ? '' : '<br>').$message->message;
            }
        }
        
        foreach ($arrayMsgs as $type => $msg) {
            if($msg != '' && isset($arrayClass[$type])) {
                echo'<div class="' . $arrayClass[$type] . '">';          
                echo $msg;
                echo'</div>';
            }
        }            
    }
}
