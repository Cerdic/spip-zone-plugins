<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-11-13 11:11:04
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
    'nom' => 'Annonces services',
    'slogan' => '',
    'description' => 'Extension de spipad pour les annonces de services à la personne',
    'prefixe' => 'ad_service',
    'version' => '1.0.0',
    'auteur' => 'Collectif SPIP - Montpellier',
    'auteur_lien' => 'http://montpel-libre.fr/',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.5;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
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
      'paquet' => '<necessite nom="saisies" compatibilite="[1.21.0;]" />
<necessite nom="spipad" compatibilite="[1.0.1;]" />',
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
      'nom' => 'Annonces Services',
      'nom_singulier' => 'Annonce Service',
      'genre' => 'feminin',
      'logo_variantes' => 'on',
      'table' => 'spip_ad_services',
      'cle_primaire' => 'id_ad_service',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'ad_service',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre de l\'annonce',
          'champ' => 'titre',
          'sql' => 'varchar(256) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '10',
          'saisie' => 'input',
          'explication' => 'Un titre pour votre proposition de service (important pour la recherche)',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Type de service',
          'champ' => 'type_service',
          'sql' => 'int(6) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '10',
          'saisie' => 'selection',
          'explication' => 'Sélectionner votre type de service',
          'saisie_options' => 'datas=[(#ARRAY{1,Travaux /jardinage,2,Ménage repassage,3,Soins à la personne,4,Garde d\'enfant,5,Cours particuliers,6,informatique})]',
        ),
        3 => 
        array (
          'nom' => 'Lattitude',
          'champ' => 'lattitude',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'recherche' => '10',
          'saisie' => 'hidden',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Longitude',
          'champ' => 'longitude',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'recherche' => '10',
          'saisie' => 'hidden',
          'explication' => 'longitude en valeur décimale',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Centre de la zone',
          'champ' => 'centre',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '10',
          'saisie' => 'input',
          'explication' => 'Ville au centre de votre zone d\'intervention',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Rayon d\'intervention',
          'champ' => 'rayon',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'A combien de kilomètres accepter vous d\'intervenir',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Descriptif',
          'champ' => 'descriptif',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '10',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Tarif Horaire',
          'champ' => 'tarif_horaire',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '5',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        9 => 
        array (
          'nom' => 'Déduction fiscale',
          'champ' => 'deduction_fiscale',
          'sql' => 'varchar(3) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '5',
          'saisie' => 'oui_non',
          'explication' => '',
          'saisie_options' => '',
        ),
        10 => 
        array (
          'nom' => 'CESU',
          'champ' => 'cesu',
          'sql' => 'varchar(3) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '10',
          'saisie' => 'oui_non',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'langues' => 
      array (
        0 => 'lang',
        1 => 'id_trad',
      ),
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Annonces services',
        'titre_objet' => 'Annonce service',
        'info_aucun_objet' => 'Aucune annonce service',
        'info_1_objet' => 'Une annonce service',
        'info_nb_objets' => '@nb@ annonce(s) service(s)',
        'icone_creer_objet' => 'Créer une annonce service',
        'icone_modifier_objet' => 'Modifier cette annonce',
        'titre_logo_objet' => 'Logo de l\'annonce',
        'titre_langue_objet' => 'Langue de cette annonce service',
        'titre_objets_rubrique' => 'Annonce service de la rubrique',
        'info_objets_auteur' => 'Les annonces services de cet auteur',
        'retirer_lien_objet' => 'Retirer cette annonce',
        'retirer_tous_liens_objets' => 'Retirer tous les annonces de service',
        'ajouter_lien_objet' => 'Ajouter ce ad service',
        'texte_ajouter_objet' => 'Ajouter une annonce service',
        'texte_creer_associer_objet' => 'Créer et associer une annonce service',
        'texte_changer_statut_objet' => 'Cette annonce service est :',
      ),
      'table_liens' => '',
      'vue_liens' => 
      array (
        0 => 'spip_ads',
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
        1 => 'outils_rapides',
      ),
      'saisies' => 
      array (
        0 => 'objets',
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
          32 => 
          array (
            'extension' => 'png',
            'contenu' => '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',
          ),
          24 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3AsNCSo7/SOBuwAAA9dJREFUSMftlV2IVVUUx39rn3Pv3Ou9d9JhnDvjF4PFmA5EgRREjA9Z+lD2YA/WQ4HIIIEgBRFSQaTQgxWUGg5YZFFGHxIiJFIIFWqlUJHV+JXaMIMfd8Z77se555y9Vw9zG5oata+32rBhsdj/vfZa//9eC/5f11jyZw71fxcy0JvhkWONJYAU8+kDQ0GDgd7MPw+w6miFnO/NRtgvsBBA4bgqy2pRcnrX4sJV8f61AljEryT2FEpq4jnKDQgnEpFpQONqePN7h6p2qOqSpk0livvLUZIux4mUo4RylNC0TS1O1v0G16eqXVfNoB67h8JEX2/aAdBxObILZOpKqkMXqaoXJnohTHRGE7c2mzLbpwwQRG6LggKiUKjG8uSFWnI448u4dzJ7Elk9NFp3j0dOZ4hMkPoSsH3KEo2FVsdCy6/7csPq8eXFt8qRPV+OrZZjS3NrObJjPy4rDow2rI41LKPjGB0LrV6Rg8hpfyl0cil0BJFemplJber5aJjQanc1se9UYluqxLZUTez7VZfMnbt3mPnTU88HsY6UQkcpdNJwuvaKHPS2t3zpVLfWE+3LpczLIhICyNYfFpPzRzEcBwTlIg23WPt7DgjEqrqhGrv1WV8OGpHPp/wHiXUbFDZNysjqaO7NU1+l86m7nBFETFMxTo1Tier2QPmB7kUZTzpk8qUbfc88NREgjN0q4O0JeaAoMG/3OWqewROhtRLQmxVUlW+qSn16gcRByilD983BE5ikNmFNxjc7pBa5VqtuWCEr4+qhkDLcvm+E7xsWT+BOV2Vg+fVcl/GbYkh4eM8gB9OtxMD8jMeRuzsJYoeM/3QVSDwxXQbwYyeDkUUih8YO3v2pxtd1h+953BjV2HlvD3tOjrLt6DCJA88Y3lu5kK7gMinfZ7Du2Hm6RuIgcmhkkdjJITwaxkGpLevdgrIudpxtEWHjYEB7xjDNF5Z3ZhERPhy8xK1drVysJ9QSpRIp98zJkfOgrcXw3ImAlEDiOAGsact6fc5SMfn0OHHtOX9LZ87vzmc8GY30gxZjyBqhkPYYqSQ82Ftk8xc/I2KILZQbjkLKJytoWgyVIHmxkPGlmPN72qf5OwDyafPHXjR79xDnVsxaqaF7wojHt6WQBOGmYgHrYNuRIXYdO4+KcORCFSOe2CBefe7+uY/+5Xbd+dqZwm0dunlpMdPfN68NEXjh8Blm5VvozKfZeyZ4Zd8n1XVsvdn+rXmw+rMSr97Rxvr9Jx+LnGzunZmnxTMcHSkTNOKn31ix4Nl/ZaI123H6mU/PLo2cM5uWdH8sIvX/xtD/BdAUAJ/Povq7AAAAAElFTkSuQmCC',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAYAAABWdVznAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3AsNCSsv/uJkhwAAAZZJREFUKM+tkU9IFHEcxd93GnddXcVIECIV7BhIYEeFIjopRNekTnURoi518CZ0KogKPAXhRVAjT/0xM13wZNjBQyK0sbKzuuPKOs02++c385vf8xB20W59Tg/ee5f3gP/BZInWC5fWSZ4AAEmICF5tV7HscbBJZBUAIvLKyBnJjPa0/s1YfwpIK22m7vS29Lu18HmhGqJQDeHWwpc3u1suKG2mjGE7ANgA4FSiRxDcskQubnqNlXbbGgCAIObqTkW/jsFLIHYBjEPHBvNZ394qq/xGSV0GgNRMbik1m1sGgI19NbRVVs6SEyR1bCCRZsKAZZLp736Igc97qi1WSRogSCTDr1e7Ev0dCYhITU5JJ/yGfuzVI1Ya2jS9cXh3Jc/N/YAkeXsxR/utw191bbx6RL+hJwAAxSC6fm/9YP7cxyK/FSocz2zTV4Zfsgfs/lDkg/XytBvo4WMb974vjT5ZKwaZfNWMLfw0T9d2f/e9K9049sMR1Fnc/6TamlPpZ6ebbVn44T7sOtvhzV07/++XSZ6ojzgEIj3h7pmIp2sAAAAASUVORK5CYII=',
          ),
        ),
      ),
    ),
  ),
);

?>