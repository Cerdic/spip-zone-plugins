<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-07-11 19:14:34
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
    'nom' => 'ORR',
    'slogan' => 'Organisation de réservations de ressources',
    'description' => 'Adaptation du célèbre GRR pour spip',
    'prefixe' => 'oresource',
    'version' => '1.0.0',
    'auteur' => 'tofulm',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.1;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => '',
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '        ',
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
      'nom' => 'orr_ressources',
      'nom_singulier' => 'orr_ressource',
      'genre' => 'feminin',
      'logo_variantes' => '',
      'table' => 'spip_orr_ressources',
      'cle_primaire' => 'id_orr_ressource',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'orr_ressource',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'ORR type de ressource',
          'champ' => 'orr_ressource_nom',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '8',
          'saisie' => 'input',
          'explication' => 'Nom de la ressource',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'orr_ressource_couleur',
          'champ' => 'orr_ressource_couleur',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'orr_ressource_nom',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Ressources',
        'titre_objet' => 'Ressource',
        'info_aucun_objet' => 'Aucune ressource',
        'info_1_objet' => 'Une ressource',
        'info_nb_objets' => '@nb@ ressources',
        'icone_creer_objet' => 'Créer une ressource',
        'icone_modifier_objet' => 'Modifier cette ressource',
        'titre_logo_objet' => 'Logo de cette ressource',
        'titre_langue_objet' => 'Langue de cette ressource',
        'titre_objets_rubrique' => 'ressources de la rubrique',
        'info_objets_auteur' => 'Les ressources de cet auteur',
        'retirer_lien_objet' => 'Retirer cette ressource',
        'retirer_tous_liens_objets' => 'Retirer toutes les ressources',
        'ajouter_lien_objet' => 'Ajouter cette ressource',
        'texte_ajouter_objet' => 'Ajouter une ressource',
        'texte_creer_associer_objet' => 'Créer et associer une ressource',
        'texte_changer_statut_objet' => 'Cette ressource est :',
      ),
      'table_liens' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => 'webmestre',
        'objet_voir' => 'webmestre',
        'objet_modifier' => 'webmestre',
        'objet_supprimer' => 'webmestre',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
      ),
    ),
    1 => 
    array (
      'nom' => 'orr_reservations',
      'nom_singulier' => 'orr_reservation',
      'genre' => 'feminin',
      'logo_variantes' => '',
      'table' => 'spip_orr_reservations',
      'cle_primaire' => 'id_orr_reservation',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'orr_reservation',
      'champs' => 
      array (
        1 => 
        array (
          'nom' => 'Nom de la ressource',
          'champ' => 'orr_reservation_nom',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '8',
          'saisie' => 'input',
          'explication' => 'Nom de votre réservation (ex : réunion CA)',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Date de début',
          'champ' => 'orr_date_debut',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '5',
          'saisie' => 'date',
          'explication' => 'Date de début de la réservation',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Date de fin',
          'champ' => 'orr_date_fin',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '8',
          'saisie' => 'date',
          'explication' => 'Date de fin de la réservation',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'orr_reservation_nom',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Reservations',
        'titre_objet' => 'Reservation',
        'info_aucun_objet' => 'Aucune reservation',
        'info_1_objet' => 'Une reservation',
        'info_nb_objets' => '@nb@ reservations',
        'icone_creer_objet' => 'Créer une reservation',
        'icone_modifier_objet' => 'Modifier cette reservation',
        'titre_logo_objet' => 'Logo de cette reservation',
        'titre_langue_objet' => 'Langue de cette reservation',
        'titre_objets_rubrique' => 'Reservations de la rubrique',
        'info_objets_auteur' => 'Les reservations de cet auteur',
        'retirer_lien_objet' => 'Retirer cette reservation',
        'retirer_tous_liens_objets' => 'Retirer toutes les reservations',
        'ajouter_lien_objet' => 'Ajouter cette reservation',
        'texte_ajouter_objet' => 'Ajouter une reservation',
        'texte_creer_associer_objet' => 'Créer et associer une reservation',
        'texte_changer_statut_objet' => 'Cette reservation est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_auteurs',
        1 => 'spip_orr_ressources',
      ),
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => 'on',
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
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
      ),
      1 => 
      array (
      ),
    ),
  ),
);

?>