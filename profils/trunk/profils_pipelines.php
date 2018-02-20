<?php
/**
 * Utilisations de pipelines par Profils
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Optimiser la base de données
 *
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_optimiser_base_disparus($flux) {
	sql_delete('spip_profils', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

/**
 * Liste les saisies à ajouter au formulaire d'inscription
 *
 * @pipeline formulaire_saisies
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_formulaire_saisies($flux) {
	if ($flux['args']['form'] == 'inscription') {
		include_spip('inc/profils');
		
		if ($saisies = profils_chercher_saisies_profil('inscription', 'new')) {
			$flux['data'] = $saisies;
		}
	}
	
	return $flux;
}

/**
 * Ajoute les champs au formulaire d'inscription
 *
 * @pipeline formulaire_fond
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_formulaire_fond($flux) {
	if (
		$flux['args']['form'] == 'inscription'
		and $saisies = $flux['args']['contexte']['_saisies']
	) {
		// On commence par virer les champs de départ car on va faire à notre sauce
		$flux['data'] = preg_replace(
			"%<div[^>]*saisie_(nom|mail)_inscription[^>]*>.*?</div>%ims",
			'',
			$flux['data']
		);
		
		// On génère le HTML des champs
		$contexte = $flux['args']['contexte'];
		$contexte['saisies'] = $contexte['_saisies'];
		unset($contexte['_saisies']);
		$champs = recuperer_fond('inclure/generer_saisies', $contexte);
		
		// On insère
		$flux['data'] = preg_replace(
			"|</fieldset>|Uims",
			"\\0" . $champs,
			$flux['data'],
			1
		);
	}

	return $flux;
}

/**
 * Remplit les bonnes valeurs pour l'inscription AVANT son traiter
 *
 * @pipeline formulaire_verifier
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_formulaire_verifier($flux) {
	if ($flux['args']['form'] == 'inscription') {
		include_spip('inc/profils');
		
		$champs_auteur = _request('auteur');
		$champs_organisation = _request('organisation');
		$champs_contact = _request('contact');
		$champs_coordonnees = _request('coordonnees');
		$email_principal = '';
		$nom_principal = '';
		
		// Si on a une info lors de l'appel
		if (isset($flux['args']['args'][1]['profil'])) {
			$id_ou_identifiant_profil = $flux['args']['args'][1]['profil'];
		}
		else {
			$id_ou_identifiant_profil = '';
		}
		
		// On cherche le bon profil
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
			
			// Pseudo et email repris des autres champs
			set_request('mail_inscription', $email_principal);
			set_request('nom_inscription', $nom_principal);
			// Et donc pas d'erreur sur les champs d'origine
			unset($flux['data']['mail_inscription']);
			unset($flux['data']['nom_inscription']);
			
			// On enregistre le profil pour ne pas avoir à refaire
			set_request('_profil', $profil);
		}
	}
	
	return $flux;
}

/**
 * Traitement supplémentaire après l'inscription
 *
 * @pipeline formulaire_traiter
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_formulaire_traiter($flux) {
	// Si inscription et qu'on a bien un id_auteur à la fin
	if ($flux['args']['form'] == 'inscription' and $id_auteur = $flux['data']['id_auteur']) {
		$retours = array();
		$champs_auteur = _request('auteur');
		$champs_organisation = _request('organisation');
		$champs_contact = _request('contact');
		$champs_coordonnees = _request('coordonnees');
		$email_principal = _request('mail_inscription');
		$nom_principal = _request('nom_inscription');
		$profil = _request('_profil');
		
		// On utilise la redirection si fournie (3ème argument lors de l'appel)
		if (isset($flux['args']['args'][2])) {
			$retour = $flux['args']['args'][2];
		}
		
		if ($profil and $config = $profil['config']) {
			include_spip('inc/editer');
			
			// Récupérer les objets liés au profil utilisateur
			extract(profils_chercher_ids_profil($id_auteur, $profil['id_profil']));
			
			// On met en request racine les champs trouvés pour l'auteur
			profils_traiter_peupler_request('inscription', $champs_auteur, $config['auteur']);
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
				profils_traiter_peupler_request('inscription', $champs_organisation, $config['organisation']);
				$retours_organisation = formulaires_editer_objet_traiter('organisation', $id_organisation, 0, 0, $retour, '');
				$retours = array_merge($retours, $retours_organisation);
				$id_organisation = $retours['id_organisation'];
				
				// Si on a aussi un contact en plus
				if ($config['activer_contact'] and $id_contact) {
					// On précise l'organisation parente
					set_request('id_parent', $id_organisation);
					profils_traiter_peupler_request('inscription', $champs_contact, $config['contact']);
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
		
		if ($retours) {
			$flux['data'] = array_merge($flux['data'], $retours);
		}
	}
	
	return $flux;
}
