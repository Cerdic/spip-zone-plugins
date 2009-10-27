<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('formulaires/forum');

function formulaires_annotations_charger_dist(
$titre, $table, $type, $script,
$id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic,
$ajouter_mot, $ajouter_groupe, $afficher_texte, $url_param_retour) {

$contexte = formulaires_forum_charger_dist($titre, $table, $type, $script,
$id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic,
$ajouter_mot, $ajouter_groupe, $afficher_texte, $url_param_retour);

$contexte['id_article'] = $id_article;

return $contexte;

}

function formulaires_annotations_verifier_dist(
	$titre, $table, $type, $script,
	$id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic,
	$ajouter_mot, $ajouter_groupe, $afficher_texte, $url_param_retour)
{

return formulaires_forum_verifier_dist($titre, $table, $type, $script,
$id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic,
$ajouter_mot, $ajouter_groupe, $afficher_texte, $url_param_retour);

}


function formulaires_annotations_traiter_dist() {

return formulaires_forum_traiter_dist($titre, $table, $type, $script,
$id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic,
$ajouter_mot, $ajouter_groupe, $afficher_texte, $url_param_retour);

}


?>
