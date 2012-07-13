<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-07-13 15:27:10
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
    'nom' => 'Spip-sondages',
    'slogan' => '',
    'description' => 'Gestion des sondages',
    'prefixe' => 'sondages',
    'version' => '2.0.0',
    'auteur' => 'Maïeul Rouquette d\'après Artego',
    'auteur_lien' => 'www.maieul.net',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.3;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '3.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'SPIP-sondages',
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
      'nom' => 'Sondages',
      'nom_singulier' => 'sondage',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_sondages',
      'cle_primaire' => 'id_sondage',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'sondage',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'TEXT NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Texte',
          'champ' => 'texte',
          'sql' => 'TEXT NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'rubriques' => 
      array (
        0 => 'id_rubrique',
        1 => 'id_secteur',
        2 => 'vue_rubrique',
      ),
      'langues' => 
      array (
        0 => 'lang',
      ),
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Sondages',
        'titre_objet' => 'Sondage',
        'info_aucun_objet' => 'Aucun sondage',
        'info_1_objet' => 'Un sondage',
        'info_nb_objets' => '@nb@ sondages',
        'icone_creer_objet' => 'Créer un sondage',
        'icone_modifier_objet' => 'Modifier ce sondage',
        'titre_logo_objet' => 'Logo de ce sondage',
        'titre_langue_objet' => 'Langue de ce sondage',
        'titre_objets_rubrique' => 'Sondages de la rubrique',
        'info_objets_auteur' => 'Les sondages de cet auteur',
        'retirer_lien_objet' => 'Retirer ce sondage',
        'retirer_tous_liens_objets' => 'Retirer tous les sondages',
        'ajouter_lien_objet' => 'Ajouter ce sondage',
        'texte_ajouter_objet' => 'Ajouter un sondage',
        'texte_creer_associer_objet' => 'Créer et associer un sondage',
        'texte_changer_statut_objet' => 'Ce sondage est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_mots',
      ),
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => 'on',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
        2 => 'prive/squelettes/contenu/objet.html',
      ),
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
      'nom' => 'Choix',
      'nom_singulier' => 'Choix',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_choix',
      'cle_primaire' => 'id_choix',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'choix',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Ordre',
          'champ' => 'Ordre',
          'sql' => 'BIGINT(21) NOT NULL DEFAULT \'0\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'TEXT NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Sondage',
          'champ' => 'id_sondage',
          'sql' => 'BIGINT(21) NOT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Choix',
        'titre_objet' => 'Choix',
        'info_aucun_objet' => 'Aucun choix',
        'info_1_objet' => 'Un choix',
        'info_nb_objets' => '@nb@ choix',
        'icone_creer_objet' => 'Créer un choix',
        'icone_modifier_objet' => 'Modifier ce choix',
        'titre_logo_objet' => 'Logo de ce choix',
        'titre_langue_objet' => 'Langue de ce choix',
        'titre_objets_rubrique' => 'Choix de la rubrique',
        'info_objets_auteur' => 'Les choix de cet auteur',
        'retirer_lien_objet' => 'Retirer ce choix',
        'retirer_tous_liens_objets' => 'Retirer tous les choix',
        'ajouter_lien_objet' => 'Ajouter ce choix',
        'texte_ajouter_objet' => 'Ajouter un choix',
        'texte_creer_associer_objet' => 'Créer et associer un choix',
        'texte_changer_statut_objet' => 'Ce choix est :',
      ),
      'table_liens' => '',
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
    ),
    2 => 
    array (
      'nom' => 'Avis',
      'nom_singulier' => 'avis',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_avis',
      'cle_primaire' => 'id_avi',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'avi',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Sondage',
          'champ' => 'id_sondage',
          'sql' => 'BIGINT(21) NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Choix',
          'champ' => 'id_choix',
          'sql' => 'BIGINT(21) NOT NULL',
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
        'titre_objets' => 'Avis',
        'titre_objet' => 'Avis',
        'info_aucun_objet' => 'Aucun avis',
        'info_1_objet' => 'Un avis',
        'info_nb_objets' => '@nb@ avis',
        'icone_creer_objet' => 'Créer un avis',
        'icone_modifier_objet' => 'Modifier ce avis',
        'titre_logo_objet' => 'Logo de ce avis',
        'titre_langue_objet' => 'Langue de ce avis',
        'titre_objets_rubrique' => 'Avis de la rubrique',
        'info_objets_auteur' => 'Les avis de cet auteur',
        'retirer_lien_objet' => 'Retirer ce avis',
        'retirer_tous_liens_objets' => 'Retirer tous les avis',
        'ajouter_lien_objet' => 'Ajouter ce avis',
        'texte_ajouter_objet' => 'Ajouter un avis',
        'texte_creer_associer_objet' => 'Créer et associer un avis',
        'texte_changer_statut_objet' => 'Ce avis est :',
      ),
      'table_liens' => '',
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
          'extension' => 'png',
          'contenu' => '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',
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
      2 => 
      array (
      ),
    ),
  ),
);

?>