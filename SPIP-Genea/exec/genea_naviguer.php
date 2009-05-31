<?php

/*******************************************************************
 *
 * Copyright (c) 2007-2008
 * Xavier BUROT
 * fichier : exec/genea_naviguer
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL
 *
 * *******************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// -- Chargement des fonctions supplementaires --------------------------
	include_spip('inc/actions');
	include_spip('inc/presentation');
	include_spip('base/abstract_sql');
	include_spip('genea_fonctions');
/*
include_spip('inc/texte');
include_spip('inc/rubriques');
include_spip('inc/actions');
include_spip('inc/forum');
include_spip('inc/mots');
include_spip('inc/documents');
charger_generer_url();*/

global $titre, $ze_logo_genea;

$titre = _T('genea:titre');
$ze_logo_genea = url_absolue(find_in_path('/images/arbre-24.png'));


// -- Fonction d'apppel de la partie d'administration des arbres
function exec_genea_naviguer_dist() {
	switch ($_GET['action']) {
		case 'creer' :
			genea_modif_arbre(0);
			break;
		case 'modif' :
			genea_modif_arbre($_GET['id_genea']);
			break;
		case 'voir' :
			genea_voir_arbre($_GET['id_genea']);
			break;
		case 'efface' :
			genea_efface_arbre($_GET['id_genea']);
			break;
		default:
			genea_affiche_liste_arbre();
			break;
	}
}

function genea_affiche_liste_arbre(){
	pipeline('exec_init',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genea:titre'), 'naviguer', 'genea');

	// Controle d'acces a la gestion des arbres genealogiques
	if (!autoriser('voir', 'genea', $id_genea)) {
		echo "<strong>" . _T('avis_acces_interdit') . "</strong>";
		echo fin_page();
		exit;
	}

	echo "<br /><br /><br />\n";
	gros_titre(_T('genea:titre'));
	echo barre_onglets("genea", "contenu");

	debut_gauche();

	genea_boite_info(_T('genea:affiche_liste_arbres'), _T('genea:boite_info'));

	genea_raccourcis();

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	creer_colonne_droite();

	echo pipeline('affiche_droite',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	debut_droite();

	// Affiche les arbres en attente d'affectation a une rubrique
	$relief = spip_num_rows(spip_query("SELECT id_genea FROM spip_genea WHERE id_rubrique<1 LIMIT 1"));

	if ($relief) {
		$res = debut_cadre_couleur('', true);
		$res .= "<div class='verdana2' style='coloe: black;'><b>" . _T('genea:texte_en_attente') . "</b></div>";
 		$res .= genea_afficher_arbres('<b>' . _T('genea:titre_arbres_attente') . '</b>', array("SELECT" => 'spip_genea.*, COUNT(spip_genea_individus.id_individu) AS nombre',"FROM" => 'spip_genea INNER JOIN spip_genea_individus', "WHERE" => 'spip_genea.id_genea=spip_genea_individus.id_genea AND spip_genea.id_rubrique<1', "GROUP BY" => 'spip_genea.id_genea', "ORDER BY"=>'spip_genea.id_genea'));
 		$res .= fin_cadre_couleur(true);
		echo $res;
	}

	echo genea_afficher_arbres('<b>' . _T('genea:titre_arbres_tous') . '</b>', array("SELECT" => 'spip_genea.*, spip_rubriques.titre, COUNT(spip_genea_individus.id_individu) AS nombre',"FROM" => 'spip_genea INNER JOIN spip_rubriques, spip_genea_individus', "WHERE" => 'spip_genea.id_genea=spip_genea_individus.id_genea AND spip_genea.id_rubrique=spip_rubriques.id_rubrique AND spip_genea.id_rubrique>0', "GROUP BY" => 'spip_genea.id_genea', "ORDER BY"=>'spip_genea.id_genea'));

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	echo fin_gauche(), fin_page();
}

function genea_modif_arbre($id_genea) {
	pipeline('exec_init',array('args'=>array('exec'=>'genea_naviguer','id_genea'=>$id_genea),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genea:titre'), 'naviguer', 'genea');

	// Controle d'acces a la gestion des arbres genealogiques
	if (!autoriser('voir', 'genea', $id_genea)) {
		echo "<strong>" . _T('avis_acces_interdit') . "</strong>";
		echo fin_page();
		exit;
	}

	echo "<br /><br /><br />\n";
	echo gros_titre(_T('genea:titre'));
	echo barre_onglets("genea", "contenu");

	echo debut_gauche();

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	echo "modif arbre";

	creer_colonne_droite();

	echo pipeline('affiche_droite',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	debut_droite();

	debut_cadre_formulaire();

	echo "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
	echo "<tr>";
	echo "<td>";

// -*- pour test -*-
	$titre = "Nom de l'arbre";
// -----------------

//	if ($id_genea)
	 icone(_T('icone_retour'), generer_url_ecrire("genea_naviguer","action=voir&id_rubrique=$id_rubrique"), $ze_logo_genea, "rien.gif");
//	else icone(_T('icone_retour'), generer_url_ecrire("naviguer","id_rubrique=$id_parent"), $ze_logo, "rien.gif");

	echo "</td>";
	echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
	echo "<td style='width: 100%'>";
	echo _T('genea:info_modifier_arbre');
	gros_titre($titre);
	echo "</td></tr></table>";

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'genea_naviguer','action'=>'voir','id_rubrique'=>$id_rubrique),'data'=>''));

	$chercher_rubrique = charger_fonction('chercher_rubrique', 'inc');

	$form = "<br/>"
//	.  "<input type='text' class='formo' name='titre' value=\"$titre\" size='40' $onfocus />"
	. debut_cadre_couleur("$logo_parent", true, '', _T('genea:texte_rubrique_rattachee').aide ("geneavoir"))
	. $chercher_rubrique($id_parent, 'rubrique', !$connect_toutes_rubriques, $id_rubrique);

	echo $form;

	echo fin_cadre_formulaire();

	echo fin_gauche(), fin_page();
}

function genea_voir_arbre($id_genea) {
	global $ze_logo_genea;

	pipeline('exec_init',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genea:titre'), 'naviguer', 'genea');

	// Controle d'acces a la gestion des arbres genealogiques
	if (!autoriser('voir', 'genea', $id_genea)) {
		echo "<strong>" . _T('avis_acces_interdit') . "</strong>";
		echo fin_page();
		exit;
	}

	echo "<br /><br /><br />\n";
	echo gros_titre(_T('genea:titre'));
	echo barre_onglets("genea", "contenu");

	echo debut_gauche();

	if ($id_genea>0) {
		echo genea_infos_naviguer($id_genea, $id_rubrique);
		echo "<br />";
	}

	if ($spip_display != 4) {
		genea_raccourcis();
		genea_boite_info(_T('genea:affiche_voir_arbre'), _T('genea:boite_info_voir'));
	}

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	creer_colonne_droite();

	echo pipeline('affiche_droite',array('args'=>array('exec'=>'genea_naviguer'),'data'=>''));

	debut_droite();

	debut_cadre_relief($ze_logo);

  global $spip_lang_right, $spip_lang_left;

  $flag_editable = ture; // pour le test

  echo "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
  echo "<tr><td style='width: 100%' valign='top'>";
  gros_titre((!acces_restreint_rubrique($id_rubrique) ? '' :
		http_img_pack("admin-12.gif",'', "width='12' height='12'",
			      _T('info_administrer_rubrique'))) .
	     $titre);
  echo "</td>";

  if ($id_genea > 0 AND $flag_editable) {
	echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
	echo "<td  align='$spip_lang_right' valign='top'>";
	icone(_T('icone_modifier_arbre'), generer_url_ecrire("genea_naviguer","action=modif&id_genea=$id_genea&retour=nav"), $ze_logo_genea, "edit.gif");
	echo "</td>";
}
  echo "</tr>\n";

  if (strlen($descriptif) > 1) {
	echo "<tr><td><div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa; ' class='verdana1 spip_small'>", propre($descriptif."~"), "</div></td></tr>\n";
  }
  echo "</table>\n";


	fin_cadre_relief();

	echo fin_gauche(), fin_page();
}

function genea_efface_arbre($id_genea) {
	echo "efface arbre";
}

function genea_boite_info($titre, $texte){
	debut_boite_info();
		echo "<font face='Verdana,Arial,Sans,sans-serif' size=1><b>$titre</b></p><p>$texte";
		echo '<br /><br />';
		echo 'Plugin : <strong>' . strtoupper(_T('genea:nom_plugin')) . '</strong><br />';
		echo 'Version plugin : <strong>' . balise_GENEA_VERSION_PLUGIN() . '</strong><br />';
		echo 'Version base : <strong>' . balise_GENEA_VERSION_BASE() . '</strong><br />';
	fin_boite_info();
}

function genea_infos_naviguer($id_genea, $id_rubrique=0){
	debut_boite_info();

	$res = "\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>"
		  .  _T('genea:titre_arbre_numero') . " :"
		  . "<br /><span class='spip_xx-large'>"
		  . $id_genea
		  . '</span></div>';
	echo $res;
	if ($id_rubrique>0) voir_en_ligne ('rubrique', $id_rubrique, 'publie');
	fin_boite_info();
}

function genea_raccourcis($id_genea=0){
	global $ze_logo_genea;
	debut_raccourcis();
		icone_horizontale(_T('genea:creer_arbre'), parametre_url(generer_url_ecrire("genea_naviguer"),"action","creer"), $ze_logo_genea, "creer.gif");
		if ($id_genea>0) {
			icone_horizontale(_T('genea:creer_sosa'), generer_url_ecrire("genea_cree_sosa"), $ze_logo_genea, "");
			icone_horizontale(_T('genea:import_arbre'), generer_url_ecrire("genea_import"), $ze_logo_genea, url_absolue(find_in_path('/images/communicate.gif')));
			icone_horizontale(_T('genea:export_arbre'), generer_url_ecrire("genea_export"), $ze_logo_genea, url_absolue(find_in_path('/images/communicate.gif')));
			icone_horizontale(_T('genea:supprimer_arbre'), parametre_url(parametre_url(generer_url_ecrire("genea_naviguer"), "action", "efface"), "id_genea", $id_genea), $ze_logo_genea, "supprimer.gif");
		}
	fin_raccourcis();
}

//
// Afficher tableau d'arbres
//
function genea_afficher_arbres($titre, $requete, $formater='') {

	if (!isset($requete['FROM'])) $requete['FROM'] = 'spip_genea AS genea';

	if (!isset($requete['SELECT'])) {
		$requete['SELECT'] = "genea.id_genea, genea.id_rubrique";
	}

	if (!isset($requete['GROUP BY'])) $requete['GROUP BY'] = '';

	$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM " . $requete['FROM'] . ($requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '') . ($requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '')));

	if (!$cpt = $cpt['n']) return '' ;


//	$requete['FROM'] = preg_replace("/(spip_genea AS \w*)/", "\\1 LEFT JOIN spip_petitions AS petitions USING (id_article)", $requete['FROM']);

//	$requete['SELECT'] .= ", petitions.id_article AS petition ";

	// memorisation des arguments pour gerer l'affichage par tranche
	// et/ou par langues.

	$hash = substr(md5(serialize($requete) . $GLOBALS['meta']['gerer_trad'] . $titre), 0, 31);
	$tmp_var = 't' . substr($hash, 0, 7);
	$nb_aff = floor(1.5 * _TRANCHES);
	$deb_aff = intval(_request($tmp_var));

	//
	// Stocke la fonction ajax dans le fichier temp pour exec=memoriser
	//

	// on lit l'existant
	lire_fichier(_DIR_SESSIONS.'ajax_fonctions.txt', $ajax_fonctions);
	$ajax_fonctions = @unserialize($ajax_fonctions);

	// on ajoute notre fonction
	if (isset($requete['LIMIT'])) $cpt = min($requete['LIMIT'], $cpt);
	$v = array(time(), $titre, $requete, $tmp_var, $formater);
	$ajax_fonctions[$hash] = $v;

	// supprime les fonctions trop vieilles
	foreach ($ajax_fonctions as $h => $fonc)
		if (time() - $fonc[0] > 48*3600)
			unset($ajax_fonctions[$h]);

	// enregistre
	ecrire_fichier(_DIR_SESSIONS.'ajax_fonctions.txt',
		serialize($ajax_fonctions));


	return genea_afficher_arbres_trad($titre, $requete, $formater, $tmp_var, $hash, $cpt);
}

//
//
//
function genea_afficher_arbres_trad($titre_table, $requete, $formater, $tmp_var, $hash, $cpt, $trad=0) {

	global $options, $spip_lang_right, $ze_logo_genea;

	if ($trad) {
		$formater = 'afficher_articles_trad_boucle';
		$icone = "langues-off-12.gif";
		$alt = _T('masquer_trad');
	} else {
		if (!$formater) {
			$formater_arbre =  charger_fonction('formater_arbre', 'inc');
			$formater = $formater_arbre;
		}
		$icone = 'langues-12.gif';
		$alt = _T('afficher_trad');
	}

	$nb_aff = ($cpt  > floor(1.5 * _TRANCHES)) ? _TRANCHES : floor(1.5 * _TRANCHES) ;
	$deb_aff = intval(_request($tmp_var));

	$q = spip_query("SELECT " . $requete['SELECT'] . " FROM " . $requete['FROM'] . ($requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '') . ($requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '') . ($requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '') . " LIMIT " . ($deb_aff >= 0 ? "$deb_aff, $nb_aff" : ($requete['LIMIT'] ? $requete['LIMIT'] : "99999")));
	$t = '';
	while ($r = spip_fetch_array($q))
		if (autoriser('voir','article',$r['id_article']))
			$t .= $formater($r);
	spip_free_result($q);

	$style = "style='visibility: hidden; float: $spip_lang_right'";

	$texte = http_img_pack("searching.gif", "", $style . " id='img_$tmp_var'");

	if (($GLOBALS['meta']['gerer_trad'] == "oui")) {
		$url = generer_url_ecrire('memoriser',"hash=$hash&trad=" . (1-$trad));
		$texte .=
		 "\n<span style='float: $spip_lang_right;'><a href=\"#\"\nonclick=\"return charger_id_url('$url','$tmp_var');\">"
		. "<img\nsrc='". _DIR_IMG_PACK . $icone ."' alt='$alt' /></a></span>";
	}
	$texte .=  '<b>' . $titre_table  . '</b>';

	$icone = $ze_logo_genea;

	$res =  "\n<div style='height: 12px;'></div>"
	. "\n<div class='liste'>"
	. bandeau_titre_boite2($texte, $icone, 'white', 'black',false)

	. (($cpt <= $nb_aff) ? ''
	   : afficher_tranches_requete($cpt, $tmp_var, generer_url_ecrire('memoriser', "hash=$hash&trad=$trad"), $nb_aff))
	. afficher_liste_debut_tableau()
	. $t
	. afficher_liste_fin_tableau()
	. "</div>\n";

	return ajax_action_greffe($tmp_var,$res);
}

?>