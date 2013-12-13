<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-05-11 18:28:11
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
    'nom' => 'Évaluations',
    'slogan' => 'Pour gérer et utiliser des grilles de notations avec commentaires',
    'description' => 'Évaluation fournit un processus assez simple pour noter, évaluer ou critiquer des éléments (par exemple des articles, documents, sites) par des visiteurs identifiés sur le site.

Les évaluations récoltées peuvent vous aider à prendre des décisions, à créer des moyennes des notes, élire des gagnants d\'un concours, trier les articles ou documents selon des critères analysés. Enfin ce qui vous plaira…',
    'prefixe' => 'evaluations',
    'version' => '1.0.0',
    'auteur' => 'Matthieu Marcillaud',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'communication',
    'etat' => 'dev',
    'compatibilite' => '[3.0.8;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configurer les évalutations',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'fonctions',
      2 => 'pipelines',
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
      'nom' => 'Évaluations',
      'nom_singulier' => 'Évaluation',
      'genre' => 'feminin',
      'logo_variantes' => '',
      'table' => 'spip_evaluations',
      'cle_primaire' => 'id_evaluation',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'evaluation',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Identifiant',
          'champ' => 'identifiant',
          'sql' => 'varchar(30) NOT NULL DEFAULT \'\'',
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
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '7',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Texte',
          'champ' => 'texte',
          'sql' => 'mediumtext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '1',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=4',
        ),
        3 => 
        array (
          'nom' => 'Date de début',
          'champ' => 'date_debut',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Date de fin',
          'champ' => 'date_fin',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'évaluations',
        'titre_objet' => 'évaluation',
        'info_aucun_objet' => 'Aucune évaluation',
        'info_1_objet' => 'Une évaluation',
        'info_nb_objets' => '@nb@ évaluations',
        'icone_creer_objet' => 'Créer une évaluation',
        'icone_modifier_objet' => 'Modifier cette évaluation',
        'titre_logo_objet' => 'Logo de cette évaluation',
        'titre_langue_objet' => 'Langue de cette évaluation',
        'titre_objets_rubrique' => 'évaluations de la rubrique',
        'info_objets_auteur' => 'Les évaluations de cet auteur',
        'retirer_lien_objet' => 'Retirer cette évaluation',
        'retirer_tous_liens_objets' => 'Retirer toutes les évaluations',
        'ajouter_lien_objet' => 'Ajouter cette évaluation',
        'texte_ajouter_objet' => 'Ajouter une évaluation',
        'texte_creer_associer_objet' => 'Créer et associer une évaluation',
        'texte_changer_statut_objet' => 'Cette évaluation est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_articles',
        1 => 'spip_documents',
        2 => 'spip_syndic',
      ),
      'roles' => '',
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
      ),
      'saisies' => 
      array (
        0 => 'objets',
      ),
    ),
    1 => 
    array (
      'nom' => 'Critères d\'évaluation',
      'nom_singulier' => 'Critère d\'évaluation',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_evaluations_criteres',
      'cle_primaire' => 'id_evaluations_critere',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'evaluations_critere',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Évalutation',
          'champ' => 'id_evaluation',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'evaluations',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Texte',
          'champ' => 'texte',
          'sql' => 'mediumtext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Noter',
          'champ' => 'noter',
          'sql' => 'varchar(3) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'oui_non',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Commenter',
          'champ' => 'commenter',
          'sql' => 'varchar(3) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'oui_non',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Note minimale',
          'champ' => 'note_mini',
          'sql' => 'int(4) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Note maximale',
          'champ' => 'note_maxi',
          'sql' => 'int(4) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Pondération',
          'champ' => 'ponderation',
          'sql' => 'int(3) NOT NULL DEFAULT 1',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Indications du commentaire',
          'champ' => 'texte_commentaire',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => '',
        ),
        9 => 
        array (
          'nom' => 'Rang',
          'champ' => 'rang',
          'sql' => 'int(4) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Pour ordonner les critères. Indiquez un numéro de rang.',
          'saisie_options' => '',
        ),
        10 => 
        array (
          'nom' => 'Évaluer les forces et faiblesses',
          'champ' => 'evaluer',
          'sql' => 'varchar(3) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'oui_non',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Critères d\'évaluation',
        'titre_objet' => 'Critère d\'évaluation',
        'info_aucun_objet' => 'Aucun critère d\'évaluation',
        'info_1_objet' => 'Un critère d\'évaluation',
        'info_nb_objets' => '@nb@ critères d\'évaluation',
        'icone_creer_objet' => 'Créer un critère d\'évaluation',
        'icone_modifier_objet' => 'Modifier ce critère d\'évaluation',
        'titre_logo_objet' => 'Logo de ce critère d\'évaluation',
        'titre_langue_objet' => 'Langue de ce critère d\'évaluation',
        'titre_objets_rubrique' => 'Critères d\'évaluation de la rubrique',
        'info_objets_auteur' => 'Les critères d\'évaluation de cet auteur',
        'retirer_lien_objet' => 'Retirer ce critère d\'évaluation',
        'retirer_tous_liens_objets' => 'Retirer tous les critères d\'évaluation',
        'ajouter_lien_objet' => 'Ajouter ce critère d\'évaluation',
        'texte_ajouter_objet' => 'Ajouter un critère d\'évaluation',
        'texte_creer_associer_objet' => 'Créer et associer un critère d\'évaluation',
        'texte_changer_statut_objet' => 'Ce critère d\'évaluation est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
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
      'saisies' => 
      array (
        0 => 'objets',
      ),
    ),
    2 => 
    array (
      'nom' => 'Critiques',
      'nom_singulier' => 'Critique',
      'genre' => 'feminin',
      'logo_variantes' => '',
      'table' => 'spip_evaluations_critiques',
      'cle_primaire' => 'id_evaluations_critique',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'evaluations_critique',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Évaluation',
          'champ' => 'id_evaluation',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'evaluations',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Critère d\'évaluation',
          'champ' => 'id_evaluations_critere',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'evaluations_criteres',
          'explication' => '',
          'saisie_options' => 'id_evaluation=#ID_EVALUATION',
        ),
        2 => 
        array (
          'nom' => 'Type d\'objet',
          'champ' => 'objet',
          'sql' => 'varchar(30) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Identifiant d\'objet',
          'champ' => 'id_objet',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Auteur',
          'champ' => 'id_auteur',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'auteurs',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Note',
          'champ' => 'note',
          'sql' => 'int(4) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Commentaire',
          'champ' => 'commentaire',
          'sql' => 'mediumtext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => 'li_class=haut',
        ),
        7 => 
        array (
          'nom' => 'Forces',
          'champ' => 'forces',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => 'li_class=haut',
        ),
        8 => 
        array (
          'nom' => 'Faiblesses',
          'champ' => 'faiblesses',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => 'li_class=haut',
        ),
      ),
      'champ_titre' => 'commentaire',
      'champ_date' => 'date',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Critiques',
        'titre_objet' => 'Critique',
        'info_aucun_objet' => 'Aucune critique',
        'info_1_objet' => 'Une critique',
        'info_nb_objets' => '@nb@ critiques',
        'icone_creer_objet' => 'Créer une critique',
        'icone_modifier_objet' => 'Modifier cette critique',
        'titre_logo_objet' => 'Logo de cette critique',
        'titre_langue_objet' => 'Langue de cette critique',
        'titre_objets_rubrique' => 'Critiques de la rubrique',
        'info_objets_auteur' => 'Les critiques de cet auteur',
        'retirer_lien_objet' => 'Retirer cette critique',
        'retirer_tous_liens_objets' => 'Retirer toutes les critiques',
        'ajouter_lien_objet' => 'Ajouter cette critique',
        'texte_ajouter_objet' => 'Ajouter une critique',
        'texte_creer_associer_objet' => 'Créer et associer une critique',
        'texte_changer_statut_objet' => 'Cette critique est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
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
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQCAMAAAC3Ycb+AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAuhQTFRFAAAA+/v77+/v/f39/Pz8+Pj4EBAQ9fX1v7+/+vr6f39/8vLyMDAwz8/P+fn5j4+P39/fn5+f9/f3QEBAYGBgICAg7Ozs9PT0enp69vb24+Pje3t78/Pzr6+vUFBQfHx85ubm4ODgcHBw8PDw6enpfX193d3d8fHx7u7ug4ODzs7OhoaG2tra6urq6OjogICA6+vreXl519fXkpKSwsLCoaGh7e3t4uLiiYmJ5eXl5+fn0dHR3t7excXFy8vLubm51NTUvLy8pKSknp6elZWVqqqqgYGByMjI5OTk09PT1dXV2NjYtra22dnZi4uL3Nzcp6enra2tCAgIm5ub1tbWmJiYx8fHjIyMnZ2djo6OnJyciIiIwMDAs7Ozurq6Pz8/mZmZysrKt7e329vbxMTEhYWFq6urpqamzMzMvb29dXV1hISEtLS0ZmZmDw8PsLCwfn5+0NDQkZGRHx8fjY2Nw8PDgoKCX19frq6u0tLSmpqasbGxqampk5OTxsbGlpaWvr6+4eHhGBgYoqKibW1tioqKpaWlNjY2CwsLzc3NoKCglJSUKCgoycnJDg4OkJCQd3d3Ly8vl5eXT09Po6Ojc3NzqKioV1dXTU1NCQkJGRkZWlpauLi4wcHBh4eHGxsbbm5urKysSUlJTExMsrKyu7u7b29vHR0dLS0tNzc3eHh4dHR0Pj4+WFhYODg4Li4uKysrcnJyLCwsHh4etbW1Z2dnampqDAwMWVlZRkZGISEhDQ0NVlZWSEhIdnZ2XV1dEhIScXFxTk5OJiYmPDw8JCQk/v7+VFRUaWlpFhYWXl5eREREJycnR0dHUVFRKioqQUFBSkpKXFxcMzMzIiIiY2NjQkJCOzs7FxcXW1tbMTExU1NTFRUVVVVVOTk5bGxsPT09ZWVlOjo6Ghoaa2trRUVFYmJiJSUlNDQ0IyMjCgoKHBwcNTU1S0tLKSkpBwcHMjIyExMTQ0NDERERFBQUZGRkaGho////oxwaBQAAAPh0Uk5T/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wAozrY3AAA2TUlEQVR42uydeWAUVbb/K5WkU6l0p7vS3UlI6E7SJCEJYQtbICEQtrAvsoZ934lssotBwAUFAgiiAwyooAKKPHEcdcSR0XEZHR31jeO8Wd/s8+Ztv+Xl31fVS2q7a3VVp4N1/yFL0+mqT91zzznfc89l2u2RVIOxb4ENxB42EBuIPWwgNhB72EBsIPawgdhA7GEDsYcNxAZiDxuIDcQeNhAbiD1sIHSX0DFsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxGIgDocNJJmAOKoqvDaQ5AHirPhxamozkyaN1FQbSGcD8VakpqWkpDRkikP8t0szuRuA7JRwZGanp+fk5KSnp2dnpojzxAbSaaNZxJGdnpORkdddHHkZ3dLTRSSpNpBOGuL0yE7v9kXLGpatqKhg2Z5ZeRk5XRdJFwbi4VoF3p2WkpmeU/ysuLJv6jNs2LA+pVW3x9xaPis9u4sS6YJAXBzPe72RUPDEoyKPjPEvhL/57JbHUzJk/OiW8HfXr1/c3FplA7F0TgT4oJNRjBO5Kdk5GcNfjX771qqJNVNW1O//V8VLnKf4gMcGYvpgA7zXwWjGTolH3soW+SdLWqvqJlbX39S+0ttlqHQFIB7B52cA40pKSnq37g+ofvbg6P0NWyZWn3le/3K/T/DYQOKfGSEgDHGMFpePbt2van/8ITe8oa7G9Snw//h9AdYGYnxquL0MbDhmSMt51nf0v3lhxsI5VSOrN8P+p9ftsYEYouFk4MMRdq8K3gP97pELjfctq6vZ+Ar0fztDLhuIiTQi7lV6xsgXIL8+e46bUzWx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',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQCAMAAAC3Ycb+AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAuhQTFRFAAAA+/v77+/v/f39/Pz8+Pj4EBAQ9fX1v7+/+vr6f39/8vLyMDAwz8/P+fn5j4+P39/fn5+f9/f3QEBAYGBgICAg7Ozs9PT0enp69vb24+Pje3t78/Pzr6+vUFBQfHx85ubm4ODgcHBw8PDw6enpfX193d3d8fHx7u7ug4ODzs7OhoaG2tra6urq6OjogICA6+vreXl519fXkpKSwsLCoaGh7e3t4uLiiYmJ5eXl5+fn0dHR3t7excXFy8vLubm51NTUvLy8pKSknp6elZWVqqqqgYGByMjI5OTk09PT1dXV2NjYtra22dnZi4uL3Nzcp6enra2tCAgIm5ub1tbWmJiYx8fHjIyMnZ2djo6OnJyciIiIwMDAs7Ozurq6Pz8/mZmZysrKt7e329vbxMTEhYWFq6urpqamzMzMvb29dXV1hISEtLS0ZmZmDw8PsLCwfn5+0NDQkZGRHx8fjY2Nw8PDgoKCX19frq6u0tLSmpqasbGxqampk5OTxsbGlpaWvr6+4eHhGBgYoqKibW1tioqKpaWlNjY2CwsLzc3NoKCglJSUKCgoycnJDg4OkJCQd3d3Ly8vl5eXT09Po6Ojc3NzqKioV1dXTU1NCQkJGRkZWlpauLi4wcHBh4eHGxsbbm5urKysSUlJTExMsrKyu7u7b29vHR0dLS0tNzc3eHh4dHR0Pj4+WFhYODg4Li4uKysrcnJyLCwsHh4etbW1Z2dnampqDAwMWVlZRkZGISEhDQ0NVlZWSEhIdnZ2XV1dEhIScXFxTk5OJiYmPDw8JCQk/v7+VFRUaWlpFhYWXl5eREREJycnR0dHUVFRKioqQUFBSkpKXFxcMzMzIiIiY2NjQkJCOzs7FxcXW1tbMTExU1NTFRUVVVVVOTk5bGxsPT09ZWVlOjo6Ghoaa2trRUVFYmJiJSUlNDQ0IyMjCgoKHBwcNTU1S0tLKSkpBwcHMjIyExMTQ0NDERERFBQUZGRkaGho////oxwaBQAAAPh0Uk5T/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wAozrY3AAA2TUlEQVR42uydeWAUVbb/K5WkU6l0p7vS3UlI6E7SJCEJYQtbICEQtrAvsoZ934lssotBwAUFAgiiAwyooAKKPHEcdcSR0XEZHR31jeO8Wd/s8+Ztv+Xl31fVS2q7a3VVp4N1/yFL0+mqT91zzznfc89l2u2RVIOxb4ENxB42EBuIPWwgNhB72EBsIPawgdhA7GEDsYcNxAZiDxuIDcQeNhAbiD1sIHSX0DFsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxAZiA7GB2EBsIDYQG4gNxGIgDocNJJmAOKoqvDaQ5AHirPhxamozkyaN1FQbSGcD8VakpqWkpDRkikP8t0szuRuA7JRwZGanp+fk5KSnp2dnpojzxAbSaaNZxJGdnpORkdddHHkZ3dLTRSSpNpBOGuL0yE7v9kXLGpatqKhg2Z5ZeRk5XRdJFwbi4VoF3p2WkpmeU/ysuLJv6jNs2LA+pVW3x9xaPis9u4sS6YJAXBzPe72RUPDEoyKPjPEvhL/57JbHUzJk/OiW8HfXr1/c3FplA7F0TgT4oJNRjBO5Kdk5GcNfjX771qqJNVNW1O//V8VLnKf4gMcGYvpgA7zXwWjGTolH3soW+SdLWqvqJlbX39S+0ttlqHQFIB7B52cA40pKSnq37g+ofvbg6P0NWyZWn3le/3K/T/DYQOKfGSEgDHGMFpePbt2van/8ITe8oa7G9Snw//h9AdYGYnxquL0MbDhmSMt51nf0v3lhxsI5VSOrN8P+p9ftsYEYouFk4MMRdq8K3gP97pELjfctq6vZ+Ar0fztDLhuIiTQi7lV6xsgXIL8+e46bUzWx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',
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
      2 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQCAYAAACAvzbMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAALMhJREFUeNrsnc1vE1n2sG/ZTgghaU23ujWbDhqh2TTDBs0sZt1S/wPzH7RgM5qRRppm9K6bsGv9FoEdOyRWID4lEEIBNbAYJDbADjJiaLJo0aCGBBLIl/36FD6e45tb5XKwkyrX80hHVbbLZWOc+/ic+1HOAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAV6KcnQcAAPJP42MafoQBAEAG0rfjI0QDADB8mUbSfb008FHC7aiHcyEUAIBiCSPpmMZWGn0rjCjlPsQBADBcmUfDPt6tYQ/Jw0qikrAfdZEPAADkXxyNwLau+7WMJ63zuQIAlI5PvexDBRJvaz1kHwAAUC7GrDDMtp4mkAiRAAAgEF8azdjQ6FbCihAIAEBpGQ+IYz1NIHR8AwCAMGEzjmasme1atz4Q5AEAgEDa0mhFtRmVLCWsCp8hAEApmTTykNLVajNW1AtZRmGRhQAAlJNPPHlUnZnzV0uQhutFHo1Gg4+54Pj/hz/++KP7/vvv3ezsbMcx/nFRFG3aaoSOA4D8kOHvcrIlkNWWL1QgcZOQNozXzjDvK1NTU6mPz8/P7+j5ivQ++3FOXwpffvml++WXX9z6+rr7/PPP3cTERMfjc3NzHV9AK49KpRKfz4pk7969ufx38x0qzjnL+u8e5HczIxMBgQip80BCIoESUK/X3eLiYiwP4c2bN258fDwWg6KP+RmHysMeCwCFZsLIQ/+we5oHAkOMzT5EHlK6evv2bcd9r169cp999ln7vrW1tfa+yqJarcbH6m0VCgAUmnFPHpJ5rLdiJctaWEikBPKQfRHFoUOHNh33/v37OMbGxuLjVlZWOiQh242NjVgiElYsSASg0IwF5DHqPozEGqnx+SAP2Zfs4ejRo+6nn34KHv/69Wv3xRdfxEJYXV1t93PIbQnNQLSjXUUCAIVmlycPKWeNtKLKWliIJI4HDx64EydOJB4nclhYWHC/+c1v3Lt379r3a9YhUavV2jLyBcUoLIBCItmG/DFvGHFoZ3qVDITsI45Q6cpHS1kiEJWCymNkZCQuY8n9sh//8jAjtJAIQCGptTKPqomKBp3oyCMuXUkGkgUZlfX8+XO3Z8+edulKMg8ZmTU6OtoxVyQ0HwQACkVIHO3JhPRwllgkEvfv33fT09OZnydZxsmTJ93y8rJbWlqKQ/YlK9EMRfpIZKSWSMUO+WXCKUDhiGzG4f7XoR51y0BYyqQE2cfhw4d7Ps+FCxfcwYMH3YEDB9rlKxGF9n34kwtt+YpSFkCh0GVLQpcyJwMpszx6KV35HD9+3L148SLOPjQ0C5FhvhKSheickbTsg8wEINcZSJSwz0q7ZRSJxNOnT1NHXXXj5cuX7syZM/GkQy1jqUQkRCBayvIFERIGEgEohESc3WcUVkmzDxl1JXM7PoYbN264/fv3x+GXr6S0pXNE7GtTygIYHshASiiPmZkZd/v27b6c//Tp05uyDx3qKxmITjrU90EpC6Dw2QgCKaNItHR17Nixvp1Xlj+5cuVKLAy/hKUjspIEEZIKEgEohjwQSAkF0o/Slc/NmzfdkydP2vKwmYiEfx2R0DIqiAOgWPJAICWTx6lTp/pWuvI5d+5cu3Sl4tARWfr6aaUsshCA4oFASiIPyTqOHDkysNeR2enXr19PzECSZCHYRRiRCAACgZwJZBClKx/JbqSPxWYgEvZ9hCQRylAAAIFADgRy6dIld/ny5W15vWvXrm0SiC8Iv5Qly6OQhQAgEMgZknVsZbmSrfLs2TN3586djv6QpMxC71N5IBEABAI5YjtKVz5SypI+kSwZiOBnIJSyABAI7DCSAWxX6coiI69kwUWdC5IkEA1ZiFEkQikLAIFADpCGeLszD4uUsiQTsaOwkjILXx5IBACBwA6yuLgYN8o7iZayumUgeu0QSlkAxWIoFlOcn58vxHkH9T595Fe/rJK700gJ6+rVq+5f//qXm5iYcJOTk3HIvoRc1XB8fDwWiCy6KIsr2sUX7TVFNAvR/e36LMvwHSrCZ1mUf3dRvpdkIBBEfsHL+lR54fHjx+7hw4cd11O3kwz1uiFJ/SCUsgDIQGAAhBrTPJSufGQeyr59++Jrp0vIFQwl9LaEXf5dMxFdFt5eYx0AyEBgAOSldOUjWcb58+fbo7L8tbL04lOShUg2YjMROtQBEAhsQ/axsLCQ2/crpSy5fG5SGUuvHSISSStnAQACgT7LQ0pXev3xvCKlLBlarNKwGYi9BK4dkaXh/7uRCQACgT6IRBpeEUjeEUnMzs4mlrI0CxGJhERCKQsAgUCfs488jbrqxr1799yjR482ZR82A+llVBYA7Bw7MgqrbGOlByUSiW+//dZNT08X6r1fvHjRTU1NudHR0faIrLSRWbIVicgoLDsXhO9kfuHzLMdnSQZSYOTaG0WThyCd/VLK0pKVLV/ZLEQzkLQshGwEAIFAxqzD7stKu0Xl7t27bm5uri0LW7pSeci+7UgP9YMAAAKBHkUyMzPjbt26Veh/x9mzZ9vDdn2JdBvKi0QAdp4o4XalFdVmjDRjMesvYxh8BqIr1kpoQ6vlH51jsby87JaWluKJhRpyW+6X0HkYoaGzdkXcLX2poqgd2o+h/RrS77Fr1y43Njbmdu/eHa+HpWHXyNJ1suQ4OV77S+QcOmM9tG4WAPT2t9qFc81Yaca7Ziw1403LBzLpbIGlTAouEiG0DLot+YS+NNKwS2Osj0tjLALR54Zep5cvowpEG3p9PX/ZEj2/DX3v3fo+BtWhDgDZQCAFk0ba42nDXP2GXGWhjbzfz5D2mt3WprKr6PoSsaOt5LbKxK64G8q2/H8f4gBAINCHLCSLNLTxtuKwMtFf/PacWUSWJhF9zC6MaBdTlLKUzUokbPkLABAIbAP2V79Kw+970F/yIXnYJUNCoshSxvIbff89+aUs7Q9RofgZCTIByM9vVgQypOLwS0WhhtqWneQ+f9Xbfo9y8stYgp0YqJmISkS2tqM8JBIAyJdE+iIQ/rgBAMonEeaBAADAlkAgAADQLetohLIQBAIAAFnk4e+nCqTh2wYAAEpF3Yij4d9HBgIAAGmJRN2L9v2VDE8GAIBysmFCBaL7ZCAAAJDIuicRK5J6LSXziMz+p80Ya8V4MyZaMdmMT1pbvW+8ddyuZoy6D3NNqq3zVdzmFYDh44lSbkddjs1Lmpx0u0FGDLBtf4fav7HRksdqK9ZMtKXSy0TCunkBayN74tWWMCrmDa23BKLyiJDItkgkiyyiHf6y9vIY8gAY7N9jI9DGr3ph2/tMAvFPXPfkYS1l5aHHVT2BFOVX8TBmI0X5IiMOgJ35O2sE2nhp29+7D9cF0bZe2v21WsYX6SaPWkAe8tiIkUclIftAHtufmRTpiw0A2ycRv61Xgay0JGJFsp5FIKHUxpasqiasPFYDAvEbNOQBAJAPidh5HqG23gpEtqu1LuKIXOf4X+1Y0ROveHLQx0db8tCsROVSQRxkH2QhALn9m6ubtlyTAZuF6OVt4/1a4CRR4KShupjfr2EFs+KSy1fIAwAgnxJJKmOtu86+kPchgYRkktT/UfGO1cetPKxkGMILAJB/idQTkoZ1LxNZqWU8WeRlGM6TQajfg+wDAGA4shB/ykZc1uqWgTgvC4laoohasvA71q08QpMHEQgAQL4F4mci/ry/tkxqCSeIUrIQ/4U0+6i5zr4RSlcAAMUVST0hE8k8E91mIfWEF9BsY8OIw8rDkX0AABQuC3EBiXSsRJLWqPvzNSqBbSVBGkmZBxIBACiGPEKZSIdMelkryWYUtmPcF4btLGfmOQBAceXhX42wQyS9LrbnC6KSsM+aVwAAwyERf9u+qFSWBj1JAlGCMLayKiwAAORXJKGMpKeGPU0kWc+FSAAAiiWOpGMavTboUY+PIQwAgOHLRD6qgUcMAAAlJ8rZeQAAoJiZCQAAAAAAAAAAAAAAAAAAAAAAAAAAAAwe5oEAAECv9LwWFsIAAIAti4C1sAAASpZpJN3HarwAAJAmjKRjuB4IAABkzjx6uh4IVyQEACivOFKvSFjLeNI6nysAQOn41Ms+Oq6JXush+wAAgHIxZoVhtvU0gUSIBAAAgfjSaMaGRrcSVoRAAABKy3hAHOtpAqHjGwAAhAmbcTRjzWzXuvWBIA8AAATSlkYrqs2oZClhVfgMAQBKyaSRh5SuVpuxol7IMgqLLAQAoJx84smj6sycv1qCNFwv8mg0GnzMAAPG/zv78ccf3ffff+9mZ2c7jvGPi6Jo01YjdByUhwz/55Mtgay2fKECib9uacN47QzzvjM1NZX6+Pz8/I6er0jvswjnLOt77Nd5rRT27t3r6vW6++WXX9z6+rr7/PPP3cTERPvxubm5TY2ElUelUonPZ0Ui5+Q7VK5zZmQiIBAhdR5ISCQAkBMWFxdjeQhv3rxx4+PjsRgEvd8XiJWHHguQQSAqD/3S9DQPBAB2EJt9SOaxsrLi3r5923Hfq1ev3GeffdYWiH2OyqJarcbH6m0VCkAK4548JPNYb8VKlrWwkAhADuQh+yKKX3/9ddNx79+/j2NsbMytrq7GorCSkO3GxkYsEQkrFiQCKYwF5DHqPozEGqnx+QAUQx4ihaNHj8YiCPH69Wv3xRdfxCLR56hAJDQD0Y52FQlACrs8eUg5a6QVVdbCAiiASCQePHjgTpw4kXicyGFhYSEWiJWPZh0StVqtLSNfUIzCggCSbcgXZcOIQzvTq+SuAAXIPiQOHTrU9Xkij6tXr7p379655eXleKshfScSUuKSfhIJyWa03MVwfAjQloWJigad6AAFkIeUriQDycIPP/zgvvrqK7dnz5526UoyDxHG6Ohox1yR0HwQAENIHO3JhGQgADkWicT9+/fd9PR05uf9/PPP7uTJk3EGsrS0FIdmI9rZLlnI2tpaOxMJyQvA/W85Kxvt+1nKBCDn2cfhw4d7Ps+FCxfcwYMH3YEDB+IMZGRkJBaF9n34kwttHwj9IWDQZUtClzInAwHIszx6KV35HD9+3L148SLOPjQ0C9H+EMlCJLplH2Qmpc5AooR9VtoFyKNIJJ4+fZo66qobL1++dGfOnIknHWoZy3asa4e6CMQXREgYSASJWHkIzAMByGn2IaOuZG7Hx3Djxg23f//+OPzylZS2dI6IfW1KWZAVMhCAHMpjZmbG3b59uy/nP3369KbsQ8pYspUMRMKOzKKUBV2yEQQCkEeRaOnq2LFjfTuvLH9y5cqVTXNDpISlI7KSBBGSChJBHggEIKcC6UfpyufmzZvuyZMnHRML7bBe/zoioWVUEAfyQCAAOZbHqVOn+la68jl37ly7dKXi0BFZ+vpppSyyEEAgADmVh2QdR44cGdjrPH/+3F2/fj0xA0mShWAXYUQigEAAciaQQZSufCS7kT4Wm4HY1XuTJBHKUAAQCEAOBHLp0iV3+fLlbXm9a9eubRKILwi/lKWLLpKFAAIByBGSdWxluZKt8uzZM3fnzp2O/pCkzELvU3kgEUAgADliO0pXPlLKkj6RLBmI4GcglLIAgQDsMJIBbFfpyiIjr2TBRZ0LkiQQDb12CKUssEQJt+2673IFqsW0k2zlyzM1NZX6+Pz8PP87fJZD9Tn6HdIywe9Pf/qT++mnn3bs3/zNN9+4r7/+2k1OTrqJiYn2VkKuJ7J79+44du3aFV9LRK4rIqFLoOj11tsNiNnne5n/72aGpWrOuQ/XP3/XjKVmvGn5YEGCtbAAtoGklXZ3Uh6ClLL+8Ic/xMu9ixh0q6EXo7Ky0K1/YSooH5SwALZZJBK3bt36qJV2+4WUsOQSuP7EQnv5W12xVy9ARSkLEAjANmcfdsJgluubbxePHz92Dx8+bAvEn2So1w1JkgcSKS+UsAC2QR72dh5KVz4yD2Xfvn3t0pWUskLlrFApS+6nlEUGAgADFIn8as9L6cpHsozz58+3R2WFSlpSypIsRLIRm4lQykIgADDA7EP3v/vuu9y+XyllyeVzk8pY2h8iEkkrZwECAYA+yuNjr2++XUgpS/poVBqhTnXbH6IZiASZBwIBgD6LROL+/ftueno69+9XJDE7O5tYykoalZVUygIEAgBbzD709naudfWx3Lt3zz169GhT9mEzkF5GZcHwsmOjsJiFymc57J9jkUpXPhcvXoxnP8vscx2RlTYyS7YiEZ1gKFtZtFFgZNbw/o2TgQAMELn2RhFKVz4LCwtxKUtLVrZ8ZbMQzUDSshCykeEFgQD0Oeuw+3maMNgrd+/edXNzc21Z2NKVykP2bUc6/SAIBAD6IJKZmZl43keROXv2bHvYri+RbkN5kcjws2Or8QIMcwaiEwcltKHV8o/OsVheXnZLS0vu7du37ZDbcr+EzsMIDZ21F3fa0h9+FLVD+zG0X0P6PWT13bGxsXglXlmVV8Ou1CsxPj4eH6er9WofiV2t174m5EwArMYLkG+RCKEr+tmST+gPW1fC1celMRaB6HNDr9NLg6EC0YZeX89ftkTPb0Pfe7e+D+1Qh+EEgQD0WRppj6cNc/UbcpWFNvJ+P0Paa3Zbm0rvD0nEjraS2yoTfY7/2r4YEQcCAYA+ZiFZpKGNtxWHlYn+4rfnzCKyNInoY3ZhRLuYor2IlL5HW/4CBAIA24D91a/S8Pse9Jd8SB52yZCQKLKUsfxG339PfilL+0NUKH5GgkzK83sIgQDskDj8UlGoobZlJ7nPX/W236Oc/DKWYCcGaiaiEpGt7SgPiQTKJZFav/5AAACgXBJhHggAAGwJBAIAAN2yjkYoC0EgAACQRR7+fqpAGr5tAACgVNSNOBr+fWQgAACQlkjUvWjfX8nwZAAAKCcbJlQguk8GAgAAiax7ErEiqddSMo/I7H/ajLFWjDdjohWTzfiktdX7xlvH7WrGqPsw16TaOl/FbV4BGKAMRCm3oy7H5oFGyu1GynFQHLRc1WiJQuSx2oo1E22p9DKRsG5ewNrInni1JYyKeUPrLYGoPCIkAkgksyyiHW5QenkMeRRbHo1AG7/qhW3vMwnEP3Hdk4e1lJWHHlf1BFKUX1wAO5GNFKWxQRzFF0bodiPQxkvb/t59uC6ItvXS7q/VMr5IN3nUAvKQx0aMPCoJ2QfyAIRS7MYHhkcifluvAllpScSKZD2LQEKpjS1ZVU1YeawGBOL/sSAPAIB8SMTO8wi19VYgsl2tdRFH5DrH/2rHip54xZODPj7akodmJSqXCuIAGIoMnCxkOEVSN225JgM2C9HL28b7tcBJosBJQ3Uxv1/DCmbFJZevkAcAQD4lklTGWnedfSHvQwIJySSp/6PiHauPW3lYyTCEFwAg/xKpJyQN614mslLLeLLIyzD81DvU70H2AQAwHFmIP2UjLmt1y0Ccl4VELVFELVn4HetWHqHJgwgEACDfAvEzEX/eX1smtYQTRClZiP9Cmn3UXGffCKUrAIDiiqSekIlknolus5B6wgtotrFhxGHl4cg+AAAKl4W4gEQ6ViJJa9T9+RqVwLaSII2kzAOJAAAUQx6hTKRDJr2sw2MzCtsx7gvDdpYz8xwAoLjy8K9G2CGSXhdy8wVRSdhnzSsAgOGQiL9tX1QqS4OeJIEoQRhbWXEUAADyK5JQRtJTw54mkqznQiQAAMUSR9IxjV4b9KjHxxAGAMDwZSIf1cAjBgCAkhPl7DwAAFDMzAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAGD/NAAACgV3peCwthAADAlkXAWlgAACXLNJLuYzVeAABIE0bSMVwPBAAAMmcePV0PhCsSAgCUVxypVySsZTxpnc8VAKB0fOplHx3XRK/1kH0AAEC5GLPCMNt6mkAiRAIAgEB8aTRjQ6NbCStCIAAApWU8II71NIHQ8Q0AAMKEzTiasWa2a936QJAHAAACaUujFdVmVLKUsCp8hgAApWTSyENKV6vNWFEvZBmFRRYCAFBOPvHkUXVmzl8tQRquF3k0Gg0+Zigk/nf31atX7o9//KN7+vSp+7//+z/3t7/9rX2cf2wURXHofqVS6bhfH9P7APJGhu/lZEsgqy1fqEDiP4u0ZUrUMvKk5X4JZGpqKvXx+fn5nj6Afp+vSO+zCOfM+3uU7+7evXvbt1+/fu3evn0b74sQfvvb37r//Oc/rl6vJwpE5WG3Er/73e/4DvHvzt05exTI/zMCed8K8cFSM94xjBfIPlqsrKy05SGINCQjWVtba2cg9jmacVSr1fhYm4HoPkDBmTDZh36p26Oyun3LkQeUQh4igF9//XXTce/fv3eXL192q6urcYhkJPS2yEW26+vrbmNjIw45lwTAEDDeit3uw6RCiV3NGBWpZFkLC4nA0MpD9xcXF+PGP8Tf//539+9//9vt2bOn/RzNMiQ0A9EMRW7b4wAKzJjJPORX0XpLHjISa4Q8G0otEgnJImzpykfk8o9//CPORt69e9extRmJnEdCs5FQpgNQMDTb0BgxUa1kyDr4CQVDm31IhEpXPrOzs+7q1auxOJaXl+OthpWIyMMKBIlAwQmJQ0djVWt8PlBmeRw9ejTOGrLwww8/uK+++iouZWnpqlarxcIYHR3t6GSndAVDgjhiXYXh/jeMNw460aGUIpG4f/++m56ezvy8n3/+2Z08eTLOQJaWluLQbETKWRJayhKphPpakoQGkFNC4mhPJiQDgdJmH4cPH+75PBcuXHAHDx50Bw4ciDOQkZGRWBbaie5PILQd6XSqQwGJbMbh/tehHnXLQFjKBIZWHlK6evDgwZbOd/z4cffixYs4+9DwO9V17ki3TIMsBHKOTigPXco8YhQWlEokErJMyYkTJ7Z8npcvX7ozZ87EI7e0jGU71rVDPSQISllQwAwkSth3lLCgdNnHoUOH4iVLPoYbN264/fv3x+GXr6S0JZ3sSeUr3aekBQWUiLP7ZCBQKnnMzMy427dv9+X8p0+f3pR96BwRyUD85U/SshGyECgiCARKIRItXR07dqxv55V1sq5cubJpboiUsEQkSbKw7wmJQAGzEQQC5cs++lG68rl586Z78uRJx8RCzUS6SUL2Q6v8AhRBHggESiOPU6dO9a105XPu3LmO5U00rEBCpSxdcJEsBIooDwQCQy8SCck6jhw5MrDXef78ubt+/XpiBpIkN121l1IWFBUEAkMvkEGUrnwku5E+Fj8Dse8jqXwVehwAgQDssDwuXboUX89jO7h27VrXEpYVhb1+CFkIIBCAHMlDso6tLFeyVZ49e+bu3LnT7g9JyyzkPisPJAIIBCBHAtmO0pWPlLKkT0SH8aZlILrsO6UsQCAAORLIdpauLDIHRBZclG1aCUvCXjuEUhYgEIAcIFnHd999t2OvL6UsyUT8obu+DELyQCJQJAq/Ftb8/Hwhzjuo91nGf7c9pz8sVm7/85//dD/99NOOfo4ikL/+9a/uyy+/dBMTE25ycjLeaoyPj8cr9up11fUa67pGVujCVIP+LMv0N17Wz5IMBCCANrq3bt36qJV2+4WUsOQSuHZUli71rqv1hq6jTikLEAjANknDykNKV9JxnhceP37sHj582BaInWSoQkmTBxKBvMNy7lBoedjbcpGonS5d+Uhn/r59++Jrp0vIFQwl9LaELv/ul7Lkfq6xDmQgAAMUifxqz0vpykeyjPPnz7dX6A2VtKSUJVmIZCM2E6GUBQgEYIDZh+7v5KirbkgpSy6fm1TG0v6QtGG9iAMQCECf5fGx1zffLqSUJX00Ko1Qp7rtD9EMhBV7AYEADEAkEvfv33fT09O5f78iidnZ2cRSVtKoLEpZgEAA+px96O3tXOvqY7l375579OjRpuzDZiC9jMoCQCAAW8w+ilC68rl48aJbXFzsWsbqloEA5IEo4XalFdVmjDRjsZdfhgCDzEBk+9///tf9/ve/L+S/489//rP7y1/+0p6VrrPU9+zZE8fu3bvjGB0djUOH/dqhvjqsl+G98FEC6P79OdeMlWa8a8ZSM960fLAgQQYChZKH7udpwmCv3L17183NzbVLVrZ0pUN5Zd92pJOFQB5BIFA4kczMzMTzPorM2bNn26UqXyLdhvIiEchNBpNwmxIW5DID0YmDEtrQ6ggmnWOxvLzslpaW3Nu3b9sht+V+CZ2HERo6a1fE3WpJQENnmOuMcylH7dq1y42NjcUlKi1ZSWg5S2/LYotynBwfKmNJ9FCGAEj8vnYhtYTFUiZQWJEIoWXQbckn9AcjDbs0xvq4NMYiEH1u6HV6+UNUgWhDr6/nL1ui57eh7z1tBJbuIw7YaRAIFEYaaY+nDXP1G3KVhTbyfj9D2mt2W5vKdm77ErHrYMltlYk+x39tX4yIAxAIQB+zkCzS0MbbisPKRH/x23NmEVmaRPQxuzCiXUxRylI2K5Gw5S8ABAKwDdhf/SoNv+9Bf8mH5GGXDAmJIksZy2/0/ffkl7K0P0SF4mckyAQQCMCAxeGXikINtS07yX3+qrf9HuXkl7EElYPNRFQi/nyPkEgAdjrxRyAwVBmHbaRVHn7WYbMAlYc/x2JQy4T4mYQtqUmoNKxE/M52shHIs0Rq/f6DBgCAckiEiYQAALAlEAgAAHTLOhqhLASBAABAFnn4+6kCafi2AQCAUlE34mj495GBAABAWiJR96J9fyXDkwEAoJxsmFCB6D4ZCAAAJLLuScSKpF5LyTwis/9pM8ZaMd6MiVZMNuOT1lbvG28dt6sZo+7DXJNq63wVt3kJeYCPJcp4Xy+PD7os0MtjVAJgu76X2r+x0ZLHaivWTLSl0stEwrp5AWsje+LVljAq5g2ttwSi8oiQCGyjSAbxnEGIox/HA3zMd7MRaONXvbDtfSaB+Ceue/KwlrLy0OOqnkBCf7QIBfIilJ3+IwbYie9aw8tCbBsvbft79+HCUtrWS7u/Vsv4It3kUQvIQx4bMfKoJGQfyAMAIB8S8dt6FchKSyJWJOtZBBJKbWzJqmrCymM1IBBfGMgDACAfErHzPEJtvRWIbFdrXcQRuc7xv9qxoide8eSgj4+25KFZicqlgjgAAHIrkrppyzUZsFmIXh893q8FThIFThqqi/n9GlYwKy65fIU8AADyKZGkMta66+wLeR8SSEgmSf0fFe9YfdzKw0qGIbwAAPmXSD0haVj3MpGVWsaTRV6G4TwZhPo9yD4AAIYjC/GnbMRlrW4ZiPOykKgliqglC79j3cojNHkQgQAA5Fsgfibiz/try6SWcIIoJQvxX0izj5rr7BuhdAUAUFyR1BMykcwz0W0WUk94Ac02Now4rDwc2QcAQOGyEBeQSMdKJGmNuj9foxLYVhKkkZR5IBEAgGLII5SJdMiklwXnbEZhO8Z9YdjOcmaeAwAUVx7+1Qg7RNLriqW+ICoJ+6x5BQAwHBLxt+2LSmVp0JMkECUIYytLawMAQH5FEspIemrY00SS9VyIBACgWOJIOqbRa4Me9fgYwgAAGL5M5KMaeMQAAFByopydBwAAipmZAAAAAAAAAAAAAAAAAAAAAAAAAAAAwOBhHggAAPRKz2thIQwAANiyCFgLCwCgZJlG0n2sxgsAAGnCSDqG64EAAEDmzKOn64FwRUIAgPKKI/WKhLWMJ63zuQIAlI5Pveyj45rotR6yDwAAKBdjVhhmW08TSIRIAAAQiC+NZmxodCthRQgEAKC0jAfEsZ4mEDq+AQBAmLAZRzPWzHatWx8I8gAAQCBtabSi2oxKlhJWhc8QAKCUTBp5SOlqtRkr6oUso7DIQgAAysknnjyqzsz5qyVIw/Uij0ajwccMAAPBb19+/PFH9/3337vZ2dmOY/zjoijatNUIHVdGMvzbJ1sCWW35QgUSf+xpw3jtDPO+MzU1lfr4/Pz8jp6vSO+zCOcs63vkO/Rx57RS2Lt3r6vX6+6XX35x6+vr7vPPP3cTExPtx+fm5jY1jlYelUolPp8ViZyzjH+PPTAREIiQOg8kJBIAgB1lcXExlofw5s0bNz4+HotB0Pt9gVh56LHQk0BUHvrh9TQPBABg27HZh2QeKysr7u3btx33vXr1yn322WdtgdjnqCyq1Wp8rN5WoUAmxj15SOax3oqVLGthIREA2DF5yL6I4tdff9103Pv37+MYGxtzq6ursSisJGS7sbERS0TCigWJZGIsII9R92Ek1kiNzwcA8iwPkcLRo0djEYR4/fq1++KLL2KR6HNUIBKagWhHu4oEMrHLk4eUs0ZaUWUtLADIrUgkHjx44E6cOJF4nMhhYWEhFoiVj2YdErVarS0jX1BlHoWVAck25APbMOLQzvQqORwA5Db7kDh06FDX54k8rl696t69e+eWl5fjrYb0nUhIiUv6SSQkm9FyF9MQUmnLwkRFg050AMitPKR0JRlIFn744Qf31VdfuT179rRLV5J5iDBGR0c75oqE5oNAkJA42pMJyUAAIHcikbh//76bnp7O/Lyff/7ZnTx5Ms5AlpaW4tBsRDvbJQtZW1trZyIhecGmJKLiRft+ljIBgFxmH4cPH+75PBcuXHAHDx50Bw4ciDOQkZGRWBTa9+FPLrR9IPSHBNFlS0KXMicDAYD8yaOX0pXP8ePH3YsXL+LsQ0OzEO0PkSxEolv2QWbSIQ5/n5V2ASA/IpF4+vRp6qirbrx8+dKdOXMmnnSoZSzbsa4d6iIQXxAhYSCRTSuStPeZBwIAuco+ZNSVzO34GG7cuOH2798fh1++ktKWzhGxr00pq3fIQAAgN/KYmZlxt2/f7sv5T58+vSn7kDKWbCUDkbAjsyhlZc5GEAgA5EckWro6duxY384ry59cuXJl09wQKWHpiKwkQYSkgkQ2D6hCIACQC4H0o3Tlc/PmTffkyZOOiYV2WK9/HZHQMiqIIywPBAIAuZDHqVOn+la68jl37ly7dKXi0BFZ+vpppSyykGQQCADsqDwk6zhy5MjAXuf58+fu+vXriRlIkiwEuwgjEkEgAJAzgQyidOUj2Y30sdgMxK7emySJUIYCCAQAciCQS5cuucuXL2/L6127dm2TQHxB+KUsXXSRLASBAECOkKxjK8uVbJVnz565O3fudPSHJGUWep/KA4kgEADIEdtRuvKRUpb0iWTJQAQ/A6GUhUAAYIeRDGC7SlcWGXklCy7qXJAkgWjotUMoZYWJEm7bdd/lClSLaSfZyoc4NTWV+vj8/Dx/ZXyWfI5D+HlKQyxZQNIlareDb775xn399dducnLSTUxMtLcScj2R3bt3x7Fr1674WiJyXREJXQJFr7febjgHvPTJoL6bGd73Offh+ufvmrHUjDctHyxIsBYWAGwri4uLOyoPQUpZf/jDH+Ll3kUMutXQi1FZWejWvzBVmaGEBQDbhpSNZJXcnUZKWHIJXH9iob38ra7YqxegopSFQABgh5CGV9anyguPHz92Dx8+bAvEn2So1w1JkgcSYTl3ABgAocY0D6UrH5mHsm/fvnbpSkpZoXJWqJQl95e9lEUGAgADJy+lKx/JMs6fP98elRUqaUkpS7IQyUZsJkIpC4EAwDZkHwsLC7l9v1LKksvnJpWxtD9EJJJWziIDAQDoszykdKXXH88rUsqSSY0qjVCnuu0P0QxEwv93l0kmCAQABiYSaXhFIHlHJDE7O5tYykoalVX2UhYCAYCBZR95GnXVjXv37rlHjx5tyj5sBtLLqKwysGOjsJjVy2fJ5zi8IpH49ttv3fT0dKHe+8WLF+NZ3zL7XEdkpY3Mkq1IRCcYDmo0Vl6/m2QgANB35NobRZOHIJ39UsrSkpUtX9ksRDOQtCykDNkIAgGAvmQddl9W2i0qd+/edXNzc21Z2NKVykP2bUd6qB+kDCAQAOirSGZmZtytW7cK/e84e/Zse9iuL5FuQ3nLJJEdW40XAIYvA9GLL0loQ6vlH51jsby87JaWluKJhRpyW+6X0HkYoaGz9uJOW2rwoqgd2o+h/RrS7yGr746NjcUr8cqqvBp2pV6J8fHx+DhdrVf7SOxqvfY1cysAVuMFgDyKRAhd0c+WfEINmq6Eq49LYywC0eeGXqeXhlIFog29vp6/bIme34a+9259H4PsUM8TCAQA+iKNtMfThrn6DbnKQht5v58h7TW7rU2l94ckYkdbyW2ViT7Hf21fjGUSBwIBgIFnIVmkoY23FYeVif7it+fMIrI0iehjdmFEu5iivYiUvkdb/gIEAgDbgP3Vr9Lw+x70l3xIHnbJkJAospSx/Ebff09+KUv7Q1QofkZSQpk0EAgAbKs4/FJRqKG2ZSe5z1/1tt+jnPwylmAnBmomohKRre0oD4mkrBKp9euLAgAA5ZII80AAAGBLIBAAAOiWdTRCWQgCAQCALPLw91MF0vBtAwAApaJuxNHw7yMDAQCAtESi7kX7/kqGJwMAQDnZMKEC0X0yEAAASGTdk4gVSb2WknlEZv/TZoy1YrwZE62YbMYnra3eN946blczRt2HuSbV1vkqbvMKwAAwvEQpt6Mux+aBRsrtRspxRUTLVY2WKEQeq61YM9GWSi8TCevmBayN7IlXW8KomDe03hKIyiNCIgCllkgWWexkG9Ho8bFhkUcj0MavemHb+0wC8U9c9+RhLWXlocdVPYEU5ZcHAGxfNlKURnZYxJH0b2kE2nhp29+7D9cF0bZe2v21WsYX6SaPWkAe8tiIkUclIftAHgAIpYiN7jBkHaFkwbb1KpCVlkSsSNazCCSU2tiSVdWElcdqQCD+lwZ5AADkQyJ2nkeorbcCke1qrYs4Itc5/lc7VvTEK54c9PHRljw0K1G5VBAHABS4DRjWqQ1WHtqWazJgsxC9vG28XwucJAqcNFQX8/s1rGBWXHL5CnkAAORTIkllrHXX2RfyPiSQkEyS+j8q3rH6uJWHlQxDeAEA8i+RekLSsO5lIiu1jCeLvAzDT0FD/R5kHwAAw5GF+FM24rJWtwzEeVlI1BJF1JKF37Fu5RGaPIhAAADyLRA/E/Hn/bVlUks4QZSShfgvpNlHzXX2jVC6AgAorkjqCZlI5pnoNgupJ7yAZhsbRhxWHo7sAwCgcFmIC0ikYyWStEbdn69RCWwrCdJIyjyQCABAMeQRykQ6ZNLLejQ2o7Ad474wbGc5M88BAIorD/9qhB0i6XVBM18QlYR91rwCABgOifjb9kWlsjToSRKIEoSxlZU3AQAgvyIJZSQ9NexpIsl6LkQCAFAscSQd0+i1QY96fAxhAAAMXybyUQ08YgAAKDlRzs4DAADFzEwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACg9Px/AQYA7xZST05Cs7sAAAAASUVORK5CYII=',
          ),
        ),
      ),
    ),
  ),
);

?>
