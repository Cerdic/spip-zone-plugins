<HTML lang='fr'>
<HEAD>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel="stylesheet" type="text/css" href="../dist/style_prive_defaut.css" />
<link rel="stylesheet" type="text/css" href="http://localhost/office-bac/spip.php?page=style_prive&amp;couleur_claire=C5E41C&amp;couleur_foncee=9DBA00&amp;ltr=left" />
<link rel="stylesheet" type="text/css" href="../dist/agenda.css" />
<link rel="stylesheet" type="text/css" href="../dist/spip_style_print.css" media="print" />
<link rel="stylesheet" type="text/css" href="../dist/spip_style_invisible.css" />

<link rel="shortcut icon" href="http://localhost/office-bac/squelettes/favicon.ico" />

<TITLE>
<?php
   $fichier=$_GET["fic"];
   echo "Affichage table CSV - $fichier";
?>
</TITLE>
</HEAD>

<BODY>
<div class="cadre_info verdana1">
<?php
   global $debug,$txt_debug;
   error_reporting(E_ERROR | E_WARNING | E_PARSE);

   include_once('inc-fichiers.php');
   include('../../../../ecrire/inc/utils.php');
   include_spip('inc/charsets');
   include_spip('inc/presentation');

   define(_DIR_PLUGIN_ODB_IMPORT,"../"); // parce qu'on esquive fonctionnement spip pour éviter cartouche espace privé
   define('DIR_ODB_COMMUN',"../../odb_commun/");
   $tab_csv = affiche_csv(_DIR_PLUGIN_ODB_IMPORT."upload/$fichier","",date("Y"),true);
   echo $tab_csv["html"];
   if($debug) 
      echo $txt_debug;
?>
</div>
</BODY>
</HTML>

