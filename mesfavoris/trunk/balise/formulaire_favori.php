<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2013 Olivier Sallou, Cedric Morin, Gilles Vincent
 * Distribue sous licence GPL
 *
 */
 
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ce formulaire permet de bookmarker des objets de SPIP.
 * Par defaut, l'objet et son identifiant sont pris dans la boucle
 * <BOUCLE_(ARTICLES){id_article}> #FORMULAIRE_FAVORI ...
 *
 * Mais il est possible de forcer un objet particulier :
 * #FORMULAIRE_FAVORI{article,8}
 *
 * Il est également possible de forcer à une catégorie donnée
 * #FORMULAIRE_FAVORI{article,8,ma_categorie}
 * <BOUCLE_(ARTICLES){id_article}> #FORMULAIRE_FAVORI{ma_categorie} ...
 *  
 */
function balise_FORMULAIRE_FAVORI($p) {
	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
	$_id_objet = $p->boucles[$p->id_boucle]->primary;
	
	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_FAVORI',
		array(
			'FAVORI_TYPE_BOUCLE', // demande du type d'objet
			$_id_objet
		)
	);
}

function balise_FORMULAIRE_FAVORI_stat($args) {
	// si on force les parametres par #FORMULAIRE_FAVORI{article,12}
	// on enleve les parametres calcules
	if (isset($args[3])) {
		array_shift($args);
		array_shift($args);
	}
	$objet = $args[0];
	$id_objet = $args[1];
	$categorie = $args[2];
	
	// pas dans une boucle ? on generera une erreur ?
	if ($objet == "'balise_hors_boucle'") {
		$objet = '';
		$id_objet = '';
		$categorie = '';
	}
	
	// on envoie les arguments a la fonction charger
	// du formulaire CVT fomulaires/favori.php
	return array($objet, $id_objet, $categorie);
}

// balise type_boucle de Rastapopoulos dans le plugin etiquettes
// present aussi dans plugin ajaxforms, notation ...
// bref, a integrer dans le core ? :p
function balise_FAVORI_TYPE_BOUCLE($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? "objet_type('$type')" : "'balise_hors_boucle'";
	
	return $p;
}
