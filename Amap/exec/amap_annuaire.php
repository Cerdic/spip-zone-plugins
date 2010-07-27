<?php

##########################################################################
#
#           Page annuaire du plugin Amap pour SPIP
#
#
#             Site de documentation du plugin Amap
#                       http://www.dadaprod.org/amap-spip
#
#                        licence GNU/GPL
#	                 2008 - Stéphane Moulinet
############################################################################

if (!defined("_ECRIRE_INC_VERSION")) return;
require_once ("base/abstract_sql.php");
include_spip('inc/presentation');
include_spip('inc/config');
include(_DIR_PLUGIN_AMAP."/inc/fonctions_annuaire.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_choix.php");

//=========================================================================
//=========================================================================
//
function exec_amap_annuaire()
{

  global $connect_statut, $connect_toutes_rubriques, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
  global $intPremierEnreg, $visiteurs, $connect_id_auteur, $table_prefix;

  $table = _request('table');

  if ($connect_statut != '0minirezo') {
  //if (!( ($connect_statut == '0minirezo') || ($connect_statut == '1comite') )) {
	echo _T('avis_non_acces_page');
	echo fin_page();
	exit;
  }

  $page = preg_replace('/\W/', '', _request('page'));

  pipeline('exec_init',array('args'=>array('exec'=>'amap_annuaire'),'data'=>''));

  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('amap:gestion_annuaire'), "configuration", "amap_annuaire");

  echo "<br /><br /><br />\n";
  echo gros_titre(_T('amap:gestion_annuaire'),'',false);
  echo barre_onglets("amap", "annuaire");

  echo debut_gauche("", true);

  echo pipeline('affiche_gauche',array('args'=>array('exec'=>'amap_annuaire'),'data'=>''));
  creer_colonne_droite();
  echo pipeline('affiche_droite',array('args'=>array('exec'=>'amap_annuaire'),'data'=>''));
  echo debut_droite("", true);

  // Affichage avertissement
  echo debut_cadre_trait_couleur("", true, "", "");
  echo "<div class='verdana2' style='text-align: justify'>
        <p style='text-align: center'><b>";
  echo _T('amap:attention');
  echo  "</b></p><img src='../prive/images/warning.gif' alt='ATTENTION !' width='48' height='48' style='float: right; padding-left: 10px;' />";
  echo _T('amap:attention_modifications');
  echo "</div>";
  echo fin_cadre_trait_couleur(true);
  echo "<br />&nbsp;<br />";


  $msg_presentation_1 = _T('amap:liste_enregistres');
  $msg_presentation_2 = _T('amap:liste_enregistrement');

  // CHOIX DE L'ACTION A REALISER => de l'affichage

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    // la page est arrivée en POST
    switch ($_POST['action'])
    {
      case "maj" :
        // la page est arrivée en POST avec action=maj
        //  ==> Demande Enregistrement des valeurs.
        echo debut_cadre_trait_couleur("", true, "", "");
        echo "<div class='verdana2' style='text-align: justify'>"
             .table_amap_modif_post_annuaire()."</div>";
        echo fin_cadre_trait_couleur(true);
        break;
      case "add" :
        // la page est arrivée en POST sans action ou autre
        //  ==> Demande Insertion des valeurs.
        echo debut_cadre_trait_couleur("", true, "", "");
        echo "<div class='verdana2' style='text-align: justify'>"
             .table_amap_post_annuaire()."</div>";
        echo fin_cadre_trait_couleur(true);
        break;
    } // switch ($_POST['action'])
    echo debut_cadre_trait_couleur("", true, "", _T('amap:choix_statut'));
    echo table_amap_lister_choix_statut($_POST['statut'], "", "amap_annuaire");
    echo fin_cadre_trait_couleur(true);

    echo debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_POST['statut']);
    echo table_amap_lister_annuaire($_POST['statut']);
    echo fin_cadre_trait_couleur(true);

    echo debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2);
    echo table_amap_get_annuaire($_POST['statut']);
    echo fin_cadre_trait_couleur(true);
  }  // if ($_SERVER['REQUEST_METHOD'] == 'POST')
  else
  {
    // la page n'est pas arrivée en POST => en GET
    switch ($_GET['action'])
    {
      case "modif" :
        // avec action=maj
        //  ==> Affichage formulaire de modification
        echo debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_personne'));
        echo "<div class='verdana2' style='text-align: justify'>"
             .table_amap_getmodif_annuaire()."</div>";
        echo fin_cadre_trait_couleur(true);
        break;
      case "suppr" :
        // avec action=maj
        //  ==> Affichage formulaire de modification
        echo debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:suppression_table_amap_personne'));
        echo "<div class='verdana2' style='text-align: justify'>"
             .table_amap_suppr_annuaire()."</div>";
        echo fin_cadre_trait_couleur(true);

        //  ==> Affichage de la liste
        echo debut_cadre_trait_couleur("", true, "", _T('amap:choix_statut'));
        echo table_amap_lister_choix_statut($_GET['statut'], "", "amap_annuaire");
        echo fin_cadre_trait_couleur(true);

        echo debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_GET['statut']);
        echo table_amap_lister_annuaire($_GET['statut']);
        echo fin_cadre_trait_couleur(true);

        echo debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2);
        echo table_amap_get_annuaire($_GET['statut']);
        echo fin_cadre_trait_couleur(true);
        break;
      default :
        // sans action ou autre
        //  ==> Affichage de la liste
        echo debut_cadre_trait_couleur("", true, "", _T('amap:choix_statut'));
        echo table_amap_lister_choix_statut($_GET['statut'], "", "amap_annuaire");
        echo fin_cadre_trait_couleur(true);

        echo debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1);
        echo table_amap_lister_annuaire($_GET['statut']);
        echo fin_cadre_trait_couleur(true);

        echo debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2);
        echo table_amap_get_annuaire($_GET['statut']);
        echo fin_cadre_trait_couleur(true);
        break;
    } // switch ($_GET['action'])
  } // if ($_SERVER['REQUEST_METHOD'] == 'POST')

  echo pipeline('affiche_milieu',array('args'=>array('exec'=>'amap_annuaire'),'data'=>''));
  echo fin_gauche(), fin_page();
} // function exec_amap_annuaire
?>
