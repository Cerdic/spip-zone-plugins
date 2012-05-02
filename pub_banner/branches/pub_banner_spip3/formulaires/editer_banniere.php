<?php
/**
 * @name 		Editer banniere
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Formulaires
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_editer_banniere_charger($id_banniere='new', $retour=''){
	$valeurs = array(
		'titre' => '',
		'titre_id' => '',
		'width' => '',
		'height' => '',
		'ratio_pages' => '',
		'statut' => '1inactif',
	);
	if( $id_banniere != 'new' ) {
		$emp = pubban_recuperer_banniere($id_banniere);
		$valeurs = array_merge($valeurs, $emp);
	}
	return $valeurs;
}

function formulaires_editer_banniere_verifier($id_banniere='new', $retour=''){
	$erreurs = array();
	if(!$titre = _request('titre') OR !strlen($titre)) 
		$erreurs['titre'] = _T('pubban:error_titre_empl');	
	if(!$width = _request('width') OR !$height = _request('height')) 
		$erreurs['dimensions'] = _T('pubban:error_dimensions_missing_empl');	
	elseif(!is_numeric($width) OR !is_numeric($height))
			$erreurs['dimensions'] = _T('pubban:error_dimensions_numeric_empl');
	return $erreurs;
}

function formulaires_editer_banniere_traiter($id_banniere='new', $retour=''){
	include_spip('inc/pubban_process');
	$datas = array(
		'titre' => _request('titre'),
		'titre_id' => pubban_transformer_titre_id(_request('titre_id')),
		'width' => pubban_transformer_nombre( _request('width') ),
		'height' => pubban_transformer_nombre( _request('height') ),
		'ratio_pages' => _request('ratio_pages'),
		'statut' => _request('statut'),
	);
	if (empty($datas['titre_id'])) {
		$datas['titre_id'] = pubban_transformer_titre_id($datas['titre']);
	}
	if($id_banniere == 'new') {
		$instit_empl = charger_fonction('instituer_banniere', 'inc');
		if( $id_banniere = $instit_empl($datas) )
			$redirect = generer_url_ecrire("banniere_voir","id_banniere=$id_banniere");
	}
	else {
		$editer_empl = charger_fonction('editer_banniere', 'inc');
		if ($ok = $editer_empl($id_banniere, $datas))
			$redirect = strlen($retour) ? $retour : generer_url_ecrire("banniere_voir","id_banniere=$id_banniere");
	}
	if($redirect){
		include_spip('inc/headers');
		return( redirige_formulaire($redirect) );
	}
	return array(
		'message_erreur' => _T('pubban:error_global')
	);
}
?>