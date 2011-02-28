<?php
/**
 * Construction et envoi du mail
 *
 * Utilisation du plugin facteur, si présent, pour envoyer un mail HTML (avec version texte initiale
 * jointe).
 * @name 		FormulaireEnvoi
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package		Tip-a-friend
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('tipafriend_fonctions');

function formulaires_tipafriend_form_charger(){
	$valeurs = array(
		'id' => _request('id'),
		'type' => _request('type'),
		'url' => urldecode(_request('usend')),
		'titre' => utf8_encode(_request('titre')),
		'destinataires' => _request('mdes'),
		'expediteur' => _request('mex'),
		'expediteur_send' => 'non',
		'expediteur_nom' => _request('nex'),
		'message_text' => '',
		'form_reset' => tipafriend_config('form_reset'),
		'header_deja' => _request('header') ? _request('header') : 'oui',
		'taf_css' => _request('taf_css') ? _request('taf_css') : 'oui',
	);
	if(isset($GLOBALS["visiteur_session"]) && $GLOBALS["visiteur_session"]['statut']) {
		if (!strlen($valeurs['expediteur']))
			$valeurs['expediteur'] = $GLOBALS["visiteur_session"]['email'];
		if (!strlen($valeurs['expediteur_nom']))
			$valeurs['expediteur_nom'] = $GLOBALS["visiteur_session"]['nom'];
	}
	if(_TIPAFRIEND_TEST) {
		echo taf_dbg_block(array(
			_T('tipafriend:taftest_param_form') => var_export($valeurs,1)
		));
	}
	return $valeurs;
}

function formulaires_tipafriend_form_verifier(){
	$erreurs = array();
	include_spip('inc/filtres');
	if(!$dest = _request('destinataires')) 
		$erreurs['destinataires'] = _T('tipafriend:error_dest');
	else {
		$m = tipafriend_multimails($dest);
		$ok=true;
		foreach($m as $mail){
			if(!email_valide(trim($mail))) $ok=false;
		}
		if(!$ok) {
			if (count($m)>1)
				$erreurs['destinataires'] = _T('tipafriend:error_one_is_not_mail');
			else $erreurs['destinataires'] = _T('tipafriend:error_not_mail');
		}
	}
	if(!$exp = _request('expediteur')) 
		$erreurs['expediteur'] = _T('tipafriend:error_exp');
	elseif(!email_valide($exp)) 
		$erreurs['expediteur'] = _T('tipafriend:error_not_mail');
	if(!$exp_n = _request('expediteur_nom')) 
		$erreurs['expediteur_nom'] = _T('tipafriend:error_exp_nom');
	return $erreurs;
}

function formulaires_tipafriend_form_traiter(){
	$sep = (_TIPAFRIEND_TEST) ? "<br />" : "\n";
	$id = _request('id');
	$type = _request('type');
	$destinataires = tipafriend_multimails(_request('destinataires'));
	$expediteur_adresse = _request('expediteur');
	$tab_dbg = array();
	$envoyer_mail = charger_fonction('envoyer_mail','inc');

	$doc_url = url_absolue(_request('url'));
	$titre = _request('titre');
	// Si pas le titre de page, on tente de le récupérer à distance ...
	if(!$titre || !strlen($titre)) {
		include_spip('inc/distant');
		$_page_headers = recuperer_infos_distantes($doc_url,0,false);
		if($_page_headers && isset($_page_headers['titre']) && strlen($_page_headers['titre']))
			$titre = $_page_headers['titre'];
		// Sinon, nom du site et basta ...
		else
			$titre = _T('tipafriend:mail_titre_default', array('nom_site'=>$GLOBALS['meta']['nom_site']));
	}

	$mel = tipafriend_contexte();
	$mel['sep'] = $sep;
	$mel['id_rubrique'] = ($type == 'rubrique') ? $id : '';
	$mel['id_article'] = ($type == 'article') ? $id : '';
	$mel['id_breve'] = ($type == 'breve') ? $id : '';
	$mel['id_mot'] = ($type == 'mot') ? $id : '';
	$mel['id_syndic'] = ($type == 'syndic') ? $id : '';
	$mel['id_auteur'] = ($type == 'auteur') ? $id : '';
	$mel['document_titre'] = $titre;
	$mel['document_url'] = $doc_url;
	$mel['destinataires'] = $destinataires;
	$mel['expediteur_nom'] = _request('expediteur_nom');
	$mel['expediteur_adresse'] = $expediteur_adresse;
	$mel['expediteur_message'] = _request('message_text');
	$mel['subject'] = tipafriend_titrage($titre);

	// Recherche du patron ...
	$patron = 'patrons/'.$mel['mail_patron'];
	if(!find_in_path($patron.'.html')) {
		if(_TIPAFRIEND_TEST)
			$tab_dbg[] = _T('tipafriend:taftest_patron_pas_trouve', array('patron'=>$patron));
		spip_log("TIPAFRIEND patron de config utilisateur pas trouve ! ['$patron']");
		$patron = 'patrons/'.str_replace('.html', '', $GLOBALS['TIPAFRIEND_DEFAULTS']['patron']);
	}
	$mel['body'] = recuperer_fond($patron, $mel);

	// Headers
	$header = "X-Originating-IP: ".$GLOBALS['ip']."\n";
	if( $send_exp = _request('expediteur_send') AND $send_exp == 'oui')
		$header .= "Cc: ".$expediteur_adresse."\n";

	// Utilisation du plugin Facteur si présent ...
	if (strlen($mel['mail_patron_html']) && defined('_DIR_PLUGIN_FACTEUR')) {
		$patron_html = 'patrons/'.$mel['mail_patron_html'];
		if(!find_in_path($patron_html.'.html')) {
			if(_TIPAFRIEND_TEST)
				$tab_dbg[] = _T('tipafriend:taftest_patron_pas_trouve', array('patron'=>$patron_html));
			spip_log("TIPAFRIEND patron de config utilisateur pas trouve ! ['$patron_html']");
			$patron_html = 'patrons/'.str_replace('.html', '', $GLOBALS['TIPAFRIEND_DEFAULTS']['patron_html']);
		}
		$html_content = recuperer_fond($patron_html, $mel);
		$mail = $envoyer_mail(
			join(",", $destinataires), $mel['subject'], 
			array(
				'html' => $html_content,
				'texte' => $mel['body'], 
			), $mel['expediteur_adresse'], $header
		);
	} 
	// Sinon fonction de SPIP standard
	else {
		$mail = $envoyer_mail(
			join(",", $destinataires), $mel['subject'], $mel['body'], 
			$mel['expediteur_adresse'], $header
		);
	}

	// Messages
	if($mail!==true)
		$message = array(
			'message_erreur' => _T("tipafriend:message_pas_envoye", array('self'=>_request('self')))
		);
	else
		$message = array('message_ok' => _T("tipafriend:message_envoye"));

	// Debugger
	if(_TIPAFRIEND_TEST) {
		$tab_dbg[_T('tipafriend:taftest_content')] = $sep
			._T('tipafriend:taftest_to')."&nbsp;:&nbsp;".join(" ; ", $mel['destinataires']).$sep
			._T('tipafriend:taftest_from')."&nbsp;:&nbsp;".$mel['expediteur_adresse'].$sep
			._T('tipafriend:taftest_mail_title')."&nbsp;:&nbsp;".$mel['subject'].$sep
			._T('tipafriend:taftest_mail_headers')."&nbsp;:&nbsp;".$header.$sep
			._T('tipafriend:taftest_mail_retour')."&nbsp;:&nbsp;".var_export($mail,true).$sep
			.( isset($html_content) ?
				$sep._T('tipafriend:taftest_mail_content_html')."&nbsp;:&nbsp;("._T('tipafriend:taftest_chargement_patron', array('patron'=>$patron_html)).")"
				."<hr />".$sep.str_replace("\n", '', $html_content).$sep
			: '')
			.$sep._T('tipafriend:taftest_mail_content')."&nbsp;:&nbsp;("._T('tipafriend:taftest_chargement_patron', array('patron'=>$patron)).")"
			."<hr />".$sep.str_replace("\n", '<br />', $mel['body']).$sep;
		if(array_key_exists('message_ok', $message))
			$message['message_ok'] .= $sep.taf_dbg_block($tab_dbg);
		elseif(array_key_exists('message_erreur', $message))
			$message['message_erreur'] .= $sep.taf_dbg_block($tab_dbg);
	}

	// LOG spip
	$infos_log = array(
		"To=[".join(" ; ", $mel['destinataires'])."]",
		"From=[".$mel['expediteur_adresse']."]",
		"Sujet=[".$mel['subject']."]",
		"Envoi=[".var_export($mail,true)."]",
		"ContenuBrut[patron=$patron][".strlen($mel['body'])."cars.]",
	);
	if(isset($html_content))
		$infos_log[] = "ContenuHTML[patron=$patron_html][".strlen($html_content)."cars.]";
	spip_log("Mail via TIPAFRIEND ".(isset($html_content)?"(via Facteur) ":'').join(" ", $infos_log), 'mail');

	return $message;
}
?>