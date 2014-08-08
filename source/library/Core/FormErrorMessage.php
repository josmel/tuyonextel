<?php


class Core_FormErrorMessage {
    private static $_errors = array(
        'Businessman_Form_Address' => array(
            'ubigeo_1' => array(
                'isEmpty' => 'Debe seleccionar un departamento.'
            ),
            'ubigeo_2' => array(
                'isEmpty' => 'Debe seleccionar una provincia.'
            ),
            'ubigeo_3' => array(
                'isEmpty' => 'Debe seleccionar un distrito.'
            ),
//            'codtvia' => array(
//                'isEmpty' => 'Debe seleccionar un tipo de vía.'
//            ),
            'desdire' => array(
                'isEmpty' => 'Debe ingresar una dirección.'
            ),
//            'numdire' => array(
//                'isEmpty' => 'Debe ingresar un número de dirección.'
//            ),
            'nomcont' => array(
                'isEmpty' => 'Debe ingresar un nmbre de contacto.'
            )
        ),
        'Businessman_Form_ShipAddress' => array(
            'state' => array(
                'isEmpty' => 'Debe seleccionar un departamento.'
            ),
            'city' => array(
                'isEmpty' => 'Debe seleccionar una provincia.'
            ),
            'district' => array(
                'isEmpty' => 'Debe seleccionar un distrito.'
            ),
            'ubidenv' => array(
                'isEmpty' => 'Debe ingresar una dirección.'
            ),
            'codtvia' => array(
                'isEmpty' => 'Debe seleccionar un tipo de vía.'
            )
        ),
        'Businessman_Form_Joined' => array(
            'email' => array(
                'isEmpty' => 'Debe ingresar un e-mail.',
                'notUnique' => 'El e-mail ya existe.',
                'emailAddressInvalid' => 'El e-mail ingresado no es válido.',
                'emailAddressInvalidFormat' => 'El e-mail ingresado no es válido.'
            ),
            'name' => array(
                'isEmpty' => 'Debe ingresar su nombre.'
            ),
            'lastname' => array(
                'isEmpty' => 'Debe ingresar su apellido.'
            ),
            'ndoc' => array(
                'notInt' => 'El número de documento no es válido.',
                'isEmpty' => 'Debe ingresar un número de documento.'
            ),
            'birthdate' => array(
                'isEmpty' => 'Debe seleccionar una fecha de nacimiento.'
            ),
            'password' => array(
                'isEmpty' => 'Debe ingresar una clave para su cuenta.'
            ),
            'confirm' => array(
                'notMatch' => 'La contraseña no se ha confirmado correctamente.',
                'isEmpty' => 'Debe confirmar la clave ingresada.'
            ),
            'civilstate' => array(
                'isEmpty' => 'Debe seleccionar su estado civil.'
            ),
            'gender' => array(
                'isEmpty' => 'Debe seleccionar su género.'
            ),
            'captcha' => array(
                'badCaptcha' => 'El código de la imagen no coincide con el texto ingresado.'
            )
        ),
        'Businessman_Form_ChangePassword' => array(
            'password' => array(
                'isEmpty' => 'Debe ingresar una clave para su cuenta.'
            ),
            'confirm' => array(
                'notMatch' => 'La contraseña no se ha confirmado correctamente.',
                'isEmpty' => 'Debe confirmar la clave ingresada.'
            )
        ),
        'Businessman_Form_SendPassword' => array(
            'email' => array(
                'isEmpty' => 'Debe ingresar un e-mail.',
                'emailAddressInvalid' => 'El e-mail ingresado no es válido.',
                'emailAddressInvalidFormat' => 'El e-mail ingresado no es válido.'
            )
        )
    );
  
    
    public static function getErrorMsg($formName, $elementName, $errorType) {
        $msg = "";
        if(isset(self::$_errors[$formName]))
            if(isset(self::$_errors[$formName][$elementName]))
                if(isset(self::$_errors[$formName][$elementName][$errorType]))
                    $msg = self::$_errors[$formName][$elementName][$errorType];
                
        return $msg;
    }
    
    public static function getErrors($form) {
        $errors = array();
        
        foreach($form->getElements() as $element) {
            $eErrors = $element->getErrors(); 
            //var_dump($eErrors);
            foreach ($eErrors as $eError){
                $errorMsg = self::getErrorMsg(
                        get_class($form), 
                        $element->getName(), 
                        $eError
                    );
                if(!empty($errorMsg)) $errors[] = $errorMsg;
            } 
        }
        
        return $errors;
    }
}

?>
