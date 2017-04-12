<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_commenter_relecture_charger_dist($element, $id_relecture, $redirect='') {
	$valeurs = array();

	if ($id = intval($id_relecture)) {
		$from = 'spip_relectures';
		$where = array("id_relecture=$id");
		$texte = sql_getfetsel("article_$element", $from, $where);

		$valeurs = array(
			'id_relecture' => $id_relecture,
			'element' => $element,
			'texte_element' => $texte);
	}

	return $valeurs;
}

function formulaires_commenter_relecture_verifier_dist($element, $id_relecture='oui', $redirect='') {
	$erreurs = array();
	return $erreurs;
}

// http://code.spip.net/@inc_editer_article_dist
function formulaires_commenter_relecture_traiter_dist($element, $id_relecture='oui', $redirect='') {
	$messages = array();
	return $messages;
}

?>