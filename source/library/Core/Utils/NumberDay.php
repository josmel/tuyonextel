<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of Core_numberDay
 *
 * @author marrselo
 */
class Core_Utils_NumberDay
{
    static function convert($number){     
         switch ($number)
         {
             case 1 : $name='uno';
                 break;
             case 2 : $name = 'dos';
                break;
            case 3 : $name='tres';
                break;
            case 4 : $name='cuatro';
                break;
            case 5 : $name='cinco';
                break;
            case 6 : $name='seis';
                break;
            case 7 : $name='siete';
                break;
            case 8 : $name='ocho';
                break;
            case 9 : $name='nueve';
                break;
            case 10 : $name='diezx';
                break;
            case 11 : $name='once';
                 break;
             case 12 : $name = 'doce';
                break;
            case 13 : $name='trece';
                break;
            case 14 : $name='catorce';
                break;
            case 15 : $name='quince';
                break;
            case 16 : $name='dieceise';
                break;
            case 17 : $name='diecisiete';
                break;
            case 18 : $name='dieciocho';
                break;
            case 19 : $name='diecinueve';
                break;
            case 20 : $name='veinte';
                break;
            case 21 : $name='ventinuno';
                 break;
             case 22 : $name = 'ventidos';
                break;
            case 23 : $name='ventitres';
                break;
            case 24 : $name='venticuatro';
                break;
            case 25 : $name='venticinco';
                break;
            case 26 : $name='veintiseis';
                break;
            case 27 : $name='ventisiete';
                break;
            case 28 : $name='ventiocho';
                break;
            case 29 : $name='ventinueve';
                break;
            case 30 : $name='treinta';
                break;
            case 31 : $name='tresuno';
                break;
            default : $name='';
         }
       return $name;
    }

}


?>
