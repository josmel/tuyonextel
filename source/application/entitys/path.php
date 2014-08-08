<?php
class path{
	function getPath($ID){
		switch ($ID){
		case 'include':
			$ph = "includes";
		break;
		case 'xml':
			$ph = "xml";
		break;
		case 'swf':
			$ph = "swf";
		break;
		case 'css':
			$ph = "css";
		break;
		case 'img':
			$ph = "baner";
		break;
		}
		return $ph;
	}
}
?>