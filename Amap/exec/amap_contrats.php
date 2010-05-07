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
include(_DIR_PLUGIN_AMAP."/inc/fonctions_liste_contrat.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_tarif_contrat.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_reglement_contrat.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_sortie_contrat.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_participation_contrat.php");
include(_DIR_PLUGIN_AMAP."/inc/fonctions_choix.php");

//=========================================================================
//=========================================================================
//
function exec_amap_contrats()
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

  pipeline('exec_init',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));

  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('amap:gestion_contrats'), "contrats", "amap_contrats");

  echo "<br /><br /><br />\n";
  echo gros_titre(_T('amap:gestion_contrats'),'',false);
  echo barre_onglets("amap", "contrats");

  echo debut_gauche("", true);

  // Affichage du sous-menu de configuration
  debut_cadre_relief();
  echo "<b><div class='verdana2'>";
  $res = icone_horizontale(_T('amap:liste_contrat'), generer_url_ecrire("amap_contrats", "
				&idSaison=".$_REQUEST['idSaison']."
				&idProduit=".$_REQUEST['idProduit']."
					"), _DIR_PLUGIN_AMAP."img_pack/page_accueil.png", _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
        .icone_horizontale(_T('amap:tarif_contrat'), generer_url_ecrire("amap_contrats", "table=tarifs
				&idSaison=".$_REQUEST['idSaison']."
				&idProduit=".$_REQUEST['idProduit']."
					"), _DIR_PLUGIN_AMAP."img_pack/page_accueil.png", _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:reglement'), generer_url_ecrire("amap_contrats", "table=reglement
				&idSaison=".$_REQUEST['idSaison']."
				&idProduit=".$_REQUEST['idProduit']."
					"),  _DIR_PLUGIN_AMAP."img_pack/options.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:sorties_ferme'), generer_url_ecrire("amap_contrats", "table=sorties
				&idSaison=".$_REQUEST['idSaison']."
				&idProduit=".$_REQUEST['idProduit']."
					"),  _DIR_PLUGIN_AMAP."img_pack/themes.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false)
       .icone_horizontale(_T('amap:participation_sorties'), generer_url_ecrire("amap_contrats
				&idSaison=".$_REQUEST['idSaison']."
				&idProduit=".$_REQUEST['idProduit']."
					", "table=participation"),  _DIR_PLUGIN_AMAP."img_pack/themes.png",  _DIR_PLUGIN_AMAP."img_pack/rien.gif", false);
  echo $res . "</div>";
  fin_cadre_relief();

  echo pipeline('affiche_gauche',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));
  creer_colonne_droite();
  echo pipeline('affiche_droite',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));
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


  if ($table == 'tarifs') { // Page tarif
    $msg_presentation_1 = _T('amap:tarifs_enregistres');
    $msg_presentation_2 = _T('amap:tarifs_enregistrement');

   // CHOIX DE L'ACTION A REALISER => de l'affichage

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
      // la page est arrivée en POST
      switch ($_POST['action'])
      {
        case "maj" :
          // la page est arrivée en POST avec action=maj
          //  ==> Demande Enregistrement des valeurs.
          if ($_POST['prixDistribution'])
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_modif_post_tarif_contrat()."</div>";
            echo fin_cadre_trait_couleur(true);
          }
          else
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>entrez un tarif...</div>";
            echo fin_cadre_trait_couleur(true);
          }
          break;
        case "add" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
          if ($_POST['prixDistribution'])
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_post_tarif_contrat()."</div>";
            echo fin_cadre_trait_couleur(true);
          }
          else
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>entrez un tarif...</div>";
            echo fin_cadre_trait_couleur(true);
          }
          break;
      } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
      echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "tarifs", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
      echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "tarifs", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
      echo table_amap_lister_tarif_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
      echo table_amap_get_tarif_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
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
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_prix'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_getmodif_tarif_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "suppr" :
          // avec action=maj
          //  ==> Affichage formulaire de modification
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_prix'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_suppr_tarif_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);

          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "tarifs", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "tarifs", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_lister_tarif_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_get_tarif_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);
          break;
        default :
          // sans action ou autre
          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "tarifs", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "tarifs", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_lister_tarif_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_get_tarif_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_GET['action'])
    } // if ($_SERVER['REQUEST_METHOD'] == 'POST')

  } // fin if ($page == 'tarifs')
  else if ($table == 'reglement') { // Page reglement
    $msg_presentation_1 = _T('amap:cheques_enregistres');
    $msg_presentation_2 = _T('amap:cheques_enregistrement');

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
               .table_amap_modif_post_reglement_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "add" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
          echo   debut_cadre_trait_couleur("", true, "", "");
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_post_reglement_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
      echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "reglement", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
      echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "reglement", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur("", true, "", "Choix du contrat");
      echo table_amap_lister_choix_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_POST['idContrat'], "reglement", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']
														." et du contrat ".$_POST['idContrat']);
      echo table_amap_lister_reglement_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_POST['idContrat']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']
														." et du contrat ".$_POST['idContrat']);
      echo table_amap_get_reglement_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_POST['idContrat']);
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
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_prix'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_getmodif_reglement_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "suppr" :
          // avec action=maj
          //  ==> Affichage formulaire de modification
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_prix'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_suppr_reglement_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);

          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "reglement", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "reglement", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", "Choix du contrat");
          echo table_amap_lister_choix_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_GET['idContrat'], "reglement", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']
														." et du contrat ".$_GET['idContrat']);
          echo table_amap_lister_reglement_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_GET['idContrat']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']
														." et du contrat ".$_GET['idContrat']);
          echo table_amap_get_reglement_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_GET['idContrat']);
          echo fin_cadre_trait_couleur(true);
          break;
        default :
          // sans action ou autre
          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "reglement", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "reglement", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", "Choix du contrat");
          echo table_amap_lister_choix_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_GET['idContrat'], "reglement", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']
														." et du contrat ".$_GET['idContrat']);
          echo table_amap_lister_reglement_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_GET['idContrat']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']
														." et du contrat ".$_GET['idContrat']);
          echo table_amap_get_reglement_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_GET['idContrat']);
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_GET['action'])
    } // if ($_SERVER['REQUEST_METHOD'] == 'POST')
 
  } // fin if ($page == 'reglement')
  else if ($table == 'sorties') 
  { // Page des sorties
    $msg_presentation_1 = _T('amap:sorties_enregistres');
    $msg_presentation_2 = _T('amap:sorties_enregistrement');

   // CHOIX DE L'ACTION A REALISER => de l'affichage

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
      // la page est arrivée en POST
      switch ($_POST['action'])
      {
        case "maj" :
          // la page est arrivée en POST avec action=maj
          //  ==> Demande Enregistrement des valeurs.
          if ($_POST['dateSortie'])
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_modif_post_sortie_contrat()."</div>";
            echo fin_cadre_trait_couleur(true);
          }
          else
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>entrez une date de sortie...</div>";
            echo fin_cadre_trait_couleur(true);
          }
          break;
        case "add" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
          if ($_POST['dateSortie'])
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_post_sortie_contrat()."</div>";
            echo fin_cadre_trait_couleur(true);
          }
          else
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>entrez une date de sortie...</div>";
            echo fin_cadre_trait_couleur(true);
          }
          break;
      } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
      echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "sorties", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
      echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "sorties", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
      echo table_amap_lister_sortie_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
      echo table_amap_get_sortie_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
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
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_sorties'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_getmodif_sortie_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "suppr" :
          // avec action=maj
          //  ==> Affichage formulaire de modification
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_sorties'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_suppr_sortie_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);

          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "sorties", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "sorties", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_lister_sortie_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_get_sortie_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);
          break;
        default :
          // sans action ou autre
          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "sorties", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "sorties", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_lister_sortie_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_get_sortie_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_GET['action'])
    } // if ($_SERVER['REQUEST_METHOD'] == 'POST')
 
  } // fin if ($page == 'sorties')
  else if ($table == 'participation') 
  { // Page des sorties
    $msg_presentation_1 = _T('amap:liste_sorties_enregistres');
    $msg_presentation_2 = _T('amap:liste_sorties_enregistrement');

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
               .table_amap_modif_post_participation_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "add" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
          if ($_POST['idPersonne'])
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>"
                 .table_amap_post_participation_contrat()."</div>";
            echo fin_cadre_trait_couleur(true);
            $_POST['idPersonne'] = '';
          }
          else
          {
            echo   debut_cadre_trait_couleur("", true, "", "");
            echo "<div class='verdana2' style='text-align: justify'>choisissez un contrat...</div>";
            echo fin_cadre_trait_couleur(true);
          }
          break;
      } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
      echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "participation", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
      echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "participation", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
      echo table_amap_lister_participation_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
      echo table_amap_get_participation_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_POST['idSortie']);
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
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_participation_sorties'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_getmodif_participation_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "suppr" :
          // avec action=maj
          //  ==> Affichage formulaire de modification
          echo   debut_cadre_trait_couleur("", true, "", "");
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_suppr_post_participation_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);

          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "participation", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "participation", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_lister_participation_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_get_participation_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_GET['idSortie']);
          echo fin_cadre_trait_couleur(true);
          break;
        default :
          // sans action ou autre
          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "participation", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "participation", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_lister_participation_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2.$_REQUEST['idSaison']
														." du produit ".$_REQUEST['idProduit']);
          echo table_amap_get_participation_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit'], $_GET['idSortie']);
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_GET['action'])
    } // if ($_SERVER['REQUEST_METHOD'] == 'POST')
  
  } // fin if ($page == 'participation')
  else 
  { // page liste des contrats
    $msg_presentation_1 = _T('amap:liste_contrat');
    $msg_presentation_2 = _T('amap:enregistrement_contrat');

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
               .table_amap_modif_post_liste_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "add" :
          // la page est arrivée en POST sans action ou autre
          //  ==> Demande Insertion des valeurs.
          echo   debut_cadre_trait_couleur("", true, "", "");
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_post_liste_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_POST['action'])
        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
      echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
      echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "", "amap_contrats");
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1
											.('amap:choix_contrat_produit').$_REQUEST['idProduit']
											._T('amap:choix_contrat_saison').$_REQUEST['idSaison']);
      echo table_amap_lister_liste_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
      echo fin_cadre_trait_couleur(true);

        echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2
											.('amap:choix_contrat_produit').$_REQUEST['idProduit']
											._T('amap:choix_contrat_saison').$_REQUEST['idSaison']);
      echo table_amap_get_liste_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
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
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_contrat'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_getmodif_liste_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);
          break;
        case "suppr" :
          // avec action=maj
          //  ==> Affichage formulaire de modification
            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", _T('amap:modification_table_amap_contrat'));
          echo "<div class='verdana2' style='text-align: justify'>"
               .table_amap_suppr_liste_contrat()."</div>";
          echo fin_cadre_trait_couleur(true);

          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1
											.('amap:choix_contrat_produit').$_REQUEST['idProduit']
											._T('amap:choix_contrat_saison').$_REQUEST['idSaison']);
          echo table_amap_lister_liste_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2
											.('amap:choix_contrat_produit').$_REQUEST['idProduit']
											._T('amap:choix_contrat_saison').$_REQUEST['idSaison']);
          echo table_amap_get_liste_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);
          break;
        default :
          // sans action ou autre
          //  ==> Affichage de la liste
            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_saison'));
          echo table_amap_lister_choix_saison($_REQUEST['idSaison'], "", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur("", true, "", _T('amap:choix_produit'));
          echo table_amap_lister_choix_produit($_REQUEST['idSaison'], $_REQUEST['idProduit'], "", "amap_contrats");
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata.gif", true, "", $msg_presentation_1
											._T('amap:choix_contrat_produit').$_REQUEST['idProduit']
											._T('amap:choix_contrat_saison').$_REQUEST['idSaison']);
          echo table_amap_lister_liste_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);

            echo   debut_cadre_trait_couleur(_DIR_PLUGIN_AMAP."img_pack/tabledata-add.gif", true, "", $msg_presentation_2
											._T('amap:choix_contrat_produit').$_REQUEST['idProduit']
											._T('amap:choix_contrat_saison').$_REQUEST['idSaison']);
          echo table_amap_get_liste_contrat($_REQUEST['idSaison'], $_REQUEST['idProduit']);
          echo fin_cadre_trait_couleur(true);
          break;
      } // switch ($_GET['action'])
    } // if ($_SERVER['REQUEST_METHOD'] == 'POST')

  } // fin else du if ($page


  echo pipeline('affiche_milieu',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));
  echo "\n<br/><br/><hr/>\n"
          ,"<center><i class='arial1' >"
          ,"Plugin Amap v1.1 par St&eacute;phane Moulinet -dadaprod.org - <br/>"
          ,"pour la gestion des 'tables amap'."
          ,"</i></center>\n<hr/>\n" ;
  echo fin_gauche(), fin_page();
}
?>
