<?php
/**
 * Fonctions utiles au plugin Réservation Événements
 *
 * @plugin     Réservation Événements
 * @copyright  2013 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Obtient les champs extras auteur et rçeservation

 *
 * @return array
 * 					Les définitions des champs.
 */
function champs_extras_reservation() {
	//les champs extras auteur
	include_spip('cextras_pipelines');

	$champs_extras = array();

	if (function_exists('champs_extras_objet')) {
		$champs_extras['auteur'] = champs_extras_objet(table_objet_sql('auteur'));
		$champs_extras['reservation'] = champs_extras_objet(table_objet_sql('reservation'));
	}

	return $champs_extras;
}

function nom_statuts($statuts) {
	$liste_objets = lister_tables_objets_sql();

	$statuts_selectionnees = array();

	if (is_array($statuts)) {
		foreach ($liste_objets['spip_reservations']['statut_textes_instituer'] AS $statut => $label) {
			if (in_array($statut, $statuts))
				$statuts_selectionnees[$statut] = _T($label);
		}
	}
	return $statuts_selectionnees;
}

//retourne les statuts qui définissent si un événement est complet
function statuts_complet() {
	$statuts_complets = charger_fonction('complet', 'inc/statuts');
	$statuts = $statuts_complets();
	return $statuts;
}

/**
 * Cherche le label d'un champ extra
 *
 * @param  string $nom Le nom du champ.
 * @param  array $champs_extras Les champs extras.
 *
 * @return string Le label.
 */
function chercher_label($nom, $champs_extras = '') {
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

/**
 * Cherche les infos d'un client
 *
 * @param  string $email L'email du client.
 * @param  string $champ Un champ spécifique.
 * @param  bin $retour_vide TRUE/FALSE.
 *
 * @return mixed La valeur du champ ou un tableau avec tous les champs.
 */
function infos_client($email, $champ = '', $retour_vide = TRUE) {
	// Si on trouve un auteur spip on le prend, sinon on cherche dans les réservations
	if (!$client = sql_fetsel('*', 'spip_auteurs', 'email=' . sql_quote($email)))
		!$client = sql_fetsel('*', 'spip_reservations', 'email=' . sql_quote($email), '', 'id_reservation DESC');

	// Si on a des informations on retrourne la valeur d'un champ
	// ou le tableau des infos selon ce qui es demandé.
	// sinon on ne retourne rien.
	if ($client) {
		if ($champ AND isset($client[$champ]))
			$infos = $client[$champ];
		else
			$infos = $client;
	}
	elseif ($retour_vide)
		$infos = '';
	else
		$infos = $email;

	return $infos;
}

/*
 * Formater un nombre pour l'afficher comme un prix avec une devise
 *
 * @param float $prix Valeur du prix à formater
 * @param string $devise devise
 * @return string Retourne une chaine contenant le prix formaté avec une devise (par défaut l'euro)
 */
function prix_formater_devise($montant, $devise) {
	include_spip('inc/config');
	include_spip('inc/cookie');

	$montant = number_format($montant, 2, '.', '');

	//On détermine la langue du contexte
	$lang = $GLOBALS['spip_lang'];
	// Si PECL intl est présent on dermine le format de l'affichage de la devise selon la langue du contexte
	if (function_exists('numfmt_create')) {
		$fmt = numfmt_create($lang, NumberFormatter::CURRENCY);
		$montant = numfmt_format_currency($fmt, $montant, $devise);
	}
	//Sinon on formate à la française
	elseif (function_exists('traduire_devise'))
		$montant = $montant . '&nbsp;' . traduire_devise($devise);
	else
		$montant = $montant . '&nbsp;' . $devise;

	return $montant;
}

/*
 * Permet d'appeler la fonction statut_texte_instituer por établir le nom ou traductions d'un statut
 *
 * @param string $objet Objet dont on cherche le nom
 * @param string $statut Nom de machine du statut
 * @return string Nom du statut.
 */
function re_statut_titre($objet, $statut) {
	include_spip('inc/puce_statut');
	if(!$texte = statut_texte_instituer($objet , trim($statut))) {
		$texte = $statut;
	}
	return $texte;
}

