<?

function mes($str) {

  return mysql_escape_string($str);
}


// bidouille ajoutee pour traiter le cas des &eacute; etc.
// dans le xml
function xmlc($str)
{
  global $g_deb;
  
  //$str = str_replace("&eacute;", "&#233;",$str);
  //$str = str_replace("&ccedil;", "&#231;",$str);
  //$str = str_replace("&agrave;", "&#224;",$str);
  //$str = str_replace("&ograve;", "&#242;",$str);
  //$str = str_replace("&ucirc;", "&#251;",$str);

  $str = str_replace("<MODIF>", "",$str);
  $str = str_replace("<NEW>", "",$str);

  //$str = html2unicode($str);
  //$str = str_replace("&", "&amp;", $str);

  $str = unhtmlentities($str);
  $str = str_replace("<", "&lt;", $str);
  $str = str_replace('"', '&quot;', $str); // a changer
  $g_deb->log(0, "str=<".$str.">");

  return $str;
}


/* fonction: johan dot andersson at strateg dot se */
function unhtmlentities ($string)
{
   $trans_tbl = get_html_translation_table (HTML_ENTITIES);
   $trans_tbl = array_flip ($trans_tbl);
   return strtr ($string, $trans_tbl);
}


// redefinition de la fonction _T de
// SPIP car celle ci fait trop de choses
function _TT($text, $args = '')
{
  //include_ecrire('inc_lang.php3');
  return html2unicode(traduire_chaine($text, $args));
}


function get_dir($lg)
{
  if ($lg=="ar"||$lg=="fa")
    return "rtl";
  else
    return "ltr";
}


// calcul approximatif du nombre
// de lignes pour les champs 'textarea'
function calc_nb_row($item)
{
  $nbmots = substr_count(trim($item), " ") + 1;
  $nbrow = intval(($nbmots / 10) + 2);
  return $nbrow;
}


// cette fonction est redefinie car celle 
// de spip utlise htmlspecialchar qui 
// appremment foire avec les car. espagnols
function entites_html_local($texte) 
{
  // ajout pour le slovaque
  $trans_caron = array(
    "\xa6" => "&#352;",
    "\x8a" => "&#352;",
    "\xa8" => "&#353;",
    "\x9a" => "&#353;",
    "\xb4" => "&#381;",
    "\xb8" => "&#382;");
  $texte = strtr($texte, $trans_caron);

  return corriger_entites_html(htmlentities($texte));
}


function get_modules()
{
  $dir_modules = DIRTD;

  $ret = array();

  $handle = opendir($dir_modules);  
  while (($fichier = readdir($handle)) != '') 
    {
      // Eviter ".", "..", ".htaccess", etc.
      if ($fichier[0] == '.') continue;
      if ($fichier == 'CVS') continue;
      
      $nom_fichier = $dir_modules."/".$fichier;
      if (is_file($nom_fichier)) 
	{
	  if (!ereg("^module_(.+)\.php$", $fichier, $extlg)) 
	    continue;
	  include($nom_fichier);
	  $ret[$extlg[1]]["fichier"]=$nom_fichier;
	  $ret[$extlg[1]]["nom"] = $nom_module;
	  $ret[$extlg[1]]["type"] = $type_module;
	}
    }  
  closedir($handle);

  return $ret;
}


function get_langues_interface()
{
  // recuperation des langues utilisees par l'interface
  // de traduction

  global $modules;
  include($modules["ts"]["fichier"]);

  return get_langues($nom_mod);
}


/*
 * functions get_id
 * renvoie une string permettant d'encapsuler
 * l'ensemble des champs id en input hidden
 */
function get_id_post($ids)
{
  reset($ids);
  $ret = "";
  while(list(,$id)=each($ids))
    $ret .= "<INPUT TYPE='hidden' VALUE='".$id."' NAME='id[]'>";
  return $ret;
}


function get_id_get($ids)
{
  reset($ids);
  $ret = "";
  while(list(,$id)=each($ids))
    $ret .= "&id[]=".$id;
  return $ret;
}


/*
 * fonctions pour serialiser/deserialiser
 * l'array qui stocke les dates
 */
function get_dates($lang_cible)
{
  global $nom_mod;

  $arr_date = array();

  $quer = "SELECT id,date_modif FROM ".TRAD_LANG." ".
    "WHERE module = '".$nom_mod."' AND lang='".$lang_cible."'";

  $res = mysql_query($quer);
  while($row = mysql_fetch_assoc($res))
    {
      $arr_date[$row["id"]] = $row["date_modif"];
    }

  return $arr_date;
}


/*
 * recherche occurences de $chaines
 * dans la langue donnee
 */
function cherche_occurence($chaine, $lang_or, $lang_dst)
{
  $ret = array();

  $chaine = supp_ltgt(entites_html_local($chaine));

  $quer = "SELECT t1.id,t1.str,t2.str FROM ".TRAD_LANG." t1,".TRAD_LANG." t2 ".
    "WHERE t1.str like '%".$chaine."%' AND t1.lang='".$lang_or."' ".
    " AND t1.id=t2.id AND t2.lang='".$lang_dst."'";

  $res = mysql_query($quer);
  while($row = mysql_fetch_row($res))
    {
      $ret[$row[0]] = array($row[1], $row[2]);
    }

  return $ret;
}


/*
 * recherche de toutes les occurences
 * d'une chaine dans le fichier langue
 */
function cherche_chaines($texte, $lang_str_orig)
{
  $idr = array();
  while(list($id, $chaine) = each($lang_str_orig))
    {
      if (eregi($texte, $chaine) OR eregi($texte,$id))
	$idr[] = $id;
    }
  return $idr;
}


// sordide bidouille pour recuperer la variable
// langue. Permet la coexistence de l'ancienne forme
// avec celle de la nouvelle
function get_idx_lang()
{
  if (is_array($GLOBALS['idx_lang']))
    return $GLOBALS['idx_lang'];
  else
    return $GLOBALS[$GLOBALS['idx_lang']];
}


// $table_reponse=reference sur la table resultat
// type=nature de la recheche (non traduit, traduit, tous, etc.)
// $langue=la langue sur laquelle appliquer le filtre
// $filtre = le filtre de recherche
// $date = date filtrant la recherche ; si egal a "-", toutes les dates
// $id= la liste eventuellement selectionnee
// $cpt = reference sur le pourcentage renvoye
// $lgorig, $lgcible=references sur les noms de langues selectionnees
// $type_recherche=type de recherche (1=fenetre recherche, 2=fenetre traduction
//    3=fenetre administration)
// $date_pos = reference sur une array qui contient au retour toutes
//    les dates possibles
function recherche($table_reponse, $type, $langue, $filtre, $date, $id, 
   $cpt, $lgorig, $lgcible, $type_recherche, $date_pos)
{
  global $nom_mod, $lang_orig, $lang_suffix;
  global $lang_cible;

  $lang_str_orig = array();
  lire_lang($lang_orig, &$lang_str_orig);
  $lgorig = $lang_str_orig["0_langue"];

  if ($type_recherche != 3)
    { 
      $lang_str_cible = array();
      lire_lang($lang_cible, &$lang_str_cible);

      $lgcible = $lang_str_cible["0_langue"];
      if (ereg("^<NEW>", $lgcible)) $lgcible = "nouveau [$lang_cible]";
    }

  $cpt_tous = 0.0;
  $cpt_aff = 0.0;
  $lang_str = array();
  if ($filtre!='')
    $filtre = supp_ltgt(entites_html_local($filtre));

  reset($lang_str_orig);
  while (list($idl, $val) = each($lang_str_orig))
    {
      $cpt_tous += 1.0;

      if ($type_recherche != 3)
	{
	  $lang_str[$idl]['orig'] = $val;
	  
	  // si filtre sur cible et vide, exclure
	  if ($filtre=='' || $langue=='origine')
	    $lang_str[$idl]['cible'] = '-vide-';
	  else
	    $lang_str[$idl]['cible'] = '-exclu-';
	  
	  if (($filtre!='') && ($langue=='origine'))
	    {
	      if (!eregi($filtre, $val) and !eregi($filtre,$idl))
		$lang_str[$idl]['orig'] = '-exclu-';
	    }
	}
      
      else // type_recherche = 3 (admin, pas de pbs. conflit, etc.)
	{
	  if ($filtre == '')
	    {
	      $table_reponse[$idl] = $val;
	      $cpt_aff += 1.0;
	    }
	  else
	    {
	      if (eregi($filtre, $val) || eregi($filtre,$idl))
		{
		  $table_reponse[$idl] = $val;
		  $cpt_aff += 1.0;
		}
	    }
	}	
    }

  // si type de recherche admin, on arrete les frais
  if ($type_recherche == 3)
    {
      $cpt = ($cpt_aff/$cpt_tous)*100;
      reset($table_reponse);
      return;
    }

  // recupere l'array qui contient l'info
  // sur les dates
  $arr_date = get_dates($lang_cible);

  // positionne le tableau date.
  while(list($val, $dt) = each($arr_date))
    {
      $date_pos[] = $dt;
    }

  reset($lang_str_cible);
  while (list($idl, $val) = each($lang_str_cible))
    {
      $lang_str[$idl]['cible'] = $val;

      if (!$lang_str_orig[$idl])
	{
	  $cpt_tous += 1.0;
	  // si filtre sur origine et vide, exclure
	  if ($filtre=='' || $langue=='cible')
	    $lang_str[$idl]['orig'] = '-vide-';
	  else
	    $lang_str[$idl]['orig'] = '-exclu-';
	}

      if (($filtre!='') && ($langue == 'cible'))
	{
	  if (!eregi($filtre, $val) AND !eregi($filtre,$idl))
	    $lang_str[$idl]['cible'] = '-exclu-';
	}
    }

  ksort($lang_str);
  reset($lang_str);
  $flag_sel = 0;

  $cpt_traduit = 0.0;
  $cpt_non_traduit = 0.0;
  $cpt_conflit = 0.0;
  $cpt_modifie = 0.0;

  $dern_id = '';
  $nb_id = 1;
  if (is_array($id))
    {
      $nb_id = count($id);
      $dern_id = $id[$nb_id-1];
    }

  if (count($lang_str)==1)
    $flag_sel=1;

  while (list($idl, $val) = each($lang_str))
    {
      $val_orig = $val['orig'];
      $val_cible = $val['cible'];

      if (($val_orig=='-exclu-') || ($val_cible=='-exclu-'))
	continue;

      //echo $val_orig.", ".$val_cible."<br>";

      if (($val_orig=='-vide-') || ereg("^<NEW>(.*)", $val_orig))
	$orig = 0;
      else
	$orig = 1;

      if (($val_cible=='-vide-') || ereg("^<NEW>(.*)", $val_cible))
	$cible = 0;
      else
	$cible = 1;

      if (ereg("^<MODIF>(.*)", $val_cible))
	$modifie = 1;
      else
	$modifie = 0;

      $statut = "";

      // calcule la date
      $dt = $arr_date[$idl];
      if ($dt)
	$dt = substr($dt, 8, 2)."/".substr($dt, 5, 2)."/".substr($dt, 0, 4);
      //$dt = date("d M Y", (int)$dt);

      $date_ok = true;
      // si le critere de date est active, verifie
      // que la date est bonne
      if ($date != "" && $date != "-")
	{
	  if ($dt != $date)
	    $date_ok = false;
	}

      if ($dt != "")
	$dt = " - ".$dt;

      if ($date_ok)
	{
	  $cpt_aff += 1.0;

	  if ($orig == 0)  // champ non present en vo
	    {
	      if ($type=='tous' || $type=='conflit')
		{
		  $statut = $idl."&nbsp;["._TT('ts:item_conflit')."]".$dt;
		  $cpt_conflit += 1.0;
		}
	    }
	  else if ($orig==1 && $cible==0)  // champ non present en langue cible
	    {
	      if ($type=='tous' || $type=='non_traduit' || $type=='revise')
		{
		  $statut = $idl."&nbsp;["._TT('ts:item_non_traduit')."]".$dt;
		  $cpt_non_traduit += 1.0;
		}
	    }
	  else  // $orig==1 && cible==1 ; champs present en vo et en cible
	    {
	      if (($type=='tous' || $type=='traduit') && $modifie==0)
		{
		  $statut = $idl."&nbsp;["._TT('ts:item_traduit')."]".$dt;
		  $cpt_traduit += 1.0;
		}
	      else if (($type=='tous' || $type=='modifie' || $type=='revise') && $modifie==1)
		{
		  $statut = $idl."&nbsp;["._TT('ts:item_modifie')."]".$dt;
		  $cpt_modifie += 1.0;
		}
	    }
	}
 
      $opt = "";
      if ($statut != "")
	{
	  if ($flag_sel != 0)
	    {
	      $flag_sel --;
	      $opt = " SELECTED";
	    }

	  if ($type_recherche==1)
	    $table_reponse[] = "<OPTION VALUE='".$idl."'".$opt.">".$statut."\n";
	  else
	    $table_reponse[$idl] = $val;
	}

      if ($dern_id==$idl)
	$flag_sel = $nb_id;
    }

  reset($table_reponse);

  $cpt_courant = $cpt_aff;
  if ($type == 'traduit')
    $cpt_courant = $cpt_traduit;
  else if ($type == 'non_traduit')
    $cpt_courant = $cpt_non_traduit;
  else if ($type == 'conflit')
    $cpt_courant = $cpt_conflit;
  else if ($type == 'modifie')
    $cpt_courant = $cpt_modifie;
  else if ($type == 'revise')
    $cpt_courant = $cpt_modifie+$cpt_non_traduit;

  $cpt = ($cpt_courant/$cpt_tous)*100;

}



function test_module($nom_mod)
{
  $quer = "SELECT id FROM ".TRAD_LANG." WHERE module='".
    $nom_mod."' LIMIT 0,1";
  $res = mysql_query($quer);
  if (mysql_num_rows($res)>0)
    return 1;
  return 0;
}


/* 
 * fonction pour lire la table langue
 * lang_str = ref.
 */
function lire_lang($lang_orig, $lang_str, $type="")
{
  global $nom_mod;

  $lang_str = array();

  if ($type=="md5")
    {
      $quer = "SELECT id,md5 FROM ".TRAD_LANG." ".
	"WHERE module='".$nom_mod."' AND lang='".$lang_orig."' AND !ISNULL(md5)";
      $res = mysql_query($quer);
      while($row = mysql_fetch_assoc($res))
	$lang_str[$row["id"]] = $row["md5"];
    }
  else
    {
      $quer = "SELECT id,str,status FROM ".TRAD_LANG." ".
	"WHERE module = '".$nom_mod."' AND lang='".$lang_orig."' ORDER BY id";
      $res = mysql_query($quer);
      while($row = mysql_fetch_assoc($res))
	{
	  if ($row["status"] != "")
	    $statut = "<".$row["status"].">";
	  else
	    $statut = "";
	  $lang_str[$row["id"]] = $statut.$row["str"];
	}
    }
}


function get_comment($id, $lang, $module)
{
  $quer = "SELECT comm FROM ".TRAD_LANG." WHERE id='".$id."' AND ".
    "lang='".$lang."' AND module='".$module."';";

  $res = mysql_query($quer);
  $row = mysql_fetch_assoc($res);
  return ($row["comm"]);
}

function enregistre_comment($id, $lang, $module, $comm)
{
  $quer = "UPDATE ".TRAD_LANG." SET comm='".texte_script($comm)."' WHERE ".
    "id='".$id."' AND lang='".$lang."' AND module='".$module."';";

  $res = mysql_query($quer);
}

function test_item($nom_mod, $codelg, $key)
{
  $quer = "SELECT id FROM ".TRAD_LANG." ". 
    "WHERE id = '".$key."' AND module = '".$nom_mod."' ".
    "AND lang='".$codelg."'";
  $res = mysql_query($quer);
  if (mysql_num_rows($res) > 0)
    return 1;
  return 0;
}


/*
 * cette fonction permet d'extraire le statut
 * de la chaine depuis le format originel, vers
 * le format base de donnee
 * chaine et statut sont passes par ref.
 */
function extrait_statut($chaine, $statut)
{
  if (ereg("^\<([a-zA-Z_]+)\> (.*)", $chaine, $r))
    {
      $chaine = $r[2];
      $statut = $r[1];
    }
}


function effacer_item($nom_mod, $key)
{
  $quer = "DELETE FROM ".TRAD_LANG." WHERE module='".$nom_mod."' AND id='".$key."'";
  $res = mysql_query($quer);  
}


// retourne la valeur du champ dans
// la langue origine
function get_val_orig($id)
{
  global $nom_mod, $lang_mere;

  $ret = "";
  $quer = "SELECT str FROM ".TRAD_LANG." WHERE module='".$nom_mod."' AND lang='".$lang_mere."'".
    " AND id='".$id."'";
  $res = mysql_query($quer);  
  if ($res)
    {
      $row = mysql_fetch_assoc($res);
      $ret = $row['str'];
    }
  return $ret;
}


/*
 * ecrit une ligne dans la base. si  la ligne existait
 * deja, elle est ecrasee
 */
function ecrire_item($codelg, $id, $chaine, $type="")
{
  global $nom_mod, $lang_mere;

  $orig = 0;
  if ($codelg == $lang_mere)
    $orig = 1;

  $statut = "";
  extrait_statut(&$chaine, &$statut);
  
  $dummy = "";
  if ($orig == 0)
    {
      // on va chercher la valeur dans la 
      // langue origine pour calculer le md5
      $md5 = md5(get_val_orig($id));
      $dummy = ", md5='".$md5."'";
    }

  if (test_item($nom_mod, $codelg, $id))
    {
      if ($type == "debut")  // pas de remise a jour de date_modif ou md5
	$quer = "UPDATE ".TRAD_LANG." SET str='".$chaine."'".
	  ",status='".$statut."' WHERE id='".$id."' AND lang='".$codelg."' AND ".
	  " module='".$nom_mod."'";

      else
	$quer = "UPDATE ".TRAD_LANG." SET str='".$chaine."'".
	  ",status='".$statut."', date_modif=NOW()".$dummy." WHERE ".
	  "id='".$id."' AND lang='".$codelg."' AND ".
	  " module='".$nom_mod."'";
    }
  else
    {
      if ($dummy != "")
	$quer = "INSERT INTO ".TRAD_LANG." (id,module,lang,status,str,orig,md5) ".
	  "VALUES ('".$id."','".$nom_mod."','".$codelg."','".$statut."','".
	  $chaine."',".$orig.",'".$md5."')";
      else
	$quer = "INSERT INTO ".TRAD_LANG." (id,module,lang,status,str,orig) ".
	  "VALUES ('".$id."','".$nom_mod."','".$codelg."','".$statut."','".
	  $chaine."',".$orig.")";
    }

  $res = mysql_query($quer);  
}


/*
 * ecrire la table langue dans la base
 * le champ "type" doit etre initialise avec "md5" pour sauvegarder
 * les fichiers de controle
 */
function ecrire_lang($lang_str, $codelg, $conflit=array(), $type="") 
{
  global $left, $right;
  global $nom_mod, $lang_prolog, $lang_epilog;
  global $export_function;

  if(!$codelg) return false;

  ksort($lang_str);
  reset($lang_str);
  $initiale = "";

  while (list($code, $chaine) = each($lang_str)) 
    {
      if (!array_key_exists($code, $conflit))
	ecrire_item($codelg, $code, texte_script($chaine), $type);
    }
  
  // ecriture des chaines en conflit
  if (count($conflit))
    {
      ksort($conflit);
      reset($conflit);
      while (list($code, $chaine) = each($conflit)) 
	ecrire_item($codelg, $code, texte_script($chaine));
    }

  $res = false;
  if (($export_function != "") && function_exists($export_function))
    {// sauvegarde du fichier
      $fic_exp_cible = "";
      $res = call_user_func($export_function, $codelg, &$fic_exp_cible, false);
    }

  return $res;
}


function supp_ltgt($texte) 
{
  $texte = ereg_replace ('&quot;', '"', $texte);
  $texte = ereg_replace ('&lt;', '<', $texte);
  $texte = ereg_replace ('&gt;', '>', $texte);
  $texte = ereg_replace ('&amp;lt;', '&lt;', $texte);
  $texte = ereg_replace ('&amp;gt;', '&gt;', $texte);
  return $texte;
}


function recup_modif($item)
{
  $item = supp_ltgt(entites_html_local(stripslashes($item)));
  $item = str_replace("^", "&nbsp;", $item);
  $item = str_replace("\x80", "&euro;", $item);
  $item = str_replace("\r", "", $item);
  $item = str_replace("\n", "", $item);
  $item = str_replace("$", "\n", $item);
  $item = interdire_scripts($item);
  return $item;
}

function affiche_modif($item)
{
  $item = str_replace("&nbsp;", "^", $item);
  $item = ereg_replace("\r\n", "\n", $item);
  $item = ereg_replace("\n", "$\n", $item);
  $item = ereg_replace("\t", " ", $item);
  $item = ereg_replace("&lt;", "&amp;lt;", $item);
  $item = ereg_replace("&gt;", "&amp;gt;", $item);
  $item = ereg_replace("<", "&lt;", $item);
  $item = ereg_replace(">", "&gt;", $item);
  return $item;
}

function affiche_consult($texte)
{
  $texte = str_replace("<", "&lt;", $texte);
  $texte = str_replace(">", "&gt;", $texte);
  $texte = ereg_replace ('"', '&quot;', $texte);
  $texte = ereg_replace ("'", '&#039;', $texte);
  return $texte;
}



function debut_html_ts($titre = "", $taille=550) 
{
  global $direction;

  if ($titre=='')
    $titre = _TT('ts:titre_traduction');
  include("./TRAD_LANG_DIR/trad_lang_header.php");
}


function fin_html_ts() 
{
  global $tlversion;
  include("./TRAD_LANG_DIR/trad_lang_footer.php");
}


function debut_table($titre_table, $retour="./trad_lang.php")
{
  global $left,$right;
  global $spip_lang, $module, $admin_ok;
  echo " <table border=0 cellspacing=2 cellpadding=5 bgcolor=#cccccc ";
  echo "class=window align=center width=700>";
  echo "<tr> ";
  echo "<td colspan=2 bgcolor=#000088>";
  echo "<table width=100% border=0 cellspacing=0 cellpadding=2 class=windowtitle>";
  echo "<FORM ACTION='".$retour."' METHOD=POST> ";
  echo "<INPUT TYPE='hidden' NAME='module' VALUE='".$module."'>";
  echo "<INPUT TYPE='hidden' NAME='spip_lang' VALUE='".$spip_lang."'>";
  echo "<tr> ";
  echo "<td align='$left' width='80%'>".$titre_table;
  echo "</td>";

  echo "<td align='$right' width='15%'>";
  if ($admin_ok == true)
    echo "<font color='white'>"._TT("ts:item_admin")."</font>";
  echo "</td>";

  echo "<td align='$right' width='5%'>";
                                                                       
  echo "<input ALT=\""._TT('ts:lien_quitter')."\" title=\""._TT('ts:lien_quitter')."\" type=\"image\" value=\"Quitter\" HSPACE=0 border=0 src=\"./images/stop.gif\" name=\"quitter\" OnClick=\"if (confirm('".addslashes(_TT('ts:lien_page_depart'))."')) submit(); else return false;\">";   

  //echo "<INPUT TYPE='submit' NAME='X' VALUE='&nbsp;X&nbsp;' OnClick='if (confirm(\"".addslashes(_TT('ts:lien_page_depart'))."\")) submit(); else return false;'>";

  echo "</td> ";
  echo "</tr> ";
  echo "</FORM>";
  echo "</table> ";
  echo "</td> </tr>";

}


function fin_table()
{
  echo "</table>";
}


function error($texte, $action) 
{
  global $left,$right;
  debut_table(_TT('ts:texte_interface')."<font color='red'>"._TT('ts:texte_erreur')."</font>"."<br>&nbsp;");

  echo "<tr><td colspan=2 class=line_pres align=center>";
  echo "<br>".$texte."<br>";
  echo "</td></tr>";

  echo "<FORM ACTION='".$action."' NAME='returnable' METHOD='POST'>";
  echo "<tr><td class=line align=$right colspan=2>";
  echo "<INPUT TYPE='submit' NAME='Valider' VALUE='"._TT('ts:bouton_revenir_2')."'>\n";
  echo "</td></tr>";
  echo "</FORM>";

  fin_table();
}




function get_langues($nom_mod, $excl="")
{
  $quer = "SELECT distinct lang FROM ".TRAD_LANG." WHERE module='".$nom_mod."'";
  $res = mysql_query($quer);

  $ret = array();
  while ($row=mysql_fetch_assoc($res))
    {
      if ($excl != $row["lang"])
	$ret[] = $row["lang"];
    }
  return $ret;
}






?>
