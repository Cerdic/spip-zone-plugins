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

include_spip('base/objets');
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/profils');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @return string
 *     Hash du formulaire
 */
function formulaires_profil_identifier_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '') {
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
 * @return array
 *     Environnement du formulaire
 */
function formulaires_profil_saisies_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '') {
	$saisies = profils_chercher_saisies_profil('edition', $id_auteur, $id_ou_identifiant_profil);
	
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
 * @return array
 *     Environnement du formulaire
 */
function formulaires_profil_charger_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '') {
	include_spip('inc/autoriser');
	$contexte = array();
	
	// Si pas d'id_auteur on prend celui connecté actuellement
	if (!intval($id_auteur)) {
		$id_auteur = session_get('id_auteur');
	}
	
	// On vérifie que l'auteur existe et qu'on a le droit de le modifier
	if (
		!$auteur = sql_fetsel('id_auteur,nom,email', 'spip_auteurs', 'id_auteur = '.intval($id_auteur))
		or !$id_auteur = intval($auteur['id_auteur'])
		or (!($id_auteur == session_get('id_auteur')) and !autoriser('modifier', 'auteur', $id_auteur))
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
 * @return array
 *     Tableau des erreurs
 */
function formulaires_profil_verifier_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '') {
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
 * @return array
 *     Retours des traitements
 */
function formulaires_profil_traiter_dist($id_auteur = 'new', $id_ou_identifiant_profil = '', $retour = '') {
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
	$id_ou_identifiant_profil = profils_selectionner_profil($id_ou_identifiant_profil, $ids['id_auteur']);
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
		
		// S'il faut inscrire pour avoir un nouvel utilisateur (à ne pas forcément utiliser pour l'instant)
		if (!$id_auteur or $id_auteur=='new') {
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
		
		// Si on a un utilisateur déjà connecté, on modifie déjà l'auteur existant
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
			
			// Si la fiche principale est une organisation
			set_request('id_auteur', $id_auteur);
			if ($config['activer_organisation'] and $id_organisation) {
				profils_traiter_peupler_request('edition', $champs_organisation, $config['organisation']);
				$retours_organisation = formulaires_editer_objet_traiter('organisation', $id_organisation, 0, 0, $retour, '');
				$retours = array_merge($retours, $retours_organisation);
				$id_organisation = $retours['id_organisation'];
				
				// Si on a aussi un contact en plus
				if ($config['activer_contact'] and $id_contact) {
					// On précise l'organisation parente
					set_request('id_parent', $id_organisation);
					profils_traiter_peupler_request('edition', $champs_contact, $config['contact']);
					$retours_contact = formulaires_editer_objet_traiter('contact', $id_contact, $id_organisation, 0, $retour, '');
					$retours = array_merge($retours_contact, $retours);
				}
			}
			// Sinon si la fiche principale est un contact
			elseif ($config['activer_contact'] and $id_contact) {
				profils_traiter_peupler_request('edition', $champs_contact, $config['contact']);
				$retours_contact = formulaires_editer_objet_traiter('contact', $id_contact, 0, 0, $retour, '');
				$retours = array_merge($retours, $retours_contact);
				$id_contact = $retours['id_contact'];
			}
			
			// Et maintenant on s'occupe des coordonnées
			// Les tests ont déjà été fait pendant la récup des ids, donc si c'est rempli c'est que c'est bon
			if (is_array($coordonnees)) {
				foreach ($coordonnees as $objet => $coordonnees_types) {
					$cle_objet = id_table_objet($objet);
					
					if (${$cle_objet}) {
						set_request('objet', $objet);
						set_request('id_objet', ${$cle_objet});
						
						foreach ($coordonnees_types as $coordonnee => $types) {
							foreach ($types as $type => $id_coordonnee) {
								// Si on le trouve bien dans ce qui a été envoyé du formulaire
								if ($champs_coordonnees[$objet][$coordonnee][$type ? $type : 0]) {
									// On met en request racine les champs de cette coordonnée
									foreach ($champs_coordonnees[$objet][$coordonnee][$type ? $type : 0] as $champ=>$valeur) {
										set_request($champ, $valeur);
									}
									set_request('type', $type);
									// Enfin on traite la coordonnée
									$retours_coordonnee = formulaires_editer_objet_traiter(objet_type($coordonnee), $id_coordonnee, 0, 0, $retour, '');
									$retours = array_merge($retours_coordonnee, $retours);
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
	
	return $retours;
}

function profils_traiter_peupler_request($form, $champs_objet, $config_objet) {
	if (is_array($champs_objet) and $config_objet) {
		foreach ($champs_objet as $champ => $valeur) {
			// Si ce champ faisait vraiment partie des choses à envoyer
			if ($config_objet[$champ] and in_array($form, $config_objet[$champ])) {
				set_request($champ, $valeur);
			}
		}
	}
}
