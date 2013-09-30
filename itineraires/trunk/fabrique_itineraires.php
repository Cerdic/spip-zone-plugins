<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-09-27 16:12:36
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
    'nom' => 'Itinéraires',
    'slogan' => 'Lister des itinéraires de randonnées, vélo, cheval…',
    'description' => 'Ce plugin ajoute un nouvel objet éditorial permettant de décrire un itinéraire (distance, difficulté, etc).',
    'prefixe' => 'itineraires',
    'version' => '1.0.0',
    'auteur' => 'Les Développements Durables',
    'auteur_lien' => 'http://www.ldd.fr',
    'licence' => 'GNU/GPL v3',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.0;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => 'Configurer les randonnées',
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
      'nom' => 'Itinéraires',
      'nom_singulier' => 'Itinéraire',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_itineraires',
      'cle_primaire' => 'id_itineraire',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'itineraire',
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
          'recherche' => '8',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Texte',
          'champ' => 'texte',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '5',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Distance',
          'champ' => 'distance',
          'sql' => 'float not null default 0',
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
          'nom' => 'Dénivelé',
          'champ' => 'denivele',
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
          'nom' => 'Difficulté',
          'champ' => 'difficulte',
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
        5 => 
        array (
          'nom' => 'Départ',
          'champ' => 'depart',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Décrire brièvement le lieu de départ de cet itinéraire',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Balisage',
          'champ' => 'balisage',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'S\'il y a un balisage, préciser lequel (trait rouge, rond jaune…)',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Boucle',
          'champ' => 'boucle',
          'sql' => 'int(1) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'case',
          'explication' => 'Le parcours est-il une boucle ?',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Transport',
          'champ' => 'transport',
          'sql' => 'int(1) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'case',
          'explication' => 'Existe-t-il un transport autour de cet itinéraire ?',
          'saisie_options' => '',
        ),
        9 => 
        array (
          'nom' => 'Handicap',
          'champ' => 'handicap',
          'sql' => 'int(1) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'case',
          'explication' => 'L\'itinéraire est-il accessible aux handicapés ?',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Itinéraires',
        'titre_objet' => 'Itinéraire',
        'info_aucun_objet' => 'Aucun itinéraire',
        'info_1_objet' => 'Un itinéraire',
        'info_nb_objets' => '@nb@ itinéraires',
        'icone_creer_objet' => 'Créer un itinéraire',
        'icone_modifier_objet' => 'Modifier cet itinéraire',
        'titre_logo_objet' => 'Logo de cet itinéraire',
        'titre_langue_objet' => 'Langue de cet itinéraire',
        'titre_objets_rubrique' => 'Itinéraires de la rubrique',
        'info_objets_auteur' => 'Les itinéraires de cet auteur',
        'retirer_lien_objet' => 'Retirer cet itinéraire',
        'retirer_tous_liens_objets' => 'Retirer tous les itinéraires',
        'ajouter_lien_objet' => 'Ajouter cet itinéraire',
        'texte_ajouter_objet' => 'Ajouter un itinéraire',
        'texte_creer_associer_objet' => 'Créer et associer un itinéraire',
        'texte_changer_statut_objet' => 'Cet itinéraire est :',
      ),
      'table_liens' => '',
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
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA/5JREFUeNrsW91R4zAQVjJ+P1PBmQoQFZDMhHeuApIKgApIKgAqiKngcs88nK+CiAoQFZw74Kyb1bCItS3JNv7LzmiEw1iWvl3tfruWJ29vb2zMEpyfn++zPsnaH+jTDs1vljWete/Qz9H/tOYkNDV/AU3aPmCyWCxME5AIED1g06IWF0F/gq4/zZcAgBIJ89ZKFS4AmJISg/laSQiLm4FWI/jbWmGWAFBrSChAbADIQxibnX6Aab4R0igHAKqILwC5gPgC0PQEv2z8KRu5HAA4AHAA4ADAuKnwgNYiEQWejckCFKE5zdox5AqqHWVtMxYAIoLrK6a3BmAa2wI6aXqF65MWAVhmLc5JstIiCu4LgHrYqkNWcJe1nZGkqd+um7KAWce2gdLwb9C4QNpvzAcos7voGAgc9dz2pipO8HIMRGhXEE6UBWyz9jdrLx3cFpWJUAqOLgWPf0d406WxBycdWtsN8gcJKCp0sYAH5FVjIBhpj5QrYOEJunbaAjExYMoGJtMC9CRh7pEjRe0tAEkNXn/VZwBeayA/MoeeujJOX7HarkHDbO8GwqVPOXwDCc0vCLeuY1wYZI0kR02WxbVcQwh19eCnBNXFv/WmLB47Ro+U8B/pV/uAupOU0HHbmDF77cLvuwIAB4rMHbUviHFu+2gBeZov2hI4rdXXPz2eLcGJ3rOSt8PTkgHm4HgmkBi5mn9eIeUIxp0TyRYGYetIvvD817Cd5tA7h8G1cf1cQw1gY3h6PdlbAoR9m0QoIjRYldrGjD65IdvMMaYWFZa6AAgLzFm0BUAZE0yIifqGI83MBLA7yRzO8rQBwFlOklQ1HnPWUEwvUCT3AYATg3DWD5nZUuWgZM++eIahLgr5gqSMCA1l8Sr8HlOOfGivx9MCUpRSxGtIACTAMO+N383M8pstALEH/W1T8yuUTf6A32LD7GfMeF8YFAx4A73yA8usXbHqBx2bkgeDU+wYXcW+s/UBG3QzTiy6Klfs/SVNHsUmawoUADtiH7Emc/KaCi9bSJ3zrPTSJhcQjC5nr3sSEi8KsshT3zDIO659Ux4L/FppGORGJUebVl9EGts3JLbEPfYN0xyN6zLUtkf8X8f81PBbe2MNH94xBAXJRN/ygJ2xxzmK+XswfcGMqlYwoDxAJzsp0nRpFBvSSdElaH1VVgMYKgDa7PfMocY41MPS4dgBYAcALKUOHyAZXeFNWD2fyjUl/w9RBR43qfbM3k9h5ckchdOIvX8s2VZxVc/3wyfCZecEE1hswvxfjGjrSAiPHUF/hoCqS/RiBSv4JjowJopvEl+gEcE+V510DMfWElosViJlCRcfsEIDdEEoa6OsQ20x+fT0VGne/wQYADu6Dk6X0nlCAAAAAElFTkSuQmCC',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA/5JREFUeNrsW91R4zAQVjJ+P1PBmQoQFZDMhHeuApIKgApIKgAqiKngcs88nK+CiAoQFZw74Kyb1bCItS3JNv7LzmiEw1iWvl3tfruWJ29vb2zMEpyfn++zPsnaH+jTDs1vljWete/Qz9H/tOYkNDV/AU3aPmCyWCxME5AIED1g06IWF0F/gq4/zZcAgBIJ89ZKFS4AmJISg/laSQiLm4FWI/jbWmGWAFBrSChAbADIQxibnX6Aab4R0igHAKqILwC5gPgC0PQEv2z8KRu5HAA4AHAA4ADAuKnwgNYiEQWejckCFKE5zdox5AqqHWVtMxYAIoLrK6a3BmAa2wI6aXqF65MWAVhmLc5JstIiCu4LgHrYqkNWcJe1nZGkqd+um7KAWce2gdLwb9C4QNpvzAcos7voGAgc9dz2pipO8HIMRGhXEE6UBWyz9jdrLx3cFpWJUAqOLgWPf0d406WxBycdWtsN8gcJKCp0sYAH5FVjIBhpj5QrYOEJunbaAjExYMoGJtMC9CRh7pEjRe0tAEkNXn/VZwBeayA/MoeeujJOX7HarkHDbO8GwqVPOXwDCc0vCLeuY1wYZI0kR02WxbVcQwh19eCnBNXFv/WmLB47Ro+U8B/pV/uAupOU0HHbmDF77cLvuwIAB4rMHbUviHFu+2gBeZov2hI4rdXXPz2eLcGJ3rOSt8PTkgHm4HgmkBi5mn9eIeUIxp0TyRYGYetIvvD817Cd5tA7h8G1cf1cQw1gY3h6PdlbAoR9m0QoIjRYldrGjD65IdvMMaYWFZa6AAgLzFm0BUAZE0yIifqGI83MBLA7yRzO8rQBwFlOklQ1HnPWUEwvUCT3AYATg3DWD5nZUuWgZM++eIahLgr5gqSMCA1l8Sr8HlOOfGivx9MCUpRSxGtIACTAMO+N383M8pstALEH/W1T8yuUTf6A32LD7GfMeF8YFAx4A73yA8usXbHqBx2bkgeDU+wYXcW+s/UBG3QzTiy6Klfs/SVNHsUmawoUADtiH7Emc/KaCi9bSJ3zrPTSJhcQjC5nr3sSEi8KsshT3zDIO659Ux4L/FppGORGJUebVl9EGts3JLbEPfYN0xyN6zLUtkf8X8f81PBbe2MNH94xBAXJRN/ygJ2xxzmK+XswfcGMqlYwoDxAJzsp0nRpFBvSSdElaH1VVgMYKgDa7PfMocY41MPS4dgBYAcALKUOHyAZXeFNWD2fyjUl/w9RBR43qfbM3k9h5ckchdOIvX8s2VZxVc/3wyfCZecEE1hswvxfjGjrSAiPHUF/hoCqS/RiBSv4JjowJopvEl+gEcE+V510DMfWElosViJlCRcfsEIDdEEoa6OsQ20x+fT0VGne/wQYADu6Dk6X0nlCAAAAAElFTkSuQmCC',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAOJJREFUeNqkU4ENwiAQBOMAsAGdwI5QEwbQDVzBEdygG+gGuoF0g26gG9gN6H/yn7wIbWM/uQTIc9z9P9p7H5VSAdABegKGAzSAHa1rgAZEkddpIkiDE2fPN2plpAQD2ZmKGynJEqCv/QzBSdpggkCsTcE7q5MPaUnwBlwnXh2SvQHccbEVsnKXDK0tXxAtdlLBA9AW2taTrYM4D6kFZDtnZL74pSTw7Ckt1IXiuUJNDHUrsoJ2Qf+VKLjlDStA+Z+FBF+qmCD+McVxzV/4mUSswRFQiTmvqFAXarMMzrejAAMAeLEusz95Z3oAAAAASUVORK5CYII=',
          ),
        ),
      ),
    ),
  ),
);

?>