<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_presentation');

function exec_acs_suivi_admins() {
  echo acs_commencer_page(_T('acs:assistant_configuration_squelettes'), "configuration", "acs");

  echo "<br />";
  echo acs_gros_titre(_T('acs:publications'));
  echo acs_3colonnes(acs_suivi_admins_affiche_gauche(), acs_suivi_admins_affiche_milieu(), false);
  echo fin_page();
}

function acs_suivi_admins_affiche_gauche() {
  return acs_info_box(
    _T('acs:publications'),
    _T('acs:pub_description').'<br /><br />',
    _T('acs:pub_help'),
    false,
    _DIR_PLUGIN_ACS."img_pack/acs_config-24.gif");
}

function acs_suivi_admins_affiche_milieu() {
  $tmpDir = _DIR_RACINE._NOM_TEMPORAIRES_INACCESSIBLES;
  $log_admins = $tmpDir.'admins.log';
  if (is_readable($log_admins)) {
    $maj = filemtime($log_admins);
    $log_admins = file_get_contents($log_admins);
    $log_admins = explode("\n", $log_admins);
    $log_admins = array_reverse($log_admins);
    $r .= '<br /><div style="border: 1px solid #aaaaaa;"><table class="arial2" border="0" cellpadding="2" cellspacing="0" style="width: 100%;">';
    foreach($log_admins as $line) {
      $bgcolor = alterner($i++, '#eeeeee','white');
      if ($line) $r .= log2html($line, $bgcolor);
    }
    $r .= '</table></div>';
  }
  $r .= '<table width="100%"><tr><td>'._T('acs:acsDerniereModif').' '.date("Y-m-d H:i:s", $maj).'</td><td style="text-align:'.$GLOBALS['spip_lang_right'].'"> ACS '.ACS_VERSION.'</td></tr></table>';

  $r = acs_box(_T('acs:publications'), $r, _DIR_PLUGIN_ACS.'img_pack/admin-24.gif');
  return $r;
}

function log2html($line, $bgcolor) {
  $matches = array();
  preg_match('/([\w]* \d{2} \d{2}:\d{2}:\d{2}) \d{1,3}[.]\d{1,3}[.]\d{1,3}[.]\d{1,3} \([^\)]*\) (.*)/', $line, $matches);
  $date = $matches[1];
  $line = $matches[2];

  $op = array();
  preg_match('/([^|]*)\|([^|]*)\|([^|]*)\|(?:\[([^\]]*)\]=(.*))?/', $line, $op);
  $id_admin = $op[1];
  $table = $op[2];
  $id_objet = $op[3];
  $statut = $op[5];

  if ($table == 'spip_articles') {
    $link = '<a href="?exec=articles&id_article='.$id_objet.'">';
    $fin_link = '</a>';
  }
  elseif ($table == 'spip_rubriques') {
    $link = '<a href="?exec=naviguer&id_rubrique='.$id_objet.'">';
    $fin_link = '</a>';
  }
  elseif ($table == 'spip_auteurs') {
    $link = '<a href="?exec=auteur_infos&id_auteur='.$id_objet.'">';
    $fin_link = '</a>';
  }
/*
  elseif ($table == 'spip_mots') {
    $link = '<a href="?exec=mots_edit&id_mot='.$id_objet.'">';
    $fin_link = '</a>';
  }*/
  else {
    $link = '';
    $fin_link = '';
  }

  if (!$statut) $statut = _T('acs:modifie');
  $r = '<tr style="background-color: '.$bgcolor.';"><td><a href="?exec=auteur_infos&id_auteur='.$id_admin.'">'.get_nom_auteur($id_admin).'</a> '._T('acs:'.$statut).' '._T('acs:'.$table).' '.$link.'n° '.$id_objet.$fin_link.'</a></td><td style="text-align: right">'.$date.'</td></tr>';
  return $r;
}

// Renvoit le nom d'un auteur - Return the name of an author
function get_nom_auteur($id_auteur) {
  static $noms;

  if (is_array($noms) && isset($noms[$id_auteur]))
    return $noms[$id_auteur];

  $result = spip_query("SELECT nom FROM spip_auteurs WHERE id_auteur=$id_auteur;");
  $result = spip_fetch_array($result);
  if ($result['nom'])
    $result = $result['nom'];
  else
    $result = $id_auteur;
  $noms[$id_auteur] = $result;
  return $result;
}
?>
