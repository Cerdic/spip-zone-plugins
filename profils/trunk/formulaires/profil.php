<?php
/**
 * Gestion du formulaire de profil des utilisateurs
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/autoriser');
include_spip('base/objets');
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/session');
include_spip('inc/config');
include_spip('inc/profils');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @param string $retour
 * 		URL de redirection une fois terminé
 * @return string
 *     Hash du formulaire
 */
function formulaires_profil_identifier_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '', $forcer_admin=false) {
	return serialize(array(intval($id_auteur)));
}

/**
 * Saisies du formulaire de profil
 *
 * Déclarer les saisies utilisées pour générer le formulaire.
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @param string $retour
 * 		URL de redirection une fois terminé
 * @return array
 *     Tableau des saisies
 */
function formulaires_profil_saisies_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '', $forcer_admin=false) {
	$saisies = profils_chercher_saisies_profil('edition', $id_auteur, $id_ou_identifiant_profil);
	
	// Si c'est pour une création et qu'on est admin
	if (!intval($id_auteur) and autoriser('creer', 'auteur')) {
		array_unshift($saisies, array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'envoyer_notification',
				'label_case' => _T('profil:envoyer_notification_label_case'),
				'conteneur_class' => 'pleine_largeur',
			),
		));
	}
	
	return $saisies;
}

/**
 * Chargement du formulaire de profil
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @param string $retour
 * 		URL de redirection une fois terminé
 * @return array
 *     Environnement du formulaire
 */
function formulaires_profil_charger_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '', $forcer_admin=false) {
	$contexte = array();
	
	// Non, si pas d'id_auteur, on en crée un nouveau
	//~ // Si pas d'id_auteur on prend celui connecté actuellement
	//~ if (!intval($id_auteur)) {
		//~ $id_auteur = session_get('id_auteur');
	//~ }
	
	// On vérifie que l'auteur existe et qu'on a le droit de le modifier
	$id_auteur = intval($id_auteur);
	if (
		// Si demande de création mais pas le droit
		(!$id_auteur and !autoriser('creer', 'auteur'))
		or
		(
			// Ou s'il y a un id_auteur mais qu'il n'existe pas ou pas le droit de le modifier
			$id_auteur
			and (
				!$auteur = sql_fetsel('id_auteur,nom,email', 'spip_auteurs', 'id_auteur = '.$id_auteur)
				or (!($id_auteur == session_get('id_auteur')) and !autoriser('modifier', 'auteur', $id_auteur))
			)
		)
	) {
		return array(
			'editable' => false,
			'message_erreur' => _T('profils:erreur_autoriser_profil'),
		);
	}
	
	// Récupérer toutes les infos possibles déjà existantes
	$infos = profils_recuperer_infos($id_auteur, $id_ou_identifiant_profil);
	
	// On remplit le contexte avec ces informations (et un préfixe pour le contact)
	$contexte = array_merge($contexte, $infos);
	
	return $contexte;
}

/**
 * Vérifications du formulaire de profil
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @param string $retour
 * 		URL de redirection une fois terminé
 * @return array
 *     Tableau des erreurs
 */
function formulaires_profil_verifier_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '', $forcer_admin=false) {
	$erreurs = array();
	
	return $erreurs;
}

/**
 * Traitement du formulaire de profil
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @param string $retour
 * 		URL de redirection une fois terminé
 * @return array
 *     Retours des traitements
 */
function formulaires_profil_traiter_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '', $forcer_admin=false) {
	if ($retour) {
		refuser_traiter_formulaire_ajax();
	}
	$retours = array();
	$champs_auteur = _request('auteur');
	$champs_organisation = _request('organisation');
	$champs_contact = _request('contact');
	$champs_coordonnees = _request('coordonnees');
	$email_principal = '';
	$nom_principal = '';
	
	// Récupérer les objets liés au profil utilisateur
	extract(profils_chercher_ids_profil($id_auteur, $id_ou_identifiant_profil));
	
	// On cherche le bon profil
	$id_ou_identifiant_profil = profils_selectionner_profil($id_ou_identifiant_profil, $id_auteur);
	if ($profil = profils_recuperer_profil($id_ou_identifiant_profil) and $config = $profil['config']) {
		// Préparer certaines valeurs magiquement suivant la configuration
		
		// Email principal
		if ($champ_email_principal = profils_chercher_champ_email_principal($config) and is_array($champ_email_principal)) {
			$email_principal = _request(array_shift($champ_email_principal));
			foreach ($champ_email_principal as $cle) {
				$email_principal = $email_principal[$cle];
			}
		}
		
		// Nom principal (peut-être vérifier que le champ est censé être là)
		if (
			!$nom_principal = $champs_auteur['nom']
			and !$nom_principal = $champs_organisation['nom']
			and !$nom_principal = trim($champs_contact['prenom'] . ' ' . $champs_contact['nom'])
			and $email_principal
		) {
			$nom_principal = explode('@', $email_principal);
			$nom_principal = array_shift($nom_principal);
		}
		
		// Si c'est une demande de création, deux cas possibles
		if (!$id_auteur or $id_auteur=='new') {
			// Si on n'a pas forcé en mode admin ou que personne n'est connecté, c'est alors une inscription de la personne qui a validé
			if (!$forcer_admin and !session_get('id_auteur')) {
				// Pseudo et email repris des autres champs
				set_request('mail_inscription', $email_principal);
				set_request('nom_inscription', $nom_principal);
				// Inscription en visiteur public
				$inscription_dist = charger_fonction('traiter', 'formulaires/inscription');
				$retours_inscription = $inscription_dist('6forum','');
				$retours = array_merge($retours, $retours_inscription);
				
				// On récupère l'auteur qui vient d'être créé
				$auteur = sql_fetsel('*', 'spip_auteurs', 'email = '.sql_quote($email_principal));
				$id_auteur = intval($auteur['id_auteur']);
				// On connecte le nouvel utilisateur directement !
				include_spip('inc/auth');
				auth_loger($auteur);
			}
			// Sinon c'est qu'une personne crée un profil pour une autre, donc édition classique
			else {
				// MAIS AVANT on cherche si cette personne existe déjà, identifiée par son email ou son nom et avec le même profil
				if (
					(
						$email_principal
						and $auteur = sql_fetsel(
							'id_auteur',
							'spip_auteurs',
							// même email et même profil
							array('email='.sql_quote($email_principal), 'id_profil='.$profil['id_profil'])
						)
					)
					or (
						!$email_principal
						and $nom_principal
						and $auteur = sql_fetsel(
							'id_auteur',
							'spip_auteurs',
							// même nom exactement et même profil et email VIDE
							array('nom='.sql_quote($nom_principal), 'email=""', 'id_profil='.$profil['id_profil'])
						)
					)
				) {
					$id_auteur = $auteur['id_auteur'];
					
					// Vu que c'était pas prévu, on va refaire la recherche de tous les objets possibles à partir de cet id_auteur là
					extract(profils_chercher_ids_profil($id_auteur, $id_ou_identifiant_profil));
				}
				// Sinon on crée juste l'auteur vide, les champs seront ajoutés après
				else {
					include_spip('action/editer_objet');
					$id_auteur = objet_inserer('auteur', null, array('statut' => '6forum', 'login' => md5($email_principal), 'pass' => ' '));
					
					// Si on doit envoyer une notification à la création (et qu'on a un email…)
					if ($email_principal and _request('envoyer_notification')) {
						include_spip('action/inscrire_auteur');
						$cookie = auteur_attribuer_jeton($id_auteur);
						
						include_spip('inc/filtres');
						$notification = recuperer_fond(
							'notifications/profil_motdepasse',
							array(
								'nom' => $nom_principal,
								'email' => $email_principal,
								'sendcookie' => url_absolue(
									generer_url_public('spip_pass', "p=$cookie"),
									$GLOBALS['meta']['adresse_site'] . '/'
								)
							)
						);
						include_spip('inc/notifications');
						notifications_envoyer_mails($email_principal, $notification);
					}
				}
			}
			
			// Pour une création, on assigne le profil principal
			set_request('id_profil', $profil['id_profil']);
		}
		
		// Si on a un auteur, on modifie déjà l'auteur existant
		if ($id_auteur > 0) {
			// On met en request racine les champs trouvés pour l'auteur
			profils_traiter_peupler_request('edition', $champs_auteur, $config['auteur']);
			// S'il y a un email principal, on l'ajoute
			if ($email_principal) {
				set_request('email', $email_principal);
			}
			// S'il y a un nom principal, on l'ajoute
			if ($nom_principal) {
				set_request('nom', $nom_principal);
			}
			
			$retours_auteur = formulaires_editer_objet_traiter('auteur', $id_auteur, 0, 0, $retour, '');
			$retours = array_merge($retours, $retours_auteur);
			//~ $auteur = sql_fetsel('id_auteur, nom, email', 'spip_auteurs', 'id_auteur = '.$id_auteur);
			
			// On voit si on doit placer dans un annuaire
			if (defined('_DIR_PLUGIN_CONTACTS') and lire_config('contacts_et_organisations/utiliser_annuaires')) {
				// On teste s'il faut créer un nouvel annuaire
				if (
					// S'il n'y a pas d'annuaire configuré
					!isset($config['id_annuaire'])
					or !$id_annuaire = intval($config['id_annuaire'])
					// Ou s'il n'existe plus
					or !sql_getfetsel('id_annuaire', 'spip_annuaires', 'id_annuaire='.$id_annuaire)
				) {
					// On cherche s'il existe un annuaire du même identifiant que le profil
					if (!$id_annuaire = sql_getfetsel('id_annuaire', 'spip_annuaires', 'identifiant='.sql_quote($profil['identifiant']))) {
						// Alors on en crée un nouveau
						$id_annuaire = objet_inserer(
							'annuaire',
							null,
							array('titre'=>$profil['titre'], 'identifiant'=>$profil['identifiant'])
						);
					}
				}
				
				// On le met en requête
				set_request('id_annuaire', $id_annuaire);
			}
			
			// Si la fiche principale est une organisation
			set_request('id_auteur', $id_auteur);
			if ($config['activer_organisation'] and $id_organisation) {
				// Si on ne trouve pas un nom d'organisation, on le remplit avec le nom principal comme pour l'auteur
				if (!isset($champs_organisation['nom']) or !$champs_organisation['nom']) {
					$champs_organisation['nom'] = $nom_principal;
				}
				// On remplit le request avec les champs de l'organisation
				profils_traiter_peupler_request('edition', $champs_organisation, $config['organisation']);
				// On appelle le traitement d'édition de l'organisation
				$retours_organisation = formulaires_editer_objet_traiter('organisation', $id_organisation, 0, 0, $retour, '');
				$retours = array_merge($retours, $retours_organisation);
				$id_organisation = $retours['id_organisation'];
				
				// Si on a aussi un contact en plus
				if ($config['activer_contact'] and $id_contact) {
					// On enlève l'id_auteur
					set_request('id_auteur', null);
					// On précise l'organisation parente
					set_request('id_parent', $id_organisation);
					// On remplit le request avec les champs du contact
					profils_traiter_peupler_request('edition', $champs_contact, $config['contact']);
					// On appelle le traitement d'édition du contact
					$retours_contact = formulaires_editer_objet_traiter('contact', $id_contact, $id_organisation, 0, $retour, '');
					$retours = array_merge($retours_contact, $retours);
					$id_contact = $retours['id_contact'];
				}
			}
			// Sinon si la fiche principale est un contact
			elseif ($config['activer_contact'] and $id_contact) {
				// On remplit le request avec les champs du contact
				profils_traiter_peupler_request('edition', $champs_contact, $config['contact']);
				// On appelle le traitement d'édition du contact
				$retours_contact = formulaires_editer_objet_traiter('contact', $id_contact, 0, 0, $retour, '');
				$retours = array_merge($retours, $retours_contact);
				$id_contact = $retours['id_contact'];
			}
			
			// Et maintenant on s'occupe des coordonnées
			// Les tests ont déjà été fait pendant la récup des ids, donc si c'est rempli c'est que c'est bon
			if (is_array($coordonnees)) {
				foreach ($coordonnees as $objet => $coordonnees_types) {
					$cle_objet = id_table_objet($objet);
					
					if (intval(${$cle_objet})) {
						set_request('objet', $objet);
						set_request('id_objet', ${$cle_objet});
						
						foreach ($coordonnees_types as $coordonnee => $types) {
							foreach ($types as $type => $id_coordonnee) {
								// Si on le trouve bien dans ce qui a été envoyé du formulaire
								if ($champs_coordonnees[$objet][$coordonnee][$type ? $type : 0]) {
									// On met en request racine les champs de cette coordonnée
									$coordonnee_remplie = false;
									foreach ($champs_coordonnees[$objet][$coordonnee][$type ? $type : 0] as $champ=>$valeur) {
										// S'il y a au moins un champ rempli, la coordonnée est à remplir
										if ($valeur) {
											$coordonnee_remplie = true;
											set_request($champ, $valeur);
										}
									}
									
									// Si la coordonnée est à remplir on la traite
									if ($coordonnee_remplie) {
										set_request('type', $type);
										// Enfin on traite la coordonnée
										$retours_coordonnee = formulaires_editer_objet_traiter(objet_type($coordonnee), $id_coordonnee, 0, 0, $retour, '');
										$retours = array_merge($retours_coordonnee, $retours);
									}
									// Sinon, tous les champs sont vides, on peut la supprimer pour faire du ménage, si c'est une coordonnée existante
									elseif ($id_coordonnee = intval($id_coordonnee)) {
										sql_delete(table_objet_sql($coordonnee), id_table_objet($coordonnee) . '=' . $id_coordonnee);
										sql_delete(table_objet_sql($coordonnee) . '_liens', id_table_objet($coordonnee) . '=' . $id_coordonnee);
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	// On vérifie pour le redirect
	if (!$retours['redirect'] and $retour) {
		$retours['redirect'] = $retour;
	}
	
	// On peut toujours continuer d'éditer après envoi
	$retours['editable'] = true;
	
	return $retours;
}
