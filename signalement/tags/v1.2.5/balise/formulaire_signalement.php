<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Balise #FORMULAIRE_SIGNALEMENT
 *
 **/
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


/**
 * Ce formulaire permet de signaler des objets illicites de SPIP.
 * Par defaut, l'objet et son identifiant sont pris dans la boucle
 * <BOUCLE_(ARTICLES){id_article}> #FORMULAIRE_SIGNALEMENT ...
 * 
 * Mais il est possible de forcer un objet particulier :
 * #FORMULAIRE_SIGNALEMENT{article,8}
 * 
 */
function balise_FORMULAIRE_SIGNALEMENT ($p) {
	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
	$_id_objet = $p->boucles[$p->id_boucle]->primary;
	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_SIGNALEMENT',
		array(
			'SIGNALEMENT_TYPE_BOUCLE', // demande du type d'objet
			$_id_objet
		)
	);
}

function balise_FORMULAIRE_SIGNALEMENT_stat($args) {
	// si on force les parametres par #FORMULAIRE_SIGNALEMENT{article,12}
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
	// du formulaire CVT fomulaires/signalement.php
	return array($objet, $id_objet);
	
}

// balise type_boucle de Rastapopoulos dans le plugin etiquettes
// present aussi dans plugin ajaxforms, notation ...
// bref, a integrer dans le core ? :p
function balise_SIGNALEMENT_TYPE_BOUCLE($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? "objet_type('$type')" : "'balise_hors_boucle'";
	return $p;
}
?>
