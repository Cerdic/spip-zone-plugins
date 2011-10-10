<?php
/**
 * Formulaire d'inscription
 * 
 * @since SPIP 2.0
 * @see http://www.spip.net/fr_article3796.html
 * @see formulaires/spip_listes_inscription.html
 * 		qui est le squelette de construction
 * 		utilisé ici
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;
	
include_spip('inc/acces');
include_spip('inc/spiplistes_api');


/**
 * @return array
 */
function formulaires_spip_listes_inscription_charger_dist ($id_liste='')
{
	$valeurs = array(
		'email' => '',
		'id_liste' => $id_liste
	);
	
	return $valeurs;
}

/**
 * @return array
 */
function formulaires_spip_listes_inscription_verifier_dist ($id_liste='')
{
	$erreurs = array();
	
	// verifier que les champs obligatoires sont bien la :
	foreach(array('email') as $obligatoire) {
		if (!_request($obligatoire))
		{
			$erreurs[$obligatoire] = _T('spiplistes:champ_obligatoire');
		}
	}
	
	if (!in_array(_request('format_abo'), array('html','texte')))
	{
		$erreurs['format'] = 'format inconnu';
	}
	
	$listes = _request('listes');
	
	$listes_sel = array();
	
	if (is_array($listes))
	{
		foreach($listes as $liste)
		{
			$id_liste = intval($liste);
			if ($id_liste > 0) 
			{
				$listes_sel[] = $id_liste;
			}
		}
	}

	// verifier que si un email a ete saisi, il est bien valide :
	include_spip('inc/filtres');
	$email = _request('email');
	if ($email && !email_valide($email)) 
	{
		$erreurs['email'] = _T('spiplistes:cet_email_pas_valide');
	}
	
	//// Verifier si le mail est deja connu
	//if (email_valide(_request('email'))) {
	//	if (sql_getfetsel('id_auteur','spip_auteurs',"id_auteur !='".intval($id_auteur)."' AND email = '$email'")) {
	//		$erreurs['email'] = _T('spiplistes:cet_email_deja_enregistre');
	//	}
	//}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('spiplistes:saisie_erreurs');
	}
	
    return ($erreurs); // si c'est vide, traiter sera appele, sinon le formulaire sera re-soumis
}

/**
 * Traite les donnees du formulaire de saisie
 * - valide l'adresse mail
 * - l'enregistre si manquant
 * - l'abonne aux listes souhaitees
 * - envoie un mail de confirmation
 * @return array
 */
function formulaires_spip_listes_inscription_traiter_dist ($id_liste = '') {
	
	/**
	 * Un abonné doit etre enregistre
	 * dans spip_auteurs,
	 * spip_auteurs_elargis, (historique, pour le format de réception)
	 * spip_auteurs_listes (table des abonnements)
	 */
	
	include_spip('inc/spiplistes_api_courrier');
	
	$val['email'] = _request('email');
	$val['email'] = email_valide ($val['email']);
	
	if ($val['email'])
	{
		$val['nom'] = _request('email');
		$val['lang'] = _request('lang');
		if (!$val['lang']) {
			$val['lang'] = $GLOBALS['meta']['langue_site'];
		}
		$val['alea_actuel'] = creer_uniqid();
		$val['alea_futur'] = creer_uniqid();
		$val['low_sec'] = '';
		$val['statut'] = 'aconfirmer';
		
		$format = _request('format_abo');
		if (!$format) {
			$format = spiplistes_format_abo_default ();
		}
		$listes = _request('listes');
	}
	
	/**
	 * Verifier si l'auteur existe deja,
	 */
	if ($val['email'])
	{
		$auteur = spiplistes_auteurs_auteur_select('id_auteur,nom,statut,lang'
											   , 'email='.sql_quote($val['email'])
					);
	}
	
	if ($auteur)
	{
		/**
		 * Si le compte existe, le reactivier
		 */
		$contexte['id_auteur'] = $id_auteur = $auteur['id_auteur'];
		if ($auteur['statut'] == '5poubelle')
		{
			$new_statut = 'aconfirmer';
			spiplistes_auteurs_auteur_statut_modifier ($id_auteur, $new_statut);
			$auteur['statut'] = $new_statut;
		}
		$contexte['nouvel_inscription'] = 'non';
		$contexte['nom'] = $auteur['nom'];
		$contexte['statut'] = $auteur['statut'];
		$contexte['lang'] = ($auteur['lang'])
							? $auteur['lang']
							: $GLOBALS['meta']['langue_site']
							;
		spiplistes_abonnements_auteur_desabonner ($id_auteur, 'toutes');
		
		spiplistes_debug_log ('inscription auteur #'.$id_auteur.' email:'.$val['email']);
	}
	else
	{
		/**
		 * Si le compte n'existe pas, le créer
		 */
		if ($id_auteur = spiplistes_auteurs_auteur_insertq ($val))
		{
			spiplistes_format_abo_modifier ($id_auteur, $format);
		}
		$contexte['nouvel_inscription'] = 'oui';
		$contexte['id_auteur'] = $id_auteur;
		$contexte['nom'] = $val['nom'];
		$contexte['statut'] = $val['statut'];
		$contexte['lang'] = $GLOBALS['meta']['langue_site'];

		spiplistes_debug_log ('NEW inscription email:'.$val['email']);
	}
	
	if ($listes && is_array($listes) && count($listes))
	{
		spiplistes_abonnements_ajouter ($id_auteur, $listes);
		$contexte['ids_abos'] = array_values($listes);
	}
	
	/**
	 * Construit le message à partir du patron
	 */
	if ($id_auteur > 0)
	{
		$cur_format = spiplistes_format_abo_demande ($id_auteur);
		if (!$cur_format)
		{
			$cur_format = $format;
			spiplistes_format_abo_modifier ($id_auteur, $format);
		}
		$contexte['format'] = $cur_format;
		$nom_site_spip = spiplistes_nom_site_texte ($lang);
		$email_objet = '['.$nom_site_spip.'] '._T('spiplistes:confirmation_inscription');

		/**
		 * Le cookie pour le lien direct
		 */
		$cookie = creer_uniqid();
		spiplistes_auteurs_cookie_oubli_updateq($cookie, $val['email'], false);
		spiplistes_debug_log ('COOKIE: '.$cookie);
		$contexte['cookie_oubli'] = $cookie;
		
		/**
		* Assemble le patron
		* Obtient en retour le contenu en version html et texte
		*/
		$path_patron = _SPIPLISTES_PATRONS_MESSAGES_DIR . spiplistes_patron_message();
		
		list($courrier_html, $courrier_texte) = spiplistes_courriers_assembler_patron (
			$path_patron
			, $contexte);
		//spiplistes_debug_log ('Messages size: html: '.strlen($courrier_html));
		//spiplistes_debug_log ('Messages size: text: '.strlen($courrier_texte));
		
		$email_contenu = array(
				/**
				 * La version HTML du message
				 */
				'html' => '<html>' . PHP_EOL
					. '<body>' . PHP_EOL
					. $courrier_html
					. '</body>' . PHP_EOL
					. '</html>' . PHP_EOL
				/**
				 * Et la version texte
				 */
				, 'texte' => $courrier_texte
				);
	}
	
	/**
	 * envoyer mail de confirmation
	 */
	if ($id_auteur
		&& spiplistes_envoyer_mail (
			$val['email']
			, $email_objet
			, $email_contenu
			, false
			, ''
			, $format
	   )
	) {
		$contexte = array('message_ok'=>_T('spiplistes:demande_ok'),'editable' => false,);
	}
	else {
		$contexte = array('message_ok'=>_T('spiplistes:demande_ko'),'editable' => false,);
	}
	
	return ($contexte);
}