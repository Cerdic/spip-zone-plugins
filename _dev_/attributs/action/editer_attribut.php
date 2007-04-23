<?php

function action_editer_attribut_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_attribut n'est pas un nombre, c'est une creation 
	if (!$id_attribut = intval($arg)) {
		$id_attribut = insert_attribut();
	} 

	// Enregistre l'envoi dans la BD
	$err = maj_attribut($id_attribut);

}

function insert_attribut() {

	include_spip('base/abstract_sql');
	$id_attribut = spip_abstract_insert("spip_attributs","(maj)","(NOW())");

	return $id_attribut;
}

function maj_attribut($id_attribut) {
	include_spip('inc/filtres');
	// Ces champs seront pris nom pour nom (_POST[x] => spip_articles.x)
	$champs_normaux = array('titre', 'texte', 'descriptif');

	// ne pas accepter de titre vide
	if (_request('titre') === '')
		$c = set_request('titre', _T('ecrire:info_sans_titre'), $c);

	$titre=_request('titre');

	$champs = array();
	foreach ($champs_normaux as $champ) {
		$val = _request($champ, $c);
		if ($val !== NULL)
			$champs[$champ] = corriger_caracteres($val);
	}

	$champs_checkbox = array('articles', 'rubriques', 'breves', 'auteurs', 'syndic', 'redacteurs');
	foreach ($champs_checkbox as $champ) {
		$val = (_request($champ, $c)=='oui')?'oui':'non';
		$champs[$champ] = $val;
	}

	$update = array();
	foreach ($champs as $champ => $val)
		$update[] = $champ . '=' . _q($val);

	if (!count($update)) return;

	spip_query("UPDATE spip_attributs SET ".join(', ', $update)." WHERE id_attribut=$id_attribut");

}


?>