<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function lienscontenus_declarer_tables_principales($tables_principales) {
  $spip_liens_contenus = array(
    'type_objet_contenant'  => 'varchar(10)', // article, rubrique, breve, site, mot, auteur, document
    'id_objet_contenant'  => 'int UNSIGNED NOT NULL',
    'type_objet_contenu'  => 'varchar(10)', // article, rubrique, breve, site, mot, auteur, document, modele
    'id_objet_contenu'    => 'varchar(255)' // peut etre le nom d'un modele
  );

  $spip_liens_contenus_key = array(
    'PRIMARY KEY' => 'type_objet_contenant, id_objet_contenant, type_objet_contenu, id_objet_contenu',
    'KEY contenant' => 'type_objet_contenant, id_objet_contenant',
    'KEY contenu' => 'type_objet_contenu, id_objet_contenu'
  );

  $tables_principales['spip_liens_contenus'] = array(
    'field' => &$spip_liens_contenus,
    'key' => &$spip_liens_contenus_key
  );

  $spip_liens_contenus_todo = array(
    'type_objet_contenant'  => 'varchar(10)', // article, rubrique, breve, site, mot, auteur, document
    'id_objet_contenant'  => 'int UNSIGNED NOT NULL',
    'date_added' => 'int(11) NOT NULL'
  );

  $spip_liens_contenus_todo_key = array(
    'PRIMARY KEY' => 'type_objet_contenant, id_objet_contenant',
  );

  $tables_principales['spip_liens_contenus_todo'] = array(
    'field' => &$spip_liens_contenus_todo,
    'key' => &$spip_liens_contenus_todo_key
  );

  return $tables_principales;
}
?>