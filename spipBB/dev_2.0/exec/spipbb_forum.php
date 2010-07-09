<?php
#-------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                 #
#  File    : exec/spipbb_forum                                      #
#  Authors : scoty 2007                                             #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs          #
#  Contact : Hugues AROUX scoty!@!koakidi!.!com                     #
# [fr]                                                              #
#-------------------------------------------------------------------#
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

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_forum() {

	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques, $connect_id_rubrique,
			$connect_id_auteur,
			$accepter_forum,
			$couleur_claire, $couleur_foncee;

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	# requis de cet exec
	#

	# valeurs recup
	$id_article=intval(_request('id_article'));
	$vl=intval(_request('vl'));

	$fixlimit = $GLOBALS['spipbb']['fixlimit'];

	# Qui a pose le lock spipbbart ?
	$auth_deplace = auth_deplace_connecte();


	#
	# prepa info forum(article)
	#
	if($id_article) {
		$row=sql_fetsel("id_article, titre, descriptif, id_rubrique, accepter_forum, statut",
					"spip_articles",
					"id_article = $id_article");
		$id_forum = $row['id_article'];
		$desc_forum = $row['descriptif'];
		$id_salon = $row['id_rubrique'];
		$accepter_forum = $row['accepter_forum'];
		$statut_article = $row['statut'];
		
		# si crea article, our premiere passe, renumerote !
		if(recuperer_numero($row['titre'])=='') {
			spipbb_renumerote();
		}
		$titre_forum = supprimer_numero($row['titre']);
	}

	# verif Forum ferme/ferme maintenance
	if(!function_exists('verif_article_ferme')) include_spip("inc/spipbb_util");
	$art_ferme = verif_article_ferme($id_forum, $GLOBALS['spipbb']['id_mot_ferme']);

	# verif Forum type "annonce" ?
	$forum_annonce = verif_forum_annonce($id_article);


	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(textebrut(typo($titre_forum)), "forum", "spipbb_admin",$id_article);
	echo "<a name='haut_page'></a>";


	debut_gauche();
		spipbb_menus_gauche(_request('exec'),$id_salon, $id_article,'',$vals_aut);
		

	debut_droite();




	#
	# Le Forum (article)
	#
	echo "\n<table cellpadding='0' cellspacing='0' border='0' width='600'>\n";
	echo "<tr>";
	echo "<td width='10%' valign='top'>\n";
	icone(_T('icone_modifier_article'), generer_url_ecrire("spipbb_articles_edit","id_article=$id_article"), _DIR_IMG_SPIPBB."gaf_forum.gif", "edit.gif", $align);
	echo "</td>";
	echo "<td width='80%' valing='top'>\n";
	debut_cadre_relief();

		echo "<span class='verdana2'>"._T('gaf:forum')." .. ".$id_article."</span>";
		gros_titre(typo($titre_forum), "puce-".puce_statut($statut_article).".gif");	
		debut_ligne_grise('25');
		echo "<span class='arial2'>".propre($desc_forum)."</span><br />";
		fin_bloc();	
		
		# instituer auteurs
		#
		$flag_editable = autoriser('modifier', 'article', $id_article);
		$editer_auteurs = charger_fonction('editer_auteurs', 'inc');
		echo $editer_auteurs('article', $id_article, $flag_editable, _request('cherche_auteur'), _request('ids'));
		
		# instituer article
		#
		$statut_rubrique = autoriser('publierdans', 'rubrique', $id_salon);	
		$instituer_article = charger_fonction('spipbb_instituer_article', 'inc');
		echo !$statut_rubrique ? ''
		 : (debut_cadre_relief('', true)
			. $instituer_article($id_article, $statut_article)
			. fin_cadre_relief(true));

		
		# boutons (+ etat) fermeture/maintenance forum
		#
		debut_cadre_couleur('');
		# bouton fermer article (bloque post en zone public)
		if(($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) AND !$art_ferme) {
			formulaire_bouton_libereferme($id_forum, 'ferme', _DIR_IMG_SPIPBB."gaf_verrou1.gif");
		}
		# bouton fermer article pour maintenance (bloque post en toutes zones)
		if($connect_toutes_rubriques AND $art_ferme!="maintenance") {
			formulaire_bouton_libereferme($id_forum, 'maintenance', _DIR_IMG_SPIPBB."gaf_verrou2.gif");
		}
		# boutons liberer article (debloque zone public)
		if(($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) AND $art_ferme=="ferme") {
			formulaire_bouton_libereferme($id_forum, 'libere', _DIR_IMG_SPIPBB."gaf_libere1.gif");
		}
		# bouton liberer article (debloque toutes zones)
		if($connect_toutes_rubriques AND $auth_deplace AND $art_ferme=="maintenance") {
			formulaire_bouton_libereferme($id_forum, 'libere_maintenance', _DIR_IMG_SPIPBB."gaf_libere2.gif");
		}
		
		# passer le forum en 'annonce'
		if(($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) AND !$forum_annonce) {
			bouton_formulaire_forum_annonce($id_article, "annonce", _DIR_IMG_SPIPBB."gaf_annonce.gif");
		}
		// passer le forum en 'desannonce'
		if(($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) AND $forum_annonce) {
			bouton_formulaire_forum_annonce($id_article, "desannonce", _DIR_IMG_SPIPBB."gaf_desannonce.gif");
		}
		
		# etat ferme/maintenance
		bloc_info_etat($art_ferme,'forum',$forum_annonce);	
		fin_cadre_couleur();

	fin_cadre_relief();
	echo "</td>\n";

	echo "<td width='10%' valign='top'>\n";

	# bouton : voir en ligne
	#
	echo "<div style='float:right; margin-left:3px;'>\n";
	icone(_T('icone_voir_en_ligne'), generer_url_public("article", "id_article=".$id_forum), "racine-24.gif", "rien.gif");
	echo "</div>\n";

	echo "</td></tr>\n";
	echo "<tr><td colspan='3'>\n";

	# bouton : ecrire un nouveau sujet
	#
	if ($connect_statut == '0minirezo' AND $accepter_forum!='non' AND $art_ferme!="maintenance") {
		if($art_ferme=="ferme" OR $forum_annonce==true) {
			if($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) {
				bouton_ecrire_post($id_forum,'');
			}
		}
		else { bouton_ecrire_post($id_forum,''); }
	}
	echo "\n</td></tr>\n</table><br />";


	#
	# afficher les sujets (classés par post plus recent) de ce forum
	#
	// recup $vl (tranche) fixe LIMIT : $dl
	$dl=($vl+0);
		
	// récup nombre total d'entrée de $req_sujet (mysql 4.0.0 mini)
	// c: 5/12/8 : malheureusement ce n'est pas standard SQL donc on perd cette faculte
	$nligne = @sql_fetsel("COUNT(*) AS total", // select
		"spip_forum AS sujet, spip_forum AS post", // FROM
		"sujet.id_article='$id_article' 
		AND sujet.id_parent='0' 
		AND sujet.statut IN ('publie', 'off', 'prop') 
		AND post.id_thread=sujet.id_forum", //  Where
		"id_thread", // Groupby
		"date_post DESC", // Order by
		"$dl,$fixlimit" // limit
		);

	// requete sujets
	$res_sujet = sql_select(
		"sujet.id_forum, sujet.*, 
		date_format(sujet.date_heure,'%d/%m/%Y %H:%i') AS date_sujet, 
		max(post.date_heure) AS date_post", // select
		"spip_forum AS sujet, spip_forum AS post", // FROM
		"sujet.id_article='$id_article' 
		AND sujet.id_parent='0' 
		AND sujet.statut IN ('publie', 'off', 'prop') 
		AND post.id_thread=sujet.id_forum", //  Where
		"id_thread", // Groupby
		"date_post DESC", // Order by
		"$dl,$fixlimit" // limit
		);

	// valeur de tranche affichée	
	$tranche_encours = $dl+1;

	// adresse retour des tranches
	$retour_gaf_local = generer_url_ecrire("spipbb_forum", "id_article=".$id_article);		

	// afficher les tranches
	if ($nligne['total']>$fixlimit)
		{
		echo "<div align='center' class='iconeoff' style='margin:2px;'><span class='verdana2'><b>";
		tranches_liste_forum($tranche_encours, $retour_gaf_local,$nligne['total']);
		echo "</b></span></div>";
		}

	$ifond=0;

	echo "\n<table cellpadding='0' cellspacing='0' border='0' width='600'><tr><td>\n";
	debut_cadre_formulaire("");

	while ($row=sql_fetch($res_sujet)) 
	{
		$id_sujet = $row['id_forum'];
		$titre_sujet = $row['titre'];
		$aut_sujet = typo($row['auteur']);
		$date_sujet = $row['date_sujet'];
		$statut_sujet = $row['statut'];
		
		// icone état du post
		$aff_statut = icone_statut_post($statut_sujet);

		// post de type "annonce" ?
		$annonce = verif_sujet_annonce($id_sujet);
		
		// verif si Sujet est ferme ?
		if(!function_exists('verif_sujet_ferme')) include_spip("inc/spipbb_util");
		$sujet_ferme = verif_sujet_ferme($id_sujet, $GLOBALS['spipbb']['id_mot_ferme']);
		
		// les posts reponses de ce sujet
		$res_post = spip_select("id_forum, titre, auteur, 
					date_format(date_heure,'%d/%m/%Y %H:%i') AS date_post",
					"spip_forum",
					"id_thread='$id_sujet' AND statut IN ('publie', 'off', 'prop') AND id_parent!='0'");
		$nbr_post = sql_count($res_post);
		
		// le dernier post de ce sujet
		$res_date = sql_select("id_forum, date_heure, auteur","spip_forum",
					"id_thread=$id_sujet AND statut IN ('publie', 'off', 'prop')",
					"", // groupby
					"date_heure DESC",
					"0,1");
		$row = sql_fetch($res_date);
		$id_post_der = $row['id_forum'];
		$der_date = date_relative($row['date_heure']);
		$aut_post = typo($row['auteur']);
		$url_post_der = url_post_tranche($id_post_der, $id_sujet);
		
		
		$ifond = $ifond ^ 1;
		$coul_sujet = ($ifond) ? $couleur_claire : '#e3e3e3';
		$rowspan_p = $nbr_post + 1;

		#
		# le tableau du sujet
		#
		debut_bloc_couleur($coul_sujet);
		echo "\n<table cellpadding=1 cellspacing=2 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td width='7%' valign='top' rowspan='".$rowspan_p."' class='verdana2'>";
		icone(_T('gaf:sujet_ouvrir'), generer_url_ecrire("spipbb_sujet", "id_sujet=".$id_sujet), _DIR_IMG_SPIPBB."gaf_sujet.gif", "rien.gif");
		echo $id_sujet;
		
		// bouton de déplacement du thread
		if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques 
			AND $auth_deplace AND $art_ferme=="maintenance")
			{ bouton_deplace_sujet($id_forum, $id_sujet); }

		echo "</td>\n";
		echo "<td width='' valign='top'>".$aff_statut;
		
		// icone de fermeture sujet
		bloc_info_etat($sujet_ferme,'',$annonce);
		
		echo "<span class='verdana3'><b>".propre($titre_sujet)."</b></span><br />\n";
		echo "<span class='verdana2'>par <b>".$aut_sujet."</b> .. "._T('gaf:le')." ".$date_sujet."<span></td>\n";
		echo "<td width='8%' valign='top' class='verdana2'>\n";
		debut_bloc_gricont();
			echo "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' border='0' valign='absmiddle' /><br />\n";
			echo $nbr_post;
		fin_bloc();
		echo "</td>";
		echo "<td width='20%' valign='top' class='verdana2'>\n";
		echo "<div align='right'><a href='".$url_post_der."' title='"._T('gaf:voir_message')."'>\n";
		echo $aut_post."<br />".$der_date."</a></div>";
		echo "</td></tr>\n";

		$i=0;
		$compt_rang=2;
		// lignes ...
		while ($row=sql_fetch($res_post)) {
			$retrait = $i*5;
			$url_post = url_post_tranche($row['id_forum'], $id_sujet, $compt_rang);
			echo "<tr><td valign='top' colspan='2' class='verdana2'>\n";
			echo "<div style='margin-left:".$retrait."px;'>\n";
			echo "<a href='".$url_post."' title='"._T('gaf:voir_message')."'>";
			echo "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' border='0' valign='absmiddle' /><b> ";
			echo (!$row['auteur']) ? _T('gaf:anonyme')."</b> | " : typo($row['auteur'])."</b> | ";
			echo couper(propre($row['titre']),'40');
			echo "</a>\n";
			echo "</div></td><td>".$row['date_post']."</td></tr>\n";
			$compt_rang++;
			$i++;
			if($i==10)$i=0;
		}
				
		echo "</table>\n";
		fin_bloc();
	}
	if (!sql_count($res_sujet)) {
		echo _T('gaf:aucun_sujet_dans_forum');
	}
	fin_cadre_formulaire();	
	echo "</td></tr></table>";

	// afficher les tranches
	if ($nligne['total']>$fixlimit) {
		echo "<div align='center' class='iconeoff' style='margin:2px;'><span class='verdana2'><b>\n";
		tranches_liste_forum($tranche_encours, $retour_gaf_local,$nligne['total']);
		echo "</b></span></div>\n";
	}

	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // exec_spipbb_forum

?>