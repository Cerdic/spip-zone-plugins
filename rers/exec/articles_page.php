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

include_spip('inc/presentation');

// http://doc.spip.org/@exec_articles_page_dist
function exec_articles_page_dist()
{
	global $connect_statut, $connect_id_auteur;

	//rers	
	$rers_rub_offres = lire_config('rers/rers_rub_offres');
	$rers_rub_demandes = lire_config('rers/rers_rub_demandes');


 	pipeline('exec_init',array('args'=>array('exec'=>'articles_page'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_articles_page'), "naviguer", "articles");

	echo debut_gauche('', true);

//
// Afficher le bouton de creation d'article
//

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_page'),'data'=>''));


// rers   (pour rédacteur)  supprimer le bloc    "raccourci   : nouvel article"
global $connect_statut; //rers
if ($connect_statut == '0minirezo') // rers
{ //rers
	if (sql_countsel('spip_rubriques')) {
		echo bloc_des_raccourcis(icone_horizontale(_T('icone_ecrire_article'), generer_url_ecrire("articles_edit","new=oui"), "article-24.gif", "creer.gif", false));
	} else {
		if (autoriser('creerrubriquedans', 'rubrique')) {
			echo _T('texte_creer_rubrique');
			echo	bloc_des_raccourcis(icone_horizontale (_T('icone_creer_rubrique'), generer_url_ecrire("rubriques_edit","new=oui&retour=nav"), "rubrique-24.gif", "creer.gif",false));
		}
	}
}//rers
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
echo debut_droite('', true);

//
// Vos articles en cours de redaction
//

	echo afficher_objets('article',_T('info_en_cours_validation'), array('FROM' => "spip_articles AS articles, spip_auteurs_articles AS lien ", "WHERE" => "articles.id_article=lien.id_article AND lien.id_auteur=$connect_id_auteur AND articles.statut='prepa'", 'ORDER BY' => "articles.date DESC"));



//
// Vos articles soumis au vote
//


//rers   :   articles en attente de validation :  exclure les rubriques DEMANDES et OFFRES
	echo afficher_objets('article',_T('info_attente_validation'), array('FROM' => "spip_articles AS articles, spip_auteurs_articles AS lien ", "WHERE" => "articles.id_article=lien.id_article AND lien.id_auteur=$connect_id_auteur AND articles.statut='prop' AND articles.id_rubrique!=$rers_rub_offres AND articles.id_rubrique!=$rers_rub_demandes", "ORDER BY" => "articles.date"));


// rers DEBUT AJOUT
//rers :  vos fiches  de savoirs

	$res = '';
	$res .=  afficher_objets('article',"Vos fiches d'offres de savoirs", array("WHERE" => "statut='prop' AND id_rubrique=$rers_rub_offres", 'ORDER BY' => "date DESC"));
	$res .=  afficher_objets('article',"Vos fiches de demandes de savoirs", array("WHERE" => "statut='prop' AND id_rubrique=$rers_rub_demandes", 'ORDER BY' => "date DESC"));
	echo	"<div style='position:relative;display:inline;'>" 
	. debut_cadre_couleur_foncee("",true, "","Vos articles `fiches de savoirs'" //rers
		. (($GLOBALS['meta']['forum_prive_objets'] != 'non')
			? ' '._T('texte_en_cours_validation_forum')
			: '' )
		)
	. $res
	. fin_cadre_couleur_foncee(true)
	. "</div>";

//rers FIN AJOUT







//
// Vos articles publies
//

	echo afficher_objets('article',_T('info_publies'),	array("FROM" =>"spip_articles AS articles, spip_auteurs_articles AS lien ", "WHERE" => "articles.id_article=lien.id_article AND lien.id_auteur=$connect_id_auteur AND articles.statut='publie'", 'ORDER BY' => "articles.date DESC"));

//
//  Vos articles refuses
//

	echo afficher_objets('article',_T('info_refuses'),	array('FROM' =>"spip_articles AS articles, spip_auteurs_articles AS lien ", "WHERE" => "articles.id_article=lien.id_article AND lien.id_auteur=$connect_id_auteur AND articles.statut='refuse'",  'ORDER BY' => "articles.date DESC"));

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_page'),'data'=>''));

	echo fin_gauche(), fin_page();
}

?>
