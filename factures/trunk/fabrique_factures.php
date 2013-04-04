<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-04-04 11:22:33
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

$data = array (
  'fabrique' => 
  array (
    'version' => 5,
  ),
  'paquet' => 
  array (
    'nom' => 'Factures & devis',
    'slogan' => 'Facturer et faire des devis avec SPIP',
    'description' => 'Factures & devis permet d\'éditer, imprimer, archiver facilement vos devis et factures.',
    'prefixe' => 'factures',
    'version' => '1.0.0',
    'auteur' => 'Cyril Marion',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.7;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => '',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'pipelines',
    ),
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => '',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Factures',
      'nom_singulier' => 'Facture',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_factures',
      'cle_primaire' => 'id_facture',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'facture',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Id organisation',
          'champ' => 'id_organisation',
          'sql' => 'int(11) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Id type document',
          'champ' => 'id_type_document',
          'sql' => 'int(11) NOT NULL DEFAULT \'1\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Num facture',
          'champ' => 'num_facture',
          'sql' => 'varchar(50) NOT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Num devis',
          'champ' => 'num_devis',
          'sql' => 'varchar(50) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Date facture',
          'champ' => 'date_facture',
          'sql' => 'datetime DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Libelle facture',
          'champ' => 'libelle_facture',
          'sql' => 'mediumtext',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Conditions',
          'champ' => 'conditions',
          'sql' => 'text NOT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        9 => 
        array (
          'nom' => 'Reglement',
          'champ' => 'reglement',
          'sql' => 'varchar(50) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        10 => 
        array (
          'nom' => 'Delais validite',
          'champ' => 'delais_validite',
          'sql' => 'int(11) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        11 => 
        array (
          'nom' => 'Fin validite',
          'champ' => 'fin_validite',
          'sql' => 'datetime DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        13 => 
        array (
          'nom' => 'Montant',
          'champ' => 'montant',
          'sql' => 'decimal(18,2) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        15 => 
        array (
          'nom' => 'Nb heures vendues',
          'champ' => 'nb_heures_vendues',
          'sql' => 'decimal(18,2) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        16 => 
        array (
          'nom' => 'Nota bene',
          'champ' => 'nota_bene',
          'sql' => 'mediumtext',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => '',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Factures',
        'titre_objet' => 'Facture',
        'info_aucun_objet' => 'Aucun facture',
        'info_1_objet' => 'Un facture',
        'info_nb_objets' => '@nb@ factures',
        'icone_creer_objet' => 'Créer un facture',
        'icone_modifier_objet' => 'Modifier ce facture',
        'titre_logo_objet' => 'Logo de ce facture',
        'titre_langue_objet' => 'Langue de ce facture',
        'titre_objets_rubrique' => 'Factures de la rubrique',
        'info_objets_auteur' => 'Les factures de cet auteur',
        'retirer_lien_objet' => 'Retirer ce facture',
        'retirer_tous_liens_objets' => 'Retirer tous les factures',
        'ajouter_lien_objet' => 'Ajouter ce facture',
        'texte_ajouter_objet' => 'Ajouter un facture',
        'texte_creer_associer_objet' => 'Créer et associer un facture',
        'texte_changer_statut_objet' => 'Ce facture est :',
      ),
      'table_liens' => '',
      'vue_liens' => 
      array (
        0 => 'spip_auteurs',
        1 => 'spip_contacts',
        2 => 'spip_organisations',
      ),
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
        1 => 'outils_rapides',
      ),
    ),
    1 => 
    array (
      'nom' => 'Lignes facture',
      'nom_singulier' => 'Ligne facture',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_lignes_factures',
      'cle_primaire' => 'id_ligne',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'ligne',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Id facture',
          'champ' => 'id_facture',
          'sql' => 'int(11) NOT NULL DEFAULT \'0\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Position',
          'champ' => 'position',
          'sql' => 'int(11) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Quantité',
          'champ' => 'quantite',
          'sql' => 'float DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Unité',
          'champ' => 'unite',
          'sql' => 'varchar(50) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Désignation',
          'champ' => 'designation',
          'sql' => 'text',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Prix unitaire hors taxes',
          'champ' => 'prix_unitaire_ht',
          'sql' => 'decimal(18,2) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Commentaire',
          'champ' => 'commentaire',
          'sql' => 'mediumtext',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => '',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Lignes factures',
        'titre_objet' => 'Lignes facture',
        'info_aucun_objet' => 'Aucun lignes facture',
        'info_1_objet' => 'Un lignes facture',
        'info_nb_objets' => '@nb@ lignes factures',
        'icone_creer_objet' => 'Créer un lignes facture',
        'icone_modifier_objet' => 'Modifier ce lignes facture',
        'titre_logo_objet' => 'Logo de ce lignes facture',
        'titre_langue_objet' => 'Langue de ce lignes facture',
        'titre_objets_rubrique' => 'Lignes factures de la rubrique',
        'info_objets_auteur' => 'Les lignes factures de cet auteur',
        'retirer_lien_objet' => 'Retirer ce lignes facture',
        'retirer_tous_liens_objets' => 'Retirer tous les lignes factures',
        'ajouter_lien_objet' => 'Ajouter ce lignes facture',
        'texte_ajouter_objet' => 'Ajouter un lignes facture',
        'texte_creer_associer_objet' => 'Créer et associer un lignes facture',
        'texte_changer_statut_objet' => 'Ce lignes facture est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
        1 => 'outils_rapides',
      ),
    ),
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => '',
          'contenu' => '',
        ),
      ),
    ),
    'objets' => 
    array (
      0 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABAxJREFUeNq8V1tME0EUvdsWLG21UnzwLg9FLMHgI6JAqIKJ/mj8IvFbEhM14iPxFY3gBz5iNHyoUfk1wJ9KTIgKEdFqUARFI9qIIoqBSEILGPva9c7SXWfp7rYW9CSTmbmzO3PuuXNndhmO40DAjaamaqwqscyDKMEwjAbxg2PZup0VFZfCPi8QuN7YWJWcmHimtLBQHxMTw0RLoPvNGzAaDNDd2+v1BwJHK8OQ0AgNJFK5buXKOTE6HQOEVJSlf2AA0tPSYEVeXqxOqz2Lqh6KlIDVYDBoiB4zKQRevx9SUlIgz2YjJGpvNDYeioTArBQCHxIgJSkxEZbn5sbqdLpaDPEROQI6ocGyrOhBS2srX6ehF3q9HpwfP4ovzDWZIN9mA0dnJ9/fUl4umdBkNIJrYgLi4uL4/iIkwXJcbF9f3+lrDQ2wa8eOc7IEaA82l5VJJs2yWkOYC8/QWUSQmZ4OTqcTMrOyAEPK2xYuXjxF4t27vdhVIQAzRyaSxVSEz/394B4fF+0ZSMzj9carh4CbDQoAVswCUqajq6eHVSZAheDBw4dgW7YMBoeGYBy9SE9N5dUZ/Po1ZNL5ZjNY0btXvb18vyA/Hz5hKv7yeGB5Tg70oH3Thg3iGooEOEqBcrudr8kuppGTna3otfAOQYLFIrEL85I11BUIttva22HtmjW8Vx70hECr0cASJPAeNxi/MTMy4Nv37+I4DXIQEfvwyAjfLwuSU1WA3gMbS0v5uqiwMOSF5KSkP7FG6dVgy82VZAqrpgCRR2DY3tHB15b4eDBiXtOxT0lOBpfbDROY6zSyMe1GR0dhzOWS2Mg5siAhIcIQBAmUlpRIUwvljgSEnBxEBcKGAP4tVENA74HHT57M6sIlxcUREMDFaYmKiookDzocjhBbpGNsJCHgpp2E5EW5yZSgNvbX5wDBuvXrJQ8+e/o0xKY09vruJfj04jbfDgQCcOuedurU9PlMdbuvurFZX3Xlw0HVu0DuXlC7K+gxsvie44fB6/HCr8mf8HNiEibdk6RmsD/X8eg++e48qHgdz5RAxuptcLn2vKiAVjulgM/n4xjWRw6Q+rC34UwIfO66E6UC9IRyi6ikGj1mVVLAiwpw/0GBgX+twNuWOvjS1SzaiZfDwZ1OYExIVVDAiwr45RWYvglfPn8eQkCwDXXegn0nj8l5yPfRQ6iqqY5egQL8FlBD7I/toodyMCflyI6HU8DpGhsrMJvNmnBn++DLZqUYBxV4gAqc+msF6lvb2i7Y7Xa9xWJRJZG2amsYBZZKxom6Ab+f49iAhwHWRyvA0HHfX1NDmO3Ekqf61TvSZDpQfYKJYg8I4+N4FM8LIUADySh/+w/fvBj8jVfCB/INqzIu3gW/BRgAh/RYXDg93aEAAAAASUVORK5CYII=',
          ),
        ),
      ),
      1 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABAxJREFUeNq8V1tME0EUvdsWLG21UnzwLg9FLMHgI6JAqIKJ/mj8IvFbEhM14iPxFY3gBz5iNHyoUfk1wJ9KTIgKEdFqUARFI9qIIoqBSEILGPva9c7SXWfp7rYW9CSTmbmzO3PuuXNndhmO40DAjaamaqwqscyDKMEwjAbxg2PZup0VFZfCPi8QuN7YWJWcmHimtLBQHxMTw0RLoPvNGzAaDNDd2+v1BwJHK8OQ0AgNJFK5buXKOTE6HQOEVJSlf2AA0tPSYEVeXqxOqz2Lqh6KlIDVYDBoiB4zKQRevx9SUlIgz2YjJGpvNDYeioTArBQCHxIgJSkxEZbn5sbqdLpaDPEROQI6ocGyrOhBS2srX6ehF3q9HpwfP4ovzDWZIN9mA0dnJ9/fUl4umdBkNIJrYgLi4uL4/iIkwXJcbF9f3+lrDQ2wa8eOc7IEaA82l5VJJs2yWkOYC8/QWUSQmZ4OTqcTMrOyAEPK2xYuXjxF4t27vdhVIQAzRyaSxVSEz/394B4fF+0ZSMzj9carh4CbDQoAVswCUqajq6eHVSZAheDBw4dgW7YMBoeGYBy9SE9N5dUZ/Po1ZNL5ZjNY0btXvb18vyA/Hz5hKv7yeGB5Tg70oH3Thg3iGooEOEqBcrudr8kuppGTna3otfAOQYLFIrEL85I11BUIttva22HtmjW8Vx70hECr0cASJPAeNxi/MTMy4Nv37+I4DXIQEfvwyAjfLwuSU1WA3gMbS0v5uqiwMOSF5KSkP7FG6dVgy82VZAqrpgCRR2DY3tHB15b4eDBiXtOxT0lOBpfbDROY6zSyMe1GR0dhzOWS2Mg5siAhIcIQBAmUlpRIUwvljgSEnBxEBcKGAP4tVENA74HHT57M6sIlxcUREMDFaYmKiookDzocjhBbpGNsJCHgpp2E5EW5yZSgNvbX5wDBuvXrJQ8+e/o0xKY09vruJfj04jbfDgQCcOuedurU9PlMdbuvurFZX3Xlw0HVu0DuXlC7K+gxsvie44fB6/HCr8mf8HNiEibdk6RmsD/X8eg++e48qHgdz5RAxuptcLn2vKiAVjulgM/n4xjWRw6Q+rC34UwIfO66E6UC9IRyi6ikGj1mVVLAiwpw/0GBgX+twNuWOvjS1SzaiZfDwZ1OYExIVVDAiwr45RWYvglfPn8eQkCwDXXegn0nj8l5yPfRQ6iqqY5egQL8FlBD7I/toodyMCflyI6HU8DpGhsrMJvNmnBn++DLZqUYBxV4gAqc+msF6lvb2i7Y7Xa9xWJRJZG2amsYBZZKxom6Ab+f49iAhwHWRyvA0HHfX1NDmO3Ekqf61TvSZDpQfYKJYg8I4+N4FM8LIUADySh/+w/fvBj8jVfCB/INqzIu3gW/BRgAh/RYXDg93aEAAAAASUVORK5CYII=',
          ),
        ),
      ),
    ),
  ),
);

?>