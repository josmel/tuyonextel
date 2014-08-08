<?php
class Core_Utils_BodyStats  {
    
    private $_idealIMC = 24;
    
    private $_imcTable = array(
        array('code' => 'thin', 'name' => 'Peso bajo', 'beginValue' => 0, 'endValue' => 18, 'message' => 'Necesario valorar signos de desnutrición.'),
        array('code' => 'normal', 'name' => 'Normal', 'beginValue' => 18, 'endValue' => 25, 'message' => 'Peso exacto.'),
        array('code' => 'overweight', 'name' => 'Sobrepeso', 'beginValue' => 25, 'endValue' => 26.9, 'message' => 'Debe revisar su alimentación y rutina de ejercicos.'),
        array('code' => 'obesity', 'name' => 'Obesidad', 'beginValue' => 27, 'endValue' => 30, 'message' => 'Riesgo relativo alto para desarrollar enfermedades cardiovasculares.'),
        array('code' => 'severe-obesity', 'name' => 'Obesidad Severa', 'beginValue' => 30, 'endValue' => 40, 'message' => 'Riesgo relativo muy alto para el desarrollo de enfermedades cardiovasculares.'),
        array('code' => 'xtreme-obesity', 'name' => 'Obesidad Extrema o Mórbida', 'beginValue' => 40, 'endValue' => 100000, 'message' => 'Riesgo relativo extremadamente alto para el desarrollo de enfermedades cardiovasculares.')
    );

    private $_bodyFrameMan = array(
        'thin' => array('code' => 'thin', 'name' => 'Delgada', 'beginValue' => 10.4, 'endValue' => 1000), 
        'normal' => array('code' => 'normal','name' => 'Normal', 'beginValue' => 9.6, 'endValue' => 10.4), 
        'gross' => array('code' => 'gross','name' => 'Gruesa', 'beginValue' => 0, 'endValue' => 9.6)
    );
    
    private $_bodyFrameWoman = array(
        'thin' => array('code' => 'thin', 'name' => 'Delgada', 'beginValue' => 11.0, 'endValue' => 1000), 
        'normal' => array('code' => 'normal','name' => 'Normal', 'beginValue' => 10.1, 'endValue' => 11.0), 
        'gross' => array('code' => 'gross','name' => 'Gruesa', 'beginValue' => 0, 'endValue' => 10.1)
    );
           
     private $_idealWeightMan = array(
        158=>array('min-thin' => 58.3, 'max-thin' => 60.8, 'min-normal' => 59.2, 'max-normal' => 63.5, 'min-gross' => 62.2, 'max-gross' => 67.9),
        159=>array('min-thin' => 58.3, 'max-thin' => 61.0, 'min-normal' => 59.6, 'max-normal' => 64.2, 'min-gross' => 62.8, 'max-gross' => 68.3), 
        160=>array('min-thin' => 58.6, 'max-thin' => 61.3, 'min-normal' => 59.9, 'max-normal' => 64.5, 'min-gross' => 63.0, 'max-gross' => 68.8), 
        161=>array('min-thin' => 59.3, 'max-thin' => 62.0, 'min-normal' => 60.6, 'max-normal' => 65.2, 'min-gross' => 63.8, 'max-gross' => 69.9), 
        162=>array('min-thin' => 59.7, 'max-thin' => 62.4, 'min-normal' => 61.0, 'max-normal' => 65.5, 'min-gross' => 64.2, 'max-gross' => 70.5), 
        163=>array('min-thin' => 60.0, 'max-thin' => 62.7, 'min-normal' => 61.3, 'max-normal' => 66.0, 'min-gross' => 64.5, 'max-gross' => 71.1), 
        164=>array('min-thin' => 60.4, 'max-thin' => 63.1, 'min-normal' => 61.7, 'max-normal' => 66.5, 'min-gross' => 64.9, 'max-gross' => 71.8), 
        165=>array('min-thin' => 60.8, 'max-thin' => 63.5, 'min-normal' => 62.1, 'max-normal' => 67.0, 'min-gross' => 65.3, 'max-gross' => 72.5), 
        166=>array('min-thin' => 61.1, 'max-thin' => 63.8, 'min-normal' => 62.4, 'max-normal' => 67.6, 'min-gross' => 65.6, 'max-gross' => 73.2), 
        167=>array('min-thin' => 61.5, 'max-thin' => 64.2, 'min-normal' => 62.8, 'max-normal' => 68.2, 'min-gross' => 66.0, 'max-gross' => 74.0), 
        168=>array('min-thin' => 61.8, 'max-thin' => 64.6, 'min-normal' => 63.2, 'max-normal' => 68.7, 'min-gross' => 66.4, 'max-gross' => 74.7), 
        169=>array('min-thin' => 62.2, 'max-thin' => 65.2, 'min-normal' => 63.8, 'max-normal' => 69.3, 'min-gross' => 67.0, 'max-gross' => 75.4), 
        170=>array('min-thin' => 62.5, 'max-thin' => 65.7, 'min-normal' => 64.3, 'max-normal' => 69.8, 'min-gross' => 67.5, 'max-gross' => 76.1), 
        171=>array('min-thin' => 62.9, 'max-thin' => 66.3, 'min-normal' => 64.8, 'max-normal' => 70.3, 'min-gross' => 68.0, 'max-gross' => 76.8), 
        172=>array('min-thin' => 63.2, 'max-thin' => 66.7, 'min-normal' => 64.8, 'max-normal' => 70.3, 'min-gross' => 68.0, 'max-gross' => 76.8), 
        173=>array('min-thin' => 63.2, 'max-thin' => 66.7, 'min-normal' => 65.4, 'max-normal' => 70.8, 'min-gross' => 68.5, 'max-gross' => 77.5), 
        174=>array('min-thin' => 63.9, 'max-thin' => 67.8, 'min-normal' => 66.4, 'max-normal' => 71.9, 'min-gross' => 69.6, 'max-gross' => 78.9),
        175=>array('min-thin' => 64.3, 'max-thin' => 68.3, 'min-normal' => 66.9, 'max-normal' => 72.4, 'min-gross' => 70.1, 'max-gross' => 79.6), 
        176=>array('min-thin' => 64.7, 'max-thin' => 68.9, 'min-normal' => 67.5, 'max-normal' => 73.0, 'min-gross' => 70.7, 'max-gross' => 80.3), 
        177=>array('min-thin' => 65.0, 'max-thin' => 69.5, 'min-normal' => 68.1, 'max-normal' => 73.5, 'min-gross' => 71.3, 'max-gross' => 81.0), 
        178=>array('min-thin' => 65.4, 'max-thin' => 70.0, 'min-normal' => 68.6, 'max-normal' => 74.0, 'min-gross' => 71.8, 'max-gross' => 81.8), 
        179=>array('min-thin' => 65.7, 'max-thin' => 70.5, 'min-normal' => 69.2, 'max-normal' => 74.6, 'min-gross' => 72.3, 'max-gross' => 82.5), 
        180=>array('min-thin' => 66.1, 'max-thin' => 71.0, 'min-normal' => 69.7, 'max-normal' => 75.1, 'min-gross' => 72.8, 'max-gross' => 83.3), 
        181=>array('min-thin' => 66.6, 'max-thin' => 71.6, 'min-normal' => 70.2, 'max-normal' => 75.8, 'min-gross' => 73.4, 'max-gross' => 84.0), 
        182=>array('min-thin' => 67.1, 'max-thin' => 72.1, 'min-normal' => 70.7, 'max-normal' => 76.5, 'min-gross' => 73.9, 'max-gross' => 84.7),
        183=>array('min-thin' => 67.7, 'max-thin' => 72.7, 'min-normal' => 71.3, 'max-normal' => 77.2, 'min-gross' => 74.5, 'max-gross' => 85.4),
        184=>array('min-thin' => 68.2, 'max-thin' => 73.4, 'min-normal' => 71.8, 'max-normal' => 77.9, 'min-gross' => 75.2, 'max-gross' => 86.1),
        185=>array('min-thin' => 68.7, 'max-thin' => 74.1, 'min-normal' => 72.4, 'max-normal' => 78.6, 'min-gross' => 75.9, 'max-gross' => 87.6),
        186=>array('min-thin' => 69.2, 'max-thin' => 74.8, 'min-normal' => 73.0, 'max-normal' => 79.3, 'min-gross' => 76.6, 'max-gross' => 87.6),
        187=>array('min-thin' => 69.8, 'max-thin' => 75.5, 'min-normal' => 73.7, 'max-normal' => 80.0, 'min-gross' => 77.3, 'max-gross' => 88.5),
        188=>array('min-thin' => 70.3, 'max-thin' => 76.2, 'min-normal' => 74.4, 'max-normal' => 80.7, 'min-gross' => 78.0, 'max-gross' => 89.4),
        189=>array('min-thin' => 70.9, 'max-thin' => 76.9, 'min-normal' => 74.4, 'max-normal' => 80.7, 'min-gross' => 78.0, 'max-gross' => 89.4),
        190=>array('min-thin' => 71.4, 'max-thin' => 77.6, 'min-normal' => 75.4, 'max-normal' => 82.2, 'min-gross' => 79.4, 'max-gross' => 91.2),
        191=>array('min-thin' => 72.1, 'max-thin' => 78.4, 'min-normal' => 76.1, 'max-normal' => 83.0, 'min-gross' => 80.3, 'max-gross' => 92.1),
        192=>array('min-thin' => 72.8, 'max-thin' => 79.1, 'min-normal' => 76.8, 'max-normal' => 83.9, 'min-gross' => 81.2, 'max-gross' => 93.0),
        193=>array('min-thin' => 73.5, 'max-thin' => 79.8, 'min-normal' => 77.6, 'max-normal' => 84.8, 'min-gross' => 82.1, 'max-gross' => 93.9),        
    );
    
    private $_idealWeightWoman = array(
        148=>array('min-thin' => 46.4, 'max-thin' => 50.6, 'min-normal' => 49.6, 'max-normal' => 55.1, 'min-gross' => 53.7, 'max-gross' => 59.8),
        149=>array('min-thin' => 46.6, 'max-thin' => 51.0, 'min-normal' => 50.0, 'max-normal' => 55.5, 'min-gross' => 54.1, 'max-gross' => 60.3), 
        150=>array('min-thin' => 46.7, 'max-thin' => 51.3, 'min-normal' => 50.3, 'max-normal' => 55.9, 'min-gross' => 54.4, 'max-gross' => 60.9), 
        151=>array('min-thin' => 46.9, 'max-thin' => 51.7, 'min-normal' => 50.7, 'max-normal' => 56.4, 'min-gross' => 54.8, 'max-gross' => 61.9), 
        152=>array('min-thin' => 47.1, 'max-thin' => 52.1, 'min-normal' => 51.1, 'max-normal' => 57.0, 'min-gross' => 55.2, 'max-gross' => 61.9), 
        153=>array('min-thin' => 47.4, 'max-thin' => 52.5, 'min-normal' => 51.5, 'max-normal' => 57.5, 'min-gross' => 55.6, 'max-gross' => 62.4), 
        154=>array('min-thin' => 47.8, 'max-thin' => 53.0, 'min-normal' => 51.9, 'max-normal' => 58.0, 'min-gross' => 56.2, 'max-gross' => 63.0), 
        155=>array('min-thin' => 48.1, 'max-thin' => 53.6, 'min-normal' => 52.2, 'max-normal' => 58.6, 'min-gross' => 56.8, 'max-gross' => 63.6), 
        156=>array('min-thin' => 48.5, 'max-thin' => 54.1, 'min-normal' => 52.7, 'max-normal' => 59.1, 'min-gross' => 57.3, 'max-gross' => 64.1), 
        157=>array('min-thin' => 48.8, 'max-thin' => 54.6, 'min-normal' => 53.2, 'max-normal' => 59.6, 'min-gross' => 57.8, 'max-gross' => 64.4), 
        158=>array('min-thin' => 49.3, 'max-thin' => 55.2, 'min-normal' => 53.8, 'max-normal' => 60.2, 'min-gross' => 58.4, 'max-gross' => 65.3), 
        159=>array('min-thin' => 49.8, 'max-thin' => 55.7, 'min-normal' => 54.3, 'max-normal' => 60.7, 'min-gross' => 58.9, 'max-gross' => 66.0), 
        160=>array('min-thin' => 50.3, 'max-thin' => 56.2, 'min-normal' => 54.9, 'max-normal' => 61.2, 'min-gross' => 59.4, 'max-gross' => 66.7), 
        161=>array('min-thin' => 50.8, 'max-thin' => 56.7, 'min-normal' => 55.4, 'max-normal' => 61.7, 'min-gross' => 59.9, 'max-gross' => 67.4), 
        162=>array('min-thin' => 51.4, 'max-thin' => 57.3, 'min-normal' => 55.9, 'max-normal' => 62.3, 'min-gross' => 60.5, 'max-gross' => 68.1), 
        163=>array('min-thin' => 51.9, 'max-thin' => 57.8, 'min-normal' => 56.4, 'max-normal' => 62.8, 'min-gross' => 61.0, 'max-gross' => 68.8), 
        164=>array('min-thin' => 52.5, 'max-thin' => 58.4, 'min-normal' => 57.0, 'max-normal' => 63.4, 'min-gross' => 61.5, 'max-gross' => 69.5),
        165=>array('min-thin' => 53.0, 'max-thin' => 58.9, 'min-normal' => 57.5, 'max-normal' => 63.9, 'min-gross' => 62.0, 'max-gross' => 70.2), 
        166=>array('min-thin' => 53.6, 'max-thin' => 59.5, 'min-normal' => 58.1, 'max-normal' => 64.5, 'min-gross' => 62.6, 'max-gross' => 70.9), 
        167=>array('min-thin' => 54.1, 'max-thin' => 60.0, 'min-normal' => 58.7, 'max-normal' => 65.0, 'min-gross' => 63.2, 'max-gross' => 71.7), 
        168=>array('min-thin' => 54.6, 'max-thin' => 60.5, 'min-normal' => 59.2, 'max-normal' => 65.5, 'min-gross' => 63.7, 'max-gross' => 72.4), 
        169=>array('min-thin' => 55.2, 'max-thin' => 61.1, 'min-normal' => 59.7, 'max-normal' => 66.1, 'min-gross' => 64.3, 'max-gross' => 73.1), 
        170=>array('min-thin' => 55.7, 'max-thin' => 61.6, 'min-normal' => 60.2, 'max-normal' => 66.6, 'min-gross' => 64.8, 'max-gross' => 73.8), 
        171=>array('min-thin' => 56.2, 'max-thin' => 62.1, 'min-normal' => 60.7, 'max-normal' => 67.1, 'min-gross' => 65.3, 'max-gross' => 74.5), 
        172=>array('min-thin' => 56.8, 'max-thin' => 62.6, 'min-normal' => 61.3, 'max-normal' => 67.6, 'min-gross' => 65.8, 'max-gross' => 75.2),
        173=>array('min-thin' => 57.3, 'max-thin' => 63.2, 'min-normal' => 61.8, 'max-normal' => 68.2, 'min-gross' => 66.4, 'max-gross' => 75.9),
        174=>array('min-thin' => 57.8, 'max-thin' => 63.7, 'min-normal' => 62.3, 'max-normal' => 68.7, 'min-gross' => 66.9, 'max-gross' => 76.4),
        175=>array('min-thin' => 58.3, 'max-thin' => 64.2, 'min-normal' => 62.8, 'max-normal' => 69.2, 'min-gross' => 67,4, 'max-gross' => 76.9),
        176=>array('min-thin' => 58.9, 'max-thin' => 64.8, 'min-normal' => 63.4, 'max-normal' => 69.8, 'min-gross' => 68.0, 'max-gross' => 77.5),
        177=>array('min-thin' => 59.5, 'max-thin' => 65.4, 'min-normal' => 64.0, 'max-normal' => 70.4, 'min-gross' => 68.5, 'max-gross' => 78.1),
        178=>array('min-thin' => 60.0, 'max-thin' => 65.9, 'min-normal' => 64.5, 'max-normal' => 70.9, 'min-gross' => 69.0, 'max-gross' => 78.6),
        179=>array('min-thin' => 60.5, 'max-thin' => 66.4, 'min-normal' => 65.1, 'max-normal' => 71.4, 'min-gross' => 69.6, 'max-gross' => 79.1),
        180=>array('min-thin' => 61.0, 'max-thin' => 66.9, 'min-normal' => 65.6, 'max-normal' => 71.9, 'min-gross' => 70.1, 'max-gross' => 79.2),
        181=>array('min-thin' => 61.6, 'max-thin' => 67.5, 'min-normal' => 66.1, 'max-normal' => 72.5, 'min-gross' => 70.7, 'max-gross' => 80.2),
        182=>array('min-thin' => 62.1, 'max-thin' => 68.0, 'min-normal' => 66.6, 'max-normal' => 73.0, 'min-gross' => 71.2, 'max-gross' => 80.7),
        183=>array('min-thin' => 62.6, 'max-thin' => 68.5, 'min-normal' => 67.1, 'max-normal' => 73.5, 'min-gross' => 71.7, 'max-gross' => 81.2)        
    );
     /**
     * 
     * @param int $weight peso
     * @param double $height Altura
     * @param string $gender Genero
     * @param string $muscleMass Tipo masa muscular
     * @return array()
     */
    public function corporalData($weight, $height, $gender,$muscleMass) 
    {
        $bodyFrame = $this->_bodyFrameMan;
        $idealWeight = $this->_idealWeightMan;
        if($gender == 'F') {
            $bodyFrame = $this->_bodyFrameWoman;
            $idealWeight = $this->_idealWeightWoman;
        }    
        
        if(isset($idealWeight[$height])){                                       
            $dataIW = $idealWeight[$height];              
        }else{                      
            $dataIW = $this->setIdealWeightOutRange($gender,$weight);
        }
        $dataBodyFrame = $bodyFrame[$muscleMass]; 
        $dataIMC=$this->calcIMC($weight,$height); 
        if($weight > $dataIW['max-'.$muscleMass]) {                
            $minChangeWeight = $weight - $dataIW['max-'.$muscleMass];
            $maxChangeWeight = $weight - $dataIW['min-'.$muscleMass];
        } elseif ($weight < $dataIW['min-'.$muscleMass]) {
            $state = 'gain-weight';
            $minChangeWeight = $dataIW['min-'.$muscleMass] - $weight;
            $maxChangeWeight = $dataIW['max-'.$muscleMass] - $weight;
        } else {
            $state = 'normal';
            $minChangeWeight = 0;
            $maxChangeWeight = 0;
        }

        $returnData = array(
            'state' => $dataIMC['state'],
            'imc' => $dataIMC['imc'],
            'bfName' => $dataBodyFrame['name'],
            'minWeight' => $dataIW['min-'.$muscleMass],
            'maxWeight' => $dataIW['max-'.$this->setMaxWeight($muscleMass,$gender)],
            'minChangeWeight' => $minChangeWeight,
            'maxChangeWeight' => $maxChangeWeight
        );
        return $returnData;
    }
   
    /**
     * 
     * @param double $weight Peso
     * @param double $height Altura
     * @return array
     */
    public function calcIMC($weight,$height)
    {
        $height=$height/100;
        $dataIMC=array();
        $imc = $weight/($height*$height);
        $dataIMC['imc']=$imc;
        if($imc<18.5){            
            $dataIMC['state']='Bajo peso';
        }elseif($imc>=18.5 && $imc<=24.9){
            $dataIMC['state']='Peso normal';
        }elseif($imc>24.9 && $imc<30){
            $dataIMC['state']='Sobrepeso';            
        }elseif($imc>=30 && $imc<35){
            $dataIMC['state']='Obesidad grado1';
        }elseif($imc>=35 && $imc<40){
            $dataIMC['state']='Obesidad grado2';
        }elseif($imc>=40){
            $dataIMC['state']='Obesidad grado3';
        }
        return $dataIMC;
    }
    
    /**
     * 
     * @param int $igc Indice(porcentaje) de grasa corporal     
     * @param string $gender
     * @return array
     */
    public function calcIGC($igc,$gender)
    {
        $dataIGC='Fuera de rango';
        if($gender=='F'){
            if($igc>=5 /*10*/ && $igc<14){            
                $dataIGC='Tu nivel de Grasa es atlético';
            }elseif($igc>=14 && $igc<21){
                $dataIGC='Tú nivel de Grasa es el ideal o Fitness';
            }elseif($igc>=21 && $igc<25){
                $dataIGC='Tú nivel de Grasa es el Óptimo - Saludable';            
            }elseif($igc>=25 && $igc<32){
                $dataIGC='Tu nivel de Grasa es Regular';
            }elseif($igc>=32){
                $dataIGC='Tu nivel de Grasa es alto';
            }
        }else{
            if($igc>=2 && $igc<6){            
                $dataIGC='Tu nivel de Grasa es atlético';
            }elseif($igc>=6 && $igc<14){
                $dataIGC='Tú nivel de Grasa es el ideal o Fitness';
            }elseif($igc>=14 && $igc<18){
                $dataIGC='Tú nivel de Grasa es el Óptimo - Saludable';            
            }elseif($igc>=18 && $igc<25){
                $dataIGC='Tu nivel de Grasa es Regular';
            }elseif($igc>=25){
                $dataIGC='Tu nivel de Grasa es alto';
            }
        }
        return $dataIGC;
    }
    public function imcData($weight, $height, $gender) {
        $height=$height/100;
        $imc = round($weight/($height*$height),1);
        $idealWeight = $height * $height * $this->_idealIMC;        
        $imcData = array();
        foreach($this->_imcTable as $item) {
            if ($imc >= $item['beginValue']  && $imc <= $item['endValue']) 
                $imcData = $item;
        }
 
        $returnData = array(
            'code' => $imcData['code'],
            'name' => $imcData['name'],
            'imc' => $imc,
            'message' => $imcData['message'],
            'idealWeight' => $idealWeight
        );
        
        return $returnData;
    }
    /**
     * aumenta a un rango superior segun el sexo
     * 
     * @param type $muscleMass
     * @param type $gender
     * @return string
     */
    public function setMaxWeight($muscleMass,$gender)
    {
        switch ($muscleMass){
            case 'thin' : $newMusclMass=($gender=='F')?'thin':'normal';
                break;
            case 'normal' : $newMusclMass=($gender=='F')?'normal':'gross';
                break;
            default:
                $newMusclMass='gross';
        }
        return $newMusclMass;
    }
    /**
     * Setea el peso ideal de las tallas que estan fuera de rango
     * 
     * @param string $gender genero
     * @param int $talla Talla
     */
    public function setIdealWeightOutRange($gender,$talla)
    {        
        $idealWeight=$this->_idealWeightMan[158];        
        if($talla>193){
            $idealWeight=$this->_idealWeightMan[193];
        }
        if($gender=='F'){
            $idealWeight=$this->_idealWeightWoman[148];
            if($talla>1.83){
                $idealWeight=$this->_idealWeightWoman[183];
            }
        }   
        return $idealWeight;
    }
    /**
     * Calcula el porcentaje de grasa corporal 
     * 
     * @param array $data array de cuello,cintura, cadera,genero  y altura     
     */
    public function calcPGC($dataBody){
        $cintura=$dataBody['cintura'];
        $cadera=$dataBody['cadera'];
        $cuello=$dataBody['cuello'];
        $talla=$dataBody['talla'];
        $gender=$dataBody['sexempr'];
        if(empty($cintura) || empty($cadera) || empty($cuello) || empty($talla) ){
           $PGC = false; 
        }else{
            if($gender=='M'){
                $PGC=495/( 1.0324 - 0.19077 * (log10($cintura - $cuello)) +
                       0.15456*(log10($talla)) ) - 450;
            }else{
                $PGC=495/( 1.29579- (0.35004 * (log10($cintura +$cadera - $cuello ))) +
                       0.22100*(log10($talla))) - 450;
            }
        }   
        return round($PGC);
    }
    
    
    public static function contextura($idTipMusc)
    {
        switch ($idTipMusc){
            case 1 : $contex = 'gross';
                break;
            case 2 : $contex = 'normal';
                break;
            default : 
                $contex = 'thin';
        }
        return $contex;
    }
    
    public static function reversecontextura($slug)
    {
        switch ($slug){
            case 'gross' : $contex = 1;
                break;
            case 'normal' : $contex = 2;
                break;
            default : 
                $contex = 3;
        }
        return $contex;
    }
    
    public static function definedAlimentationXHeight($height)
    {
           if(150 <= $height && $height <= 165)
                $kcal = 1000;
            elseif(166 <= $height && $height <= 170)
                $kcal = 1200;
            elseif(171 <= $height && $height <= 175)
                $kcal = 1300;
            elseif(176 <= $height && $height <= 180)
                $kcal = 1400;
            elseif(181 <= $height&& $height <= 185)
                $kcal = 1500;
            else
                $kcal = 0;
            
            return $kcal;
    }
    
    public static function colectionPathPdfAndPicture($path)
    {
        return array(
                '1000' => array($path.'1000/1.jpg',$path.'1000/2.jpg',$path.'1000/3.jpg',$path.'1000/4.jpg',$path.'1000/5.jpg'),
                '1200' => array($path.'1200/1.jpg',$path.'1200/2.jpg',$path.'1200/3.jpg',$path.'1200/4.jpg',$path.'1200/5.jpg'),
                '1300' => array($path.'1300/1.jpg',$path.'1300/2.jpg',$path.'1300/3.jpg',$path.'1300/4.jpg',$path.'1300/5.jpg'),
                '1400' => array($path.'1400/1.jpg',$path.'1400/2.jpg',$path.'1400/3.jpg',$path.'1400/4.jpg',$path.'1400/5.jpg'),
                '1500' => array($path.'1500/1.jpg',$path.'1500/2.jpg',$path.'1500/3.jpg',$path.'1500/4.jpg',$path.'1500/5.jpg'),
                '0' => array(),
            );
    }
}
