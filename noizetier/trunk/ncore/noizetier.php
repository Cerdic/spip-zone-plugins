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
 * Les types de noisettes sont stockés dans la table `spip_types_noisettes`.
 *
 * @package SPIP\NOIZETIER\TYPE_NOISETTE\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $types_noisettes
 *        Tableau associatif à 3 entrées fournissant les descriptions des types de noisettes nouveaux, obsolètes
 *        et modifiés:
 *        `a_effacer` : liste des identifiants de type de noisette devenus obsolètes.
 *        `a_changer` : liste des descriptions des types de noisette dont le fichier YAML a été modifié.
 *        `a_ajouter` : liste des descriptions des nouveaux types de noisette.
 *        Si $recharger est à `true`, seul l'index `a_ajouter` est fourni dans le tableau $types_noisette.
 * @param bool   $recharger
 *        Indique si le chargement en cours est forcé ou pas. Cela permet à la fonction N-Core ou au service
 *        concerné d'optimiser le traitement sachant que seules les types de noisette nouveaux sont fournis.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function noizetier_type_noisette_stocker($plugin, $types_noisettes, $recharger) {

	$retour = true;

	// Mise à jour de la table des noisettes 'spip_types_noisettes'.
	$from = 'spip_types_noisettes';

	// -- Suppression des types de noisette obsolètes ou de tous les types de noisette d'un coup si on est en mode
	//    rechargement forcé.
	if (sql_preferer_transaction()) {
		sql_demarrer_transaction();
	}
	$where = array('plugin=' . sql_quote($plugin));
	if ($recharger) {
		sql_delete($from, $where);
	} elseif (!empty($types_noisettes['a_effacer'])) {
		$where[] = sql_in('type_noisette', $types_noisettes['a_effacer']);
		sql_delete($from, $where);
	}
	// -- Update des types de noisettes modifiés
	if (!empty($types_noisettes['a_changer'])) {
		sql_replace_multi($from, $types_noisettes['a_changer']);
	}
	// -- Insertion des nouveaux types de noisette
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
 * @package SPIP\NOIZETIER\TYPE_NOISETTE\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description du type de noisette issue de la lecture du fichier YAML. Suivant le plugin utilisateur elle
 *        nécessite d'être compléter avant son stockage.
 *
 * @return array
 *        Description du type de noisette complétée avec le type de page et la composition (éventuellement vides).
 */
function noizetier_type_noisette_completer($plugin, $description) {

	// Initialiser les composants de l'identifiant du type de noisette:
	// - type_page-type_noisette si le type de noisette est dédié uniquement à une page
	// - type_page-composition-type_noisette si le type de noisette est dédié uniquement à une composition
	// - type_noisette sinon
	$description['type'] = '';
	$description['composition'] = '';
	$identifiants = explode('-', $description['type_noisette']);
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
 * Le noiZetier lit la description du type de noisette concerné dans la table `spip_types_noisettes`.
 *
 * @package SPIP\NOIZETIER\TYPE_NOISETTE\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $type_noisette
 *        Identifiant du type de noisette.
 *
 * @return array
 *        Tableau de la description du type de noisette. Les champs textuels et les champs de type tableau sérialisé
 *        sont retournés en l'état, le timestamp `maj n'est pas fourni.
 */
function noizetier_type_noisette_decrire($plugin, $type_noisette) {

	// Table des types de noisette.
	$from = 'spip_types_noisettes';

	// Chargement de toute la configuration de la noisette en base de données sauf le timestamp 'maj'.
	// Les données sont renvoyées brutes sans traitement sur les textes ni les tableaux sérialisés.
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table = $trouver_table($from);
	$select = array_diff(array_keys($table['field']), array('maj'));

	$where = array('plugin=' . sql_quote($plugin), 'type_noisette=' . sql_quote($type_noisette));
	$description = sql_fetsel($select, $from, $where);

	return $description;
}

/**
 * Renvoie l'information brute demandée pour l'ensemble des types de noisette utilisés
 * ou toute les descriptions si aucune information n'est explicitement demandée.
 *
 * @package SPIP\NOIZETIER\TYPE_NOISETTE\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $information
 *        Identifiant d'un champ de la description d'un type de noisette.
 *        Si l'argument est vide, la fonction renvoie les descriptions complètes et si l'argument est
 *        un champ invalide la fonction renvoie un tableau vide.
 *
 * @return array
 *        Tableau de la forme `[type_noisette] = information ou description complète`. Les champs textuels et
 *        les champs de type tableau sérialisé sont retournés en l'état, le timestamp `maj n'est pas fourni.
 */
function noizetier_type_noisette_lister($plugin, $information = '') {

	// Initialiser le tableau de sortie en cas d'erreur
	$types_noisettes = array();

	$from = 'spip_types_noisettes';
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table = $trouver_table($from);
	$champs = array_keys($table['field']);
	if ($information) {
		// Si une information précise est demandée on vérifie sa validité
		$information_valide = in_array($information, $champs);
		$select = array('type_noisette', $information);
	} else {
		// Tous les champs sauf le timestamp 'maj' sont renvoyés.
		$select = array_diff($champs, array('maj'));
	}
	$where = array('plugin=' . sql_quote($plugin));

	if ((!$information or ($information and $information_valide))
	and ($types_noisettes = sql_allfetsel($select, $from, $where))) {
		if ($information) {
			$types_noisettes = array_column($types_noisettes, $information, 'type_noisette');
		} else {
			$types_noisettes = array_column($types_noisettes, null, 'type_noisette');
		}
	}

	return $types_noisettes;
}

/**
 * Renvoie la configuration par défaut de l'ajax à appliquer pour la compilation des noisettes.
 * Cette information est utilisée si la description YAML d'un type noisette ne contient pas de tag ajax
 * ou contient un tag ajax à `defaut`.
 *
 * @package SPIP\NOIZETIER\TYPE_NOISETTE\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return bool
 * 		`true` si par défaut une noisette est insérée en ajax, `false` sinon.
 */
function noizetier_type_noisette_initialiser_ajax($plugin) {

	// La valeur Ajax par défaut est inscrite dans la configuration du plugin.
	include_spip('inc/config');
	$defaut_ajax = lire_config("${plugin}/ajax_noisette");

	return $defaut_ajax;
}


// -----------------------------------------------------------------------
// ----------------------------- NOISETTES -------------------------------
// -----------------------------------------------------------------------

/**
 * Stocke la description d'une nouvelle noisette et calcule son identifiant unique, ou met à jour les paramètres
 * d'affichage d'une noisette existante.
 *
 * @package SPIP\NOIZETIER\NOISETTE\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noizetier ou
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
		// On s'assure que la description contient bien le plugin et alors on insère la nouvelle noisette.
		$id_noisette = 0;
		if (isset($description['plugin']) and ($description['plugin'] == $plugin)) {
			// Insertion de la nouvelle noisette.
			$id_noisette = sql_insertq('spip_noisettes', $description);
		}
	} else {
		// On sauvegarde l'id de la noisette et on le retire de la description pour éviter une erreur à l'update.
		$id_noisette = intval($description['id_noisette']);
		unset($description['id_noisette']);

		// Mise à jour de la noisette.
		$where = array('id_noisette=' . $id_noisette, 'plugin=' . sql_quote($plugin));
		if (!sql_updateq('spip_noisettes', $description, $where)) {
			$id_noisette = 0;
		}
	}

	return $id_noisette;
}

/**
 * Transfère une noisette d'un conteneur vers un autre à un rang donné.
 * Le rang destination n'est pas vérifié lors du rangement dans le conteneur destination. Il convient
 * à l'appelant de vérifier que le rang est libre.
 * La description complète de la noisette est renvoyée avec mise à jour des champs de positionnement (id_conteneur,
 * conteneur, rang_noisette et profondeur).
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description de la noisette à changer de conteneur.
 * @param string $id_conteneur
 *        Identifiant unique sous forme de chaine du conteneur destination.
 * @param int    $rang
 *        Rang où positionner la noisette dans le conteneur destination. Il faut toujours vérifier au préalable
 *        que ce rang est libre.
 * @param int    $profondeur
 *        Profondeur de la noisette à sa nouvelle position.
 *
 * @return array
 *         Description de la noisette mise à jour avec les informations sur le nouvel emplacement
 */
function noizetier_noisette_changer_conteneur($plugin, $description, $id_conteneur, $rang, $profondeur) {

	// On rajoute la description à son emplacement destination en prenant soin de modifier les index id_conteneur,
	// conteneur et rang_noisette qui doivent représenter le conteneur destination.
	include_spip('inc/ncore_conteneur');
	$description['conteneur'] = serialize(conteneur_construire($plugin, $id_conteneur));
	$description['id_conteneur'] = $id_conteneur;
	$description['rang_noisette'] = $rang;
	$description['profondeur'] = $profondeur;

	// On met à jour l'objet en base
	// On sauvegarde l'id de la noisette et on le retire de la description pour éviter une erreur à l'update.
	$id_noisette = intval($description['id_noisette']);
	unset($description['id_noisette']);

	// Mise à jour de la noisette.
	$where = array('id_noisette=' . $id_noisette, 'plugin=' . sql_quote($plugin));
	sql_updateq('spip_noisettes', $description, $where);

	// On remet l'ide de la noisette pour renvoyer la description complète
	$description['id_noisette'] = $id_noisette;

	return $description;
}

/**
 * Complète la description fournie avec les champs propres au noiZetier, à savoir, ceux identifiant
 * la page/composition ou l'objet et le bloc.
 * On parse le squelette pour identifier les données manquantes.
 *
 * @package SPIP\NOIZETIER\NOISETTE\SERVICE
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

		// La description de la noisette contient l'id du conteneur : on le décompose car il contient certains
		// des champs complémentaires.
		$id_conteneur = $description['id_conteneur'];
		$conteneur_etendu = noizetier_conteneur_decomposer($id_conteneur);

		if ($conteneur_etendu) {
			// On ajoute les index manquants avec leur valeur par défaut
			$conteneur_etendu = array_merge($complement, $conteneur_etendu);
			// On filtre les index inutiles comme squelette par exemple.
			$complement = array_intersect_key($conteneur_etendu, $complement);
		}

		// Ajout du complément à la description.
		$description = array_merge($description, $complement);
	}

	return $description;
}

/**
 * Positionne une noisette à un rang différent que celui qu'elle occupe dans le conteneur.
 *
 * @package SPIP\NOIZETIER\NOISETTE\SERVICE
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
		$where = array('id_noisette=' . $id, 'plugin=' . sql_quote($plugin));
		$update = array('rang_noisette' => $rang_destination);
		if (sql_updateq('spip_noisettes', $update, $where)) {
			$retour = true;
		}
	}

	return $retour;
}

/**
 * Retire, de l'espace de stockage, une noisette donnée de son conteneur.
 *
 * @package SPIP\NOIZETIER\NOISETTE\SERVICE
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
		$where = array('id_noisette=' . intval($description['id_noisette']), 'plugin=' . sql_quote($plugin));

		// Suppression de la noisette.
		if (sql_delete('spip_noisettes', $where)) {
			$retour = true;
		}
	}

	return $retour;
}

/**
 * Renvoie un champ ou toute la description des noisettes d'un conteneur ou de tous les conteneurs.
 * Le tableau retourné est indexé soit par identifiant de noisette soit par identifiant du conteneur et rang.
 *
 * @package SPIP\NOIZETIER\NOISETTE\SERVICE
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
function noizetier_noisette_lister($plugin, $conteneur = array(), $information = '', $cle = 'rang_noisette') {

	// Initialisation du tableau de sortie.
	$noisettes = array();

	// Construction du where et du order by en fonction du conteneur qui est soit un squelette,
	// soit un squelette d'un objet donné, soit vide (on veut toutes les noisettes du plugin).
	$where = array('plugin=' . sql_quote($plugin));
	if ($conteneur) {
		// On sélectionne le contenant par son identifiant qui est stocké dans la table.
		if (is_array($conteneur)) {
			include_spip('inc/ncore_conteneur');
			$id_conteneur = conteneur_identifier($plugin, $conteneur);
		} else {
			$id_conteneur = $conteneur;
		}
		$where[] = array('id_conteneur=' . sql_quote($id_conteneur));
		$order_by = array('rang_noisette');
	} else {
		// On veut toutes les noisettes, on ordonne toujours le tableau résultant par conteneur et par rang dans chaque
		// conteneur.
		$order_by = array('id_conteneur', 'rang_noisette');
	}

	// Construction du select en fonction des informations à retourner.
	$select = $information ? array_merge(array('id_conteneur', 'rang_noisette', 'id_noisette'), array($information)) : '*';

	if ($table_noisettes = sql_allfetsel($select, 'spip_noisettes', $where, '', $order_by)) {
		if ($cle == 'rang_noisette') {
			// On demande un rangement par rang.
			// Il faut tenir compte du fait que la liste est réduite à un conteneur ou pas.
			foreach ($table_noisettes as $_noisette) {
				if ($conteneur) {
					$noisettes[$_noisette['rang_noisette']] = $information
						? array($information => $_noisette[$information])
						: $_noisette;
				} else {
					$noisettes[$_noisette['id_conteneur']][$_noisette['rang_noisette']] = $information
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
 * @package SPIP\NOIZETIER\NOISETTE\SERVICE
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

	$where = array();
	if (!is_array($noisette)) {
		// L'identifiant est l'id unique de la noisette. Il faut donc parcourir le tableau pour trouver la
		// noisette désirée
		// => C'est la méthode optimale pour le stockage noiZetier.
		$where[] = 'id_noisette=' . intval($noisette);
	} elseif (isset($noisette['id_conteneur']) and isset($noisette['rang_noisette'])) {
		// L'identifiant est un tableau associatif fournissant l'id du conteneur et le rang.
		$where[] = 'id_conteneur=' . sql_quote($noisette['id_conteneur']);
		$where[] = 'rang_noisette=' . intval($noisette['rang_noisette']);
	}

	if ($where) {
		$where[] = 'plugin=' . sql_quote($plugin);
		$description = sql_fetsel('*', 'spip_noisettes', $where);
	}

	return $description;
}

/**
 * Renvoie la configuration par défaut de l'encapsulation d'une noisette.
 * Cette information est utilisée si le champ `encapsulation` de la noisette vaut `defaut`.
 *
 * @package SPIP\NOIZETIER\NOISETTE\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return string
 * 		Vaut `on` pour une encapsulation ou chaine vide sinon.
 */
function noizetier_noisette_initialiser_encapsulation($plugin) {

	// La capsule par défaut est inscrite dans la configuration du plugin.
	include_spip('inc/config');
	$defaut_capsule = lire_config("${plugin}/encapsulation_noisette");

	return $defaut_capsule;
}


// -----------------------------------------------------------------------
// ----------------------------- CONTENEURS ------------------------------
// -----------------------------------------------------------------------

/**
 * Vérifie la conformité des index du tableau représentant le conteneur et supprime les index inutiles, si besoin.
 * Pour le noiZetier, la vérification concerne uniquement les conteneurs non noisette. Dans ce cas, le conteneur
 * est toujours un squelette, soit générique soit d'un objet donné.
 *
 * @package SPIP\NOIZETIER\CONTENEUR\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur dont les index doivent être vérifiés.
 *
 * @return array
 *         Tableau du conteneur dont tous les index sont conformes (`squelette` et éventuellement `objet`, `id_objet`)
 *         ou tableau vide si non conforme.
 */
function noizetier_conteneur_verifier($plugin, $conteneur) {

	// Liste des index autorisés.
	static $index_conteneur = array('squelette', 'objet', 'id_objet');

	// On vérifie que les index autorisés sont les seuls retournés.
	$conteneur_verifie = array();
	if ($conteneur) {
		// L'index squelette doit toujours être présent.
		if ((isset($conteneur['squelette']) and $conteneur['squelette'])) {
			$conteneur = array_intersect_key($conteneur, array_flip($index_conteneur));
			if ((count($conteneur) == 1)
			or ((count($conteneur) == 3)
				and isset($conteneur['objet'], $conteneur['id_objet'])
				and $conteneur['objet']
				and intval($conteneur['id_objet']))) {
				// Le conteneur coincide avec un squelette de bloc générique ou d'un objet donné.
				$conteneur_verifie = $conteneur;
			}
		}
	}

	return $conteneur_verifie;
}

/**
 * Construit un identifiant unique pour le conteneur de noisettes hors les noisettes conteneur.
 * Pour le noiZetier, un conteneur est toujours un squelette, soit générique soit d'un objet donné.
 *
 * @package SPIP\NOIZETIER\CONTENEUR\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array $conteneur
 *        Tableau associatif descriptif du conteneur. Pour le noiZetier, les seuls index autorisés sont
 *        `squelette`, `objet` et `id_objet`.
 *
 * @return string
 *         L'identifiant calculé à partir du tableau.
 */
function noizetier_conteneur_identifier($plugin, $conteneur) {

	// On initialise l'identifiant à vide.
	$id_conteneur = '';

	// Les noisettes conteneur ont été identifiées par N-Core, inutile donc de s'en préoccuper.
	if ($conteneur) {
		// Le nom du squelette en premier si il existe (normalement toujours).
		if (!empty($conteneur['squelette'])) {
			$id_conteneur .= $conteneur['squelette'];
		}
		// L'objet et son id si on est en présence d'un objet.
		if (!empty($conteneur['objet'])	and !empty($conteneur['id_objet']) and intval($conteneur['id_objet'])) {
			$id_conteneur .= ($id_conteneur ? '|' : '') . "{$conteneur['objet']}|{$conteneur['id_objet']}";
		}
	}

	return $id_conteneur;
}

/**
 * Reconstruit le conteneur sous forme de tableau à partir de son identifiant unique (fonction inverse
 * de `noizetier_conteneur_identifier`).
 * N-Core ne fournit le conteneur pour les noisettes conteneur.
 * Pour les autres conteneurs, c'est au noiZetier de calculer le tableau.
 *
 * @package SPIP\NOIZETIER\CONTENEUR\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $id_conteneur
 *        Identifiant unique du conteneur.
 *
 * @return array
 *        Tableau représentatif du conteneur ou tableau vide en cas d'erreur.
 */
function noizetier_conteneur_construire($plugin, $id_conteneur) {

	// Il faut recomposer le tableau du conteneur à partir de son id.
	// N-Core s'occupe des noisettes conteneur; le noiZetier n'a donc plus qu'à traiter les autres conteneur,
	// à savoir ses conteneurs spécifiques.
	$conteneur = array();
	if ($id_conteneur) {
		$elements = explode('|', $id_conteneur);
		if (count($elements) == 1) {
			// C'est une page ou une composition : seul l'index squelette est positionné.
			$conteneur['squelette'] = $id_conteneur;
		} elseif (count($elements) == 3) {
			// C'est un objet
			// -- le type d'objet et son id
			$conteneur['objet'] = $elements[1];
			$conteneur['id_objet'] = $elements[2];
			// -- le squelette
			$conteneur['squelette'] = $elements[0];
		}
	}

	return $conteneur;
}


/**
 * Retire, de l'espace de stockage, toutes les noisettes d'un conteneur.
 * L'imbrication des conteneurs est gérée dans la fonction de service de N-Core.
 *
 * @package SPIP\NOIZETIER\CONTENEUR\SERVICE
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
		include_spip('inc/ncore_conteneur');
		$id_conteneur = conteneur_identifier($plugin, $conteneur);
	} else {
		$id_conteneur = $conteneur;
	}

	if ($id_conteneur) {
		// Suppression de toutes les noisettes du conteneur.
		$where = array('id_conteneur=' . sql_quote($id_conteneur), 'plugin=' . sql_quote($plugin));
		if (sql_delete('spip_noisettes', $where)) {
			$retour = true;
		}
	}

	return $retour;
}
