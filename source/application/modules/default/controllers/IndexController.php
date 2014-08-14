<?php

class Default_IndexController extends App_Controller_Action_Portal {

    public function init() {
        $this->_helper->layout->setLayout('layout-nextel');
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $model = new App_Model_ConfigPerfil();
        $controller = $this->getParam('controller');
        $model->getPerfil($ua, $controller);
    }

    public function indexAction() {
        $ft = $this->_getParam('ft', '');
        $rt = $this->_getParam('rt', '');
        $portal = new App_Entity_PooArweb();
        $nusoap = new App_Entity_Nusoap();
        $DESTACADOs = $portal->getDestacado();
        $dataContenidoFt = $nusoap->getContenido("wsRTConsultarAlbum", "1", "42", "_ftwap", "5", "0");
        $dataContenidoRT = $nusoap->getContenido("wsRTConsultarAlbum", "1", "68", "_RTWAP", "5", "0");
        if (isset($_SERVER['HTTP_X_UP_SUBNO']) && $_SERVER['HTTP_X_UP_SUBNO'] != "") {
            header("Location: 2g.php");
            exit;
            $dosG = $_SERVER['HTTP_X_UP_SUBNO'];
            $url = file_get_contents("http://wsperu.multibox.pe/ws-nextel.php?nextel-2g=$dosG");
            $conteDosG = json_decode($url);
            $msidn = "51" . $conteDosG->PTN;
            $str_number = $msidn;
        }
        $this->view->str_number = $str_number;
        $this->view->portal = $portal;
        $this->view->DESTACADOs = $DESTACADOs;
        $this->view->dataContenidoFt = $dataContenidoFt;
        $this->view->dataContenidoRt = $dataContenidoRT;
        $this->view->ft = $ft;
        $this->view->rt = $rt;
    }

    public function validacionAction() {
        $v = $this->_getParam('v', "1");
        $item = $this->_getParam('item', NULL);
        $cod = $this->_getParam('cod', NULL);
        $serv = $this->_getParam('serv', header("Location: http://bip.pe/pe/ne/wap/nextelportal/") && die());
        $portalCobro = new App_Entity_CobroShootLink();
        if (isset($_SERVER['HTTP_MSISDN']) && $_SERVER['HTTP_MSISDN'] != "") {
            $str_number = $_SERVER['HTTP_MSISDN'];
            $portalCobro->shootLink($str_number, $c, "0", $serv, $item, $cod);
        }
        if (isset($_SERVER['HTTP_COOKIE']) && $_SERVER['HTTP_COOKIE'] != "") {
            $b = strpos($_SERVER['HTTP_COOKIE'], "msisdn=") + 7;
            if ($b != "7") {
                $num = substr($_SERVER['HTTP_COOKIE'], $b);
                $str_number = $num;
                $portalCobro->shootLink($str_number, $c, "0", $serv, $item, $cod);
            }
        }
        if (isset($_SERVER['HTTP_X_UP_SUBNO']) && $_SERVER['HTTP_X_UP_SUBNO'] != "") {
            $dosG = $_SERVER['HTTP_X_UP_SUBNO'];
            //$dosG = "PER0006111680_net2.nextelinternational.com";
            $url = file_get_contents("http://wsperu.multibox.pe/ws-nextel.php?nextel-2g=$dosG");
            $conteDosG = json_decode($url);
            $str_number = "51" . $conteDosG->PTN;
            $portalCobro->shootLink($str_number, $c, "0", $serv, $item, $cod);
        }

        if (isset($_GET['nue'])) {
            $number = $_GET['nue'];
            if (( strlen($number) == 11)) {
                if (substr($number, 0, 2) == "51") {
                    $portalCobro->shootLink2($number, $c, "0", $serv, $item, $cod);
                }
            } elseif (strlen($number) == 9) {
                if (substr($number, 0, 2) !== "51") {
                    $portalCobro->shootLink2("51" . $number, $c, "0", $serv, $item, $cod);
                }
            }
        }


        if (isset($_GET['num'])) {
            $number = $_GET['num'];
            if (( strlen($number) == 11)) {
                if (substr($number, 0, 2) == "51") {

                    $portalCobro->shootLink($number, $c, "1", $serv, $item, $cod);
                }
            } elseif (strlen($number) == 9) {
                if (substr($number, 0, 2) !== "51") {
                    $portalCobro->shootLink("51" . $number, $c, "1", $serv, $item, $cod);
                }
            }
        }

        if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']) && $_SERVER['HTTP_X_UP_CALLING_LINE_ID'] != "") {
            $str_number = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
            $portalCobro->shootLink($str_number, $c, "0", $serv, $item, $cod);
        }

        $this->view->serv = $serv;
        $this->view->c = $c;
        $this->view->v = $v;
    }

    public function suscribeteAction() {

        $v = isset($_GET['serv']) ? $_GET['serv'] : "1";
        $nue = isset($_GET['nue']) ? $_GET['nue'] : "";
        $item = isset($_GET['item']) ? $_GET['item'] : "";
        $cod = isset($_GET['cod']) ? $_GET['cod'] : "";
        $saldo = isset($_GET['saldo']) ? $_GET['saldo'] : "";
        if ($v == "1")
            header("Location: /");


        $msidn = (isset($_SERVER['HTTP_MSISDN'])) ? $_SERVER['HTTP_MSISDN'] : $nue;

//        $wsClient = 'http://174.121.234.90/moso/WSMultimedia/wstools.asmx?wsdl';
//        $SoapClien = new nusoap_client($wsClient, true);

        $tparam = array(
            "operadora" => "3",
            "numUser" => $msidn,
        );
        $token = base64_encode($msidn);

        switch ($v) {
            case "_FTWAPNX":
                $tit = "Club de M&uacute;sica";
                $img = MEDIA_URL . "img/nextel/banner-musica-nextel.gif";
                $texto = "Te est&aacute;s suscribiendo al servicio de Fulltracks, donde podr&aacute;s descargar todas las canciones
     que puedas por solo US$ 0.59 diarios.";
                $ruta = "http://bip.pe/pe/ne/wap/ft/?token=" . $token;
                $dir = "../ft";
                $tparam['servicio'] = "130";
                break;

            case "_RTWAPNX":
                $tit = "Tonos";
                $img = MEDIA_URL . "img/nextel/banner-nextel-tonos.jpg";
                $texto = "Te est&aacute;s suscribiendo al servicio de Tonos, cada semana podr&aacute;s descargar el tono que desees. Costo US$ 1.80 por suscripci&oacute;n semanal.";
                $ruta = "http://bip.pe/pe/ne/wap/rt/?token=" . $token;
                $dir = "../rt";
                $tparam['servicio'] = "124";
                break;
            case "_DEWAPNX":
                $tit = "Dedicatorias";
                $img = MEDIA_URL . "img/nextel/banner-dedicatorias-nextel.gif";
                $texto = "Te est&aacute;s suscribiendo al servicio de Dedicatorias. 
Selecciona una canci&oacute;n y se la enviaremos a la persona que elijas. Costo por suscripci&oacute;n semanal US$ 1.80.";
                $dir = "../dedicatorias";
                $ruta = "http://bip.pe/pe/ne/wap/dedicatorias/?token=" . $token;
                $tparam['servicio'] = "146";
                break;
            case "_IMGWAPNX":
                $tit = "Im&aacute;genes";
                $img = MEDIA_URL . "img/nextel/banner_imagenes_nextel.jpg";
                $texto = "Te est&aacute;s suscribiendo al servicio de Imagenes. 
Selecciona una imagen y se la enviaremos a la persona que elijas. Costo por suscripci&oacute;n semanal US$ 1.80.";
                $ruta = "http://bip.pe/pe/ne/wap/img/?token=" . $token;
                $dir = "../img";
                $tparam['servicio'] = "141";
                break;
            default :
                break;
        }
        $this->view->tit = $tit;
        $this->view->img = $img;
        $this->view->texto = $texto;
        $this->view->ruta = $ruta;
        $this->view->dir = $dir;
        $this->view->servicio = $tparam['servicio'];

        $this->view->v = $v;
        $this->view->nue = $nue;
        $this->view->item = $item;
        $this->view->cod = $cod;
        $this->view->saldo = $saldo;
    }

    public function confirmacionAction() {

        if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']) && $_SERVER['HTTP_X_UP_CALLING_LINE_ID'] != "") {
            $str_number = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
        } else {
            $str_number = $_GET['nu'];
            if (!isset($str_number) || $str_number == "") {
                if (isset($_GET['nue'])) {
                    $str_number = $_GET['nue'];
                    if (strlen($str_number) < 11) {
                        $str_number = "51" . $str_number;
                    }
                }
            }
        }
    }

    public function confirmaSuscripcionAction() {

        //include "log/md_log.php";
        $item = isset($_POST['item']) ? $_POST['item'] : NULL;
        $cod = isset($_POST['cod']) ? $_POST['cod'] : NULL;
        $portalCobroSuscripcion = new App_Entity_CobroShootLink();

        if (isset($_SERVER['HTTP_MSISDN']) && $_SERVER['HTTP_MSISDN'] != "") {
            $str_number = $_SERVER['HTTP_MSISDN'];
            $portalCobroSuscripcion->shootLinkSuscripcion($str_number, $c, "0", $_POST['serv'], $item, $cod);
        }
        if (isset($_SERVER['HTTP_X_UP_SUBNO']) && $_SERVER['HTTP_X_UP_SUBNO'] != "") {
            $dosG = $_SERVER['HTTP_X_UP_SUBNO'];
            //$dosG = "PER0006111680_net2.nextelinternational.com";
            $url = file_get_contents("http://wsperu.multibox.pe/ws-nextel.php?nextel-2g=$dosG");
            $conteDosG = json_decode($url);
            $str_number = "51" . $conteDosG->PTN;
            $portalCobroSuscripcion->shootLinkSuscripcion($str_number, $c, "0", $_POST['serv'], $item, $cod);
        }

        if (isset($_POST['nue'])) {
            $number = $_POST['nue'];
            if (( strlen($number) == 11)) {
                if (substr($number, 0, 2) == "51") {
                    $portalCobroSuscripcion->shootLinkSuscripcion($number, $c, "0", $_POST['serv'], $item, $cod);
                }
            } elseif (strlen($number) == 9) {
                if (substr($number, 0, 2) !== "51") {
                    $portalCobroSuscripcion->shootLinkSuscripcion("51" . $number, $c, "0", $_POST['serv'], $item, $cod);
                }
            }
        }


        if (isset($_POST['num'])) {
            $number = $_POST['num'];
            if (( strlen($number) == 11)) {
                if (substr($number, 0, 2) == "51") {
                    $portalCobroSuscripcion->shootLinkSuscripcion($number, $c, "1", $_POST['serv'], $item, $cod);
                }
            } elseif (strlen($number) == 9) {
                if (substr($number, 0, 2) !== "51") {
                    $portalCobroSuscripcion->shootLinkSuscripcion("51" . $number, $c, "1", $_POST['serv'], $item, $cod);
                }
            }
        }

        if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']) && $_SERVER['HTTP_X_UP_CALLING_LINE_ID'] != "") {
            $str_number = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
            $portalCobroSuscripcion->shootLinkSuscripcion($str_number, $c, "0", $_POST['serv'], $item, $cod);
        }

        if (isset($_SERVER['HTTP_COOKIE']) && $_SERVER['HTTP_COOKIE'] != "") {
            $b = strpos($_SERVER['HTTP_COOKIE'], "msisdn=") + 7;
            if ($b != "7") {
                $num = substr($_SERVER['HTTP_COOKIE'], $b);
                $str_number = $num;
                $portalCobroSuscripcion->shootLinkSuscripcion($str_number, $c, "0", $_POST['serv'], $item, $cod);
            }
        }

        if ($exito) {
            echo "estimado usuario gracias por la suscripcion ";
        } else {
            header("Location: /?error=confirma");
            exit;
        }
    }

    public function suscripcionExitosaAction() {
        $trans = isset($_GET['trans']) ? $_GET['trans'] : NULL;
        $iden = isset($_GET['iden']) ? $_GET['iden'] : NULL;
        $this->view->trans = $trans;
        $this->view->iden = $iden;
    }

    function validarTarifa($operadora, $numServ, $catalogo, $tarifa, $codigoEncriptado) {

        $wsCliente = 'http://174.121.234.90/moso/WSMultimedia/wstools.asmx?wsdl';

        $SoapClient = new nusoap_client($wsCliente, true);
        if ($Error = $SoapClient->getError()) {
            echo "No se pudo realizar la operaci&oacute;n de conexi&oacute;n[" . $Error . "]";
            echo "<body></body></html>";
            die();
        }
        if ($SoapClient->fault) { // Si
            echo 'No se pudo completar la operaci&oacute;n ...';
            echo "<body></body></html>";
            die();
        } else { // No
            $aError = $SoapClient->getError();
            // Hay algun error ?
            if ($Error) { // Si
                echo 'Error:' . $Error;
                echo "<body></body></html>";
                die();
            }
        }

        $Parametros = array('operadora' => $operadora, 'numServ' => $numServ, 'catalogo' => $catalogo, 'tarifa' => $tarifa, 'codigoEncriptado' => $codigoEncriptado);
        //print_r( $Parametros);
        $Respuesta = $SoapClient->call("RegistrarLicenciaMultimedia", $Parametros);
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
    }

    public function goAction() {

        $_getVars = $_GET;
        $_key = array_keys($_getVars);
        $_valor = array_values($_getVars);

        $numGet = count($_getVars);
        $bucleGet = $numGet - 1;
        for ($x = 0; $x <= $bucleGet; $x++) {
            if ($x == 0) {
                $link.=$_valor[$x];
            } else {
                $link.="&" . $_key[$x] . "=" . $_valor[$x];
            }
        }
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $model = new App_Model_ConfigPerfil();
        $controller = $this->getParam('controller');
        $model->getPerfil($ua, $controller, $link);
        header("Location: " . $link);
        exit;
    }

    public function legalAction() {

        if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']) && $_SERVER['HTTP_X_UP_CALLING_LINE_ID'] != "") {
            $str_number = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
        } else {
            $str_number = $_GET['nu'];
            if (strlen($str_number) < 11) {
                $str_number = "51" . $str_number;
                $validar_sms = true;
            }
        }
        $nuetxt = "";
        if (isset($_GET['nue'])) {
            $nue = $_GET['nue'];
            $validar_sms = false;
            if (strlen($nue) < 11) {
                $nue = "51" . $nue;
            }
            $nuetxt = "&amp;nue=" . $nue;
        }

        if (isset($_GET['validar_sms']) && $_GET['validar_sms'] != "") {
            $validar_sms = $_GET['validar_sms'];
        }

        if ($validar_sms == true)
            $val_sms = 1;
        else
            $val_sms = 0;
    }

}
