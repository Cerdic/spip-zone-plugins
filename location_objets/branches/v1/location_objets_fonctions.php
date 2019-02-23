<?php
/**
 * Fonctions utiles au plugin Location d&#039;objets
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Fonctions
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Retourne le champ pour les services extras.
 *
 * @param mixed $service
 *          Le nom de ou des objets à utiliser comme service extra.
 * @param string $location_objet
 *          L'objet de la location
 * @param integer $id_location_objet
 *          L'id de l'objet de la location
 * @param array $contexte
 *          Les variables du contexte
 * @param array $options
 *          type_prix: prix_ht ou prix
 *          separateur_prix
 * @return array
 *          La saisies
 */
function objet_location_extras_champs($service, $location_objet_table, $id_location_objet, $contexte = array(), $options = array()) {
	include_spip('action/editer_liens');
	// Créer une variable pour chaque option
	foreach ($options as $cle => $valeur) {
		$$cle = $valeur;
	}

	// Le type de prix
	if (!$type_prix) {
		$type_prix = 'prix_ht';
	}

	if (!$separateur_prix) {
		$separateur_prix = '<span class="separateur"> - </span>';
	}

	$objet_associable = objet_associable($service);
	$data = array();

	if ($objet_associable) {
		if (function_exists('prix_par_objet')) {
			$fonction_prix = 'prix_par_objet';
		}

		list ($id_table_objet, $table_liens) = $objet_associable;
		$location_objet = objet_type($location_objet_table);
		$table_extra = table_objet_sql($service);
		$identifiant_extra = id_table_objet($service);
		$objet_extra = objet_type($service);

		$sql = sql_select('*',
			"$table_extra LEFT JOIN $table_liens USING(id_objets_service)",
			'objet=' . sql_quote($location_objet) . ' AND id_objet=' . $id_location_objet);
		while ($row = sql_fetch($sql)) {
			$id_objet = $row[$identifiant_extra];

			if ($prix = $fonction_prix($objet_extra, $id_objet, $contexte, $type_prix)) {
				$prix = $separateur_prix . filtres_prix_formater($prix);
			}
			else {
				$prix = '';
			}

			$titre = isset($row['titre']) ?
				$row['titre'] :
				(
					isset($row['nom']) ?
					$row['nom'] :
					generer_info_entite($objet_extra, $id_objet, 'titre'));

			$data[$id_objet] = $titre . $prix;
		}
	}

	return array(
		array(
			'saisie' => 'checkbox',
			'options' => array(
				'nom' => 'extras_' . $objet_extra,
				'label' => _T(objet_info($objet_extra, 'texte_objets')),
				'data' => $data
			)
		)
	);
}

/*
 * Permet d'appeler la fonction statut_texte_instituer por établir le nom ou traductions d'un statut
 *
 * @param string $objet Objet dont on cherche le nom
 * @param string $statut Nom de machine du statut
 * @return string Nom du statut.
 */
function ol_statut_titre($objet, $statut) {
	include_spip('inc/puce_statut');
	if(!$texte = statut_texte_instituer($objet , trim($statut))) {
		$texte = $statut;
	}
	return $texte;
}

/**
 * Fonctionne qui détermine le nom de l'entité durée.
 *
 * @param string $entite_duree
 *   le nom d'entité pour la durée de prériode.
 *
 * @return string
 *   Nom traduit ou valeur d'origine.
 */
function entite_duree_nom($entite_duree) {
	include_spip('inc/objets_location');
	$nom = _T('date_outils:nuits');
	$entite_duree_definitions = entite_duree_definitions();
	if (isset($entite_duree_definitions[$entite_duree])) {
		return $entite_duree_definitions[$entite_duree];
	}
	else {
		return $entite_duree;
	}
}

/**
 * Obtient les champs extras auteur et locations
 *
 * @return array
 *   Les définitions des champs.
 */
function champs_extras_locations() {
	//les champs extras auteur
	include_spip('cextras_pipelines');

	$champs_extras = array();

	if (function_exists('champs_extras_objet')) {
		$champs_extras['auteur'] = champs_extras_objet('_spip_auteurs');
		$champs_extras['objets_location'] = champs_extras_objet('spip_objets_locations');
	}

	return $champs_extras;
}

/**
 * Cherche le label d'un champ extra
 *
 * @param  string $nom Le nom du champ.
 * @param  array $champs_extras Les champs extras.
 *
 * @return string Le label.
 */
function locations_chercher_label($nom, $champs_extras = '') {
	$label = $nom;

	if (!$champs_extras) {
		//les champs extras auteur
		include_spip('cextras_pipelines');

		if (function_exists('champs_extras_objet')) {
			//Charger les définitions pour la création des formulaires
			$champs_extras = champs_extras_objet(table_objet_sql('auteur'));
		}
	}

	foreach ($champs_extras as $value) {
		if (isset($value['options']['nom']) and $value['options']['nom'] == $nom) {
			$label = $value['options']['label'];
		}
	}
	return $label;
}
