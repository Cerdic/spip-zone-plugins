<?php
function formulaires_clevermail_post_edit_charger_dist($pst_id = -1) {
	if ($pst_id == -1 || !$valeurs = sql_fetsel('*', 'spip_cm_posts', 'pst_id='.intval($pst_id))) {
		$valeurs = array(
			'pst_id' => -1,
			'pst_subject' => '',
			'pst_html' => '',
			'pst_text' => ''
		);
	}
	return $valeurs;
}

function formulaires_clevermail_post_edit_verifier_dist($pst_id = -1) {
	$erreurs = array();
	foreach(array('pst_subject', 'pst_html', 'pst_text') as $obligatoire) {
		if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = _T('clevermail:ce_champ_est_obligatoire');
		}
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('clevermail:veuillez_corriger_votre_saisie');
	}
	return $erreurs;
}

function formulaires_clevermail_post_edit_traiter_dist($pst_id = -1) {
  $champs = array(
    'pst_subject' => _request('pst_subject'),
    'pst_html' => _request('pst_html'),
    'pst_text' => _request('pst_text')
  );
  $lst_id = sql_getfetsel("lst_id", "spip_cm_posts", "pst_id=".intval(_request('pst_id')));
  $lst_name = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".intval($lst_id));
  sql_updateq('spip_cm_posts', $champs, "pst_id = ".intval(_request('pst_id')));
  spip_log('Modification du message « '._request('pst_subject').' » (id = '._request('pst_id').') de la liste « '.$lst_name.' » (id = '.$lst_id.')', 'clevermail');

 	return array('message_ok' => 'ok', 'redirect' => generer_url_ecrire('clevermail_posts', 'lst_id='.$lst_id));
}
?>