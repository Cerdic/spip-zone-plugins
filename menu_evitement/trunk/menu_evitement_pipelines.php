<?php
/**
 * Plugin Menu d&#039;évitement
 * (c) 2013 Michel Bystranowski
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function menu_evitement_affichage_final ($html) {

  if (preg_match('#<html[^>]*>#', $html)) {

    include_spip('lib/phpQuery/phpQuery/phpQuery');
    $doc = phpQuery::newDocumentHTML($html);

    $doc['body']->prepend(recuperer_fond('inclure/menu'));

    $structure = lire_config('menu_evitement/structure');

    if (lire_config('menu_evitement/lien_vers_menu_admin') == 'on') {
      $structure[] = array(
                           'cible' => 'spip-admin',
                           'texte_ancre' => '#',
                           'class' => 'spip-admin-boutons',
                           );
    }

    foreach ($structure as $menu_item) {
      $defaut = array(
                      'cible' => '#',
                      'class' => '',
                      'texte_ancre' => _T('menu_evitement:retour_au_menu'),
                      );
      $ancre = recuperer_fond('inclure/ancre-evitement', array_merge($defaut, $menu_item));
      $doc['#'.$menu_item['cible']]->prepend($ancre);
    }

    $html = $doc->getDocument();
  }

  return $html;
}

function menu_evitement_insert_head_css ($flux) {

  $css = recuperer_fond('inclure/insert_head_css');
  return $flux . $css;
}

function menu_evitement_jqueryui_plugins ($scripts) {

  $scripts[] = 'jquery.ui.sortable';
  return $scripts;
}

?>