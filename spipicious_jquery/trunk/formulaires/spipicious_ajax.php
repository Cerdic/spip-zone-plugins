<?php

/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational
 *
 * © 2007-2012 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function formulaires_spipicious_ajax_charger($id_objet,$type='article',$retour='') {
	include_spip('inc/autoriser');
	if(!autoriser('tagger_spipicious',$type,$id_objet)){
		return array('editable'=> false);
	}

	$id_type = id_table_objet($type);
	$id_groupe = lire_config('spipicious/groupe_mot');
	$valeurs = array('type'=>$type,'type'=>$type,'id_objet'=>$id_objet,'spipicious_groupe'=>$id_groupe);
	return $valeurs;
}

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
		include_spip ("inc/invalideur");
		suivre_invalideur("1");
	}

	if ($retour) {
		include_spip('inc/headers');
		$message .= redirige_formulaire($retour);
	}
	
	return array('editable'=>true,'message'=>$message);
}
?>