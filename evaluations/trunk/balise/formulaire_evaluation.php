<?php
/**
 * Déclarations de la balise dynamique FORMULAIRE_EVALUATION
 *
 * @plugin     Évaluations
 * @copyright  2013
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Evaluations\Pipelines
 */
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


/**
 * Ce formulaire permet d'évaluer un objet de SPIP
 * 
 * Par defaut, l'objet et son identifiant sont pris dans la boucle
 * <BOUCLE_(ARTICLES){id_article}> #FORMULAIRE_EVALUATION{identifiant} ...
 *
 * Mais il est possible de forcer un objet particulier :
 * #FORMULAIRE_NOTATION{identifiant,#SELF,article,8}
 *
 */
function balise_FORMULAIRE_EVALUATION ($p) {
	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
	$i_boucle = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$_id_objet = $p->boucles[$i_boucle]->primary;
	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_EVALUATION',
		array(
			'EVALUATION_TYPE_BOUCLE', // demande du type d'objet
			$_id_objet
		)
	);
}


function balise_FORMULAIRE_EVALUATION_stat($args, $filtres) {
	// si on force les parametres par #FORMULAIRE_EVALUATION{identifiant,#SELF,article,12}
	// on enleve les parametres calcules
	$auto_objet    = array_shift($args);
	$auto_id_objet = array_shift($args);

	// retour
	if (!isset($args[1])) {
		$args[1] = '';
	}
	// objet et id_objet non forcés
	if (!isset($args[2])) {
		$args[2] = $auto_objet;
		$args[3] = $auto_id_objet;
	}
	
	$identifiant = $args[0];
	$retour = $args[1];
	$objet = $args[2];
	$id_objet = $args[3];
	// pas dans une boucle ? on generera une erreur ?
	if ($objet == 'balise_hors_boucle') {
		$args[2] = '';
		$args[3] = '';
	} else {
		$args[2] = objet_type($objet);
	}

	// on envoie les arguments a la fonction charger
	// du formulaire CVT fomulaires/evaluation.php
	return $args;

}

// balise type_boucle de Rastapopoulos dans le plugin etiquettes
// present aussi dans plugin ajaxforms...
// bref, a integrer dans le core ? :p
function balise_EVALUATION_TYPE_BOUCLE($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? "'$type'" : "'balise_hors_boucle'";
	return $p;
}
?>
