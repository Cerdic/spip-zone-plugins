<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt


// Appels aux fonctions de mise en page ACS
// Simplifie le suivi des versions de spip, et assure la compatibilité depuis 1.9.2
// au niveau de l'affichage de l'espace ecrire

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function acs_commencer_page($titre = "", $rubrique = "configuration", $sous_rubrique = "acs") {
  $commencer_page = charger_fonction('commencer_page', 'inc');
  $r = $commencer_page($titre, $rubrique, $sous_rubrique);
  return $r;
}

// Retourne une boite info ACS standardisée
function acs_info_box($titre, $description, $help, $info, $icon, $description_contextuelle = false, $addon = false) {
  if ($description) $r .= '<div>'.$description.'</div>';
  if ($description_contextuelle) $r .= '<div>'.$description_contextuelle.'</div>';
  if ($info) $r .= '<div class="onlinehelp">'.$info.'</div>';
  if ($help) $r .= acs_help_call('info_box_'.$titre).'<br />'.acs_help_div('info_box_'.$titre, $help);
  if ($addon) $r .= '<br />'.$addon;
  return acs_box($titre, $r, $icon, false, '<img src="'._DIR_PLUGIN_ACS.'images/info.png" />');
}
/**
 * Boite
 * @param string titre : titre
 * @param string contenu: contenu
 * @param string icon: chemin de l'icône
 * @param string class: classe de la boite acs_box
 * @param string titre2: autre élément positionné à droite dans la barre de titre
 */
function acs_box($titre, $contenu, $icon=false, $class=false, $titre2=false) {
  if ($class) $class = " $class";
  $r = '<div class="acs_box'.$class.'">';
  if ($icon) $r .= '<div style="position: absolute; top: -16px; left: 10px; z-index: 100;"><img src="'.$icon.'" alt="" /></div>';
  if ($titre) {
    $r .= '<div class="acs_box_titre"><table width="100%"><tr><td width="100%">'.$titre.'</td>';
    if ($titre2) $r .= '<td align="right">'.$titre2.'</td>';
    $r .= '</tr></table></div>';
  }
  $r .= '<div class="acs_box_texte arial2">'.$contenu.'</div>';
  $r .= '</div>';
  return $r;
}

// Affichage 3 colonnes dans l'interface admin spip
function acs_3colonnes($col1, $col2, $col3) {
  echo '<div class="acs_colonnes">';
  if ($GLOBALS['spip_ecran'] == 'etroit') {
    echo '<div class="acs_col1"><div class="acs_col">'.$col1.'<br />'.$col3.'</div></div>'.
         '<div class="acs_col2" style="width:75%"><div class="acs_col">'.$col2.'</div></div>';
  }
  else {
    echo '<div class="acs_col1"><div class="acs_col">'.$col1.'</div></div>'.
         '<div class="acs_col2" style="width:50%"><div class="acs_col">'.$col2.'</div></div>'.
         '<div class="acs_col3"><div class="acs_col">'.$col3.'</div></div>';
  }
  echo '</div><br style ="clear: both"/>';
}

function acs_help_call($id) {
  return '<img src="'._DIR_PLUGIN_ACS.'images/aide.gif" onmouseover=\'$("#'.$id.'").slideToggle("slow");\' onclick=\'$("#'.$id.'").slideToggle("slow");\' style="cursor:help;" title="'._T('icone_aide_ligne').'" align="right"/>';
}

function acs_help_div($id, $help) {
  return '<div id="'.$id.'" class="contexthelp onlinehelp pliable" style="text-align: justify" onclick=\'$("#'.$id.'").slideToggle("slow");\'>'.$help.'</div>';
}

/**
 * Crée un lien image plieur/déplieur jQuery pour les éléments de la classe $classe
 * Utilise le href si pas de jQuery ou pas de javascript (soft downgrade)
 *
 * Classes définies:
 * plieur : lien(s) a href
 * imgp_<classe_a_plier> : image affichée
 * imgoff_<classe_a_plier> : image plié
 * imgon_<classe_a_plier> : image déplié
 */
function acs_plieur($id_plieur, $classe_a_plier, $url, $on=false, $onclick=false, $texte='') {
  $imgoff = _DIR_PLUGIN_ACS.'images/deplierhaut.gif';
  $imgon = _DIR_PLUGIN_ACS.'images/deplierbas.gif';
  $imgp = $on ? $imgon : $imgoff;

  if ($onclick) $onclick = ' onclick="'.$onclick.'"';
  return '<a href="'.$url.'" id="'.$id_plieur.'" class="acs_plieur" name="plieur_'.$classe_a_plier.'" title="'._T('info_deplier').'"'.$onclick.'><img class="imgp_'.$classe_a_plier.'" src="'.$imgp.'" alt="¤>" />'.($texte ? ' '.$texte.' ' : '').'</a><img class="imgon_'.$classe_a_plier.'" src="'.$imgon.'" alt="" width="0" height="0" border="0" style="visibility: hidden" /><img class="imgoff_'.$classe_a_plier.'" src="'.$imgoff.'" alt="" width="0" height="0" border="0" style="visibility: hidden" />';
}
?>
