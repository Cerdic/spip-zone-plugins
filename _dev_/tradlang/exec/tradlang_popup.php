<?php

// recuperation de ce qui peut etre recupere
// de l'ancienne version de trad-lang

/*

    This file is part of Trad-Lang.

    Trad-Lang is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Trad-Lang is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Trad-Lang; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    Copyright 2003 
        Florent Jugla <florent.jugla@eledo.com>, 
        Philippe Riviere <fil@rezo.net>

*/


//ini_set(memory_limit, "32M");


/* 
 * fonction pour lire la table langue
 * renvoie un array contenant la table
 */
function tradlang_lirelang($module, $langue, $type="")
{
  $prefix = $GLOBALS['table_prefix'];
  $ret = array();

  if ($type=="md5")
    {
      $quer = "SELECT id,md5 FROM ".$prefix."_tradlang ".
	"WHERE module='".$nom_mod."' AND lang='".$lang_orig."' AND !ISNULL(md5)";
      $res = spip_query($quer);
      while($row = spip_fetch_array($res))
	$ret[$row["id"]] = $row["md5"];
    }
  else
    {
      $nom_mod = $module["nom_mod"];
      
      $quer = "SELECT id,str,status FROM ".$prefix."_tradlang ".
	"WHERE module = '".$nom_mod."' AND lang='".$langue."' ORDER BY id";

      $res = spip_query($quer);
      while($row = spip_fetch_array($res))
	{
	  if ($row["status"] != "")
	    $statut = "<".$row["status"].">";
	  else
	    $statut = "";
	  $ret[$row["id"]] = $statut.$row["str"];
	}

      // initialise la chaine de tag timestamp sauvegarde
      $quer = "SELECT MAX(ts) as ts FROM ".$prefix."_tradlang ".
	"WHERE module = '".$nom_mod."' AND lang='".$langue."'";
      $res = spip_query($quer);
      $row = spip_fetch_array($res);
      $ts = $row["ts"];

      $ret["zz_timestamp_nepastraduire"] = $ts;
    }

  return $ret;
}


// sauvegarde d'un module/langue sur
// les fichiers. Le module + langue a
// sauver doivent etre passes en parametre
function tradlang_sauvegarde($module, $langue)
{
  $prefix = $GLOBALS['table_prefix'];

  // Debut du fichier de langue
  $lang_prolog = "<"."?php\n\n// This is a SPIP language file  --  Ceci est un fichier langue de SPIP\n\n";
  // Fin du fichier de langue
  $lang_epilog = "\n\n?".">\n";

  $fic_exp = $module["dir_lang"]."/".$module["langue_".$langue];
  $tab = array();
  $conflit = array();  
  $tab = tradlang_lirelang($module, $langue);

  ksort($tab);
  reset($tab);
  $initiale = "";
  $texte = $lang_prolog;
  $texte .= "\$GLOBALS[\$GLOBALS['idx_lang']] = array(\n";

  while (list($code, $chaine) = each($tab))
    {
      if (!array_key_exists($code, $conflit))
        {
          if ($initiale != strtoupper($code[0]))
            {
              $initiale = strtoupper($code[0]);
              $texte .= "\n\n// $initiale\n";
            }                                                                                                                                              
	  $texte .= "'".$code."' => '".texte_script($chaine)."',\n";
        }
    }

  // ecriture des chaines en conflit
  if (count($conflit))
    {
      ksort($conflit);
      reset($conflit);
      $texte .= "\n\n// PLUS_UTILISE\n";
      while (list($code, $chaine) = each($conflit))
        $texte .= "'".$code."' => '".texte_script($chaine)."',\n";
    }

  $texte = ereg_replace (",\n$", "\n\n);\n", $texte);
  $texte .= $lang_epilog;

  $nomfic=$fic_exp;
  $f = @fopen($fic_exp, "wb");
  if (!$f)
    return false;
  fwrite($f, $texte);
  fclose($f);
  @chmod($fic_exp, 0666);
  
  return true;
}



?>