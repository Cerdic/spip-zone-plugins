<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-01-13 11:27:03
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
    'nom' => 'Guestbook',
    'slogan' => 'Livre d\'or facile',
    'description' => 'Un livre d\'or simple',
    'prefixe' => 'guestbook',
    'version' => '2.9.0',
    'auteur' => 'Yohann Prigent (potter64), Stephane Santon',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'communication',
    'etat' => 'dev',
    'compatibilite' => '[3.0.5;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '2.2.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => '',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'fonctions',
      2 => 'options',
      3 => 'pipelines',
    ),
    'inserer' => 
    array (
      'paquet' => '<necessite nom="saisies" compatibilite="[1.20.0;[" />
<utilise nom="nospam" compatibilite="[1.0.0;[" />
',
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
    'exemples' => 'on',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Messages Livre d\'Or',
      'nom_singulier' => 'Message Livre d\'Or',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_guestmessages',
      'cle_primaire' => 'id_guestmessage',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'guestmessage',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Texte de message',
          'champ' => 'guestmessage',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '5',
          'saisie' => 'textarea',
          'explication' => 'Texte du message de livre d\'or',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Email',
          'champ' => 'email',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Nom',
          'champ' => 'nom',
          'sql' => 'varchar(100) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Prénom',
          'champ' => 'prenom',
          'sql' => 'varchar(100) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Pseudo',
          'champ' => 'pseudo',
          'sql' => 'varchar(100) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Ville',
          'champ' => 'ville',
          'sql' => 'varchar(100) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Adresse IP',
          'champ' => 'ip',
          'sql' => 'varchar(15) NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Identification de l\'ordi de saisie',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Note attribuée',
          'champ' => 'note',
          'sql' => 'int(2) ',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'De 0 à 10',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Date',
          'champ' => 'date',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'pseudo',
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Messages livre d\'or',
        'titre_objet' => 'Message livre d\'or',
        'info_aucun_objet' => 'Aucun message livre d\'or',
        'info_1_objet' => 'Un message livre d\'or',
        'info_nb_objets' => '@nb@ messages livre d\'or',
        'icone_creer_objet' => 'Créer un message livre d\'or',
        'icone_modifier_objet' => 'Modifier ce message livre d\'or',
        'titre_logo_objet' => 'Logo de ce message livre d\'or',
        'titre_langue_objet' => 'Langue de ce message livre d\'or',
        'titre_objets_rubrique' => 'Messages livre d\'or de la rubrique',
        'info_objets_auteur' => 'Les messages livre d\'or de cet auteur',
        'retirer_lien_objet' => 'Retirer ce message livre d\'or',
        'retirer_tous_liens_objets' => 'Retirer tous les messages livre d\'or',
        'ajouter_lien_objet' => 'Ajouter ce message livre d\'or',
        'texte_ajouter_objet' => 'Ajouter un message livre d\'or',
        'texte_creer_associer_objet' => 'Créer et associer un message livre d\'or',
        'texte_changer_statut_objet' => 'Ce message livre d\'or est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'autorisations' => 
      array (
        'objet_creer' => 'toujours',
        'objet_voir' => '',
        'objet_modifier' => 'administrateur',
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
      'nom' => 'Réponses Livre d\'Or',
      'nom_singulier' => 'Réponse Guestbook',
      'genre' => 'feminin',
      'logo_variantes' => '',
      'table' => 'spip_guestreponses',
      'cle_primaire' => 'id_guestreponse',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'guestreponse',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Id du message auquel on repond',
          'champ' => 'id_guestmessage',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Id de message recevant la réponse',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Id auteur',
          'champ' => 'id_auteur',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'auteurs',
          'explication' => 'Id d\'auteur répondant au message',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Réponse',
          'champ' => 'guestreponse',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => 'Réponse de l\'auteur au message de Livre d\'Or',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Date',
          'champ' => 'date',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => 'Date de réponse',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'id_guestreponse',
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Réponses livre d\'or',
        'titre_objet' => 'Réponse guestbook',
        'info_aucun_objet' => 'Aucune réponse guestbook',
        'info_1_objet' => 'Une réponse guestbook',
        'info_nb_objets' => '@nb@ réponses livre d\'or',
        'icone_creer_objet' => 'Créer une réponse guestbook',
        'icone_modifier_objet' => 'Modifier cette réponse guestbook',
        'titre_logo_objet' => 'Logo de cette réponse guestbook',
        'titre_langue_objet' => 'Langue de cette réponse guestbook',
        'titre_objets_rubrique' => 'Réponses livre d\'or de la rubrique',
        'info_objets_auteur' => 'Les réponses livre d\'or de cet auteur',
        'retirer_lien_objet' => 'Retirer cette réponse guestbook',
        'retirer_tous_liens_objets' => 'Retirer toutes les réponses livre d\'or',
        'ajouter_lien_objet' => 'Ajouter cette réponse guestbook',
        'texte_ajouter_objet' => 'Ajouter une réponse guestbook',
        'texte_creer_associer_objet' => 'Créer et associer une réponse guestbook',
        'texte_changer_statut_objet' => 'Cette réponse guestbook est :',
      ),
      'table_liens' => '',
      'vue_liens' => 
      array (
        0 => 'spip_auteurs',
        1 => 'spip_guestmessages',
      ),
      'roles' => '',
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => 'on',
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => 'administrateur',
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
            'contenu' => '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',
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
            'contenu' => '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',
          ),
        ),
      ),
    ),
  ),
);

?>