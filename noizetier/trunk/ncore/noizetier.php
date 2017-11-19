<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// -----------------------------------------------------------------------
// ------------------------- TYPES DE NOISETTE ---------------------------
// -----------------------------------------------------------------------

/**
 * Stocke les descriptions des types de noisette en distinguant les types de noisette obsolètes, les types de
 * noisettes modifiés et les types de noisettes nouveaux.
 * Chaque description de type de noisette est un tableau associatif dont tous les index possibles - y compris
 * la signature - sont initialisés quelque soit le contenu du fichier YAML.
 *
 * Les types de noisettes sont stockés dans la table `spip_noizetier_noisettes`.
 *
 * @package SPIP\NOIZETIER\NCORE\TYPE_NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $types_noisette
 *        Tableau associatif à 3 entrées fournissant les descriptions des types de noisettes nouveaux, obsolètes
 *        et modifiés:
 *        `a_effacer` : liste des identifiants de type de noisette devenus obsolètes.
 *        `a_changer` : liste des descriptions des types de noisette dont le fichier YAML a été modifié.
 *        `a_ajouter` : liste des descriptions des nouveaux types de noisette.
 *        Si $recharger est à `true`, seul l'index `nouvelles` est fourni dans le tableau $types_noisette.
 * @param bool   $recharger
 *        Indique si le chargement en cours est forcé ou pas. Cela permet à la fonction N-Core ou au service
 *        concerné d'optimiser le traitement sachant que seules les types de noisette nouveaux sont fournis.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function noizetier_type_noisette_stocker($plugin, $types_noisettes, $recharger) {

	$retour = true;

	// Mise à jour de la table des noisettes 'spip_noizetier_noisettes'.
	$from = 'spip_noizetier_noisettes';

	// -- Suppression des noisettes obsolètes ou de toute les noisettes d'un coup si on est en mode
	//    rechargement forcé.
	if (sql_preferer_transaction()) {
		sql_demarrer_transaction();
	}
	$where = array('plugin=' . sql_quote($plugin));
	if ($recharger) {
		sql_delete($from, $where);
	} elseif (!empty($types_noisettes['a_effacer'])) {
		$where[] = sql_in('noisette', $types_noisettes['a_effacer']);
		sql_delete($from, $where);
	}
	// -- Update des pages modifiées
	if (!empty($types_noisettes['a_changer'])) {
		sql_replace_multi($from, $types_noisettes['a_changer']);
	}
	// -- Insertion des nouvelles pages
	if (!empty($types_noisettes['a_ajouter'])) {
		sql_insertq_multi($from, $types_noisettes['a_ajouter']);
	}
	if (sql_preferer_transaction()) {
		sql_terminer_transaction();
	}

	return $retour;
}

/**
 * Complète la description d'un type de noisette issue de la lecture de son fichier YAML.
 *
 * Le noiZetier phrase le type de noisette pour détecter son type et sa composition éventuelle.
 *
 * @package SPIP\NOIZETIER\NCORE\TYPE_NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description du type de noisette issue de la lecture du fichier YAML. Suivant le plugin utilisateur elle
 *        nécessite d'être compléter avant son stockage.
 *
 * @return array
 *        Description du type de noisette éventuellement complétée par le plugin utilisateur.
 */
function noizetier_type_noisette_completer($plugin, $description) {

	// Initialiser les composants de l'identifiant du type de noisette:
	// - type_page-type_noisette si le type de noisette est dédié uniquement à une page
	// - type_page-composition-type_noisette si le type de noisette est dédié uniquement à une composition
	// - type_noisette sinon
	$description['type'] = '';
	$description['composition'] = '';
	$identifiants = explode('-', $description['noisette']);
	if (isset($identifiants[1])) {
		$description['type'] = $identifiants[0];
	}
	if (isset($identifiants[2])) {
		$description['composition'] = $identifiants[1];
	}

	return $description;
}

/**
 * Renvoie la description brute d'un type de noisette sans traitement typo ni désérialisation des champs de type
 * tableau sérialisé.
 *
 * Le noiZetier lit la description du type de noisette concerné dans la table `spip_noizetier_noisettes`.
 *
 * @package SPIP\NOIZETIER\NCORE\TYPE_NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $type_noisette
 *        Identifiant du type de noisette.
 *
 * @return array
 *        Tableau de la description du type de noisette. Les champs textuels et les champs de type tableau sérialisé
 *        sont retournés en l'état.
 */
function noizetier_type_noisette_decrire($plugin, $noisette) {

	// Chargement de toute la configuration de la noisette en base de données.
	// Les données sont renvoyées brutes sans traitement sur les textes ni les tableaux sérialisés.
	$where = array('plugin=' . sql_quote($plugin), 'noisette=' . sql_quote($noisette));
	$description = sql_fetsel('*', 'spip_noizetier_noisettes', $where);

	return $description;
}

/**
 * Renvoie l'information brute demandée pour l'ensemble des types de noisette utilisés
 * ou toute les descriptions si aucune information n'est explicitement demandée.
 *
 * @package SPIP\NOIZETIER\NCORE\TYPE_NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $information
 *        Identifiant d'un champ de la description d'un type de noisette ou `signature`.
 *        Si l'argument est vide, la fonction renvoie les descriptions complètes et si l'argument est
 *        un champ invalide la fonction renvoie un tableau vide.
 *
 * @return array
 *        Tableau de la forme `[noisette] = information ou description complète`.
 */
function noizetier_type_noisette_lister($plugin, $information = '') {

	$where = array('plugin=' . sql_quote($plugin));
	$select = $information ? array('noisette', $information) : '*';
	if ($info_noisettes = sql_allfetsel($select, 'spip_noizetier_noisettes', $where)) {
		if ($information) {
			$info_noisettes = array_column($info_noisettes, $information, 'noisette');
		} else {
			$info_noisettes = array_column($info_noisettes, null, 'noisette');
		}
	}

	return $info_noisettes;
}


// -----------------------------------------------------------------------
// ----------------------------- NOISETTES -------------------------------
// -----------------------------------------------------------------------

/**
 * Stocke la description d'une nouvelle noisette et calcule son identifiant unique, ou met à jour les paramètres
 * d'affichage d'une noisette existante.
 *
 * @package SPIP\NOIZETIER\NCORE\NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description de la noisette. Soit la description ne contient pas l'id de la noisette et c'est un ajout,
 *        soit la description contient l'id et c'est une mise à jour.
 *
 * @return int
 *        Id de la noisette de type entier ou 0 en cas d'erreur.
 */
function noizetier_noisette_stocker($plugin, $description) {

	// Mise à jour en base de données.
	if (empty($description['id_noisette'])) {
		// Insertion de la nouvelle noisette.
		$id_noisette = sql_insertq('spip_noizetier', $description);
	} else {
		// On sauvegarde l'id de la noisette et on le retire de la description pour éviter une erreur à l'update.
		$id_noisette = intval($description['id_noisette']);
		unset($description['id_noisette']);

		// Mise à jour de la noisette.
		$where = array('id_noisette=' . $id_noisette);
		if (!sql_updateq('spip_noizetier', $description, $where)) {
			$id_noisette = 0;
		}
	}

	if ($id_noisette) {
		// On invalide le cache si le stockage a fonctionné.
		include_spip('inc/invalideur');
		suivre_invalideur("id='noisette/$id_noisette'");
	}

	return $id_noisette;
}

/**
 * Compléte la description fournie avec les champs propres au noizetier, à savoir, ceux identifiant
 * la page/composition ou l'objet et le bloc.
 * On parse le squelette pour identifier les données manquantes.
 *
 * @package SPIP\NOIZETIER\NCORE\NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description  par défaut de la noisette.
 *
 * @return array
 *        Description de la noisette complétée avec les champs de type de page, composition, bloc et
 *        de l'objet concerné si cela est le cas. Le champ conteneur est lui supprimé car non stocké en
 *        base de données.
 */
function noizetier_noisette_completer($plugin, $description) {

	if (!empty($description['conteneur'])) {
		//
		$complement = array(
			'type'        => '',
			'composition' => '',
			'objet'       => '',
			'id_objet'    => 0,
			'bloc'        => ''
		);

		// On désérialise le conteneur et après on traite les compléments.
		$conteneur = unserialize($description['conteneur']);

		if (!empty($conteneur['squelette'])) {
			$squelette = strtolower($conteneur['squelette']);

			if (!empty($conteneur['objet']) and !empty($conteneur['id_objet']) and ($id = intval($conteneur['id_objet']))) {
				// Objet
				$complement['objet'] = $conteneur['objet'];
				$complement['id_objet'] = $id;
				$complement['bloc'] = $conteneur['squelette'];
			} else {
				$page = basename($squelette);
				$identifiants_page = explode('-', $page, 2);
				if (!empty($identifiants_page[1])) {
					// Forcément une composition
					$complement['type'] = $identifiants_page[0];
					$complement['composition'] = $identifiants_page[1];
				} else {
					// Page simple
					$complement['type'] = $identifiants_page[0];
				}
				$bloc = dirname($squelette);
				if ($bloc != '.') {
					$complement['bloc'] = basename($bloc);
				}
			}
			$description = array_merge($description, $complement);

			// On supprime l'index 'conteneur' qui n'est pas utile pour le noiZetier qui utilise uniquement les
			// données de page et d'objet venant d'être ajoutées et l'id du conteneur.
			unset($description['conteneur']);
		}
	}

	return $description;
}

/**
 * Positionne une noisette à un rang différent que celui qu'elle occupe dans le conteneur.
 *
 * @package SPIP\NOIZETIER\NCORE\NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description complète de la noisette.
 * @param int    $rang_destination
 *        Position à laquelle ranger la noisette au sein du conteneur.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function noizetier_noisette_ranger($plugin, $description, $rang_destination) {

	// Initialisation de la sortie.
	$retour = false;

	if (isset($description['id_noisette']) and ($id = intval($description['id_noisette']))) {
		$where = array('id_noisette=' . $id);
		$update = array('rang' => $rang_destination);
		if (sql_updateq('spip_noizetier', $update, $where)) {
			$retour = true;
		}
	}

	return $retour;
}

/**
 * Retire, de l'espace de stockage, une noisette donnée de son conteneur.
 *
 * @package SPIP\NOIZETIER\NCORE\NOISETTE
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $description
 *        Description complète de la noisette.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function noizetier_noisette_destocker($plugin, $description) {

	// Initialisation de la sortie.
	$retour = false;

	// Calcul de la clause where à partir de l'id du conteneur.
	if (isset($description['id_noisette'])) {
		$where = array('id_noisette=' . intval($description['id_noisette']));

		// Suppression de la noisette.
		if (sql_delete('spip_noizetier', $where)) {
			$retour = true;
		}
	}

	return $retour;
}


/**
 * Renvoie un champ ou toute la description des noisettes d'un conteneur ou de tous les conteneurs.
 * Le tableau retourné est indexé soit par identifiant de noisette soit par identifiant du conteneur et rang.
 *
 * @package SPIP\NOIZETIER\NCORE\NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $conteneur
 *        Tableau descriptif du conteneur ou identifiant du conteneur ou vide si on souhaite adresser tous les
 *        conteneurs.
 * @param string $information
 *        Identifiant d'un champ de la description d'une type de noisette.
 *        Si l'argument est vide, la fonction renvoie les descriptions complètes et si l'argument est
 *        un champ invalide la fonction renvoie un tableau vide.
 * @param string $cle
 *        Champ de la description d'une noisette servant d'index du tableau. En général on utilisera soit `id_noisette`
 *        soit `rang`.
 *
 * @return array
 *        Tableau de la liste des informations demandées indexé par identifiant de noisette ou par rang.
 */
function noizetier_noisette_lister($plugin, $conteneur = array(), $information = '', $cle = 'rang') {

	// Initialisation du tableau de sortie.
	$noisettes = array();

	// Construction du where et du order by en fonction du conteneur qui est soit un squelette,
	// soit un squelette d'un objet donné, soit vide (on veut toutes les noisettes du plugin).
	$where = array('plugin=' . sql_quote($plugin));
	if ($conteneur) {
		// On sélectionne le contenant par son identifiant qui est stocké dans la table.
		if (is_array($conteneur)) {
			$id_conteneur = noizetier_conteneur_identifier($plugin, $conteneur);
		} else {
			$id_conteneur = $conteneur;
		}
		$where[] = array('id_conteneur=' . sql_quote($id_conteneur));
		$order_by = array('rang');
	} else {
		// On veut toutes les noisettes, on ordonne toujours le tableau résultant par conteneur et par rang dans chaque
		// conteneur.
		$order_by = array('id_conteneur', 'rang');
	}

	// Construction du select en fonction des informations à retourner.
	$select = $information ? array_merge(array('id_conteneur', 'rang', 'id_noisette'), array($information)) : '*';

	if ($table_noisettes = sql_allfetsel($select, 'spip_noizetier', $where, '', $order_by)) {
		if ($cle == 'rang') {
			// On demande un rangement par rang.
			// Il faut tenir compte du fait que la liste est réduite à un conteneur ou pas.
			foreach ($table_noisettes as $_noisette) {
				if ($conteneur) {
					$noisettes[$_noisette['rang']] = $information
						? array($information => $_noisette[$information])
						: $_noisette;
				} else {
					$noisettes[$_noisette['id_conteneur']][$_noisette['rang']] = $information
						? array($information => $_noisette[$information])
						: $_noisette;
				}
			}
		} else {
			// On demande un rangement par id_noisette
			$noisettes = $information
				? array_column($table_noisettes, $information, 'id_noisette')
				: array_column($table_noisettes, null, 'id_noisette');
		}
	}

	return $noisettes;
}


/**
 * Renvoie la description brute d'une noisette sans traitement typo des champs textuels ni désérialisation
 * des champs de type tableau sérialisé.
 *
 * @package SPIP\NOIZETIER\NCORE\NOISETTE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed  $noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un couple (conteneur, rang).
 *
 * @return array
 *        Tableau de la description du type de noisette. Les champs textuels et les champs de type tableau sérialisé
 *        sont retournés en l'état.
 */
function noizetier_noisette_decrire($plugin, $noisette) {

	$description = array();

	if (!is_array($noisette)) {
		// L'identifiant est l'id unique de la noisette. Il faut donc parcourir le tableau pour trouver la
		// noisette désirée
		// => C'est la méthode optimale pour le stockage noiZetier.
		$where = array('id_noisette=' . intval($noisette));
	} elseif (isset($noisette['id_conteneur']) and isset($noisette['rang'])) {
		// L'identifiant est un tableau associatif fournissant l'id du conteneur et le rang.
		$where = array('id_conteneur=' . sql_quote($noisette['id_conteneur']), 'rang=' . intval($noisette['rang']));
	} else {
		$where = array();
	}

	if ($where) {
		$description = sql_fetsel('*', 'spip_noizetier', $where);
	}

	return $description;
}


// -----------------------------------------------------------------------
// ----------------------------- CONTENEURS ------------------------------
// -----------------------------------------------------------------------

/**
 * Construit un identifiant unique pour le conteneur de noisettes.
 * Pour le noiZetier, un conteneur est toujours un squelette, soit générique soit d'un objet donné.
 *
 * @package SPIP\NOIZETIER\NCORE\CONTENEUR
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array $conteneur
 *        Tableau associatif descriptif du conteneur accueillant la noisette. Un conteneur peut-être un squelette seul
 *        ou associé à un contexte d'utilisation et dans ce cas il possède un index `squelette` ou un objet quelconque
 *        sans lien avec un squelette. Dans tous les cas, les index, à l'exception de `squelette`, sont spécifiques
 *        à l'utilisation qui en est faite par le plugin.
 *
 * @return string
 */
function noizetier_conteneur_identifier($plugin, $conteneur) {

	// On initialise l'identifiant à vide.
	$identifiant = '';

	if ($conteneur) {
		// Le nom du squelette en premier si il existe (normalement toujours).
		if (!empty($conteneur['squelette'])) {
			$identifiant .= $conteneur['squelette'];
		}

		// L'objet et son id si on est en présence d'un objet.
		if (!empty($conteneur['objet'])	and !empty($conteneur['id_objet']) and intval($conteneur['id_objet'])) {
			$identifiant .= ($identifiant ? '|' : '') . "{$conteneur['objet']}|{$conteneur['id_objet']}";
		}
	}

	return $identifiant;
}


/**
 * Retire, de l'espace de stockage, toutes les noisettes d'un conteneur.
 *
 * @package SPIP\NOIZETIER\NCORE\CONTENEUR
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $conteneur
 *        Tableau descriptif du conteneur ou identifiant du conteneur.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function noizetier_conteneur_destocker($plugin, $conteneur) {

	// Initialisation de la sortie.
	$retour = false;

	// Calcul de l'id du conteneur en fonction du mode d'appel de la fonction.
	if (is_array($conteneur)) {
		$id_conteneur = noizetier_conteneur_identifier($plugin, $conteneur);
	} else {
		$id_conteneur = $conteneur;
	}

	if ($id_conteneur) {
		// Suppression de toutes les noisettes du conteneur.
		$where = array('id_conteneur=' . sql_quote($id_conteneur));
		if (sql_delete('spip_noizetier', $where)) {
			$retour = true;
		}
	}

	return $retour;
}


function noizetier_noisette_config_ajax() {

	// On détermine la valeur par défaut de l'ajax des noisettes qui est stocké dans la configuration du plugin.
	include_spip('inc/config');
	$defaut_ajax = lire_config('noizetier/ajax_noisette') == 'on' ? true : false;

	return $defaut_ajax;
}
