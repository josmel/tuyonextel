<?php

/**
 * Description of User
 *
 * @author Josmel
 */
class App_Model_ConfigPerfil extends App_Db_Table_Abstract {

    protected $_name = 'perfil';

    const TABLA_CONFIG = 'perfil';
    const INTERMEDIO = '3';
    const SMART = '5';

   
    public function getPerfil($userAgent, $controller,$link = NULL)
    {
        
      
        $query = $this->getAdapter()
                ->select()->from($this->_name, array('perfil'))
                ->where('user_agent = ?', $userAgent);

        $result = $this->getAdapter()->fetchRow($query);
        // registrar UserAgent
        if (!$result){
                        $findme   = 'Android';
                        $findme1   = 'iPhone';
                        $findme2   = 'Windows NT';
                        $findme3   = 'Macintosh';

                        $pos = strpos($userAgent, $findme);
                        $pos1 = strpos($userAgent, $findme1);
                        $pos2 = strpos($userAgent, $findme2);
                        $pos3 = strpos($userAgent, $findme3);
                        $perfil = ($pos === false and $pos1 === false and $pos2 === false and $pos3 === false)?self::INTERMEDIO:self::SMART;
                $result = array(
                      'user_agent' => $userAgent,
                          'perfil' => $perfil,
                  'fecha_creacion' => date('Y-m-d'),
                );
         //       $id = $this->getAdapter()->insert($this->_name, $result);
        }
        //$this->_cache->save($result, $cacheId, array(), 72000);
        $result['controller'] = $controller;
        // registrar CDR
        $this->registerCdr($userAgent, $result,$link);

        return $result;
    }

    
    public function registerCdr($userAgent, $result, $link = NULL) {
    	$telefono = (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']))?$_SERVER['HTTP_X_UP_CALLING_LINE_ID']:'';
        //foreach ($headers as $header => $value) {echo "$header: $value <br />\n";}exit;
    	$datos = array(
    	    'telefono'   => $telefono,
    	    'user_agent' => $userAgent,
    	    'perfil'     => $result['perfil'],
    	    'url'        => ($link)?$link:$result['controller'],
    	    'fecha'      => date('Y-m-d'),
    	    'hora'       => date('H:i:s'),
    	);
    	$model = new App_Model_Cdr();
    	$id = $model->saveCdr($datos);
    	
    	return $id;
    	
    	
    }

}