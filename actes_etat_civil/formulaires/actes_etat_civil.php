<?php
// intialisation des variables
function formulaires_actes_etat_civil_charger_dist($mail,$commune){
	include_spip('inc/texte');
	$valeurs = array();
	// pour les étapes du formulaire
	$valeurs['_etapes'] = 4;
	// intialiser types actes
	$valeurs['type_acte'] = 1;
	
	// lien de parente
	$valeurs['lien_parent'] = 0;
	$valeurs['nbre_liens_parent'] = 10;

	// personne sur laquelle porte acte
	$valeurs['personne_nom'] = '';
	$valeurs['personne_prenom'] = '';
	$valeurs['personne_date_evenement'] = '';
	$valeurs['personne_commune_evenement'] = $commune;
	$valeurs['personne_nom_pere'] = '';
	$valeurs['personne_prenom_pere'] = '';
	$valeurs['personne_nom_mere'] = '';
	$valeurs['personne_prenom_mere'] = '';
	$valeurs['personne_absence_pere'] = 0;
	$valeurs['personne_absence_mere'] = 0;
	
	// intialiser demandeur
	$valeurs['demandeur_nom'] = '';
	$valeurs['demandeur_prenom'] = '';
	$valeurs['demandeur_adresse1'] = '';
	$valeurs['demandeur_adresse2'] = '';
	$valeurs['demandeur_adresse3'] = '';
	$valeurs['demandeur_code_postal'] = '';
	$valeurs['demandeur_ville'] = '';
	$valeurs['demandeur_pays'] = 'France';
	$valeurs['demandeur_email'] = '';
	$valeurs['demandeur_telephone'] = '';
	
	// remarque evnetuelle
	$valeurs['remarque'] = '';
	
	return $valeurs;
}

// verification etape 1
function formulaires_actes_etat_civil_verifier_1_dist($mail,$commune){
	$erreurs = array();
	$type_acte = intval(_request('type_acte'));
	if ($type_acte < 0 or $type_acte > 10 ) $erreurs['type_acte'] = _T('etat_civil:selectionner_acte');
	
	$lien_parent = intval(_request('lien_parent'));
	if ($type_acte <= 8){
		if ($lien_parent <= 0 or $lien_parent > 10) $erreurs['lien_parent'] = _T('etat_civil:indiquer_lien_parent');
	} else {
		set_request('lien_parent',0);
	}
	
	return $erreurs;
}

// verification étape 2
function formulaires_actes_etat_civil_verifier_2_dist($mail,$commune){
	include_spip('actes_etat_civil_utils');
	$erreurs = array();
	verifier_input_texte($erreurs, 'personne_nom', 2);
	verifier_input_texte($erreurs, 'personne_prenom', 2);
	verifier_input_date($erreurs, 'personne_date_evenement');
	if (_request(type_acte) <= 8) {
		if (_request('personne_absence_pere') != 1) {
			verifier_input_texte($erreurs, 'personne_nom_pere', 2);
			verifier_input_texte($erreurs, 'personne_prenom_pere', 2);
		}
		if (_request('personne_absence_mere') != 1) {
			verifier_input_texte($erreurs, 'personne_nom_mere', 2);
			verifier_input_texte($erreurs, 'personne_prenom_mere', 4);
		}
	}
	
	// si acte concerne la personne qui fait la demande et pour la première présentation du formulaire
	if (_request('lien_parent') == 1 and !_request('demandeur_nom')) {
		set_request('demandeur_nom',_request('personne_nom'));
		set_request('demandeur_prenom',_request('personne_prenom'));
	}
	
	return $erreurs;
}

// verifier etape 3
function formulaires_actes_etat_civil_verifier_3_dist($mail,$commune){
	include_spip('actes_etat_civil_utils');
	$erreurs = array();
	verifier_input_texte($erreurs, 'demandeur_nom', 2);
	verifier_input_texte($erreurs, 'demandeur_prenom', 2);
	verifier_input_texte($erreurs, 'demandeur_adresse2', 8);
	verifier_input_code_tel($erreurs, 'demandeur_code_postal', 5);
	verifier_input_texte($erreurs, 'demandeur_ville', 2);
	verifier_input_texte($erreurs, 'demandeur_pays', 5);
	// email ou téléphone
	if (!_request('demandeur_telephone') and !_request('demandeur_email')) $erreurs['demandeur_email'] = _T('etat_civil:indiquer_mail_ou_tel');
	if (_request('demandeur_telephone')) {
		verifier_input_code_tel($erreurs, 'demandeur_telephone', 10);
		if (_request('demandeur_email')) verifier_input_email($erreurs, 'demandeur_email');
	}
	if (_request('demandeur_email')) {
		verifier_input_email($erreurs, 'demandeur_email');
		if (_request('demandeur_telephone')) verifier_input_code_tel($erreurs, 'demandeur_telephone', 10);
	}
	
	return $erreurs;
}

// verifier etape 4 previsu
function formulaires_actes_etat_civil_verifier_4_dist($mail,$commune){
	include_spip('actes_etat_civil_utils');
	$erreurs = array();
	
	return $erreurs;
	// passage au traitement
}

// traiter
function formulaires_actes_etat_civil_traiter_dist($mail,$commune){
	// pour les envois multiples
	$mail = str_replace(' ','',$mail);
	$mail = str_replace(';', ',', $mail);
	// si on a plusieurs destinataires on prend le premier pour le return-path
	$les_mails = explode(',',$mail);
	$return_adress = $les_mails[0];
	$n = "\n";
	$charset = $GLOBALS['meta']['charset'];
	
	$type_acte = _request('type_acte');
	$sujet = _T('etat_civil:objet_mel_prefix').' '._T('etat_civil:acte_etat_civil_'.$type_acte);
	
	$texte = _T('etat_civil:demande_acte_etat_civil').$n;
	$texte .= _T('etat_civil:acte_etat_civil_'.$type_acte).$n;
	if ($type_acte <= 8) $texte .= _T('etat_civil:par').' '._T('etat_civil:lien_parent_'._request('lien_parent')).$n.$n;
	
	$texte .= _T('etat_civil:personne_concerne_acte').$n;
	$texte .= mb_strtoupper(_request('personne_nom'),$charset).' '._request('personne_prenom').$n;
	if ($type_acte <= 4) $texte .= _T('etat_civil:ne_le');
	elseif ($type_acte <= 8) $texte .= _T('etat_civil:marie_le');
	else $texte .= _T('etat_civil:decede_le');
	$texte .= ' '._request('personne_date_evenement').' - '.$commune.$n;
	if ($type_acte <= 8){
		$texte .= _T('etat_civil:pere').' : ';
		if (_request('personne_absence_pere') == 1) $texte .= _T('etat_civil:absence_filiation_pere').$n;
		else $texte .= mb_strtoupper(_request('personne_nom_pere'),$charset).' '._request('personne_prenom_pere').$n;
		$texte .= _T('etat_civil:mere').' : ';
		if (_request('personne_absence_mere') == 1) $texte .= _T('etat_civil:absence_filiation_mere').$n;
		else $texte .= mb_strtoupper(_request('personne_nom_mere'),$charset).' '._request('personne_prenom_mere').$n;
	}
	
	$texte .= $n._T('etat_civil:demandeur').$n;
	$texte .= mb_strtoupper(_request('demandeur_nom'),$charset).' '._request('demandeur_prenom').$n;
	if (_request('demandeur_adresse1'))
		$texte .= _request('demandeur_adresse1').$n;
	$texte .= _request('demandeur_adresse2').$n;
	if (_request('demandeur_adresse3'))
		$texte .= _request('demandeur_adresse3').$n;
	$texte .= _request('demandeur_code_postal').' '._request('demandeur_ville').' '._request('demandeur_pays').$n;
	if (_request('demandeur_email'))
		$texte .= _T('etat_civil:email').' : '._request('demandeur_email').$n;
	if (_request('demandeur_telephone'))
		$texte .= _T('etat_civil:telephone').' : '._request('demandeur_telephone').$n;
	
	if (_request('remarque'))
		$texte .= $n._T('etat_civil:remarque').' : '._request('remarque').$n;
	
	if (_request('demandeur_email')) {
		$texte .= $n.'--'.$n._T('etat_civil:generer_accuse_reception').$n;
		$confirme_objet = rawurlencode(_T('etat_civil:confirmation_demande_objet').' : '._T('etat_civil:acte_etat_civil_'.$type_acte));
		$confirme_body = rawurlencode(_T('etat_civil:confirmation_demande_body'));
		$texte .= 'mailto:'._request('demandeur_email').'?subject='.$confirme_objet.'&body='.$confirme_body.$n;
	}
	$texte .= $n.'--'.$n._T('etat_civil:envoi_via')." ".supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site'])).' ('.$GLOBALS['meta']['adresse_site'].')';
	
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($mail, $sujet, $texte, $adres,'X-Originating-IP: '.$GLOBALS['ip'].$n.'Return-Path: -f'.$return_adress);		
		
	$message = _T('etat_civil:message_envoye').'<br /><a href="'._request('action').'">'._T('etat_civil:termine').'</a>';

	// pour debug return array('message_ok' => $mail.$n.$sujet.$n.$texte.$n.$return_adress.$n.$message);
	return array('message_ok' => $message);
}

?>