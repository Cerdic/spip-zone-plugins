<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2018-04-18 17:22:14
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

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$data = array (
  'fabrique' => 
  array (
    'version' => 6,
  ),
  'paquet' => 
  array (
    'prefixe' => 'devis',
    'nom' => 'Devis',
    'slogan' => 'Lister des devis',
    'description' => '',
    'logo' => 
    array (
      0 => '',
    ),
    'version' => '1.0.0',
    'auteur' => 'RastaPopoulos',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.2.1;3.2.*]',
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
      'nom' => 'Devis',
      'nom_singulier' => 'Devis',
      'genre' => 'masculin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => 'on',
      'table' => 'spip_devis',
      'cle_primaire' => 'id_devis',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'devis',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '10',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Référence',
          'champ' => 'reference',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '10',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Descriptif',
          'champ' => 'descriptif',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '8',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Devis',
        'titre_objet' => 'Devis',
        'info_aucun_objet' => 'Aucun devis',
        'info_1_objet' => 'Un devis',
        'info_nb_objets' => '@nb@ devis',
        'icone_creer_objet' => 'Créer un devis',
        'icone_modifier_objet' => 'Modifier ce devis',
        'titre_logo_objet' => 'Logo de ce devis',
        'titre_langue_objet' => 'Langue de ce devis',
        'texte_definir_comme_traduction_objet' => 'Ce devis est une traduction du devis numéro :',
        'titre_\\objets_lies_objet' => 'Liés à ce devis',
        'titre_objets_rubrique' => 'Devis de la rubrique',
        'info_objets_auteur' => 'Les devis de cet auteur',
        'retirer_lien_objet' => 'Retirer ce devis',
        'retirer_tous_liens_objets' => 'Retirer tous les devis',
        'ajouter_lien_objet' => 'Ajouter ce devis',
        'texte_ajouter_objet' => 'Ajouter un devis',
        'texte_creer_associer_objet' => 'Créer et associer un devis',
        'texte_changer_statut_objet' => 'Ce devis est :',
        'supprimer_objet' => 'Supprimer ce devis',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de ce devis ?',
      ),
      'rubriques' => 
      array (
        0 => 'id_rubrique',
        1 => 'id_secteur',
        2 => 'vue_rubrique',
        3 => 'statut_rubrique',
        4 => 'plan',
      ),
      'liaison_directe' => '',
      'table_liens' => '',
      'afficher_liens' => '',
      'roles' => '',
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => 'on',
      'fichiers' => 
      array (
        'echafaudages' => 
        array (
          0 => 'prive/squelettes/contenu/objets.html',
          1 => 'prive/objets/infos/objet.html',
        ),
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
