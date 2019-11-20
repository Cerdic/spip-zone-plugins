<?php
/**
 * Ce fichier contient les cas d'utilisation des pipelines d'affichage, d'édition et d'autorisation.
 *
 * @package SPIP\ARCHOBJET\PIPELINES
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Affichage, dans la fiche d'un objet autorisé à être archivé, d'un bloc identifiant
 * l'état d'archivage, la date d'archivage et la raison.
 *
 * @pipeline affiche_milieu
 *
 * @param array $flux Données de contexte fournies au pipeline
 *
 * @return array Données de contexte complétées par la fonction
 */
function archobjet_affiche_milieu($flux){

	if (isset($flux['args']['exec'])) {
		// Initialisation de la page du privé
		$exec = $flux['args']['exec'];

		// Lecture des tables autorisées à l'archivage
		include_spip('inc/config');
		$configuration = lire_config('archobjet', array());

		if (
			($objet_exec = trouver_objet_exec($exec))
			and !$objet_exec['edition']
			and ($table = $objet_exec['table_objet_sql'])
			and in_array($table, $configuration['objets_archivables'])
			and ($objet = $objet_exec['type'])
			and ($id_objet = intval($flux['args'][$objet_exec['id_table_objet']]))
			and autoriser('archivage', $objet, $id_objet)
		) {
			// Page d'un objet archivable : message d'archivage si besoin
			// -- Etat d'archivage.
			include_spip('inc/archobjet_objet');
			$etat_archivage = objet_etat_archivage(
				$objet,
				$id_objet,
				array(
					'table' => $table,
					'champ_id' => $objet_exec['id_table_objet']
				)
			);

			// -- Vérifier les conditions d'affichage du message d'archivage.
			if (
				($etat_archivage['est_archive'] == 1)
				or (
					$configuration['consigner_desarchivage']
					and ($etat_archivage['est_archive'] == 0)
					and $etat_archivage['date_archive']
					)
				) {
				// Construction du contexte pour l'inclusion affichant le message d'archivage.
				$contexte = array_merge(
					$etat_archivage,
					array(
						'etat'     => $etat_archivage['est_archive'] ? 'archive' : 'desarchive',
						'objet'    => $objet,
						'id_objet' => $id_objet,
					)
				);

				if ($texte = recuperer_fond('prive/squelettes/inclure/inc-objet_archive', $contexte)) {
					if ($pos = strpos($flux['data'],'<!--affiche_milieu-->')) {
						$flux['data'] = substr_replace($flux['data'], $texte, $pos, 0);
					} else {
						$flux['data'] .= $texte;
					}
				}
			}
		}
	}

	return $flux;
}

/**
 * Insertion dans le pipeline boite_infos (SPIP)
 * Rajouter, pour les objets archivables un bouton d'archivage ou de désarchivage suivant l'état courant.
 *
 * @pipeline boite_infos
 *
 * @param $flux array Le contexte du pipeline
 *
 * @return $flux array Le contexte du pipeline modifié
 */
function archobjet_boite_infos($flux) {

	if (isset($flux['args']['type'])) {
		// Initialisation du type d'objet concerné.
		$objet = $flux['args']['type'];

		// Lecture des tables autorisées à l'archivage
		include_spip('inc/config');
		$tables_autorisees = lire_config('archobjet/objets_archivables', array());

		if (
			($objet_exec = trouver_objet_exec($objet))
			and !$objet_exec['edition']
			and ($table = $objet_exec['table_objet_sql'])
			and in_array($table, $tables_autorisees)
			and ($id_objet = intval($flux['args']['id']))
			and autoriser('archivage', $objet, $id_objet)
		) {
			// Page d'un objet archivable : afficher le bouton adéquat.
			// -- Acquérir l'état d'archivage.
			include_spip('inc/archobjet_objet');
			$etat_archivage = objet_etat_archivage(
				$objet,
				$id_objet,
				array(
					'table' => $table,
					'champ_id' => $objet_exec['id_table_objet']
				)
			);

			// -- Inclure le bouton archiver ou désarchiver
			$contexte = array_merge(
				$etat_archivage,
				array(
					'action'   => !$etat_archivage['est_archive'] ? 'archiver' : 'desarchiver',
					'objet'    => $objet,
					'id_objet' => $id_objet,
				)
			);
			if ($bouton = recuperer_fond('prive/squelettes/inclure/inc-bouton_archivage', $contexte)) {
				$flux['data'] .= $bouton;
			}
		}
	}

	return $flux;
}
/**
 * Filtrer les boucles pour ne pas afficher les objets archivés par défaut sauf si un critère
 * d'archivage explicite ou conditionnel existe.
 *
 * @pipeline post_boucle
 *
 * @param Boucle $boucle
 *        Objet boucle de SPIP correspond à la boucle en cours de traitement.
 *
 * @return Boucle
 *         La boucle dont la condition `where` a été modifiée ou pas.
 */
function archobjet_post_boucle($boucle){

	// Initialisation de la table sur laquelle porte le critère
	include_spip('base/objets');
	$table = table_objet_sql($boucle->id_table);
	$id_table = id_table_objet($boucle->id_table);

	if ($table) {
		// Vérifier que la table fait bien partie de la liste autorisée à utiliser l'archivage.
		include_spip('inc/config');
		$tables_autorisees = lire_config('archobjet/objets_archivables', array());
		if (in_array($table, $tables_autorisees)) {
			// On cherche un critère d'archivage explicite parmi :
			// - {est_archive = 0} ou {est_archivee = 1}
			// - {archive} ou {!archive}
			// Ou un critère explicite sur l'id de la table comme {id_article} ou {id_article=xxx}
			// --> Si il existe un tel critère, alors on n'exclut pas les archives par défaut.
			$critere_archive_explicite = false;
			$criteres = $boucle->criteres;
			foreach ($criteres as $_critere) {
				if (
					($_critere->op == 'archive')
					or ($_critere->op == $id_table)
					or (!empty($_critere->param[0][0]->texte)
						and (
							($_critere->param[0][0]->texte == 'est_archive')
							or ($_critere->param[0][0]->texte == $id_table)
						)
					)
				) {
					$critere_archive_explicite = true;
					break;
				}
			}

			// Si on a trouvé aucun critère explicite, il se peut qu'il y ait un critère conditionnel du type {id_?} :
			// dans ce cas, la condition a déjà été calculée mais pas de la bonne façon.
			// -> Il faut donc la corriger et ne pas rajouter la condition par défaut.
			if (!$critere_archive_explicite) {
				$conditions = $boucle->where;
				foreach ($conditions as $_cle => $_condition) {
					if (
						(strpos($_condition[0], '?') !== false)
						and (strpos($_condition[1], 'est_archive') !== false)
					) {
						// On met à jour cette condition pour que l'absence de est_archive dans l'env
						// implique l'ajout de est_archive=0.
						// TODO : faire évoluer le core pour calculer cette condition avec une fonction spéficique.
						$where_est_article[0] = $_condition[0];
						$where_est_article[1] = $_condition[1];
						$where_est_article[2] = array("'='", "'est_archive'", 0);
						$where_est_article[3] = $_condition[3][3];
						$boucle->where[$_cle] = $where_est_article;
						$critere_archive_explicite = true;
						break;
					}
				}

				// Aucun critère d'archivage explicite ni conditionnel : on peut filtrer la boucle en excluant les archives.
				if (!$critere_archive_explicite) {
					$boucle->where[] = array("'='", "'est_archive'", 0);
				}
			}
		}
	}

	return $boucle;
}

function archobjet_lister_champs_selection_conditionnelle($flux) {

	if (isset($flux['args']['table'])
		and $flux['args']['table']
	) {
		$table = $flux['args']['table'];

		// Vérifier que la table fait bien partie de la liste autorisée à utiliser l'archivage.
		include_spip('inc/config');
		$tables_autorisees = lire_config('archobjet/objets_archivables', array());
		if (in_array($table, $tables_autorisees)) {
			$flux['data'][] = 'est_archive';
		}
	}

	return $flux;
}


