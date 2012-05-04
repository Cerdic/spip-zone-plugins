<?php
/**
 * @name 		Editer pub
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Formulaires
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_editer_publicite_charger_dist($id_publicite='new', $retour=''){
	if ($id_publicite=='0') $id_publicite='new';
	$valeurs = array(
		'id_publicite' => $id_publicite,
		'statut' => '1inactif',
		'titre' => '',
		'url' => $GLOBALS['meta']['adresse_site'].'/',
		'blank' => 'oui',
		'url_optionnel' => defined('_DIR_PUBLIC_PUBBAN') && _PUBBAN_ADDS ? 'oui' : '',
		'objet' => '',
		'banniere' => _request('id_banniere') ? _request('id_banniere') : array(),
		'illimite' => 'non',
		'affichages_restant' => '',
		'clics_restant' => '',
		'date_debut' => '',
		'date_fin' => '',
		'type' => 'img',
	);
	if (!is_array($valeurs['banniere'])) {
		$valeurs['banniere'] = array($valeurs['banniere']);
	}
	if($id_publicite != 'new') {
		include_spip('inc/publicite');
		$pub = pubban_recuperer_publicite($id_publicite);
		$valeurs = array_merge($valeurs, $pub);
	}
	return $valeurs;
}

function formulaires_editer_publicite_verifier_dist($id_publicite='new', $retour=''){
	$erreurs = array();

	if(!$titre = _request('titre')) 
		$erreurs['titre'] = _T('pubban:erreur_titre');

	if(!$url = _request('url') AND !defined('_PUBBAN_ADDS') ) 
		$erreurs['url'] = _T('pubban:erreur_url');
	elseif(!pubban_UrlOK($url)) {
		if(!$forcer = _request('forcer_url') || $forcer=='oui')
			$erreurs['url'] = _T('pubban:erreur_url_no_response')
				." <input type=\"hidden\" name=\"forcer_url\" value=\"oui\" /> "
				._T('pubban:valider_pour_forcer');
	}

	if(!$objet = _request('objet')) 
		$erreurs['objet'] = _T('pubban:erreur_code');

	$empls = _request('banniere');
	if(!is_array($empls) || !count($empls))
		$erreurs['banniere'] = _T('pubban:pas_banniere_selectionne');

	if(	// illimites
		($ill = _request('illimite') AND $ill == 'oui') OR
		// affichages
		($affs = _request('affichages_restant') AND strlen(trim($affs)) AND is_numeric($affs)) OR
		// clics
		($clics = _request('clics_restant') AND strlen(trim($clics)) AND is_numeric($clics))
	) $droits = true;
	// dates
	elseif($date_deb = _request('date_debut') AND strlen(trim($date_deb))) {
		if($date_fin = _request('date_fin') AND strlen(trim($date_fin))) 
			$droits = true; 
		// erreur si date debut sans date fin
		else $erreurs['droits_dates'] = _T('pubban:manque_date_fin');
	}

	if(!isset($droits) || !$droits)
		$erreurs['droits'] = _T('pubban:reponse_form_def_droits');

//var_export($erreurs); exit;
	return $erreurs;
}

function formulaires_editer_publicite_traiter_dist($id_publicite='new', $retour=''){
	if ($id_publicite=='0') $id_publicite='new';
	include_spip('inc/banniere');
	$empls = _request('banniere');

	// verification de l'objet : son extension ?
	$objet = _request('objet');
	$ext = strtolower(pubban_extension($objet));
	$images_extensions = array( 'png', 'gif', 'jpg', 'jpeg', 'bmp' ); // lowercase
	$type = (in_array($ext, $images_extensions) ? 'img' : ( ($ext == 'swf') ? 'swf' : 'flash'));

	$datas = array( 
		'titre' => _request('titre'),
		'url' => _request('url'),
		'blank' => _request('blank'),
		'objet' => $objet,
		'type' => $type,
		'illimite' => ($ill = _request('illimite') AND $ill == 'oui') ? 'oui' : 'non',
		'date_debut' => _request('date_debut') ? _request('date_debut') : date('Y-m-d'),
		'date_fin' => _request('date_fin') ? _request('date_fin') : '',
		'affichages_restant' => 
			($affs = _request('affichages_restant') AND is_numeric($affs)) ? 
				intval( pubban_transformer_nombre($affs) ) : '',
		'clics_restant' => 
			($clics = _request('clics_restant') AND is_numeric($clics)) ? 
				intval( pubban_transformer_nombre($clics) ) : '',
	);
	include_spip('inc/pubban_process');
	if($id_publicite != 'new' AND $id_publicite != '0') {
		$editer_pub = charger_fonction('editer_publicite', 'inc');
		$ok = $editer_pub($id_publicite, $datas);
	}
	else {
		$instit_pub = charger_fonction('instituer_publicite', 'inc');
		$id_publicite = $instit_pub($datas);
	}
	if($id_publicite) {
		$attacher = charger_fonction('attacher_publicite_bannieres', 'inc');
		$ok = $attacher($id_publicite, $empls);
	}

	if(!pubban_comparer_bannieres($empls)) $message = array(
		'message_ok' => _T('pubban:edit_pub_ok_bannieres_differents')
	);
	else {
		include_spip('inc/headers');
		$retour = generer_url_ecrire("publicite","id_publicite=$id_publicite");
		return( redirige_formulaire($retour) );
	}
	return $message;
}
?>