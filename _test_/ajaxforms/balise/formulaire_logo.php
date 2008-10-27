<?php


if (!defined("_ECRIRE_INC_VERSION")) return;


function balise_FORMULAIRE_LOGO_dist ($p) {
	// on recupere le nom de la boucle
	// sauf qu'il faut passer par une balise renvoyant le nom 
	# $table = $p->boucles[$p->id_boucle]->id_table;
	
	// on recupere la valeur de la cle primaire de l'objet
	$pk = $p->boucles[$p->id_boucle]->primary;
	return calculer_balise_dynamique($p,'FORMULAIRE_LOGO', array('FORMULAIRE_LOGO_TYPE_BOUCLE',$pk));
}

function balise_FORMULAIRE_LOGO_stat($args,$filtres) {
	// si on force les parametres par #FORMULAIRE_LOGO{article,12}
	// on enleve les parametres calcules
	if (isset($args[3])) {
		array_shift($args);
		array_shift($args);
	}
	$objet = $args[0];
	$id_objet = $args[1];
	// pas dans une boucle ? formulaire pour le logo du site
	// dans ce cas, il faut chercher un 'siteon0.ext'
	if ($objet == 'balise_hors_boucle') {
		$objet = '';
		$id_objet = 0;
		$_id_objet = 'site'; 
	} else {		
		$objet = table_objet($objet);
		$_id_objet = id_table_objet($objet);
	}
	return array($objet, $id_objet);
}

// copie joyeusement sur le plugin etiquette (balise_TYPE_BOUCLE)
// bete de copier plusieurs fois cette fonction du coup.
// a mettre dans le core ?
function balise_FORMULAIRE_LOGO_TYPE_BOUCLE_dist($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? $type : "balise_hors_boucle";
	return $p;   
}

?>
