<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
include_spip('inc/rubriques');
include_spip('inc/actions');
include_spip('inc/mots');
include_spip('inc/date');
include_spip('inc/documents');
include_spip('inc/petition');
include_spip('exec/editer_auteurs');
include_spip('exec/referencer_traduction');
include_spip('exec/virtualiser');
include_spip('exec/discuter');
include_spip('base/abstract_sql');

include_spip('mes_fonctions_publiHALRSS');


// http://doc.spip.org/@exec_articles_dist
function exec_publihal_publi()
{
	global $cherche_auteur, $ids, $cherche_mot,  $select_groupe, $debut, $id_syndic_article, $trad_err; 

	global  $connect_id_auteur, $connect_statut, $options, $spip_display, $spip_lang_left, $spip_lang_right, $dir_lang;

	$id_syndic_article= intval($id_syndic_article);
	// publihal_publi","id_syndic_article=$id_syndic_article"

	pipeline('exec_init',array('args'=>array('exec'=>'publihal_publi','id_syndic_article'=>$id_syndic_article),'data'=>''));

	$row = spip_fetch_array(spip_query("SELECT * FROM spip_syndic_articles WHERE id_syndic_article=$id_syndic_article"));

	if (!$row) {
	   // cas du numero hors table
		$titre = "pas de publication $id_syndic_article !";//_T('public:aucun_article');
		debut_page("&laquo; $titre &raquo;", "naviguer", "articles");
		debut_grand_cadre();
		fin_grand_cadre();
		echo $titre;
		exit;
	}
/*`id_syndic_article` bigint(21) NOT NULL auto_increment,
  `id_syndic` bigint(21) NOT NULL default '0',
  `titre` text NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `lesauteurs` text NOT NULL,
  `maj` timestamp(14) NOT NULL,
  `statut` varchar(10) NOT NULL default '',
  `descriptif` blob NOT NULL,
  `lang` varchar(10) NOT NULL default '',
  `url_source` tinytext NOT NULL,
  `source` tinytext NOT NULL,
  `tags` text NOT NULL,
*/

	$id_syndic = $row["id_syndic"];
	$titre = $row["titre"];
	$url = $row["url"];
	$date = $row["date"];
	$lesauteurs = $row["lesauteurs"];
	$maj = $row["maj"];
	$statut = $row["statut"];
	$descriptif = $row["descriptif"];
	$lang = $row["lang"];
	$url_source = $row["url_source"];
	$source = $row["source"];
	$tags = $row["tags"];
	
	$flag_editable = true;// ($statut_rubrique OR ($flag_auteur AND ($statut_article == 'prepa' OR $statut_article == 'prop' OR $statut_article == 'poubelle')));

	debut_page("&laquo; $titre &raquo;", "naviguer", "articles", "", "");

	debut_grand_cadre();

	//afficher_hierarchie($id_rubrique);

	fin_grand_cadre();

//
// Affichage de la colonne de gauche
//

debut_gauche();

//	boite_info_articles($id_article, $statut_article, $visites, $id_version);

//
// Logos de l'article
//

//  if ($flag_editable AND ($spip_display != 4)) {
//	  include_spip('inc/chercher_logo');
//	  echo afficher_boite_logo('id_article', $id_article,
//			      _T('logo_article').aide ("logoart"), _T('logo_survol'), 'articles');
//  }

// pour l'affichage du virtuel
//$virtuel = '';
//if (substr($chapo, 0, 1) == '=') {
//	$virtuel = substr($chapo, 1);
//}

// Boites de configuration avancee

//if ($options == "avancees" && $connect_statut=='0minirezo' && $flag_editable)
//  {
//	boites_de_config_articles($id_article);
// 
//	boite_article_virtuel($id_article, $virtuel);
//  }

//
// Articles dans la meme rubrique
//

//meme_rubrique_articles($id_rubrique, $id_article, $options);

echo pipeline('affiche_gauche',array('args'=>array('exec'=>'publihal_publi','id_syndic_article'=>$id_syndic_article),'data'=>''));

//
// Affichage de la colonne de droite
//

creer_colonne_droite();
 echo pipeline('affiche_droite',array('args'=>array('exec'=>'publihal_publi','id_syndic_article'=>$id_syndic_article),'data'=>''));

debut_droite();

//changer_typo('','article'.$id_article);

debut_cadre_relief();

//
// Titre, surtitre, sous-titre
//

//$modif = titres_articles($titre, $statut_article,$surtitre, $soustitre, $descriptif, $url_site, $nom_site, $flag_editable, $id_article, $id_rubrique);

//+++>
$logo_statut = "puce-".puce_statut($statut).".gif";

echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
echo "<tr width='100%'><td valign='top'>";
 
gros_titre($titre, $logo_statut);

echo "</td><td>".icone(_T('icone_retour'), generer_url_ecrire("publihal",""), "article-24.gif", "rien.gif",'',false);

echo "</td></tr>";
echo "\n</table>";

//<+++

 echo "<div class='serif' align='$spip_lang_left'>";

//
// Liste des auteurs de l'article
//

// echo "\n<div id='editer_auteurs-$id_article'>";
// echo formulaire_editer_auteurs($cherche_auteur, $ids, $id_article,$flag_editable);
// echo "</div>";
//
// Liste des mots-cles de l'article
//

//if ($options == 'avancees' AND $GLOBALS['meta']["articles_mots"] != 'non') {
  //var_dump($cherche_mot);
  echo publiHAL_formulaire_mots('syndic_article', $id_syndic_article, $cherche_mot, $select_groupe, $flag_editable);
//}

//// Les langues
//
//  if (($GLOBALS['meta']['multi_articles'] == 'oui')
//	OR (($GLOBALS['meta']['multi_rubriques'] == 'oui') AND ($GLOBALS['meta']['gerer_trad'] == 'oui'))) {
//
//    echo formulaire_referencer_traduction($id_article, $id_rubrique, $id_trad,  $flag_editable, $trad_err);
//  }

 echo pipeline('affiche_milieu',array('args'=>array('exec'=>'publihal_publi','id_syndic_article'=>$id_syndic_article),'data'=>''));

// if ($statut_rubrique)
//   echo debut_cadre_relief('', true),
//     "\n<div id='instituer_article-$id_article'>",     
//     formulaire_instituer_article($id_article, $statut_article, 'articles', "id_article=$id_article"),
//     '</div>',
//     fin_cadre_relief('', true);

 publiHAL_afficher_corps_publi($id_syndic_article,$titre,$url,$date,$lesauteurs,$maj,$statut,$descriptif,$lang,$url_source,$source,$tags);

// if ($flag_editable) {
//	echo "\n<div align='$spip_lang_right'><br />";
//	bouton_modifier_articles($id_article, $id_rubrique, $modif,_T('texte_travail_article', $modif), "warning-24.gif", "");
//	echo "</div>";
// }

//
// Documents associes a l'article
//

// if ($spip_display != 4)
// afficher_documents_et_portfolio($id_article, "article", $flag_editable);
//
// if ($flag_auteur AND  $statut_article == 'prepa' AND !$statut_rubrique)
//	echo demande_publication($id_article);
//
// echo "</div>";
 echo "</div>";
 fin_cadre_relief();

//  echo "<br /><br />";
//  
//  $tm = rawurlencode($titre);
//  echo "\n<div align='center'>";
//  icone(_T('icone_poster_message'), generer_url_ecrire("forum_envoi","statut=prive&id_article=$id_article&titre_message=$tm&url=" . generer_url_retour("articles","id_article=$id_article")), "forum-interne-24.gif", "creer.gif");
//  echo "</div><br />";
//  echo  "<div id='forum'>", exec_discuter_dist($id_article, $debut),"</div>";

  fin_page();

}

/**
 * PAS UTILISÉ !
 */
// http://doc.spip.org/@bouton_modifier_articles
function publiHAL_bouton_modifier_publi($id_article, $id_rubrique, $flag_modif, $mode, $ip, $im)
{
	if ($flag_modif) {
	  icone(_T('icone_modifier_article'), generer_url_ecrire("articles_edit","id_article=$id_article"), $ip, $im);
		echo "<font face='arial,helvetica,sans-serif' size='2'>$mode</font>";
		echo aide("artmodif");
	}
	else {
		icone(_T('icone_modifier_article'), generer_url_ecrire("articles_edit","id_article=$id_article"), "article-24.gif", "edit.gif");
	}

}
/**
 * AFFICHAGE DU CORPS DE LA PUBLI
 */
// http://doc.spip.org/@afficher_corps_articles
function publiHAL_afficher_corps_publi($id_syndic_article,$titre,$url,$date,$lesauteurs,$maj,$statut,$descriptif,$lang,$url_source,$source,$tags)
{
	global $revision_nbsp, $activer_revision_nbsp, $champs_extra, $les_notes, $dir_lang;
	global $connect_statut, $spip_lang_right;
	

	echo "\n\n<div align='justify' style='padding: 10px;'>";

//	echo "<div $dir_lang><b>";
//	echo propre($titre);
//	echo "</b></div>\n\n";
	
	echo "<div $dir_lang><b>Publisher&nbsp;:</b> ";
	echo propre(publiHAL_extraction_publisher($tags));
	echo "</div>\n\n";
	echo "<div $dir_lang><b>Date&nbsp;:</b> ";
	echo propre(affdate_mois_annee($date));
	echo "</div>\n\n";
	
	echo "<br><div $dir_lang><i><b>(";
	echo propre(presenteAuteursPubli($lesauteurs));
	echo ")</b></i><br clear='all' />";
	echo "</div>";
	
	echo "<div $dir_lang>";
	echo propre($descriptif);
	echo "<br clear='all' />";
	echo "</div>";
		// tags
	if ($tags){
		$tags=publiHAL_extraction_tags($tags);
		$sep='';
//		echo "<div style='float:$spip_lang_right;'><b>TAGS&nbsp;:</b> <em>";
		echo "<div><b>TAGS&nbsp;:</b> <em>";
		//var_dump($tags['tag']);
		foreach ($tags['tag'] as $tag) {
			echo $sep.'<a rel="tag">'.$tag['val'].'</a>';
			$sep=', ';
		}
		echo '</em></div>';
		echo "<br clear='all' />";
		if(count($tags['coverage'])){
			$sep='';
			echo "<div><b>MOTS-CLÉS </b>(non Spip)<b>&nbsp;:</b> <em>";
			foreach ($tags['coverage'] as $tag) {
				$motsClef=explode(";",$tag['val']);
				echo $sep.'<a rel="coverage">'.implode('</a>&nbsp;; <a rel="coverage">',$motsClef).'</a>';
				$sep=', ';
			}
			echo '</em></div>';
			echo "<br clear='all' />";
		}
		if(count($tags['typedoc'])){
			$sep='';
			echo "<div><b>TYPE DE DOCUMENT </b>(non Spip)<b>&nbsp;:</b> <em>";
			foreach ($tags['typedoc'] as $tag) {
				echo $sep.'<a rel="typedoc">'.$tag['val'].'</a>';
				$sep=', ';
			}
			echo '</em></div>';
			echo "<br clear='all' />";
		}
	}
	
	echo "<br> <br> <div class='verdana2'>";
	echo "id_syndic_article : <b>".$id_syndic_article;
	echo "</b></div>";
	echo "</div>";
}

// http://doc.spip.org/@formulaire_mots
function publiHAL_formulaire_mots($objet, $id_objet, $cherche_mot, $select_groupe, $flag_editable) {
	global $connect_statut, $spip_lang_rtl, $spip_lang_right, $spip_lang;

	$visible = ($cherche_mot OR ($flag_editable === 'ajax'));

	if ($objet == 'syndic_article') {
		$table_id = 'id_syndic_article';
		$table = 'syndic_articles';
		$url_base = "publihal_publi";
	}
	else {
		spip_log("erreur dans formulaire_mots($objet, $id_objet, $cherche_mot, $select_groupe, $flag_editable)");
		return '';
	}

	$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_mots AS mots, spip_mots_$table AS lien WHERE lien.$table_id=$id_objet AND mots.id_mot=lien.id_mot"));

	if (!($nombre_mots = $cpt['n'])) {
		if (!$flag_editable) return;
//		$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_groupes_mots WHERE $table = 'oui'	AND ".substr($connect_statut,1)." = 'oui'"));
//
//		if (!$cpt['n']) return;
	}

	//
	// Preparer l'affichage
	//

	// La reponse
	$reponse = '';
	if ($flag_editable AND $cherche_mot) {
		$reindexer = false;
		list($reponse, $nouveaux_mots) = recherche_mot_cle($cherche_mot, $select_groupe, $objet, $id_objet, $table, $table_id, $url_base);
		foreach($nouveaux_mots as $nouv_mot) {
			if ($nouv_mot!='x') {
				$reindexer |= inserer_mot("spip_mots_$table", $table_id, $id_objet, $nouv_mot);
			}
		}
		if ($reindexer AND ($GLOBALS['meta']['activer_moteur'] == 'oui')) {
			include_spip("inc/indexation");
			marquer_indexer("spip_$table", $id_objet);
		}
	}
		
	$form = afficher_mots_cles($flag_editable, $objet, $id_objet, $table, $table_id, $url_base, $visible);

	// Envoyer titre + div-id + formulaire + fin
	if ($flag_editable){
		if ($visible)
			$bouton = bouton_block_visible("lesmots");
		else
			$bouton =  bouton_block_invisible("lesmots");
	} else $bouton = '';

	$bouton .= _T('titre_mots_cles').aide ("artmots");

	$res =  '<div>&nbsp;</div>' // place pour l'animation pendant Ajax
	. debut_cadre_enfonce("mot-cle-24.gif", true, "", $bouton)
	  . $reponse
	  . $form
	  . fin_cadre_enfonce(true);

	return ($flag_editable === 'ajax') 
	  ? $res
	  : "\n<div id='editer_mot-$id_objet'>$res</div>";
}


?>