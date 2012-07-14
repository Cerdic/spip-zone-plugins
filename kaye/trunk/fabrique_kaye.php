<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-05-08 16:01:59
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
    'nom' => 'kaye',
    'slogan' => 'Un cahier de texte électronique pour l’école primaire.',
    'description' => 'Un cahier de texte électronique pour l’école primaire.',
    'prefixe' => 'kaye',
    'version' => '3.0.0',
    'auteur' => 'Cédric Couvrat',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.0-rc;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configuration du cahier de texte',
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
    'exemples' => 'on',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Classes',
      'nom_singulier' => 'Classe',
      'genre' => 'feminin',
      'logo_variantes' => 'on',
      'table' => 'spip_classes',
      'cle_primaire' => 'id_classe',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'classe',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
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
          'nom' => 'Descriptif',
          'champ' => 'descriptif',
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
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Classes',
        'titre_objet' => 'Classe',
        'info_aucun_objet' => 'Aucune classe',
        'info_1_objet' => 'Une classe',
        'info_nb_objets' => '@nb@ classes',
        'icone_creer_objet' => 'Créer une classe',
        'icone_modifier_objet' => 'Modifier cette classe',
        'titre_logo_objet' => 'Logo de cette classe',
        'titre_langue_objet' => 'Langue de cette classe',
        'titre_objets_rubrique' => 'Classes de la rubrique',
        'info_objets_auteur' => 'Les classes de cet auteur',
        'retirer_lien_objet' => 'Retirer cette classe',
        'retirer_tous_liens_objets' => 'Retirer toutes les classes',
        'ajouter_lien_objet' => 'Ajouter cette classe',
        'texte_ajouter_objet' => 'Ajouter une classe',
        'texte_creer_associer_objet' => 'Créer et associer une classe',
        'texte_changer_statut_objet' => 'Cette classe est :',
      ),
      'table_liens' => '',
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => 'on',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => 'webmestre',
        'objet_voir' => '',
        'objet_modifier' => 'administrateur',
        'objet_supprimer' => 'webmestre',
        'associerobjet' => 'administrateur_restreint',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
      ),
      'vue_liens' => 
      array (
      ),
    ),
    1 => 
    array (
      'nom' => 'Devoirs',
      'nom_singulier' => 'Devoir',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_devoirs',
      'cle_primaire' => 'id_devoir',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'devoir',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Matière',
          'champ' => 'matiere',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
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
          'nom' => 'Classe',
          'champ' => 'id_classe',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Pour le',
          'champ' => 'date_echeance',
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
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'matiere',
      'champ_date' => 'date_publication',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Devoirs',
        'titre_objet' => 'Devoir',
        'info_aucun_objet' => 'Aucun devoir',
        'info_1_objet' => 'Un devoir',
        'info_nb_objets' => '@nb@ devoirs',
        'icone_creer_objet' => 'Créer un devoir',
        'icone_modifier_objet' => 'Modifier ce devoir',
        'titre_logo_objet' => 'Logo de ce devoir',
        'titre_langue_objet' => 'Langue de ce devoir',
        'titre_objets_rubrique' => 'Devoirs de la rubrique',
        'info_objets_auteur' => 'Les devoirs de cet auteur',
        'retirer_lien_objet' => 'Retirer ce devoir',
        'retirer_tous_liens_objets' => 'Retirer tous les devoirs',
        'ajouter_lien_objet' => 'Ajouter ce devoir',
        'texte_ajouter_objet' => 'Ajouter un devoir',
        'texte_creer_associer_objet' => 'Créer et associer un devoir',
        'texte_changer_statut_objet' => 'Ce devoir est :',
      ),
      'table_liens' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => 'administrateur_restreint',
        'objet_supprimer' => 'administrateur_restreint',
        'associerobjet' => 'administrateur_restreint',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
        1 => 'outils_rapides',
      ),
      'vue_liens' => 
      array (
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
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAGQAAABlCAMAAACMYLbDAAADAFBMVEUKBgVogFT///+ESED////////////HxmO6g13///+2RjdbBwa2s1v///+HT29kSDvl5XJDJiO7qqj////////////CvLtsSFEtJSP///////9+Y2/FwcD///////+lUR7Sa17////////Z1dWKh0f///////9YazmcHBj///////////96Ri3///+INif///////////9FQ3L////p5ubMqar///8/OzvndWX///////+QhoH///////////////////////+uBQT///////++vV/////////////8/vz///8gFhX6+n39gG7///+EfXFwbT+nho2oPTL///////9+dYVMGxr///////////+xGBXMjoz///////9kYGL///////+WlZP///9zPDakoVT////V1Wv///9bMSywdVFzc5n///96YFeZZYGyXFH////////ukYTi2dr///////////+fVUVOUIX///+2aGD///+USkFVU1bnAAD1fGqTOif+/v79/f36+vrz7O37+fj8+vv8/Pz9/Pzr3+KgX2KUV2KlaGuxfn67jpDJp6jRtLbfysvi09Pq3tzw6er18/Tf3t7Yys2yhYWocG+TSk2WS0+VTFCVT1ObVlmfXF6kZ2eqcXOwe322jI3FoKLLsLHaxcfm297u6ery7+/59/fc2NjNx8iypKSqgoOVS0+WTFCWS1CUTE6WUE+cVlOfYWaidna0gYO+lpfIpKbXvr/fzM/k1tfr4eTw7O308vLy5OPpzMTmy7/p18vn3dT28O36+fnl4eGdanOSUlWVS1CWTE2XTlGYUladWlmoZFqna22ueHqyg4e+kZPHoaTUubrexsbp29zx6Oj9/P339fTXlZLLWE7HYFHGZVfFgHLOq5Dq5OP7+/vr6+va0dGkc42caWmPSk2ZUlOSW1qjY2asdHa4iIrDmpzNrK7hz9D79vb48/PThH/GSj3DTT7JST3HUEC0aUanfXLz7er29fX5+fnV0tHQzc27r7CwkpWPT1Hy3+YmAAARH0lEQVRogY2ZW2wTV97AXc0DFa0YzEOwRKSMFCT6EPmhSuSR8lZpn0yUBGn7sCshRYMq8ykZaXlwFTnrzKQlbYBgtksgthPnBksg3pnYBjspS/by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',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAADAFBMVEX+/v7KysoAAADExMRddVV6oHZkgmJMYUq6urodJRzHrIvT09OwmHvewpxVRRlGMwlXQQxUZXN3jaFjc4NMW2idiG2umHtIQTPqzXLt1YDpymvnxmVlSw5LV2QcISU7NCjiu1FlTRKbm5t+cFlSSTrfvFvs0nrov17lrUbfiRmoSSH1Xjn6Nxj7aVb8vrl5aVTgw51cUi6GcTXjxHzmy4/UmxtmNhzqcVn+FgL9BwX+R0chHReLemFEOyT4dnX+HCD+Ahf/y9BfRg58kHazs7P9l5/+BippW0kvKiFJOhX+a4D+jaPP+daXkWuBcVqXAGG5oYFvYU6Tk5P+Azz+cJVY/Yeh+rdzcVOOC11TaAD+A0nL/+EK/mok/nKDxnO2NReTf2VmeAB3iQBhYwAAHgCJiYn+Alb+jLFR/6YC/nedvYPaQB3IOxpdU0GJkAByeAD+IHX9AGBR/rgA/YsS/pHf/e/kh3GBjgABcwAAbgD+yuL+AXb+J4IA/Z4X/qWZAAB3Ig7c3Nz9A4iB/9cA/64X/rOtMhbTPhxYGQtnhwCInSr+Fpv+YbYC/rsR/sCaLBTYcFdZdACVqkv8FKr+AJ79T7gg/s8B/snG//LeeGGmpqb3Ebr9ALL1R8MB/9Qf/tnfiHRlEVn7A9f+Acm7//YB/uaW/vOJpT/REsr9AOrKg79x/vPRX0NDU2VRZHm8L7zmAvn2APvQBcsB9/pI/P3LTzN1kK5heJFLaYOMIq+5AvnMAP3MCOtdQWUT+P1g8v5UM8FyDPWGAPycAP2oB/NrKpwQ6v4B4f4G1/5O3P4yQDYaIpkiBsAgAfI7AfxTAPxpAvp1EOl+YcgzSFs+V210dHRh5f4Fyv4Cuf4Eqf4KlfEHhO8Hd+8GYu4GVO4FQvICMfkAGvsHA/xAF+9mX8h+2P9Nwf0RoPwKj/EZQM4bMKgyQYZtbW26nphDVD1JXUUAAAAAAAAAAAAAAAD///////////////////////////////////////////////8V9TaaAAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEgAACxIB0t1+/AAAAxlJREFUeNrtlOlXElEYh4cZCgIn20zRSgOzzXIqrSwqy0lbxWjTNpLGyhillSwrxEqk3fZot2zfLaJ937NVM9uzvYz+hd47YKeDEPqhL51+c8697+E8z73vPcMdDPuffyg8HEJUh+fXqCnAhVUWYHlBLRiqLohEqCdcXOX2vUgSxtre3nU8tsXD69ar3wD3IRv6+kkk/gGNPBmwcOMmaJNACUpAUFPpnwWZo/1gSbOQEP+Q5i1atmod2qateyGMoigZCFS79h3Cw/0CgiI6dgrtHBnZxZ0g7CqiKDmOk6QPLqLC/YMiuhFirHuPyKiebncAkAqkSF971Sua5sHvvaNi3AgkCU3F9sFxOYUi7yuluf9Iv5j+rvgBA0kfihLFKRSKeApVg5QOARs8xAU/dNhwtKxIpkhIDOMqJNhf+IjKwshRo8eogscChickqccFy1HFJAvtwvgJzvzElJRJGg0bK4tLVeGJ6jRtaryXXKtllJwweYozP3Xa9Bk6TUVwrTptJkr6LAYJszPmOPNz52GYjnXwen2mVg24Wq1OZ3gGDMvKcOLnL1gIoy7bbhiN+pwkoE1Ji0CAQyzOWuIkLF2GRt1yJLCqFUjQwvK5K0GgiVWr1zjxa9etx37tYFRtAMGkhocTzBs3bXY+8Zat3KRTsayGZbO3sbADdASCKX173o5K/M78XfaCZY1GFloCIdeEhN2mPXvz9lW6q/vzHQXQ4LAHQDh4KNdkMh0+cvSYma58uQuO2+doPh8M1QkQLCet1lOnz5w9x7gSzhdcqBCQoteDcPHS5StXrzGMSwG7fuPmLU4QIOX2nbuF9+4/ePjI8riIKTYLXX1vnpSUPC0tffb8xctXr9+8LSsrfPfearFYiqyM0rUA+fDx02dO+PL1Ww5jsSDBigTCgLkLnAH1JBDklDOMQ4Ab5F74bvthFzKTlcUM7MLAEbhL7S4Gpc3G59ts5UqpVBoNuFLq9gh2gUfbbLZiJXA0bTabpTDzDH8QMDFBc5hZSBCEkDbTQsLDR9wgFtI0wgwQHsSAeYr4d8wzXh3sr+QnZkwdCWCdbY8AAAAASUVORK5CYII=',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAADAFBMVEUA/wCztLfa29zW19jR0tPu7vDv7/Hw8PLz8/Ty8/Tx8fP19fb09PXu7/Dx8vOwMijq6uzl5ujOz9Lw8PHv8fHy8vTv8PHu8PDw8vL29vfz8/Xt7e/R0tTy8vPw8fHw8fLNztDPr6DN08SkCwm6SkPt7u+tMCjp6uvr7O2kDw3U1NbV1tjn6Onr7e3o6eq9opVQnduOvdMzebtQirrs7e43fL3s7O7r6+3q6+zp6+sqdcZ8rcbQ3Kfq7OzgyyPl1VPp6evcyjbo6urdyznczT3o6Orn6enS1YnS6cDMzc/Oz9HP0dPR09XQ0tTNz9HS1NbS0NTO0NLQ0dPP0NIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgAAAAAAAAAAADQAABNBfeQAAAAAAADIikDIikAACVwAAAAAAAAAAAAAAAAAAAEAAADIimTIimQACTgAAADGi+TIg/QAACQAAAAAAAAAAAAAAAAAgAAAADQAABNBfeQAAAAAAADGjFDIg/QAACQAAAAAAAAAAAAAAAAIAAAAAGgAABNBfeQAAAAAAADIitzIitwAAIwAAAAAAAAAAAAAAAAgAAAAAADIiSzIimQAAGgAAADIixDIixAAAFgAAAAAAAAAAAAAAAAQAAAAAADIv5jIiMQAADQAAADGi+TIg/QAACQAAAAAAAAAAAAAAAACAAAAAQQAABNBfeQAAAAAAADIi3jIi3gACCQAAAAAAAAAAAAAAABAAAAAAADIi5zIi5wACAAAAADIi6zIi6wAAFgAAAAAAAAAAAAAAAAQAAAAAADIizTIiMQAADQAAADGjFDIg/QAACQAAAAAAAAAAAAAAAAEAAAAAGgAABNBfeQAAAAAAADIjBTIjBQAB4gAAACJ9/uGAAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEgAACxIB0t1+/AAAAT5JREFUeNqFk4tSgkAUhjU6YNS60cUuXtqiAmule2oXI8suapT2/u/SYiwscbRvzg4M883PP2dmczlBHicnyc9pmjYPulFYMGERAJaIWSwYOo2FZcuyVgisrq2XNgjZ3IKSCYRsJ0JZUIGqbtR0AiBG36kZxeofgWWgu3uphBASnQnU3leFchnCOYDfpxhqH2YSBA64MqFOFeEI6VBXE455wwMFzhvpBMZDVIFTOy2EX4Er0BN1DwzpcHqmCpCBnl9EwqUAE66kcC1g6Y5poem6TaxDLLQEM3/RarcdTLi5jQUHFe7kHu4dISAdOg+J4GMJnUcpdH2fcUR4koLneWjCcyS0vV4P7SCFrljizISXKcLrWyTwKUJ8LybCe39QGQ4/BgH77H+NxuPvfiJEpUZBELAgGIkTviXCv7d7Jj82qUYb4mTEAQAAAABJRU5ErkJggg==',
          ),
        ),
      ),
    ),
  ),
);

?>