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
		if (strcmp($a, SXMPMeta::kXMP_NS_EXIF)==0) {
			spip_log("Skipping ".SXMPMeta::kXMP_NS_EXIF." ... \n",'xmp');
			$iterator->Skip(SXMPIterator::kXMP_IterSkipSubtree );
		} else {
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
				else{
					$content[$matches[1]][$matches[2]][$matches[3]] = $c;
				}
			}else if(!is_null($c) && $c && preg_match(',(.*):(.*)\/(.*),',$b,$matches)){
				$cle = str_replace('/','',str_replace('/?','',$matches[4]));
				$content[$matches[1]][$matches[2]][$matches[3]][$cle] = $c;
			}else if(!is_null($c) && $c && preg_match(',(.*):(.*),',$b,$matches)){
				$content[$matches[1]][$matches[2]] = $c;
			}
		}
	}

	$xmpfiles->CloseFile();

	$infos_new = array();
	if(isset($content['dc']) && is_array($content['dc'])){
		foreach($content['dc'] as $cle => $val){
			if(in_array($cle,array('description','title','creator','rights')) && !isset($infos_new[$cle]) && is_array($val)){
				// On ne garde que la première
				$val = array_shift($val);
				if(is_array($val)){
					if(count($val) == 2 && isset($val['xml:lang']))
						unset($val['xml:lang']);
					if(count($val) == 1)
						$newval = array_shift($val);
				}else if(is_string($val))
					$newval = $val;

				$newval = str_replace('  ',' ',$newval);
				if($cle == 'description')
					$infos_new['descriptif'] = $newval;
				if($cle == 'title')
					$infos_new['titre'] = $newval;
				if($cle == 'creator')
					$infos_new['credits'] = $newval;
				if($cle == 'rights')
					$infos_new['credits'] = $newval;
			}
			if(($cle == 'subject') && is_array($val) && !isset($infos_new['tags']))
				$infos_new['tags'] = implode(',',$val);
		}
	}
	if(isset($content['pdf']) && is_array($content['pdf'])){
		foreach($content['pdf'] as $cle => $val){
			if(($cle == 'Producer') && !isset($infos_new['credits']) && is_string($val) && strlen($val) > 0)
				$infos_new['credits'] = $val;
			if(($cle == 'Copyright') && is_string($val) && strlen($val) > 0)
				$infos_new['credits'] = $val;
			if(($cle == 'Keywords') && is_string($val) && strlen($val) > 0 && !isset($infos_new['tags']))
				$infos_new['tags'] = implode(',',explode(' ',str_replace('  ',' ',$val)));
		}
	}
	if(isset($content['xmp']) && is_array($content['xmp'])){
		if(isset($content['xmp']['MetadataDate']) OR isset($content['xmp']['ModifyDate']) OR isset($content['xmp']['CreateDate'])){
			$date_verif = false;
			foreach(array('MetadataDate','ModifyDate','CreateDate') as $date){
				if(isset($content['xmp'][$date])){
					$date_verif = $date;
					break;
				}
			}
			if(preg_match('/([0-9]{4}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2}).*/',$content['xmp'][$date_verif],$matches))
				$infos_new['date'] = $matches[1].' '.$matches[2];
		}
	}
	if(isset($content['xmpRights']) && is_array($content['xmpRights'])){
		$url = false;
		if(preg_match('/^http:\/\/.*/',$content['xmpRights']['WebStatement']))
			$url = true;
		if($url && isset($infos_new['credits']))
			$infos_new['credits'] = '['.$infos_new['credits'].'->'.$content['xmpRights']['WebStatement'].']';
		elseif(!isset($infos_new['credits']))
			$infos_new['credits'] = $content['xmpRights']['WebStatement'];
	}

	$infos_new['metas'] = serialize($content);

	/**
	 * Si on a $only_return à true, on souhaite juste retourner les metas, sinon on les enregistre en base
	 * Utile pour metadatas/video par exemple
	 */
	if(!$only_return && (intval($id_document) && (count($infos_new) > 0))){
		$document = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
		foreach($infos_new as $champ=>$val){
			if($document[$champ] == $val) unset($infos_new[$champ]);
		}
		include_spip('action/editer_document');
		spip_log('modification du document','xmp');
		document_modifier($id_document, $infos_new);
		return true;
	}
	return $infos_new;
}
?>