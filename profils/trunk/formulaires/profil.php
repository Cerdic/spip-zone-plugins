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
function formulaires_profil_identifier_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
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
function formulaires_profil_saisies_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
	$saisies = array();
	
	// S'il y a un id_auteur on cherche s'il a un profil
	if (intval($id_auteur) > 0 and $id_profil = sql_getfetsel('id_profil', 'spip_auteurs', 'id_auteur = '.intval($id_auteur))) {
		$id_ou_identifiant_profil = $id_profil;
	}
	
	// On ne continue que si on a un profil sous la main
	if ($profil = profils_chercher_profil($id_ou_identifiant_profil) and $config = $profil['config']) {
		foreach (array('auteur', 'organisation', 'contact') as $objet) {
			// Si c'est autre chose que l'utilisateur, faut le plugin qui va avec et que ce soit activé
			if ($objet == 'auteur' or (defined('_DIR_PLUGIN_CONTACTS') and $config["activer_$objet"])) {
				// On récupère les champs pour cet objet ET ses champs extras s'il y a
				$saisies_objet = profils_chercher_saisies_objet($objet);
				$saisies_a_utiliser = array();
				
				// Pour chaque chaque champ vraiment configuré
				foreach ($config[$objet] as $champ => $config_champ) {
					// On cherche la saisie pour ce champ
					if ($saisie = saisies_chercher($saisies_objet, $champ)) {
						// On modifie son nom
						$saisie['options']['nom'] = $objet . '[' . $saisie['options']['nom'] . ']';
						// On modifie son obligatoire suivant la config
						$saisie['options']['obligatoire'] = in_array('obligatoire', $config_champ) ? 'oui' : false;
						// On ajoute la saisie
						$saisies_a_utiliser[] = $saisie;
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
				
				// On teste s'il faut un groupe de champs ou pas pour cet objet
				if ($legend = $config["activer_groupe_$objet"]) {
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
function formulaires_profil_charger_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
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
	
	//var_dump($contexte);
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
function formulaires_profil_verifier_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
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
function formulaires_profil_traiter_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
	//$retours = formulaires_editer_objet_traiter('profil', $id_profil, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	
	return $retours;
}
