<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2014-07-16 21:58:25
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
    'nom' => 'Requêteur SQL',
    'slogan' => 'Pour réaliser des requêtes SQL',
    'description' => 'Cet outil permet de :
- Gérer un ensemble de requêtes SQL
- Visualiser et exporter au format CSV les résultats',
    'prefixe' => 'requeteursql',
    'version' => '1.0.0',
    'auteur' => 'David Dorchies',
    'auteur_lien' => 'http://dorch.fr',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.16;3.0.*]',
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
      'nom' => 'Requêtes',
      'nom_singulier' => 'Requête',
      'genre' => 'feminin',
      'logo_variantes' => 'on',
      'table' => 'spip_sql_requetes',
      'cle_primaire' => 'id_sql_requete',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'sql_requete',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre de la requête',
          'champ' => 'titre',
          'sql' => 'varchar(250) NOT NULL DEFAULT \'\'',
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
        1 => 
        array (
          'nom' => 'Description de la requête',
          'champ' => 'description',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '4',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Requête SQL',
          'champ' => 'requetesql',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '1',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Requêtes',
        'titre_objet' => 'Requête',
        'info_aucun_objet' => 'Aucune requête',
        'info_1_objet' => 'Une requête',
        'info_nb_objets' => '@nb@ requêtes',
        'icone_creer_objet' => 'Créer une requête',
        'icone_modifier_objet' => 'Modifier cette requête',
        'titre_logo_objet' => 'Logo de cette requête',
        'titre_langue_objet' => 'Langue de cette requête',
        'titre_objets_rubrique' => 'Requêtes de la rubrique',
        'info_objets_auteur' => 'Les requêtes de cet auteur',
        'retirer_lien_objet' => 'Retirer cette requête',
        'retirer_tous_liens_objets' => 'Retirer toutes les requêtes',
        'ajouter_lien_objet' => 'Ajouter cette requête',
        'texte_ajouter_objet' => 'Ajouter une requête',
        'texte_creer_associer_objet' => 'Créer et associer une requête',
        'texte_changer_statut_objet' => 'Cette requête est :',
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
        'objet_creer' => 'administrateur',
        'objet_voir' => 'administrateur',
        'objet_modifier' => 'administrateur',
        'objet_supprimer' => 'administrateur',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAEAAAAA3CAYAAAC8TkynAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gcQExYu2Pf5QQAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAABTSSURBVGje5Zt5kFXVncc/59z73uv3Xq9ANzs0zSbQLAKKIKKCSKNRJC5jjCEzMckYo5NUTCrbpCpJpSqTzExVppJy1LjEMahJFBWVKJqRTcEGWQR7g17pbui9+y33bXeZP+55j/e6ASFjtplT9aq7bt937v1+z2/5/s75Nfz1DQ0YDXwbOA70A1uAGfw/GJOA+4FewBn2SQLL/68Cn6yAH0sDLi0d7SxaNM8J5geySegCVnycDxZ/JoCFQAFgA3nKzNPXrwI+DVwG4MvzceMN1zO3cgZlZSUcOPABm59+Ecuy0nPVAZuA/X8LBFwD3AcsynqWVB8AXfm7H2DjJ2/mqlWXo2kOXq8Hx7GQUrJzxz6efebl7HnfB+4ETvw1EqABt6sgNgvwfdRzli5dzH1f/gKJZBjTTLovJkEKgZQCXdPY+sof2PrS9uyv7QZuUUHyL06AV5nz54GvAOPSf/D5vHg8XoRwHycEOA4UFBRQWFjAPfdsYm7lTBob63EcByllDniBACHweHU2//pF/vDmO9nPfRm4CzD+UgSUApcAnwHuSZu2EIJx48Yye/Ys7r77Tm68cS0+nw+fz4fX60XXfQwO9tPe0cn+/e/S19+Lz+dFCBe8QIEX6pP5HR579Fn27T2ECooCeBL4MhD7cxKwQAWvDcDazGRCsHTpElauXM6NN1axatVyHMchlUpiWTZCaMRiCdraWmlsPk5LSyNerwdd186AFWcBL91rUkiiUYPHf/kbDh08lk3CfwJfVanyooZ+kfffBGxU+fiS9MVgMJ8NG26iquo6Fi9eyPTp5YBNPB7HcRxM08TjCdDR2UFt7THaTjaRSqXI8/myVlcghERKcshwwUu1Ug6FRfls+uxGolGDhvqm9AJ+CYgDX1eZ5mO1gIDy7buBChW1XdUyaTL33vsFqqquY9y4MZSUFGPbFrZtIwSkUiZSehHCw7t7d9DU1EA8HkdIgabJEauds/JCILQz4IUg4waaJunu6uMnP36Yrq7e9OukgB8BP/y4CChTkXwTUJSVu1m4cCHf+c63Wb16JboOUuZOY9s2ti3Izx9FTe1Rdu16k1Qq6YKQWcClQCpQUl0f7vcuKWfAp19a13W6unv47rf+DcOIpx9tAp8Dnv5jCJDKJZYAXwNuy/iJruP1ernhhht48MGvccUVS4hGB7LFibtEjsB2HKTwYcRivLF9K52dJ/F49BxQmpRnBw9nXEJqyh1GgpdSgEqPzS3tfO/b/45pWtkkLAcOXAwBE1Qwuw+4PP3HoqIiKioqWL++invv/SKTJ0/AMAYxzRQgM34rpMRMWdi2IJm0qK37gOrqPdiOha5p7qqfF7wb+d17JEKTSDESPAq8yFiFRNME217dydNPbcG2nfRtjwP3KjI+MgiOB34LXJm+WFFRwYoVK6iqWsu6dWsZM2Y0UWOQULgPKSRSetWLSxW4NMxUEr/fz8tbn6S1rRF/Xh66piGlhDR4kW3mZwGfNvuPAC+lwHHAiMaIRAwqpk9h5qxp1Nc1pW9dmaU2P5KArwJX6rrO8uUruP3221m1ahULFy4AwDIjxOMRNOlB07wIJ/12EqkIEFKjqCgfx7GprFxI/0A3lmkiXRQKVHZKQ33XBc+IQDgcvEuKVBYSjcQJhUIYRgxLBV2fz5uNK/9CU7wOfMbj0bnvy1/kW9/6OjEjTnNzG5ufOURRURElxSUUFBRSUFBAMD9IYUEBeb78ERO9+ebrHD16mLvuupvV11axc8d2TNtyFZ1Q0VyBF0hIgx+e7sTw6CTQJEhdEo9Z9Pb2YMQMpRoFmqbh2OTGo4tQhjowXtM0Ro8qIeD3ouuSJUvnEwqVc+jQIXbs2E4oFEbTcsVKIBCksLCIoqJiWlubiUTCWJbF888/x113beKKFVezb98uwMkydRe8kCDT4NMiR56xWCFFBr8mwbYlp9v7GAoNZjKJlDJjEULa2f4PYCmRdEEEpEzT8kQNA9u2iRlR4kLg9Xq55ppVrFlzLW1tJ/nggw9obz9JKpXCsi0sO8HAYDf9g11IIcgv8APQP9DFK68+zy0b7sA2Td4/vA8cR5n3MPAZt5CZRJIGLxA4CPoHovT0dGOrytCd4wx4qQgcRkDoYiyg3zTNsZFINCNEAFLJJMlEAiklZaWl3HzTTcQScZqbmmhta2FwsB/DMEilUq7ZivSqCbp7Onh9+0tUXX8zmkfj4MF9WJZ1xudz4kGW0JGgSw3LdojHknT39BI1ImgqmGrnAC+EHL7e9kURAIwNh8I5cUPXXZVsWRaGEcUwougeD9PKpzFr5ixC4RDdPd309vbQP9DH0FA/tm0jpSQQ9NPX38UfdmzjqhXX4vN6qN7/DqZpZmKBUNo+LXF13YNH9xKJRBkYGGJgoB9HOAq8UKl0OHglqhjhAtGLcYEBgHg8TiqVUqLHw7EPa3Fsh+nTp+H3+4nH41imSTSVQgiBrutMmTSFyZMmE4vHMKJRuntO0dXdSSQyhNfrIxTq4d33/sDSxVeyfNkqqvfvwbRMF4CQgOsahYUlxGMJ2jtOMTg4SMpMomu6Srkix+dt2+FkWzuBoJ+JE8e6JGoC285Z9MTFWgCJRBLHcWhtbePrX/8ejY1NOI5DWVkpX/nKvVRVrVUa310z0zQxTdMlQ2qUFJcwatQoZs+eRyQcor2zhVOn2hgK97P/4G4unb+My5Yu58Ch97AsG9u23CBaWEJDQwPdPd2kUkmklOianiWXcwNeIhHjzTd2s3hJJZMnjUdqAmEKEonUH0WABqwHFlZUlLNo0QI+//n7OXDgEJFIlEgkSldXD1u3/p7CwgIWLpw/QvcDOI6DZVlYpoVt2eT5/EwYP4WZM+YxZlQpsViUlpMnKJ86g6LCYiLREDOmz2ZoKMS+994lFA5h21bG17PB27ZNLBYnLy8PKQXJZJLqfUcoL5/ElPIJGRn+7p73iUSi6Vc6DLx4IbEgYwGWZfPznz/Chx/Wn/XG73//x0ydOonrr1893N9yhmVZWJZFMplASo3CwlFcvnQVyVSSaDTMzBmXMHHiVB5+9BcMDQ6ge7w5/pytCZqb23ntlf9moD/E2nUruWb1MqTQMhsm6T0ITeokEzlbAfELtQCZJuDUqdMcOXJsuC/ljPvv/wb9/QMZNxhRWAhBa+tJ9u6tpqamjlAoRCJmMDjQj5lKMapkDL48P9tef5VwaAiP1zuiBJbSrRdOn+7lxeffoLfHLbq2v76LcDjmAhdnniekQNd0LCvnvZMXQ8BgOgjG4+cnLho1+NnPHsbv95+TgKGhEA899Bhf+tLX2L//fTTd9WczlcI0U1iWRfvJVqS6ntEHWWZvWjb79h7CMOIsW76IGTPLsW2H4/XN6LqWUYrpokrTNJKp1P/OArw+HwUFBR/5hS1btmIY0bNageM4VFbOYdy4sTQ3t/L440/T19eHruvuyYYDyWQCV/67wC3LpqWlg57uPlcnSkkkHKHm2HHWrF3BxlvXcdsd6/H5vHS0n0JqMkO2zBCokcolwLjQNJghIJVMsq5qDaWlY877hXA4zP79h/B49LNaQHt7Jw0N7nZ9XV0D99zzAIODg0ghcBznzMtLiWHE2fbq27z4whu88PzrhMIRpBQ0nmgjEPBz2bKFCCEYPbqEklFFhEIRpR2EigOu5WiaRiplDpfCF2cBoVCYlSuXc9ddt2cmXr16FT/84XeGrTLU1NSjaZoqUSWapqHrOrqu09TUzOHDRwEoKSlh+vRp3HffgwSCgayyVuI4Drt3VnPsaANGNEZvzwCtze0I4Qa/iZPHUVAQdLWAJigszCeRTCE0kTF/t6QGKbXhCx66aB1gGAZ9vf386Ef/TE9vL8//7iXGjStj7dpreeqpZ2lsbM6Y+dBQCCk1+vsHeOihx3jlld/T29tPWVlpTpqsrz9Off1xyspKqa2tZ+7cOVmxIsL7B44xc1Y5s+dM5+233qW7qw8pBX09/cybPzsDHkD36NiW7ZbYSrOmN2SckVnJvmglmEgkSSYTGEaMX/z8X5kyeRIHDhzC4/EwY0ZFhgBXJmucPn2aBx74Brt2vZu53t7ekWteQrBg0XyWL7+MqeWT1e6wu/9/vL6JoqICbvu7GwgE8ojHElwyp8JdvnCU4uLCnFSXTCQJ5gdyYo9tOdTU1HH4/boR8fpiLCDmEpAgHk9kov2DDz5AdfUBOjo6ycvz5fi5x+PhkUeezAF/trF4ySKe+82TFBTkY0QNPLo3o+1Pneph4qRxBAJ5CCFZfd1ypJTYtkMymcLr1UHtLDs4DA2GmTR5fGZhm5pOUl/fzMEDxxg9umT4o5MXQ4Cj0kZeLBbPMByPx1myZBGdnafp7x/MISCZTPLkk5vPO7HH4+Gb3/wKHo+OYRg4jo3jOBm1F4kYWJbFybbTJJNJfD4vU8snIoXEsW1SSma3tLRTvfcwPT39TJxYpirVFO/tPeyeyXk9XHrpAt56a2f24yMXWhGmCRgExhmGkbOzYlk2iUSCWCyWk+oam5oJhcLnnXjDhhuYVzkXy7IyKdBx3IdJKdF1jZbmdp7bvBXbcXAchxtvvJaly+ajaRpDg2GEFLS3neLQwRoAZs2ehmOTifiXL1vIyquXMnFs+XACZgGzgZMftTskFQFDAFHDwDRzN1Lj8ZEEnD7V9ZHM7tz5DqFQGMfJCkmO425zSJg8xdXxoVCESDhKNGJw+HCN2m3y09baiRSCefNnUTKqCICOjm50XWNe5Uy+9o172PS5W5k3b/bw7TBUL8JidZJVpdprPOezgAGAcChMyjTx6GdyfF6eb4TyE/L8G666rtHT08t3vv0DfvnLn+Pg4GBjOzaOA0JoXLlyiSI3ga5r1Nc2Eg5HkVIyfmIZx+ub2fL8G/T1DjA44Ga1utpGliyt5B8+fzsIdz9BkxLbGhHwrawFHgVcASwFWlSDRTh9T7YLEI0amKkzBDiOgz/gz8nhQkBRUeE5wQeDAW655UZAsHnzb2lpbWXq1CmqBHaUDoD8giC3fHKdqwg1wYsvbKezowshBYsWzaHm2HF2vv0eAPMqZzGtYhLzF8x2Y5QC7x6T6RixEVaeXQ47CqxQljBTNVbUAEP6CBdQ5mQDwrEpGVXCuLFlOWcpY8tKz0nAtddexb/85AcE84McP9FIQ0MjU8unYOP6OY4bA5JJk1g0xpgyN4JHwlHGlI5CIFi0eC41Hx6no72LKVMnsPG2dZSWjhoBXghBQX4hvb0fDH+N1LmKVfVzOjAG2CuzLSASjpBKpVyTddwV83o8VM6fk0mFQgjGTxh3zmLo1KkuqqsPMtA/yN1338mcObOwTAtslwBH7QzvfHsfb721B4CGuibqahuZfck0NE0SCOTx6U0b+MKXPsWmv99I6Zhc8O4Wms74sZNpaTrFli2vZb9G7AK2xS0VJxbkxIBIJEoymcS27cxqJZNJqqrW8qtfPUNTY4ubZBNJ/P48YrH4iGLo4MEj/OMX/4lHHv0PVq+5Ck1qaj7XGk3T3UTtaD9NU2MbMSNOS0sHwfwAM2eVZ8rjYDBIfr4rhYXMXnkHf14Arx7k0Yd/zdtv72FwcCj7NT5UafBCRjAnC/T09pFMultj7mo5mKbFxIkT+NznPoPX68WyLN7esZtrrll51hkdx6G3t5+aD+vAESr9uXNZtuNuq0nBtIrJJJMpVQsY3LRhDcXFBWfOG1W6zAUPXq+f43XtPHD/d3nxxdeGgzeAN1WQ4wKsoDl95D0WuKOvtw/LNKmYXoGua3h0D0IIUqkUV1xxGR2dp6itqWNwYIixquQ911i0eAELF1ZmCh/btvF6PPj9fo7VHKG4pIDjDS0Eg35uvWM9i5fMVYcvCrzqH0AIZUUWkXCc3z37e5544hkikZxFdoBu4Al1Kqyd5zA4LfyOAHVaFnMbgJIjR47xqyc309zU6nZoeXTyC4I4DqxbtwYB1NY2cPToh+i6fs4dpI0bb+KSObMy5u84Dh6PB3+en5rao2i6ZOVVS1l19eVMnDQ267D1DHipuQev3V297Hq7mice+y21dQ3DH9UP7FVtMrWqYetsesdWltGq7u8mi6kw0AFcl+7ZO3GiiW3b3mTf3v20tbYTTyQoLi5k/fq1LFo0H6lpDAwMMDQ0svLMzw+ycMF8PF4vY8eWumpQERDwB6ipP+oenmZ2e8UZ8KoU93g9RMIGu3dW85tnX2V/9RESuft+KWCfOtl+RaU+77DV1rI6TBuBD5QWMM/WIKEBV6segVuHgyopKaaiopwVVy7jppvWM3v2TD6sqaWuroFjR2upqamjtqY+Q0ggEKCwqICf/PSHLF68ENM0CQaCjB49mi0vP0s8Hss6BVbghUTTNXSPTvW+I7y1fQ8njreezcpOAFsVoNgwk083ekSVFD6pgnzsQltkClSf32eBL6pWuJwip6iokLnzLmHTpk+x8qoVJJMJQqEwsVic3p5ejhw+ysFDRziw/xDTKqbyzLOPE4ka5AeCFBUX8eq2F4jHjREr7/N56enp59dPvUxtzQmSyRFFnQlsVk2SRlaXWHYHai9ul3mnqgrNP7ZHSCiT+gTwXeDSs900ceJ47vzUbdxxxy34/X5V2Lg+L4SrLgOBALZtEwwGKSku4rXXtygLUGpOugv4+207eeXlt7LbXbJHter8CJ3lvS3l2w3plP6n6BO8VHWBrlddoXnDb7h5ww3cetsGKqaX48/zo2kyQ4btOAT8foqLi3h9+0skEgmkEFi2ReOJkzz3zCt0dnSdbWenE/gv4FBWQWOpTz/QpMD/2TpFS1Tn2CeAearFJmdUVs7lEzdXUTl/HlOnTqKosIhkKkl+MIg/z8eOXduJxQza2jrZvWs/u3eetfH7NLAHeC3Lf6MqYJ++kFL3T90qmw6aa4DVquLK7bMrK+Wyyxez9LLFLFu2lKlTJzPQ18vuPe+w771qdu2sZqB/aPjXYsrHdyqTNtRK9wE9adn+cY2Pq1l6mrKGjbid4jkHDF6fl+kV05gwYSyxWIy2tg7a2zvPNs/7wHYlZppUQAsraWvxNzACiowHgGZG/tvLuT6DwI+BVSrr+M6j5v6mxhpg23mAJ5V8ncaf779X/iJjAvBTJVW7gDbgKdyu87/o+B9v4NOflk9dMwAAAABJRU5ErkJggg==',
          ),
          32 => 
          array (
            'extension' => 'png',
            'contenu' => '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',
          ),
          24 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABgAAAAVCAYAAABc6S4mAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gcQEzkVe+AoCAAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAAATeSURBVDjLnZVbbFRVFIa/s/c+Z2Y6M6VDgBawtFRuotwvAgXSRBoKWkgVAa0IEvRBCEREBBPQYCQREjCIqA8mhMREEGt8KIgUUuQSQEpbaGlLW0pvcmspM51LZ6Znjg9MiRBC0PW4s9b/5//X2mvBfw87MM3pTCgBrgEjn5SsPQVgBrAYkPH8iTNmTs3NzBzPhQvlFB091aRpTLEsbjyuWD0BeADwDTBz+vSpvT0eD7puY3bODIIhLw6HnbS0AXR2BgadO1t6BfA8jYJEYJRSamtycr+Zc+fmsGvXNux2G/X11/mjqJBIpAvDMJBSIHUJlsVnm3bQ0nKzWdfV4Gi023wcQTqwBMjNzX1lUnZ2FsuWvYXbnUR9fS1l5aXU1VfjcNhRSqKUQEqFVAJDlwSDQbZs2U1z498FQD7Q9W+CjUKoTzZuXO9avWY1uoridLoRQudi6UVOnz6GGTMxDP0+uBRIJZFKIgQoJTFjJpUVtXy59TuAQUBzD4FMSUk+WVVVYtjsBh0ddzFjgkgkRltbOw3Xr6GUoCscRNfVfVukROoCKQSxmMXtO+3cuHELKwan/vwL4CvA+6DJsVgs6Pf7Ema9NB1/IEhJyQWu+ny0tDbjD/h4cfJ0kpP7UV1TgW44kLpACUlb+106OrwIATabDSsW7sGMPTRFQmi3L12qSF+8eBl5ebmsXbsKr9fHBHMCUkouV5YQjoQZNnQEt9tvYJmS6poqNCEeqAINqWQPpvUQQSAQatu8eWt6dfVVysoqSExMZOHCPKSQhIJdpPYfwuBnM9A0jW3bvyDU1YVu6Bi64uatNtxOJx0dXlLTBgKEH1UgLCvWFgyGHjzs319AIBDE7XbR1NTMgteXUFtbS3NzIwgNw1DouuJY0RkO/nSI8vIqLA1shgEQfVSBkFLdeWd5PunpqaxYsYSyssv07p1Efv67rFy5jrq6BubPewOXy0l3tJszp0vp9AfwdwZZt+E9qq7UYbcZ6MroUfAwQXd31PvMwAEUFx/C5XLh8SSRnZ1HYeERampqcbmcXGsox+vr5ETxOfz+AGUllUzNHA8a2O02hNCoq73+eAWmGbvr6/Tj8SQRCoYIhUKUl19+kJCZOYWOe140TaOxoYWx40bicNjpleTiZPF5+vTxcLjwBOfPl/WsnjHxBeiKE5i3Av4A4UiE5JR+KHXfY4Ds7CzS0lNpbmpBA1JTB7BvbwGd/gClJZVcb2hFKcWqNUtZtGg+gAmkAWOBl4GhIhqN3r53z0s4HCZnziyczgSM+w3j6NFi5szNJiHBQbdpMic3i6XLX2PypNFcrWlg2PDB5L89n0gIPlq3hfgPjsRtUsALEhBtbe3v+3x++ib3ZfSo56msrKa9/S4AWVkzGDI0g0gkzIb1nxP0Bxk/cRR5C2YTjUT55cAR9u7dD1AE/Az0jGQjcFaTUgrTNKcB+9xu1+CMjHQWv7mACRPGUXDwNw4c+JU93+9g5HPDKTp+GMuySOzl5Nuvf6Ticg2RSLQJ2A3cilt0MQ7eBcQeXdcj4jdgjNvt6r3p04+1nJxsot3dCGFx9twJGhtb2bn9B8LhSBdwHNgTP0YlQOvTnsU04APgkMvlsnLnzbE+XLfKGjNupBX393fgVaA/4Ob/hqZpDiAF2Al0AufiE2J/Wox/AOQF6XYdc/8qAAAAAElFTkSuQmCC',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAOCAYAAAAmL5yKAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gcQEzkzqe2t9QAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAAAKbSURBVCjPhZNbSJNxGMZ/3/bt2/e5Td3c3OZKy050uqiwIi1LJA9YQZBGQRF1URfdBhFEdaNdlUoRURZiUKIURYRmp2lRIYJZqZSnMmetlSbbtM39u0mJSHpu3pvn/fG88Lwws5yAA1i1cGH647g47QVg/tsk/WMxDbiwfPnSlQkJtlhefpbVYjEaP374RHn5lb5oNJr+p1n/eyYCq9PSUqtzcrLPPH3aNP/gof0mS7xsnoxNyJqm4klNQcQmrV2dPVuBy4CYSnC4oKDgyImTx90up1Xv8cymrv46Q75BZFmHUZExKAZkgw4tzsjVqnru3nmYBzQCyLm5myrKK8oIfBtldDRET28zkiShqgqSJJAVGUkHP8aC9PT2Y7dbAdSpE3SRyE//+XMX+B74jMWiJ9GqkWS34HTZiU+wgNDT2zeAzzeMoijIBj1AbBrQ3v5muLLyIsXF+xgZGcPtdDMRgm1bShj5PkZXdxeN97z0vP9AJBJFMRgBItMAj8ftc7udZGSsQIgYx46dYtfu/ezdc4ChoWHC4+MsXrKAFm8rqmpAVTWAyWmApqmBtjYvSUlWiopKqKqqIRgMUVNzgwdNLbzpeIfL7cBqi6f5SSsiJgAMgAwgR6OTvlB4HFmW8fu/YjLFsTkvh97+DoLBMJIOmr0vsdkSKSzayGB/QACrAAV4rQuHw4Odnd0U79yOzWYlGAyRmbUWhGBD9hpyctfhciWzo6SQwYEApaUVXySJMcAELJJUVU0xm0wPU+fMWpSdncW8eXM5XXaWa9cv0ex9hMOZwIvn7dy+eR+//1utJPFMCF4Bb4Gvf7ZyGdCQnOz4XFtXLRqabonyc6fE5vz1AhgAjgKZgMZ/5AYyHQ77A7PZ9AnIB2wz/A2/AClr6Ds49vzBAAAAAElFTkSuQmCC',
          ),
        ),
      ),
    ),
  ),
);

?>