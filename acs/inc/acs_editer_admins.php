<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

// from spip 1.9.2.c, 1.9.2d

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');
include_spip('inc/acs_groups');

// L'ajout d'un admin se fait par mini-navigateur dans la fourchette:
define('_SPIP_SELECT_MIN_ADMINS', 10); // en dessous: balise Select
define('_SPIP_SELECT_MAX_ADMINS', 100); // au-dessus: saisie + return

if (!is_callable('sql_count')) { // spip < 1.9.3
  function sql_count($res) {
    return spip_num_rows($res);
  }
}
if (!is_callable('sql_fetch')) { // spip < 1.9.3
  function sql_fetch($res) {
    return spip_fetch_array($res);
  }
}

function acs_ajax_action_greffe($div, $id, $res) {
  global $spip_version_code;

  if($spip_version_code >= 1.93)
    return ajax_action_greffe("acs_editer_admins", $id, $res);
  else
    return ajax_action_greffe("acs_editer_admins-$id", $res);
}

function acs_bloc_depliable($flag, $id, $contenu) {
  if (is_callable('debut_block_depliable')) {
    return debut_block_depliable($flag, $id).$contenu.fin_block();
  }
  else { // spip < 1.9.3
    return ($flag === 'ajax' ? debut_block_visible("admins$type$id") : debut_block_invisible("admins$type$id")).
    $contenu.
    fin_block();
  }
}
function acs_bouton_bloc_depliable($titre_boite, $flag, $id) {
  if (is_callable('bouton_block_depliable')) {
    $r = bouton_block_depliable($titre_boite, $flag ?($flag === 'ajax'):1, $id);
  }
  else { // spip < 1.9.3
    $r = (!$flag
       ? ''
       : (($flag === 'ajax')
      ? bouton_block_visible("admins$type$id")
      : bouton_block_invisible("admins$type$id")))
  . $titre_boite;
  }
  return '<div style="text-align:'.$GLOBALS['spip_lang_left'].'">'.$r.'</div>';
}



// doc: acs_editer_admins($type, $id, $flag_editable, $cherche_auteur, $ids, $titre_boite = NULL, $script_edit_objet = NULL)
// (ça ressemble à http://doc.spip.org/@inc_editer_auteurs_dist)
// $id est l'id de l'objet à éditer. pour ACS, c'est sans importance puisque l'objet est ACS lui-même
// Cette usine à gaz spip-ajax sert seulement à affecter à ACS_ADMINS la liste des admins ACS autorisés ...
function inc_acs_editer_admins($type, $id, $flag, $cherche_auteur, $ids, $titre_boite = NULL, $script_edit_objet = NULL,  $icon="auteur-24.gif") {
	global $options;

  acs_log("inc_acs_editer_admins($type, $id, $flag, $cherche_auteur, $ids, $titre_boite, $script_edit_objet)");

	$arg_ajax = "&id_{$type}=$id";
	if ($script_edit_objet===NULL) $script_edit_objet = $type.'s';
	if ($titre_boite===NULL)
		$titre_boite = _T('texte_auteurs'). aide("artauteurs");
	else
		$arg_ajax.= "&titre=".urlencode($titre_boite);
	$aff_les_admins = afficher_admins_objet($type, $id, $flag, "", $script_edit_objet, $arg_ajax);

	//if ($flag AND $options == 'avancees') {
		$futurs = ajouter_admins_objet($type, $id, "",$script_edit_objet, $arg_ajax);
	//} else $futurs = '';

	return editer_admins_objet($type, $id, $flag, $cherche_auteur, $ids, $aff_les_admins, $futurs, $titre_boite,$script_edit_objet, $arg_ajax, $icon, $extras);
}

// http://doc.spip.org/@editer_admins_objet
function editer_admins_objet($type, $id, $flag, $cherche_auteur, $ids, $les_auteurs, $futurs, $titre_boite, $script_edit_objet, $arg_ajax, $icon)
{
	global $spip_lang_left, $spip_lang_right, $options;

  acs_log("inc_acs_editer_admins_objet($type, $id, $flag, cherche_auteur, $ids, les_auteurs, futurs, $titre_boite, $script_edit_objet, $arg_ajax)");

//
// Ajouter un admin
//
	$res = '';
	if ($flag) {
		$res = "<div style='float:$spip_lang_right; width:280px;position:relative;display:inline;'>"
		. $futurs
		."</div>\n"
		. $res;
	}
  $bouton = acs_bouton_bloc_depliable($titre_boite, $flag, "admins$type$id");
  if ($id > 0){
    $formpages = '<form name="acs_pages_'.$id.'" action="?exec=acs" method="post"><input type="hidden" name="onglet" value="adm"><input type="hidden" name="changer_pages" value="oui"><input type="hidden" name="group" value="'.$id.'"><table style="width:100%;" cellpadding="2px"><tr style="vertical-align: top;"><td style="width: 20%; text-align:'.$GLOBALS['spip_lang_right'].'">'._T('acs:locked_pages').' : </td><td style="width:75%"> <input name="pages" type="text" class="formc" value="'.acs_pages($id).'" style="width:100%" /> </td><td style="text-align:'.$GLOBALS['spip_lang_right'].'"> <input type="submit"  name="'._T('bouton_valider').
        '" value="'._T('bouton_valider').'" class="fondo"> </td></tr></table></form>';
  }
  else
    $formpages = _T('acs:locked_pages').' : '.implode(', ', $GLOBALS['ACS_ENFER']);
  $formpages = '<div>'.$formpages.'</div>';
	$res = '<div style="text-align:'.$GLOBALS['spip_lang_left'].'"><div>&nbsp;</div>' // pour placer le gif patienteur
	. debut_cadre_enfonce($icon, true, "", $bouton)
	. $les_auteurs
  . acs_bloc_depliable(($flag === 'ajax'), "admins$type$id", $formpages.$res)
	. fin_cadre_enfonce(true)
. '</div>';
	return acs_ajax_action_greffe("acs_editer_admins", $id, $res);
}

// http://doc.spip.org/@determiner_admins_objet
function determiner_admins_objet($type, $id, $cond='', $limit='')
{
	$les_auteurs = array();
	if (!preg_match(',^[a-z]*$,',$type)) return $les_auteurs;

  if ($id == 0) {
    // Add author 1 as default ACS admin
    if (!isset($GLOBALS['meta']['ACS_ADMINS']) || !$GLOBALS['meta']['ACS_ADMINS']) {
      ecrire_meta('ACS_ADMINS', '1');
      ecrire_metas();
    }
    $ids = $GLOBALS['meta']['ACS_ADMINS'];
  }
  else {
    $ids = implode(',',acs_members(acs_grid($id)));
  }

	if ($ids) $result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE id_auteur IN ($ids) and statut='0minirezo'"
	. ($cond ? " AND $cond" : '')
	. ($limit? " LIMIT $limit": '')
	);

	return $result;
}
// http://doc.spip.org/@determiner_non_auteurs
function determiner_non_admins($type, $id, $cond_les_auteurs, $order)
{
	$cond = '';
	$res = determiner_admins_objet($type, $id, $cond_les_auteurs);
	if (sql_count($res)<200){ // probleme de performance au dela, on ne filtre plus
		while ($row = sql_fetch($res))
			$cond .= ",".$row['id_auteur'];
	}
	if (strlen($cond))
		$cond = "id_auteur NOT IN (" . substr($cond,1) . ') AND ';
	return spip_query("SELECT id_auteur, nom, email, statut FROM spip_auteurs WHERE $cond statut='0minirezo'");
}

// http://doc.spip.org/@afficher_admins_objet
function afficher_admins_objet($type, $id, $flag_editable, $cond_les_auteurs, $script_edit, $arg_ajax)
{
	global $connect_statut, $options,$connect_id_auteur, $spip_display;

  acs_log("inc/acs_editer_admins: afficher_admins_objet(type=$type, id=$id, flag_editable=$flag_editable, cond_les_auteurs=$cond_les_auteurs, script_edit=$script_edit, arg_ajax=$arg_ajax)");

	$les_auteurs = array();
	if (!preg_match(',^[a-z]*$,',$type)) return $les_auteurs;

	$result = determiner_admins_objet($type,$id,$cond_les_auteurs);
	$cpt = sql_count($result);

  if (!defined('_TRANCHES'))
    define('_TRANCHES', 10); // Compat 2.0
    
	$tmp_var = "acs_editer_admins-$id";
	$nb_aff = floor(1.5 * _TRANCHES);
	if ($cpt > $nb_aff) {
		$nb_aff = _TRANCHES;
		$tranches = afficher_tranches_requete($cpt, $tmp_var, generer_url_ecrire('acs_editer_admins',$arg_ajax), $nb_aff);
	} else $tranches = '';

	$deb_aff = _request($tmp_var);
	$deb_aff = ($deb_aff !== NULL ? intval($deb_aff) : 0);

	$limit = (($deb_aff < 0) ? '' : "$deb_aff, $nb_aff");
	$result = determiner_admins_objet($type,$id,$cond_les_auteurs,$limit);

	// charger ici meme si ps d'auteurs
	// car inc_formater_auteur peut aussi redefinir determiner_non_auteurs qui sert plus loin
	if (!$formater_auteur = charger_fonction("formater_auteur_$type", 'inc',true))
		$formater_auteur = charger_fonction('formater_auteur', 'inc');

	if (!sql_count($result)) return '';

	$table = array();

	while ($row = sql_fetch($result)) {
		$id_auteur = $row['id_auteur'];
		$vals = $formater_auteur($id_auteur);

    $gr = ($id == 0) ? _T('acs:acs') : acs_grid($id);
		if ($flag_editable AND ($connect_id_auteur != $id_auteur OR $connect_statut == '0minirezo')) {
			$vals[] =  ajax_action_auteur('acs_editer_admins', "$id,$type,-$id_auteur", $script_edit, "id_{$type}=$id", ((($id_auteur != 1) || ($id > 0)) ? array(_T('acs:lien_retirer_admin')."&nbsp;".$gr."&nbsp;".http_img_pack('croix-rouge.gif', "X", " class='puce' style='vertical-align: bottom;'")): array() ),$arg_ajax);
		} else  $vals[] = "";
		$table[] = $vals;
	}

	$largeurs = array('14', '', '', '', '', '');
	$styles = array('arial11', 'arial2', 'arial11', 'arial11', 'arial11', 'arial1');

	$t = afficher_liste($largeurs, $table, $styles);
	if ($spip_display != 4)
	  $t = $tranches
	  	. "<table width='100%' cellpadding='3' cellspacing='0' border='0'>"
	    . $t
	    . "</table>";
	return "<div class='liste'>$t</div>\n";
}


// http://doc.spip.org/@ajouter_auteurs_objet
function ajouter_admins_objet($type, $id, $cond_les_auteurs,$script_edit, $arg_ajax)
{
	if (!$determiner_non_admins = charger_fonction('determiner_non_admins'.$type,'inc',true))
		$determiner_non_admins = 'determiner_non_admins';
	$query = $determiner_non_admins($type, $id, $cond_les_auteurs, "statut, nom");

	if (!$num = sql_count($query)) return '';

	$js = "findObj_forcer('valider_ajouter_admin_$id').style.visibility='visible';";

	$text = "<span class='verdana1'><b>"
	. _T('titre_cadre_ajouter_auteur')
	. "</b></span>\n"; // spip translation : less to do ;-)

  $sel = '';
	if ($num <= _SPIP_SELECT_MIN_ADMINS){
		$sel .= "$text<select name='nouv_admin_$id' size='1' style='width:150px;' class='fondl' onchange=\"$js\">" .
		   objet_admin_select($query) .
		   "</select>";
		$clic = _T('bouton_ajouter');
	} else if  ((_SPIP_AJAX < 1) OR ($num >= _SPIP_SELECT_MAX_ADMINS)) {
      $sel .= "$text <input type='text' name='cherche_admin' onclick=\"$js\" class='fondl' value='' size='20' />";
		  $clic = _T('bouton_chercher');
	} else {
	    $sel .= selecteur_admin_ajax($type, $id, $js, $text);
	    $clic = _T('bouton_ajouter');
	}

	return ajax_action_post('acs_editer_admins', "$id,$type", $script_edit, "id_{$type}=$id", $sel, $clic, "class='fondo visible_au_chargement' id='valider_ajouter_admin_$id'", "", $arg_ajax);
}

if (!is_callable('afficher_tranches_requete')) {
  // http://doc.spip.org/@afficher_tranches_requete from Spip 1.9.2d (Deleted from 2.0)
  function afficher_tranches_requete($num_rows, $tmp_var, $url='', $nb_aff = 10, $old_arg=NULL) {
    static $ancre = 0;
    global $browser_name, $spip_lang_right, $spip_display;
    if ($old_arg!==NULL){ // eviter de casser la compat des vieux appels $cols_span ayant disparu ...
      $tmp_var = $url;    $url = $nb_aff; $nb_aff=$old_arg;
    }
  
    $deb_aff = intval(_request($tmp_var));
    $ancre++;
    $self = self();
    $ie_style = ($browser_name == "MSIE") ? "height:1%" : '';
  
    $texte = "\n<div style='position: relative;$ie_style; background-color: #dddddd; border-bottom: 1px solid #444444; padding: 2px;' class='arial1' id='a$ancre'>";
    $on ='';
  
    for ($i = 0; $i < $num_rows; $i += $nb_aff){
      $deb = $i + 1;
      $fin = $i + $nb_aff;
      if ($fin > $num_rows) $fin = $num_rows;
      if ($deb > 1) $texte .= " |\n";
      if ($deb_aff + 1 >= $deb AND $deb_aff + 1 <= $fin) {
        $texte .= "<b>$deb</b>";
      }
      else {
        $script = parametre_url($self, $tmp_var, $deb-1);
        if ($url) {
          $on = "\nonclick=\"return charger_id_url('"
          . $url
          . "&amp;"
          . $tmp_var
          . '='
          . ($deb-1)
          . "','"
          . $tmp_var
          . '\');"';
        }
        $texte .= "<a href=\"$script#a$ancre\"$on>$deb</a>";
      }
    }
  
    $style = " class='arial2' style='border-bottom: 1px solid #444444; position: absolute; top: 1px; $spip_lang_right: 15px;'";
  
    $script = parametre_url($self, $tmp_var, -1);
    if ($url) {
          $on = "\nonclick=\"return charger_id_url('"
          . $url
          . "&amp;"
          . $tmp_var
          . "=-1','"
          . $tmp_var
          . '\');"';
    }
    $l = htmlentities(_T('lien_tout_afficher'));
    $texte .= "<a$style\nhref=\"$script#a$ancre\"$on><img\nsrc='". _DIR_IMG_PACK . "plus.gif' title=\"$l\" alt=\"$l\" /></a>";
  
  
    $texte .= "</div>\n";
  
    return $texte;
  }
  // http://doc.spip.org/@afficher_liste
  function afficher_liste($largeurs, $table, $styles = '') {
    global $spip_display;
  
    if (!is_array($table)) return "";
  
    if ($spip_display != 4) {
      $res = '';
      foreach ($table as $t) {
        $res .= afficher_liste_display_neq4($largeurs, $t, $styles);
      }
    } else {
      $res = "\n<ul style='text-align: $spip_lang_left; background-color: white;'>";
      foreach ($table as $t) {
        $res .= afficher_liste_display_eq4($largeurs, $t, $styles);
      }
      $res .= "\n</ul>";
    }
  
    return $res;
  }
  
  // http://doc.spip.org/@afficher_liste_display_neq4
  function afficher_liste_display_neq4($largeurs, $t, $styles = '') {
    global $spip_lang_left,$browser_name;
    if (!is_array($t) or !count($t)) return "";
  
    $evt = (preg_match(",msie,i", $browser_name) ? " onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"" :'');
  
    reset($largeurs);
    if ($styles) reset($styles);
    $res ='';
    while (list(, $texte) = each($t)) {
      $style = $largeur = "";
      list(, $largeur) = each($largeurs);
      if ($styles) list(, $style) = each($styles);
      if (!trim($texte)) $texte .= "&nbsp;";
      $res .= "\n<td" .
        ($largeur ? (" style='width: $largeur" ."px;'") : '') .
        ($style ? " class=\"$style\"" : '') .
        ">" . lignes_longues($texte) . "\n</td>";
    }
  
    return "\n<tr class='tr_liste'$evt>$res</tr>";
  }
  
  // http://doc.spip.org/@afficher_liste_display_eq4
  function afficher_liste_display_eq4($largeurs, $t, $styles = '') {
    global $spip_lang_left;
    if (!is_array($t) or !count($t)) return "";
  
    $res = "\n<li>";
    reset($largeurs);
    if ($styles) reset($styles);
    while (list(, $texte) = each($t)) {
      $style = $largeur = "";
      list(, $largeur) = each($largeurs);
      if (!$largeur) $res .= $texte." ";
    }
    $res .= "</li>\n";
    return $res;
  }
}
// http://doc.spip.org/@objet_auteur_select
function objet_admin_select($result)
{
	global $couleur_claire, $connect_statut ;

	$statut_old = $premiere_old = $res = '';

	while ($row = spip_fetch_array($result)) {
		$id_auteur = $row["id_auteur"];
		$nom = $row["nom"];
		$email = $row["email"];
		$statut = $row["statut"];

		$statut=str_replace("0minirezo", _T('info_administrateurs'), $statut);
		$statut=str_replace("1comite", _T('info_redacteurs'), $statut);
		$statut=str_replace("6visiteur", _T('info_visiteurs'), $statut);

		$premiere = strtoupper(substr(trim($nom), 0, 1));

		if ($connect_statut != '0minirezo')
			if ($p = strpos($email, '@'))
				  $email = substr($email, 0, $p).'@...';
		if ($email)
			$email = " ($email)";

		if ($statut != $statut_old) {
			$res .= "\n<option value=\"x\" />";
			$res .= "\n<option value=\"x\" style='background-color: $couleur_claire;'> $statut</option>";
		}

		if ($premiere != $premiere_old AND ($statut != _T('info_administrateurs') OR !$premiere_old))
			$res .= "\n<option value=\"x\" />";

		$res .= "\n<option value=\"$id_auteur\">&nbsp;&nbsp;&nbsp;&nbsp;" . supprimer_tags(couper(typo("$nom$email"), 40)) . '</option>';
		$statut_old = $statut;
		$premiere_old = $premiere;
	}

	return $res;
}

// http://doc.spip.org/@selecteur_auteur_ajax
function selecteur_admin_ajax($type, $id, $js, $text)
{
  include_spip('inc/chercher_rubrique');
  $url = generer_url_ecrire('acs_selectionner_admin', "admid=$id");

  // doc spip: construire_selecteur($url, $js, $idom, $name, $init='', $id=0)
  // construit un bloc comportant une icone clicable avec image animee a cote
  // pour charger en Ajax du code a mettre sous cette icone.
  // Attention: changer le onclick si on change le code Html.
  // (la fonction JS charger_node ignore l'attribut id qui ne sert en fait pas;
  // getElement en mode Ajax est trop couteux).
  return $text . construire_selecteur($url, $js, 'selection_admin_'.$id, 'nouv_admin_'.$id, ' type="hidden"', $id);
}

?>