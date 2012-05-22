<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/autoriser');
include_spip('inc/documents');
include_spip('photospip_fonctions');

function formulaires_editer_image_charger_dist($id_document='new', $retour='', $config_fonc='image_edit_config'){
	$valeurs = array();
	$id_document = sql_getfetsel('id_document','spip_documents','id_document='.intval($id_documents));
	$valeurs['id_document'] = $id_document;
	
	if(!$id_document){
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('phpotospip:erreur_doc_numero');
		return $valeurs;
	}
	
	$limite = lire_config('photospip/limite_version',1000000);
	$nb_versions = sql_count('*','spip_documents_inters','id_document='.intval($id_document));
	if($nb_versions >= $limite){
		$valeurs['modifiable'] = false;
		$valeurs['message_erreur'] = _T('phpotospip:erreur_nb_versions_atteint',array('nb'=>$limite));
	}
		
	/**
	 * Restaurer les inputs en cas de test
	 */
	foreach(array('filtre',
		'ratio','recadre_width','recadre_height','recadre_x1','recadre_y1',
		'params_image_sepia',
		'params_image_gamma',
		'params_image_flou',
		'params_image_saturer',
		'params_image_rotation',
		'params_image_niveaux_gris_auto') as $input){
		if(_request($input))
			$valeurs[$input] = _request($input);	
	}
	
	$valeurs['largeur_previsu'] = test_espace_prive()? 548 : lire_config('photospip/largeur_previsu','450');
	if(!is_numeric($id_document)){
		$valeurs['message_erreur'] = _T('photospip:erreur_doc_numero');
		$valeurs['editable'] = false;
	}
	else if(!$id_document = sql_getfetsel('id_document','spip_documents','id_document='.intval($id_document))){
		$valeurs['message_erreur'] = _T('photospip:erreur_doc_numero');
		$valeurs['editable'] = false;
	}
	else if(!autoriser('modifier','document',$id_document)){
		$valeurs['message_erreur'] = _T('photospip:erreur_auth_modifier');
		$valeurs['editable'] = false;
	}
	else if($GLOBALS['meta']['image_process'] != 'gd2'){
		$valeurs['message_erreur'] = _T('photospip:erreur_image_process');
		$valeurs['editable'] = false;
	}
	return $valeurs;
}

// Choix par defaut des options de presentation
function image_edit_config($row)
{
	global $spip_ecran, $spip_lang, $spip_display;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['langue'] = $spip_lang;

	$config['restreint'] = ($row['statut'] == 'publie');
	return $config;
}

function formulaires_editer_image_verifier_dist($id_document='new', $id_parent='', $retour='', $lier_trad=0, $config_fonc='documents_edit_config', $row=array(), $hidden=''){
	if(!$var_filtre = _request('filtre')){
		$erreurs['message_erreur'] = _T('photospip:erreur_form_filtre');
	}
	/**
	 * On test uniquement
	 */
	elseif(_request('tester')){
		if(in_array($var_filtre,array('tourner','image_recadre'))){
			$erreurs['message_erreur'] = _T('photospip:erreur_form_filtre_sstest');
		}
		else{
			list($param1, $param2, $param3,$params) = photospip_recuperer_params_form($var_filtre);
			$erreurs['message'] = 'previsu';
			$erreurs['filtre'] = $var_filtre;
			$erreurs['param'] = $params;
			if($param2){
				$erreurs['param2'] = $param2;
			}
			if($param3){
				$erreurs['param3'] = $param3;
			}
		}
	}
	return $erreurs;
}

function photospip_recuperer_params_form($var_filtre){
	$param1 = $param2 = $param3 = $params = null;
	if ($var_filtre == "tourner"){
		$params = _request('params_tourner');
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
		$params = str_replace('#','',$params);
	}
	else if($var_filtre == 'image_gamma'){
		$params = _request('params_image_gamma');
	}
	else if($var_filtre == 'image_flou'){
		$params = _request('params_image_flou');
	}
	else if($var_filtre == 'image_saturer'){
		$params = _request('params_image_saturer');
	}
	else if($var_filtre == 'image_rotation'){
		$params = _request('params_image_rotation');
	}
	else if($var_filtre == 'image_niveaux_gris_auto'){
		$params = '';
	}
	return array($param1,$param2,$param3,$params);
}
// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_image_traiter_dist($id_document='new', $id_parent='', $retour='', $lier_trad=0, $config_fonc='documents_edit_config', $row=array(), $hidden=''){
	$res = array();
	$var_filtre = _request('filtre');
	$params = photospip_recuperer_params_form($var_filtre);
	spip_log($params,'photospip');
	$row = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
	include_spip('inc/documents'); 
	$src = get_spip_doc($row['fichier']);
	if (preg_match(',^(.*)-photospip(\d+).([^.]+)$,', $src, $match)) {
		$version = $match[2];
		$orig_src = $match[1].'.'.$match[3];
		spip_log("nouvel src $src","photospip");
		spip_log("version = $version","photospip");
		$newversion = ++$version;
		spip_log("La nouvelle version sera $newversion","photospip");
	}
	if($version){
		// $dest = preg_replace(',\.[^.]+$,', '-r'.$var_rot.'$0', $src); //original
		$dest = preg_replace(",\.[^.]+$,", "-photospip".($newversion)."$0", $orig_src);
		spip_log("la destination sera $dest","photospip");
		spip_log("application du filtre $var_filtre $src : $dest","photospip");
	}
	else{
		$dest = preg_replace(',\.[^.]+$,', '-photospip1.png', $src);
		// on transforme l'image en png non destructif
		include_spip('inc/filtres_images');
		spip_log("On transforme l'image source en PNG non destructif","photospip");
		$src = extraire_attribut(image_alpha($src,0),'src');
		spip_log("application du filtre $var_filtre $src : $dest","photospip");
	}
	
	if($var_filtre == "tourner"){
		include_spip('inc/filtres');
		include_spip('public/parametrer'); // charger les fichiers fonctions #bugfix spip 2.1.0
		$dst_img = filtrer('image_rotation',$src,$params[3]);
		$dst_img = filtrer('image_format',$dst_img,$row['extension']);
		$dst_img = extraire_attribut($dst_img,'src');
		include_spip('inc/getdocument');
		deplacer_fichier_upload($dst_img,$dest);
	}
	else{
		$sortie = photospipfiltre($src, $dest, $var_filtre,$params);
		if(!$sortie){
			$res['message_erreur'] = 'photospip n a pas pu appliquer le filtre '.$var_filtre;
			return $res;
		}
	}

	$size_image = getimagesize($dest);
	spip_log("taille de l'image $size_image[0] x $size_image[1]","photospip");
	$largeur = $size_image[0];
	$hauteur = $size_image[1];
	$ext = substr(basename($dest), strpos(basename($dest), ".")+1, strlen(basename($dest)));
	$poids = filesize($dest);
	
	// succes !
	if ($largeur>0 AND $hauteur>0) {
		if(is_array($params))
			$params = serialize($params);
		sql_insertq("spip_documents_inters",array("id_document" => $row['id_document'],"id_auteur" => $id_auteur,"extension" => $row['extension'], "fichier" => $row['fichier'], "taille" => $row['taille'],"hauteur" => $row['hauteur'], "largeur" => $row['largeur'],"mode" => $row['mode'], "version" => ($version? $version:1), "filtre" => $var_filtre, "param" => $params));
		sql_updateq('spip_documents', array('fichier' => set_spip_doc($dest), 'taille' => $poids, 'largeur'=>$largeur, 'hauteur'=>$hauteur, 'extension' => $ext), "id_document=".intval($row['id_document']));
		spip_log("Update de l'image dans la base poid= $poids, extension = $ext, hauteur= $hauteur, largeur = $largeur, fichier = $dest","photospip");
	}

	if (!isset($res['redirect']))
		$res['editable'] = true;
	if (!isset($res['message_erreur']))
		$res['message_ok'] = _L('Votre modification a &eacute;t&eacute; enregistr&eacute;e');

	return $res;
}

?>