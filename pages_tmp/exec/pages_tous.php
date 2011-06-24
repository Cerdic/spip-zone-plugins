<?php
#---------------------------------------------------#
#  Plugin  : Pages                                  #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Pages       #
#-----------------------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;

find_in_path('presentation.php', 'inc/', true);
find_in_path('filtres.php', 'inc/', true);

function exec_pages_tous_dist()
{
	global $connect_statut, $connect_id_auteur;

 	pipeline('exec_init',array('args'=>array('exec'=>'pages_tous'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_articles_page'), "naviguer", "articles");

	echo debut_gauche('', true);

//
// Afficher le bouton de creation d'article
//

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'pages_tous'),'data'=>''));

	echo bloc_des_raccourcis(icone_horizontale(_T('pages:creer_page'), generer_url_ecrire("articles_edit","new=oui&type=page&id_rubrique=-1"), "article-24.gif", "creer.gif", false));

	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'pages_tous'),'data'=>''));
	echo debut_droite('', true);

//
// Toutes les pages
//

	echo sinon(afficher_objets('article',_T('pages:toutes_les_pages'), array('FROM' => "spip_articles AS articles ", "WHERE" => "id_rubrique='-1'", 'ORDER BY' => "articles.date DESC, articles.statut DESC")), '<div class="messages"><p>'._T('pages:aucune_page').'</p></div>');

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_page'),'data'=>''));

	echo fin_gauche(), fin_page();
}

?>
