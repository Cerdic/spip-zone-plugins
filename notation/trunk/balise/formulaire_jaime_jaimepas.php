<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


/**
 * Ce formulaire permet de noter des objets de SPIP.
 * Par defaut, l'objet et son identifiant sont pris dans la boucle
 * <BOUCLE_(ARTICLES){id_article}> #FORMULAIRE_JAIME_JAIMEPAS ...
 * 
 * Mais il est possible de forcer un objet particulier :
 * #FORMULAIRE_JAIME_JAIMEPAS{article,8}
 * 
 */
function balise_FORMULAIRE_JAIME_JAIMEPAS ($p) {
	include_spip("balise/formulaire_notation");
	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
    $_id_objet = $p->boucles[$p->id_boucle]->primary;
	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_JAIME_JAIMEPAS',
		array(
			'NOTATION_TYPE_BOUCLE', // demande du type d'objet
			$_id_objet
		)
	);
}


function balise_FORMULAIRE_JAIME_JAIMEPAS_stat($args, $filtres) {
	include_spip("balise/formulaire_notation");
	return balise_FORMULAIRE_NOTATION_stat($args,$filtres);
}

?>