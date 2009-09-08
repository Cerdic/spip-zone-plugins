<?
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

$spip_genespip_individu = array(
    "id_individu" => "int(11) NOT NULL auto_increment",
                         "nom" => "text NOT NULL",
                         "prenom" => "text",
                         "sexe" => "int(11) NOT NULL default '0'",
                         "metier" => "longtext",
                         "pere" => "int(11) NOT NULL default '0'",
                         "mere" => "int(11) NOT NULL default '0'",
                         "enfant" => "int(11) NOT NULL default '0'",
                         "note" => "longtext NOT NULL",
                         "proprio" => "int(11) NOT NULL default '0'",
                         "portrait" => "int(11) default '0'",
                         "format_portrait" => "text",
                         "id_auteur" => "int(3) default NULL",
                         "source" => "text",
                         "adresse" => "text",
                         "signature" => "int(11) default NULL",
                         "format_signature" => "text",
                         "date_update"  => "datetime NOT NULL default '0000-00-00 00:00:00'",
                         "poubelle" => "int(1) NOT NULL default '0'",
                         "limitation" => "int(3) default NULL"
  );
$spip_genespip_individu_key = array(
  "PRIMARY KEY" => "id_individu"
  );

  $tables_principales['spip_genespip_individu'] = array(
        'field' => &$spip_genespip_individu,
        'key' => &$spip_genespip_individu_key);

$spip_genespip_documents = array(
                         "id_documents" => "int(11) NOT NULL auto_increment",
                         "id_individu" => "int(11) NOT NULL default '0'",
                         "id_article" => "int(11) NOT NULL default '0'"
  );
$spip_genespip_documents_key = array(
  "PRIMARY KEY" => "id_documents"
  );
  $tables_principales['spip_genespip_documents'] = array(
        'field' => &$spip_genespip_documents,
        'key' => &$spip_genespip_documents_key);

$spip_genespip_liste = array(
                         "id_liste" => "int(11) NOT NULL auto_increment",
                         "nom" => "text NOT NULL",
                         "nombre" => "int(11) NOT NULL",
                         "date_couverte" => "TINYTEXT NOT NULL",
                         "date_update"  => "date NOT NULL"
  );
$spip_genespip_liste_key = array(
  "PRIMARY KEY" => "id_liste"
  );
  $tables_principales['spip_genespip_liste'] = array(
        'field' => &$spip_genespip_liste,
        'key' => &$spip_genespip_liste_key);


$spip_genespip_lieux = array(
                         "id_lieu" => "INT NOT NULL auto_increment",
                         "ville" => "TEXT NOT NULL",
                         "code_departement" => "INT NOT NULL",
                         "departement" => "TEXT NOT NULL",
                         "region" => "TEXT NOT NULL",
                         "pays" => "TEXT NOT NULL"
);
$spip_genespip_lieux_key = array(
  "PRIMARY KEY" => "id_lieu"
  );
$tables_principales['spip_genespip_lieux'] = array(
        'field' => &$spip_genespip_lieux,
        'key' => &$spip_genespip_lieux_key);

$spip_genespip_evenements = array(
                          "id_evenement" => "INT NOT NULL auto_increment",
                          "id_individu" => "INT NOT NULL",
                          "id_type_evenement" => "INT NOT NULL",
                          "date_evenement" => "DATE NOT NULL",
                          "precision_date" => "TEXT NOT NULL",
                          "id_lieu" => "INT NOT NULL DEFAULT '1'",
                          "id_epoux" => "INT NOT NULL",
                          "date_update"  => "datetime NOT NULL"
);
$spip_genespip_evenements_key = array(
  "PRIMARY KEY" => "id_evenement"
  );
$tables_principales['spip_genespip_evenements'] = array(
        'field' => &$spip_genespip_evenements,
        'key' => &$spip_genespip_evenements_key);

$spip_genespip_type_evenements = array(
                          "id_type_evenement" => "INT NOT NULL auto_increment",
                          "type_evenement" => "TEXT NOT NULL",
                          "clair_evenement" => "TEXT NOT NULL"
);
$spip_genespip_type_evenements_key = array(
  "PRIMARY KEY" => "id_type_evenement"
  );
$tables_principales['spip_genespip_type_evenements'] = array(
        'field' => &$spip_genespip_type_evenements,
        'key' => &$spip_genespip_type_evenements_key);

$spip_genespip_journal = array(
                          "id_journal" => "INT NOT NULL auto_increment",
                          "action" => "TINYTEXT NOT NULL",
                          "descriptif" => "TEXT NOT NULL",
                          "id_individu" => "INT NOT NULL",
                          "id_auteur" => "INT NOT NULL",
                          "date_update"  => "DATETIME NOT NULL"
);
$spip_genespip_journal_key = array(
  "PRIMARY KEY" => "id_journal"
  );
$tables_principales['spip_genespip_journal'] = array(
        'field' => &$spip_genespip_journal,
        'key' => &$spip_genespip_journal_key);

global $table_des_tables;
    $table_des_tables['spip_genespip_individu'] = 'spip_genespip_individu';
    $table_des_tables['spip_genespip_documents'] = 'spip_genespip_documents';
    $table_des_tables['spip_genespip_liste'] = 'spip_genespip_liste';
    $table_des_tables['spip_genespip_lieux'] = 'spip_genespip_lieux';
    $table_des_tables['spip_genespip_evenements'] = 'spip_genespip_evenements';
    $table_des_tables['spip_genespip_type_evenements'] = 'spip_genespip_type_evenements';
    $table_des_tables['spip_genespip_journal'] = 'spip_genespip_journal';
?>