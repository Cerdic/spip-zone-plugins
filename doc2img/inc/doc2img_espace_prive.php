<?php

/**
 *	Charge le fond de selection des documents coté privé
 *
 *  lors d'un appel à la page ecrire/?exec=articles, un menu est chargé.
 *  Ce menu liste les documents affecté à l'article
 *  et offre la possibilité de les convertir
 *
 *  @param $id_article identifiant de l'article
 *  @return $flux un flux html contenant le menu de selection
 */
function affiche_liste_doc($id_article) {
	$flux = '<div style="height: 5px;"/>';
	$flux .= '</div>';

	$flux .= debut_cadre('r');

	// définition du contexte
	$contexte = array("id_article" => $id_article);
	// chargement du fond demandé
	$flux .= recuperer_fond("prive/doc2img",$contexte);

	$flux .= fin_cadre('r');
    return $flux;
}

?>