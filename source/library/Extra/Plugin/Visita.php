<?php

class Extra_Plugin_Visita
        extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $session = new Zend_Session_Namespace('Menus');
        $fecha=Zend_Date::now()->toString('YYYY-MM-dd');
        $config = Zend_Registry::get('config');
        if (($session->site==1&&!isset($_COOKIE['visitaofertop']))||
                ($session->site==2&&!isset($_COOKIE['visitaclubtop']))) {
            $modeloVisita=new App_Model_Visita();
            $vfs=$modeloVisita->getVisitaPorFechaYPorSitio($session->site, $fecha);
            if($vfs)
                $datos=array(
                    'id'=>$vfs['id'],
                    'cantidad'=>$vfs['cantidad']+1
                );
            else
                $datos=array(
                    'sitio_id'=>$session->site,
                    'fecha'=>$fecha,
                    'cantidad'=>1
                );
            $modeloVisita->actualizarDatos($datos);
            if($session->site==1)
                setcookie('visitaofertop', 1, time()+$config->app->timeCookieVisita);
            if($session->site==2)
                setcookie('visitaclubtop', 1, time()+$config->app->timeCookieVisita);
        }
    }

}
