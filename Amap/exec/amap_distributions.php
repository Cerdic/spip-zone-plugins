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

if (!defined("_ECRIRE_INC_VERSION")) return;
require_once ("base/abstract_sql.php");
include_spip('inc/presentation');
include_spip('inc/config');
include(_DIR_PLUGIN_AMAP."/inc/fonctions_evenement_distribution.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_produit_distribution.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_vacance_distribution.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_choix.php");

//=========================================================================
//=========================================================================
//
function exec_amap_distributions()
{

  global $connect_statut, $connect_toutes_rubriques, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
  global $debut, $visiteurs, $connect_id_auteur, $table_prefix;

  $table = _request('table');

  if ($connect_statut != '0minirezo') {
  //if (!( ($connect_statut == '0minirezo') || ($connect_statut == '1comite') )) {
	echo _T('avis_non_acces_page');
	echo fin_page();
	exit;
  }

  $page = preg_replace('/\W/', '', _request('page'));

  pipeline('exec_init',array('args'=>array('exec'=>'amap_distributions'),'data'=>''));

  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('amap:gestion_distributions'), "distributions", "amap_distributions");

  echo "<br /><br /><br />\n";
  echo gros_titre(_T('amap:gestion_distributions'),'',false);
  echo barre_onglets("amap", "distributions");

  echo debut_gauche("", true);

  // Affichage du sous-menu de configuration
  debut_cadre_relief();
  echo "<b><div class='verdana2'>";
  $res = icone_horizontale(_T('amap:evenements'), generer_url_ecrire("amap_distributions", "
				&idSaison=".$_REQUEST['idSaison']), _DIR_PLUGIN_AMAP."img_pack/page_accueil.png", _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:produits'), generer_url_ecrire("amap_distributions", "table=produits
				&idSaison=".$_REQUEST['idSaison']."
					"),  _DIR_PLUGIN_AMAP."img_pack/themes.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:vacances'), generer_url_ecrire("amap_distributions", "table=vacances
				&idSaison=".$_REQUEST['idSaison']."
					"),  _DIR_PLUGIN_AMAP."img_pack/themes.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false);
  echo $res . "</div>";
  fin_cadre_relief();

  echo pipeline('affiche_gauche',array('args'=>array('exec'=>'amap_distributions'),'data'=>''));
  creer_colonne_droite();
  echo pipeline('affiche_droite',array('args'=>array('exec'=>'amap_distributions'),'data'=>''));
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



if ($table == 'spip_amap_vacances') { // Page vacances
    $msg_presentation_1 = _T('amap:liste_vacance');
    $msg_presentation_2 = _T('amap:enregistrement_vacance');

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
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_modif_post_vacance_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "add" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
  echo   debut_cadre_trait_couleur("", true, "", "");
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_post_vacance_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
      echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "vacances", "amap_distributions");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
      echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_POST['idProduit'], "vacances", "amap_distributions");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
      echo table_amap_lister_vacance_distribution($_REQUEST['idSaison'], $_POST['idProduit']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']);
      echo table_amap_get_vacance_distribution($_REQUEST['idSaison'], $_POST['idProduit']);
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
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_resp_distribution'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_getmodif_vacance_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "suppr" :
          // avec action=maj
          //  ==> Affichage formulaire de modification
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_resp_distribution'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_suppr_vacance_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);

          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "vacances", "amap_distributions");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_GET['idProduit'], "vacances", "amap_distributions");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
          echo table_amap_lister_vacance_distribution($_REQUEST['idSaison'], $_GET['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']);
          echo table_amap_get_vacance_distribution($_REQUEST['idSaison'], $_GET['idProduit']);
          echo fin_cadre_trait_couleur(true);
          break;
        default :
          // sans action ou autre
          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "vacances", "amap_distributions");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_GET['idProduit'], "vacances", "amap_distributions");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
          echo table_amap_lister_vacance_distribution($_REQUEST['idSaison'], $_GET['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']);
          echo table_amap_get_vacance_distribution($_REQUEST['idSaison'], $_GET['idProduit']);
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_GET['action'])
    } // if ($_SERVER['REQUEST_METHOD'] == 'POST')

  } // fin if ($page == 'vacances')
  else if ($table == 'spip_amap_produits') 
  { // Page produits
    $msg_presentation_1 = _T('amap:liste_distribution_enregistres');
    $msg_presentation_2 = _T('amap:liste_distribution_enregistrement');

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
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_modif_post_produit_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "add" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
  echo   debut_cadre_trait_couleur("", true, "", "");
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_post_produit_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
      echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "produits", "amap_distributions");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
      echo table_amap_lister_produit_distributions($_REQUEST['idSaison']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']);
      echo table_amap_get_produit_distribution($_REQUEST['idSaison']);
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
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", "modification de la table amap_produit_distribution");
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_getmodif_produit_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "suppr" :
          // avec action=maj
          //  ==> Affichage formulaire de modification
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_produit_distribution'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_suppr_produit_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);

          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "produits", "amap_distributions");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
          echo table_amap_lister_produit_distributions($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']);
          echo table_amap_get_produit_distribution($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);
          break;
        default :
          // sans action ou autre
          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "produits", "amap_distributions");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
          echo table_amap_lister_produit_distributions($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']);
          echo table_amap_get_produit_distribution($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_GET['action'])
    } // if ($_SERVER['REQUEST_METHOD'] == 'POST')

  } // fin if ($page == 'produits')
  else 
  { // page évènements
    $msg_presentation_1 = _T('amap:liste_evenement_distribution');
    $msg_presentation_3 = _T('amap:maj_evenement_distribution');
    $msg_presentation_4 = _T('amap:enregistrement_evenement_distribution');

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
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_modif_post_evenement_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "add" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
          if ($_POST['dateEvenement'])
          {
    echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_add_post_evenement_distribution()."</div>";
            echo fin_cadre_trait_couleur(true);
          }
          else
          {
    echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>";
		echo _T('amap:label_date');
	    echo "</div>";
            echo fin_cadre_trait_couleur(true);
          }
          break;
        case "agenda_update" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
  echo   debut_cadre_trait_couleur("", true, "", "");
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_agenda_update_evenement_distribution()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
      echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "", "amap_distributions");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
      echo table_amap_lister_evenement_distribution($_REQUEST['idSaison']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_3.$_REQUEST['idSaison']);
      echo table_amap_getAgendaUpdate_evenement_distribution($_REQUEST['idSaison']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_4.$_REQUEST['idSaison']);
      echo table_amap_get_evenement_distribution($_REQUEST['idSaison']);
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
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_evenements'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_getmodif_evenement_distribution($_REQUEST['idSaison'],$_GET['idEvenement'])."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "suppr" :
          // avec action=maj
          //  ==> Affichage formulaire de modification
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:suppression_table_amap_evenements'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_suppr_evenement_distribution($_REQUEST['idSaison'],$_GET['idEvenement'])."</div>";
          echo fin_cadre_trait_couleur(true);

          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "", "amap_distributions");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
          echo table_amap_lister_evenement_distribution($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_3.$_REQUEST['idSaison']);
          echo table_amap_getAgendaUpdate_evenement_distribution($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_4.$_REQUEST['idSaison']);
          echo table_amap_get_evenement_distribution($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);
          break;
        default :
          // sans action ou autre
          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "", "amap_distributions");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']);
          echo table_amap_lister_evenement_distribution($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_3.$_REQUEST['idSaison']);
          echo table_amap_getAgendaUpdate_evenement_distribution($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_4.$_REQUEST['idSaison']);
          echo table_amap_get_evenement_distribution($_REQUEST['idSaison']);
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_GET['action'])
    } // if ($_SERVER['REQUEST_METHOD'] == 'POST')

  } // fin else du if ($page


  echo pipeline('affiche_milieu',array('args'=>array('exec'=>'amap_distributions'),'data'=>''));
  echo fin_gauche(), fin_page();
}
?>
