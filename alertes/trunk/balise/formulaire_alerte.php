<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Fonctions reprise du plugin Mes favoris de Olivier Sallou, Cedric Morin.
 */
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


/**
 * Ce formulaire permet de bookmarker des objets contenants/lié à des articles (rubriques, secteurs, mots, auteurs) de SPIP pour abonnement .
 * Par defaut, l'objet et son identifiant sont pris dans la boucle
 * <BOUCLE_(RUBRIQUES){id_rubrique}> #FORMULAIRE_ALERTE ...
 * 
 * Mais il est possible de forcer un objet particulier :
 * #FORMULAIRE_ALERTE{secteur,8}
 * 
 * Ne pas utiliser sur des objets non-liés/non-contenant des articles (articles, brèves, groupes de mots, ...)
 * Cela fonctionnera niveau base, mais vous n'aurez évidemment pas d'alertes.
 * 
 */
function balise_FORMULAIRE_ALERTE($p) {
	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
	$_id_objet = $p->boucles[$p->id_boucle]->primary;
	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_ALERTE',
		array(
			'ALERTE_TYPE_BOUCLE', // demande du type d'objet
			$_id_objet
		)
	);
}

function balise_FORMULAIRE_ALERTE_stat($args, $filtres) {
	// si on force les parametres par #FORMULAIRE_ALERTE{rubrique,12}
	// on enleve les parametres calcules
	if (isset($args[3])) {
		array_shift($args);
		array_shift($args);
	}
	$objet = $args[0];
	$id_objet = $args[1];
	// pas dans une boucle ? on generera une erreur ?
	if ($objet == "'balise_hors_boucle'") {
		$objet = '';
		$id_objet = '';
	}
	
	// on envoie les arguments a la fonction charger
	// du formulaire CVT fomulaires/alerte.php
	return array($objet, $id_objet);
	
}

// balise type_boucle de Rastapopoulos dans le plugin etiquettes
// present aussi dans plugin ajaxforms, notation ...
// bref, a integrer dans le core ? :p
function balise_ALERTE_TYPE_BOUCLE($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? "objet_type('$type')" : "'balise_hors_boucle'";
	return $p;
}
?>
