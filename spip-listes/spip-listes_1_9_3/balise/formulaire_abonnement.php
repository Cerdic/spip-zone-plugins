<?php
/**
 * Balise: #FORMULAIRE_ABONNEMENT
 * 
 * Si inscriptions pas autorisees, retourner une chaine d'avertissement
 * Sinon inclusion du squelette
 * Si pas de nom ou pas de mail valide, premier appel rien d'autre a faire
 * Autrement 2e appel, envoyer un mail et le squelette ne produira pas de
 * formulaire.
 * @see formulaire_abonnement.html
 * 		qui est un formulaire d'exemple d'utilisation
 * 		de la balise.
 * @see formulaires/formulaire_abonnement.html
 * 		qui est le formulaire utilisé par cette balise
 * 		pour calculer le contenu du formulaire
 * 		d'abonnement ou de gestion, à l'aide du contexte.
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if(!defined('_ECRIRE_INC_VERSION')) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_courrier');
include_once(_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_mail.inc.php');

// Balise independante du contexte

function balise_FORMULAIRE_ABONNEMENT ($p) {

	spiplistes_debug_log('balise_FORMULAIRE_ABONNEMENT()');

	return(calculer_balise_dynamique($p, 'FORMULAIRE_ABONNEMENT', array('id_liste')));
}

/**
 * args[0] indique une liste
 * args[1] indique un eventuel squelette alternatif
 * [(#FORMULAIRE_ABONNEMENT{mon_squelette})]
 * un cas particulier est :
 * [(#FORMULAIRE_ABONNEMENT{listeX})]
 * qui permet d'afficher le formulaire d'abonnement a la liste numero X
 * @param array $args
 * @param $filtres
 * @return array
 */
function balise_FORMULAIRE_ABONNEMENT_stat($args, $filtres) {

	preg_match_all('/liste([0-9]+)/x', $args[1], $matches);
	if($id_liste = intval($matches[1][0])) {
		$args[0]=$id_liste;
	}
	$args[1]='formulaire_abonnement';
	
	return(array($args[0],$args[1]));
}


function balise_FORMULAIRE_ABONNEMENT_dyn($id_liste, $formulaire) {

	//spiplistes_debug_log("balise_FORMULAIRE_ABONNEMENT_dyn() -$id_liste-");

	include_spip ('inc/meta');
	include_spip ('inc/session');
	include_spip ('inc/filtres');
	include_spip ('inc/texte');
	include_spip ('inc/meta');
	include_spip ('inc/mail');
	include_spip ('inc/acces');
	include_once (_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_mail.inc.php');

		
	//recuperation des variables utiles
	$oubli_pass = _request('oubli_pass');
	$email_oubli = _request('email_oubli');
	$type = _request('type');
	
	// recuperation de la config SPIP-Listes
	// 'simple' = ne renvoie que la confirmation d'abonnement
	// 'membre' = complete le mail par un mot de passe pour s'identifier sur le site
	$acces_membres = ($GLOBALS['meta']['abonnement_config'] == 'membre') ? 'oui' : 'non';
		
	// aller chercher le formulaire html qui va bien				
	$formulaire = 'formulaires/'.$formulaire ;		
			
	/**
	 * Accepter l'inscription en tant qu'auteur ?
	 * pour memo: l'auteur a acces a l'espace prive'
	 */
	$accepter_inscrire_auteur = ($GLOBALS['meta']['accepter_inscriptions'] == 'oui');
	/**
	 * Accepter l'inscription en tant que visiteur ?
	 * pour memo: le visiteur n'a pas acces a l'espace prive'
	 */
	$accepter_inscrire_visiteur = ($GLOBALS['meta']['accepter_visiteurs'] == 'oui');
	
	$affiche_formulaire = $inscription_redacteur = $inscription_visiteur = '';
	
	$nom_site_spip = spiplistes_nom_site_texte ($lang);
	$adresse_site = $GLOBALS['meta']['adresse_site'];
	
	// envoyer le cookie de relance mot de passe si pass oublie
	/**
	 * @todo 25/07/2011: bloc email_oubli non fonctionnel ? A vérifier.
	 */
	if($email_oubli)
	{
		if(email_valide($email_oubli))
		{
			$row = spiplistes_auteurs_auteur_select('id_auteur,statut'
													, 'email='.sql_quote($email_oubli)
													);
			
			if($row /* = sql_fetch($sql_result) */ )
			{
				if($row['statut'] == '5poubelle')
				{
					$erreur = _T('pass_erreur_acces_refuse');
				}
				else if($id_abo = intval($row['id_auteur']))
				{
					$cookie = creer_uniqid();
					spiplistes_auteurs_cookie_oubli_updateq($cookie, $email_oubli);
					
					$message = _T('spiplistes:abonnement_mail_passcookie'
								, array(
									'nom_site_spip' => $nom_site_spip
									, 'adresse_site' => $adresse_site
									, 'cookie' => $cookie)
								);
				
					$objet = "[$nom_site_spip] " . _T('pass_oubli_mot');
					$patron = spiplistes_patron_message();
					$format = spiplistes_format_abo_demande($id_abo);
					$contexte = array('format' => $format);
					list ($message_html, $message_texte) = spiplistes_preparer_message (
						$objet
						, $patron
						, $contexte
						);
					
					if(spiplistes_envoyer_mail($email_oubli
											   , $objet
											   , $email_a_envoyer
											   ))
					{
						$erreur = _T('pass_recevoir_mail');
					}
					else
					{
						$erreur = _T('pass_erreur_probleme_technique');
					}
				}
			}
			else
			{
				$erreur = _T('pass_erreur_non_enregistre'
							 , array('email_oubli' => htmlspecialchars($email_oubli))
							 );
			}
		}
		else
		{
			$erreur = _T('pass_erreur_non_valide'
						 , array('email_oubli' => htmlspecialchars($email_oubli))
						 );
		}
	} // end if $email_oubli
	
	// afficher le formulaire d'oubli du pass
	if($oubli_pass == 'oui') {
		return array($formulaire,
					 $GLOBALS['delais'],
			array(
				'oubli_pass' => $oubli_pass
				, 'erreur' => $erreur
				, 'inscription_redac' => ''
				, 'inscription_visiteur' => ''
				, 'mode_login' => false
				, 'reponse_formulaire' => false
				, 'liste' => ''
			)
		);
	}
	
	/**
	 * S'inscrire en tant qu'auteur ou visiteur
	 */
	else if (
		$accepter_inscrire_auteur 
		|| $accepter_inscrire_visiteur 
		|| ($GLOBALS['meta']['forums_publics'] == 'abo') 
	) {
		$accepter_nouveau = 'oui';
		
		// debut presentation
	
		$inscription_redacteur = 
			($accepter_inscrire_auteur && ($type=='redac')) 
			? 'oui' 
			: 'non'
			;

		$inscription_visiteur = 
			(($type!='redac') && $accepter_inscrire_visiteur && ($acces_membres=='oui')) 
			? 'oui' 
			: 'non'
			;
				
		list($affiche_formulaire
			 , $reponse_formulaire
			 , $mode_modifier
			 , $abonne
			 , $activer_formulaire
			 ) = 
			spiplistes_formulaire_abonnement(
				(($type=='redac') ? 'redac' : 'forum')
				, $acces_membres
				, $formulaire
				, $nom_site_spip
				, $inscription_redacteur
				, $inscription_visiteur
			);
	}
	else {
		spiplistes_debug_log (_T('pass_erreur').' acces visiteurs non autorises');
	}
	
	return array($formulaire, $GLOBALS['delais'],
				array(
					'oubli_pass' => $oubli_pass
					, 'erreur' => $erreur
					, 'inscription_redacteur' => $inscription_redacteur
					//, 'acces_membres' => $acces_membres
					, 'inscription_visiteur' => $inscription_visiteur
					, 'mode_login' => $affiche_formulaire
					, 'message_formulaire' => $message_formulaire
					, 'reponse_formulaire' => $reponse_formulaire
					, 'accepter_auteur' => $GLOBALS['meta']['accepter_inscriptions']
					, 'id_liste' => $id_liste
					, 'accepter_nouveau' => $accepter_nouveau
					, 'mode_modifier' => $mode_modifier
					, 'id_auteur' => $abonne['id_auteur']
					, 'format' => $abonne['format']
					, 'statut_auteur' => $abonne['statut']
					, 'activer_formulaire' => $activer_formulaire
					)
				);
				
				
} // end balise_FORMULAIRE_ABONNEMENT_dyn()


/**
 * Abonnement d'un visiteur ou d'un auteur
 * Si authentifie', modifie l'abonnement,
 * 	sinon envoie mail avec cookie_oubli pour confirmer.
 * Si adresse_mail absent de la base, cree un login
 * 	a partir de l'email et renvoie un mail de confirmation.
 *
 * @param string $type
 * @param string $acces_membres
 * @param string $formulaire
 * @param string $nom_site_spip
 * @param string $inscription_redacteur
 * @param string $inscription_visiteur
 * @return array()
 */
function spiplistes_formulaire_abonnement (
											$type
											, $acces_membres
											, $formulaire
											, $nom_site_spip
											, $inscription_redacteur
											, $inscription_visiteur
											) {
	
	$mail_inscription_ = trim(strtolower(_request('mail_inscription_')));
	$nom_inscription_ = trim(_request('nom_inscription_'));
	$type_abo = _request('suppl_abo') ;
	$listes_demande = _request('list');
	$desabo = ($type_abo == 'non') ? 'oui' : 'non';
	
	$adresse_site = $GLOBALS['meta']['adresse_site'];

	$reponse_formulaire = '';
	$email_a_envoyer = $mode_modifier = $sql_where = false;
	$abonne = array();
	
	/**
	 * Le formulaire est (re) activé
	 * sauf si c'est un retour d'inscription
	 * (qui attend confirmation via mail)
	 */
	$activer_formulaire = 'oui';
	
	/**
	 * La variable d est transmise via URL proposé en pied de mail
	 * du courrier envoyé.
	 * Elle n'est pas utilisée dans le squelette
	 * d'abonnement.
	 */
	$d = _request('d');
	
	if(!empty($d)) {
		$sql_where = array(
				'cookie_oubli='.sql_quote($d)
				, 'statut<>'.sql_quote('5poubelle')
				, 'pass<>'.sql_quote('')
			);
	}
	// ou si identifie'
	else if($connect_id_auteur = intval($GLOBALS['auteur_session']['id_auteur']))
	{
		$sql_where = array("id_auteur=$connect_id_auteur");
	}
	if($sql_where) {
		// cherche les coordonnees de l'abonne'
		$sql_select = 'id_auteur,statut,nom,email,cookie_oubli';
		$sql_result = sql_select(
			$sql_select
			, 'spip_auteurs'
			, $sql_where
			, '', '', 1
		);
		if($row = sql_fetch($sql_result)) {
			foreach(explode(',', $sql_select) as $key) {
				$abonne[$key] = $row[$key];
			}
		}
		$abonne['format'] = spiplistes_format_abo_demande($abonne['id_auteur']);
		
	}
	
	
	
	// si identifie' par cookie ou login... effectuer les modifications demandees
	if(count($abonne)) {
		
		/**
		 * Créer un nouveau cookie pour ce compte
		 * afin de le transmettre par mail
		 * pour lien direct sur le formulaire sans auth.
		 */
		$abonne['cookie_oubli'] = creer_uniqid();
		spiplistes_auteurs_cookie_oubli_updateq(
												$abonne['cookie_oubli'],
												$abonne['email']
												);
		//spiplistes_debug_log ('COOKIE: '.$abonne['cookie_oubli']);
	
		// toujours rester en mode modif pour permettre la correction
		$mode_modifier = 'oui';
		
		if($desabo == 'oui')
		{
			spiplistes_format_abo_modifier($abonne['id_auteur']);
			$reponse_formulaire = _T('spiplistes:vous_etes_desabonne');
			$email_a_envoyer = true;
		}
		
		else if($listes_demande)
		{
			//spiplistes_debug_log("demande modification abonnements listes " . implode(",", $listes_demande));
			
			if(is_array($listes_demande) && count($listes_demande))
			{
				$listes_ajoutees = spiplistes_abonnements_ajouter($abonne['id_auteur']
															, array_map('intval', $listes_demande)
															);
				$curr_abos_auteur = spiplistes_abonnements_listes_auteur($abonne['id_auteur']);
				
				foreach($curr_abos_auteur as $id_liste) {
					if(!in_array($id_liste, $listes_demande)) {
						spiplistes_abonnements_auteur_desabonner($abonne['id_auteur']
																 , $id_liste
																 );
					}
				}
			}
			
			// modifier le format de reception ?
			if(spiplistes_format_valide($type_abo) && ($type_abo != $abonne['format']))
			{
				spiplistes_format_abo_modifier($abonne['id_auteur'], $abonne['format'] = $type_abo);
				//$abonne['ids_abos'] = spiplistes_abonnements_listes_auteur($abonne['id_auteur']);
				
			}
			
			$reponse_formulaire = _T('spiplistes:demande_enregistree_retour_mail');
			$email_a_envoyer = true;
		}
		else if ( isset($abonne['nom']) )
		{
			spiplistes_debug_log('pas de demande, afficher formulaire de modif au complet');
			$reponse_formulaire = ''
				. '<span class="nom">' . $abonne['nom'] . "</span>\n"
				. '<span class="souhait">' . _T('spiplistes:effectuez_modif_validez', array('s'=>$abonne['nom'])). "</span>\n"
				;
		}
		
		$id_abonne = $abonne['id_auteur'];
		$objet_email = _T('spiplistes:votre_abo_listes');
		$contexte = array('titre' => $objet_email);
		
	}
	else // non identifie' ? gestion par cookie_oubli.
	{
		
		$texte_intro = _T('form_forum_message_auto') . '<br /><br />'._T('spiplistes:bonjour') . "<br />\n";
		
		$abonne = array(
			'email' => email_valide($mail_inscription_),
			'cookie_oubli' => creer_uniqid()
			);
		
		if($abonne['email'])
		{
			// si l'abonne existe deja mais pas d'action demandee,
			// affiche formulaire complet
			if ($row = 
				spiplistes_auteurs_auteur_select ('id_auteur,login,nom,statut,lang'
												  , 'email='.sql_quote($abonne['email'])
												  )
			) {
				
				$abonne['id_auteur'] = intval($row['id_auteur']);
				$abonne['statut'] = $row['statut'];
				$abonne['login'] = $row['login'];
				$abonne['nom'] = $row['nom'];
				$abonne['lang'] = $row['lang'];
				$abonne['format'] =
					($f = spiplistes_format_abo_demande($abonne['id_auteur']))
					? $f
					: 'texte'
					;
	
				if($abonne['statut'] == '5poubelle')
				{
					$reponse_formulaire = _T('form_forum_access_refuse');
				}
				// si encore nouveau, c'est qu'il ne s'est jamais identifie'
				else if($abonne['statut'] == 'nouveau')
				{
					// le supprimer. Il sera re-cree plus loin
					spiplistes_auteurs_auteur_delete('id_auteur='.sql_quote($abonne['id_auteur']));
					$abonne['id_auteur'] = false;
				}
				else {
					// demande de modifier l'abonnement ? envoie le cookie de relance par mail
					spiplistes_auteurs_cookie_oubli_updateq($abonne['cookie_oubli']
															, $abonne['email']
															);
					$objet_email = _T('spiplistes:abonnement_titre_mail');
					$texte_email = spiplistes_texte_inventaire_abos($abonne['id_auteur']
																	, $type_abo
																	, $nom_site_spip
																	);
					$contexte = array('titre' => $objet_email);
					$id_abonne = $abonne['id_auteur'];
				}
				
			}
			/**
			 * Si l'adresse mail n'existe pas dans la base,
			 * créer le compte
			 */
			else 
			{
				$activer_formulaire = 'non';
				
				$abonne['login'] = spiplistes_login_from_email($abonne['email']);
				$abonne['nom'] =
					(($acces_membres == 'non') || empty($nom_inscription_))
					? ucfirst($abonne['login'])
					: $nom_inscription_
					;
				
				// ajouter l'abonne
				$pass = creer_pass_aleatoire(8, $abonne['email']);
				$abonne['zepass'] = $pass;
				$abonne['mdpass'] = md5($pass);
				$abonne['htpass'] = generer_htpass($pass);
				
				$abonne['statut'] = ($inscription_redacteur == 'oui') ? 'nouveau' : '6forum';
	
				// format d'envoi par defaut pour le premier envoi de confirmation
				$abonne['format'] = spiplistes_format_abo_default();
				
				/**
				 * creation du compte
				 */
				if($id_abonne = spiplistes_auteurs_auteur_insertq(
						array(
							'nom' => $abonne['nom']
							, 'email' => $abonne['email']
							, 'login' => $abonne['login']
							, 'pass' => $abonne['mdpass']
							, 'statut' => $abonne['statut']
							, 'htpass' => $abonne['htpass']
							, 'cookie_oubli' => $abonne['cookie_oubli']
							)
					)) {
					// creation .htpasswd & LDAP si besoin systeme
					ecrire_acces();
					
					// premier format de reception par defaut
					spiplistes_format_abo_modifier($id_abonne, $abonne['format']);
				}

				$objet_email = _T('spiplistes:confirmation_inscription');
				
				$contexte = array(
								'titre' => $objet_email
								, 'nouvel_inscription' => 'oui'
								, 'inscription_redacteur' => $inscription_redacteur
								, 'inscription_visiteur' => $inscription_visiteur
							);
			}
			
			$email_a_envoyer = true;
			
		}
		else if(!empty($mail_inscription_)) {
			//Non email o non valida
			return(array(true, _T('spiplistes:erreur_adresse'), $mode_modifier, false));
		}
	}
	
	if($id_abonne && $email_a_envoyer) {
		
		$abonne['ids_abos'] = spiplistes_abonnements_listes_auteur($abonne['id_auteur']);

		$abonne['format'] = spiplistes_format_valide($abonne['format']);
		
		list ($message_html, $message_texte) = spiplistes_preparer_message(
					($objet_email = "[$nom_site_spip] " . $objet_email)
					, spiplistes_patron_message()
					, array_merge($contexte, $abonne)
					);
		if(
			spiplistes_envoyer_mail (
				$abonne['email']
				, $objet_email
				, array ('html' => $message_html, 'texte' => $message_texte)
				, false
				, ''
				, $abonne['format']
			)
		) {
			$reponse_formulaire =
				($acces_membres == 'oui')
				? _T('form_forum_identifiant_mail')
				: _T('spiplistes:demande_enregistree_retour_mail')
				;
		}
		else {
			$reponse_formulaire = _T('form_forum_probleme_mail');
		}
	}
	
	$result = array(
		TRUE,
		$reponse_formulaire,
		$mode_modifier,
		$abonne,
		$activer_formulaire
	);

	return($result);
} // end spiplistes_formulaire_abonnement()


/**
 * Petit message texte - inventaire des abonnements
 * @return string
 */
function spiplistes_texte_inventaire_abos ($id_abonne, $type_abo, $nom_site_spip) {
	
	// fait l'inventaire des abos
	$listes_abonnements = spiplistes_abonnements_listes_auteur ($id_abonne, true);
	$nb = count($listes_abonnements);
	$message_list = 
		($nb)
		? "\n- " . implode("\n- ", $listes_abonnements) . ".\n"
		: ''
		;

	$m1 = ($nb > 1) ? 'inscription_reponses_s' : 'inscription_reponse_s';
	if($nb > 1) {
		$m2 = _T('spiplistes:inscription_listes_f', array('f' => $type_abo));
	} else if($nb == 1) {
		$m2 = _T('spiplistes:inscription_liste_f', array('f' => $type_abo));
	} else {
		$m2 = _T('spiplistes:vous_abonne_aucune_liste');
	}
	$texte = ''
		. "\n"._T('spiplistes:'.$m1, array('s' => htmlentities($nom_site_spip)))
		. ".\n"
		. $m2.$message_list
		;
	return($texte);
}
