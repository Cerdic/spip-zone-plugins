<?php
/**
 * Plugin Videos Accessibles
 * Licence GPL (c) 2011 Cedric Morin yterium pour Temesis
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_adapter_image_charger_dist($id_document,$mode){
	$mode = preg_replace(',\W,','',$mode);
	include_spip('inc/documents');
	$valeurs = sql_fetsel('id_document,mode,extension','spip_documents','id_document='.intval($id_document));
	if (!$valeurs)
		return array('editable'=>false,'id'=>$id_document);

	$valeurs['id'] = $id_document;
	$valeurs['_hidden'] = "<input name='id_document' value='$id_document' type='hidden' />";
	$valeurs['mode'] = $mode; // pour les id dans le dom
	$annexe = adaptive_images_variante($id_document,$mode);
	if ($annexe)
		$valeurs['id_annexe'] = $annexe['id_document'];
	if ($annexe)
		$annexe['type_document'] = sql_getfetsel('titre as type_document','spip_types_documents','extension='.sql_quote($annexe['extension']));
	$valeurs['annexe'] = $annexe;
	$valeurs['_pipeline'] = array('editer_contenu_objet',array('type'=>'adapter_image','mode'=>$mode,'id'=>$id_document));

	adaptive_images_width_from_mode($mode, $valeurs);

	return $valeurs;
}

function formulaires_adapter_image_verifier_dist($id_document,$mode){
	$mode = preg_replace(',\W,','',$mode);
	$erreurs = array();
	if (_request('supprimer')){

	}
	else {
		$annexe = adaptive_images_variante($id_document,$mode);
		$id = $annexe['id_document'];
		$verifier = charger_fonction('verifier','formulaires/joindre_document');
		$erreurs = $verifier($id,0,'',$mode);
	}
	return $erreurs;
}

function formulaires_adapter_image_traiter_dist($id_document,$mode){
	$mode = preg_replace(',\W,','',$mode);
	$annexe = adaptive_images_variante($id_document,$mode);
	$id = $annexe['id_document'];
	$res = array('editable'=>true);
	if (_request('supprimer')){
		$supprimer_document = charger_fonction('supprimer_document','action');
		if ($id)
			$supprimer_document($id);
		$res['message_ok'] = _T('adaptive_images:variante_'.$mode.'_supprimee');
	}
	else {
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');

		include_spip('inc/joindre_document');
		$files = joindre_trouver_fichier_envoye();

		$ajoute = $ajouter_documents($id,$files,'document',$id_document,$mode);

		if (is_numeric(reset($ajoute))
		  AND $id = reset($ajoute)){
			$res['message_ok'] = _T('medias:document_installe_succes');
		}
		else
			$res['message_erreur'] = reset($ajoute);
	}

	return $res;
}

/**
 * Verifier la taille de l'image version mobile
 * @param $infos
 * @return bool|mixed|string
 */
function inc_verifier_document_mode_mobileview_dist($infos){
	if (isset($infos['type_image'])){
		$v = array();
		adaptive_images_width_from_mode('mobileview',$v);
		if ($infos['largeur']<$v['_width_hr']){
			return _T('adaptive_images:erreur_largeur_mobileview',array('width'=>$v['_width_hr']));
		}
	}
	return true;
}

function adaptive_images_width_from_mode($mode, &$valeurs){

	switch ($mode){
		case 'mobileview':
			$valeurs['_width'] = _ADAPTIVE_IMAGES_MAX_WIDTH_MOBILE_VERSION;
			break;
		default:
			$valeurs['_width'] = 0;
			break;
	}
	$valeurs['_width_hr'] = 2*$valeurs['_width'];
}
?>