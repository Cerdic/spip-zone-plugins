<?php
/**
 * Fonctions utilitaires pour les profils
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Utils
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/objets');
include_spip('base/abstract_sql');
include_spip('action/editer_liens');

/**
 * Trouver quel profil utiliser suivant s'il y a un auteur ou pas et s'il a déjà un profil
 * 
 * @param int|string $id_ou_identifiant_profil 
 * @param int $id_auteur 
 * @return int|string
 * 		Retourne un identifiant entier ou texte d'un profil
 */
function profils_selectionner_profil($id_ou_identifiant_profil='', $id_auteur=0) {
	$id_auteur = intval($id_auteur);
	
	// S'il y a un utilisateur et qu'il a déjà un profil sélectionné
	// c'est prioritaire au profil par défaut donc si pas d'autre profil demandé explicitement
	if (!$id_ou_identifiant_profil and $id_auteur > 0 and $id_profil = sql_getfetsel('id_profil', 'spip_auteurs', 'id_auteur = '.$id_auteur)) {
		$id_ou_identifiant_profil = $id_profil;
	}
	
	return $id_ou_identifiant_profil;
}

/**
 * Cherche le profil suivant un id SQL ou un identifiant textuel
 * 
 * @param int|string $id_ou_identifiant_profil
 * 		ID SQL ou identifiant textuel du profil
 * @return array|bool
 * 		Retourne le profil demandé ou false
 */
function profils_recuperer_profil($id_ou_identifiant_profil) {
	static $profils = array();
	$profil = false;
	
	// Si on l'a déjà trouvé avant
	if (isset($profils[$id_ou_identifiant_profil])) {
		return $profils[$id_ou_identifiant_profil];
	}
	// Sinon on cherche
	else {
		// Si pas de profil précis demandé
		if (!$id_ou_identifiant_profil) {
			// Si on a configuré un profil par défaut explicitement
			if ($id_profil_defaut = intval(lire_config('profils/id_profil_defaut'))) {
				$profil = sql_fetsel('*', 'spip_profils', 'id_profil = '.$id_profil_defaut);
			}
			// Sinon on prend le tout premier
			else {
				$profil = sql_fetsel('*', 'spip_profils', '', '', 'id_profil asc', '0,1');
			}
		}
		// Si c'est un identifiant numérique
		elseif (is_numeric($id_ou_identifiant_profil)) {
			$profil = sql_fetsel('*', 'spip_profils', 'id_profil = '.intval($id_ou_identifiant_profil));
		}
		// Si c'est un identifiant textuel
		else {
			$profil = sql_fetsel('*', 'spip_profils', 'identifiant = '.sql_quote($id_ou_identifiant_profil));
		}
	}
	
	if (isset($profil['config'])) {
		$profil['config'] = unserialize($profil['config']);
	}
	
	$profils[$id_ou_identifiant_profil] = $profil;
	return $profil;
}


/**
 * Cherche dans une config s'il y a bien un champ email obligatoire et si oui lequel
 * 
 * La fonction cherche l'email obligatoire *le plus proche* du compte utilisateur
 * 
 * @param array $config
 * 		Le tableau de configuration d'un profil
 * @return
 * 		Retourne le champ d'email principal
 */
function profils_chercher_champ_email_principal($config) {
	$champ = false;
	
	foreach (array('auteur', 'organisation', 'contact') as $objet) {
		if ($objet == 'auteur' or (defined('_DIR_PLUGIN_CONTACTS') and $config["activer_$objet"])) {
			// On cherche dans les champs de l'objet
			if (
				!$champ
				and $config[$objet]['email'] 
				and in_array('inscription', $config[$objet]['email'])
				and in_array('obligatoire', $config[$objet]['email'])
			) {
				$champ = array($objet, 'email');
			}
			// Sinon on cherche dans les coordonnées emails
			elseif (
				!$champ
				and defined('_DIR_PLUGIN_COORDONNEES')
				and $config["activer_coordonnees_$objet"]
				and $config['coordonnees'][$objet]['emails']
			) {
				// On parcourt les emails configurés
				foreach ($config['coordonnees'][$objet]['emails'] as $champ_email) {
					if ($champ_email['inscription'] and $champ_email['obligatoire']) {
						$type = $champ_email['type'] ? $champ_email['type'] : 0;
						$champ = array('coordonnees', $objet, 'emails', $type, 'email');
					}
				}
			}
		}
	}
	
	return $champ;
}

/**
 * Cherche les saisies d'édition d'un objet ET des champs extras ajoutés
 * 
 * @param string $objet
 * 		Nom de l'objet dont on cherche les saisies
 * @return array
 * 		Retoure un tableau de saisies
 * 
 */
function profils_chercher_saisies_objet($objet) {
	static $saisies = array();
	$objet = objet_type($objet);
	
	if (isset($saisies[$objet])) {
		return $saisies[$objet];
	}
	else {
		$saisies[$objet] = array();
		
		// Les saisies de base
		if ($saisies_objet = saisies_chercher_formulaire("editer_$objet", array())) {
			$saisies[$objet] = array_merge($saisies[$objet], $saisies_objet);
		}
		
		// Les saisies des champs extras
		if (defined('_DIR_PLUGIN_CEXTRAS')) {
			include_spip('cextras_pipelines');
			
			if ($saisies_extra = champs_extras_objet(table_objet_sql($objet))) {
				$saisies[$objet] = array_merge($saisies[$objet], $saisies_extra);
			}
		}
	}
	
	return $saisies[$objet];
}

/**
 * Cherche les saisies configurées pour un profil et pour tel formulaire (inscription ou édition)
 * 
 * @param string $form
 * 		Quel formulaire : "inscription" ou "edition" 
 * @param $id_auteur 
 * 		Identifiant du compte utilisateur dont on cherche les saisies
 * @param $id_ou_identifiant_profil 
 * 		Identifiant du profil, notamment si c'est une création
 * @return array
 * 		Retourne le tableau des saisies du profil
 */
function profils_chercher_saisies_profil($form, $id_auteur=0, $id_ou_identifiant_profil='') {
	$saisies = array();
	
	// On cherche le bon profil
	$id_ou_identifiant_profil = profils_selectionner_profil($id_ou_identifiant_profil, $id_auteur);
	
	// On ne continue que si on a un profil sous la main
	if ($profil = profils_recuperer_profil($id_ou_identifiant_profil) and $config = $profil['config']) {
		foreach (array('auteur', 'organisation', 'contact') as $objet) {
			// Si c'est autre chose que l'utilisateur, faut le plugin qui va avec et que ce soit activé
			if ($objet == 'auteur' or (defined('_DIR_PLUGIN_CONTACTS') and $config["activer_$objet"])) {
				// On récupère les champs pour cet objet ET ses champs extras s'il y a
				$saisies_objet = profils_chercher_saisies_objet($objet);
				$saisies_a_utiliser = array();
				
				// Pour chaque chaque champ vraiment configuré
				if ($config[$objet]) {
					foreach ($config[$objet] as $champ => $config_champ) {
						// On cherche la saisie pour ce champ SI c'est pour le form demandé
						if (in_array($form, $config_champ) and $saisie = saisies_chercher($saisies_objet, $champ)) {
							// On modifie son nom
							$saisie['options']['nom'] = $objet . '[' . $saisie['options']['nom'] . ']';
							// On modifie son obligatoire suivant la config
							$saisie['options']['obligatoire'] = in_array('obligatoire', $config_champ) ? 'oui' : false;
							// On ajoute la saisie
							$saisies_a_utiliser[] = $saisie;
						}
					}
				}
				
				// On cherche des coordonnées pour cet objet
				if (
					defined('_DIR_PLUGIN_COORDONNEES')
					and $config["activer_coordonnees_$objet"]
					and $coordonnees = $config['coordonnees'][$objet]
				) {
					// Pour chaque type de coordonnéees (num, email, adresse)
					foreach ($coordonnees as $coordonnee => $champs) {
						// Pour chaque champ ajouté
						foreach ($champs as $cle => $champ) {
							// Si ce cette coordonnées est configurée pour le form demandé
							if ($champ[$form]) {
								// Attention, si pas de type, on transforme ici en ZÉRO
								if (!$champ['type']) {
									$champ['type'] = 0;
								}
								// On va chercher les saisies de ce type de coordonnées
								$saisies_coordonnee = profils_chercher_saisies_objet($coordonnee);
								// On vire le titre libre
								$saisies_coordonnee = saisies_supprimer($saisies_coordonnee, 'titre');
								// On change le nom de chacun des champs
								$saisies_coordonnee =  saisies_transformer_noms(
									$saisies_coordonnee,
									'/^\w+$/',
									"coordonnees[$objet][$coordonnee][${champ['type']}][\$0]"
								);
								// On reconstitue le label
								$label = $champ['label'] ? $champ['label'] : _T(objet_info(table_objet_sql($coordonnee), 'texte_objet'));
								if ($champ['type'] and !$champ['label']) {
									$label .= ' (' . coordonnees_lister_types_coordonnees(objet_type($coordonnee), $champ['type']) . ')';
								}
								// Si c'est un numéro ou un email on change peut-être le label du champ lui-même et le obligatoire
								if (in_array($coordonnee, array('numeros', 'emails'))) {
									$saisies_coordonnee = saisies_modifier(
										$saisies_coordonnee,
										"coordonnees[$objet][$coordonnee][${champ['type']}][" . objet_type($coordonnee) . ']',
										array(
											'options' => array(
												'label' => $label,
												'obligatoire' => $champ['obligatoire'] ? 'oui' : false,
											),
										)
									);
									// On ajoute enfin
									$saisies_a_utiliser	= array_merge($saisies_a_utiliser, $saisies_coordonnee);
								}
								// Alors que si c'est une adresse on l'utilise pour le groupe de champs
								else {
									$saisies_a_utiliser[] = array(
										'saisie' => 'fieldset',
										'options' => array(
											'nom' => "groupe_${coordonnee}_$cle",
											'label' => $label,
										),
										'saisies' => $saisies_coordonnee,
									);
								}
							}
						}
					}
				}
				
				// On teste s'il faut un groupe de champs ou pas pour cet objet
				if ($saisies_a_utiliser and $legend = $config["activer_groupe_$objet"]) {
					$saisies[] = array(
						'saisie' => 'fieldset',
						'options' => array(
							'nom' => "groupe_$objet",
							'label' => $legend,
						),
						'saisies' => $saisies_a_utiliser,
					);
				}
				// Sinon on les ajoute directement
				else {
					$saisies = array_merge($saisies, $saisies_a_utiliser);
				}
			}
		}
	}
		
	return $saisies;
}

/**
 * Récupérer tous les identifiants des objets liés au profil d'un compte utilisateur
 *
 * @param int $id_auteur=0
 * 		Identifiant d'un auteur précis, sinon visiteur en cours
 * @return array
 * 		Retourne un tableau de tous les identifiants
 */
function profils_chercher_ids_profil($id_auteur=0, $id_ou_identifiant_profil='') {
	$ids = array();
	$ids['id_auteur'] = intval($id_auteur);
	$coordonnees_liees = array();
	
	// On cherche le bon profil
	$id_ou_identifiant_profil = profils_selectionner_profil($id_ou_identifiant_profil, $ids['id_auteur']);
	
	// Si pas d'utilisateur, il faut en créer un
	if (!$ids['id_auteur']) {
		$ids['id_auteur'] = 'new';
	}
	
	// Maintenant on ne continue que si on a trouvé un profil
	if ($profil = profils_recuperer_profil($id_ou_identifiant_profil) and $config = $profil['config']) {
		// Si le plugin est toujours là
		if (defined('_DIR_PLUGIN_CONTACTS')) {
			// Est-ce qu'il y a une orga en fiche principale ?
			if ($config['activer_organisation']) {
				// Cherchons une organisation
				if (
					!intval($ids['id_auteur'])
					or !$ids['id_organisation'] = intval(sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur = '.$ids['id_auteur']))
				) {
					$ids['id_organisation'] = 'new';
				}
				
				// Il peut aussi y avoir un contact physique *lié à l'orga*
				if (
					!intval($ids['id_organisation'])
					or !$liens = objet_trouver_liens(array('organisation'=>$ids['id_organisation']), array('contact'=>'*'))
					or !$contact = $liens[0]
					or !$ids['id_contact'] = intval($contact['id_objet'])
				) {
					$ids['id_contact'] = 'new';
				}
			}
			elseif ($config['activer_contact']) {
				// Cherchons un contact physique
				if (
					!intval($ids['id_auteur'])
					or !$ids['id_contact'] = intval(sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur = '.$ids['id_auteur']))
				) {
					$ids['id_contact'] = 'new';
				}
			}
		}
		
		// Si on doit chercher des coordonnées
		if (defined('_DIR_PLUGIN_COORDONNEES')) {
			foreach (array('auteur', 'organisation', 'contact') as $objet) {
				// S'il y a des coordonnées activées pour cet objet
				if ($config["activer_coordonnees_$objet"] and $coordonnees = $config['coordonnees'][$objet]) {
					$cle_objet = id_table_objet($objet);
					
					foreach ($coordonnees as $coordonnee => $champs) {
						$cle_coordonnee = id_table_objet($coordonnee);
						
						foreach ($champs as $cle => $champ) {
							// On cherche s'il y a une liaison sinon new
							if (
								!$id_objet = $ids[$cle_objet]
								or !$liens = objet_trouver_liens(
									array(objet_type($coordonnee) => '*'),
									array($objet => $id_objet),
									array('type = '.sql_quote($champ['type']))
								)
								or !$liaison = $liens[0]
								or !$coordonnees_liees[$objet][$coordonnee][$champ['type']] = $liaison[$cle_coordonnee]
							) {
								$coordonnees_liees[$objet][$coordonnee][$champ['type']] = 'new';
							}
						}
					}
				}
			}
			
			// S'il y a des coordonnées on ajoute
			if ($coordonnees_liees) {
				$ids['coordonnees'] = $coordonnees_liees;
			}
		}
	}
	
	return $ids;
}

/**
 * Récupérer les informations complètes d'un compte de profil
 *
 * Renvoie un tableau multidimensionnel avec l'ensemble des valeurs
 * chargées par les formulaires d'édition des objets du profil,
 * chaque objet dans une clé (organisation, contact, etc.)
 *
 * @param int $id_auteur=0
 * 		Identifiant d'un auteur précis, sinon visiteur en cours
 * @return array
 * 		Informations d'un profil, chaque objet SPIP dans une clé (organisation, contact, telephone, etc).
 */
function profils_recuperer_infos($id_auteur=0, $id_ou_identifiant_profil='') {
	include_spip('inc/editer');
	$retour = '';
	$infos = array();
	
	// Récupérer les objets liés au profil utilisateur
	extract(profils_chercher_ids_profil($id_auteur, $id_ou_identifiant_profil));
	
	// On charge les infos du compte SPIP
	$infos['auteur'] = formulaires_editer_objet_charger('auteur', $id_auteur, 0, 0, $retour, '');
	
	// On charge les infos de l'organisation
	if ($id_organisation) {
		$infos['organisation'] = formulaires_editer_objet_charger('organisation', $id_organisation, 0, 0, $retour, '');
		unset($infos['organisation']['_pipeline']);
	}
	
	// On charge les infos du contact
	if ($id_contact) {
		$infos['contact'] = formulaires_editer_objet_charger('contact', $id_contact, intval($id_organisation), 0, $retour, '');
	}
	
	// S'il y a des coordonnées
	if ($coordonnees and is_array($coordonnees)) {
		foreach ($coordonnees as $objet => $coordonnees_objet) {
			foreach ($coordonnees_objet as $coordonnee => $champs) {
				foreach ($champs as $type => $id) {
					// Attention, si pas de type, on transforme ici en ZÉRO
					if (!$type) {
						$type = 0;
					}
					$infos['coordonnees'][$objet][$coordonnee][$type] = formulaires_editer_objet_charger(objet_type($coordonnee), $id, 0, 0, $retour, '');
				}
			}
		}
	}
	
	return $infos;
}

/**
 * Récupérer les informations résumées d'un compte de profil
 *
 * Renvoie un tableau simple avec toutes les valeurs du profil,
 * les champs configurés pour l'édition uniquement.
 *
 * @uses profils_recuperer_profil()
 * @uses profils_recuperer_infos()
 *
 * @param int $id_auteur=0
 * 		Identifiant d'un auteur précis, sinon visiteur en cours
 * @return array
 * 		Informations d'un profil sous forme de tableau associatif
 */
function profils_recuperer_infos_simples($id_auteur=0, $id_ou_identifiant_profil='') {
	$ligne = array();

	if (
		$infos = profils_recuperer_infos($id_auteur, $id_ou_identifiant_profil)
		and $profil = profils_recuperer_profil($id_ou_identifiant_profil)
		and $config = $profil['config']
	) {
		$ligne = array();

		// Les objets dans un ordre précis
		foreach (array('auteur', 'organisation', 'contact') as $objet) {
			// Si c'est autre chose que l'utilisateur, faut le plugin qui va avec et que ce soit activé
			if ($objet == 'auteur' or (defined('_DIR_PLUGIN_CONTACTS') and $config["activer_$objet"])) {
				// Pour chaque chaque champ vraiment configuré
				if ($config[$objet]) {
					foreach ($config[$objet] as $champ => $config_champ) {
						// On prend les champs d'édition de profil uniquement
						if (in_array('edition', $config_champ)) {
							$cle = $objet.'_'.$champ;;
							$ligne[$cle] = $infos[$objet][$champ];
						}
					}
				}
				
				// On cherche des coordonnées pour cet objet
				if (
					defined('_DIR_PLUGIN_COORDONNEES')
					and $config["activer_coordonnees_$objet"]
					and $coordonnees = $config['coordonnees'][$objet]
				) {
					// Pour chaque type de coordonnéees (num, email, adresse)
					foreach ($coordonnees as $coordonnee => $champs) {
						// Pour chaque champ ajouté
						foreach ($champs as $cle => $champ) {
							// Si ce cette coordonnées est configurée pour le form demandé
							if ($champ['edition']) {
								// Attention, si pas de type, on transforme ici en ZÉRO
								if (!$champ['type']) {
									$champ['type'] = 0;
								}
								// On va chercher les saisies de ce type de coordonnées
								$saisies_coordonnee = profils_chercher_saisies_objet($coordonnee);
								// On vire le titre libre
								$saisies_coordonnee = saisies_supprimer($saisies_coordonnee, 'titre');
								// On cherche uniquement le nom des champs
								$saisies_noms = saisies_lister_champs($saisies_coordonnee);
								
								// On ajoute aux colonnes
								foreach ($saisies_noms as $nom) {
									$cle = $objet.'_'.$coordonnee.'_'.$champ['type'].'_'.$nom;
									$ligne[$cle] = $infos['coordonnees'][$objet][$coordonnee][$champ['type']][$nom];
								}
							}
						}
					}
				}
			}
		}
	}

	return $ligne;
}

function profils_traiter_peupler_request($form, $champs_objet, $config_objet) {
	if (is_array($champs_objet) and $config_objet) {
		foreach ($config_objet as $champ => $config_champ) {
			// Si c'est configuré pour ce formulaire
			if (in_array($form, $config_champ)) {
				set_request('cextra_'.$champ, 1); // pour que champs extras le gère dans pre_edition ensuite
				
				if (isset($champs_objet[$champ])) {
					set_request($champ, $champs_objet[$champ]);
				}
			}
		}
	}
}

/**
 * @brief 
 * @param $id_auteur 
 * @param $id_ou_identifiant_profil 
 * @returns 
 */
function profils_enregistrer_profil($id_auteur, $id_ou_identifiant_profil='') {
	
}
