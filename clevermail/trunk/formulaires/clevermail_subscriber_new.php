<?php
include_spip('inc/clevermail_abonnes');

function formulaires_clevermail_subscriber_new_charger_dist() {
	$valeurs = array(
		'cm_file' => '',
	  'cm_subs' => '',
	  'cm_mode' => 0,
	  'cm_lists' => array()
	);

	return $valeurs;
}

function formulaires_clevermail_subscriber_new_verifier_dist() {
	$erreurs = array();
	if (
	    (!isset($_FILES['cm_file']) || $_FILES['cm_file']['name'] == '' || !is_uploaded_file($_FILES['cm_file']['tmp_name']))
	    && _request('cm_subs') == '') {
    $erreurs['cm_file'] = _T('clevermail:vous_devez_choisir_un_fichier');
    $erreurs['cm_subs'] = _T('clevermail:et_ou_saisir_des_adresses');
	}
  if (isset($_FILES['cm_file']) && is_uploaded_file($_FILES['cm_file']['tmp_name'])) {
    $adresses = implode('', file($_FILES['cm_file']['tmp_name']));
    if (!clevermail_verification_adresses_email($adresses)) {
      $erreurs['cm_file'] = _T('clevermail:le_format_des_adresses_email_ne_semble_pas_bon');
    }
  }
  if (_request('cm_subs') != '' && !clevermail_verification_adresses_email(_request('cm_subs'))) {
    $erreurs['cm_subs'] = _T('clevermail:le_format_des_adresses_email_ne_semble_pas_bon');
	}
	if (sizeof(_request('cm_lists')) == 0) {
    $erreurs['cm_lists'] = _T('clevermail:vous_devez_choisir_au_moins_une_liste');
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('clevermail:veuillez_corriger_votre_saisie');
	}
	return $erreurs;
}

function formulaires_clevermail_subscriber_new_traiter_dist() {
	$adresses = '';
  if (isset($_FILES['cm_file']) && is_uploaded_file($_FILES['cm_file']['tmp_name'])) {
    $adresses .= "\n".implode('', file($_FILES['cm_file']['tmp_name']));
  }
  if (_request('cm_subs')) {
    $adresses .= "\n"._request('cm_subs');
  }
  $retour = clevermail_abonnes_ajout(_request('cm_lists'), intval(_request('cm_mode')), $adresses);

  if ($retour === false) {
  	$msg = _T('clevermail:aucun_nouvel_abonne');
  } else {
	  $msg = $retour['nb_nouv'] > 0 ? $retour['nb_nouv']._T('clevermail:n_nouveaux_abonnes') : _T('clevermail:aucun_nouvel_abonne');
	  $msg .= $retour['nb_nouv'] > 0 && $retour['nb_maj'] > 0 ? _T('clevermail:nouveaux_abonnes_et') : '';
	  $msg .= $retour['nb_maj'] > 0 ? $retour['nb_maj']._T('clevermail:changements_mode_abonnement') : '';
  }
  
  return array('message_ok' => $msg);
}
?>