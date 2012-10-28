<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-10-23 23:44:28
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
    'nom' => 'À vos souhaits',
    'slogan' => 'Lister des choses qu\'on aimerait avoir',
    'description' => 'Ce plugin permet à chaque utilisateur de lister des choses qu\'il aimerait avoir, par exemple pour inspirer les personnes qui voudraient lui offrir un cadeau. Cela peut servir à une liste de naissance, de mariage, d\'anniversaire, de noël, etc.',
    'prefixe' => 'souhaits',
    'version' => '1.0.0',
    'auteur' => 'RastaPopoulos',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL v3',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.5;3.0.*]',
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
      'nom' => 'Souhaits',
      'nom_singulier' => 'Souhait',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_souhaits',
      'cle_primaire' => 'id_souhait',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'souhait',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'text not null',
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
          'nom' => 'Descriptif',
          'champ' => 'descriptif',
          'sql' => 'text not null default \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '5',
          'saisie' => 'textarea',
          'explication' => 'Vous pouvez décrire plus en détail votre souhait.',
          'saisie_options' => 'class=inserer_barre_edition, rows=10',
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
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Prix',
          'champ' => 'prix',
          'sql' => 'float not null default 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Vous pouvez indiquer un prix si vous le connaissez',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Statut',
          'champ' => 'statut',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
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
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Souhaits',
        'titre_objet' => 'Souhait',
        'info_aucun_objet' => 'Aucun souhait',
        'info_1_objet' => 'Un souhait',
        'info_nb_objets' => '@nb@ souhaits',
        'icone_creer_objet' => 'Ajouter un souhait',
        'icone_modifier_objet' => 'Modifier ce souhait',
        'titre_logo_objet' => 'Logo de ce souhait',
        'titre_langue_objet' => 'Langue de ce souhait',
        'titre_objets_rubrique' => 'Souhaits de la rubrique',
        'info_objets_auteur' => 'Les souhaits de cet auteur',
        'retirer_lien_objet' => 'Retirer ce souhait',
        'retirer_tous_liens_objets' => 'Retirer tous les souhaits',
        'ajouter_lien_objet' => 'Ajouter ce souhait',
        'texte_ajouter_objet' => 'Ajouter un souhait',
        'texte_creer_associer_objet' => 'Créer et associer un souhait',
        'texte_changer_statut_objet' => 'Ce souhait est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => 'on',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
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
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAH3UlEQVRYw62Xe4xU1R3HP+d3zr0zzM7usi/WF1S3orE2bY1N2qDWaNqkKrUEaolaNYI0RSO2+KgxNv6hSWuoqY1UrEnbENTWFuVRkYY/fKSSiCiQylN0FaS47M7ussPM7My995zTP2YYZ3YBbeIvObn33Hse3/P9PY9issi/l9x6T9z35YezvaeHlVKB4vAgxeFcNOXjj3919XMv/xZwfEGiJm6+adH8Ze7sc5a2nzUDCUIKuSMUh4coDg9RLo1j8qW1N63+13VA0jDPvPbIfc8HZ82Ya8I0pZEcpZEcMjCw/qonn5k3YWzzhhP6maj3tKVtp51Otqsb5RNcHJFUyiRRhLWOQmDmrLx81tKGueHfltwc5+NobqWQRymY0t5Buq0d29l17T9v+/EawJwMwMQf6ZaeHp5a9nvGS+Okw4AvTT+dvnPORItgrcNay5Fs+tGnF128qfvcjCvsaF+ZVCpExQJRJkPc0kprTy8mMIhS5JNoNpAGCp8HgLR0dpEvlGhJhaSnpBnLF9iz90OmnzGNMBVincN7SLfP3O5sK+PqE1JJQhJFlEZH2PvODna+u4/C+DheKRYtXsD/A4BUazvee7z3KKUQEUSEkaN52tuyOAfOOw7uOERrSwqTCjHWUS4W2btrP4XiOGEqJGUtY6Uy6bb2E6n6pDaQvL5p073Xzr4SpRRxFGOtw3tf/RklOOuxzlOJbV0lLkkYOjKCd9Vx1jkqUcRFX7+A0dzIc6fymokMJG1ueNlFV91Id2cHcblEuVSiUkmolCOiOKZSifHO453HOYt3GmsdPT0dnDn9NFLZLNmOLsLWNqb1nc/W9StvAO74PADCVbfPHbvitgd4+s7FxN4RxwmVOCGKYqIkIXaWJEmIrSNxDg9oUQQiGKVJBYYwCJiSDgmMJtCaxcv/yCoJRm9a8UIKiE4WB8Lnf3NrZe7df+Dj7WuRIEBEUEpQSqGUQoeGPRs3c2T3B4jR9QW8dUy7oI8LrroEGyV1+/He4RKLswnTL5rDi4/dwfz7/zIJhABm9SPXvTDvnhUc3rkBE6TQSqO8gKdKt69iHdjdDyi849Pm+fR7bTwelBe0NpggxeGdG5h3zwpWP7rgvxPVboC0D6fOHti3BXQ31b2aA6RXCq/TYDJkuttQ0sCAsxRzebzuwqkyiG+eWzPggX1b8Ep3A1ngaBMAgI4zZrL71ZUopScBQBThlBR7tm7n+z9byI4Na1Gi8M7zjWvm8PbGP3Fh/1tE4xVwzQDwHu8tX7nilrqtTWRAACqFIbJTu6ubq2bvVKIIM2mUCMXhIaZkWxClcN5THB5CiZDt6CZKleuu2EAReE+lMHRyLxDR2DhPtrOnuuEkBoQwk0aLUB4bJZPNIqJxzlIeG0U3AMC5RuRVAIBNCkiD6hoBuMLY0CuvrV51pbVJDcBEBoQgHSJaeHfz1rpnVK29H9HC5g3rsJUY5asHktqY43agRGFQuRMBKN/y8At3r1720+1z73qMbS89gRLTzIIoTDpkp3uL7y5ayJbVz6KU4L3jWz+6kU1PPk3feefi44RDg4fZum8He4ZGPw2zCs5ua+PZ17csnOiGBigDI957onIRlKBEmkoFJVKj3BGNlwCFKMF6TzxewjtIhSk2vvU6ewY/4YpLr+aH519MKp0GIKpU2L9/GyXl1w1ccvjPf1/+/qLj4dnUioUygI3LGBOANHuCiKBNAICLIrRoREu9b5TipTc3cSh/lNsXPcTA4HtsfvMZjh4dBaBjaiczz/02ty18kOdWLVtw/Z3w1yeqIJqU7ZIICUJMEGJMUG/aBOggADxxpYI2VQBaa2wUUWzx9OeGuPknv2DbjnXs3PUqhcIxHrx/Iw/ev5FjhTzv7nqFt995kfnXL6HXnLnge4vbLp2UDW1SQWszqYnWaF0NYC6Oqn0xaK1xlYhcV8yVl/2AvfteI58fRItCiWpyYy2KfH6QPXtf5fLvzKaX6b8GQmlMCy6J0EGAGNPUdO1ZVVOE1HKEiOCimHyb0Ns7jcGhA1jnSKzDJrbhYLb6zTkGcweY1juN0GZmAenmdOwt2oSgQDHBC4JqAHM2QbQgSvDe4+Kq6w2PfIT3nqVL1kzy9Qfu21B/f3z5PIZHP6pHRdMctBK00ZOKZaWlgYEYo2tG6quAlFKUxo/WDfNUIlooFkeO7yAnYCCYBEC0YEyIA1wcI1pX44T32DgCpSgUhwnDkOVPzcdaSxQn3PvztQAse3wOYVC1mVQYUigO152sDqAaeBzGhCeALegaA1UVmJrVKJxzTC3A6MgYPd0dGF2tnlUDG6l0SCoI0FrQWhgeGqXkStsA1wxAIMhkqBUADSrQmHQ1F+A8QSrVkOwsPaMB/Qdz9PR0YYyglKVxhTAIMEajdTUXfHQgx+4PD/4OiI5z3b3qoevWZVq7Zjk3uX48ngt2/WMrOtBNGhJArOAu6+DgsRzfvPg8lCiSOCGx1bWMFkxg8M6z/Z33ODA8sH7Nyg/uAg4fXyoLzAC6T3WL+Qwx19zQd9c53b1X9/X10t3biampLUkSckdG6O8/wqHRoZfXrnr/l8BBIK8mFCbpU9XwnyEh0Hnhpa2zvjpzxuJsmPlaXQ8KClHpPzv3H1yx641jbwCHa1WRU3xxIjUmO2tMZhsO42o3oxwwUnt3MPl2/EVIWGMynAAgqiW9pnT8P3cDkpk7jJz7AAAAAElFTkSuQmCC',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFXklEQVR42u2XaWyUVRSG/eFPExHaAqVjoRS60UKhZa0CZVFQkLZhCVCIqAkQ/WFYZAdREDGIssWoEKq0tAVEQjQxGJFFAUlpO21nOl1gujOdttNtSksLr+c9mWEyKRT/CH+4yZvz3XvPd5733u9+szwH4KnqmYFnBtzt+aUv+5xJGOSPnvRWYH/M9PdD6mcrcGTTUhzeuITS69QdyzmnOY+rs9Tgc4bMB/Bkg8/Zg8sX4vfvD/aoc98dwKk9O+FsrEaj7SYctVY02ctEVrQ118jcDuY8tkb69o9AptvECwlB/vh1/xeYE9APCYaHK9Glib59YPzzGLJ+O4LscylynSbxKPIupHHOlffoOmT8sm83yCSbBnzYOfvlTiQZ+mP+oAAsCDIwdtfAAEzp5wPT5XTk/vEDjOdTUXApQ0ykwvx3JuL79mFO9/s8NckQ1g41QDYN+LFzetc2zJObp/f3w7R+vgR5aapLMb1fxOlvNuHE/nU4dWgjfv52qytu5pw7r9v9rMnaZJBFJtlqgAfjxPb1WCCTTLRWlsFiMcFsykOhOZ/XlF6b8nNhzLqO7H+uPFDO9Wsc45xXPuXusyZrk0GWMD0GeHoPvb9ME2L79EJJiQX5OVkw5d5AkZgotZgEcA1FBUaYjdkozMthlH4eSgoLUGwSSL5Rx4sl/2aRGbeKCxn1flNeNkqlJmuTQZYwPQbSdq2EvcIoBylVtnMDSiyFLC4GsvDe/FkYPTwcsZGhWLd8McpvlWJk+FBUV5RjzIhwjI2OQGxUGEaEDYG1tBjREjkWFxOlcURosJosLbKwNhnKEqbHwNGtb6PKchU5cpoz9q6WQiWosN4U+GwsnvOa9It0F04fOwx7TbUaanI0YNzISDjq63C7skLzyySvquwW6u21GC9zjJUyrjtSUszaZChLmB4D/CCpLLwiJ/tHZO5bK6urQJ3ttq4s++plQqVfg4Y6O8FqoP1OmxpwtrZwjiA1UCE7VFd7GzGyY4zsWzleZmVtMpRFppcBusq/mC7btAl2m01Bo4aFEKCrjAoJ1qJ3O++qgXv372NCTJQ8hggMGxpEk7r66nIrGhvqMTIihFH7lTJuq65hbTLI6m6g3HSZ77O+Xo4GB9qcTjVgNt7AnTYngQqWJjGMgXAdb2lu0tXaqiq5G3JvK6LlnDCyX1tdhfq6etYmQ1lkep0Bq/G8frikf7UKTmcb2tvbsXJRIpITXud2E6CPhI3G2HgYO7s6uVv6eOrtNl11R0cHd4yRfZ1raWllbTKUJUyvt0BcXZDtSUPm/jXovNclBu7IK2XE7ClxXI1qxcIEBYcHD9QYNjhQDbS2NKG50UHxWsdCBhncc6Jm3O3qYm0yyPJ+C94Y0BdrZ8QjKSgQE3xfAhu39r+0LoF0iFmKK6bc9zLyzNAIG2uTQZYwPQYSB/tj74LZWD1+FOYOCcT/1VibDLLI9DKwZ96bWPfqaCQPD8X80CAkDTYgMcijJFd8xbc3Mr5ehbQ9H0pcg5MH1iNd3u/MfWs458n1iLVYk7XJIKu7gd1JM7Ahfhw2UpPHYsOkh2jiGCySs5B3Qb+CXd+GmRKPybM9LnNDmPOwe1mTtckgy9tAUvAAfD5nOjZPi3uktkwXTY3DEnkTjBdpIAW551MFnAGNl9KRHBnGHOb2WIssYXoMzA32d6ydGItPZ07G9hmTHqlPRMuiI1HwVwZ/iAg0E+YrP9GExJOcY06PNcggi0y3gV6iGBlooquexG2bFdAXx3d/gJSP38XRLctUKdve4RjnNOdxdcgiU9mu32X+roH4J6QYMr1+mLrc+D0ZkUXm02/P/hk9fQP/Akk+azwNtsZsAAAAAElFTkSuQmCC',
          ),
          32 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFXklEQVR42u2XaWyUVRSG/eFPExHaAqVjoRS60UKhZa0CZVFQkLZhCVCIqAkQ/WFYZAdREDGIssWoEKq0tAVEQjQxGJFFAUlpO21nOl1gujOdttNtSksLr+c9mWEyKRT/CH+4yZvz3XvPd5733u9+szwH4KnqmYFnBtzt+aUv+5xJGOSPnvRWYH/M9PdD6mcrcGTTUhzeuITS69QdyzmnOY+rs9Tgc4bMB/Bkg8/Zg8sX4vfvD/aoc98dwKk9O+FsrEaj7SYctVY02ctEVrQ118jcDuY8tkb69o9AptvECwlB/vh1/xeYE9APCYaHK9Glib59YPzzGLJ+O4LscylynSbxKPIupHHOlffoOmT8sm83yCSbBnzYOfvlTiQZ+mP+oAAsCDIwdtfAAEzp5wPT5XTk/vEDjOdTUXApQ0ykwvx3JuL79mFO9/s8NckQ1g41QDYN+LFzetc2zJObp/f3w7R+vgR5aapLMb1fxOlvNuHE/nU4dWgjfv52qytu5pw7r9v9rMnaZJBFJtlqgAfjxPb1WCCTTLRWlsFiMcFsykOhOZ/XlF6b8nNhzLqO7H+uPFDO9Wsc45xXPuXusyZrk0GWMD0GeHoPvb9ME2L79EJJiQX5OVkw5d5AkZgotZgEcA1FBUaYjdkozMthlH4eSgoLUGwSSL5Rx4sl/2aRGbeKCxn1flNeNkqlJmuTQZYwPQbSdq2EvcIoBylVtnMDSiyFLC4GsvDe/FkYPTwcsZGhWLd8McpvlWJk+FBUV5RjzIhwjI2OQGxUGEaEDYG1tBjREjkWFxOlcURosJosLbKwNhnKEqbHwNGtb6PKchU5cpoz9q6WQiWosN4U+GwsnvOa9It0F04fOwx7TbUaanI0YNzISDjq63C7skLzyySvquwW6u21GC9zjJUyrjtSUszaZChLmB4D/CCpLLwiJ/tHZO5bK6urQJ3ttq4s++plQqVfg4Y6O8FqoP1OmxpwtrZwjiA1UCE7VFd7GzGyY4zsWzleZmVtMpRFppcBusq/mC7btAl2m01Bo4aFEKCrjAoJ1qJ3O++qgXv372NCTJQ8hggMGxpEk7r66nIrGhvqMTIihFH7lTJuq65hbTLI6m6g3HSZ77O+Xo4GB9qcTjVgNt7AnTYngQqWJjGMgXAdb2lu0tXaqiq5G3JvK6LlnDCyX1tdhfq6etYmQ1lkep0Bq/G8frikf7UKTmcb2tvbsXJRIpITXud2E6CPhI3G2HgYO7s6uVv6eOrtNl11R0cHd4yRfZ1raWllbTKUJUyvt0BcXZDtSUPm/jXovNclBu7IK2XE7ClxXI1qxcIEBYcHD9QYNjhQDbS2NKG50UHxWsdCBhncc6Jm3O3qYm0yyPJ+C94Y0BdrZ8QjKSgQE3xfAhu39r+0LoF0iFmKK6bc9zLyzNAIG2uTQZYwPQYSB/tj74LZWD1+FOYOCcT/1VibDLLI9DKwZ96bWPfqaCQPD8X80CAkDTYgMcijJFd8xbc3Mr5ehbQ9H0pcg5MH1iNd3u/MfWs458n1iLVYk7XJIKu7gd1JM7Ahfhw2UpPHYsOkh2jiGCySs5B3Qb+CXd+GmRKPybM9LnNDmPOwe1mTtckgy9tAUvAAfD5nOjZPi3uktkwXTY3DEnkTjBdpIAW551MFnAGNl9KRHBnGHOb2WIssYXoMzA32d6ydGItPZ07G9hmTHqlPRMuiI1HwVwZ/iAg0E+YrP9GExJOcY06PNcggi0y3gV6iGBlooquexG2bFdAXx3d/gJSP38XRLctUKdve4RjnNOdxdcgiU9mu32X+roH4J6QYMr1+mLrc+D0ZkUXm02/P/hk9fQP/Akk+azwNtsZsAAAAAElFTkSuQmCC',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACQElEQVR42sWT3UuTYRiHn7+hYurc0jKPKlaJRYSWRGmOcH4ddNRkNbTSZk6PBm5TKbWDgsg+WFiynArVRDG3ktEWlJkOdWvuo/ITYqbzI2MQ/nrudzDqNA964IIf931f98vzvrwMwJbY+gJVqshauFuMwl0xClKSkC8WwdxUjke68wRlqlEvPkcOuYyC/eEdDN6/DRtnsO0WupsN+LGygPXlBawtzWM9Mkc16sVmOOQUpYnBaOsL3iySJsY5IdqO8aF2+N49x6SzG267iWp/zVhbjVCkJoGVZaSjrVKFk4k78GVmGqFQAAH/J3jGxzAxOkII2T/lRehzEF9nZ4TZe1UXhWsxul94bhJelwUW0100VKth0FxAX1eHkI0811ep0GtpR+M1NRpqyuFxdgqO+XoF2GO9CovzXgTHBrioxmokIvDK2oMmLqytruB7+BuWFsPQ80WR5Qimhnv5Ag/IZU9vXMKsz4kJxxM0aitAR39VBZe9X3ii7rISPzc2EI1GobtShl+bm/j48gGmPQ6Qy+TJCag7kwNFigRvHHY066rxesCKYZcD9fyefx5jbewBcnEiavNyQC4r5p/iZqkcdVmZ0B49iJojMpSm7YR7qAOBkT54XD0YtZlQIBVDk7kfmsMyaI9loLUkH+SyknQJWhS5MOYdFzCczobywD743j9D0G2D/0M/PG+7oJTthf5UFgy52YTgkMvOSbeZKRTvSSaErWclCehsqaSXRFCmGvXic+SQy/iRcA79I5L//zf+BtcAezaI/neoAAAAAElFTkSuQmCC',
          ),
        ),
      ),
    ),
  ),
);

?>