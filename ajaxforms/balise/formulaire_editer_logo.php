<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pour editer / ajouter / supprimer un logo sur un objet SPIP.
 * 
 * <BOUCLE_(ARTICLES){id_article}>#FORMULAIRE_EDITER_LOGO ...
 * 
 * On peut forcer l'objet et son id
 * #FORMULAIRE_EDITER_LOGO{article,8}
 * 
 * Et forcer une redirection sur un traitement ok
 * #FORMULAIRE_EDITER_LOGO{article,8,#URL_AUTEUR{8}}
 */
function balise_FORMULAIRE_EDITER_LOGO_dist ($p) {
	// on recupere le nom de la boucle
	// sauf qu'il faut passer par une balise renvoyant le nom 
	# $table = $p->boucles[$p->id_boucle]->id_table;
	
	// on recupere la valeur de la cle primaire de l'objet
	$pk = $p->boucles[$p->id_boucle]->primary;
	return calculer_balise_dynamique($p,'FORMULAIRE_EDITER_LOGO', array('AJAXFORM_TYPE_BOUCLE',$pk));
}

function balise_FORMULAIRE_EDITER_LOGO_stat($args,$filtres) {
	
	// si on force les parametres par #FORMULAIRE_EDITER_LOGO{article,12}
	// on enleve les parametres calcules
	if (isset($args[3])) {
		array_shift($args);
		array_shift($args);
	}
	$objet = $args[0];
	$id_objet = $args[1];
	// si on demande en plus une redirection 
	// #FORMULAIRE_EDITER_LOGO{article,12,#URL_AUTEUR{12}}
	$retour = isset($args[2])?$args[2]:"";
	// tableau d'options
	$options = isset($args[3])?$args[3]:array();
	// pas dans une boucle ? formulaire pour le logo du site
	// dans ce cas, il faut chercher un 'siteon0.ext'
	if ($objet == 'balise_hors_boucle') {
		$objet = '';
		$id_objet = 0;
		#$_id_objet = 'site'; // calcule dans le CVT
	} else {		
		$objet = table_objet($objet);
		#$_id_objet = id_table_objet($objet); // calcule dans le CVT
	}
	return array($objet, $id_objet, $retour, $options);
}

?>
