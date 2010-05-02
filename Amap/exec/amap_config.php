<?php

##########################################################################
#
#           Page de configuration du plugin Amap pour SPIP
#
#
#             Site de documentation du plugin Amap
#                       http://www.dadaprod.org/amap-spip
#
#	                 2008 - Stéphane Moulinet
############################################################################

$GLOBALS['page'] = "amap_config";

if (!defined("_ECRIRE_INC_VERSION")) return;
require_once ("base/abstract_sql.php");
include_spip('inc/presentation');
include_spip('inc/config');
include(_DIR_PLUGIN_AMAP."/inc/fonctions_table.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_paysan.php");

function exec_amap_config()
{

  global $connect_statut, $connect_toutes_rubriques, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
  global $debut, $visiteurs, $connect_id_auteur, $table_prefix;

  $table = _request('table');
  $serveur = _request('serveur');
  $mode = _request('mode');
  $idLigne = _request('id_ligne');

  if ($connect_statut != '0minirezo') {
  //if (!( ($connect_statut == '0minirezo') || ($connect_statut == '1comite') )) {
	echo _T('avis_non_acces_page');
	echo fin_page();
	exit;
  }

  $page = preg_replace('/\W/', '', _request('page'));

  pipeline('exec_init',array('args'=>array('exec'=>'amap_config'),'data'=>''));

  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page("Configuration du module", "configuration", "amap_config");

  echo "<br /><br /><br />\n";
  echo gros_titre(_T('amap:configuration_du_plugin'),'',false);
  echo barre_onglets("amap", "configuration");

  echo debut_gauche("", true);

  // Affichage du sous-menu de configuration
  echo debut_cadre_relief("", true);
  echo "<b><div class='verdana2'>";
  $res = icone_horizontale(_T('amap:saisons'), generer_url_ecrire("amap_config"), _DIR_PLUGIN_AMAP."img_pack/page_accueil.png", _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:produits'), generer_url_ecrire("amap_config", "table=amap_produit"),  _DIR_PLUGIN_AMAP."img_pack/options.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:banques'), generer_url_ecrire("amap_config", "table=amap_banque"),  _DIR_PLUGIN_AMAP."img_pack/themes.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:types_de_contrat'), generer_url_ecrire("amap_config", "table=amap_type_contrat"),  _DIR_PLUGIN_AMAP."img_pack/themes.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:lieux'), generer_url_ecrire("amap_config", "table=amap_lieu"),  _DIR_PLUGIN_AMAP."img_pack/themes.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false);
  echo $res . "</div></b>";
  echo fin_cadre_relief("", true);

  echo pipeline('affiche_gauche',array('args'=>array('exec'=>'amap_config'),'data'=>''));
  echo creer_colonne_droite('', true);
  echo pipeline('affiche_droite',array('args'=>array('exec'=>'amap_config'),'data'=>''));
  echo debut_droite("", true);

  // Affichage avertissement
  echo   debut_cadre_trait_couleur("", true, "", "");
  echo "<div class='verdana2' style='text-align: justify'>
        <p style='text-align: center'><b>";
  echo _T('amap:attention');
  echo  "</b></p><img src='../prive/images/warning.gif' alt='ATTENTION !' width='48' height='48' style='float: right; padding-left: 10px;' />";
  echo _T('amap:attention_modifications');
  echo "</div>";
  echo fin_cadre_trait_couleur(true);
  echo "<br />&nbsp;<br />";

  if ($table == 'amap_produit') { // Page produits
    $msg_presentation_1 = _T('amap:produits_enregistres');
    $msg_presentation_2 = _T('amap:produits_enregistrement');
  }
  else if ($table == 'amap_banque') { // Page banques
    $msg_presentation_1 = _T('amap:banques_enregistres');
    $msg_presentation_2 = _T('amap:banques_enregistrement');
  }
  else if ($table == 'amap_type_contrat') { // Page sur les types de contrat
    $msg_presentation_1 = _T('amap:contrats_enregistres');
    $msg_presentation_2 = _T('amap:contrats_enregistrement');
  }
  else if ($table == 'amap_lieu') { // Page des lieux de distributions
    $msg_presentation_1 = _T('amap:lieux_enregistres');
    $msg_presentation_2 = _T('amap:lieux_enregistrement');
  }
  else { // page saisons
    $table = "amap_saison";
    $msg_presentation_1 = _T('amap:saisons_enregistres');
    $msg_presentation_2 = _T('amap:saisons_enregistrement');
  }

  $description = sql_showtable($table, $serveur);
  $field = $description['field'];
  $key = $description['key'];

  // CHOIX DE L'ACTION A REALISER => de l'affichage


  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    // la page est arrivée en POST
    switch ($_POST['action'])
    {
      case "maj" :
        // la page est arrivée en POST avec action=maj
        //  ==> Demande Enregistrement des valeurs.
  	echo   debut_cadre_trait_couleur("", true, "", "");
        if ($table == 'amap_produit')
          echo "<div class='verdana2' style='text-align: justify'>"
             .table_amap_modif_post_paysan($table, $serveur, $field, $key, $idLigne)."</div>";
        else
          echo "<div class='verdana2' style='text-align: justify'>"
             .table_amap_modif_post($table, $serveur, $field, $key, $idLigne)."</div>";
        echo fin_cadre_trait_couleur(true);
        break;
      default:
        // la page est arrivée en POST sans action ou autre
        //  ==> Demande Insertion des valeurs.
	echo   debut_cadre_trait_couleur("", true, "", "");
        if ($table == 'amap_produit')
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_post_paysan()."</div>";
        else
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_post($table, $serveur)."</div>";
        echo fin_cadre_trait_couleur(true);
        break;
    } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1);
    if ($table == 'amap_produit')
      echo table_amap_lister_paysan();
    else
      echo table_amap_lister($table, $serveur, $field, $key);
    echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2);
    if ($table == 'amap_produit')
      echo table_amap_get_paysan();
    else
      echo table_amap_get($table, $serveur, $field, $key);
    echo fin_cadre_trait_couleur(true);
  }  // if ($_SERVER['REQUEST_METHOD'] == 'POST')
  else
  {
    // la page n'est pas arrivée en POST => en GET
    switch ($_GET['action'])
    {
      case "edit" :
        // avec action=maj
        //  ==> Affichage formulaire de modification
        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", "modification de la table ".$table);
        if ($table == 'amap_produit')
          echo "<div class='verdana2' style='text-align: justify'>"
             .table_amap_getmodif_paysan()."</div>";
        else
          echo "<div class='verdana2' style='text-align: justify'>"
             .table_amap_getmodif($table, $serveur, $field, $key , $idLigne)."</div>";
        echo fin_cadre_trait_couleur(true);
        break;
      default :
        // sans action ou autre
        //  ==> Affichage de la liste
        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1);
        if ($table == 'amap_produit')
          echo table_amap_lister_paysan();
        else
          echo table_amap_lister($table, $serveur, $field, $key);
        echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2);
        if ($table == 'amap_produit')
          echo table_amap_get_paysan();
        else
          echo table_amap_get($table, $serveur, $field, $key);
        echo fin_cadre_trait_couleur(true);
        break;
    } // switch ($_GET['action'])
  } // if ($_SERVER['REQUEST_METHOD'] == 'POST')

  echo pipeline('affiche_milieu',array('args'=>array('exec'=>'amap_config'),'data'=>''));
  echo "\n<br/><br/><hr/>\n"
          ,"<center><i class='arial1' >"
          ,"Plugin Amap v1.1 par St&eacute;phane Moulinet -dadaprod.org - <br/>"
          ,"pour la gestion des 'tables amap'."
          ,"</i></center>\n<hr/>\n" ;
  echo fin_gauche().fin_page();
}
?>
