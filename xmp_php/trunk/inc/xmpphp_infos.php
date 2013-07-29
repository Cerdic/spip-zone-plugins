<?php 
/**
 * XMP php
 * Récupération des métadonnées XMP
 *
 * Auteur : kent1 (kent1@arscenic.info - www.kent1.info )
 * ©2011-2013 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_xmpphp_infos_dist($id_document=false,$fichier=null,$only_return=false){
	if((!intval($id_document) && !isset($fichier)) OR !extension_loaded('xmpPHPToolkit'))
		return false;

	include_spip('inc/documents');
	$fichier_tmp = false;

	if($fichier && file_exists($fichier)){
		spip_log("XMP PHP : recuperation des infos du document $fichier","xmp");
	}elseif(is_numeric($id_document)){
		spip_log("XMP PHP : recuperation des infos du document $id_document","xmp");
		$chemin = sql_getfetsel("fichier", "spip_documents","id_document=".intval($id_document));
		$fichier = get_spip_doc($chemin);
		if(!file_exists($fichier)){
			spip_log("Erreur : le fichier $fichier n'existe pas","xmp");
			return false;
		}
	}

	SXMPFiles::Initialize();
	$xmpfiles = new SXMPFiles();
	$xmpmeta = new SXMPMeta();
	$xmpfiles->OpenFile($fichier);
	$xmpfiles->GetXMP($xmpmeta);
	$xmpmeta->Sort();

	$a = $b = $c = $o = '';

	$iterator = new SXMPIterator(&$xmpmeta);
	$content = array();
	while($iterator->Next(&$a, &$b, &$c, &$o) == TRUE){
		if(preg_match(',(.*):(.*)\[(.*)\](.*),',$b,$matches)){
			if(!is_array($content[$matches[1]][$matches[2]])){
				if(is_null OR !$content[$matches[1]][$matches[2]])
					$content[$matches[1]][$matches[2]] = array();
				else
					$content[$matches[1]][$matches[2]] = array($content[$matches[1]][$matches[2]]);
			}
			if($matches[4]){
				if(!is_array($content[$matches[1]][$matches[2]][$matches[3]])){
					if(is_null($content[$matches[1]][$matches[2]][$matches[3]]) OR !$content[$matches[1]][$matches[2]][$matches[3]])
						$content[$matches[1]][$matches[2]][$matches[3]] = array();
					else
						$content[$matches[1]][$matches[2]][$matches[3]] = array($content[$matches[1]][$matches[2]][$matches[3]]);
				}
				$cle = str_replace('/','',str_replace('/?','',$matches[4]));
				$content[$matches[1]][$matches[2]][$matches[3]][$cle] = $c;
			}
			else
				$content[$matches[1]][$matches[2]][$matches[3]] = $c;
		}else if(!is_null($c) && $c && preg_match(',(.*):(.*)\/(.*),',$b,$matches)){
			$cle = str_replace('/','',str_replace('/?','',$matches[4]));
			$content[$matches[1]][$matches[2]][$matches[3]][$cle] = $c;
		}else if(!is_null($c) && $c && preg_match(',(.*):(.*),',$b,$matches))
			$content[$matches[1]][$matches[2]] = $c;
	}

	spip_log($content,'xmp');

	$xmpfiles->CloseFile();

	/**
	 * Si on a $only_return à true, on souhaite juste retourner les metas, sinon on les enregistre en base
	 * Utile pour metadatas/video par exemple
	 */
	if(!$only_return && (intval($id_document) && (count($content) > 0))){
		$infos = array('metas'=>serialize($content));
		include_spip('action/editer_document');
		spip_log('modification du document','xmp');
		spip_log($infos,'xmp');
		document_modifier($id_document, $infos);
		return true;
	}
	return array('metas'=>serialize($content));
}
?>