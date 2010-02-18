<?php
function formulaires_sauvegarder_savecfg_charger() {
	$fond = _request('cfg');
	$valeurs = array(
		'nom' => $fond,
	);
	return $valeurs;
}
function formulaires_sauvegarder_savecfg_verifier(){
	$erreurs = array();
	if (strlen(_request('titre')) < 1)
		$erreurs['message_erreur'] = _T('spip:info_obligatoire');
	return $erreurs;
}
function formulaires_sauvegarder_savecfg_traiter() {
	$message = sauvegarder_savecfg(_request('fond'), _request('titre'));
	return $message;
}
function sauvegarder_savecfg($fond, $titre) {
	if (sql_countsel('spip_meta', 'nom='.sql_quote($fond)) == 1) {
		$sfg = sql_getfetsel('valeur', 'spip_meta', 'nom='.sql_quote($fond));
		// Insert ou Update ?
		$id_savecfg = sql_getfetsel('id_savecfg', 'spip_savecfg', 'titre='.sql_quote($titre).' AND fond='.sql_quote($fond));
		if ($id_savecfg > 0) { // Update
			sql_updateq('spip_savecfg', array( 'valeur' => $sfg, 'date' => date('Y-m-d H:m:s')), 'id_savecfg='.$id_savecfg);
			return _T('savecfg:miseajour_ok',array('titre'=>$titre));
		} else { // Insert
			sql_insertq('spip_savecfg', array('id_savecfg' => '', 'fond' => $fond, 'valeur' => $sfg, 'titre' => $titre, 'date' => date('Y-m-d H:m:s')));
			return _T('savecfg:sauvegarde_ok',array('titre'=>$titre));
		}
	}
	return _T('savecfg:sauvegarde_pas_ok');
}
?>