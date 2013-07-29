<?php 
/**
 * XMP php
 * Récupération des métadonnées XMP
 *
 * Auteur : kent1
 * ©2011 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_xmpphp_infos_dist($id_document){
	if(!intval($id_document) OR !extension_loaded('xmpPHPToolkit'))
		return false;

	include_spip('inc/documents');
	$document = sql_fetsel("docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin = $document['fichier'];
	$fichier = get_spip_doc($chemin);
	
	if(!file_exists($fichier)){
		spip_log("Erreur : le fichier $fichier n'existe pas","xmp");
		return;
	}
	SXMPFiles::Initialize();
	$xmpfiles = new SXMPFiles();
	$xmpmeta = new SXMPMeta();
	
	$xmpfiles->OpenFile($fichier);
	$xmpfiles->GetXMP($xmpmeta);

	$xmpmeta->Sort();
	
	$a = $b = $c = $o = "";

	$iterator = new SXMPIterator(&$xmpmeta);
	spip_log($xmpmeta,'xmp');
	$content = array();
	while($iterator->Next(&$a, &$b, &$c, &$o) == TRUE){
		if(preg_match(',(.*):(.*)\[(.*)\](.*),',$b,$matches)){
			spip_log($b,'xmp');
			if(!is_array($content[$matches[1]][$matches[2]])){
				if(is_null OR !$content[$matches[1]][$matches[2]]){
					$content[$matches[1]][$matches[2]] = array();
				}else{
					$content[$matches[1]][$matches[2]] = array($content[$matches[1]][$matches[2]]);
				}
			}
			if($matches[4]){
				if(!is_array($content[$matches[1]][$matches[2]][$matches[3]])){
					if(is_null($content[$matches[1]][$matches[2]][$matches[3]]) OR !$content[$matches[1]][$matches[2]][$matches[3]]){
						spip_log($b.' est null','xmp');
						$content[$matches[1]][$matches[2]][$matches[3]] = array();
					}else{
						$content[$matches[1]][$matches[2]][$matches[3]] = array($content[$matches[1]][$matches[2]][$matches[3]]);
					}
				}
				$cle = str_replace('/?','',$matches[4]);
				$cle = str_replace('/','',$cle);
				spip_log($cle.' => '.$c,'xmp');
				$content[$matches[1]][$matches[2]][$matches[3]][$cle] = $c;
			}
			else{
				$content[$matches[1]][$matches[2]][$matches[3]] = $c;
			}
		}else if(preg_match(',(.*):(.*)\/(.*),',$b,$matches)){
			spip_log($b,'xmp');
			if(is_null($c) OR !$c){
				spip_log('c est null','xmp');
			}else{
				$cle = str_replace('/?','',$matches[4]);
				$cle = str_replace('/','',$cle);
				$content[$matches[1]][$matches[2]][$matches[3]][$cle] = $c;
			}
		}else if(preg_match(',(.*):(.*),',$b,$matches)){
			if(is_null($c) OR !$c){
				
			}else{
				$content[$matches[1]][$matches[2]] = $c;
			}
		}
	}

	spip_log($content,'xmp');
	
	$xmpfiles->CloseFile();
	
	return $buffer;
}
?>