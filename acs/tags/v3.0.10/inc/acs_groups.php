<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne un tableau des groupes d'admins
 * (ACS_CADENASSE est structurée pour le controle d'accès)
 */
function acs_groups() {
  $groups = unserialize($GLOBALS['meta']['ACS_GROUPS']);
  if (is_array($groups))
    return $groups;
  else
    return array();
}

/**
 * Nom d'un groupe à partir de son numéro d'ordre
 */
function acs_grid($id) {
  $grids = array_keys(acs_groups());
  return $grids[$id-1];
}

/**
 * Retourne les membres du groupe
 */
function acs_members($grid) {
  $groups = unserialize($GLOBALS['meta']['ACS_GROUPS']);
  if (is_array($groups) && isset($groups[$grid]))
    return array_keys($groups[$grid]);
  else
    return array($grid => '');
}

function acs_groups_update($newgroups) {
  if ($newgroups) {
    $ng = explode(',', $newgroups);
    foreach ($ng as $k=>$group)
      $ng[$k] = trim($group);
    $acsGroups = acs_groups();
    if (is_array($acsGroups)) {
      foreach ($acsGroups as $grid=>$group) { // remove old groups
        if (!in_array($grid, $ng)) {
          acs_group_update_pages($grid, '');
          unset($acsGroups[$grid]);
        }
      }
    }
    foreach ($ng as $group) { // add new groups
      if (!isset($acsGroups[$group]))
        $acsGroups[$group] = array(1=>$group);
    }
    ecrire_meta('ACS_GROUPS', serialize($acsGroups));
  }
  else {
    ecrire_meta('ACS_GROUPS', '');
    ecrire_meta('ACS_CADENASSE', '');
  }
  ecrire_metas();
  acs_log("acs_groups_update : ".$newgroups);
}

/**
 * Met à jour les pages du groupe $grid
 */
function acs_group_update_pages($grid, $pages) {
  $pages_array = explode(',', $pages);
  foreach($pages_array as $key=>$page)
    $pages_array[$key] = trim($page);
  $acsPages = unserialize($GLOBALS['meta']['ACS_CADENASSE']);
  if (is_array($acsPages)) {
    foreach ($acsPages as $page=>$admins) { // remove old or empty pages from group
      if ((!count($acsPages[$page])) ||
          (!in_array($page, $pages_array) && in_array($page, acs_group($grid, 'pages')))
         ) {
        unset($acsPages[$page]);
      }
    }
  }
  if ((count($pages_array) > 0) && $pages_array[0]) {
    foreach ($pages_array as $page) { // add new pages to group
      $page = trim($page);
      if (!isset($acsPages[$page]) &&
          !in_array($page, $GLOBALS['ACS_ENFER']) &&
          find_in_path('exec/'.$page.'.php')
         ) {
        $acsGroups = acs_groups();
        $acsPages[$page] = $acsGroups[$grid];
      }
    }
  }
  ecrire_meta('ACS_CADENASSE', serialize($acsPages));
  ecrire_metas();
  acs_log("acs_group_update_pages ($grid) : ".$pages);
}

function acs_group_add_admin($id, $id_admin) {
  $groups = acs_groups();
  $grid = acs_grid($id);
  if (!in_array($id_admin, array_keys($groups[$grid]))) {
    $groups[$grid][$id_admin] = $grid;
    $pages = unserialize($GLOBALS['meta']['ACS_CADENASSE']);
    foreach ($pages as $page => $admins) {
      if (in_array($page, acs_group($grid, 'pages')))
        $pages[$page] = $groups[$grid];
    }
    ecrire_meta('ACS_GROUPS', serialize($groups));
    ecrire_meta('ACS_CADENASSE', serialize($pages));
    ecrire_metas();
    acs_log("acs_group_add_admin (nouvel admin dans le groupe $id ".$grid."): ".$GLOBALS['auteur_session']['id_auteur']."+$id_admin");
  }
}

function acs_group_del_admin($id, $id_admin) {
  $acsGroups = unserialize($GLOBALS['meta']['ACS_GROUPS']);
  if (is_array($acsGroups)) {
    $grid = acs_grid($id);
    $admins = array_keys($acsGroups[$grid]);
    if (count($admins)) {
      if (in_array($id_admin, $admins)) {
        unset($acsGroups[$grid][$id_admin]);
        $pages = unserialize($GLOBALS['meta']['ACS_CADENASSE']);
        foreach ($pages as $page => $admins) {
          if (in_array($page, acs_group($grid, 'pages')))
            if (count($acsGroups[$grid]) > 0)
              $pages[$page] = $acsGroups[$grid];
            else
              unset($pages[$page]);
        }
      }
    }
    else {
      unset($acsGroups[$grid]);
    }
    ecrire_meta('ACS_GROUPS', serialize($acsGroups));
    ecrire_meta('ACS_CADENASSE', serialize($pages));
    ecrire_metas();
    acs_log("acs_group_del_admin (efface admin $id_admin du groupe $id $grid): ".$GLOBALS['auteur_session']['id_auteur']."-$id_admin");
  }
}

function acs_groups_from_acsCadenasse() {
  $groups = array();

  $acsCadenasse = unserialize($GLOBALS['meta']['ACS_CADENASSE']);
  if (!is_array($acsCadenasse))
    return $groups;

  foreach($acsCadenasse as $page=>$admins) {
    if (is_array($admins)) {
      foreach($admins as $admin=>$gr) {
        $groups[$gr]['pages'][$page] = '';
        $groups[$gr]['members'][$admin] = '';
      }
    }
    else {
      $groups[$gr]['pages'] = array();
      $groups[$gr]['members'] = array();
    }
  }
  return $groups;
}


/**
 * Retourne les pages gérées par le groupe $grid,
 * ou les membres du groupe, à partir de la méta ACS_CADENASSE
 */

function acs_group($grid, $pm) {
  $gr = acs_groups_from_acsCadenasse();
  if (is_array($gr[$grid][$pm]))
    return array_keys($gr[$grid][$pm]);
  else
    return array();
}

/**
 * Retourne les pages du groupe n° $id
 */
function acs_pages($id) {
  $pages = acs_group(acs_grid($id), 'pages');
  return implode(', ', $pages);
}
?>
