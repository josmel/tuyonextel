<?php
/**
 *
 * @author Marrselo
 */
class Core_View_Helper_UrlNav extends Zend_View_Helper_Abstract {

    /**
     * @param  String
     * @return string
     */
    public function urlNav($base, $initParams, $newParams) {
        $urlNav = $base;
        $allParams = $initParams;
        foreach($newParams as $key => $value) {
            $allParams[$key] = $value;
        }
        
        foreach($allParams as $key => $value) {
            if(!empty($value)) {
                $urlNav .= '/'.$key.'/'.$value;
            }
        }
        
        return $urlNav;
    }
}
