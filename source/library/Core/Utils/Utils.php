<?php
/**
 * Description of Utils
 *
 * @author Marrselo
 */
class Core_Utils_Utils {
   /**
    * 
    * @param array $array
    * @param int $keyDos nroColumna del que se extrae el segundo parametro
    * @return array
    */
    static function fetchPairs($array,$nroCol=null){
        $arrayResponse = array();
        $nroCol=!empty($nroCol)?$nroCol:1;
        foreach($array as $index => $datos){
            $keys = array_keys($datos);
            $arrayResponse[$datos[$keys[0]]]=$datos[$keys[$nroCol]];
        }
        return $arrayResponse;
    }
    //put your code here
    
    /**
     * 
     * @param array $array resultado de una consulta    
     * @return array
     */
    static function fetchPairsConcat($array)
    {
        $arrayResponse = array();                
        foreach($array as $index => $datos){
            $keys = array_keys($datos);            
            $arrayResponse[$datos[$keys[0]]]=$datos[$keys[1]].'-'.$datos[$keys[2]];
        }
        return $arrayResponse;
    }
    
    /**
     * Concatena columna 2 y 3
     * @param type $array
     * @return string
     */
    static function fetchPairsConcat23($array)
    {
        $arrayResponse = array();                
        foreach($array as $index => $datos){
            $keys = array_keys($datos);            
            $arrayResponse[$datos[$keys[0]]]=$datos[$keys[2]].'-'.$datos[$keys[3]];
        }
        return $arrayResponse;
    }
    
    /**
     * Obtiene aleatoriamente la cantidad indicada de caracteres alfanuméricos
     * @param type $ramdomChars Cantidad de caracteres
     * @return string
     */
    static function getRamdomChars($ramdomChars = 1, $type = "AN") {        
        $rChars = "";
        $chars = array();
        $characters = array(
            "a","b","c","d","e","f","g","h","i","j","k","l","m",
            "n","o","p","q","r","s","t","u","v","w","x","y","z");
        $numbers = array("1","2","3","4","5","6","7","8","9","0");
        
        switch($type) {
            case 'A': $chars = $characters; break; 
            case 'N': $chars = $numbers; break; 
            case 'AN': $chars = array_merge($characters, $numbers); break; 
        }
        
        for ($i = 0; $i < $ramdomChars; $i++) {
            $x = mt_rand(0, count($chars)-1);
            $rChars.=$chars[$x];
        }
        
        return $rChars;
    }
    
    /**
     * Convierte una fecha del formato año-mes-dia a dia/mes/año
     * @param type $stringDate fecha en formato año-mes-dia
     * @return string
     */
    static function dateFormatSqlToView($stringDate) {
        $date = new Zend_Date(
                $stringDate, 
                Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY
            );
        return $date->get(
                Zend_Date::DAY.'/'.Zend_Date::MONTH.'/'.Zend_Date::YEAR
            );
    }
    
    /**
     * Convierte una fecha del formato dia/mes/año a año-mes-dia
     * @param type $stringDate fecha en formato dia/mes/año
     * @return string
     */
    static function dateFormatViewToSql($stringDate) {
        $date = new Zend_Date(
                $stringDate, 
                Zend_Date::DAY.'/'.Zend_Date::MONTH.'/'.Zend_Date::YEAR
            );
        return $date->get(
                Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY
            );
    }
    
    /**
     * Convierte una fecha del formato dia/mes/año a año-mes-dia
     * @param type $stringDate fecha en formato dia/mes/año
     * @return string
     */
    static function dateFormatToPartialDescription($stringDate, $format = 'VIEW', $typeMonth = 'SHORT', $typeFormat = 1) {
        if($format == 'VIEW')   
            $date = new Zend_Date(
                $stringDate, 
                Zend_Date::DAY.'/'.Zend_Date::MONTH.'/'.Zend_Date::YEAR
            );
        elseif($format == 'SQL')   
            $date = new Zend_Date(
                $stringDate, 
                Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY
            );
        
        $normalDayDesc = array(
            'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'
        );
        
        $shortMonthDesc = array(
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
            '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
            '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
        );
        
        $normalMonthDesc = array( 
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', 
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', 
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciciembre'
        );
        
        $monthDescription = $shortMonthDesc;
        if ($typeMonth == 'NORMAL') $monthDescription = $normalMonthDesc;
        
        switch ($typeFormat) {
            case 1: 
                $dateDesc = $date->get(Zend_Date::DAY).' '
                    .$monthDescription[$date->get(Zend_Date::MONTH)].' '
                    .$date->get(Zend_Date::YEAR);
                break;
            case 2: 
                $dateDesc = $date->get(Zend_Date::DAY).' '
                    .$monthDescription[$date->get(Zend_Date::MONTH)].' '
                    .$date->get(Zend_Date::YEAR);
                $dateDesc = $normalDayDesc[$date->get(Zend_Date::WEEKDAY_DIGIT)].', '.$dateDesc;
                break;
        }
        
        return $dateDesc;
    }
}


