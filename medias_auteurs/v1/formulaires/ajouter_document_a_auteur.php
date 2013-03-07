<?php
/**
 * Media auteurs
 *
 * Copyright (c) 2012
 * Yohann Prigent
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 * Pour plus de details voir le fichier COPYING.txt.
 *  
 **/
function formulaires_ajouter_document_a_auteur_charger() {
	$values = array('choix_auteur' => $auteur, 'id_document' => _request('id_document'));
	return $values;
}
function formulaires_ajouter_document_a_auteur_verifier() {
	$values = array('inp_aut' => _request('inp_aut'), 'sel_aut' => _request('sel_aut'));
	if ($values['inp_aut']) {
		if (!is_numeric($values['inp_aut']))
			$erreurs['inp_aut'] = _T('media_aut:chiffres_seulement');
		$res = sql_select('nom', 'spip_auteurs', 'id_auteur='.$values['inp_aut']);
		if ($res and sql_count($res)==0)
			$erreurs['inp_aut'] = _T('media_aut:id_not_found');
	}
	return $erreurs;
}
function formulaires_ajouter_document_a_auteur_traiter() {
	$values = array('inp_aut' => _request('inp_aut'), 'sel_aut' => _request('sel_aut'));
	$id_aut = $values['inp_aut'] ? $values['inp_aut'] : $values['sel_aut'];
	sql_insertq('spip_documents_liens', array(
		'id_document' => _request('id_document'),
		'id_objet' => $id_aut,
		'objet' => 'auteur',
		'vu' => 'non'
	));
	sql_updateq('spip_documents', array('statut' => 'publie'), 'id_document='._request('id_document'));
	return _T('media_aut:liaison_enregistree');
}
?>