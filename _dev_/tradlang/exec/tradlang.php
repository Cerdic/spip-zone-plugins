<?php

ob_start();


include_spip("inc/presentation");
include("tradlang_inc.php");
include("tradlang_fonctions.php");

global $operation;
if (!isset($operation) || empty($operation))
  $operation = $_POST["operation"];


function exec_tradlang() {

  global $connect_statut, $connect_toutes_rubriques;
  global $operation, $couleur_foncee;

  $tababs = tradlang_tablesabsentes();

  pipeline('exec_init',array('args'=>array('exec'=>'config_lang'),'data'=>''));
  debut_page(_T('tradlang:tradlang'), "configuration", "langues");

  echo "<br><br><br>";
  gros_titre(_T('tradlang:tradlang'));

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
    echo _T('avis_non_acces_page');
    exit;    
  }
  
  barre_onglets("config_lang", "tradlang");
  
  // HTML output
  debut_gauche();	
  debut_boite_info();
  echo propre(_T('tradlang:readme'));  	
  fin_boite_info();
  
  if (!$tababs)
    {
      echo "<br>";
      debut_cadre_relief();
      echo "<div class='verdana3' style='background-color: $couleur_foncee; color: white; padding: 3px;'><b>"._T('tradlang:moduletitre').":</b></div><br>\n";

      $mods = tradlang_getmodules_base();
      if (count($mods))
	{
	  foreach($mods as $mod)
	    {
	      echo "<a href='".generer_url_ecrire("tradlang")."&operation=visumodule&module=".$mod["nom_mod"]."'>".propre($mod["nom_mod"])."</a><br>";
	    }
	  echo "<br>";
	}
      else
	echo propre(_T('tradlang:aucunmodule'))."<br><br>";  	

      echo "<form action='".generer_url_ecrire("tradlang")."&amp;' method='post' name='tradlang'>\n";
      echo "<input type='hidden' name='operation' value='importermodule' />\n";
      echo "<input type='submit' class='fondo' value='"._T("tradlang:importermodule")."'>";
      echo "</form>";

      //echo "<form action='".generer_url_ecrire("tradlang")."&amp;' method='post' name='tradlang2'>\n";
      //echo "<input type='hidden' name='operation' value='creermodule' />\n";
      //echo "<input type='submit' class='fondo' value='"._T("tradlang:creermodule")."'>";
      //echo "</form>";

      fin_cadre_relief();
    }

  debut_droite();
  
  if (!$tababs)
    {
      switch ($operation)
	{
	case "importermodule":
	  tradlang_importermodule1();
	  break;
	case "importermodule2":
	  tradlang_importermodule2();
	  break;
	case "importermodule3":
	  tradlang_importermodule3();
	  break;
	case "visumodule":
	  tradlang_visumodule();
	  break;
	case "creermodule":
	  break;
	case "popup":
	  ob_clean();
	  include("tradlang_popup.php");
	  exit;
	}
    }
  else
    {
      tradlang_gere_tablesabsentes();
    }
  
  fin_page();
}



// importer un module 1ere etape
// presentation du repertoire langue
function tradlang_importermodule1()
{
  global $repertoirelangue;

  if (!isset($repertoirelangue))
    $repertoirelangue = "./lang";
  else if (empty($repertoirelangue))
    $repertoirelangue = "./";    

  debut_cadre_relief("", false, "", _T('tradlang:importermodule1'));

  echo "<form action='".generer_url_ecrire("tradlang")."' method='post' name='tradlang'>\n";
  echo propre(_T('tradlang:repertoirelangue'));  	
  echo "&nbsp;<input type='text' name='repertoirelangue' value='".$repertoirelangue."' />\n";
  echo "<input type='hidden' name='operation' value='importermodule' />\n";
  echo "<input type='submit' class='fondo' value='"._T("tradlang:changerrepertoire")."'>";
  echo "<br><br>";
  echo propre(_T('tradlang:modulespresents'));  	
  echo "</form>";

  $modules = tradlang_getmodules_fics($repertoirelangue);
  $modules_base = tradlang_getmodules_base();

  if (count($modules))
    {
      echo "<br><form action='".generer_url_ecrire("tradlang")."' method='post' name='tradlang2'>\n";
      foreach($modules as $nom=>$module)
	{
	  $dis = "";
	  if (array_key_exists($nom, $modules_base))
	    $dis = "disabled";
	  echo "<input ".$dis." type='radio' name='module' value='$nom'>";
	  echo "<input type='hidden' name='operation' value='importermodule2' />\n";
	  echo "<input type='hidden' name='repertoirelangue' value='".$repertoirelangue."' />\n";
	  echo "<b>".propre($nom)."</b>";
	  $lgs = " (";
	  foreach($module as $cle=>$item)
	    {
	      if (strncmp($cle, "langue_", 7) == 0)
		$lgs .= " ".substr($cle,7);	      
	    }
	  $lgs .= " )";
	  echo $lgs;
	  echo "<br><br>";  	
	}
      echo "<input type='submit' class='fondo' value='"._T("tradlang:importermodule")."'>";
      echo "</form>";
    }
  else
    echo propre(_T('tradlang:aucunmodule'));  	
    
  fin_cadre_relief();
}

// importer un module 2eme etape
// saisie des informations liees au module
function tradlang_importermodule2()
{
  global $repertoirelangue, $module;

  if (!isset($repertoirelangue) || empty($repertoirelangue))
    return;
  if (!isset($module) || empty($module))
    return;

  $modules = tradlang_getmodules_fics($repertoirelangue);
  if (!isset($modules[$module]))
    return;
  $modok = $modules[$module];

  debut_cadre_relief("", false, "", _T('tradlang:importermodule2'));

  echo "<form action='".generer_url_ecrire("tradlang")."' method='post' name='tradlang'>\n";

  echo "<br>";
  echo "<span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:repertoirelangue2'))."</span>";
  echo propre($modok["dir_lang"]);  	
  echo "<br><br>";

  echo "<span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:idmodule'))."</span>";  	
  echo propre($module);
  echo "<br><br>";

  echo "<input type='hidden' name='module' value='".$module."' />\n";
  echo "<input type='hidden' name='repertoirelangue' value='".$repertoirelangue."' />\n";

  echo "<span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:nommodule'))."</span>";  	
  echo "<input type='text' name='nommodule' value='".$module."' />\n";
  echo "<br><br>";

  echo "<span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:languemere'))."</span>";  	
  echo "<select name='languemere'>\n";
  $opts = array();
  $ficnok = array();
  foreach($modok as $cle=>$item)
    {
      if (strncmp($cle, "langue_", 7) == 0)
	{
	  $sel = "";
	  $lg = substr($cle,7);
	  if ($lg == "fr")
	    $sel = " selected ";
	  $opts[] =  "<option  value='".$lg."' ".$sel.">".traduire_nom_langue($lg)."</option>\n";

	  // test si fichier inscriptible
	  $fic = $modok["dir_lang"]."/".$item;
	  if (!$fd = @fopen($fic, "a"))
	    $ficnok[] = $fic;
	  else
	    fclose($fd);
	}      
    }
  echo implode("", $opts);
  echo "</select>\n";

  if (count($ficnok))
    {
      echo "<br><br>\n";
      echo propre(_T("tradlang:attentionimport"));
      echo "<br><br>".implode("<br>", $ficnok)."\n";
    }

  echo "<br>\n";
  echo "<br>\n";

  echo "<input type='hidden' name='operation' value='importermodule3' />\n";
  echo "<input type='submit' class='fondo' value='"._T("tradlang:lancerimport")."'>";
  echo "</form>";
  

  fin_cadre_relief();
}


// importer un module 3eme etape
// import du module dans la base
function tradlang_importermodule3()
{
  global $repertoirelangue, $module;
  global $languemere, $nommodule;

  if (!isset($repertoirelangue) || empty($repertoirelangue))
    return;
  if (!isset($module) || empty($module))
    return;
  if (!isset($languemere) || empty($languemere))
    return;
  if (!isset($nommodule) || empty($nommodule))
    return;

  $modules = tradlang_getmodules_fics($repertoirelangue);
  if (!isset($modules[$module]))
    return;
  $modok = $modules[$module];
  $modok["nom_module"] = $nommodule;
  $modok["lang_mere"] = $languemere;

  debut_cadre_relief("", false, "", _T('tradlang:importermodule3'));

  /*echo "repertoirelangue : ".$repertoirelangue."<br>";
  echo "module : ".$module."<br>";
  echo "languemere : ".$languemere."<br>";
  echo "nommodules : ".$nommodule."<br>";*/

  tradlang_renseignebase($modok);

  echo "<br><form action='".generer_url_ecrire("tradlang")."&operation=visumodule&module=".$modok["nom_mod"]."' method='post' name='tradlang2'>\n";
  echo "<input type='submit' class='fondo' value='"._T("tradlang:traduire")."'>";
  echo "</form>";
      
  fin_cadre_relief();
}


function tradlang_visumodule()
{
  global $module;

  if (!isset($module) || empty($module))
    return false;

  $modules = tradlang_getmodules_base();
  if (!isset($modules[$module]))
    return;
  $modok = $modules[$module];

  debut_cadre_relief("", false, "", _T('tradlang:visumodule'));

  echo "<table cellspacing=0 cellpadding=2 border=0>\n";
  echo "<tr>\n";
  echo "<td valign=top><span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:nommodule'))."</span></td>";  	
  echo "<td><b>".propre($modok["nom_module"])."</b></td>";
  echo "</tr><tr>";

  echo "<td valign=top><span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:repertoirelangue2'))."</span></td>";
  echo "<td>".propre($modok["dir_lang"])."</td>";  	
  echo "</tr><tr>";

  echo "<td valign=top><span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:idmodule'))."</span></td>";  	
  echo "<td>".propre($modok["nom_mod"])."</td>";
  echo "</tr><tr>";

  echo "<td valign=top><span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:languemere'))."</span></td>";  	
  echo "<td>".propre($modok["lang_mere"])."</td>";
  echo "</tr><tr>";

  echo "<td valign=top><span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:languesdispo'))."</span></td>";  	
  $lgs = " ( ";
  foreach($modok as $cle=>$item)
    {
      if (strncmp($cle, "langue_", 7) == 0)
	$lgs .= substr($cle,7)." ";
    }
  $lgs .= " ) ";
  echo "<td>".propre($lgs)."</td>";
  echo "</tr>";

  echo "<tr>";
  echo "<td><form action='".generer_url_ecrire("tradlang")."' method='post' name='tradlang'>\n";
  echo "<input type='hidden' name='operation' value='ajouterlangue' />\n";
  echo "<input type='hidden' name='module' value='".$module."' />\n";
  echo "<span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:entrerlangue'))."</span></td>";  	
  echo "<td><input type='text' size='8' name='codelangue' value='' />\n";
  echo "<input type='submit' class='fondo' value='"._T("tradlang:ajoutercode")."'>";
  echo "</form></td></tr>";

  echo "</table>";
  fin_cadre_relief();

  debut_cadre_relief("", false, "", _T('tradlang:traductions'));

  // recupere la liste des traductions dans la base
  // et sur le disque
  $modules2 = tradlang_getmodules_fics($modok["dir_lang"]);
  $modok2 = $modules2[$module];

  // union entre modok et modok2
   foreach($modok2 as $cle=>$item)
    {
      if (strncmp($cle, "langue_", 7) == 0)
	{
	  $sel = "";
	  $lg = substr($cle,7);
	  if (!array_key_exists($lg, $modok))
	    {
	      $modok["langue_".$lg] = $item;
	    }
	}      
    }
   
   // imprime la table des langues
  echo "<table cellspacing=2 cellpadding=3 border=0>\n";
  echo "<tr>";
  echo "<th>&nbsp;</th>\n";
  echo "<th style='border:1px solid black;'>".propre(_T('tradlang:synchro'))."</th>\n";
  echo "<th style='border:1px solid black;'>".propre(_T('tradlang:traducok'))."</th>\n";
  echo "<th style='border:1px solid black;'>".propre(_T('tradlang:traducnok'))."</th>\n";
  echo "</tr>\n";
   foreach($modok as $cle=>$item)
     {
       if (strncmp($cle, "langue_", 7) == 0)
	 {
	   $sel = "";
	   $lg = substr($cle,7);	  
	   echo "<tr>\n";
	   echo "<td><a href='#' onclick='window.open (\"".generer_url_ecrire("tradlang")."&operation=popup&module=".$modok["nom_mod"]."&etape=droits&lang_orig=fr&lang_cible=".$lg."\", \"trad_lang\", config=\"height=500, width=700, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no\");return false;'>".traduire_nom_langue($lg)." ($lg) </a></td>";
	   if (tradlang_testesynchro($modok, $lg))
	     echo "<td style='border:1px solid black;'><img src='"._DIR_PLUGIN_TRADLANG."/../img_pack/vert.gif'></td>\n";
	   else
	     echo "<td style='border:1px solid black;'><img src='"._DIR_PLUGIN_TRADLANG."/../img_pack/rouge.gif'></td>\n";
	   echo "<td style='border:1px solid black;'>&nbsp;</td>\n";
	   echo "<td style='border:1px solid black;'>&nbsp;</td>\n";
	   echo "</tr>\n";	  
	 }
     }
   echo "</table>";
     
  fin_cadre_relief();
  /*
  echo "<span style='float:left;width:150px;padding-left:10px;'>".propre(_T('tradlang:languemere'))."</span>";  	
  echo "<select name='languemere'>\n";
  $opts = array();
y  sort($opts);
  echo implode("", $opts);
  echo "</select>\n";
  echo "<br>\n";
  echo "<br>\n";
  */


  return true;
}


// teste la synchro de la langue pour le module
// regarde si le timestamp max dans la base est
// egal a celui du fichier
function tradlang_testesynchro($module, $langue)
{
  $prefix = $GLOBALS['table_prefix'];

  $nom_module = $module["nom_module"];
  $nom_mod = $module["nom_mod"];
  $dir_lang = $module["dir_lang"];

  // lit le timestamp fichier
  $fic = $dir_lang."/".$module["langue_".$langue];
  include($fic);
  $chs = $GLOBALS[$GLOBALS['idx_lang']];
  $tsf = $chs["zz_timestamp_nepastraduire"];
  unset($GLOBALS[$GLOBALS['idx_lang']]);

  // lit le timestamp  base
  $quer = "SELECT MAX(ts) as ts FROM ".$prefix."_tradlang ".
    "WHERE module = '".$nom_mod."' AND lang='".$langue."'";
  $res = spip_query($quer);
  $row = spip_fetch_array($res);
  $tsb = $row["ts"];

  return ($tsb == $tsf);
}


// cree un module dans la base de donnee
// (lit les fichiers langues et importe dans la base)
function tradlang_renseignebase($module)
{
  $prefix = $GLOBALS['table_prefix'];

  $nom_module = $module["nom_module"];
  $nom_mod = $module["nom_mod"];
  $lang_mere = $module["lang_mere"];
  $dir_lang = $module["dir_lang"];

  // creation du module
  $quer = "INSERT INTO ".$prefix."_tradlang_modules (nom_module, nom_mod, lang_mere, type_export, dir_lang, lang_prefix) ".
    " VALUES ('".$nom_module."','".$nom_mod."','".$lang_mere."','spip','".$dir_lang."','".$nom_mod."') ";
  
  $res = spip_query($quer);      
  if ($res == false) 
    {
      echo mysql_error();
      return false;	  
    }
  
  // import des fichiers langues
  foreach($module as $cle=>$fichier)
    {
      if (strncmp($cle, "langue_", 7) != 0)
	continue;

      $lg = substr($cle, 7);

      $orig = 0;
      if ($lg == $lang_mere)
	$orig = 1;

      // sauvegarde le fichier dans la base
      echo propre(_T('tradlang:insertionlangue')." : ".$lg."...");
      $nom_fichier = $dir_lang."/".$fichier;
      include($nom_fichier);
      $chs = $GLOBALS[$GLOBALS['idx_lang']];
      
      reset($chs);
      while(list($id, $str) = each($chs))
	{
	  $quer = "INSERT INTO ".$prefix."_tradlang (id, module, str, lang, orig, status) ".
	    "VALUES ('".$id."', '".$nom_mod."', '".
	    mysql_escape_string($str)."', '".$lg."', ".$orig.", '') ";
	  
	  $res = spip_query($quer);      
	  if ($res == false) 
	    {
	      echo mysql_error();
	      return false;	  
	    }
	}
      echo propre(_T('tradlang:insertionlangueok')."<br>");
      ob_flush();

      unset($GLOBALS[$GLOBALS['idx_lang']]);

      // si le fichier est inscriptible, on sauvegarde le
      // fichier depuis la base afin de tagguer le timestamp
      if ($fd = @fopen($nom_fichier, "a"))
	{
	  fclose($fd);
	  tradlang_sauvegarde($module, $lg);
	}
    }

  return true;
}



// retourne une liste contenant les modules
// trouves dans le repertoire passe en parametre
function tradlang_getmodules_fics($rep)
{
  $ret = array();

  // parcourt de l'ensemble des fichiers
  $handle = opendir($rep);  
  while (($fichier = readdir($handle)) != '') 
    {
      // Eviter ".", "..", ".htaccess", etc.
      if ($fichier[0] == '.') continue;
      if ($fichier == 'CVS') continue;
      
      $nom_fichier = $rep."/".$fichier;
      if (is_file($nom_fichier)) 
	{
	  // cherche un fichier de la forme <nom module>_<langue>.php
	  if (preg_match("/^([a-z]*)_([a-z_]*)\.php$/i", $fichier, $match))
	    {
	      $nommod = $match[1];
	      $langue = $match[2];
	      
	      if (tradlang_verif($nom_fichier))
		{
		  // verifie si deja trouve
		  if (!isset($ret[$nommod]))
		    {
		      $ret[$nommod] = array();
		      $ret[$nommod]["nomfichier"]=$fichier;
		      $ret[$nommod]["nom_mod"]=$nommod;
		      $ret[$nommod]["dir_lang"]=$rep;
		    }
		  $ret[$nommod]["langue_".$langue] = $fichier;
		}
	    }
	}
    }  
  closedir($handle);

  return $ret;
}


// verifie si le fichier passe en param
// est bien un fichier de langue
function tradlang_verif($fic)
{
  include($fic);
  // verifie si c'est un fichier langue
  if (is_array($GLOBALS[$GLOBALS['idx_lang']]))
    {
      unset($GLOBALS[$GLOBALS['idx_lang']]);
      return true;
    }
  return false;
}

// retourne les modules disponibles
// dans la base de données
function tradlang_getmodules_base()
{
  $prefix = $GLOBALS['table_prefix'];
  $ret = array();

  // recup. des modules
  $req = "SELECT * FROM ".$prefix."_tradlang_modules;";
  $res = spip_query($req);
  if ($res)
    {
      while($row=spip_fetch_array($res))
	{
	  $nom_mod = $row["nom_mod"];
	  $ret[$nom_mod] = $row;
	  
	  // recup des langues pour le module
	  $req2 = " SELECT DISTINCT lang FROM ".$prefix."_tradlang WHERE module='".$nom_mod."'";
	  $res2 = spip_query($req2);
	  while($row2=spip_fetch_array($res2))
	    {
	      $lg = $row2["lang"];
	      // calcul du nom fichier langue
	      $ret[$nom_mod]["langue_".$lg] = $row["lang_prefix"]."_".$lg.".php";
	    }	  
	}
    }
  
  return $ret;
}


// teste si les table liees au module
// sont presentes dans la base
function tradlang_tablesabsentes()
{
  $prefix = $GLOBALS['table_prefix'];

  // teste si la table existe
  $req = "SELECT COUNT(*) FROM ".$prefix."_tradlang;";
  $res = spip_query($req);
  if ($res) 
    return false;

  return true;
}


// gestion de la cinematique "tables absentes"
function tradlang_gere_tablesabsentes()
{
  global $connect_statut;
  global $operation;

  $prefix = $GLOBALS['table_prefix'];

  // test si on est dans la phase de creation
  if ($operation == "creertables")
    {
      $ret = false;
      debut_boite_info();

      if (tradlang_creertables())
	{
	  echo propre(_T('tradlang:creationok'));
	  echo "<br><br>";
	  echo "<form action='".generer_url_ecrire("tradlang")."&amp;' method='post' name='tradlang'>\n";
	  echo "<input type='hidden' name='operation' value='utiliser' />\n";
	  echo "<input type='submit' class='fondo' value='"._T("tradlang:creationutiliser")."'>";
	  echo "</form>";
	  $ret = true;
	}
      else
	{
	  echo propre(_T('tradlang:creationnok'));
	  echo "<br>";	  
	  echo propre(mysql_error());
	  echo "<br><br>";	  
	  $req = tradlang_req();
	  echo nl2br(implode("\n",$req));
	}

      fin_boite_info();
      return $ret;
    }

  debut_boite_info();
  echo propre(_T('tradlang:tablenoncreee'));  	
  
  if ($connect_statut == "0minirezo") 
    {
      echo '<br><br>';

      echo "<form action='".generer_url_ecrire("tradlang")."' method='post' name='tradlang'>\n";
      echo "<input type='hidden' name='operation' value='creertables' />\n";
      echo "<input type='submit' class='fondo' value='"._T("tradlang:creertables")."'>";
      echo "</form>";
    }
  else
    {
      echo '<br><br>';
      echo propre(_T('tradlang:demandeadmin'));  	
    }
  
  fin_boite_info();
  
  return true;
}


// creation des tables
function tradlang_creertables()
{
  // creation des tables tradlang

  $reqs = tradlang_req();

  foreach($reqs as $req)
    {
      $res = spip_query($req);
      if (!$res) 
	return false;
    }

  return true;
}


// requere mysql pour creer les tables
function tradlang_req()
{
  $prefix = $GLOBALS['table_prefix'];  

  return array (
		"CREATE TABLE ".$prefix."_tradlang (id varchar(128) NOT NULL default '',module varchar(32) NOT NULL default 0,lang varchar(16) NOT NULL default '', str text NOT NULL, comm text NOT NULL, ts timestamp(14) NOT NULL, status varchar(16) default NULL, traducteur varchar(32) default NULL, md5 varchar(32) default NULL, orig tinyint(4) NOT NULL default '0', date_modif datetime default NULL ) TYPE=MyISAM;",
		"CREATE INDEX idx_tl on ".$prefix."_tradlang(id);",
		"CREATE INDEX idx_t2 on ".$prefix."_tradlang(module);",
		"CREATE INDEX idx_t4 on ".$prefix."_tradlang(module,lang);" ,
		"ALTER TABLE `spip_tradlang` ADD PRIMARY KEY ( `id` , `module` , `lang` ) ;",

		"CREATE TABLE `spip_tradlang_modules` (`idmodule` bigint(21) NOT NULL auto_increment,`nom_module` varchar(128) NOT NULL,`nom_mod` varchar(16) NOT NULL,`lang_mere` varchar(16) NOT NULL default 'fr',`type_export` varchar(16) NOT NULL default 'spip',`dir_lang` varchar(255) NOT NULL,`lang_prefix` varchar(16) NOT NULL, PRIMARY KEY  (`idmodule`),KEY `nom_module` (`nom_module`),KEY `nom_mod` (`nom_mod`) ) TYPE=MyISAM;",

		"ALTER TABLE `spip_tradlang_modules` ADD UNIQUE (`nom_mod`) ;"
	);
        
}



?>
