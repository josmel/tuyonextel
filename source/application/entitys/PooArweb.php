<?php
class App_Entity_PooArweb {

	function get_fileNowXml($CATEGORIA){
		$dom=new DOMDocument();
                 $config = Zend_Registry::get('config');
	         $path = $config->app->xmlcfg;
                
		$dom->load($path);
		$xml=simplexml_import_dom($dom);
		$file=$xml->categorias->$CATEGORIA;
		return $file;
	}
	
		function getDestacado1(){
		 
		$file=$this->get_fileNowXml("portal");				 
		$XMLString = file_get_contents('xml/' . $file);
		 
		$portal = new SimpleXMLElement($XMLString);
		$i=0;
        $data=null;
		foreach ($portal->HEAD->DESTACADO as $DESTACADO) {
		   //echo $personaje->nombre, ' interpretado por ', $personaje->actor, PHP_EOL;
		   
		    $data[$i]["NOMBRE"] = $DESTACADO->NOMBRE;
			$data[$i]["TITULOENPORTAL"] =$DESTACADO->TITULOENPORTAL;
			$data[$i]["TEXTOENPORTAL"] = utf8_decode ($DESTACADO->TEXTOENPORTAL);
			$data[$i]["ALBUM"] = $DESTACADO->ALBUM;
			$data[$i]["KEYWORD"] = $DESTACADO->KEYWORD;
			$data[$i]["FILASXPAGINA"] = $DESTACADO->FILASXPAGINA;
			$data[$i]["NUMPAGINA"] = $DESTACADO->NUMPAGINA;
			$data[$i]["RUTAIMAGEN"] = $DESTACADO->RUTAIMAGEN;
			$i++;
		}
		return $data;
	}
	
	
		function getTituloPortal(){
		$doc = new DOMDocument();
		$file=$this->get_fileNowXml("portal");	
		//echo $file;	
		$doc->load( 'xml/' . $file );		
		$PORTAL = $doc->getElementsByTagName("PORTALWAP");
		$TITULO = utf8_decode($PORTAL->item(0)->attributes->getNamedItem('TITULO')->nodeValue);
		return $TITULO;
	}
	
	
	function getDestacado(){ ;
		$doc = new DOMDocument("1.0", "iso-8859-1");
		//$file=$this->get_fileNowXml("portal");
                   $config = Zend_Registry::get('config');
	         $path = $config->app->xmlPortalft2;
		//$doc->load(  'xml/' . $file );	
                $doc->load($path);	
		//echo 	$doc;
		$HEADs = $doc->getElementsByTagName("HEAD");
		$i=0;
        $data=null;
		foreach( $HEADs as $HEAD ){
			//echo "1";	
			$DESTACADOs = $HEAD->getElementsByTagName("DESTACADO");			
			foreach( $DESTACADOs as $DESTACADO ){
				//echo "2";
				$NOMBRE = $DESTACADO->getElementsByTagName("NOMBRE");
				$TITULOENPORTAL = $DESTACADO->getElementsByTagName("TITULOENPORTAL");
				$TEXTOENPORTAL = $DESTACADO->getElementsByTagName("TEXTOENPORTAL");
				$ALBUM = $DESTACADO->getElementsByTagName("ALBUM");
				$KEYWORD = $DESTACADO->getElementsByTagName("KEYWORD");
				$FILASXPAGINA = $DESTACADO->getElementsByTagName("FILASXPAGINA");
				$NUMPAGINA = $DESTACADO->getElementsByTagName("NUMPAGINA");
				$RUTAIMAGEN = $DESTACADO->getElementsByTagName("RUTAIMAGEN");
				
				$data[$i]["NOMBRE"] = $NOMBRE->item(0)->nodeValue;
				$data[$i]["TITULOENPORTAL"] = utf8_decode($TITULOENPORTAL->item(0)->nodeValue);
				$data[$i]["TEXTOENPORTAL"] = utf8_decode($TEXTOENPORTAL->item(0)->nodeValue);
				$data[$i]["ALBUM"] = $ALBUM->item(0)->nodeValue;
				$data[$i]["KEYWORD"] = $KEYWORD->item(0)->nodeValue;
				$data[$i]["FILASXPAGINA"] = $FILASXPAGINA->item(0)->nodeValue;
				$data[$i]["NUMPAGINA"] = $NUMPAGINA->item(0)->nodeValue;
				$data[$i]["RUTAIMAGEN"] = str_replace("_ImagenesPortalWap/","imagenes/",$RUTAIMAGEN->item(0)->nodeValue);
				$i++;			
				 
			}
		}
		return $data;
	}
	
	
	
		function getBaner(){
		$doc = new DOMDocument();
		$file=$this->get_fileNowXml("portal");	
		//echo $file;	
		$doc->load( 'xml/' . $file );		
		$BANERs = $doc->getElementsByTagName("BANER");
		$i=0;
        $data=null;
		foreach( $BANERs as $BANER ){
			//echo "1";	
			$IMAGENES = $BANER->getElementsByTagName("IMAGEN");			
			foreach( $IMAGENES as $IMAGEN ){
				//echo "2";
				$IMG = $IMAGEN->getElementsByTagName("IMG");
				$LINK = $IMAGEN->getElementsByTagName("LINK");
				$data[$i]["IMG"] = $IMG->item(0)->nodeValue;
				$data[$i]["LINK"] = $LINK->item(0)->nodeValue;
				$i++;
			}
		}
		return $data;
	}
	
	
	
	
	
	
	function getCuerpo(){
		$doc = new DOMDocument();
		$file=$this->get_fileNowXml("portal");	
		//echo $file;	
		$doc->load( 'xml/' . $file );		
		$BODYs = $doc->getElementsByTagName("BODY");
		$i=0;
        $data=null;
		foreach( $BODYs as $BODY ){
			//echo "1";	
			$ITEMs = $BODY->getElementsByTagName("ITEM");			
			foreach( $ITEMs as $ITEM ){
				//echo "2";
				$NOMBRE = $ITEM->getElementsByTagName("NOMBRE");
				$TITULOENPORTAL = $ITEM->getElementsByTagName("TITULOENPORTAL");
				$TEXTOENPORTAL = $ITEM->getElementsByTagName("TEXTOENPORTAL");
				$ALBUM = $ITEM->getElementsByTagName("ALBUM");
				$KEYWORD = $ITEM->getElementsByTagName("KEYWORD");
				$FILASXPAGINA = $ITEM->getElementsByTagName("FILASXPAGINA");
				$NUMPAGINA = $ITEM->getElementsByTagName("NUMPAGINA");
				$RUTAIMAGEN = $ITEM->getElementsByTagName("RUTAIMAGEN");
				
				$data[$i]["NOMBRE"] = $NOMBRE->item(0)->nodeValue;
				$data[$i]["TITULOENPORTAL"] = $TITULOENPORTAL->item(0)->nodeValue;
				$data[$i]["TEXTOENPORTAL"] = $TEXTOENPORTAL->item(0)->nodeValue;
				$data[$i]["ALBUM"] = $ALBUM->item(0)->nodeValue;
				$data[$i]["KEYWORD"] = $KEYWORD->item(0)->nodeValue;
				$data[$i]["FILASXPAGINA"] = $FILASXPAGINA->item(0)->nodeValue;
				$data[$i]["NUMPAGINA"] = $NUMPAGINA->item(0)->nodeValue;
				$data[$i]["RUTAIMAGEN"] = $RUTAIMAGEN->item(0)->nodeValue;
				$i++;			
				 
			}
		}
		return $data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_categoria($CATEGORIA,$NUM=3,$TOPTXT=true,$img,$baseurl,$vermas){
		if( !is_numeric($NUM) || $NUM < 0 ) $NUM=3;
		$p_path = new path;
		$doc = new DOMDocument();
		switch($CATEGORIA){
			case "noticias":
				$txcategoria="Noticias";
			break;
			case "musica":
				$txcategoria="M&uacute;sica y Tonos";
			break;
			case "juegos":
				$txcategoria="Juegos";
			break;
			case "rbt":
				$txcategoria="Rbt";
			break;
		}
		
		$file=$this->get_fileNowXml($CATEGORIA);
		
		$doc->load( $p_path->getPath('xml') . '/' . $file );
		
		$categorias = $doc->getElementsByTagName( "Noticias" );
		
		foreach( $categorias as $categoria ){
			$corre+=1;
			if($corre>$NUM){
				if($vermas==true){
					$categoriaBody .= "<a class=\"item\" style=\"line-height: 30px !important;text-align: right;\" href=\"noticias.php?des=".$CATEGORIA."\">";
					$categoriaBody .= "<span class=\"tema\">Ver más [+]</span>";
					$categoriaBody .= "<span class=\"clear\"></span>";
					$categoriaBody .= "</a>";
				}
				else{
					//$categoriaBody .= "<a class=\"item\" style=\"line-height: 30px !important;text-align: right;\" href=\"index_.php\">";
//					$categoriaBody .= "<span class=\"tema\">Regresar [-]</span>";
//					$categoriaBody .= "<span class=\"clear\"></span>";
//					$categoriaBody .= "</a>";
				}
				 break;
			}
			
			$titulos = $categoria->getElementsByTagName( "titulo" );
			$url = $categoria->getElementsByTagName( "link" );
			$descripcion = $categoria->getElementsByTagName( "descripcion" );
			
			$categoriaBody .= "<a class=\"item\" href=\"" . $baseurl . $url->item(0)->nodeValue ."\"><table>";
			$categoriaBody .= "<tr><td><img src=\"img/".$img.".jpg\" width=\"70\" height=\"56\" alt=\"noticias\" /></td>";
			$categoriaBody .= "<td><span>" .urldecode ($titulos->item(0)->nodeValue) . "</span>";
			
			if($TOPTXT)
				$categoriaBody .= " <br />". urldecode($descripcion->item(0)->nodeValue) ;
			
			$categoriaBody .= "</td><td><span class=\"flecha\"></span></td></tr></table></a>";

			 
			
			
//			  <a class="item" href="busqueda.php?pag=1&filtro=">
//<span class="numero"></span>
//<span class="tema">Ver más [+]</span>
//<span class="flecha"></span>
//<span class="clear"></span>
//</a>
		}
		//$categoriaBody = "<div class=\"item\">" . $txt_top .  "<div class=\"text_item_li\">". $categoriaBody . "</div></div>";
		return $categoriaBody;
	}
	
	 
	
	function get_randomImage(){
		$p_path = new path;
		$directorio=opendir($p_path->getPath('img'));
		while($archivador=readdir($directorio)){
			$archivos[]=$archivador;
			//echo $archivador;
		}
		unset($archivos[array_search(".",$archivos)]);
		unset($archivos[array_search("..",$archivos)]);
		$archivos = array_values($archivos);
		return $archivos[rand(0,count($archivos)-1)];
		//foreach($archivos as $archivo){echo $archivo . "<br />";}
	}
}
?>
