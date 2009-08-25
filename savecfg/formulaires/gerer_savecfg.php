<?php
function formulaires_gerer_savecfg_charger_dist() {
	$valeurs = array(
		'nom' => $nom,
		'fond' => $fond
	);
	return $valeurs;
}
function formulaires_gerer_savecfg_verifier_dist(){
	$erreurs = array();
	if (_request('id_fond') == 'none')
		$erreurs['message_erreur'] = _T('spip:info_obligatoire');
	return $erreurs;
}
function formulaires_gerer_savecfg_traiter_dist() {
	if (_request('_restaurer_')) {
		$message = restaurer_savecfg(_request('id_fond'), _request('fond'));
	}
	if (_request('_supprimer_')) {
		$message = supprimer_savecfg(_request('id_fond'), _request('fond'));
	}
	return $message;
}
function restaurer_savecfg($id_savecfg, $fond) {
	if (sql_countsel('spip_savecfg', 'fond='.sql_quote($fond)) > 0) {
		include_spip('inc/meta');
		$sfg = sql_fetsel(array('titre', 'valeur'), 'spip_savecfg', 'id_savecfg='.sql_quote($id_savecfg));
		ecrire_meta($fond, $sfg['valeur']);
		ecrire_metas();
	}
	return _T('savecfg:savecfg_restauree', array('nom' => $sfg['titre'], 'fond' => $fond));
}
function supprimer_savecfg($id_savecfg, $fond) {
	$nom = sql_getfetsel('titre', 'spip_savecfg', 'id_savecfg='.sql_quote($id_savecfg).' AND fond='.sql_quote($fond));
	sql_delete('spip_savecfg', 'id_savecfg='.sql_quote($id_savecfg));
	return _T('savecfg:savecfg_supprimee', array('nom' => $nom, 'fond' => $fond));
}
?>