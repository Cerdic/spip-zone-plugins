<?php
if (!defined("_ECRIRE_INC_VERSION")) {
  return
}

function seoptimizr_declarer_tables_interfaces($interface) {
  // definir les jointures possibles
  $interface['tables_jointures']['spip_seobjets'][] = 'seobjets_liens';
  $interface['tables_jointures']['spip_seobjets_liens'][] = 'seobjets';
  $interface['tables_jointures']['spip_articles'][] = 'seobjets_liens';
  $interface['tables_jointures']['spip_rubriques'][] = 'seobjets_liens';
  $interface['tables_jointures']['spip_breves'][] = 'seobjets_liens';
  $interface['tables_jointures']['spip_auteurs'][] = 'seobjets_liens';
  $interface['tables_jointures']['spip_documents'][] = 'seobjets_liens';

  // definir les noms raccourcis pour les <BOUCLE_(seoptimizr) ...
  $interface['table_des_tables']['seobjets'] = 'seobjets';
  $interface['table_des_tables']['seobjets_liens'] = 'seobjets_liens';

  // Titre pour url
  $interface['table_titre']['seobjets'] = "title, '' AS lang";

  return $interface;
}

function seoptimizr_declarer_tables_principales($tables_principales) {
  // definition de la table metas
  $spip_seobjets = array(
    'id_seobjet' => 'BIGINT(21) NOT NULL auto_increment',
    'url_redir' => 'TEXT NOT NULL',
    'meta_robots' => 'TEXT NOT NULL',
    'logo_alt' => 'TEXT NOT NULL',
    'maj' => 'TIMESTAMP', );

  // definir les cle primaire et secondaires
  $spip_seobjets_key = array(
    'PRIMARY KEY' => 'id_seobjet', );

  // inserer dans le tableau
  $tables_principales['spip_seobjets'] = array(
    'field' => &$spip_seobjets,
    'key' => &$spip_seobjets_key, );

  return $tables_principales;
}

function seoptimizr_declarer_tables_auxiliaires($tables_auxiliaires) {
  $spip_seobjets_liens = array(
    'id_seobjet' => 'BIGINT(21) NOT NULL',
    'id_objet' => 'BIGINT(21) NOT NULL',
    'objet' => 'VARCHAR(255) NOT NULL',
  );

  $spip_seobjets_liens_key = array(
    'PRIMARY KEY' => 'id_seobjet, id_objet, objet', );

  $tables_auxiliaires['spip_seobjets_liens'] = array(
    'field' => &$spip_seobjets_liens,
    'key' => &$spip_seobjets_liens_key, );

  return $tables_auxiliaires;
}