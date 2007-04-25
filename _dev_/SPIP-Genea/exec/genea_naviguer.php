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

// -- Chargement des fonctions supplementaires --------------------------	
	include_spip('inc/presentation');
	include_spip('base/abstract_sql');
/*
include_spip('inc/texte');
include_spip('inc/rubriques');
include_spip('inc/actions');
include_spip('inc/forum');
include_spip('inc/mots');
include_spip('inc/documents');
charger_generer_url();*/

// -- Fonction d'apppel de la partie d'administration des arbres
function exec_genea_naviguer() {
///// Definition des variables generales
	global $connect_statut, $connect_toutes_rubriques, $titre;
	
	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	include_spip('public/assembler');
	
	$titre = _T('genea:titre');
	$ze_logo = _DIR_PLUGIN_GENEA."/img_pack/arbre-24.png";
	
///// Verification de l'existence de la base
	genea_verifier_base();
	
///// debut de la page

	pipeline('exec_init',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	debut_page(_T('genea:titre'));

		debut_grand_cadre();
		fin_grand_cadre();

//		changer_typo('', 'rubrique'.$id_rubrique);

		debut_gauche();
			debut_boite_info();
	        	echo "<font face='Verdana,Arial,Sans,sans-serif' size=1><b>".strtoupper(_T('genea:nom_plugin'))."</b><p />"._T('genea:boite_info');
				echo '<br /><br />Version : <strong>';
				echo genea_version();
				echo '</strong>';
        	fin_boite_info();

/*	  if ($spip_display != 4) {

//		infos_naviguer($id_rubrique, $statut);

//
// Logos de la rubrique
//
		if ($flag_editable AND ($spip_display != 4)) {
			include_spip('inc/chercher_logo');
			echo afficher_boite_logo('id_individu', $id_individu, ($id_individu ? _T('logo_individu') : _T('logo_standard_rubrique'))." ".aide ("indivlogo"), _T('logo_survol'), 'naviguer');
		}

//
// Afficher les boutons de creation d'article et de breve
// */
//			raccourcis_naviguer($id_rubrique, $id_parent);
			debut_raccourcis();
				icone_horizontale(_T('genea:creer_arbre'), parametre_url(generer_url_ecrire("genea_naviguer"),"new","oui"), '../'._DIR_PLUGIN_GENEA.'/img_pack/arbre-24.png', "creer.gif");
				icone_horizontale(_T('genea:creer_sosa'), generer_url_ecrire("genea_cree_sosa"), '../'._DIR_PLUGIN_GENEA.'/img_pack/arbre-24.png', "");
				icone_horizontale(_T('genea:import_arbre'), generer_url_ecrire("genea_import"), '../'._DIR_PLUGIN_GENEA.'/img_pack/arbre-24.png', "../"._DIR_PLUGIN_GENEA."/img_pack/communicate.gif");
				icone_horizontale(_T('genea:export_arbre'), generer_url_ecrire("genea_export"), '../'._DIR_PLUGIN_GENEA.'/img_pack/arbre-24.png', "../"._DIR_PLUGIN_GENEA."/img_pack/communicate.gif");
				icone_horizontale(_T('genea:supprimer_arbre'), generer_url_ecrire("genea_efface_arbre"), '../'._DIR_PLUGIN_GENEA.'/img_pack/arbre-24.png', "supprimer.gif");
			fin_raccourcis();
		
//	  }
		
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));
		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));	  
		debut_droite();

	  debut_cadre_relief($ze_logo);
		  gros_titre(_T('genea:titre'));
		  
			echo recuperer_fond("exec/genea_naviguer",$contexte);

/*
	  montre_naviguer($id_rubrique, $titre, $descriptif, $ze_logo, $flag_editable);

	  if ($champs_extra AND $extra) {
		include_spip('inc/extra');
		extra_affichage($extra, "rubriques");
	  }

/// Mots-cles
	    if ($GLOBALS['meta']["articles_mots"] != 'non' AND $id_rubrique > 0) {
	      echo "\n<p>",
		formulaire_mots('rubrique', $id_rubrique,  $cherche_mot,  $select_groupe, $flag_editable);
	    }


	    if (strlen($texte) > 1) {
	      echo "\n<p><div align='justify'><font size=3 face='Verdana,Arial,Sans,sans-serif'>", justifier(propre($texte)), "&nbsp;</font></div>";
	    }


//
// Langue de la rubrique
//

	    langue_naviguer($id_rubrique, $id_parent, $flag_editable);
*/	    
	    fin_cadre_relief();


//
// Gerer les modifications...
//

	    contenu_naviguer($id_rubrique, $id_parent, $ze_logo, $flag_editable);

	    fin_page();
}

// http://doc.spip.org/@raccourcis_naviguer
function raccourcis_naviguer() 
{
	global $connect_statut;

	debut_raccourcis();
	
	icone_horizontale(_T('icone_tous_articles'), generer_url_ecrire("articles_page"), "article-24.gif");
	
	$n = spip_num_rows(spip_query("SELECT id_rubrique FROM spip_rubriques LIMIT 1"));
	if ($n) {
		if ($id_rubrique > 0)
			icone_horizontale(_T('icone_ecrire_article'), generer_url_ecrire("articles_edit","id_rubrique=$id_rubrique&new=oui"), "article-24.gif","creer.gif");
	
		$activer_breves = $GLOBALS['meta']["activer_breves"];
		if ($activer_breves != "non" AND $id_parent == "0" AND $id_rubrique != "0") {
			icone_horizontale(_T('icone_nouvelle_breve'), generer_url_ecrire("breves_edit","id_rubrique=$id_rubrique&new=oui"), "breve-24.gif","creer.gif");
		}
	}
	else {
		if ($connect_statut == '0minirezo') {
			echo "<p>"._T('info_creation_rubrique');
		}
	}
	
	fin_raccourcis();
}

// http://doc.spip.org/@langue_naviguer
function langue_naviguer($id_rubrique, $id_parent, $flag_editable)
{

if ($id_rubrique>0 AND $GLOBALS['meta']['multi_rubriques'] == 'oui' AND ($GLOBALS['meta']['multi_secteurs'] == 'non' OR $id_parent == 0) AND $flag_editable) {

	$row = spip_fetch_array(spip_query("SELECT lang, langue_choisie FROM spip_rubriques WHERE id_rubrique=$id_rubrique"));
	$langue_rubrique = $row['lang'];
	$langue_choisie_rubrique = $row['langue_choisie'];
	if ($id_parent) {
		$row = spip_fetch_array(spip_query("SELECT lang FROM spip_rubriques WHERE id_rubrique=$id_parent"));
		$langue_parent = $row['lang'];
	} 
	if (!$langue_parent)
		$langue_parent = $GLOBALS['meta']['langue_site'];
	if (!$langue_rubrique)
		$langue_rubrique = $langue_parent;

	debut_cadre_enfonce('langues-24.gif');
	echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100% BACKGROUND=''><TR><TD BGCOLOR='#EEEECC' class='serif2'>";
	echo bouton_block_invisible('languesrubrique');
	echo "<B>";
	echo _T('titre_langue_rubrique');
	echo "&nbsp; (".traduire_nom_langue($langue_rubrique).")";
	echo "</B>";
	echo "</TD></TR></TABLE>";

	echo debut_block_invisible('languesrubrique');
	echo "<div class='verdana2' align='center'>";
	echo menu_langues('changer_lang', $langue_rubrique, '', $langue_parent, redirige_action_auteur('instituer_langue_rubrique', "$id_rubrique-$id_parent","naviguer","id_rubrique=$id_rubrique"), $ze_logo, "supprimer.gif");
	echo "</div>\n";
	echo fin_block();

	fin_cadre_enfonce();
 }
}

// http://doc.spip.org/@contenu_naviguer
function contenu_naviguer($id_rubrique, $id_parent, $ze_logo,$flag_editable) {

global $connect_statut, $connect_toutes_rubriques, $options, $spip_lang_left, $spip_lang_right;

///// Afficher les rubriques 
//afficher_enfant_rub($id_rubrique, $flag_editable);


//echo "<div align='$spip_lang_left'>";


//////////  Vos articles en cours de redaction
/////////////////////////

echo "<P>";

//
// Verifier les boucles a mettre en relief
//

$relief = spip_num_rows(spip_query("SELECT id_article FROM spip_articles AS articles WHERE id_rubrique='$id_rubrique' AND statut='prop' LIMIT 1"));

if (!$relief) {
	$relief = spip_num_rows(spip_query("SELECT id_breve FROM spip_breves WHERE id_rubrique='$id_rubrique' AND (statut='prepa' OR statut='prop') LIMIT 1"));
 }

if (!$relief AND $GLOBALS['meta']['activer_syndic'] != 'non') {
	$relief = spip_num_rows(spip_query("SELECT id_syndic FROM spip_syndic WHERE id_rubrique='$id_rubrique' AND statut='prop' LIMIT 1"));
 }

if (!$relief AND $GLOBALS['meta']['activer_syndic'] != 'non' AND $connect_statut == '0minirezo' AND $connect_toutes_rubriques) {
	$relief = spip_num_rows(spip_query("SELECT id_syndic FROM spip_syndic WHERE id_rubrique='$id_rubrique' AND (syndication='off' OR syndication='sus') AND statut='publie' LIMIT 1"));
 }


if ($relief) {
	echo "<p>";
	debut_cadre_couleur();
	echo "<div class='verdana2' style='color: black;'><b>"._T('texte_en_cours_validation')."</b></div><p>";

	//
	// Les articles a valider
	//
	afficher_articles(_T('info_articles_proposes'),	array('WHERE' => "id_rubrique='$id_rubrique' AND statut='prop'", 'ORDER BY' => "date DESC"));

	//
	// Les breves a valider
	//
	afficher_breves(_T('info_breves_valider'), array("FROM" => 'spip_breves', 'WHERE' => "id_rubrique='$id_rubrique' AND (statut='prepa' OR statut='prop')", 'ORDER BY' => "date_heure DESC"), true);


	//
	// Les sites references a valider
	//
	if ($GLOBALS['meta']['activer_sites'] != 'non') {
		include_spip('inc/sites_voir');
		afficher_sites(_T('info_site_valider'), array("FROM" => 'spip_syndic', 'WHERE' => "id_rubrique='$id_rubrique' AND statut='prop'", 'ORDER BY' => "nom_site"));
	}

	//
	// Les sites a probleme
	//
	if ($GLOBALS['meta']['activer_sites'] != 'non' AND $connect_statut == '0minirezo' AND $connect_toutes_rubriques) {
		include_spip('inc/sites_voir');
		afficher_sites(_T('avis_sites_syndiques_probleme'), array('FROM' => 'spip_syndic', 'WHERE' => "id_rubrique='$id_rubrique' AND (syndication='off' OR syndication='sus') AND statut='publie'", 'ORDER BY' => "nom_site"));
	}

	// Les articles syndiques en attente de validation
	if ($id_rubrique == 0
	AND $connect_statut == '0minirezo' AND $connect_toutes_rubriques) {
		$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_syndic_articles WHERE statut='dispo'"));
		if ($cpt = $cpt['n'])
			echo "<br /><small>
  <a href='",				generer_url_ecrire("sites_tous"),				"' style='color: black;'>",
				$cpt,
				" ",
				_T('info_liens_syndiques_1'),
				" ",
				_T('info_liens_syndiques_2'),
				"</a></small>";
	}

	fin_cadre_couleur();
}

//////////  Les articles en cours de redaction
/////////////////////////

	if ($connect_statut == "0minirezo" AND $options == 'avancees') {
	  afficher_articles(_T('info_tous_articles_en_redaction'), array("WHERE" => "statut='prepa' AND id_rubrique='$id_rubrique'", 'ORDER BY' => "date DESC"));
	}


//////////  Les articles publies
/////////////////////////

	  afficher_articles(_T('info_tous_articles_presents'), array("WHERE" => "statut='publie' AND id_rubrique='$id_rubrique'", 'ORDER BY' => "date DESC"), true);



	if ($id_rubrique > 0){
	  echo "<div align='$spip_lang_right'>";
	  icone(_T('icone_ecrire_article'), generer_url_ecrire("articles_edit","id_rubrique=$id_rubrique&new=oui"), "article-24.gif", "creer.gif");
	  echo "</div><p>";
	}

//// Les breves

	afficher_breves(_T('icone_ecrire_nouvel_article'), array("FROM" => 'spip_breves', 'WHERE' => "id_rubrique='$id_rubrique' AND statut != 'prop' AND statut != 'prepa'", 'ORDER BY' => "date_heure DESC"));

	$activer_breves=$GLOBALS['meta']["activer_breves"];

	if ($id_parent == "0" AND $id_rubrique != "0" AND $activer_breves!="non"){
	  echo "<div align='$spip_lang_right'>";
	  icone(_T('icone_nouvelle_breve'), generer_url_ecrire("breves_edit","id_rubrique=$id_rubrique&new=oui"), "breve-24.gif", "creer.gif");
	  echo "</div><p>";
	}

//// Les sites references

	if ($GLOBALS['meta']["activer_sites"] == 'oui') {
		include_spip('inc/sites_voir');
		afficher_sites(_T('titre_sites_references_rubrique'), array("FROM" => 'spip_syndic', 'WHERE' => "id_rubrique='$id_rubrique' AND statut!='refuse' AND statut != 'prop' AND syndication NOT IN ('off','sus')", 'ORDER BY' => 'nom_site'));

		if ($id_rubrique > 0 AND ($flag_editable OR $GLOBALS['meta']["proposer_sites"]> 0)) {
	
		echo "<div align='$spip_lang_right'>";
		icone(_T('info_sites_referencer'), generer_url_ecrire('sites_edit', "id_rubrique=$id_rubrique&redirect=" . generer_url_retour('naviguer', "id_rubrique=$id_rubrique")), "site-24.gif", "creer.gif");
		echo "</div><p>";
	  }
	}

/// Documents associes a la rubrique
	if ($id_rubrique > 0) {

	  afficher_documents_et_portfolio($id_rubrique, "rubrique", $flag_editable);
	}

////// Supprimer cette rubrique (si vide)

	bouton_supprimer_naviguer($id_rubrique, $id_parent, $ze_logo, $flag_editable);
}

// http://doc.spip.org/@montre_naviguer
function montre_naviguer($id_rubrique, $titre, $descriptif, $logo, $flag_editable)
{
  global $spip_lang_right, $spip_lang_left;

  echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
  echo "<tr width='100%'><td width='100%' valign='top'>";
  gros_titre((!acces_restreint_rubrique($id_rubrique) ? '' :
		http_img_pack("admin-12.gif",'', "width='12' height='12'",
			      _T('info_administrer_rubrique'))) .
	     $titre);
  echo "</td>";

  if ($id_rubrique > 0 AND $flag_editable) {
	echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
	echo "<td  align='$spip_lang_right' valign='top'>";
	icone(_T('icone_modifier_rubrique'), generer_url_ecrire("rubriques_edit","id_rubrique=$id_rubrique&retour=nav"), $logo, "edit.gif");
	echo "</td>";
}
  echo "</tr>\n";

  if (strlen($descriptif) > 1) {
	echo "<tr><td>\n";
	echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
	echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
	echo propre($descriptif."~");
	echo "</font>";
	echo "</div></td></tr>\n";
  }
  echo "</table>\n";
}

// http://doc.spip.org/@tester_rubrique_vide
function tester_rubrique_vide($id_rubrique) {
	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_rubriques WHERE id_parent='$id_rubrique' LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_articles WHERE id_rubrique='$id_rubrique' AND (statut='publie' OR statut='prepa' OR statut='prop') LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_breves WHERE id_rubrique='$id_rubrique' AND (statut='publie' OR statut='prop') LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_syndic WHERE id_rubrique='$id_rubrique' AND (statut='publie' OR statut='prop') LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_documents_rubriques WHERE id_rubrique='$id_rubrique' LIMIT 1"));
	if ($n['n'] > 0) return false;

	return true;
}

// http://doc.spip.org/@bouton_supprimer_naviguer
function bouton_supprimer_naviguer($id_rubrique, $id_parent, $ze_logo, $flag_editable)
{
	if (($id_rubrique>0) AND tester_rubrique_vide($id_rubrique) AND $flag_editable) {

		echo "<p><div align='center'>";
		icone(_T('icone_supprimer_rubrique'), redirige_action_auteur('supprimer', "rubrique-$id_rubrique", "naviguer","id_rubrique=$id_parent"), $ze_logo, "supprimer.gif");
		echo "</div><p>";
	}
}


// http://doc.spip.org/@enregistre_creer_naviguer
function enregistre_creer_naviguer($id_parent)
{
	return spip_abstract_insert("spip_rubriques", 
			"(titre, id_parent)",
			"('"._T('item_nouvelle_rubrique')."', '$id_parent')");
}

// http://doc.spip.org/@enregistre_modifier_naviguer
function enregistre_modifier_naviguer($id_rubrique, $id_parent, $titre, $texte, $descriptif)
{
	// si c'est une rubrique-secteur contenant des breves, ne deplacer
	// que si $confirme_deplace == 'oui', et changer l'id_rubrique des
	// breves en question
	if ($GLOBALS['confirme_deplace'] == 'oui'
	AND $id_parent > 0) {
		$id_secteur = spip_fetch_array(spip_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique=$id_parent"));
		if ($id_secteur= $id_secteur['id_secteur'])
			spip_query("UPDATE spip_breves	SET id_rubrique=$id_secteur	WHERE id_rubrique=$id_rubrique");
	} else
		$id_parent = 0;

	if ($GLOBALS['champs_extra']) {
			include_spip('inc/extra');
			$extra = extra_recup_saisie("rubriques");
	}
	else $extra = '';

	spip_query("UPDATE spip_rubriques SET " .  (acces_rubrique($id_parent) ? "id_parent=$id_parent," : "") . "titre=" . spip_abstract_quote($titre) . ", descriptif=" . spip_abstract_quote($descriptif) . ", texte=" . spip_abstract_quote($texte) . (!$extra ? '' :  ", extra = " . spip_abstract_quote($extra) . "") . "WHERE id_rubrique=$id_rubrique");
	if ($GLOBALS['meta']['activer_moteur'] == 'oui') {
			include_spip("inc/indexation");
			marquer_indexer('spip_rubriques', $id_rubrique);
	}
	propager_les_secteurs();
}

// affichage de la version en cours de Genea-SPIP à partir de plugin.xml
function genea_version(){
	$result = 0.0;
	$Tlecture_fich_plugin = file(_DIR_PLUGIN_GENEA.'/plugin.xml');
	foreach ($Tlecture_fich_plugin as $ligne) {
		if (substr_count($ligne, '<version>') > 0) {
			$ligne_xml = parse_plugin_xml($ligne);
			$result = doubleval($ligne_xml['version'][0]);
			break;
		}
	}
	return $result;
}

function genea_install(){
	genea_verifier_base();
}

function genea_uninstall(){
//	include_spip('base/genea_base');
}

function genea_verifier_base(){
	$version_base = genea_version(); //version actuelle
	$current_version = 0.0;
	
	if ((!isset($GLOBALS['meta']['genea_base_version'])) 
		|| (($current_version = $GLOBALS['meta']['genea_base_version'])!=$version_base)){
		include_spip('base/genea_base');

		if ($current_version==0.0){
			include_spip('base/create');
			creer_base();
//			spip_query("INSERT INTO spip_asso_profil (nom) VALUES('')");
			ecrire_meta('genea_base_version',$current_version=$version_base);
		}
		ecrire_metas();
	}
}
?>