<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-09-22 08:08:16
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
    'nom' => 'Annonces',
    'slogan' => 'Les annonces gérées par spip',
    'description' => 'Créer et gérer des annonces avec SPIP',
    'prefixe' => 'spipad',
    'version' => '1.0.1',
    'auteur' => 'apéro spip',
    'auteur_lien' => 'http://montpel-ibre.fr',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.3;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configuration des annonces',
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
      'post_creation' => 'include_once($destination_ancien_plugin . \'fabrique_spipad_post_creation.php\');
fabrique_spipad_post_creation($data, $destination_plugin, $destination_ancien_plugin);
',
    ),
    'exemples' => 'on',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Annonces',
      'nom_singulier' => 'Annonce',
      'genre' => 'feminin',
      'logo_variantes' => 'on',
      'table' => 'spip_ads',
      'cle_primaire' => 'id_ad',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'ad',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '5',
          'saisie' => 'input',
          'explication' => 'Titre de l\'annonce',
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
          'explication' => 'Texte de l\'annonce',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'PRIX',
          'champ' => 'PRIX',
          'sql' => 'varchar(255)',
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
          'nom' => 'Type annonce',
          'champ' => 'type_annonce',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'mot',
          'explication' => '',
          'saisie_options' => 'id_groupe= #CONFIG{spipad/grpmot_type}',
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
        1 => 'id_trad',
      ),
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Annonces',
        'titre_objet' => 'Annonce',
        'info_aucun_objet' => 'Aucune annonce',
        'info_1_objet' => 'Une annonce',
        'info_nb_objets' => '@nb@ annonces',
        'icone_creer_objet' => 'Créer une annonce',
        'icone_modifier_objet' => 'Modifier cette annonce',
        'titre_logo_objet' => 'Logo de cette annonce',
        'titre_langue_objet' => 'Langue de cette annonce',
        'titre_objets_rubrique' => 'Annonces de la rubrique',
        'info_objets_auteur' => 'Les annonces de cet auteur',
        'retirer_lien_objet' => 'Retirer cette annonce',
        'retirer_tous_liens_objets' => 'Retirer toutes les annonces',
        'ajouter_lien_objet' => 'Ajouter cette annonce',
        'texte_ajouter_objet' => 'Ajouter une annonce',
        'texte_creer_associer_objet' => 'Créer et associer une annonce',
        'texte_changer_statut_objet' => 'Cette annonce est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_articles',
        1 => 'spip_auteurs',
        2 => 'spip_documents',
        3 => 'spip_groupes_mots',
        4 => 'spip_messages',
        5 => 'spip_mots',
        6 => 'spip_rubriques',
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
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA4VJREFUeNrEV0tME0EY/neWEhGUgiHxAW1BWgQaIpUQEyU0qCcvXkw8mGjiwYN3zybGBC/e1EQxJl6UePJAjBeVhPgi9oGl0JZHLU+DVaiUIlDrzOzu7Lbbx5aWMOnk/3e7s//3f/PNP7NcIpGA3WwlqTesBq4Cm6M7FG/SE0qsZgVAgnvGXK75YBAOmbthITBI7dayN79QHId/iHbC8fJKJGy3nz2DXXcuAHRwLBaDeDyeZPOLjwCQAIDDFsi1linQ2t4ODoFeXwkV5XtBp9PBdDAE9SYDtT32LpEBqSPyzJ6iAujpPp10XW80CNZkZIGBdkR9hBAqGMDE7J9tgW22HAZIQOFT0Fi7T4PwRPEhns49tZSFIgDweH3w4uUA9S9dPA/WliZN7LQeqxNEWSiAh33PobbWwPz7927J7NTtZ4pPZYDqACUKB1CK1S5pifhJ+piJZBxnba3HGtAIoL/vmmvkwzPwzZWDsfVc0n83rl+GVwPvma+ce7NBnzznxPK84joPBkxNp2A2uqG6//hpP1RV1zD/7u2bTHiB0HJGBtrazMBpZeDzu9dwxHoVttbdqofD4V8QW9+k/lpUEB0OT0FYTAdkBkSLKAslQhXk/mlnoFzfAhtrb1T3+x70qpadXPNTrUKQ+S7DrfWfsBlb0FDvxWoHXAoYuf4zIOkJgLSLc2NtEQP4kbPo0AkgQZEcGFI3IWkjykCBioGOC49o9vHVUNFKcXtHm/ZCRLMvi0ICT0OmckxeFphZYcLzTS3Req8UniBGHtxOr8iAxlUQGPsI35aGNW1I/mCY+WP++exaQSXaANzpfXLcM+52TXrTn4DMdZV0XgPff0NTQw3NcnxiEdd7g1x4cDAkMuD8OoIR8IJQi3EeINRLjVAvtdHxUBbB8uRAUlYYAFHFFmMVZcA/HYbmxoM0a69vDtf7BkEDjAVSgnlwDDvxKD7TgtMOgBMp9GPq2bxj6tlWPTqVZXShAMQiYzFWi2UWyVmKHSmUL21AwJMdUycGL1QDHKfoiJ31Uquey+FRDbWdtGv/MMk098oTrnzoVB5AyFITnrV1tmO/lGbv+DSUMfvcAKSgjIHkrFnmik2HNMcX5/Y/zdLJT6j5UpaKWq/86BC7rdPGshfEx2V9e1pp/o1EkqgXNjvFTsepM6cdpIrHZxVeTgZOdHZd2bWvY/IFu4PxVO/+L8AAEv7t4N8ueEwAAAAASUVORK5CYII=',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA4VJREFUeNrEV0tME0EY/neWEhGUgiHxAW1BWgQaIpUQEyU0qCcvXkw8mGjiwYN3zybGBC/e1EQxJl6UePJAjBeVhPgi9oGl0JZHLU+DVaiUIlDrzOzu7Lbbx5aWMOnk/3e7s//3f/PNP7NcIpGA3WwlqTesBq4Cm6M7FG/SE0qsZgVAgnvGXK75YBAOmbthITBI7dayN79QHId/iHbC8fJKJGy3nz2DXXcuAHRwLBaDeDyeZPOLjwCQAIDDFsi1linQ2t4ODoFeXwkV5XtBp9PBdDAE9SYDtT32LpEBqSPyzJ6iAujpPp10XW80CNZkZIGBdkR9hBAqGMDE7J9tgW22HAZIQOFT0Fi7T4PwRPEhns49tZSFIgDweH3w4uUA9S9dPA/WliZN7LQeqxNEWSiAh33PobbWwPz7927J7NTtZ4pPZYDqACUKB1CK1S5pifhJ+piJZBxnba3HGtAIoL/vmmvkwzPwzZWDsfVc0n83rl+GVwPvma+ce7NBnzznxPK84joPBkxNp2A2uqG6//hpP1RV1zD/7u2bTHiB0HJGBtrazMBpZeDzu9dwxHoVttbdqofD4V8QW9+k/lpUEB0OT0FYTAdkBkSLKAslQhXk/mlnoFzfAhtrb1T3+x70qpadXPNTrUKQ+S7DrfWfsBlb0FDvxWoHXAoYuf4zIOkJgLSLc2NtEQP4kbPo0AkgQZEcGFI3IWkjykCBioGOC49o9vHVUNFKcXtHm/ZCRLMvi0ICT0OmckxeFphZYcLzTS3Req8UniBGHtxOr8iAxlUQGPsI35aGNW1I/mCY+WP++exaQSXaANzpfXLcM+52TXrTn4DMdZV0XgPff0NTQw3NcnxiEdd7g1x4cDAkMuD8OoIR8IJQi3EeINRLjVAvtdHxUBbB8uRAUlYYAFHFFmMVZcA/HYbmxoM0a69vDtf7BkEDjAVSgnlwDDvxKD7TgtMOgBMp9GPq2bxj6tlWPTqVZXShAMQiYzFWi2UWyVmKHSmUL21AwJMdUycGL1QDHKfoiJ31Uquey+FRDbWdtGv/MMk098oTrnzoVB5AyFITnrV1tmO/lGbv+DSUMfvcAKSgjIHkrFnmik2HNMcX5/Y/zdLJT6j5UpaKWq/86BC7rdPGshfEx2V9e1pp/o1EkqgXNjvFTsepM6cdpIrHZxVeTgZOdHZd2bWvY/IFu4PxVO/+L8AAEv7t4N8ueEwAAAAASUVORK5CYII=',
          ),
          32 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA4VJREFUeNrEV0tME0EY/neWEhGUgiHxAW1BWgQaIpUQEyU0qCcvXkw8mGjiwYN3zybGBC/e1EQxJl6UePJAjBeVhPgi9oGl0JZHLU+DVaiUIlDrzOzu7Lbbx5aWMOnk/3e7s//3f/PNP7NcIpGA3WwlqTesBq4Cm6M7FG/SE0qsZgVAgnvGXK75YBAOmbthITBI7dayN79QHId/iHbC8fJKJGy3nz2DXXcuAHRwLBaDeDyeZPOLjwCQAIDDFsi1linQ2t4ODoFeXwkV5XtBp9PBdDAE9SYDtT32LpEBqSPyzJ6iAujpPp10XW80CNZkZIGBdkR9hBAqGMDE7J9tgW22HAZIQOFT0Fi7T4PwRPEhns49tZSFIgDweH3w4uUA9S9dPA/WliZN7LQeqxNEWSiAh33PobbWwPz7927J7NTtZ4pPZYDqACUKB1CK1S5pifhJ+piJZBxnba3HGtAIoL/vmmvkwzPwzZWDsfVc0n83rl+GVwPvma+ce7NBnzznxPK84joPBkxNp2A2uqG6//hpP1RV1zD/7u2bTHiB0HJGBtrazMBpZeDzu9dwxHoVttbdqofD4V8QW9+k/lpUEB0OT0FYTAdkBkSLKAslQhXk/mlnoFzfAhtrb1T3+x70qpadXPNTrUKQ+S7DrfWfsBlb0FDvxWoHXAoYuf4zIOkJgLSLc2NtEQP4kbPo0AkgQZEcGFI3IWkjykCBioGOC49o9vHVUNFKcXtHm/ZCRLMvi0ICT0OmckxeFphZYcLzTS3Req8UniBGHtxOr8iAxlUQGPsI35aGNW1I/mCY+WP++exaQSXaANzpfXLcM+52TXrTn4DMdZV0XgPff0NTQw3NcnxiEdd7g1x4cDAkMuD8OoIR8IJQi3EeINRLjVAvtdHxUBbB8uRAUlYYAFHFFmMVZcA/HYbmxoM0a69vDtf7BkEDjAVSgnlwDDvxKD7TgtMOgBMp9GPq2bxj6tlWPTqVZXShAMQiYzFWi2UWyVmKHSmUL21AwJMdUycGL1QDHKfoiJ31Uquey+FRDbWdtGv/MMk098oTrnzoVB5AyFITnrV1tmO/lGbv+DSUMfvcAKSgjIHkrFnmik2HNMcX5/Y/zdLJT6j5UpaKWq/86BC7rdPGshfEx2V9e1pp/o1EkqgXNjvFTsepM6cdpIrHZxVeTgZOdHZd2bWvY/IFu4PxVO/+L8AAEv7t4N8ueEwAAAAASUVORK5CYII=',
          ),
          24 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9wJFgQrMfHtSUkAAAPxSURBVEjHpZbLixxVFIe/c25V9fTEmX5MTKJjZjAPTRDFRcSgg8zCQLaSlQtjQBduXIgQXPsfCAEXLlSCCyEQN4KKi4ASNUEkmclLopiFmWRe3dPdSXdXV93jono63clMa/BCUc97f+f8zrkfJQwOAZT/Nzxg6zdB/5v33j707LE3X38/N76nVbk5T3nHLnxzqau7yRAQUURUWnGsJ058/NEnX/40t6FAeaK8c2pq6uho+SlyfonHp5+kXQ8BUFVEBDPrnVWEJPWIc4go3qBYLJ4CNhYAzMwwM8x7vBlmHhHhjz//YrVapVgYo1ZrUCiMcfv2MjMvH8RblopzjiAMB9INhpmpAqtrMakZY6XtFMo7MDOKE9I9T3JruQGiOOfYvq2IyKCdQwXMoDAWEYURqU/pdJLMdgERybzXEcQ5VF3WHw8j4ALH3PxlPjt5mv37dnPsjSPkoohKPaZSi3u1FyAMA/bumUQYIrC4mjSCIOgFEUQhn39xmsmdU1y4dJ16/Q65iYhcpBTHoswOUUQVFwQgD2Yw0PNvvVb85vwv3xJ3rei0Y9595yhJEvPq7EEK42MA5HMhpUKeUnEL5dIjlMvjlErjmdgwi1Qkv/WJF0Gyx2EU8enJU+Ty45z54RyzMy9QnihRrcdU1tp920OIooB9+3cPr8HF8z/z3OEjmM8ySJOEmZcOkKRGu7WTkXwOQdhaHGXb1nHUBYgGaBCgLuy166YCQRCSxhXMdzKLOgmHD73Sm5TNVwzBmyAm0Hetzg23KMiN0Vz7HbMDve7odHwPLCKKOI8giBmpedQ8YikeRxToAxkMFPmZ2ePEq3PQtUhVuHGrQaOZsLDSpNroUKnF/H27Tu1Oi1q9SbV2l1rtLlcuX0fD8AFsDWQwd/4rlq99b88f+rD3mVOhUm9jHmpJVlgzY3U16ZIuyzUIXLdNdXOBC1drsx8c//pMtGXiHnsNdk0WWKw2KRVGEXGsNdrs2F4GdagLCcKI+YtXQCMEt7nASE62dOImodk9GgtUGzFxx1NrxIgKrXbCSqWBqCKiWXFVDUIJgoeAHSKYQaXWIkk9cZyCQJp64rjT2wMiYIZky/1XFklmz97pchahC/r6PsS5kDjxiDrEhYjkMjreT+RNSdrX+96ke9DdA4AqF36bZ2WlwvJihYu/nusR4N8z6OE4g5lqBjRRh2gXzepIU8/iwi1AUN041mCDtftY77r2uB7z1+/N4ODsDMgIEHZnpsNZdGX+Uvvsj2e/y08stSoLV7lxs0rSrg5Evn4touDC7hKuuzHdyLXLc+37A+6Npx/j0VaLaQwvCt73BWR9/yKbF07zo9y4tsDS+qN/ACHdgO7G99JdAAAAAElFTkSuQmCC',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9wJFgUHNld63XMAAAJESURBVDjLrZI9TxRRFIafO3tnZ90PYQ0RgQSNdlbUikoPscGEBDv9ARQm/gYbTTS01mpLJYUx2pPQuBViBy6s2a+5M+zMnXMsFjUkFsT4Vqe473NuTh4Adnd3+dfYnZ0dFhYW+NJqrV1sXHwC6HmKxpiSc27DALRarbWJicm3qqqAOUeZIAjwhX9q9/e/PWw2m69PN5tfD8asvyeO4/Ggig1DOweUAbO3t0eWZfT7fZaWllDV3yBVxXv/GyIiiChWRFVFjaKMRiO2t9+zvLLC5uYmIsL1GzdQEeJ4yOrqA4qiOAO1CgxdjHOO9lGb24uLHLWPmL86T1EU9HpdZmZmKJVKHH7/ThAERFH5DwBV4thx2G4zMXkJgxKVI+r1+pmj1ao1Dg8OiCoRs7NzoIqIYFUFF4R8mrxJdnzAtcka92oZP3o9vPfUazUwkGUZtmSpXKiMy6ooSqAKgzRn6+ULtjbW2f34gSSJCYwhMIZhPMS5hDAMSdOUbJQhIqgKKooVFa5UDM8e3afyeJlKrYHPR4gKviiYmppiMBiQJClpmpLnOc1mk0IEKYrxD9LEUS8pbjDA+IyStcSxI4oiOp0OIkKaJJycnHB5ehovQq/Xo9P5oUGe53jv6Xa7YAxZluG9J89zFCVJEqQoKEQoWUuj0dA0SSi8f7O4eOu5PT461qFzBIEldjEiYx+z3NPr9hGB/jDG5zlhucze130TlcPP9+7eWQewNgxeReXyO2OMVi9UkFNJatXqGZ1/6a2qVCtRh/+Vn134Xwwr/w2hAAAAAElFTkSuQmCC',
          ),
        ),
      ),
    ),
  ),
);

?>