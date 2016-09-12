<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_rediriger_rubrique_charger_dist($id_rubrique, $retour = '') {
	include_spip('inc/autoriser');
	if (!autoriser('modifier', 'rubrique', $id_rubrique)) {
		return false;
	}

	$row = sql_fetsel('id_rubrique,virtuel', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique));
	if (!$row['id_rubrique']) {
		return false;
	}
	include_spip('inc/lien');
	$redirection = virtuel_redirige($row['virtuel']);

	include_spip('inc/texte');
	$valeurs = array(
		'redirection'=>$redirection,
		'id'=>$id_rubrique,
		'_afficher_url' => ($redirection?propre("[->$redirection]"):''),
		);
	return $valeurs;
}

function formulaires_rediriger_rubrique_verifier_dist($id_rubrique, $retour = '') {
	$erreurs = array();

	if (($redirection = _request('redirection')) == $id_rubrique || $redirection == 'rub'.$id_rubrique) {
		$erreurs['redirection'] = _T('info_redirection_boucle');
	}

	return $erreurs;
}

function formulaires_rediriger_rubrique_traiter_dist($id_rubrique, $retour = '') {

	$url = preg_replace(',^\s*https?://$,i', '', rtrim(_request('redirection')));
	if ($url) {
		$url = corriger_caracteres($url);
	}

	include_spip('action/editer_rubrique');
	rubrique_modifier($id_rubrique, array('virtuel'=>$url));
	sql_updateq('spip_rubriques', array('statut'=>'publie'), 'id_rubrique='.$id_rubrique);
	$js = '';
	if (_AJAX) {
		$js = '
			<script type="text/javascript">
				if (window.ajaxReload) $("#rubrique_virtuelle").ajaxReload({args:{virtuel:"'.$url.'"}});
				ajaxReload("navigation");
			</script>';
	}

	return array(
		'message_ok' => ($url ? _T('info_redirection_activee'):_T('info_redirection_desactivee')).$js,
		'editable' => true
	);
}
