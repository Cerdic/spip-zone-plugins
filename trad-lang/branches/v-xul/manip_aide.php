<?

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
        Philippe Rivière <fil@rezo.net>

*/


$import = "traduction depuis le format SPIP vers le format de l'interface.";
$export = "traduction depuis le format de l'interface vers le format SPIP.";

$usage = "usage : %s (-import|-export) <code langue> <fichier origine> <fichier destination>\n".
         "    -import : ".$import."\n".
         "    -export : ".$export."\n\n";


function lvide($ligne)
{
  //echo "ligne=<".$ligne.">\n";
  if (ltrim($ligne) == "")
    return true;
  return false;
}


function target_file($codelg)
{
  return "./ecrire/AIDE/$codelg/aide";
}


function import($fic_orig, $fic_dest, $codelg)
{
  $fdd = fopen($fic_dest, "w");
  if (!$fdd)
    {
      echo "impossible ouvrir fichier ".$fic_dest." en ecriture.\n";
      exit(1);
    }

  $fdo = fopen($fic_orig, "r");
  if (!$fdo)
    {
      echo "impossible ouvrir fichier ".$fic_orig." en lecture.\n";
      return 1;
    }
  
  include("module_aide.php");

  $cpt = 0;
  $etat = 1;
  $lc = "";

  $texte = $lang_prolog;
  $texte .= "\$GLOBALS[\$GLOBALS['idx_lang']] = array(\n\n"; 

  $texte .= "
\"0_URL\" => \"http://listes.rezo.net/mailman/listinfo/spip-dev\",
\"0_langue\" => \"francais [fr]\",
\"0_liste\" => \"spip-dev@rezo.net\",
\"0_mainteneur\" => \"spip-dev@rezo.net\",

";

  while (!feof($fdo))
    {
      //echo "etat=".$etat."\n";
      $l = fgets($fdo, 4096);
      $l = str_replace('"', '\"', $l);
      switch ($etat)
	{
	case 1:
	  if (!lvide($l))
	    {
	      $lc = $l;
	      $etat = 2;
	    }
	  break;
	case 2:
	  if (!lvide($l))
	    $lc .= $l;
	  else
	    $etat = 3;
	  break;
	case 3:
	  $cpt += 1;
	  $lc = rtrim($lc);
	  $scpt = sprintf("\"ligne%05d\"", $cpt*10);
	  $texte .= $scpt." => \n\"".$lc."\",\n\n";
	  if (!lvide($l))
	    {
	      $lc = $l;
	      $etat = 2;
	    }
	  else
	    {
	      $lc = "";
	      $etat = 1;
	    }
	  break;
	}
    }

  //ecrit la derniere ligne
  $cpt += 1;
  $lc = rtrim($lc);
  $scpt = sprintf("\"ligne%05d\"", $cpt*10);
  $texte .= $scpt." => \n\"".$lc."\",\n\n";

  $texte = ereg_replace (",\n\n$", "\n\n);\n", $texte);
  $texte .= $lang_epilog;
  fwrite($fdd, $texte);

  fclose($fdo);
  return 0;
}


function export($fic_orig, $fic_dest, $codelg)
{
  global $isincluded;

  $fdd = @fopen($fic_dest, "w");
  if (!$fdd)
    {
      if (!$isincluded)
	{
	  echo "impossible ouvrir fichier ".$fic_dest." en ecriture.\n";
	  exit(1);
	}
      else
	return 1 ;
    }

  include("./traduction/module_aide.php");
  $texte = "";

  $GLOBALS['idx_lang'] = $lang_var.$codelg;
  include($fic_orig);

  if (is_array($GLOBALS['idx_lang']))
    $lang_str = $GLOBALS['idx_lang'];
  else
    $lang_str = $GLOBALS[$GLOBALS['idx_lang']];

  reset($lang_str);
  while(list($str,$ch) = each($lang_str))
    {
      if (strncmp($str, "0_", 2) != 0)
	$texte .= $ch."\n\n";
    } 

  fwrite($fdd, $texte);
  return 0;
}


//main
if (!isset($isincluded))
{
  $op = substr($argv[1], 1);

  if ( ($argc!=5) && (($op!="import") || ($op!="export")) )
    {
      echo sprintf($usage, $argv[0]);
      exit(1);
    }

  $codelg = $argv[2];
  $fic_orig = $argv[3];
  $fic_dest = $argv[4];

  echo "Confirmer l'".$op." (=".$$op.")\ndu fichier ".$fic_orig." vers le fichier ".$fic_dest.".\n(o/n)";

  $stdin = fopen('php://stdin', 'r');
  $rep = fscanf($stdin, "%s");
  fclose($stdin);
  if ($rep[0] != "o")
    {
      echo "arrêt traîtement.\n";
      exit(0);
    }
  
  if (!is_file($fic_orig))
    {
      echo "fichier origine ".$fic_orig." non existant.\n";
      exit(1);
    }

  $res = 1;

  if ($op == "import")
    $res = import($fic_orig, $fic_dest, $codelg);
  
  else if ($op == "export")
    $res = export($fic_orig, $fic_dest, $codelg);

  fclose($fdd);

  echo "\n".$op." ok.\n";
  exit($res);
}

?>
