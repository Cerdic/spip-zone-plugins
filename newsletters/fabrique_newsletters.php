<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-12-03 15:10:38
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
    'nom' => 'Newsletters',
    'slogan' => 'Composer des Infolettres',
    'description' => '',
    'prefixe' => 'newsletters',
    'version' => '0.1.0',
    'auteur' => 'Cedric Morin',
    'auteur_lien' => 'http://nursit.com',
    'licence' => 'GNU/GPL',
    'categorie' => 'communication',
    'etat' => 'dev',
    'compatibilite' => '[3.0.5;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '0.1.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configuration des Newsletters',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'fonctions',
      2 => 'options',
      3 => 'pipelines',
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
      'nom' => 'Newsletters',
      'nom_singulier' => 'Newsletter',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_newsletters',
      'cle_primaire' => 'id_newsletter',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'newsletter',
      'champs' => 
      array (
        1 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Chapo',
          'champ' => 'chapo',
          'sql' => 'mediumtext NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Texte',
          'champ' => 'texte',
          'sql' => 'longtext NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        9 => 
        array (
          'nom' => 'Date redac',
          'champ' => 'date_redac',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'langues' => 
      array (
        0 => 'lang',
      ),
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Infolettres',
        'titre_objet' => 'Infolettre',
        'info_aucun_objet' => 'Aucune infolettre',
        'info_1_objet' => 'Une infolettre',
        'info_nb_objets' => '@nb@ infolettres',
        'icone_creer_objet' => 'Créer une infolettre',
        'icone_modifier_objet' => 'Modifier cette infolettre',
        'titre_logo_objet' => 'Logo de cette infolettre',
        'titre_langue_objet' => 'Langue de cette infolettre',
        'titre_objets_rubrique' => 'Infolettres de la rubrique',
        'info_objets_auteur' => 'Les infolettres de cet auteur',
        'retirer_lien_objet' => 'Retirer cette infolettre',
        'retirer_tous_liens_objets' => 'Retirer toutes les infolettres',
        'ajouter_lien_objet' => 'Ajouter cette infolettre',
        'texte_ajouter_objet' => 'Ajouter une infolettre',
        'texte_creer_associer_objet' => 'Créer et associer une infolettre',
        'texte_changer_statut_objet' => 'Cette infolettre est :',
      ),
      'table_liens' => 'on',
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
      'boutons' => 
      array (
        0 => 'menu_edition',
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
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAEv0lEQVR42r2W3U9bdRjHF8LlYrwgXhv/AOMf4IXxzpvptdnFohs4xstAjPFlGWJMlBXcpo6piYsbytxWSpnI63AuWaKZF7LBVl5KS0/fX2gLbSmj4/H3+WHaslJamOxJvjm/8/ye7+d5Tk/PafeJyL5dREWj6eKh9lOda4g1ud2AdjNAZedXXb50Oi2p1VUt1uTY29MBDrZdeOFS98/Ksi6haEz+umtDrHWOvTfbfnh+LwaoeO9sz8GxG78L4YssysS0fZPIEYPDo0ItnicZoLLx/baBluYWOd7YLI0NTVJfd1xqaxukuuaYVFcfk5qazSLHHjXU4sELAxbMnQyw/92mFnmYyUgqnd6k5MrKdiqohwEL5k4GqGqobxJi0maXqRmH2OwLMj3vKkvU4sFLwIJZ9gBv1J54ua6uUYgHs06Ztruk/ePXpOPkAelofX1bUUMtHrwELJjlDFDRYrrQPDA4LEeP1gsxM2/InNMjDrdPnGWKWjx4CVgwYdOj2ACVJ9q/sYzf/EOII+pL9Wh9XeZdXnEYXnF5A2L4ghy1FjxaW55zxIdgwCJg04Nejw9Q2Wo6d33qvk1WMxkxgmE5fKQWs4a5VeOu9lY5f6pNRvp7ZcRqluG+q+INReTc5ycLzlkb/w0CAxZM2PSgFz3zB9ivXqm6YN7jEyMQkrcP10pGmT3+kHgDYQksxiQYjemjP7KIUNFzfAwOAxZM2PSgFz3zB6j6or1Dkum0WAeGhTj01jv6EfIHIxIIL6qr/0TGrltkVF1tOBrPnff3FuRG1CfgD0XwwtAsAjY96EXP/AGeI5lQz/Gdezax/jokdycmZHVtTULqiiIKHk8mtaLLCa0ltV5KrehcLKHzHLM1DIQXBiyYsOlBL3oWDAD03qxDbt/5Rz784CMhovFliSkgV3djgCs267rvOj6V7zs/k/HfrOqqe7P5b01trPHgBQELJmxqig/A9JPqBfJAvUhu/fm3mC19QiSSuTddIrWh1MavIcdcPskxtyZgwIIJO15qgKk5p9y3O8XmMGRw7KZcM1uESK2oRgrMkY917VEmp0yGHPvZGgIvDFgwYW87APeQSZFNvVJnFjzSc7VXevv6haARwfmZr7vytVUNXhiwYCJ6bD/AtMPIalaZ7W6v/Njdo4BWIfqs/XLxp8vcS+oRa3J6j6AWD14YecwSAygQhnzNGV6eX32/L6km18x94vAGdH7G6UasybFHDbV4yBfwYqVuAaZ8cRV80bovX1HP8ZAs+INZeJ7IsUcNtXjwFtSVvAVciUPBkFOteW67e36R8Vu39dtswaf3txR71FCLB68zx4Nd+hYAwcRxObUiV8wWDXQHw+LyBdnbVtRQiwcvjHxmrNRjaPhDiLUGTNpm9Y+MW+WAlyFq8eCFUcAt/iZUH5k3HOFVqo1TM3P82dQwTyC8E+HBCwMWTNj0KD7Acnrj3T40Ns5vPwBMwHYjvDBgwYRNj+IDJNJpGRodF7c/mP1Z9T2ZYMCCCZsexQdIPlyVxURCwvElrVAs/n8IFoJNj60HMHWe5s+CLkjskWDTg16FA3x5dpkNpttL6R6m08bjf0ieVXpJ6dWnoFeUXlR6ZtOfUobgk3gKqqI5PWn8Ly5BGmTiRK4WAAAAAElFTkSuQmCC',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAEv0lEQVR42r2W3U9bdRjHF8LlYrwgXhv/AOMf4IXxzpvptdnFohs4xstAjPFlGWJMlBXcpo6piYsbytxWSpnI63AuWaKZF7LBVl5KS0/fX2gLbSmj4/H3+WHaslJamOxJvjm/8/ye7+d5Tk/PafeJyL5dREWj6eKh9lOda4g1ud2AdjNAZedXXb50Oi2p1VUt1uTY29MBDrZdeOFS98/Ksi6haEz+umtDrHWOvTfbfnh+LwaoeO9sz8GxG78L4YssysS0fZPIEYPDo0ItnicZoLLx/baBluYWOd7YLI0NTVJfd1xqaxukuuaYVFcfk5qazSLHHjXU4sELAxbMnQyw/92mFnmYyUgqnd6k5MrKdiqohwEL5k4GqGqobxJi0maXqRmH2OwLMj3vKkvU4sFLwIJZ9gBv1J54ua6uUYgHs06Ztruk/ePXpOPkAelofX1bUUMtHrwELJjlDFDRYrrQPDA4LEeP1gsxM2/InNMjDrdPnGWKWjx4CVgwYdOj2ACVJ9q/sYzf/EOII+pL9Wh9XeZdXnEYXnF5A2L4ghy1FjxaW55zxIdgwCJg04Nejw9Q2Wo6d33qvk1WMxkxgmE5fKQWs4a5VeOu9lY5f6pNRvp7ZcRqluG+q+INReTc5ycLzlkb/w0CAxZM2PSgFz3zB9ivXqm6YN7jEyMQkrcP10pGmT3+kHgDYQksxiQYjemjP7KIUNFzfAwOAxZM2PSgFz3zB6j6or1Dkum0WAeGhTj01jv6EfIHIxIIL6qr/0TGrltkVF1tOBrPnff3FuRG1CfgD0XwwtAsAjY96EXP/AGeI5lQz/Gdezax/jokdycmZHVtTULqiiIKHk8mtaLLCa0ltV5KrehcLKHzHLM1DIQXBiyYsOlBL3oWDAD03qxDbt/5Rz784CMhovFliSkgV3djgCs267rvOj6V7zs/k/HfrOqqe7P5b01trPHgBQELJmxqig/A9JPqBfJAvUhu/fm3mC19QiSSuTddIrWh1MavIcdcPskxtyZgwIIJO15qgKk5p9y3O8XmMGRw7KZcM1uESK2oRgrMkY917VEmp0yGHPvZGgIvDFgwYW87APeQSZFNvVJnFjzSc7VXevv6haARwfmZr7vytVUNXhiwYCJ6bD/AtMPIalaZ7W6v/Njdo4BWIfqs/XLxp8vcS+oRa3J6j6AWD14YecwSAygQhnzNGV6eX32/L6km18x94vAGdH7G6UasybFHDbV4yBfwYqVuAaZ8cRV80bovX1HP8ZAs+INZeJ7IsUcNtXjwFtSVvAVciUPBkFOteW67e36R8Vu39dtswaf3txR71FCLB68zx4Nd+hYAwcRxObUiV8wWDXQHw+LyBdnbVtRQiwcvjHxmrNRjaPhDiLUGTNpm9Y+MW+WAlyFq8eCFUcAt/iZUH5k3HOFVqo1TM3P82dQwTyC8E+HBCwMWTNj0KD7Acnrj3T40Ns5vPwBMwHYjvDBgwYRNj+IDJNJpGRodF7c/mP1Z9T2ZYMCCCZsexQdIPlyVxURCwvElrVAs/n8IFoJNj60HMHWe5s+CLkjskWDTg16FA3x5dpkNpttL6R6m08bjf0ieVXpJ6dWnoFeUXlR6ZtOfUobgk3gKqqI5PWn8Ly5BGmTiRK4WAAAAAElFTkSuQmCC',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAB4ElEQVR42p2TX09ScRzGeyFe+Qpab6OLfB+lMwbjpkRSc2HHnJQ3sjmLBA5Z66rGaulm1noDWvTHIkGGOJA/hxPo0/n8Lo4xXGv9tu++e57n833YYFw4742vvh2KLTwo3Z1fkHVvsRtceXPxPE7SoDmdeBpNZbKqNZrq9Lpmo2eX7fhfCwKPtoduRGYK4WBYgeshjY0FNDoaYBuNTw43UDCxlJ5KptIKBkI6clqqNBoDg08ON7GUifoF0/GV9e33H3Ryemo+seY4mrt5WVbkiqzJETYan9xw8LcXlzdNQWxu3ph7pbKuXhtX0+2o7sH1jqNjb9hofHI4eO5MwZ2YJbfX0/MXOYWCYfPFZRNx2Yn7/qDxyeHgufML2q6r3a8/tJpMaedjXt2THgf+oPHJ4eD7ChptR7vfCvr0fV8PH6f1MvdKfzw0PjkcfH9BvdVS3gs/F4rirdnrmrw1o0h0io3GJoeDHyzYK5bluL+UTNtay2RVPKyqWKmy0fjkcIMFx21HrU5H9pNn2th6Z472y4f6eVBho/HJ4eD7C5qOo9zrDe3kv6hSq+ugeqTS2aDxyeHgzwr4w/CztFxXTa+94YVm2v74HjkcPHemIDJrbSJo/NeB544C3rA3l/5jhin4DS3LudN0N6QwAAAAAElFTkSuQmCC',
          ),
        ),
      ),
    ),
  ),
);

?>