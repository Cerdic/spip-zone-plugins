<?php 

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin Mots-Partout                                                    *
 *                                                                         *
 *  Copyright (c) 2006-2008                                                *
 *  Pierre ANDREWS, Yoann Nogues, Emmanuel Saint-James                     *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 *    This program is free software; you can redistribute it and/or modify *
 *    it under the terms of the GNU General Public License as published by * 
 *    the Free Software Foundation.                                        *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once(_DIR_PLUGIN_MOTSPARTOUT."/mots_partout_choses.php");
include_spip('inc/afficher_objets');
include_spip("inc/presentation");
include_spip("inc/documents");
include_spip("base/abstract_sql");

/***********************************************************************/
/* function*/
/***********************************************************************/


function verifier_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
}

function verifier_auteur($table, $id_objet, $id) {
  global $connect_id_auteur;

  return sql_fetsel('id_auteur', $table, "id_auteur = $connect_id_auteur AND $id_objet = $id");
}


// on calcule le style pour ce mot. I.E. 
// on regarde s'il est attache a tout ou seulement une partie
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

  global $browser_name;

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

//
// Afficher les mots-cles d'un groupe
//
function mots_partout_affiche($id_groupe, $titre_groupe, $choses, $show_mots)
{
	$table = sql_allfetsel('id_mot, titre','spip_mots',"id_groupe=$id_groupe", '', 'titre');
	foreach($table as $k => $row) {
		$id_mot = $row['id_mot'];
		$show = calcul_numeros($show_mots,$id_mot,count($choses));
		$sel = '<select id="id_mot'.$id_mot.'" name="mots['.$id_mot.']"><option value="">--'.
		_T('motspartout:action').'--</option><option value="voir">'.
		_T('motspartout:voir').'</option><option value="cacher">'.
		_T('motspartout:cacher').'</option><option value="avec">'.
		_T('motspartout:ajouter').'</option><option value="sans">'.
		  _T('motspartout:enlever').'</option></select>';
		$table[$k] = array(typo($row['titre']) => $show, $sel=>$show);
	}
	if (!$table) return '';
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

	echo debut_cadre_enfonce("groupe-mot-24.gif", true, '', typo($titre_groupe));
	echo "<div class='liste'>";
	md_afficher_liste($largeurs, $table, $styles);
	echo "</div>";
	echo fin_cadre_enfonce(true);
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
global $choses_possibles;
  
  /***********************************************************************/
  /* recuperation de la chose sur laquelle on travaille*/
  /***********************************************************************/

  $nom_chose = addslashes(_request('nom_chose'));
  if(!isset($choses_possibles[$nom_chose])) {
	list($nom_chose,) = each($choses_possibles);
	reset($choses_possibles);
  }
  $id_chose = $choses_possibles[$nom_chose]['id_chose'];
  $table_principale = $choses_possibles[$nom_chose]['table_principale'];
  $table_auth = $choses_possibles[$nom_chose]['table_auth'];
  $tables_limite = $choses_possibles[$nom_chose]['tables_limite'];

  list($mots_voir, $mots_cacher, $mots_ajouter, $mots_enlever) = splitArrayIds(_request('mots'));
  $choses = secureIntArray(_request('choses'));

  $limit =  addslashes(_request('limit'));
  if($limit == '') $limit = 'rien';
  $id_limit =  intval(_request('identifiant_limit'));
  if($id_limit < 1) $id_limit = 0;
  $nb_aff = intval(_request('nb_aff'));
  if($nb_aff < 1) $nb_aff = 20;
  $switch = addslashes(_request('switch'));
  if($switch == '') $switch = 'voir';
  $strict = intval(_request('strict'));

  /**********************************************************************/
  /* recherche des choses.*/
  /***********************************************************************/
  
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
	  if(count($mots_voir) > 0) {
		$from[1] = "spip_mots_$nom_chose as table_temp";
		$where[1] = "table_temp.$id_chose = main.$id_chose";
		$where[] = sql_in("table_temp.id_mot", $mots_voir);
		if($strict) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = ('tot DESC');
		}
	  }
	  if($mots_cacher) {
		$from[1] = "spip_mots_$nom_chose as table_temp";
		$where[1] = "table_temp.$id_chose = main.$id_chose";
		$where[] = sql_in("table_temp.id_mot", $mots_cacher, 'NOT');
		if($strict) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = ('tot DESC');
		}
	  }	
	} else if((count($mots_voir) > 0)||($mots_cacher)){
	  if(count($mots_voir) > 0) {
		$from[0] = "spip_mots_$nom_chose as main";
		$where[] = sql_in("main.id_mot", $mots_voir);
		if($strict) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = ('tot DESC');
		}
	  }
	  if($mots_cacher) {
		$from[0] = "spip_mots_$nom_chose as main";
		$where[] = sql_in("main.id_mot", $mots_cacher,'NOT');
		if($strict) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = ('tot DESC');
		}
	  }
	} else {
	  $from[] = "$table_principale as main"; 
	}
	$select = join(',',$select);
	$from = join(',',$from);
	$where = join(',',$where);
	$res=sql_select($select,$from,$where,$group,$order);

	$choses = array();
	$in_sans = $mots_cacher ? sql_in('id_mot', $mots_cacher) : '';
	$do = (!isset($table_auth) || (isset($table_auth) && verifier_admin()));
	while ($row = sql_fetch($res)) {
	  if($do OR verifier_auteur($table_auth,$id_chose,$row[$id_chose])) {
		if($mots_cacher) {
		  $test = sql_countsel("spip_mots_$nom_chose", $in_sans . " AND $id_chose = ".$row[$id_chose]);
		  if($test) continue;
		}
		if(count($mots_voir) > 0 && $strict) {
		  if($row['tot'] >= count($mots_voir)) {
			$choses[] = $row[$id_chose];
		  } else {
			break;
		  }
		} else {
		  $choses[] = $row[$id_chose];
		}
	  }
	}
	sql_free($res);
  }

  if(count($choses) > 0) {
	$debut_aff = _request('t_debut');

	$show_mots = array_map('array_shift', sql_allfetsel("spip_mots_$nom_chose.id_mot", "spip_mots_$nom_chose", sql_in("spip_mots_$nom_chose.$id_chose", array_slice($choses,$debut_aff,$nb_aff))));
  }


  /***********************************************************************/
  /* affichage*/
  /***********************************************************************/

  $commencer_page = charger_fonction('commencer_page', 'inc');
  $l = $commencer_page('&laquo; '._T('motspartout:titre_page').' &raquo;', 'documents', 'mots');
  $css = '<link rel="stylesheet" type="text/css" href="'
    . url_absolue( _DIR_PLUGIN_MOTSPARTOUT."/mots_partout.css")
    . '" id="cssprivee" />'  . "\n";
  echo str_replace('</head>', "$css</head>", $l);
  echo '<br><br><center>';
  echo gros_titre(_T('motspartout:titre_page'), '', false);
  echo '</center>';

  //Colonne de gauche
  echo debut_gauche('', true);

  $tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));
  if (!$tables_installees) {
    $tables_installees=array(
	"articles"=>true,
	"rubriques"=>true,
	"breves"=>true,
	"syndic"=>true,
#	"auteurs" => true,
#	"messages"=>true,
#	'documents'=>true,
#	'groupes_mots'=>true
	);
	ecrire_meta('MotsPartout:tables_installees',serialize($tables_installees));
  }

  echo mots_partout_choix($choses, $choses_possibles, $id_limit, $limit, $nb_aff, $nom_chose, $tables_installees, $tables_limite);

  $redirect = generer_url_ecrire('mots_partout',"limit=$limit&identifiant_limit=$id_limit&nb_aff=$nb_aff");

  echo "<form method='post' action='".generer_url_action('mots_partout',"redirect=$redirect")."'>";
  
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
	echo fin_cadre_enfonce(true);
  }
  creer_colonne_droite();

  $where = "tables_liees REGEXP '(^|,)$nom_chose($|,)'";

  $index = ($GLOBALS['connect_statut'] == '0minirezo')
      ? 'minirezo'
      : (($GLOBALS['connect_statut'] == '1comite') ? 'comite' : '');

  if ($index) $where .= " AND ($index='oui')";

  $q = sql_select('*','spip_groupes_mots',$where,'','titre');
  while ($r = sql_fetch($q))
	mots_partout_affiche($r['id_groupe'], $r['titre'], $choses, $show_mots);

  sql_free($q);

  //Milieu

  echo debut_droite('',true);

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

  // Affichage de toutes les choses (on pourrait imaginer faire une pagination
  debut_cadre_relief('',false,'document', _T('portfolio'));
  if(count($choses) > 0) {
  	
	$trouver_table = charger_fonction('trouver_table', 'base');
	$afficher_objets = charger_fonction('afficher_objets','inc');
	$desc = $trouver_table($nom_chose);
	$nom_choe = substr($nom_chose, 0,-1); # enlever le 's' final
	if (charger_fonction("afficher_{$nom_choe}s", 'inc', true)
	OR function_exists('afficher_' . $nom_choe . 's_boucle')) {
		if (!function_exists($f = 'formater_' . $nom_chose . '_mots'))				$f='';
		echo $afficher_objets($nom_choe, $nom_chose,
			array('SELECT' => '*', 
				'FROM' => $desc['table'], 
				'WHERE' => sql_in($desc['key']['PRIMARY KEY'], $choses)),
		       $f);
	} else afficher_liste_defaut($choses,$nb_aff);

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

// choix de la chose sur laquelle on veut ajouter des mots

function mots_partout_choix($choses, $choses_possibles, $id_limit, $limit, $nb_aff, $nom_chose, $tables_installees, $tables_limite)
{
  $res = debut_cadre_enfonce('',true,'',_T('motspartout:choses'));
  $res .= '<div class=\'liste\'>
<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
<tr class=\'tr_liste\'>
<td colspan=2><select name="nom_chose">';

  foreach($choses_possibles as $cho => $m) {
	  if($tables_installees[$cho]) {
		$res .= "<option value=\"$cho\"".(($cho == $nom_chose)?'selected':'').'>'._T($m['titre_chose']).'</option>';
	  }
  }

  $res .= '</select></td>'
	.'</tr>
<tr class=\'tr_liste\'><td colspan=2>'.
	_T('motspartout:limite').
	':</td></tr>'
	.'<tr class=\'tr_liste\'><td><select name="limit">
<option value="rien" selected="true">'.
	_T('motspartout:aucune').
	'</option>';
  
  foreach($tables_limite as $t => $m)
	$res .= "<option value=\"$t\"".(($t == $limit)?'selected':'').">$t</option>";
  
  $res .= '</select></td>'
	."<td><input type='text' size='3' name='identifiant_limit' value='$id_limit'></td></tr>"
	.'<tr class=\'tr_liste\'>'
	."<td>
	<button type='submit' name='switch' value='chose'>"
	._T('motspartout:voir')
	."    </button>
	</td>";

  if (count($choses)>0) {
	$res .= '<td colspan=2><label for="nb_aff">'._T('motspartout:par').':</label><select name="nb_aff">';
	for($nb = 10;$nb<count($choses);$nb=$nb+10)
		$res .="<option value=\"$nb\"".(($nb == $nb_aff)?'selected="true"':'').">$nb</option>";
	$res .= '</select></td>';
  } else $res .= '<td colspan=2></td>';

  $res .= "	</table></div>"
	.fin_cadre_enfonce(true);

  return generer_form_ecrire('mots_partout', $res, " method='get'");
}

?>
