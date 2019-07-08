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
		
		// Si on a une info lors de l'appel
		if (isset($flux['args']['args'][1]['profil'])) {
			$id_ou_identifiant_profil = $flux['args']['args'][1]['profil'];
		}
		else {
			$id_ou_identifiant_profil = '';
		}
		
		if ($saisies = profils_chercher_saisies_profil('inscription', 'new', $id_ou_identifiant_profil)) {
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
			include_spip('action/editer_objet');
			
			// Récupérer les objets liés au profil utilisateur
			extract(profils_chercher_ids_profil($id_auteur, $profil['id_profil']));
			
			// Pour une création, on assigne le profil principal
			set_request('id_profil', $profil['id_profil']);
			
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
					$id_contact = $retours['id_contact'];
				}
			}
			// Sinon si la fiche principale est un contact
			elseif ($config['activer_contact'] and $id_contact) {
				profils_traiter_peupler_request('inscription', $champs_contact, $config['contact']);
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
		
		if ($retours) {
			$flux['data'] = array_merge($flux['data'], $retours);
		}
	}
	
	return $flux;
}

/**
 * Ajouter la liste des comptes d'un profil
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_afficher_config_objet($flux) {
	if (
		$flux['args']['type'] == 'profil'
		and isset($flux['args']['id'])
		and $id_profil = $flux['args']['id']
	) {
		$importer = recuperer_fond('prive/squelettes/inclure/profil_importer', array('id_profil'=>$id_profil));
		$flux['data'] .= $importer;
	}
	
	return $flux;
}
