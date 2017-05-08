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

// https://code.spip.net/@inc_instituer_article_dist
function inc_instituer_article_dist($id_article, $statut, $id_rubrique)
{
	// menu de date pour les articles post-dates (plugin)
	/* un branchement sauvage ?
	if ($statut <> 'publie'
	AND $GLOBALS['meta']['post_dates'] == 'non'
	AND function_exists('menu_postdates'))
		list($postdates,$postdates_js) = menu_postdates();
	else $postdates = $postdates_js = '';*/

	// cf autorisations dans action/editer_article
	if (!autoriser('modifier', 'article', $id_article)) return '';

	$res = '';

	$etats = $GLOBALS['liste_des_etats'];

	if (!autoriser('publierdans', 'rubrique', $id_rubrique)) {
//RERS   tout le monde a la publication autorisée ('publie')  pour les rubriques offres et demandes lors de la création de
//               l'article dans cette rubrique. Pour ces articles, on n'affiche dans les statuts que 
//                  - "publie" (mais ce n'est pas actif) 
//		    - et "a la poubelle"
		$rers_rub_offres = lire_config('rers/rers_rub_offres');	
		$rers_rub_demandes = lire_config('rers/rers_rub_demandes');
		$rers_rub_vie = lire_config('rers/rers_rub_vie');
		if ( $id_rubrique == $rers_rub_offres OR  $id_rubrique == $rers_rub_demandes ) {
			unset($etats[array_search('prop', $etats)]);
			unset($etats[array_search('prepa', $etats)]);
		}
		else {
			unset($etats[array_search('publie', $etats)]);
		}

		unset($etats[array_search('refuse', $etats)]);
		if ($statut == 'prepa')
			$res = supprimer_tags(_T('texte_proposer_publication'));
	}
	
	$res .=
	  "<ul id='instituer_article-$id_article' class='instituer_article instituer'>" 
	  . "<li>" . _T('texte_article_statut')
		. aide("artstatut")
	  ."<ul>";
	
	$href = redirige_action_auteur('instituer_article',$id_article,'articles', "id_article=$id_article");

	foreach($etats as $affiche => $s){
		$puce = puce_statut($s) . _T($affiche);
		if ($s==$statut)
			$class=' selected';
		else {
			$class=''; 
			$puce = "<a href='"
			. parametre_url($href,'statut_nouv',$s)
			. "' onclick='return confirm(confirm_changer_statut);'>$puce</a>";
		}
		$res .= "<li class='$s $class'>$puce</li>";
	}

	$res .= "</ul></li></ul>";
  
	return $res;
}
?>
