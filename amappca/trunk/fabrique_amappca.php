<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2016-03-25 23:28:51
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
    'version' => 6,
  ),
  'paquet' => 
  array (
    'prefixe' => 'amappca',
    'nom' => 'AMAP, Producteurs et Consommateurs associés',
    'slogan' => 'Gérer une AMAP ou assimilé',
    'description' => '',
    'logo' => 
    array (
      0 => '',
    ),
    'version' => '1.0.0',
    'auteur' => 'Rien',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.1.0;3.1.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configurer la gestion de l’AMAP',
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
      'nom' => 'Périodes de commande',
      'nom_singulier' => 'Période de commande',
      'genre' => 'feminin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => 'on',
      'table' => 'spip_amap_periodes',
      'cle_primaire' => 'id_amap_periode',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'amap_periode',
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
          'nom' => 'Date limite des commandes',
          'champ' => 'date_limite',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
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
        'titre_objets' => 'Périodes de commande',
        'titre_objet' => 'Période de commande',
        'info_aucun_objet' => 'Aucune période de commande',
        'info_1_objet' => 'Une période de commande',
        'info_nb_objets' => '@nb@ périodes de commande',
        'icone_creer_objet' => 'Créer une période de commande',
        'icone_modifier_objet' => 'Modifier cette période de commande',
        'titre_logo_objet' => 'Logo de cette période de commande',
        'titre_langue_objet' => 'Langue de cette période de commande',
        'texte_definir_comme_traduction_objet' => 'Cette période de commande est une traduction de la période de commande numéro :',
        'titre_objets_rubrique' => 'Périodes de commande de la rubrique',
        'info_objets_auteur' => 'Les périodes de commande de cet auteur',
        'retirer_lien_objet' => 'Retirer cette période de commande',
        'retirer_tous_liens_objets' => 'Retirer toutes les périodes de commande',
        'ajouter_lien_objet' => 'Ajouter cette période de commande',
        'texte_ajouter_objet' => 'Ajouter une période de commande',
        'texte_creer_associer_objet' => 'Créer et associer une période de commande',
        'texte_changer_statut_objet' => 'Cette période de commande est :',
        'supprimer_objet' => 'Supprimer cette période de commande',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de cette période de commande ?',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'fichiers' => 
      array (
        'echafaudages' => 
        array (
          0 => 'prive/squelettes/contenu/objets.html',
          1 => 'prive/objets/infos/objet.html',
          2 => 'prive/squelettes/contenu/objet.html',
        ),
      ),
      'saisies' => 
      array (
        0 => 'objets',
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
      'nom' => 'Distributions',
      'nom_singulier' => 'Distribution',
      'genre' => 'feminin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => 'on',
      'table' => 'spip_amap_distributions',
      'cle_primaire' => 'id_amap_distribution',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'amap_distribution',
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
          ),
          'recherche' => '10',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Période',
          'champ' => 'id_amap_periode',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'amap_periode',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Date',
          'champ' => 'date',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
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
      ),
      'champ_titre' => 'titre',
      'champ_date' => 'date',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Distributions',
        'titre_objet' => 'Distribution',
        'info_aucun_objet' => 'Aucune distribution',
        'info_1_objet' => 'Une distribution',
        'info_nb_objets' => '@nb@ distributions',
        'icone_creer_objet' => 'Créer une distribution',
        'icone_modifier_objet' => 'Modifier cette distribution',
        'titre_logo_objet' => 'Logo de cette distribution',
        'titre_langue_objet' => 'Langue de cette distribution',
        'texte_definir_comme_traduction_objet' => 'Cette distribution est une traduction de la distribution numéro :',
        'titre_objets_rubrique' => 'Distributions de la rubrique',
        'info_objets_auteur' => 'Les distributions de cet auteur',
        'retirer_lien_objet' => 'Retirer cette distribution',
        'retirer_tous_liens_objets' => 'Retirer toutes les distributions',
        'ajouter_lien_objet' => 'Ajouter cette distribution',
        'texte_ajouter_objet' => 'Ajouter une distribution',
        'texte_creer_associer_objet' => 'Créer et associer une distribution',
        'texte_changer_statut_objet' => 'Cette distribution est :',
        'supprimer_objet' => 'Supprimer cette distribution',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de cette distribution ?',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'fichiers' => 
      array (
        'echafaudages' => 
        array (
          0 => 'prive/squelettes/contenu/objets.html',
          1 => 'prive/objets/infos/objet.html',
          2 => 'prive/squelettes/contenu/objet.html',
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
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACZklEQVR42o2SPUwTcQDFOzgxODobRyYjk5OauIAirhIhAReDfC0OamLiojYoSuXDBIRgW1IUCm2Fo3KttpRa7nq2V/pBiydt7Tc2iGAqkeT5/1/SxHo28Z+8u9x7v/9b7qkAqEw3646+6D0/MtLdIGi6L0HT3aQQ9WlOOcrTe1TyY7D3MjY8i/i4rAe/MArXTL9C1Kc55ShfUTBEjKXRWxDtWmwnfNgrJvD9a7ws+k19OWcIN9jTVFlw90pd3/M77XjaeQHqa+dwv+2MrIftZ8uS/WddFzFyuw2Urygg59iraePBsluAlM7i5+EvHBweQghvIijFEUumkcgVkMzkYTAYDyj/d4Gqtb2z2WxehIPzI5/LYW8nhzC/BL/zNSJBPxLpPJatLFoIR3lFATk1rW3Xm98svC05VjmEORZfogJcljGIgRBsrKN0leSUq1ZADw1P9j0eUrM2JzaiMTgcbqgfDT+gfvly1QKd/VONiVnRsnYXOF8I8VQWQmADK6s8TMx7RkvyqgUTzPpx87wZrJOH6OOQ2QoiHnZDdM0hHOARim7BYrJgnHD/LDBMz4sWxg6fx0Yuh4giCHqs8Fh14MmAgtw7eL0ipgxzoqKA6T1xRDuhhc3lRTGbRD4ZQyEl4QOjh31mCI7ZYXiWppDdLmJy/CUWCV+5xJ5GGPUTcK755f+9s7+PH6USAutheAU/IlICUiqDSGwLs7pxEF45ZesYmTI7iby0hm/ZqKzdfAy7hU1Zhc88zWWO8JUF91pODwzcqI9quhrlOT/paEB/Rz19lyX7NKcc5RVTJqolOvUfqv1zyr8BbAqFXHt7YBIAAAAASUVORK5CYII=',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFKUlEQVR42s2WWUxUVxzGG0NIY2hDGmL60DQ+9rEPfexTH9taW6WGLraKlWWAmbkDsw/OsElghk0kcUOj4lIVARcQCCIo+yKyFAeBYYARR1BjU/WhD1/Pd0IminRgWhp7ky/8z7nn//u+Ofcwd94C8Eb1/wgQ4hXePeJG2dEKsKZYc06OQ70AhGzed3ccma4SNkZSrDnXNXxXhvivAoT3C5PtKQYMjnug2DLZuIFizbldiplBZIi1DhB+2z2OnVoz9uQVYmTSi+1qYyAAa85lF+1Hgt6G/lBCAFjZfGwCsYoFDmcxVEZ75ah3Fl/EqgMBWHNOY8mszCkugwzhXmUIAMHNJz3YobPB4SpFkjnr/G9TM9Bn5GK7Yg0EiEk2QmPNAO8p6XvP5+w7gHijHewlI9QAYYXpqf4CmxZOczLyDYkoMKlQZErAiHcGaY4cfK82YWdiMoptGqkf41SIVqVBbXFwjVzrMiayVzLIEsx5slcKEOa0Kb5zR8vg981ibmYac9MePJydwuDklDDfix+EeU5BCR77fXjom5F68vA+MvIKFkNkYMjjZQ97yZAsMp1WZYQeyweguVXrO3ukFM+f/YGqinJUC9VUHMZtccJ19hxprjNZ0HjxFK6cPY6a08co1nJOrTdim0qPZLMDA6Lnkuglg6ynTx6D7L0mTZXwWrc0QBi3iAuePJrH1V9P4ECeHeWuDNyZnJLm36UYEZeixYmSXJQXZuOwKwuHFsWac7y3K0mDrYmpDMFdk4yD+Q4yBXtBhsiz6i7T8+UAkS6rFvNz9+HzenDfO4l5nxcDE56AeW5xKZ4uPMDCnE9s6cyy4r3fF/zIchbJx5FiER9AMMji4yCbHsKLphEvB9jgsmjk86o8fhBVJw6h1z0OrS1bmqearai/cBKXxHZXVRwJKq5pqKyA1mAWO5GGJJMDfWPjZJItPYQXTaNeCzA9eQ9NVy/iRm0VXPv245uENBhsdnQ0XkHLtRo011bj+grimtb6y+hsqoVismLTLzrkFZWgpa6abOlBL3q+EoDb4h13o7fthtRAZyuGutsw2HUT/e2t6BPi/CrEtexhLxmCdTNwjx70ei1Avln96IFvFp57owFN8W/oCsqgR7pmd/PSRxAp9AmT7csyoSTDAKlM41oowCN78dN/vPQQhgltLLApLxqqz6H2winUVZ7BtYtnKdb/ekwm2WKn/cLrg6X/hryicvSqnrrK0/KwNNacR2mWWarx0gXqH4/JIpNsuxLfKLzeW+6bMNKgii3nN1dHS6NsKNyThiK7Hi31VyjWIY/JIItMspW4HU7h9e5yAdZv+erz6JNlLgz1daG5rgatDVfRfr2eEKn25npq1WP2kkEWmWRv+3pTtPB6e7kA8hyUioMy4R5Bp0hM9dxqhsumUKxDHQc4ZAo2zT6k19+9Dd/PN6e8GB3sx3BfN3L1KuSZknG785aUuEetesxeMsgiM9eQ5KdHsNdxVKYuoaertQlTE2PIUuKRrYvHQFcbJWs57m6jlh0vXU8GWWSma+LkAQwWIFKfGFvON9e8fw6DPR0Y6u3EnZ52WQ/3d1GsVxyzh72sySJTvfsneQCDBVj/7eYvY44V5+LRvB8vnj+Tf6c9E3AP38HYyOBqxLXseYVBZsyWzTE8gEF/Eb0TEbExNf7nM+KF8adTPE+XRQ1Rw2WltKuUhj2ylwzOpSXsOBX4AlrhNyG36COhT4U+WyORRabc/pUCrOOjWDwsG9ZIUUIRZAcJ8IYuAG9UfwFUMQM1Epur+gAAAABJRU5ErkJggg==',
          ),
          32 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFKUlEQVR42s2WWUxUVxzGG0NIY2hDGmL60DQ+9rEPfexTH9taW6WGLraKlWWAmbkDsw/OsElghk0kcUOj4lIVARcQCCIo+yKyFAeBYYARR1BjU/WhD1/Pd0IminRgWhp7ky/8z7nn//u+Ofcwd94C8Eb1/wgQ4hXePeJG2dEKsKZYc06OQ70AhGzed3ccma4SNkZSrDnXNXxXhvivAoT3C5PtKQYMjnug2DLZuIFizbldiplBZIi1DhB+2z2OnVoz9uQVYmTSi+1qYyAAa85lF+1Hgt6G/lBCAFjZfGwCsYoFDmcxVEZ75ah3Fl/EqgMBWHNOY8mszCkugwzhXmUIAMHNJz3YobPB4SpFkjnr/G9TM9Bn5GK7Yg0EiEk2QmPNAO8p6XvP5+w7gHijHewlI9QAYYXpqf4CmxZOczLyDYkoMKlQZErAiHcGaY4cfK82YWdiMoptGqkf41SIVqVBbXFwjVzrMiayVzLIEsx5slcKEOa0Kb5zR8vg981ibmYac9MePJydwuDklDDfix+EeU5BCR77fXjom5F68vA+MvIKFkNkYMjjZQ97yZAsMp1WZYQeyweguVXrO3ukFM+f/YGqinJUC9VUHMZtccJ19hxprjNZ0HjxFK6cPY6a08co1nJOrTdim0qPZLMDA6Lnkuglg6ynTx6D7L0mTZXwWrc0QBi3iAuePJrH1V9P4ECeHeWuDNyZnJLm36UYEZeixYmSXJQXZuOwKwuHFsWac7y3K0mDrYmpDMFdk4yD+Q4yBXtBhsiz6i7T8+UAkS6rFvNz9+HzenDfO4l5nxcDE56AeW5xKZ4uPMDCnE9s6cyy4r3fF/zIchbJx5FiER9AMMji4yCbHsKLphEvB9jgsmjk86o8fhBVJw6h1z0OrS1bmqearai/cBKXxHZXVRwJKq5pqKyA1mAWO5GGJJMDfWPjZJItPYQXTaNeCzA9eQ9NVy/iRm0VXPv245uENBhsdnQ0XkHLtRo011bj+grimtb6y+hsqoVismLTLzrkFZWgpa6abOlBL3q+EoDb4h13o7fthtRAZyuGutsw2HUT/e2t6BPi/CrEtexhLxmCdTNwjx70ei1Avln96IFvFp57owFN8W/oCsqgR7pmd/PSRxAp9AmT7csyoSTDAKlM41oowCN78dN/vPQQhgltLLApLxqqz6H2winUVZ7BtYtnKdb/ekwm2WKn/cLrg6X/hryicvSqnrrK0/KwNNacR2mWWarx0gXqH4/JIpNsuxLfKLzeW+6bMNKgii3nN1dHS6NsKNyThiK7Hi31VyjWIY/JIItMspW4HU7h9e5yAdZv+erz6JNlLgz1daG5rgatDVfRfr2eEKn25npq1WP2kkEWmWRv+3pTtPB6e7kA8hyUioMy4R5Bp0hM9dxqhsumUKxDHQc4ZAo2zT6k19+9Dd/PN6e8GB3sx3BfN3L1KuSZknG785aUuEetesxeMsgiM9eQ5KdHsNdxVKYuoaertQlTE2PIUuKRrYvHQFcbJWs57m6jlh0vXU8GWWSma+LkAQwWIFKfGFvON9e8fw6DPR0Y6u3EnZ52WQ/3d1GsVxyzh72sySJTvfsneQCDBVj/7eYvY44V5+LRvB8vnj+Tf6c9E3AP38HYyOBqxLXseYVBZsyWzTE8gEF/Eb0TEbExNf7nM+KF8adTPE+XRQ1Rw2WltKuUhj2ylwzOpSXsOBX4AlrhNyG36COhT4U+WyORRabc/pUCrOOjWDwsG9ZIUUIRZAcJ8IYuAG9UfwFUMQM1Epur+gAAAABJRU5ErkJggg==',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABxklEQVR42qWTSS9DYRiFrS39EAt/gYhfQMSKhaGGqqm4WqUJWlNUUBLEogvTwsZUMzUUTVtVc4vG3GJvcdzzGZJLm0jc5M3Je87zvm2+e784AP+qqI/nPAit0SRSKvsoWPQF7tMLVDa0IDWrUKRU9vRjLmjTV062V6vgDVyiwtCElMwCWHRqdEolVPb0RU5O5sc/F3C4avLxJgR3IIhyQzOSM/LwfBfC3XUQN1cBKnv6zAVHvr2u7OljgVSKyMMtLNYBJKfnw+1Yxs7yHBwL09hYmKGyp8+cnOA5Jxa01qojvr1tOFfmsbtqF/Dm0iwHv4s9febkyHPu6wwSuW18uA9jQ70YHeyJWczJkefc9yF26MrgXF+C1VSPbflX+s0G7KwtKpQ+c3LkFW+hRVt8xL+5u7GCniYJrq119DbrFEqfOTnyigV6Tb7NPjUBv8cFj3MLXY1a+PadsBhrqOzpi5wcecUCSaPST9mGEH68h7mmBEdel/yaNDg+cFPZ0xc5OfKKBUW5OWkj3Sa8PkfwEgnjzH+AixM/zo8Pqezpi5wc+Z+fcrwqO6tOPt23NkkNWX8VfebkyEe7CwlyJf2hEhR34b/X+R2VlVFYJ3a/UgAAAABJRU5ErkJggg==',
          ),
        ),
      ),
    ),
  ),
);