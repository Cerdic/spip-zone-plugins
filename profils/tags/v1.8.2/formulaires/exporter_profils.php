<?php
/**
 * Gestion du formulaire de d'exporter de profils depuis un tableur
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

include_spip('inc/profils');
include_spip('inc/saisies');

function formulaires_exporter_profils_saisies_dist($id_profil) {
	$saisies = array(
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'seulement_colonnes',
				'label_case' => _T('profil:exporter_champ_seulement_colonnes_label_case'),
				'pleine_largeur' => 'oui',
			),
		),
		'options' => array(
			'texte_submit' => _T('bouton_telecharger'),
			'inserer_debut' => '<h3 class="titrem">'._T('profil:exporter_titre').'</h3>'
		),
	);
	
	return $saisies;
}

function formulaires_exporter_profils_traiter_dist($id_profil) {
	refuser_traiter_formulaire_ajax();
	$retours = array();
	
	if ($profil = profils_recuperer_profil($id_profil) and $config = $profil['config']) {
		$colonnes = array();
		$donnees = array();
		
		foreach (array('auteur', 'organisation', 'contact') as $objet) {
			// Si c'est autre chose que l'utilisateur, faut le plugin qui va avec et que ce soit activé
			if ($objet == 'auteur' or (defined('_DIR_PLUGIN_CONTACTS') and $config["activer_$objet"])) {
				// Pour chaque chaque champ vraiment configuré
				if ($config[$objet]) {
					foreach ($config[$objet] as $champ => $config_champ) {
						// On prend les champs d'édition de profil uniquement
						if (in_array('edition', $config_champ)) {
							$colonnes[] = $objet.'_'.$champ;
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
									$colonnes[] = $objet.'_'.$coordonnee.'_'.$champ['type'].'_'.$nom;
								}
							}
						}
					}
				}
			}
		}
		
		// Si on demande tout, on va charger les données de chaque profil
		if (!_request('seulement_colonnes')) {
			// On récupère tous les auteurs de ce profil
			if ($ids = sql_allfetsel('id_auteur', 'spip_auteurs', 'id_profil = '.$profil['id_profil'])) {
				$ids = array_map('reset', $ids);
				foreach ($ids as $id_auteur) {
					$ligne = profils_recuperer_infos_simples($id_auteur, $profil['id_profil']);
					$donnees[] = array_values($ligne);
				}
			}
		}
		
		$exporter_csv = charger_fonction('exporter_csv', 'inc/');
		$exporter_csv($profil['identifiant'], $donnees, ',', $colonnes);
	}
	
	return true;
}
