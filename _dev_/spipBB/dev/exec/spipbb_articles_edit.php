<?php
#-------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                 #
#  File    : exec/spipbb_articles_edit                              #
#  Authors : scoty 2007                                             #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs          #
#  Source  : exec/articles_edit                                     #
#  Contact : Hugues AROUX scoty!@!koakidi!.!com                     #
# [fr]                                                              #
#-------------------------------------------------------------------#

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

# requis spip
include_spip('inc/article_select');
include_spip('inc/documents');

// ------------------------------------------------------------------------------
// Source http://doc.spip.org/@exec_articles_edit_dist
// ------------------------------------------------------------------------------
function exec_spipbb_articles_edit() {
	# requis spipbb
	include_spip("inc/spipbb_init");

	spipbb_articles_edit(_request('id_article'), // intval plus tard
		intval(_request('id_rubrique')),
		intval(_request('lier_trad')),
		intval(_request('id_version')),
		((_request('new') == 'oui') ? 'new' : ''),
		'articles_edit_config');
} // exec_spipbb_articles_edit

// ------------------------------------------------------------------------------
// Source http://doc.spip.org/@articles_edit
// ------------------------------------------------------------------------------
function spipbb_articles_edit($id_article, $id_rubrique,$lier_trad,  $id_version, $new, $config_fonc)
{
	$row = article_select($id_article ? $id_article : $new, $id_rubrique,  $lier_trad, $id_version);
	$id_article = $row['id_article'];
	$id_rubrique = $row['id_rubrique'];

	$commencer_page = charger_fonction('commencer_page', 'inc');
	
	if (!$row
	  OR ($new AND !autoriser('creerarticledans','rubrique',$id_rubrique)) 
	  OR (!$new AND (!autoriser('voir', 'article', $id_article)	OR !autoriser('modifier','article', $id_article))) 
	  ) {
		echo $commencer_page(_T('info_modifier_titre', array('titre' => $titre)), "naviguer", "rubriques", $id_rubrique);
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}

	pipeline('exec_init',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));

	if ($id_version) $titre.= ' ('._T('version')." $id_version)";
	else $titre = $row['titre'];

	#h modif
	echo $commencer_page(_T('titre_page_articles_edit', array('titre' => $titre)), "forum", "spipbb_admin", $id_rubrique);
	echo "<a name='haut_page'></a>";

	debut_gauche();
		spipbb_menus_gauche(_request('exec'),$id_rubrique,$id_article);


	// Pave "documents associes a l'article"
	#h.
	/*
	if (!$new){
		# affichage sur le cote des pieces jointes, en reperant les inserees
		# note : traiter_modeles($texte, true) repere les doublons
		# aussi efficacement que propre(), mais beaucoup plus rapidement

		traiter_modeles(join('',$row), true);
		echo afficher_documents_colonne($id_article, 'article');
	} else {
		# ICI GROS HACK
		# -------------
		# on est en new ; si on veut ajouter un document, on ne pourra
		# pas l'accrocher a l'article (puisqu'il n'a pas d'id_article)...
		# on indique donc un id_article farfelu (0-id_auteur) qu'on ramassera
		# le moment venu, c'est-à-dire lors de la creation de l'article
		# dans editer_article.
		echo afficher_documents_colonne(
			0-$GLOBALS['auteur_session']['id_auteur'], 'article');
	}
	*/

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));
	debut_droite();

	debut_cadre_formulaire();
	echo articles_edit_presentation($new, $row['id_rubrique'], $lier_trad, $row['id_article'], $row['titre']);
	#h. modif
	$editer_article = charger_fonction('spipbb_editer_article', 'inc');
	echo $editer_article($new, $id_rubrique, $lier_trad, generer_url_ecrire("spipbb_forum"), $config_fonc, $row);
	fin_cadre_formulaire();

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));


	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // spipbb_articles_edit

// ------------------------------------------------------------------------------
// http://doc.spip.org/@articles_edit_presentation
// ------------------------------------------------------------------------------
function articles_edit_presentation($new, $id_rubrique, $lier_trad, $id_article, $titre)
{
	#h. modif
	$oups = ($lier_trad ?
	     generer_url_ecrire("articles","id_article=$lier_trad")
	     : ($new
		? generer_url_ecrire("spipbb_admin","id_salon=$id_rubrique")
		: generer_url_ecrire("spipbb_forum","id_article=$id_article")
		));
	#h. modif
	return
		"\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>" .
		"<tr>" .
		"\n<td>" .
		icone(_T('icone_retour'), $oups, _DIR_IMG_SPIPBB."gaf_forum.gif", "rien.gif", '',false) .
		"</td>\n<td>" .
		"<img src='" .
	  	_DIR_IMG_PACK .	"rien.gif' width='10' alt='' />" .
		"</td>\n" .
		"<td style='width: 100%'>" .
	 	_T('gaf:nouveau_forum') .
		gros_titre($titre,'',false) . 
		"</td></tr></table><hr />\n";
} // articles_edit_presentation

?>
