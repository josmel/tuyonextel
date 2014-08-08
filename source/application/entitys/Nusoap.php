<?php
 require_once('nusoap/lib/nusoap.php');
class App_Entity_Nusoap {

    function getContenido($metodo, $operadora, $album, $keyword, $filasxPagina, $numPagina) {
       
        $wsCliente = 'http://174.121.234.90/moso/WSMultimedia/wsRT.asmx?wsdl';

        $SoapClient = new nusoap_client($wsCliente, true);

        if ($Error = $SoapClient->getError()) {
            echo "No se pudo realizar la operaci&oacute;n de conexi&oacute;n[" . $Error . "]";
            die();
        }
        if ($SoapClient->fault) { // Si
            echo 'No se pudo completar la operaci&oacute;n ...';
            die();
        } else { // No
            $aError = $SoapClient->getError();
            // Hay algun error ?
            if ($Error) { // Si
                echo 'Error:' . $Error;
                die();
            }
        }

        $Parametros = array('operadora' => $operadora, 'album' => $album, 'keyword' => $keyword, 'filasxPagina' => $filasxPagina, 'numPagina' => $numPagina);
        //print_r( $Parametros);
        $Respuesta = $SoapClient->call($metodo, $Parametros);
        //print_r($Respuesta);
        if ($SoapClient->fault) { // Si
            echo 'No se pudo completar la operaci&oacute;n, por favor ingrese un texto a buscar.';
            die();
        } else { // No
            $Error = $SoapClient->getError();
            // Hay algun error ?
            if ($Error) { // Si
                echo 'Error:' . $Error;
                die();
            }
        }

        list($clave, $b2) = each($Respuesta);
        list($clave, $b3) = each($b2);
        if ($this->is_vector($b3) == 0) {
            $b3 = $b2;
        }
        return $b3;
    }

    function is_vector(&$array) {
        if (!is_array($array) || empty($array)) {
            return -1;
        }
        $next = 0;
        foreach ($array as $k => $v) {
            if ($k !== $next)
                return 0;
            $next++;
        }
        return 1;
    }

}
