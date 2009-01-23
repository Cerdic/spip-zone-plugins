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

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/acces');
include_spip('inc/texte');
include_spip('inc/forum');

/*******************************/
/* GESTION DU FORMULAIRE FORUM */
/*******************************/

// Contexte du formulaire
function balise_FORMULAIRE_FORUM_PRIVE ($p) {

	$p = calculer_balise_dynamique($p,'FORMULAIRE_FORUM_PRIVE', array('id_rubrique', 'id_forum', 'id_article', 'id_breve', 'id_syndic', 'id_message', 'afficher_texte', 'statut'));
	return $p;
}

//
// Chercher le titre et la configuration d'un forum 

// http://doc.spip.org/@balise_FORMULAIRE_FORUM_PRIVE_stat
function balise_FORMULAIRE_FORUM_PRIVE_stat($args, $filtres) {

	// le denier arg peut contenir l'url sur lequel faire le retour
	// exemple dans un squelette article.html : [(#FORMULAIRE_FORUM{#SELF})]

	// recuperer les donnees du forum auquel on repond.
	list ($idr, $idf, $ida, $idb, $ids, $idm, $af, $st, $url) = $args;
	$idr = intval($idr);
	$idf = intval($idf);
	$ida = intval($ida);
	$idb = intval($idb);
	$ids = intval($ids);
	$idm = intval($idm);

	if ($ida) {
		$titre = sql_fetsel('titre', 'spip_articles', "id_article = $ida");
	} else {
		if ($idb) {
			$titre = sql_fetsel('titre', 'spip_breves', "id_breve = $idb");
		} else if ($ids) {
			$titre = sql_fetsel('nom_site AS titre', 'spip_syndic', "id_syndic = $ids");
		} else if ($idr) {
			$titre = sql_fetsel('titre', 'spip_rubriques', "id_rubrique = $idr");
		} else if ($idm) {
			$titre = sql_fetsel('titre', 'spip_messages', "id_message = $idm");
		}
	}

	if ($idf>0) {
		$titre_m = sql_fetsel('titre', 'spip_forum', "id_forum = $idf");
		if (!$titre_m) return false; // URL fabriquee
		$titre = $titre_m;
	}

	$titre = supprimer_numero($titre['titre']);

	return
		array($idr, $idf, $ida, $idb, $ids, $idm, $af, $st, $titre, $url);
}

?>
