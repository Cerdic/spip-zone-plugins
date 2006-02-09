<?php 


//	  exec_mots_partout.php
//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2005  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


/***********************************************************************
 * function
 ***********************************************************************/

  
function verifier_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
}

function verifier_admin_restreint($id_rubrique) {
  global $connect_id_auteur;
  global $connect_statut, $connect_toutes_rubriques;

}

function verifier_auteur($table, $id_objet, $id) {
  global $connect_id_auteur;
  $select = array('id_auteur');
  
  $from =  array($table);
  
  $where = array("id_auteur = $connect_id_auteur", "$id_objet = $id");
  
  $result = spip_abstract_select($select,$from,$where);
  
  if (spip_abstract_count($result) > 0) {
	spip_abstract_free($result);
	return true;
  }
  spip_abstract_free($result);
  return false;
}


function calcul_numeros($array, $search, $total) {
  if(is_array($array))
	$tt = count(array_keys($array,$search));
  else
	return 0;

  if($tt == 0) return 0;
  if($tt < $total) return 1;
  return 2;
}

function md_afficher_liste($largeurs, $table, $styles = '') {
	global $couleur_claire;
	global $browser_name;
	global $spip_display;
	global $spip_lang_left;

	if (!is_array($table)) return;
	reset($table);
	echo "\n";
	if ($spip_display != 4) {
		while (list(,$t) = each($table)) {
			if (eregi("msie", $browser_name)) $msover = " onMouseOver=\"changeclass(this,'tr_liste_over');\" onMouseOut=\"changeclass(this,'tr_liste');\"";
			echo "<tr class='tr_liste'$msover>";
			reset($largeurs);
			if ($styles) reset($styles);
			while (list($texte, $sel) = each($t)) {
				$style = $largeur = "";
				list(, $largeur) = each($largeurs);
				if ($styles) list(,$style) = each($styles);
				if (!trim($texte)) $texte .= "&nbsp;";
				echo "<td";
				if ($largeur) echo " width=\"$largeur\"";
				if ($style)  echo ' class="'.$style[$sel].'"';
				echo ">$texte</td>";
			}
			echo "</tr>\n";
		}
	} else {
		echo "<ul style='text-align: $spip_lang_left;'>";
		while (list(, $t) = each($table)) {
			echo "<li>";
			reset($largeurs);
			if ($styles) reset($styles);
			while (list(, $texte) = each($t)) {
				$style = $largeur = "";
				list(, $largeur) = each($largeurs);
				
				if (!$largeur) {
					echo $texte." ";
				}
			}
			echo "</li>\n";
		}
		echo "</ul>";
	}
	echo "\n";
}


function find_tables($nom, $tables) {
  $toret = array();
  foreach($tables as $t => $dec) {
	if(ereg($nom,$t)) {
	  $toret[] = $t;
	}
  }
  return $toret;
}

function calcul_in($mots) {
  for($i=0; $i < count($mots); $i++) {
	if($i > 0) $to_ret .= ',';
	$to_ret .= $mots[$i];
  }
  return $to_ret;
}

function secureIntArray($array) {
  if(is_array($array)) {
	$to_return = Array();
	foreach($array as $id) {
	  $to_return[] = intval($id);
	}
  } 
  return $to_return;
}

//======================================================================

function afficher_liste_defaut($choses) {
  echo '<table>';
  $i = 0;
  foreach($choses as $id_chose) {
	$i++;
	echo "<td><tr><input type='checkbox' name='id_choses[]' value='$id_chose' id='id_chose$i'/></tr><tr> <label for='id_chose$i'>$id_chose</label></tr></td>";
  }
  echo '</table>';
}

//---------------------------------------------------------------------

function mots_partout() {

  include("mots_partout_choses.php");
  include_ecrire ("inc_presentation");
  include_ecrire ("inc_documents");
  include_ecrire ("inc_abstract_sql");
  include_ecrire ("inc_objet");

  
  /***********************************************************************
 * récuperation de la chose sur laquelle on travaille
 ***********************************************************************/

$nom_chose = addslashes($_POST['nom_chose']);
if(!isset($choses_possibles[$nom_chose])) {
  list($nom_chose,) = each($choses_possibles);
  reset($choses_possibles);
}
$id_chose = $choses_possibles[$nom_chose]['id_chose'];
$table_principale = $choses_possibles[$nom_chose]['table_principale'];
$table_auth = $choses_possibles[$nom_chose]['table_auth'];
$tables_limite = $choses_possibles[$nom_chose]['tables_limite'];

/***********************************************************************
 * action
 ***********************************************************************/
$mots = secureIntArray($_REQUEST['id_mots']);
$sans_mots = secureIntArray(addslashes($_REQUEST['sans_mots']));
$choses = secureIntArray($_REQUEST['id_choses']);
$limit =  addslashes($_POST['limit']);
if($limit == '') $limit = 'rien';
$id_limit =  intval($_POST['id_limit']);
if($id_limit < 1) $id_limit = 0;
$nb_aff = intval($_POST['nb_aff']);
if($nb_aff < 1) $nb_aff = 20;
$switch = addslashes($_POST['switch']);
if($switch == '') $switch = 'voir';
$strict = intval($_POST['strict']);

//echo "!!!".$nom_chose."!!!";
//echo "action :".$_REQUEST['switch']."<br>";
//echo "choses :".serialize($choses)."<br>";
//echo count($mots)." mots :".serialize($mots)."<br>";
//echo "sans_mots :".serialize($sans_mots)."<br>";
//echo "limit :".serialize($limit)."<br>";
//echo "id_limit :".serialize($id_limit)."<br>";

if($switch == 'action' && count($choses)) {
	if(count($mots)) {
	  foreach($mots as $m) {	
		$from = array('spip_mots');
		$select = array('id_groupe');
		$where = array("id_mot = $m");
		$res = spip_abstract_select($select,$from,$where);
		$unseul = false;
		$id_groupe = 0;
		$titre_groupe = '';
		if($row = spip_abstract_fetch($res)) {
		  spip_abstract_free($res);
		  $from = array('spip_groupes_mots');
		  $select = array('unseul','titre');
		  $id_groupe = $row['id_groupe'];
		  $where = array("id_groupe = $id_groupe");
		  $res = spip_abstract_select($select,$from,$where);
		  if($row = spip_abstract_fetch($res)) {
			$unseul = ($row['unseul'] == 'oui');
			$titre_groupe = $row['titre'];
		  }
		}
		spip_abstract_free($res);
		foreach($choses as $d) {
		  if($unseul) {
			$from = array("spip_mots_$nom_chose",'spip_mots');
			$select = array("count('id_mot') as cnt");
			$where = array("id_groupe = $id_groupe","spip_mots_$nom_chose.id_mot = spip_mots.id_mot","$id_chose = $d");
			$group = $id_chose;
			$res = spip_abstract_select($select,$from,$where,$group);
			if($row = spip_abstract_fetch($res)) {	
			  if($row['cnt'] > 0) {
				$warnings[] = array(_T('motspartout:dejamotgroupe',array('groupe' => $titre_groupe, 'chose' => $d)));
				continue; 
			  }
			}
			spip_abstract_free($res);
		  }
//		  echo "!!!!!!!action insert:"."$nom_chose(id_mot,$id_chose)($m,$d)"."!!!!!!!!!";
		  
		  spip_abstract_insert("spip_mots_$nom_chose","(id_mot,$id_chose)","($m,$d)");
		}
	  }
	}
	if (count($sans_mots)) {
	    foreach($sans_mots as $m) {
		  foreach($choses as $d) {
//			echo "!!!!!!!action delete:"."$nom_chose(id_mot,$id_chose)($m,$d)"."!!!!!!!!!";
		  	spip_query("DELETE FROM spip_mots_$nom_chose WHERE id_mot=$m AND $id_chose=$d");
		  }
		}
	}
}
/**********************************************************************
* recherche des choses.
***********************************************************************/

if(count($choses) == 0) {
  $select = array();
  $select[] = "DISTINCT main.$id_chose";
  
  $from = array();
  $where = array();
  $group = '';
  $order = array();
  
  if(isset($limit) && $limit != 'rien') {
	$table_lim = $tables_limite[$limit]['table'];
	$nom_id_lim = $tables_limite[$limit]['nom_id'];
	
	$from[0] = "$table_lim as main";
	$where[0] = "main.$nom_id_lim IN ($id_limit)"; 
	if(count($mots) > 0) {
	  $from[1] = "spip_mots_$nom_chose as table_temp";
	  $where[1] = "table_temp.$id_chose = main.$id_chose";
	  $where[] = "table_temp.id_mot IN (".calcul_in($mots).')';
	  if($strict) {
		$select[] = 'count(id_mot) as tot';
		$group = "main.$id_chose";
		$order = array('tot DESC');
	  }
	}
	if(count($sans_mots) > 0) {
	  $from[1] = "spip_mots_$nom_chose as table_temp";
	  $where[1] = "table_temp.$id_chose = main.$id_chose";
	  $where[] = "table_temp.id_mot not IN (".calcul_in($sans_mots).')';
	  if($strict) {
		$select[] = 'count(id_mot) as tot';
		$group = "main.$id_chose";
		$order = array('tot DESC');
	  }
	}	
  } else if((count($mots) > 0)||(count($sans_mots) > 0)){
  	if(count($mots) > 0) {
	  $from[0] = "spip_mots_$nom_chose as main";
	  $where[] = "main.id_mot IN (".calcul_in($mots).')';
	  if($strict) {
		$select[] = 'count(id_mot) as tot';
		$group = "main.$id_chose";
		$order = array('tot DESC');
	  }
  	}
  	if(count($sans_mots) > 0) {
	  $from[0] = "spip_mots_$nom_chose as main";
	  $where[] = "main.id_mot not IN (".calcul_in($sans_mots).')';
	  if($strict) {
		$select[] = 'count(id_mot) as tot';
		$group = "main.$id_chose";
		$order = array('tot DESC');
	  }
  	}
  } else {
	$from[] = "$table_principale as main"; 
  }

//  echo "select :".serialize($select);
//  echo "from :".serialize($from);
//  echo "where :".serialize($where);
//  echo "group :".serialize($group);
//  echo "order :".serialize($order);

  $res=spip_abstract_select($select,$from,$where,$group,$order);
  
  $choses = array();
  $avec_sans = (count($sans_mots) > 0);
  if($avec_sans) $in_sans = calcul_in($sans_mots);
  while ($row = spip_abstract_fetch($res)) {
	if(!isset($table_auth) ||
	   (isset($table_auth) &&
		(verifier_admin() ||
		 verifier_auteur($table_auth,$id_chose,$row[$id_chose])
		 )
		)
	   ) {
	  if($avec_sans) {
		$test = spip_abstract_select(array($id_chose),array("spip_mots_$nom_chose"),array("id_mot IN ($in_sans)","$id_chose = ".$row[$id_chose]));
		if(spip_abstract_count($test) > 0) {
		  continue;
		}
		spip_abstract_free($test);
	  }
	  if(count($mots) > 0 && $strict) {
		if($row['tot'] >= count($mots)) {
		  $choses[] = $row[$id_chose];
		} else {
		  break;
		}
	  } else {
		$choses[] = $row[$id_chose];
	  }
	}
  }
  spip_abstract_free($res);
}

if(count($choses) > 0) {
  $query = "SELECT spip_mots_$nom_chose.id_mot FROM spip_mots_$nom_chose WHERE spip_mots_$nom_chose.$id_chose".((count($choses))?(' IN('.calcul_in($choses).')'):'');

  afficher_tranches_requete($query, 3,'debut',false,$nb_aff);
  
  $res = spip_query($query);
  
  while ($row = spip_fetch_array($res)) {
	$show_mots[] = $row['id_mot'];
  }
  spip_free_result($res);
} 

/***********************************************************************
 * affichage
 ***********************************************************************/

debut_page('&laquo; '._T('motspartout:titre_page').' &raquo;', 'documents', 'mots');
?>
</script>
<?php

echo '<br><br><center>';
gros_titre(_T('motspartout:titre_page'));
echo '</center>';

//Colonne de gauche
debut_gauche();

echo '<form method="post" action="'.generer_url_ecrire('mots_partout','').'">';


// choix de la chose sur laquelle on veut ajouter des mots
debut_cadre_enfonce('',false,'',_T('motspartout:choses'));
//echo  '<form action="'.generer_url_ecrire('mots_partout','').'">';
echo '<div class=\'liste\'>
<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
<tr class=\'tr_liste\'>
<td><select name="nom_chose">';
foreach($choses_possibles as $cho => $m) {
  echo "<option value=\"$cho\"".(($cho == $nom_chose)?'selected':'').'>'._T($m['titre_chose']).'</option>';
}
echo '</select></td>';

echo '<td><label for="nb_aff">'._T('motspartout:par').':</label><select name="nb_aff">';

  for($nb = 10;$nb<count($choses);$nb=$nb+10)
	echo "<option value=\"$nb\"".(($nb == $nb_aff)?'selected="true"':'').">$nb</option>";

  echo '</select></td>';

echo '</tr>
<tr class=\'tr_liste\'><td colspan=2>'.
_T('motspartout:limite').
':</td></tr>';
echo '<tr class=\'tr_liste\'><td><select name="limit">
<option value="rien" selected="true">'.
_T('motspartout:aucune').
'</option>';

foreach($tables_limite as $t => $m) {
  echo "<option value=\"$t\"".(($t == $limit)?'selected':'').">$t</option>";
}

echo '</select></td>';
echo "<td><input type='text' size='3' name='id_limit' value='$id_limit'></td></tr>";
echo '<tr class=\'tr_liste\'>';
?>
<td colspan=3><button type='submit' name='switch' value='chose'>
<?php echo _T('motspartout:voir'); ?>
</button>
</td>
</table></div>
<?php
fin_cadre_enfonce();
echo "</form><form method='post' action='".generer_url_ecrire('mots_partout','')."'>
<input type='hidden' name='limit' value='$limit'>
<input type='hidden' name='id_limit' value='$id_limit'>
";

debut_cadre_enfonce('',false,'',_T('motspartout:voir'));
?>
<div class='liste'>
<table border=0 cellspacing=0 cellpadding=3 width="100%">
<tr class='tr_liste'>
<td colspan=2>
<?php
echo _T('motspartout:voir_help');
?>
</td>
</tr>
<tr class='tr_liste'>
<td><button type='submit' name='switch' value='voir'>
<?php echo _T('motspartout:voir'); ?>
</button>
</td>
<td colspan=2>
<input type='checkbox' id='strict' name='strict'/><label for='strict'>
<?php echo _T('motspartout:stricte'); ?>
</label></td>
</tr>
</table></div>
<?php
fin_cadre_enfonce();


 // echo '</form>';

// 	echo '<a name="action"></a><form action="'.generer_url_ecrire('mots_partout','').'#voir">';

  echo '<input type="hidden" name="nom_chose" value="'.$nom_chose.'">';  
  echo '<input type="hidden" name="nb_aff" value="'.$nb_aff.'">';  
//  echo "<input type='hidden' name='id_limit' value='$id_limit'>";
//  echo "<input type='hidden' name='limit' value='$limit'>";
//  for($i=0; $i < count($choses); $i++) {
//	echo "<input type=\"hidden\" name=\"id_choses[]\" value=\"".$choses[$i].'">';
//  }
  
  // les actions et limitations possibles.
if(count($choses)) {
  debut_cadre_enfonce('',false,'',_T('motspartout:action'));
  
  
  echo '<div class=\'liste\'>
<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">';
//ca ne sert à rien, on n'utilise jamais ces choix, on utilise directement avec/sans mais ça pourrait être utile pour gérer tag-machine 
/* echo '<tr class=\'tr_liste\'>
<td><input type=\'radio\' value=\'ajouter\' name="bouton" id=\'ajouter\'><br><label for=\'ajouter\'>'.
	_T('motspartout:ajouter').
	'</label></td>
<td ><input type=\'radio\' value=\'enlever\' name="bouton" id=\'enlever\'><br><label for=\'enlever\'>'.
	_T('motspartout:enlever').
	'</label></td>
</tr>';*/
  ?>
	   <tr class='tr_liste'>
	   <td colspan=2>
<?php
echo _T('motspartout:ajouter_help');
?>
</td>
		   </tr>
	   <tr class='tr_liste'>
	   <td colspan=2><button type='submit' name='switch' value='action'>
		   <?php  echo _T('bouton_valider'); ?>
	   </button></td>
		   </tr>
		   </table>
		   </div>
		   
<?php
		   
  fin_cadre_enfonce();
}
 creer_colonne_droite();
// affichage de mots clefs.
$select = array('*');
$from = array('spip_groupes_mots');
$order = array('titre');
$m_result_groupes = spip_abstract_select($select,$from,'','',$order);

while ($row_groupes = spip_abstract_fetch($m_result_groupes)) {
  $id_groupe = $row_groupes['id_groupe'];
  $titre_groupe = typo($row_groupes['titre']);
  $unseul = $row_groupes['unseul'];
  $acces_admin =  $row_groupes['minirezo'];
  $acces_redacteur = $row_groupes['comite'];

  if($row_groupes[$nom_chose] == 'oui' && (($GLOBALS['connect_statut'] == '1comite' AND $acces_redacteur == 'oui') OR ($GLOBALS['connect_statut'] == '0minirezo' AND $acces_admin == 'oui'))) {
	// Afficher le titre du groupe
	debut_cadre_enfonce("groupe-mot-24.gif", false, '', $titre_groupe);
	
	//
	// Afficher les mots-cles du groupe
	//
	$result = spip_abstract_select(array('*'),
								  array('spip_mots'),
								  array("id_groupe = '$id_groupe'"),
								  '', array('titre'));
	$table = '';
	
	if (spip_abstract_count($result) > 0) {
	  echo "<div class='liste'>";
	  echo "<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">";
	  $i =0;
	  $table[] = array(
					   ' ' => 0,
					   _T('motspartout:avec') => 0,
					   _T('motspartout:sans') => 0
					   );
	  while ($row = spip_abstract_fetch($result)) {
		$i++;
		$vals = '';
		
		$id_mot = $row['id_mot'];
		$titre_mot = $row['titre'];
		
		$s = typo($titre_mot);
		
		$vals["<label for='id_mot$i'>$s</label>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		
		$vals["<input type='checkbox' name='id_mots[]' id='id_mot$i' value='$id_mot'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		
		$vals["<input type='checkbox' name='sans_mots[]' id='sans_mot$i' value='$id_mot'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		$table[] = $vals;
	  }
	  
  }
	$largeurs = array(40, 10, 10);
	$styles = array(
					array('arial11',
						  'diff-deplace',
						  'diff-ajoute'),
					array('arial1',
						  'diff-para-deplace',
						  'diff-para-ajoute'),
					array('arial1',
						  'diff-para-deplace',
						  'diff-para-ajoute')
					);
	md_afficher_liste($largeurs, $table, $styles);
	
	echo "</table>";
	echo "</div>";
	spip_abstract_free($result);
	
	fin_cadre_enfonce();
  }
}
spip_abstract_free($m_result_groupes);


//Milieu

debut_droite();

if(count($warnings) > 0) {
  debut_cadre_relief('',false,'',_T('motspartout:ATTENTION'));
  echo '<div class="liste"><table border=0 cellspacing=0 cellpadding=3 width=\"100%\">';
  $largeurs = array('100%');
  $styles = array( 'arial11');
  afficher_liste($largeurs, $warnings, $styles);
  echo '</table>';
  echo '</div>';
  fin_cadre_relief();
}

// Affichage de toutes les choses (on pourrait imaginer faire une pagination là)
debut_cadre_relief('',false,'document', _T('portfolio'));
if(count($choses) > 0) {
  $function = "afficher_liste_$nom_chose";
  if(function_exists($function)) 
	$function($choses,$nb_aff);
  else {
	afficher_liste_defaut($choses,$nb_aff);
	}	
?>
<!--
<input type="radio" name="selectall" id="all" onclick="selectAll(this.form, 'id_choses[]', 0);"><label for="all">Select All</label>
<input type="radio" name="selectall" id="inverse"  onclick="selectAll(this.form, 'id_choses[]', 1);"><label for="inverse">Inverse All</label>
-->
<?php
} else {
  echo _T('motspartout:pas_de_documents').'.';
}


fin_cadre_relief();
echo '</form>';
?>


<script>
function selectAll(formObj, isInverse) 
{
   for (var i=0;i < formObj.length;i++) 
   {
      fldObj = formObj.elements[i];
      if (fldObj.type == 'checkbox')
      { 
         if(isInverse)
            fldObj.checked = (fldObj.checked) ? false : true;
         else fldObj.checked = true; 
       }
   }
}
</script>

<?php

fin_page();

}


?>

