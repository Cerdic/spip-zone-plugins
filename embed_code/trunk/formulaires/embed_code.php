<?php
/**
 * MediaSPIP player
 * Lecteur multimédia HTML5 pour MediaSPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 * 
 * Formulaire dynamique #FORMULAIRE_EMBED_CODE
 * Crée le code d'embed d'une video, d'un son
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');
/**
 * Chargement des valeurs par defaut des champs du formulaire
 *
 * @param int $id_objet : L'identifiant numérique de l'objet
 * @param string $objet : Le type d'objet
 *
 */
function formulaires_embed_code_charger_dist($id_objet=null, $objet='document'){
	$valeurs['id_objet'] = $id_objet;
	$valeurs['objet'] = $objet;
	$valeurs['largeur'] = lire_config('embed_code/embed_largeur',480);
	$valeurs['hauteur'] = lire_config('embed_code/embed_hauteur',360);
	if(!intval($id_objet))
		return false;
	
	if ($objet == 'document')
		$infos_doc = sql_fetsel('hauteur,largeur,extension','spip_documents','id_document='.intval($id_objet));
	
	if($infos_doc && ($infos_doc['hauteur'] > 0) && ($infos_doc['largeur'] > 0)){
		$valeurs['ratio'] = $infos_doc['largeur']/$infos_doc['hauteur'];
		$valeurs['hauteur_ratio'] = $valeurs['hauteur'] = floor($valeurs['largeur']/$valeurs['ratio']);
	}else{
		$valeurs['hauteur_ratio'] = $valeurs['hauteur'];
	}
	
	return $valeurs;
}

/**
 * Vérifications du formulaires
 * 
 * @param int $id_objet : L'identifiant numérique de l'objet
 * @param string $objet : Le type d'objet
 *
 */
function formulaires_embed_code_verifier_dist($id_objet=null, $objet='document'){
	$numeriques = array('largeur','hauteur');
	foreach($numeriques as $numerique){
		if(_request($numerique) && !ctype_digit(_request($numerique))){
			if($numerique != 'largeur' OR !preg_match('/%/',_request($numerique)))
				$erreurs[$numerique] = _T('mediaspip_player:erreur_valeur_int');
		}
		if(!$erreurs[$numerique] && _request($numerique) && (_request($numerique) > 2000)){
			$erreurs[$numerique] = _T('mediaspip_player:erreur_valeur_int_inf',array('nb'=>'2000'));
		}
	}
	if(!$erreurs['largeur'] && _request('largeur') && !preg_match('/%/',_request('largeur')) && (_request('largeur') < 200)){
		$erreurs['largeur'] = _T('mediaspip_player:erreur_valeur_int_sup',array('nb'=>'200'));
	}
	if(!$erreurs['hauteur'] && _request('hauteur') && (_request('hauteur') < 25))
		$erreurs['hauteur'] = _T('mediaspip_player:erreur_valeur_int_sup',array('nb'=>'24'));

	return $erreurs;
}

/**
 * Traitement du formulaire
 *
 * @param int $id_objet : L'identifiant numérique de l'objet
 * @param string $objet : Le type d'objet
 *
 */
function formulaires_embed_code_traiter_dist($id_objet=null, $objet='document'){
	if ($objet == 'document')
		$infos_doc = sql_fetsel('hauteur,largeur,extension','spip_documents','id_document='.intval($id_objet));
	if($infos_doc && ($infos_doc['hauteur'] > 0) && ($infos_doc['largeur'] > 0)){
		$valeurs['ratio'] = $infos_doc['hauteur']/$infos_doc['largeur'];
	}
	if($infos_doc && ($infos_doc['largeur'] > 0) && ($infos_doc['hauteur'] > 0) && ($largeur = _request('largeur') OR $hauteur = _request('hauteur'))){
		if(intval($largeur) > 0){
			$ratio = $largeur/$infos_doc['largeur'];
			$valeurs['hauteur_ratio'] = floor($infos_doc['hauteur']*$ratio);
			set_request('hauteur_ratio',$valeurs['hauteur_ratio']);
			if(_request('hauteur') && !preg_match('/%/',_request('largeur'))){
				$hauteur = floor($infos_doc['hauteur']*$ratio);
				set_request('hauteur',$hauteur);
			}
		}else if(intval($hauteur) > 0){
			$ratio = $infos_doc['hauteur']/$hauteur;
			$largeur = floor($infos_doc['largeur']/$ratio);
			set_request('ratio',$ratio);
			set_request('hauteur_ratio',$hauteur);
			set_request('largeur',$largeur);
		}
	}
	return $res;
}

?>