<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/autoriser');
include_spip('inc/documents');
include_spip('inc/filtres_images');
include_spip('photospip_fonctions');

function formulaires_editer_image_charger_dist($id_document='new',$mode=false, $retour=''){
	$valeurs = array('editable'=>true);
	$id_document = sql_getfetsel('id_document','spip_documents','id_document='.intval($id_document));
	$valeurs['id_document'] = $id_document;
	$valeurs['mode'] = $mode;
	
	if(!$id_document){
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('phpotospip:erreur_doc_numero');
		return $valeurs;
	}
		
	/**
	 * Restaurer les inputs en cas de test
	 */
	foreach(array('filtre',
		'ratio',
		'recadre_width',
		'recadre_height',
		'recadre_x1',
		'recadre_x2',
		'recadre_y1',
		'recadre_y2',
		'reduire_width',
		'reduire_height',
		'passe_partout_width',
		'passe_partout_height',
		'params_tourner',
		'params_image_sepia',
		'params_image_gamma',
		'params_image_flou',
		'params_image_saturer',
		'params_image_rotation',
		'params_image_niveaux_gris_auto',
		'type_modification') as $input){
		if(_request($input))
			$valeurs[$input] = _request($input);	
	}
	
	$valeurs['largeur_previsu'] = test_espace_prive()? 800 : lire_config('photospip/largeur_previsu','450');
	if($mode != 'vignette'){
		$limite = lire_config('photospip/limite_version',1000000);
		$nb_versions = sql_countsel('spip_documents_inters','id_document='.intval($id_document));
		$resultats = lire_config('photospip/resultats',array('remplacer_image','creer_nouvelle_image','creer_version_image'));
		if(count($resultats) == 1){
			$valeurs['_hidden'] .= '<input type="hidden" name="type_modification" value="'.$resultats[0].'" />';
		}
		if($nb_versions >= $limite){
			$valeurs['modifiable'] = false;
			$valeurs['message_erreur'] = _T('phpotospip:erreur_nb_versions_atteint',array('nb'=>$limite));
			return $valeurs;
		}
	}else{
		$id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document));
		if($id_vignette && ($id_vignette > 0) && $id_vignette = sql_getfetsel('id_document','spip_documents','id_document='.intval($id_vignette))){
			$valeurs['id_document'] = $id_vignette;
			$valeurs['vignette'] = 'oui';
		}
	}
	
	if(!autoriser('modifier','document',$id_document)){
		$valeurs['message_erreur'] = _T('photospip:erreur_auth_modifier');
		$valeurs['editable'] = false;
	}
	else if($GLOBALS['meta']['image_process'] != 'gd2'){
		$valeurs['message_erreur'] = _T('photospip:erreur_image_process');
		$valeurs['editable'] = false;
	}
	spip_log($valeurs,'photospip');
	return $valeurs;
}

function formulaires_editer_image_verifier_dist($id_document='new',$mode=false, $retour=''){
	if(!_request('supprimer_vignette') && !_request('supprimer_version') && !_request('revenir_version')){
		if(!$var_filtre = _request('filtre'))
			$erreurs['message_erreur'] = _T('photospip:erreur_form_filtre');
		else if(($mode != 'vignette') && (!$type_resultat = _request('type_modification')))
			$erreurs['message_erreur'] = _T('photospip:erreur_form_type_resultat');
		/**
		 * On test uniquement
		 */
		else{
			$verifier = charger_fonction('verifier','inc');
			$params = photospip_recuperer_params_form($var_filtre);
			if(in_array($var_filtre,array('image_reduire','image_passe_partout'))){
				if((strlen($params[0]) && strlen($erreur_param0 = $verifier($params[0],'entier'))) OR (strlen($params[1]) && strlen($erreur_param1 = $verifier($params[1],'entier')))){
					$erreurs[$var_filtre] = _T('photospip:erreur_valeurs_numeriques');
				}
			}
			if(in_array($var_filtre,array('image_rotation','image_gamma','image_flou'))){
				if(strlen($params[0]) && strlen($erreur_param0 = $verifier($params[0],'entier')))
					$erreurs[$var_filtre] = _T('photospip:erreur_valeur_numerique');
				elseif($var_filtre == 'image_rotation' && $erreur_param0 = $verifier($params[0],'entier',array('min'=>'-180','max'=>'180'))){
					$erreurs[$var_filtre] = $erreur_param0;
				}
				elseif($var_filtre == 'image_gamma' && $erreur_param0 = $verifier($params[0],'entier',array('min'=>'-254','max'=>'254'))){
					$erreurs[$var_filtre] = $erreur_param0;
				}
				elseif($var_filtre == 'image_flou' && $erreur_param0 = $verifier($params[0],'entier',array('min'=>'1','max'=>'11'))){
					$erreurs[$var_filtre] = $erreur_param0;
				}
			}
			if(($var_filtre == 'image_sepia') && strlen($params[0]) && strlen($erreur_param0 = $verifier($params[0],'couleur', array('type'=>'hexa'))))
				$erreurs[$var_filtre] = $erreur_param0;
		}
		/**
		 * Ces erreurs ne sont pas de réelles erreurs mais seulement
		 * les valeurs pour la prévisualisation si c'est ce que l'on a validé
		 */
		if(count($erreurs) == 0 && _request('tester')){
			if(in_array($var_filtre,array('tourner','image_recadre'))){
				$erreurs['message_erreur'] = _T('photospip:erreur_form_filtre_sstest');
			}
			else{
				list($param1, $param2, $param3,$params) = photospip_recuperer_params_form($var_filtre);
				$erreurs['message'] = 'previsu';
				$erreurs['filtre'] = $var_filtre;
				$erreurs['param'] = $params;
				$erreurs['param1'] = $param1;
				if($param2){
					$erreurs['param2'] = $param2;
				}
				if($param3){
					$erreurs['param3'] = $param3;
				}
			}
		}
	}
	return $erreurs;
}

function formulaires_editer_image_traiter_dist($id_document='new',$mode=false, $retour=''){
	$res = array('editable'=>true);
	$autoclose= '';
	if($mode == 'vignette'){
		$id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document));
		$res['redirect'] = sinon(_request('redirect'),'');
		if(_request('supprimer_vignette')){
			$supprimer_document = charger_fonction('supprimer_document','action');
			if ($id_vignette)
				$supprimer_document($id_vignette);
			$res['message_ok'] = _T('medias:vignette_supprimee').$autoclose;
			set_request('id_document',$id_document);
		}else{
			$id_document_orig = $id_document;
			if($id_vignette && ($id_vignette > 0) && $id_vignette = sql_getfetsel('id_document','spip_documents','id_document='.intval($id_vignette)))
				$id_document = $id_vignette;
		}
	}
	if(_request('validation') OR _request('supprimer_vignette')){
		$row = sql_fetsel('*','spip_documents','id_document='.intval($id_document)); 
		$src = get_spip_doc($row['fichier']);
		
		if(!$mode){
			$mode = $row['mode'];
		}
		if(!_request('supprimer_vignette')){
			if(_request('type_retour') == 'retour'){
				$res['redirect'] = $retour ? $retour : _request('retour');
				$autoclose = "<script type='text/javascript'>if (window.jQuery) jQuery.modalboxclose();</script>";
			}else{
				$res['redirect'] = '';
			}
			$var_filtre = _request('filtre');
			$type_modif = _request('type_modification');
			$params = photospip_recuperer_params_form($var_filtre);
			
			$version = sql_countsel('spip_documents_inters','id_document='.intval($row['id_document']))+1;
			// on transforme l'image en png non destructif
			//spip_log("On transforme l'image source en PNG non destructif","photospip");
			//$src = extraire_attribut(image_alpha($src,0),'src');
			
			/**
			 * L'image créée aura pour nom image_orig-xxxx.ext où xxxx est le md5 de la date
			 * L'image temporaire est crée dans tmp/
			 */
			$src_tmp = preg_replace(",\-photospip\w+([^\-]),","$1", $src);
			$tmp_img = _DIR_TMP.preg_replace(",\.[^.]+$,","-photospip".md5(date('Y-m-d H:i:s'))."$0", basename($src_tmp));
			$dest = preg_replace(",\.[^.]+$,","-photospip".md5(date('Y-m-d H:i:s'))."$0", $src_tmp);
			
			spip_log("application du filtre $var_filtre $src : $tmp_img","photospip");
			if($var_filtre == "tourner"){
				include_spip('inc/filtres');
				include_spip('public/parametrer'); // charger les fichiers fonctions #bugfix spip 2.1.0
				$tmp_img = filtrer('image_rotation',$src,$params[3]);
				$tmp_img = filtrer('image_format',$tmp_img,$row['extension']);
				$tmp_img = extraire_attribut($tmp_img,'src');
			}
			else{
				$appliquer_filtre = charger_fonction('photospip_appliquer_filtre','inc');
				$sortie = $appliquer_filtre($src, $tmp_img, $var_filtre,$params);
				if(!$sortie && (file_exists($tmp_img))){
					$res['message_erreur'] = 'photospip n a pas pu appliquer le filtre '.$var_filtre;
					return $res;
				}
			}
			if($type_modif == 'creer_version_image'){
				$size_image = getimagesize($tmp_img);
				$largeur = $size_image[0];
				$hauteur = $size_image[1];
				$ext = substr(basename($tmp_img), strpos(basename($tmp_img), ".")+1, strlen(basename($tmp_img)));
				$poids = filesize($tmp_img);
				/**
				 * Crée une version de l'image
				 */
				if(is_array($params))
					$params = serialize($params);
				include_spip('inc/getdocument');
				sql_insertq("spip_documents_inters",array("id_document" => $row['id_document'],"id_auteur" => $id_auteur,"extension" => $row['extension'], "fichier" => $row['fichier'], "taille" => $row['taille'],"hauteur" => $row['hauteur'], "largeur" => $row['largeur'],"mode" => $row['mode'], "version" => ($version? $version:1), "filtre" => $var_filtre, "param" => $params));
				deplacer_fichier_upload($tmp_img,$dest,true);
				spip_log("move $tmp_img => $dest",'photospip');
				sql_updateq('spip_documents', array('fichier' => set_spip_doc($dest), 'taille' => $poids, 'largeur'=>$largeur, 'hauteur'=>$hauteur, 'extension' => $ext), "id_document=".intval($row['id_document']));
				spip_log("Update de l'image dans la base poid= $poids, extension = $ext, hauteur= $hauteur, largeur = $largeur, fichier = $dest","photospip");
			}else {
				$files[0]['tmp_name'] = $tmp_img;
				$files[0]['name'] = basename($dest);
				if(($type_modif == 'remplacer_image') OR $mode=='vignette'){
					/**
					 * Remplace l'image actuelle par une nouvelle
					 */
					 $ajouter_document = charger_fonction('ajouter_documents','action');
					 if($mode != 'vignette'){
						$ajoute = $ajouter_document($row['id_document'], $files, $objet, $id_objet, $mode);
					 }else{
						 $ajoute = $ajouter_document($id_vignette,$files,'',0,'vignette');
						if(is_int(reset($ajoute))){
							$id_vignette = reset($ajoute);
							include_spip('action/editer_document');
							document_set($id_document_orig,array("id_vignette" => $id_vignette,'mode'=>'document'));
							set_request('id_document',$id_vignette);
							$res['message_ok'] = _T('photospip:message_vignette_installe_succes').$autoclose;
						}
					 }
					 include_spip('inc/flock');
					 spip_unlink($tmp_img);
				}
				if($type_modif == 'creer_nouvelle_image'){
					/**
					 * Crée un nouveau document
					 */
					 spip_log('on crée un nouveau doc','photospip');
					 $ajouter_document = charger_fonction('ajouter_documents','action');
					 $objet_lie = sql_fetsel('*','spip_documents_liens','id_document='.intval($row['id_document']));
					 $id_document = $ajouter_document('new', $files, $objet_lie['objet'], $objet_lie['id_objet'], $mode);
					 spip_log($id_document,'photospip');
					 $res['redirect'] = parametre_url(parametre_url(self(),'redirect',''),'id_document',$id_document[0]);
					 include_spip('inc/flock');
					 spip_unlink($tmp_img);
				}
			}
		}
		/**
		 * Restaurer les inputs à vide
		 */
		foreach(array('filtre',
			'ratio',
			'recadre_width',
			'recadre_height',
			'recadre_x1',
			'recadre_x2',
			'recadre_y1',
			'recadre_y2',
			'reduire_width',
			'reduire_height',
			'passe_partout_width',
			'passe_partout_height',
			'type_modification') as $input){
			if(_request($input))
				set_request($input,'');	
		}
	}
	if(_request('supprimer_version')){
		include_spip('action/images_versions');
		$r['action_faire'] = 'supprimer';
		$r['version'] = _request('version');
		$r[1] = $id_document;
		$res['message_ok'] = _T('photospip:message_ok_version_supprimee',array('version'=>_request('version')));
		action_images_versions_post($r);
	}
	else if(_request('revenir_version')){
		include_spip('action/images_versions');
		$r['action_faire'] = 'revenir';
		$r['version'] = _request('version');
		$r[1] = $id_document;
		$res['message_ok'] = _T('photospip:message_ok_version_retour',array('version'=>_request('version')));
		action_images_versions_post($r);
	}
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_document/$id_document'");
	if (!isset($res['message_erreur']) && !$res['message_ok'])
		$res['message_ok'] = _L('Votre modification a &eacute;t&eacute; enregistr&eacute;e').$autoclose;
	
	return $res;
}

function photospip_recuperer_params_form($var_filtre){
	$param1 = $param2 = $param3 = $params = null;
	if ($var_filtre == "tourner"){
		$params = _request('params_tourner');
	}
	else if($var_filtre == "image_reduire"){
		$param1 = _request('reduire_width');
		$param2 = _request('reduire_height');
		$params = array($param1,$param2);
	}
	else if($var_filtre == "image_passe_partout"){
		$param1 = _request('passe_partout_width');
		$param2 = _request('passe_partout_height');
		$params = array($param1,$param2);
	}
	else if ($var_filtre == "image_recadre"){
		$param1 = _request('recadre_width');
		$param2 = _request('recadre_height');
		$param_left = _request('recadre_x1');
		$param_top = _request('recadre_y1');
		$param3 = 'left='.$param_left.' top='.$param_top;
		$params = array($param1,$param2,$param3);
	}
	else if ($var_filtre == 'image_sepia'){
		$params = _request('params_image_sepia');
		$param1 = $params;
	}
	else if($var_filtre == 'image_gamma'){
		$param1 = _request('params_image_gamma');
	}
	else if($var_filtre == 'image_flou'){
		$param1 = _request('params_image_flou');
	}
	else if($var_filtre == 'image_saturer'){
		$param1 = _request('params_image_saturer');
	}
	else if($var_filtre == 'image_rotation'){
		$param1 = _request('params_image_rotation');
	}
	else if($var_filtre == 'image_niveaux_gris_auto'){
		$param1 = sinon(_request('params_image_niveaux_gris_auto'),null);
	}
	return array($param1,$param2,$param3,$params);
}
?>