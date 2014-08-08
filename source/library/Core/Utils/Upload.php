<?php
/**
 * Description of Utils
 *
 * @author Marrselo
 */
class Core_Utils_Upload {
   
    /**
     *
     * @var type object 
     * Objeto de zend 
     */
    protected $_objHttpTransfer ;
    /**
     *
     * @var type string
     * 
     */
    protected $_type;
    
    protected $_nameInputImagen='';
    
    protected $_nameImagen='';
    
    protected $_objForm;
    /**
     * 
     * @param type $objHttpTransfer
     * @param type $nameInputFile
     * @param path $destination
     */
    public function __construct($objHttpTransfer,$nameInputFile,$destination) {
        $this->_objHttpTransfer=$objHttpTransfer;
        $this->_nameInputImagen=$nameInputFile;
        $this->_objForm=$form;
        $this->_objHttpTransfer->getFileName('imageIcon',false);
        $nameImg=$this->_objHttpTransfer->getFileName($this->_nameInputImagen,false);        
        if(!empty($nameImg)){
            $this->_nameImagen=$this->slugNameImage($nameImg);
            $this->moveFile();
        }
        
    }
    
    public function __toString() {
        return $this->_nameImagen;
    }
    
    
    public function slugNameImage($nameImage)
    {
          $nameWithoutExtension=$this->subtractNameWithoutExtension($nameImage);
          $ext=$this->subtractExtension($nameImage);
          $seo=new Core_Utils_SeoUrl();
          $slug=$seo->filter($nameWithoutExtension,'-',0);
          return $newNameImagen=$slug.'.'.$ext;
    }
     
    public function moveFile(){
   
        
        $form->imageIcon->addFilter('Rename',
            array('target'=>$form->_nameInputImagen->getDestination().'/'.$this->_nameImagen,
                'overwrite'=>true));
        $form->$this->_nameInputImagen->receive();       
        if($form->imagen->receive()){ 
            return  $this->_nameImagen;
        }else{
            Zend_Debug::dump($form->getMessages());
                exit();
//            if(APPLICATION_ENV=='local'){
//                Zend_Debug::dump($form->getMessages());
//                exit();
//            }else{
//                return '';
//            }
        }
    }
    
    public function subtractNameWithoutExtension($nameFile)
    {
        if(!empty($nameFile)){
            $num=  strrpos($nameFile,'.');
            return substr($nameFile,0,-($nameFile - $num));
        }else{
            return '';
        }
    }
    public function subtractExtension($nameFile)
    {
        if(!empty($nameFile)){
            $num = strrpos($nameFile,'.');
            return substr($nameFile, $num + 1);
        }else{
            return '';
        }
    }
    
   
}


