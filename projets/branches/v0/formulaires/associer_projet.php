<?php
	if (!defined("_ECRIRE_INC_VERSION")) return;

	function formulaires_associer_projets_charger_dist($objet, $id_objet,$type=''){
		$valeurs = array();
		$valeurs['objet'] = $objet;
		$valeurs['id_objet'] = $id_objet;
		$valeurs['id_projet'] = sql_getfetsel('id_projet','spip_projets_liens','objet='.sql_quote($objet).' AND id_objet='.intval($id_objet));
		return $valeurs;
	}
	function formulaires_associer_projets_verifier_dist($objet, $id_objet,$type=''){
		$erreurs = array();
		return $erreurs;
	}
	function formulaires_associer_projets_traiter_dist($objet, $id_objet,$type=''){
		$id_projet = _request('id_projet');
		spip_log("id_projet=$id_projet");
		$associer_projet = charger_fonction('associer_projet','action');
		$associer_projet($id_projet,$objet,$id_objet,$type);
		return array('message_ok'=>_T('projets:message_objet_associe'),'editable'=> true);
	}
?>