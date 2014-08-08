<?php

class Extra_Utils
{
    static function encr($cad, $key='front-end')
    {
        $result = '';
        for ($i = 0; $i < strlen($cad); $i++) {
            $char = substr($cad, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result.=$char;
        }
        return base64_encode($result);
    }

    static function decr($cad, $key='front-end')
    {
        $result = '';
        $cad = base64_decode($cad);
        for ($i = 0; $i < strlen($cad); $i++) {
            $char = substr($cad, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result.=$char;
        }
        return $result;
    }

    static function truncar($value)
    {
        return ((int) $value == (float) $value) ? (int) $value : (float) $value;
    }

    /* static function excel($headings=array(),$data=array(),$name='reporte'){
      $path = APPLICATION_PATH."/../library/phpexcel/PHPExcel.php";
      require_once($path);

      $objPHPExcel = new PHPExcel();

      //algunos datos sobre autoría
      $objPHPExcel->getProperties()->setCreator("autor");
      $objPHPExcel->getProperties()->setLastModifiedBy("autor");
      $objPHPExcel->getProperties()->setTitle("titulo del Excel");
      $objPHPExcel->getProperties()->setSubject("Asunto");
      $objPHPExcel->getProperties()->setDescription("Descripcion");

      //Trabajamos con la hoja activa principal
      $objPHPExcel->setActiveSheetIndex(0);

      $col = 0;
      $row = 1;
      $objPHPExcel->getActiveSheet()->fromArray(array($headings),NULL,'A'.$row);
      $row = 2;
      foreach ($data as $dato){
      //foreach($dato as $key=>$value) {
      //    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
      //    $col++;
      //}
      //$col=0;
      $objPHPExcel->getActiveSheet()->fromArray(array(array_values($dato)),NULL,'A'.$row);
      $row++;
      }

      //Titulo del libro y seguridad
      $objPHPExcel->getActiveSheet()->setTitle('Reporte');
      $objPHPExcel->getSecurity()->setLockWindows(true);
      $objPHPExcel->getSecurity()->setLockStructure(true);


      // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
      header('Cache-Control: max-age=0');

      //Creamos el Archivo .xlsx
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
      } */

    static function enviarMail($mail, $name, $subject, $htmlbody)
    {
        $correo = new Zend_Mail('utf-8');
        $correo->addTo($mail, $name)
                ->clearSubject()
                ->setSubject($subject)
                ->setBodyHtml($htmlbody);
        try {
            $correo->send();
            return true;
        } catch (Zend_Exception $e) {
            //throw new Zend_Mail_Exception($e->getMessage());
            $log = Zend_Registry::get('log');
            $log->debug("Enviar-Mail: " . $e->getMessage());
            return false;
        }
    }

    static function generarYDescargarPDFCupon($idpedido, $idcamp)
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;
        $config = Zend_Registry::get('config');
        $cupon = new App_Model_Cupon();
        $datacupones = $cupon->pdfCuponesPorIdPedido($idpedido);
        $idsubcamp = $datacupones[0]['subcampana_id'];
        $cupones = array();
        if (!empty($idsubcamp)) {
            foreach ($datacupones as $datacupon) {
                $datacupon['fch_expiracion_cupon'] = $datacupon['fch_expiracion_cupon_subcamp'];
                $cupones[] = $datacupon;
            }
            $prefijo = $idcamp . '_' . $idsubcamp;
        } else {
            foreach ($datacupones as $datacupon)
                $cupones[] = $datacupon;
            $prefijo = $idcamp;
        }
        $view->cupones = $cupones;
        $view->idcamp = $idcamp;
        $html = $view->render('pedido/vercupones.phtml');

        $path = APPLICATION_PATH . "/../library/dompdf/dompdf_config.inc.php";
        require_once($path);

        $codigo = utf8_decode($html);
        $dompdf = new DOMPDF();
        $dompdf->load_html($codigo);
        $sizeFile = $config->app->sizePdf_MemoryLimit;
        ini_set("memory_limit", $sizeFile);
        $dompdf->render();
        
        $pdf = APPLICATION_PATH . "/../public/html/" . $prefijo . '_' . $idpedido . ".pdf";
        file_put_contents($pdf, $dompdf->output());
        $ftp = new Extra_Ftp(
            $config->app->elementsUrlHost, $config->app->elementsUrlUsername,
            $config->app->elementsUrlPassword
        );
        $ftp->openFtp();
        $ftp->newDirectory(array('pdf'));
        $ftp->upImage($prefijo . '_' . $idpedido . ".pdf", $pdf);
        $ftp->closeFtp();
        unlink($pdf);
        $dompdf->stream($prefijo . '_' . $idpedido . ".pdf");
    }

    static function generarPDFCupon($rpta, $flagcron = NULL)
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;
        $config = Zend_Registry::get('config');
        
        if (in_array($flagcron, array('crones', 'crontr_newpend', 'cron_conftran'))) {
            $pedidoRpta = (in_array($flagcron, array('crontr_newpend', 'cron_conftran')))
                    ? $rpta["datos_pedido"] : $rpta;
            $controller = 'pedido';
            $view->addBasePath(APPLICATION_PATH . '/modules/admin/views');
        } else {
            $pedidoRpta = $rpta["datos_pedido"];
            $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        }
        

        $idpedido = $pedidoRpta['id'];
        $idcamp = $pedidoRpta['campana_id'];

        $cupon = new App_Model_Cupon();
        $cupones = $cupon->pdfCuponesPorIdPedido($idpedido);
        //$view->cupones = $cupon->pdfCuponesPorIdPedido($idpedido);        
        if (in_array($flagcron, array('crones', 'crontr_newpend'))) {
            $view->assign('cupones', $view->cupones);
        }
        /*sasa*/
        
        $path = APPLICATION_PATH . "/../library/MPDF56/mpdf.php";
        require_once($path);

        $mpdf=new mPDF('c', 'A4'); 
        $mpdf->SetDisplayMode('fullpage');
        $sizeFile = $config->app->sizePdf_MemoryLimit;
        ini_set("memory_limit", $sizeFile);
        
        foreach ($cupones as $valor):
            $view->cupones = $valor;
            $view->idcamp = $idcamp;
            $html = $view->render("$controller/cuponespdf.phtml");
            //$codigo = utf8_decode($html);
            $mpdf->WriteHTML($html);
            //$mpdf->AddPage();
            
        endforeach;
        
        if ($pedidoRpta['subcampana_id'] == 0)
            $prefijo = $idcamp;
        else
            $prefijo=$idcamp . '_' . $pedidoRpta['subcampana_id'];        
        $name = $prefijo . '_' . $idpedido . ".pdf";
        $ruta = APPLICATION_PATH . "/../public/html/"; 
        $mpdf->Output($name, '', $ruta);
        
        $pdf = $ruta . $name;        
        
        $ftp = new Extra_Ftp(
            $config->app->elementsUrlHost, $config->app->elementsUrlUsername, $config->app->elementsUrlPassword
        );
        $ftp->openFtp();
        $ftp->newDirectory(array('pdf'));
        $ftp->upImage($prefijo . '_' . $idpedido . ".pdf", $pdf);
        $ftp->closeFtp();
        unlink($pdf);
    }

    static function mailRecuperarClave($mail, $name, $clave, $sitio = App_Model_Site::SITE_DEFAULT)
    {
        $sitio = ($sitio == App_Model_Site::SITE_CLUBTOP) ? 'ClubTop' : 'OferTOP';
        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_RECUPERAR_CLAVE');
        $htmlbody = str_replace("[SITIO]", $sitio, $contenido["contenido"]);
        $htmlbody = str_replace("[NAME]", $name, $htmlbody);
        $htmlbody = str_replace("[CLAVE]", $clave, $htmlbody);
        return Extra_Utils::enviarMail($mail, $name, 'Nueva contraseña para ' . $sitio . '!', $htmlbody);
    }

    static function mailEnviarCupon($rpta, $flagcron = NULL, $sitio = App_Model_Site::SITE_DEFAULT)
    {
        $sitio = ($sitio == App_Model_Site::SITE_CLUBTOP) ? 'ClubTop' : 'OferTOP';
        /*         * ************ Cron enviar cupones pedidos capturados *************** */
        if ($flagcron == 'crones') {
            $pedidoRpta = $rpta;
            $user = array(
                'email' => $pedidoRpta['email'], 'nombre' => $pedidoRpta['nombre'],
                'apellido' => $pedidoRpta['apellido']
            );
        } else {
            $pedidoRpta = $rpta["datos_pedido"];
            $user = $rpta["datos_usuario"];
        }
        /*         * ******************************************************************* */

        $config = Zend_Registry::get('config');
        $idpedido = $pedidoRpta['id'];
        if ($pedidoRpta['subcampana_id'] == 0) {
            $campana = ($flagcron == 'crones')
                ? (array('id' => $pedidoRpta['campana_id'], 'titulo' => $pedidoRpta['titulo']))
                    : ($rpta["datos_campana"]);
            $enlace = $config->app->elementsUrl . '/pdf/' . $campana['id'] . "_$idpedido.pdf";
        } else {
            $campana = ($flagcron == 'crones') 
                ? (array('id' => $pedidoRpta['subcampana_id'], 'titulo' => $pedidoRpta['titulo']))
                    : ($rpta["datos_subcampana"]);
            $enlace = $config->app->elementsUrl . '/pdf/' . $pedidoRpta["campana_id"] . '_'
                    . $campana['id'] . "_$idpedido.pdf";
        }

        $mail = $user['email'];
        $name = $user['nombre'] . ' ' . $user['apellido'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_ENVIAR_CUPON');
        $htmlbody = str_replace("[SITIO]", $sitio, $contenido["contenido"]);
        $htmlbody = str_replace("[NAME]", $name, $htmlbody);
        $htmlbody = str_replace("[TITULO_CAMPANA]", $campana['titulo'], $htmlbody);
        $htmlbody = str_replace("[ENLACE]", $enlace, $htmlbody);
        $htmlbody = str_replace("[IDPEDIDO]", $idpedido, $htmlbody);
        return Extra_Utils::enviarMail($mail, $name, 'Tus compras en ' . $sitio, $htmlbody);
    }

    static function mailEnviarCompra($idpedido, $campana, $user)
    {
        $mail = $user['email'];
        $name = $user['nombre'] . ' ' . $user['apellido'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_ENVIAR_COMPRA');
        $htmlbody = str_replace("[NAME]", $name, $contenido["contenido"]);
        $htmlbody = str_replace("[TITULO_CAMPANA]", $campana['titulo'], $htmlbody);
        $htmlbody = str_replace("[IDPEDIDO]", $idpedido, $htmlbody);
        Extra_Utils::enviarMail($mail, $name, 'Tu pedido en OferTOP', $htmlbody);
    }

    static function mailNuevoCliente($user, $clave, $sitio = App_Model_Site::SITE_DEFAULT)
    {
        $sitio = ($sitio == App_Model_Site::SITE_CLUBTOP) ? 'ClubTop' : 'OferTOP';
        $mail = $user['email'];
        $name = $user['nombre'] . ' ' . $user['apellido'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_NUEVO_CLIENTE');
        $htmlbody = str_replace("[SITIO]", $sitio, $contenido["contenido"]);
        $htmlbody = str_replace("[NAME]", $name, $htmlbody);
        $htmlbody = str_replace("[MAIL]", $mail, $htmlbody);
        $htmlbody = str_replace("[CLAVE]", $clave, $htmlbody);
        Extra_Utils::enviarMail($mail, $name, 'Tu cuenta en ' . $sitio, $htmlbody);
    }

    static function mailMeLaJuego($rpta, $flagcron = NULL)
    {
        /*         * ************ Cron enviar cupones pedidos capturados *************** */
        if ($flagcron == 'crones') {
            $user = array('email' => $rpta['email'], 'nombre' => $rpta['nombre'], 'apellido' => $rpta['apellido']);
            $campana = array('fch_final' => $rpta['fch_final']);
            $subcampana = array('opcion_sorteo' => $rpta['opcion_sorteo']);
            $pedido = array('cant_pedido' => $rpta['cant_pedido']);
        } else {
            $user = $rpta["datos_usuario"];
            $campana = $rpta["datos_campana"];
            $subcampana = $rpta["datos_subcampana"];
            $pedido = $rpta["datos_pedido"];
        }
        /*         * ******************************************************************* */

        $opciones = ($subcampana['opcion_sorteo'] * $pedido["cant_pedido"]);
        $fecha = $campana['fch_final'];
        $date = new Zend_Date($fecha);
        $date->addDay(1);
        $time = $date->getTimestamp();
        $fecha = Extra_Utils::formatearFechaIdiomas($time, 'largo');
        $mail = $user['email'];
        $name = $user['nombre'] . ' ' . $user['apellido'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_MELAJUEGO');
        $htmlbody = str_replace("[OPCIONES]", $opciones, $contenido["contenido"]);
        $htmlbody = str_replace("[NAME]", $name, $htmlbody);
        $htmlbody = str_replace("[FECHA]", $fecha, $htmlbody);
        return Extra_Utils::enviarMail($mail, $name, 'Ya estás participando en ME LA JUEGO', $htmlbody);
    }

    /**
      Function formatearFechaIdiomas
      In: timestamp, variable setting, formato (corto, medio, largo)
     * */
    static function formatearFechaIdiomas($time, $formato = 'corto')
    {
        /*         * *
          Formato de fechas validos, corto, medio y largo, pueden personalizar el suyo de así necesitarlo:
         * * */
        $setting['FORMATO_FECHA_CORTO'] = 'd/m/Y';
        $setting['FORMATO_FECHA_MEDIO'] = '%mes% %de% Y';
        $setting['FORMATO_FECHA_LARGO'] = '%dia% d %de% %mes% %de% Y';
        // se le pueden poner cosas como: \H\o\y \e\s %dia%, o agregar
        // un setting tal cual lo es "de" que se llame "hoy" y otro "es".
        // Además de agregar cualquier comodín válido de www.php.net/date

        /*         * *
          Para cada caso del ejemplo tengo dias, meses y el texto "de"
         * * */
        $setting['DIAS'] = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $setting['MESES'] = array(
            'positionZero', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        );
        $setting['DE'] = 'de';

        $no = array('%dia%', '%mes%', '%de%'); 
        //Esto es lo que escribimos en el setting, para que sea más legible
        //para el administrador del sitio. Se pueden agregar tantas variantes
        //se crean necesarias, tendiendo su posición declarada en el array $setting

        $si = array('%\d\i\a%', '%\m\e\s%', '%\d\e%'); 
        //No se le pude pasar a date cosas como "mes",
        //ya que las tres letras son valores reservados, hay que escaparlos.

        $traduccion = array(
            $setting['DIAS'][date("w", $time)], $setting['MESES'][date("n", $time)],
            $setting['DE']
        );
        //Y esta es la traducción de cada elemento

        #FORMATO CORTO
        if ($formato == 'corto')
            return date($setting['FORMATO_FECHA_CORTO'], $time);
        #FORMATO MEDIO
        if ($formato == 'medio') {
            $setting['FORMATO_FECHA_MEDIO'] = str_replace($no, $si, $setting['FORMATO_FECHA_MEDIO']);
            return str_replace($no, $traduccion, date($setting['FORMATO_FECHA_MEDIO'], $time));
        }
        #FORMATO LARGO
        if ($formato == 'largo') {
            $setting['FORMATO_FECHA_LARGO'] = str_replace($no, $si, $setting['FORMATO_FECHA_LARGO']);
            return str_replace($no, $traduccion, date($setting['FORMATO_FECHA_LARGO'], $time));
        }
        return FALSE;
    }

    static function mailEnviarMensaje($rpta, $flagcron = NULL, $sitio = App_Model_Site::SITE_DEFAULT) 
    {
        $sitio = ($sitio == App_Model_Site::SITE_CLUBTOP) ? 'ClubTop' : 'OferTOP';

        /*         * ************ Cron enviar cupones pedidos capturados *************** */
        if ($flagcron == 'crones') {
            $pedidoRpta = $rpta;
            $user = array('email' => $rpta['email'], 'nombre' => $rpta['nombre'], 'apellido' => $rpta['apellido']);
            $campana = array('titulo' => $rpta['titulo']);
        } else {
            $pedidoRpta = $rpta["datos_pedido"];
            $user = $rpta["datos_usuario"];
            $campana = ($pedidoRpta['subcampana_id'] == 0) ? $rpta["datos_campana"] : $rpta["datos_subcampana"];
        }
        /*         * ******************************************************************* */

        $config = Zend_Registry::get('config');
        $idpedido = $pedidoRpta['id'];
        $mail = $user['email'];
        $name = $user['nombre'] . ' ' . $user['apellido'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_ENVIAR_MENSAJE');
        $htmlbody = str_replace("[SITIO]", $sitio, $contenido["contenido"]);
        $htmlbody = str_replace("[NAME]", $name, $htmlbody);
        $htmlbody = str_replace("[CAMPANA_TITULO]", $campana['titulo'], $htmlbody);
        $htmlbody = str_replace("[IDPEDIDO]", $idpedido, $htmlbody);
        Extra_Utils::enviarMail($mail, $name, 'Tus compras en ' . $sitio, $htmlbody);
    }

    static function mailEnviarCuponSocio($id, $est, $sitio = App_Model_Site::SITE_DEFAULT)
    {
        $sitio = ($sitio == App_Model_Site::SITE_CLUBTOP) ? 'ClubTop' : 'OferTOP';

        $config = Zend_Registry::get('config');
        $enlace = $config->app->elementsUrl . '/pdf/' . $id . "_listsocio.pdf";

        $mail = $est[0]['email'];
        $name = $est[0]['establecimiento'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_ENVIAR_CUPON_SOCIO');
        $htmlbody = str_replace("[SITIO]", $sitio, $contenido["contenido"]);
        $htmlbody = str_replace("[NAME]", $name, $htmlbody);
        $htmlbody = str_replace("[CAMPANA_TITULO]", $est[0]['titulo'], $htmlbody);
        $htmlbody = str_replace("[ENLACE]", $enlace, $htmlbody);
        return Extra_Utils::enviarMail($mail, $name, 'Tus cupones en ' . $sitio, $htmlbody);
    }

    static function mailEstablecimientoRegistrado($data, $dataCod)
    {
        $config = $config = Zend_Registry::get('config');
        $email = $config->app->mailContactoRetailer;

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_ESTABLECIMIENTO_REGISTRADO');
        $htmlbody = str_replace("[EMPRESA]", $data['nombre'], $contenido["contenido"]);
        $htmlbody = str_replace("[TIPO_DOC]", $data['tipo_documento'], $htmlbody);
        $htmlbody = str_replace("[NRO_DOC]", $data['numero_documento'], $htmlbody);
        $htmlbody = str_replace("[CODIGO_SAP]", $dataCod['sap_codigo'], $htmlbody);
        return Extra_Utils::enviarMail($email, 'Admin', 'Establecimiento ya creado SAP', $htmlbody);
    }

    static function mailAvisoAdmin($usersMail, $rpta)
    {
        $datosPedido = $rpta['datos_pedido'];
        $idpedido = $datosPedido['id'];
        $name = "Administradores del Site";
        $fecha = Zend_Date::now()->toString('Y-MM-dd HH:mm:ss');

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_AVISO_ADMIN');
        $htmlbody = str_replace("[IDPEDIDO]", $idpedido, $contenido["contenido"]);
        $htmlbody = str_replace("[FECHA]", $fecha, $htmlbody);
        Extra_Utils::enviarMail($usersMail, $name, 'Comprobación de Pedido: PAGADO a FALLIDO', $htmlbody);
    }

    static function getMenus($home = NULL)
    {
        $session = new Zend_Session_Namespace('Menus');
        $site = App_Model_Site::SITE_DEFAULT;
        $ciudad = App_Model_Ciudad::CIUDAD_DEFAULT_ID;

        $idSite = ($session->site) ? $session->site : $site;
        $idCiudad = ($session->ciudad) ? $session->ciudad : $ciudad;

        /*         * ************* Inicializando site y ciudad del portal ************** */
        $session->site = $idSite;
        $session->ciudad = $idCiudad;
        /*         * ******************************************************************* */

        $categoria = new App_Model_Categoria();
        $data = array(
            'site' => $idSite,
            'ciudad' => $idCiudad,
            'home' => $home,
        );
        $result = $categoria->obtenerPestanasSiteCiudad($data);

        return $result;
    }

    /**
     * @return string
     */
    public static function quitarDiacriticas($string, $permitidos = array())
    {
        //cyrylic transcription
        $cyrylicFrom = array(
            '¿', '?', '®', '™', '’', '”', '“', '*', '·', '|', '@', '`', '^', '[', ']', '{', '}',
            '=', '/', '%', '.', '+', ',', ':', '!', '¡', '®', '&', '(', ')', '"', "'", 'А', 'Б', 'В', 'Г', 'Д',
            'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 
            'Х', 'Ц', 'Ч', 'Ш','Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е',
            'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
            'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
        );
        $cyrylicTo = array(
            '', '', '', '', '', '', '', '', '', '', 'a', '', '',
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
            '', 'A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '',
            'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i',
            'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch',
            'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia'
        );

        $from = array(
            "Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç",
            "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ",
            "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č",
            "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ",
            "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ",
            "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ",
            "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł",
            "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř",
            "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų",
            "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş",
            "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ",
            "ý", "ŷ", "ÿ", "ź", "ż", "ž"
        );
        $to = array(
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C",
            "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E",
            "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a",
            "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e",
            "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H",
            "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L",
            "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE",
            "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k",
            "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o",
            "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U",
            "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z",
            "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y",
            "z", "z", "z"
        );

        $from = array_merge($from, $cyrylicFrom);
        $to = array_merge($to, $cyrylicTo);

        //permnitidos
        foreach ($permitidos as $permitido) {
            if (in_array($permitido, $from)) {
                $copia = array_flip($from);
                $indice = $copia[$permitido];
                unset($from[$indice]);
                unset($to[$indice]);
            }
        }
        $newstring = str_replace($from, $to, $string);
        return $newstring;
    }

    static function mailAvisoAdminNotificacionPasarela($data)
    {
        $idtransaccion = $data['idtr'];
        $estadorpta = $data['estadorpta'];
        $estadoactual = $data['estadoactual'];
        $fecha = Zend_Date::now()->toString('Y-MM-dd HH:mm:ss');

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_AVISO_ADMIN_NOTIFICACION_PASARELA');
        $htmlbody = str_replace("[IDTRANSACCION]", $idtransaccion, $contenido["contenido"]);
        $htmlbody = str_replace("[FECHA]", $fecha, $htmlbody);
        $htmlbody = str_replace("[ESTADOACTUAL]", $estadoactual, $htmlbody);
        $htmlbody = str_replace("[ESTADORPTA]", $estadorpta, $htmlbody);
        Extra_Utils::enviarMail($data['emailadm'], "Administradores del Site", 'Notificación de Pasarela', $htmlbody);
    }

    static function enviarMailContacto($data)
    {
        $config = Zend_Registry::get('config');
        $mailContacto = $config->app->mailContactoRetailer;

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('ENVIAR_MAIL_CONTACTO');
        $htmlbody = str_replace("[RAZON_SOCIAL]", $data["razon_social"], $contenido["contenido"]);
        $htmlbody = str_replace("[RUC]", $data["ruc"], $htmlbody);
        $htmlbody = str_replace("[RUBRO]", $data["rubro"], $htmlbody);
        $htmlbody = str_replace("[DIRECCION]", $data["direccion"], $htmlbody);
        $htmlbody = str_replace("[TELEFONO]", $data["telefono"], $htmlbody);
        $htmlbody = str_replace("[EMAIL]", $data["email"], $htmlbody);
        $htmlbody = str_replace("[CONTACTO]", $data["contacto"], $htmlbody);
        $htmlbody = str_replace("[DESCRIPCION]", $data["descripcion"], $htmlbody);
        Extra_Utils::enviarMail($mailContacto, 'Socio Ofertop', 'Contacto Socios', $htmlbody);
    }

    static function mailReembolso($idpedido, $idusuario, $transferencia, 
            $creditos, $sitio = App_Model_Site::SITE_DEFAULT)
    {
        $sitio = ($sitio == App_Model_Site::SITE_CLUBTOP) ? 'ClubTop' : 'OferTOP';
        $usuarioModelo = new App_Model_Usuario();
        $user = $usuarioModelo->getUsuarioPorId($idusuario);
        $mail = $user['email'];
        $usuario = $user['nombre'] . ' ' . $user['apellido'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_REEMBOLSO');
        $htmlbody = str_replace("[USUARIO]", $usuario, $contenido["contenido"]);
        $htmlbody = str_replace("[IDPEDIDO]", $idpedido, $htmlbody);
        $htmlbody = str_replace("[TRANSFERENCIA]", $transferencia, $htmlbody);
        $htmlbody = str_replace("[CREDITOS]", $creditos, $htmlbody);
        $htmlbody = str_replace("[SITIO]", $sitio, $htmlbody);
        Extra_Utils::enviarMail($mail, $usuario, 'Reembolso', $htmlbody);
    }

    static function mailExpiracionCupon($data, $dias)
    {
        $idusuario = $data['idreceptor'];
        $titulocamp = $data['titulocamp'];
        $config = Zend_Registry::get('config');
        $idpedido = $data['idped'];
        if ($data['idsubcamp'] == 0)
            $enlace = $config->app->elementsUrl . '/pdf/' . $data['idcamp'] . "_$idpedido.pdf";
        else
            $enlace=$config->app->elementsUrl . '/pdf/' . $data["idcamp"] . '_' . $data['idsubcamp'] . "_$idpedido.pdf";
        $usuarioModelo = new App_Model_Usuario();
        $user = $usuarioModelo->getUsuarioPorId($idusuario);

        $sitiouser = $usuarioModelo->getSitioDeUsuarioPorId($user['id']);
        $sitio = $sitiouser['idsite'];
        $sitio = ($sitio == App_Model_Site::SITE_CLUBTOP) ? 'ClubTop' : 'OferTOP';

        $mail = $user['email'];
        $usuario = $user['nombre'] . ' ' . $user['apellido'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_EXPIRACION_CUPON');
        $htmlbody = str_replace("[USUARIO]", $usuario, $contenido["contenido"]);
        $htmlbody = str_replace("[IDPEDIDO]", $idpedido, $htmlbody);
        $htmlbody = str_replace("[TITULOCAMP]", $titulocamp, $htmlbody);
        $htmlbody = str_replace("[DIAS]", $dias, $htmlbody);
        $htmlbody = str_replace("[ENLACE]", $enlace, $htmlbody);
        $htmlbody = str_replace("[SITIO]", $sitio, $htmlbody);
        return Extra_Utils::enviarMail(
            $mail, $usuario, "Solo te quedan $dias días para utilizar tu cupón", $htmlbody
        );
    }

    static function mailAvisoAdminPedidoBloqueado($idpx, $emailadmin)
    {
        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_AVISO_ADMIN_PEDIDO_BLOQUEADO');
        $htmlbody = str_replace("[IDPX]", $idpx, $contenido["contenido"]);
        Extra_Utils::enviarMail(
            $emailadmin, "Administradores del Site", 'Notificación de Pedido bloqueado', $htmlbody
        );
    }

    static function mailNuevaSuscripcion($mail, $sitio = App_Model_Site::SITE_DEFAULT)
    {
        $sitio = ($sitio == App_Model_Site::SITE_CLUBTOP) ? 'ClubTop' : 'OferTOP';

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_NUEVA_SUSCRIPCION');
        $htmlbody = str_replace("[SITIO]", $sitio, $contenido["contenido"]);
        Extra_Utils::enviarMail($mail, '', '¡Bienvenid@ a ' . $sitio . '!', $htmlbody);
    }

    static function mailAvisoAdminNotaDebitoInactivo($idl, $emailadmin)
    {
        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_AVISO_ADMIN_NOTA_DEBITO_INACTIVO');
        $htmlbody = str_replace("[IDL]", $idl, $contenido["contenido"]);
        Extra_Utils::enviarMail(
            $emailadmin, "Administradores del Site", 'Notificación de Nota de Debito inactiva', $htmlbody
        );
    }

    static function mailCodigoSAPNoGeneradoEstablecimiento($ide, $error, $emailadmin)
    {
        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_CODIGO_SAP_NO_GENERADO_ESTABLECIMIENTO');
        $htmlbody = str_replace("[IDE]", $ide, $contenido["contenido"]);
        $htmlbody = str_replace("[ERROR]", $error, $htmlbody);
        Extra_Utils::enviarMail(
            $emailadmin, "Administradores del Site", 'Notificación Establecimientos Sin Código SAP', $htmlbody
        );
    }

    static function mailAdminSAP($ide, $nameEst,
            $emailadmin, $rangofechas, $opc = NULL, $nrefact = NULL)
    {
        $fch1 = substr($rangofechas['fchi'], 0, 10);
        $fch2 = substr($rangofechas['fchf'], 0, 10);

        $mensaje = 'El siguiente establecimiento no esta registrado en el servicio SAP,'
                   .' por ello su liquidación no se procesará';
        $asunto = 'Notificación Liquidación - Establecimientos Sin Código SAP';
        if ($opc == 1) {
            $mensaje = 'El siguiente establecimiento no tiene un monto a facturar (monto=0),'
                    .' por ello su liquidación no se procesará en SAP';
            $asunto = 'Notificación Liquidación - Monto cero a facturar en SAP';
        }
        if ($opc == 2) {
            $mensaje = 'El siguiente establecimiento tiene una factura que empieza con "9",'
                    .' la factura es: '.$nrefact;
            $asunto = 'Notificación Liquidación - Factura con digito inicial 9';
        }

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_ADMIN_SAP');
        $htmlbody = str_replace("[MENSAJE]", $mensaje, $contenido["contenido"]);
        $htmlbody = str_replace("[IDE]", $ide, $htmlbody);
        $htmlbody = str_replace("[NAME_EST]", $nameEst, $htmlbody);
        $htmlbody = str_replace("[FCH1]", $fch1, $htmlbody);
        $htmlbody = str_replace("[FCH2]", $fch2, $htmlbody);
        Extra_Utils::enviarMail($emailadmin, "Administradores del Site", $asunto, $htmlbody);
    }

    static function mailNotasDebitoLiquidacionSAP($idLiqNotaDeb, $emailadmin)
    {
        $sapLiquidacionModelo = new App_Model_SapLiquidacion();
        $dataNotaDeb = $sapLiquidacionModelo->getDataNotasDebitosPorIdLiquidacion($idLiqNotaDeb);

        $htmlbody2 = "<table width='480' cellspacing='0' cellpadding='0' border='1'><tbody>";
        $htmlbody2.="<tr>";
        $htmlbody2.= "<td>ID Liquidación</td>";
        $htmlbody2.= "<td>ID Establecimiento SAP</td>";
        $htmlbody2.= "<td>Campaña</td>";
        $htmlbody2.= "<td>Subcampaña</td>";
        $htmlbody2.= "<td>Total cupones reembolsados</td>";
        $htmlbody2.= "<td>ID Cupón</td>";
        $htmlbody2.= "<td>ID Liquidacion de facturación</td>";
        $htmlbody2.= "<td>Pedido</td>";
        $htmlbody2.= "<td>Nombre Comprador</td>";
        $htmlbody2.= "<td>Fecha reembolso</td>";
        $htmlbody2.= "<td>Motivo reembolso</td>";
        $htmlbody2.= "<td>Precio cupón</td>";
        $htmlbody2.= "<td>Monto a descontar</td>";
        $htmlbody2.="</tr>";

        $idcamp = array();
        $idsubcamp = array();
        //$html_descontar = "";

        foreach ($dataNotaDeb as $row) {
            $htmlbody2.="<tr>";
            $htmlbody2.="<td align='center'>" . $row['idliq'] . "</td>";

            if ((!empty($row['idsubcamp'])
                    && !in_array($row['idsubcamp'], $idsubcamp))
                    || (!in_array($row['idcamp'], $idcamp))) {
                $rowspan = ($row['total_cupon_reembolsados'] > 1)
                    ? 'rowspan="' . $row['total_cupon_reembolsados'] . '"' : '';
       $htmlbody2.="<td $rowspan align='center'>" . (empty($row['idsap_est']) ? "&nbsp;" : $row['idsap_est']) . "</td>";
                $htmlbody2.="<td $rowspan>" . $row['idcamp'] . " " . $row['campana_nombre'] . "</td>";
                $htmlbody2.="<td $rowspan>" . $row['idsubcamp'] . "&nbsp;" . $row['subcampana_nombre'] . "</td>";
                $htmlbody2.="<td $rowspan align='center'>" . $row['total_cupon_reembolsados'] . "</td>";
                //$html_descontar = "<td $rowspan align='center'>" . $row['monto_descontar_cpn'] . "</td>";
            }

            $htmlbody2.="<td align='center'>" . $row['idcupon'] . "</td>";
            $htmlbody2.="<td align='center'>" . (empty($row['nroLiqFact'])?"&nbsp;":$row['nroLiqFact']) . "</td>";
            $htmlbody2.="<td align='center'>" . $row['idped'] . "</td>";
            $htmlbody2.="<td>" . $row['nombre_comprador'] . "</td>";
            $htmlbody2.="<td>" . $row['fch_reembolso_cupon'] . "</td>";
            $htmlbody2.="<td>" . $row['motivo_reembolso'] . "</td>";
            $htmlbody2.="<td align='center'>" . $row['precio_cupon'] . "</td>";
            $htmlbody2.="<td align='center'>" . $row['monto_descontar_cpn'] . "</td>";
            $htmlbody2.="</tr>";

            $idcamp[] = $row['idcamp'];
            $idsubcamp[] = $row['idsubcamp'];
            //$html_descontar = "";
        }

        $htmlbody2.="</tbody></table>";
        
        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_NOTAS_DEBITO_LIQUIDACION_SAP');
        $htmlbody = str_replace("[HTMLBODY2]", $htmlbody2, $contenido["contenido"]);

        Extra_Utils::enviarMail(
            $emailadmin, "Administradores del Site",
            'Notificación Liquidación - Notas de debito para SAP', $htmlbody
        );
    }

    static function generarCuponesEstablecimiento($idcamp)
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;

        $config = Zend_Registry::get('config');
        $view->addBasePath(APPLICATION_PATH . '/modules/default/views');

        $camp = new App_Model_Campana();
        $view->pdf = $camp->cuponCampanaSubcampana($idcamp);
        $view->est = $est = $camp->estCampana($idcamp, 0);
        $view->siteUrl = $config->app->siteUrl;
        //$view->assign('cupones', $view->cupones);
        $html = $view->render('campana/verpdfcampana.phtml');

        $path = APPLICATION_PATH . "/../library/dompdf/dompdf_config.inc.php";
        require_once($path);

        $codigo = utf8_decode($html);
        $dompdf = new DOMPDF();
        $dompdf->load_html($codigo);
        $sizeFile = $config->app->sizePdf_MemoryLimit;
        ini_set("memory_limit", $sizeFile);
        $dompdf->render();
        $pdf = APPLICATION_PATH . "/../public/html/" . $idcamp . "_listsocio.pdf";
        file_put_contents($pdf, $dompdf->output());
        $ftp = new Extra_Ftp(
            $config->app->elementsUrlHost, $config->app->elementsUrlUsername,
            $config->app->elementsUrlPassword
        );
        $ftp->openFtp();
        $ftp->newDirectory(array('pdf'));
        $ftp->upImage($idcamp . "_listsocio.pdf", $pdf);
        $ftp->closeFtp();
        unlink($pdf);
        return Extra_Utils::mailEnviarCuponSocio($idcamp, $est);
    }

    static function mailAdminSinLiquidacionSAP(
        $idliq, $nameEst, $emailadmin, $rangofechas
    )
    {
        $fch1 = $rangofechas['fchi'];
        $fch2 = $rangofechas['fchf'];

        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_ADMIN_SIN_LIQUIDACION_SAP');
        $htmlbody = str_replace("[IDLIQ]", $idliq, $contenido["contenido"]);
        $htmlbody = str_replace("[NAME_EST]", $nameEst, $htmlbody);
        $htmlbody = str_replace("[FCH1]", $fch1, $htmlbody);
        $htmlbody = str_replace("[FCH2]", $fch2, $htmlbody);
        Extra_Utils::enviarMail(
            $emailadmin, "Administradores del Site",
            'Notificación Liquidación - Sin Nro Factura SAP', $htmlbody
        );
    }

    static function excel1($headings=array(), $data=array(), $name='reporte')
    {
        $filename = "$name.xls";
        header('Content-type: application/ms-excel;');
        header('Content-Disposition: attachment; filename=' . $filename);
        $content = "<table><tr>";
        foreach ($headings as $h)
            $content.="<td>" . $h . "</td>";
        $content.='</tr>';
        foreach ($data as $row) {
            $content.='<tr>';
            foreach ($row as $c)
                $content.="<td>" . $c . "</td>";
            $content.='</tr>';
        }
        $content.='</table>';
        //$content= iconv("UTF-8","WINDOWS-1252",html_entity_decode( $content ,ENT_COMPAT,'utf-8'));
        $content = iconv("UTF-8", "WINDOWS-1252", $content);
        echo $content;
    }

    static function xml($headings=array(), $data=array(), $name='reporte')
    {
        $filename = "$name.xml";
        header('Content-type: application/octet-stream;');
        header('Content-Disposition: attachment; filename=' . $filename);

        $content = "<data>";
        foreach ($data as $row) {
            $content.='<row>';
            $i = 0;
            foreach ($row as $c) {
                $head = str_replace(' ', '_', $headings[$i]);
                $content.="<$head>" . htmlspecialchars($c) . "</$head>";
                $i++;
            }
            $content.='</row>';
        }
        $content.='</data>';
        //$content= iconv("UTF-8","WINDOWS-1252",html_entity_decode( $content ,ENT_COMPAT,'utf-8'));
        //$content= iconv("UTF-8","WINDOWS-1252",$content);
        echo $content;
    }

    static function mail_EjecucionUrlerror($data, $emailadmin)
    {
        $tipotoken = (($data['metodo_pago'] == App_Model_Transaccion::METODO_PAGO_PAGOEFECTIVO) ? "CIP" : "Nro Orden");
        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_EJECUCION_URL_ERROR');
        $htmlbody = str_replace("[ID]", $data['id'], $contenido["contenido"]);
        $htmlbody = str_replace("[FCH_MODIFICACION]", $data['fch_modificacion'], $htmlbody);
        $htmlbody = str_replace("[METODO_PAGO]", $data['metodo_pago'], $htmlbody);
        $htmlbody = str_replace("[PEDIDO_ID]", $data['pedido_id'], $htmlbody);
        $htmlbody = str_replace("[TIPOTOKEN]", $tipotoken, $htmlbody);
        $htmlbody = str_replace("[TOKEN]", $data['token'], $htmlbody);
        Extra_Utils::enviarMail($emailadmin, "Administradores del Site", 'Ejecución de Url Error', $htmlbody);
    }
    
    static function mail_ErrorConfirmarPago($data, $emailadmin)
    {
        $modelMailPlantilla = new App_Model_MailPlantilla();
        $contenido = $modelMailPlantilla->extraeTemplate('MAIL_ERROR_CONFIRMARPAGO');
        $htmlbody = str_replace("[ID]", $data['id'], $contenido["contenido"]);
        $htmlbody = str_replace("[METODO_PAGO]", $data['metodo_pago'], $htmlbody);
        $htmlbody = str_replace("[ID_TRANSACCION]", $data['id'], $htmlbody);
        Extra_Utils::enviarMail($emailadmin, "Administradores del Site", 'Error al confirmar Pago', $htmlbody);
    }

    static function excel($headings=array(), $data=array(), $name='reporte')
    {
        $csvEnd = "\r\n";
        $csvSep = ",";
        $filename = "$name.csv";
        header('Content-type: application/octet-stream;');
        header('Content-Disposition: attachment; filename=' . $filename);

        //$content = "sep=$csv_sep".$csv_end;
        $content = "";

        $j = 0;
        foreach ($headings as $h) {
            if ($j == 0)
                $content.=$h;
            else
                $content.=$csvSep . $h;
            $j++;
        }
        $content.=$csvEnd;
        $utils=new Extra_Utils();
        foreach ($data as $row) {
            $i = 0;
            foreach ($row as $c) {
                $c = $utils::procesarTexto($c);
                if ($i == 0)
                    $content.=$c;
                else
                    $content.=$csvSep . $c;
                $i++;
            }
            $content.=$csvEnd;
        }
        //$content= iconv("UTF-8","WINDOWS-1252",html_entity_decode( $content ,ENT_COMPAT,'utf-8'));
        $content = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $content);
        //$content= iconv("UTF-8","WINDOWS-1252",$content);
        echo $content;
    }

    static function procesarTexto($c='')
    {
        $pos = strpos($c, ',');
        if ($pos === false) {
            $d = $c;
        } else {
            $c = str_replace('"', '""', $c);
            $d = '"'.$c.'"';
        }
        return $d;
    }
}