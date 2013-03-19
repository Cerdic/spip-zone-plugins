<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_seo_charger($objet, $id_objet, $retour=''){
	$valeurs = array();
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	$metas = sql_select("*", "spip_seo", "id_objet =".intval($id_objet)." AND objet =".sql_quote($objet));
	while($meta = sql_fetch($metas)){
		$valeurs[$meta['meta_name']] = $meta['meta_content'];
	}
	$valeurs['editable'] = true;
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_seo_identifier_dist($objet, $id_objet, $retour=''){
	return serialize(array(intval($id_objet),$objet));
}

function formulaires_editer_seo_verifier($objet, $id_objet, $retour=''){
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_seo_traiter($objet, $id_objet, $retour=''){
	$editer_seo = charger_fonction('editer_seo','action');

	$err = $editer_seo($objet, $id_objet);

	if (!$err)
		return array('message_ok'=>_L('Meta tags mis a jour'));
	else
		return array('message_erreur'=>_L('Vous n\'avez pas le droit de modifier ces meta-tags : '.$err));
}

?>
