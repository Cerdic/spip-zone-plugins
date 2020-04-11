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
	$doc = sql_fetsel('id_document,mode,extension,largeur,hauteur','spip_documents','id_document='.intval($id_document));
	if (!$doc)
		return array('editable'=>false,'id'=>$id_document);

	$valeurs = array(
		'id_document' => $doc['id_document'],
		'extension' => $doc['extension'],
		'id' => $id_document,
		'_hidden' => "<input name='id_document' value='$id_document' type='hidden' />",
		'mode' => $mode, // pour les id dans le dom
		'_bigup_rechercher_fichiers' => true,
	);

	$annexe = adaptive_images_variante($id_document,$mode);
	if ($annexe){
		$valeurs['id_annexe'] = $annexe['id_document'];
		$annexe['type_document'] = sql_getfetsel('titre as type_document','spip_types_documents','extension='.sql_quote($annexe['extension']));

		// verifier que les proportions de la version mobile et de la version desktop sont les memes
		// si on utilise la methode 3 couches
		if (lire_config('adaptive_images/markup_method','3layers') === '3layers') {
			$h2 = intval(round($annexe['largeur']*$doc['hauteur']/$doc['largeur']));
			if (abs(intval($h2-$annexe['hauteur']))>1){
				$size1 = $annexe['largeur']." x {$h2} pixels";
				$w2 = intval(round($annexe['hauteur']*$doc['largeur']/$doc['hauteur']));
				$size2 = "{$w2} x ".$annexe['hauteur']." pixels";
				$valeurs['_warning_ratio'] = _T('adaptive_images:warning_ratio_mobileview',array('size1'=>$size1,'size2'=>$size2));
				$valeurs['_warning_ratio'] .= " <input type='submit' name='recadrer' value='".attribut_html(_T('adaptive_images:bouton_recadrer'))."' />";
			}
		}
	}
	$valeurs['annexe'] = $annexe;
	$valeurs['_pipeline'] = array('editer_contenu_objet',array('type'=>'adapter_image','mode'=>$mode,'id'=>$id_document));

	adaptive_images_width_from_mode($mode, $valeurs);

	return $valeurs;
}

/**
 * @param array $args
 * @param \Spip\Bigup\Formulaire $formulaire
 * @return \Spip\Bigup\Formulaire
 */
function inc_bigup_medias_formulaire_adapter_image_dist($args, $formulaire) {
	$formulaire->preparer_input(
		'fichier_upload[]',
		[
			'multiple' => false,
			'previsualiser' => true,
			'input_class' => 'bigup_illustration',
			'drop-zone-extended' => '.formulaire_adapter_image .editer_fichier',
		]
	);
	$formulaire->inserer_js('bigup.adapter_image.js');
	return $formulaire;
}


function formulaires_adapter_image_verifier_dist($id_document,$mode){
	$mode = preg_replace(',\W,','',$mode);
	$erreurs = array();
	if (_request('supprimer')){

	}
	elseif (_request('recadrer')){

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
	elseif (_request('recadrer')){
		$doc = sql_fetsel('id_document,mode,extension,largeur,hauteur','spip_documents','id_document='.intval($id_document));
		$h2 = intval(round($annexe['largeur']*$doc['hauteur']/$doc['largeur']));
		$w = $annexe['largeur'];
		$h = $annexe['hauteur'];
		if ($h2<$annexe['hauteur'])
			$h = $h2;
		else
			$w = intval(round($annexe['hauteur']*$doc['largeur']/$doc['hauteur']));

		include_spip("filtres/images_transforme");
		include_spip("inc/documents");
		$file = get_spip_doc($annexe['fichier']);
		if (function_exists("image_recadre")){
			$ir = extraire_attribut(image_recadre($file,$w,$h),'src');
			list($h,$w) = taille_image($ir);
			if ($ir AND $h AND $w){
				@unlink($file);
				@rename($ir,$file);
				$set = array(
					"hauteur" => $h,
					"largeur" => $w,
				);
				sql_updateq("spip_documents",$set,"id_document=".intval($annexe['id_document']));
			}
		}
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

	include_spip("inc/config");
	switch ($mode){
		case 'mobileview':
			$valeurs['_width'] = lire_config("adaptive_images/max_width_mobile_version",320);
			break;
		default:
			$valeurs['_width'] = 0;
			break;
	}
	$valeurs['_width_hr'] = 2*$valeurs['_width'];
}
