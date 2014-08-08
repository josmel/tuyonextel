<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model
 *
 * @author Laptop
 */
class Core_Model {

    //put your code here
    protected $_cache;
    protected $_config;
    
    function __construct() {
        //$this->_cache = Zend_Registry::get('cache');
        $this->_config = Zend_Registry::get('config');
    }
    
    function clearCache($nameCache) {
        $this->_cache->remove($nameCache);
    }
    
    function arrayAsoccForFirstItem($array, $key='') {
        $arrayResponse = array();
        if ($key == '') {
            foreach ($array as $index => $data) {
                $arrayResponse[$data[key($data)]][] = $data;
            }
        } else {
            foreach ($array as $index => $data) {
                $arrayResponse[$data[$key]][] = $data;
            }
        }
        return $arrayResponse;
    }

    function fetchPairs($array) {
        $arrayResponse = array();
        foreach ($array as $index => $datos) {
            $keys = array_keys($datos);
            $arrayResponse[$datos[$keys[0]]] = $datos[$keys[1]];
        }
        return $arrayResponse;
    }

    function generateSlug($name, $ramdomChars = 0) {
//        $specials = "äáàâãªÁÀÂÃÄÍÌÎÏíìîïéèêëÉÈÊËóòôõöºÓÒÔÕÖúùûüÚÙÛÜçÇñÑÝý";
//        $replaces = "aaaaaaaaaaaiiiiiiiieeeeeeeeooooooooooouuuuuuuuccnnyy";
//        
//        $name = mb_ereg_replace('([óòôõöºÓÒÔÕÖ])', 'o', $name);
//        //$name = mb_ereg_replace('ó', 'o', $name);
//        //$name = strtr($name, $specials, $replaces);
//        $name = mb_strtolower($name, 'utf8');
//        
//        
//        $name = mb_ereg_replace('-', ' ', $name);
//        while (mb_substr_count($name, "  ") > 0) $name = mb_ereg_replace('  ', ' ', $name);
//        
//        $name = mb_ereg_replace(' ', '-', $name);
//        
//        
//        $name = preg_replace('([^A-Za-z0-9-])', '', $name);	     					
        
        $name = utf8_decode($name);
                
        $characters = array(
		"Á" => "A", "Ç" => "c", "É" => "e", "Í" => "i", "Ñ" => "n", "Ó" => "o", "Ú" => "u", 
		"á" => "a", "ç" => "c", "é" => "e", "í" => "i", "ñ" => "n", "ó" => "o", "ú" => "u",
		"à" => "a", "è" => "e", "ì" => "i", "ò" => "o", "ù" => "u"
	);
	
	$name = strtr($name, $characters); 
	$name = strtolower(trim($name));
	$name = preg_replace("/[^a-z0-9-]/", "-", $name);
	$name = preg_replace("/-+/", "-", $name);
	
	if(substr($name, strlen($name) - 1, strlen($name)) === "-") {
		$name = substr($name, 0, strlen($name) - 1);
	}
        
        if ($ramdomChars > 0) {
            $name.='-';
            $characters = array(
                "a","b","c","d","e","f","g","h","i","j","k","l","m",
                "n","o","p","q","r","s","t","u","v","w","x","y","z",
                "1","2","3","4","5","6","7","8","9","0");
            
            for ($i = 0; $i < $ramdomChars; $i++) {
                $x = mt_rand(0, count($characters)-1);
                $name.=$characters[$x];
            }
        }
        return $name;
    }
}

?>
