<?php
/**
 * SPIP.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational (http://www.erational.org)
 *
 * © 2007-2013 - Distribue sous licence GNU/GPL
 * 
 * Formulaire d'ajout de tags
 * 
 * @package SPIP\SPIPicious\Formulaires
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement du formulaire d'ajout/suppression de tags
 * 
 * @param int id_objet
 * 		L'identifiant numérique de l'objet à tagger
 * @param string $type
 * 		Le type d'objet à tagger
 * @param string $retour
 * 		Une URL de retour
 * @return array $valeurs
 * 		Les valeurs chargées dans le formulaire
 */
function formulaires_spipicious_ajax_charger($id_objet,$type='article',$retour='') {
	include_spip('inc/autoriser');
	if(!autoriser('tagger_spipicious',$type,$id_objet))
		return array('editable'=> false);

	$id_type = id_table_objet($type);
	
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	$id_groupe = lire_config('spipicious/groupe_mot');
	$valeurs = array('type'=>$type,'type'=>$type,'id_objet'=>$id_objet,'spipicious_groupe'=>$id_groupe);
	return $valeurs;
}

/**
 * Traitement du formulaire d'ajout/suppression de tags
 * 
 * @param int id_objet
 * 		L'identifiant numérique de l'objet à tagger
 * @param string $type
 * 		Le type d'objet à tagger
 * @param string $retour
 * 		Une URL de retour
 * @return array
 * 		Le tableau de tous les CVT avec editable et message
 */
function formulaires_spipicious_ajax_traiter($id_objet,$type,$retour='') {
	$add_tags = _request('add_tags');
	$remove_tag = _request('remove_tags');
	$spipicious_tags = _request('spipicious_tags');

	if (is_array($remove_tag)) {
		$supprimer_tags = charger_fonction('spipicious_supprimer_tags','action');
		list($message,$invalider,$err) = $supprimer_tags();
	}

	if((!empty($add_tags)) AND (!empty($spipicious_tags))){
		$ajouter_tags = charger_fonction('spipicious_ajouter_tags','action');
		list($message,$invalider,$err) = $ajouter_tags();
	}

	if($invalider){
		include_spip("inc/invalideur");
		suivre_invalideur("1");
	}

	if ($retour) {
		include_spip('inc/headers');
		$message .= redirige_formulaire($retour);
	}
	
	return array('editable'=>true,'message'=>$message);
}
?>