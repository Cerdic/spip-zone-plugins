<?php 


//	  exec/mots_partout.php
//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
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


define('_DIR_PLUGIN_MOTS_PARTOUT',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/..'))))));

/***********************************************************************/
/* function*/
/***********************************************************************/


function verifier_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
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


//on calcul le style pour ce mot. I.E. on regarde si il est attache à tout ou seulement une partie
function calcul_numeros($array, $search, $total) {
  if(is_array($array))
	$tt = count(array_keys($array,$search));
  else
	return 0;

  if($tt == 0) return 0;
  if($tt < $total) return 1;
  return 2;
}

//liste des mots a droite
function md_afficher_liste($largeurs, $table, $styles = '') {
  global $couleur_claire;
  global $browser_name;
  global $spip_display;
  global $spip_lang_left;

  if (!is_array($table)) return;
  reset($table);
  echo "\n";
  while (list(,$t) = each($table)) {
	if (eregi("msie", $browser_name)) $msover = " onMouseOver=\"changeclass(this,'tr_liste_over');\" onMouseOut=\"changeclass(this,'tr_liste');\"";
	reset($largeurs);
	if ($styles) reset($styles);
	list($texte, $sel) = each($t);
	$style = $largeur = "";
	list(, $largeur) = each($largeurs);
	if ($styles) list(,$style) = each($styles);
	if (!trim($texte)) $texte .= "&nbsp;";
	echo "<ul class='".$style[$sel]."'$msover>";
	echo "<li";
	if ($style)  echo ' class="'.$style[$sel].'"';
	echo ">$texte</li>";
	
	while (list($texte, $sel) = each($t)) {
	  $style = $largeur = "";
	  if ($styles) list(,$style) = each($styles);
	  if (!trim($texte)) $texte .= "&nbsp;";
	  echo "<li";
	  if ($largeur) echo " width=\"$largeur\"";
	  if ($style)  echo ' class="'.$style[$sel].'"';
	  echo ">$texte</li>";
	}
	echo "</ul>\n";
	echo "\n";
  }
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

//genere la liste de IN a partir d'un tableau
function calcul_in($mots) {
  for($i=0; $i < count($mots); $i++) {
	if($i > 0) $to_ret .= ',';
	$to_ret .= $mots[$i];
  }
  return $to_ret;
}

//force a un tableau de int
function secureIntArray($array) {
  $to_return = Array();
  if(is_array($array)) {
	foreach($array as $id) {
	  $to_return[] = intval($id);
	}
  } 
  return $to_return;
}

// transfert la variable POST d'un tableau (19 => 'avec', 20=>'voir') en 4 tableaux avec=(19) voir=(20)
function splitArrayIds($array) {
  $voir = Array();
  $cacher = Array();
  $ajouter = Array();
  $enlever = Array();
  if(is_array($array)) {
    foreach($array as $id_mot => $action) {
      $id_mot = intval($id_mot);
      if($id_mot > 0) {
        switch(addslashes($action)) {
		  case 'avec': 
			$ajouter[] = $id_mot;
		  case 'voir':
			$voir[] = $id_mot;
			break;
		  case 'sans':
			$enlever[] = $id_mot;
			break;
		  case 'cacher':
			$cacher[] = $id_mot;
            break; 

        }
      }
    }
  }
  return array($voir, $cacher, $ajouter, $enlever);
}

//====================l'affichage par defaut======================================

function afficher_liste_defaut($choses) {
  echo '<table>';
  $i = 0;
  foreach($choses as $id_chose) {
	$i++;
	echo "<td><tr><input type='checkbox' name='choses[]' value='$id_chose' id='id_chose$i'/></tr><tr> <label for='id_chose$i'>$id_chose</label></tr></td>";
  }
  echo '</table>';
}

//------------------------la fonction qui fait tout-----------------------------------

function exec_mots_partout() {

  include(_DIR_PLUGIN_MOTS_PARTOUT."/mots_partout_choses.php");
  include_ecrire ("inc_presentation");
  include_ecrire ("inc_documents");
  include_ecrire ("inc_abstract_sql");
  include_ecrire ("inc_objet");

  /***********************************************************************/
/* PREFIXE*/
  /***********************************************************************/
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
  
  
  /***********************************************************************/
  /* récuperation de la chose sur laquelle on travaille*/
  /***********************************************************************/

  $nom_chose = addslashes($_POST['nom_chose']);
  if(!isset($choses_possibles[$nom_chose])) {
	list($nom_chose,) = each($choses_possibles);
	reset($choses_possibles);
  }
  $id_chose = $choses_possibles[$nom_chose]['id_chose'];
  $table_principale = $choses_possibles[$nom_chose]['table_principale'];
  $table_auth = $choses_possibles[$nom_chose]['table_auth'];
  $tables_limite = $choses_possibles[$nom_chose]['tables_limite'];


  /***********************************************************************/
  /* affichage*/
  /***********************************************************************/

  debut_page('&laquo; '._T('motspartout:titre_page').' &raquo;', 'documents', 'mots', '', _DIR_PLUGIN_MOTS_PARTOUT."/mots_partout.css");
  echo'</script>

		<script type="text/javascript" src="'. _DIR_PLUGIN_MOTS_PARTOUT.'/javascript/prototype.js"></script>
		<script type="text/javascript" src="'. _DIR_PLUGIN_MOTS_PARTOUT.'/javascript/behaviour.js"></script>
		<script type="text/javascript" src="'. _DIR_PLUGIN_MOTS_PARTOUT.'/javascript/effects.js"></script>
		<script type="text/javascript" src="'. _DIR_PLUGIN_MOTS_PARTOUT.'/javascript/MultiStateRadio.js"></script>
		<script type="text/javascript">

		MultiStateRadio.apply(\'.liste ul\');

  </script>';

	  echo '<br><br><center>';
  gros_titre(_T('motspartout:titre_page'));
  echo '</center>';

  //Colonne de gauche
  debut_gauche();

  echo '<form method="post" action="'.generer_url_ecrire('mots_partout','').'">';


  // choix de la chose sur laquelle on veut ajouter des mots
  debut_cadre_enfonce('',false,'',_T('motspartout:choses'));
  echo '<div class=\'liste\'>
<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
<tr class=\'tr_liste\'>
<td colspan=2><select name="nom_chose">';
  $tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));
  foreach($choses_possibles as $cho => $m) {
	  if($tables_installees[$cho]) {
		echo "<option value=\"$cho\"".(($cho == $nom_chose)?'selected':'').'>'._T($m['titre_chose']).'</option>';
	  }
  }
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
  echo "<td><input type='text' size='3' name='identifiant_limit' value='$id_limit'></td></tr>";
  echo '<tr class=\'tr_liste\'>';
  echo "
	<td>
	<button type='submit' name='switch' value='chose'>";
  echo _T('motspartout:voir');
  echo"    </button>
	</td>";

	echo '<td colspan=2><label for="nb_aff">'._T('motspartout:par').':</label><select name="nb_aff">';

  for($nb = 10;$nb<count($choses);$nb=$nb+10)
					 echo "<option value=\"$nb\"".(($nb == $nb_aff)?'selected="true"':'').">$nb</option>";

  echo '</select></td>';

					 echo "	</table></div>";
  fin_cadre_enfonce();

  $redirect = generer_url_ecrire('mots_partout',"limit=$limit&identifiant_limit=$id_limit&nb_aff=$nb_aff");

  echo "</form><form method='post' action='".generer_url_action('mots_partout',"redirect=$redirect")."'>";
  
  echo '<input type="hidden" name="nom_chose" value="'.$nom_chose.'">'; 
  
  // les actions et limitations possibles.
  if(count($choses)) {
	debut_cadre_enfonce('',false,'',_T('motspartout:action'));
	
	
	echo '<div class=\'liste\'>
		  <table border=0 cellspacing=0 cellpadding=3 width=\'100%\'>
     	   <tr class=\'tr_liste\'>
          <td colspan=2>';
	echo _T('motspartout:action_help',array('chose' => $nom_chose));
	echo "</td>
		   </tr>
	   <tr class='tr_liste'>
	   <td><button type='submit' name='switch' value='action'>";
	echo _T('bouton_valider');
	echo "	   </button></td>
<td>
(<input type='checkbox' id='strict' name='strict'/>
<label for='strict'>selection".
_T('motspartout:stricte').
"?)</label></td>
		   </tr>
		   </table>
		   </div>";
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
		$i =0;
		while ($row = spip_abstract_fetch($result)) {
		  $vals = '';
		  
		  $id_mot = $row['id_mot'];
		  $titre_mot = $row['titre'];
		  
		  $s = typo($titre_mot);
		  
		  $vals["$s"] = calcul_numeros($show_mots,$id_mot,count($choses));
		  
		  $vals["<label for='id_mot".$id_mot."_vide'>"._T('motspartout:action')."?</label><input type='radio' id='id_mot".$id_mot."_vide' checked='true'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		  
		  $vals["<label for='id_mot".$id_mot."_voir'>"._T('motspartout:voir')."</label><input type='radio' name='mots[$id_mot]' id='id_mot".$id_mot."_voir' value='voir'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		  
		  $vals["<label for='id_mot".$id_mot."_cacher'>"._T('motspartout:cacher')."</label><input type='radio' name='mots[$id_mot]' id='id_mot".$id_mot."_cacher' value='cacher'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		  
		  $vals["<label for='id_mot".$id_mot."_avec'>"._T('motspartout:ajouter')."</label><input type='radio' name='mots[$id_mot]' id='id_mot".$id_mot."_avec' value='avec'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		  
		  $vals["<label for='id_mot".$id_mot."_sans'>"._T('motspartout:enlever')."</label><input type='radio' name='mots[$id_mot]' id='id_mot".$id_mot."_sans' value='sans'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		  $table[] = $vals;
		}
		
	  }
	  $largeurs = array(40, 10, 10);
	  $styles = array(
					  array('arial11',
							'partie arial11',
							'avec arial11'),
					  array('arial1',
							'partie arial1',
							'avec arial1'),
					  array('arial1',
							'partie arial1',
							'avec arial1'),
					  array('arial1',
							'partie arial1',
							'avec arial1'),
					  array('arial1',
							'partie arial1',
							'avec arial1'),
					  array('arial1',
							'partie arial1',
							'avec arial1')
					  );
	  md_afficher_liste($largeurs, $table, $styles);
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
	echo "<!--
<input type=\"radio\" name=\"selectall\" id=\"all\" onclick=\"selectAll(this.form, 'choses[]', 0);\"><label for=\"all\">Select All</label>
<input  type=\"radio\" name=\"selectall\" id=\"inverse\"  onclick=\"selectAll(this.form, 'choses[]', 1);\"><label for=\"inverse\">Inverse All</label>
-->";
  } else {
	echo _T('motspartout:pas_de_documents').'.';
  }
  
  fin_cadre_relief();
  echo '</form>
<script>
function selectAll(formObj, isInverse) 
{
   for (var i=0;i < formObj.length;i++) 
   {
      fldObj = formObj.elements[i];
      if (fldObj.type == \'checkbox\')
      { 
         if(isInverse)
            fldObj.checked = (fldObj.checked) ? false : true;
         else fldObj.checked = true; 
       }
   }
}
</script>';
  
  fin_page();
  
}
?>

