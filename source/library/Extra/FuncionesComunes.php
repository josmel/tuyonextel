<?php 
class Extra_FuncionesComunes extends Zend_Db_Table_Abstract
{
    
    public function init()
    {
        $this->_db = $this->getDefaultAdapter();
    }   
    
    public function cursorFetch($consulta)
    {
        $cursor = $this->_db->query($consulta);
        $result = $cursor->fetch();
        $cursor->closeCursor();
        return $result;
    }

    public function cursorFetchAll($consulta)
    {
        $cursor = $this->_db->query($consulta);
        $result = $cursor->fetchAll();
        $cursor->closeCursor();
        return $result;
    }

    public function cursorFetchOne($consulta)
    {
        $cursor = $this->_db->query($consulta);
        $result = $cursor->fetch();
        if (is_array($result)) {
            foreach ($result as $res):
                $resultado = $res;
            endforeach;
        } else {
            $resultado = '';
        }
        $cursor->closeCursor();
        return $resultado;
    }

    public function cursorFetchPair($consulta)
    {        
        $cursor = $this->_db->query($consulta);
        $result = $cursor->fetchAll();
        $resultado = array();
        foreach ($result as $rs):
            $key1 = $this->obtenerPrimerValorArray($rs);
            $key2 = $this->obtenerSegundoValorArray($rs);
            $resultado[$key1] = $key2;
        endforeach;
        $cursor->closeCursor();
        return $resultado;
    }

    public function cursorFetchAssoc($consulta)
    {
        $cursor = $this->_db->query($consulta);
        $db = $this->getDefaultAdapter();
        $cursor = $db->query($consulta);
        $result = $cursor->fetchAll();
        $resultado = array();
        foreach ($result as $res):
            $key = $this->obtenerPrimerValorArray($res);
            $resultado[$key] = $res;
        endforeach;
        $cursor->closeCursor();
        return $resultado;
    }
    
    function obtenerPrimerValorArray($matriz)
    {
        foreach ($matriz as $key => $valor)
            return $valor;
    }
    
    function obtenerSegundoValorArray($matriz)
    {
        $i=0;
        foreach ($matriz as $key => $valor) {
            if($i==1)
                return $valor;
            else
                $i++;
        }
    }

    public function cursorFetchCol($consulta)
    {
        $cursor = $this->_db->query($consulta);
        $db = $this->getDefaultAdapter();
        $cursor = $db->query($consulta);
        $result = $cursor->fetchAll();
        $resultado = array();
        foreach ($result as $res):
            $resultado[] = $this->obtenerPrimerValorArray($res);
        endforeach;
        $cursor->closeCursor();
        return $resultado;
    }

    public function bitly_shorten($url)
    {
        $client = new Zend_Http_Client('http://api.bit.ly/shorten');        
        $client->setParameterGet(
            array(
                'version' => '3.0.1',
                'longUrl' => $url,
                'login' => 'jamesotiniano',
                'apiKey' => 'R_f1753e95b076d9218f58d4ed6b09bf92'
            )
        );

        $response = $client->request();
        if ($response->isSuccessful()) {
            $phpNative = Zend_Json::decode($response->getBody());
            if ($phpNative['errorCode'] == 0) {
                return $phpNative['results'][$url]['shortUrl'];
            }
        }
        return "";
    }

}