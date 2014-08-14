<?php
require_once('nusoap/lib/nusoap.php');

class App_Entity_CobroShootLink {

    function EstaSuscrito($NUMBER, $codigo) {
        $tparam = array(
            "operadora" => 3,
            "numUser" => $NUMBER,
            "servicio" => $codigo
        );
        $wsClient = 'http://174.121.234.90/moso/WSMultimedia/wstools.asmx?wsdl';
        $SoapClien = new nusoap_client($wsClient, true);
        $rptsus = $SoapClien->call("EstaSuscrito", $tparam);
        return $rptsus['EstaSuscritoResult'];
    }

    function EstaSuscritoFree($NUMBER) {

        $wsEsFreeUser = 'http://174.121.234.90/moso/WSMultimedia/wstools.asmx?wsdl';
        $SoapEsFreeUser = new nusoap_client($wsEsFreeUser, true);
        $parametros = array(
            'pais' => "51",
            'numuser' => $NUMBER,
        );
        $resultado = $SoapEsFreeUser->call('EsFreeUser',$parametros);
        return $resultado['EsFreeUserResult'];
    }

    function file_get_contents($NUMBER, $cod) {
        $xml = file_get_contents("http://174.121.234.90/Moso/WSMultimedia/wsTOOLS.asmx/RegistrarDescarga?operadora=3&numUser=" . $NUMBER . "&idContenido=0&catalogo=" . $cod . "&esGratis=True");
        $x = new SimpleXMLElement($xml);
        $ID = $x;

        /////pagina llevar............
        header("Location: http://174.121.234.90/nxpe/Baja.aspx?id=" . $ID);
        getPutsDescargaLOG("http://174.121.234.90/mvpe/Baja.aspx", $ID, $NUMBER, $cod);
        exit;
    }

    function shootLink($NUMBER, $CODIGO, $VERIFICATION, $serv = NULL, $item = NULL, $cod = NULL) {

        switch ($serv):
            case '_FTWAPNX' :
                $codigo = 130;
                $rutaServ = "http://bip.pe/pe/ne/wap/ft/";
                //$rutaServ = "http://local.ft.pe/";
                break;
            case '_RTWAPNX' :
                $codigo = 124;
                $rutaServ = "http://bip.pe/pe/ne/wap/rt-devel/";
                break;
            case '_IMGWAPNX' :
                $codigo = 141;
                $rutaServ = "http://bip.pe/pe/ne/wap/img-devel/";
                break;
            default :  //cambiando valor de 124 a 0
                $codigo = 0;
                break;
        endswitch;
        $EstaSuscritoResult = $this->EstaSuscrito($NUMBER, $codigo);
        if ($EstaSuscritoResult == '1' and $item == "true" and is_numeric($cod)) {
            // Descarga la canción(Tonos)  //empesamos a cobrar 
            $WSCobrosXBIFree = $this->EstaSuscritoFree($NUMBER);
            if ($WSCobrosXBIFree == "1") {
                $this->file_get_contents($NUMBER, $cod);
            } else {
                $RespuestaXBI = $this->WSCobrosXBI($NUMBER, $codigo);
                if ($RespuestaXBI == "0") {
                    $this->file_get_contents($NUMBER, $cod);
                } else {
                    $cadena = '&nue=' . $NUMBER;
                    header('Location: /?error=007' . $cadena);
                    exit;
                }
            }
            header('Location: /?error=008');
            exit;
        } else { 
            if ($EstaSuscritoResult == '1') { 
                header("Location: $rutaServ?numero=$NUMBER");
                exit;
            } else {
                if ($EstaSuscritoResult == '0' and $item = "true" and is_numeric($cod)) {
                    header("Location: suscribete?serv=$serv&item=true&cod=$cod");
                    exit;
                }
                header("Location: suscribete?serv=$serv");
                exit;
            }
        }
        die();
    }

    function shootLinkSuscripcion($NUMBER, $CODIGO, $VERIFICATION, $serv, $item = NULL, $cod = NULL) {
        
        
        if ((strlen($NUMBER) == 11) and ( substr($NUMBER, 0, 2) == "51")) {
            ///ver si esun free
            $WSCobrosXBIFree = $this->EstaSuscritoFree($NUMBER);
            $parmorigen = '&o=7';
            switch ($serv):
                case '_RTWAPNX':
                    // Suscribirlo
                    file_get_contents("http://174.121.234.90/SVARequest/Request.aspx?op=3&nu=" . $NUMBER . "&sc=4556&su=1&k={sys}.$>_RTWAPNX&v=0" . $parmorigen);
                    if (is_numeric($cod) and $item == "true") {
                        if ($WSCobrosXBIFree == "1") {
                            $this->file_get_contentsRT($NUMBER, $cod);
                        } else {
                            $RespuestaXBI = $this->WSCobrosXBI($NUMBER, 124);
                            // cobraleXBI y descargalo y muestrale el mensaje para desafiliacion
                            if ($RespuestaXBI == "0") { // Descarga el contenido
                                $this->file_get_contentsRT($NUMBER, $cod);
                            } else {
                                header("Location: /?rt=no-saldo");
                                // No tiene crédito enviarlo al home Nextel
                            }
                        }
                    }
                    header("Location: http://bip.pe/pe/ne/wap/rt-devel/?num=$NUMBER");
                    exit;
                    break;
                case '_FTWAPNX':
                    // Suscribirlo
                    file_get_contents("http://174.121.234.90/SVARequest/Request.aspx?op=3&nu=" . $NUMBER . "&sc=4556&su=1&k={sys}.$>_FTWAPNX&v=0" . $parmorigen);
                    if ($WSCobrosXBIFree == "1") {
                        header("Location: http://bip.pe/pe/ne/wap/ft/?nue=$NUMBER"); exit;
                    } else {
                        // cobraleXBI y descargalo y muestrale el mensaje para desafiliacion
                        $RespuestaXBI = $this->WSCobrosXBI($NUMBER, 130);
                        if ($RespuestaXBI == "0") { // Descarga el contenido
                            header("Location: http://bip.pe/pe/ne/wap/ft/?nue=$NUMBER"); exit;
                        } else {
                            header("Location: /?ft=no-saldo");
                            exit;
                        }
                    }

                    break;
                case '_IMGWAPNX':
                    header("Location: http://bip.pe/pe/ne/wap/rt-devel/");
                    exit;
                    break;
                default:
                    header("Location: /");
                    exit;
            endswitch;

            header("Location: /suscribete?serv=$serv&saldo=otro");
            exit;
        } else { 
            //....ccccccccccccccccccccccccccc
            header("Location: /validacion?serv=$serv&error=009");
            exit;
        }
    }

    function WSCobrosXBI($NUMBER, $idSrv) {
        $params = array(
            'token' => "M0b1l3.",
            'idSrv' => $idSrv,
            'desSrv' => 'RealTone',
            'iv' => 11,
            'sc' => 70,
            'ssc' => '00000',
            'nu' => $NUMBER,
            'sqn' => 50
        );
        $wsClienteXBI = 'http://174.121.234.90/Moso/WSCobrosXBI/PE_Nextel.asmx?wsdl';
        $SoapClientXBI = new nusoap_client($wsClienteXBI, true);
        $tarifas = array('01530', '00840', '00420');
        for ($i = 0; $i < count($tarifas); $i++) {
            $params['ssc'] = $tarifas[$i];
            $RespuestaXBI = $SoapClientXBI->call("CobroXBI", $params);
            if ($RespuestaXBI['CobroXBIResult'] == "0")
                break;
        }
        return $RespuestaXBI['CobroXBIResult'];
    }

    function file_get_contentsRT() {
        $xml = file_get_contents("http://174.121.234.90/Moso/WSMultimedia/wsTOOLS.asmx/RegistrarDescarga?operadora=3&numUser=" . $NUMBER . "&idContenido=0&catalogo=" . $cod . "&esGratis=True");
        $x = new SimpleXMLElement($xml);
        $ID = $x;
        header("Location: /suscripcion-exitosa?trans=ok&iden=" . $ID);
        exit;
    }

    
    function shootLink2($NUMBER, $CODIGO, $VERIFICATION, $serv = NULL, $item = NULL, $cod = NULL) { 
        $parmorigen = '&o=7';
        $ruta = "http://174.121.234.90/SVARequest/Request.aspx?op=3&nu=" . $NUMBER . "&sc=4556&su=1&k=" . $serv . "&v=" . $VERIFICATION . $parmorigen . "&re=17";
        header("Location: $ruta");
    }

}
