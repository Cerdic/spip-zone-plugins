<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-04-15 14:09:23
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
    'nom' => 'Import_ics',
    'slogan' => 'Importez vos événements',
    'description' => 'Importez les événements de sites distants dans votre base de données d\'événements SPIP',
    'prefixe' => 'import_ics',
    'version' => '1.0.0',
    'auteur' => 'Amaury',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'date',
    'etat' => 'dev',
    'compatibilite' => '[3.0.7;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => '',
    'inserer' => 
    array (
      'paquet' => '<necessite nom="agenda"/>
<necessite nom="icalendar"/>
<necessite nom="cextras" compatibilite="[3.0.5;[" />
<necessite nom="seminaire" />

',
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
      'nom' => 'Almanachs',
      'nom_singulier' => 'Almanach',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_almanachs',
      'cle_primaire' => 'id_almanach',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'almanach',
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
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Titre de l\'almanach',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'URL',
          'champ' => 'url',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'URL d\'origine du calendrier',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Article d\'accueil de l\'almanach',
          'champ' => 'id_article',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'selecteur_article',
          'explication' => 'Choisissez un article qui va recevoir les événements importés',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Almanachs',
        'titre_objet' => 'Almanach',
        'info_aucun_objet' => 'Aucun almanach',
        'info_1_objet' => 'Un almanach',
        'info_nb_objets' => '@nb@ almanachs',
        'icone_creer_objet' => 'Créer un almanach',
        'icone_modifier_objet' => 'Modifier cet almanach',
        'titre_logo_objet' => 'Logo de cet almanach',
        'titre_langue_objet' => 'Langue de cet almanach',
        'titre_objets_rubrique' => 'Almanachs de la rubrique',
        'info_objets_auteur' => 'Les almanachs de cet auteur',
        'retirer_lien_objet' => 'Retirer cet almanach',
        'retirer_tous_liens_objets' => 'Retirer tous les almanachs',
        'ajouter_lien_objet' => 'Ajouter cet almanach',
        'texte_ajouter_objet' => 'Ajouter un almanach',
        'texte_creer_associer_objet' => 'Créer et associer un almanach',
        'texte_changer_statut_objet' => 'Cet almanach est :',
      ),
      'table_liens' => 'on',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
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
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAEYklEQVRYhbWWy48UVRTGf+fcW9UjL3FswAwsMGF8xYVxAQtXxsiCuBU2OjPMX+D4P2hciY+4ImlmuoHEhQtWRiVhMQsTMjFgjOuBIAExxjAz/ajuusdFVT9qHj09DJykU/VV3T73u985X90rS0tL/tc/7l5bvH3nTBqMpxVOhZcnXlj+8N03J4HOVuM8MLZ4+86Z77/8hObaylMjMLZ3P+c+/eq4me0RkcdbEjCzsTQNACz/9QBEQeTJZzYDC7z2yn7yvPGw4d7MNBPe0LgEz+1BoujJ52+3oVEHDAPMTGXIgnz/VnD79vLfoTLSewIKBMBGwOTXg4/+yd9uHzoIXFwCYNyM8UbCoWbCL5fmOdxocmi1zpFGk+uVSxzZBJfrDcpmKBCVSr0FbBcDChiqGR8HIIonEHUSSq0G1umgqow11yg11gghRdX1sDcDMcaAMZdNPUz6bsjNmzcPf3F58eEPX89xf6XBwwN7e3KWgOp7Z/n91ZNYu42IICKYGWYBEe3hYAaRR3yEddq4NCWOHF43NrVT4a0TL/12+tTkqYICvtPmxsUKH83MYGlKyQm35r7hp2/nqK8+7hc+G76JxpJNZgZYRniTVecWffv9kyf29AkEw5nhWw3c6gohTXGxx1pNwLh7/+EzsWifgBhOs/rHTgliRE4Ry5b7rCxasKGqwznlypXLmBmlSPNG0mdmUe29EcldIExPzzA1NcX01AzBAoZtsGi5lfDzfJVyK+HFenMoHm8kjFtGo5unG34QqGre4YYFw8iuGyyqihNBRXDiQMCJ2xqrFeboa9IjkInmVPHeE0URwTl85LMSGHjL5HokguVETgN/T39MOgLulsSv23AHvoSCOkVVqVar1GpVatUFRAUs9Cx6rB2YaLaZaAeuz9eYGBEfawduXKzgO+2NCnShqgDG7OwsIaQ456ktfg5WtKil2Tu/toJbXcXSzgjYZf83g7ChBLkGqhgQzAjBcA5CCJhQsKjDcE7wIsROcOgIWHFkeXJvb9KEkjWXU8XlTSci6DqLhhBwzhFFnqtXr5Cm6bZYNSOh6hi0YYEAgIkwv7AAwFjss64tWHS6N0GtVmNqanS8sLDQy9etu4fiJ332/PkCocr1z/L+GLBo/gsh7Aib9e1cUMC54sNiX2QbzKBFMzkdURTtCHvv89JmzQ7gRSRMHn3+z7NzF97opCHfychkF6EUe0ALFg0hoDmhnWDVLM/6Hkg+eOf1c61Wq2xmBSlCCPF31279mJWgb9FeTefnmZ09PzKuVCp5nr71vYjUoyi6G0XRg/UE0jQ92C/FgEXNEDPS/H5UbHme9T3Q6Z7bhx2hBi2qqgj07DoyFkFlkyYcNboWNctOO+rcjrBtssBtCQyz6G6im3dr/+UxzKK7iW7eoQqISDha3nfv7NyFYwWL7iZE8E45Wt53T0SCLC0tbTnWzA4kSXI8SZLx9Q7ZHQcJcRz/G8fx8nYK1OM4Xo7j+P7TJgAkIlL/HzTWviqSC0lGAAAAAElFTkSuQmCC',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAGMElEQVRYhb2WX4xVVxnFf9/e+/y7w9xpaSv9gyR9oiQ2JkVNgRiNoTRFrCFS+tCkPlIfjPFNEx/0yTcTTTT0xWiNMXYKdRjslA5UYy0YmZJYTG0Q+4ciZQYc5t65czn3nLP39mGfmXNBbGzrdCfnJHfdfc639lrr22fLzMyMWffPZybWX/jlTrzjIxmiOH/nY8/N3rXnywZI18+O72THj8FZQEBWqbCvb0qz/tg3d87etSc13vsUnUBVwCs/BNGrS8Bb2PwN0Ane+9R47xVxC/qXYHR9ILCaw9tQK27hvVcGACPw7NeaSatqAXDyV7Dp06E0wOPJBN//3h24es5q1hdAAd9+9l2+zvlAIC47ZNzB0/ufRABfUxVRiCi8d/i6Qz4UhuCBvU/sIy47jQLOVxjAiOfeR5/A28BYafjr+JN8Yu++0CAfEhMNp3+9H1PXXCFgXWColKAczQN1R4oD+T9gSkKN4ZoGoHIeAbRSKFWbBCgF6n/AtAatDEaD9ddiWgOueVYrhdQ1VwiUPhAQpYki8HUKlQGlDXEE7j0wE0Ec3UQrBqcbC+LoJpIIgtggJtSQumZjga0VMIbTB36Or+URrTFxwqsHn8Jbe0NMUESmzeH5PTy3H+r3IhIIqoMHKasuHocohTYGqWsOWeBWWu++PV9tQmjg1IFfsHnP47jqxpgIJBEc/glsvx/ckNxH/wSfemQ3gzIQEw2nDjxVWzCcAetRgBJBHCsERADvQ4j+GyZBbufAmSbA1JhyYZ748KwSQV2fAetrBUQw0nirJXhmVFjR8spEaYw0Sddh88AnDXmvA6YlFPESukBqAtY7QOoQuqCA0YbINCE0BrJ4LWuSoXDVWBJBVROIDYgCSesVU/9W4T/nw6U06ChiYQBSVkC0rEATwpOHxvHOAorYjPH0+Z0c+BErRwVRtfKTz1NUYTdLzBgWOHa4madUUPLExBHKqgPiUVqTxhEzZy6Ri2osKFdC6Nm6+xGcDfJmMYz/ALZvuZbA9An47Fce5GoRZDYRyMQLfOZLD1AMwmq1gROHprjviw8iHiITwvrTn/2G6o0LPPyxAdBq2lBT+1OHUAip9Q78ULhUjYkNl/fBssX8Cp1FKAqLKMFoRV50mZsvWOwt8c5ch1NnLyDlKPu23sPbrxxrCCwnElEhULW3ps6CGwrXchaMhIB5oD+A3FbkRYWzFZMvv87pN2fpLymyud+xrp2ydjRjy8Z1bPr4bSRpQl7YxoKq1tcYjTF1WgGj6jZLr+1vBCIFcQRLAygrj/OKynms9Zx87QyHvrM3mOqhO6jo5RWLeUln4Ln45xex9rpvQRBA89LkJM45BEUShXBNT/5nCH//2z9Q2C7zvT7WCRbN8alnGJSWdTffAkB/YOkOSvqFpZ9XLJae3uV3yeKIjh1SoKy3RSXCFx7eRU2OSIFSf2T7Q9sY2l84OvUyn3toG4sFXOqWFJXnpamDfPLzu3jz4r+4fPYtrINuXtIbVPQHFb3S0+/10L1Zsnabyl5sCNihDdzTrNYqKN0iOWDrrVibgDmgLCxVUVBZT2kdZVnwztwCmzbcRjcvWSrsSvGlvIQr50jTlFaWUZbhhQrALlsggiaES0tYrcfV23R91e2qAYXgvV058TjreHuuw51rR1nMq2uK+4uv09KQpSlJmlFVQweSstZca0MMWN3IbaLoBliYp7QQRwYrHiWa0jrm+zn33n07l7t5KN6/ip39O61IkWQZSZrRaiVUVTncBcvHVThyZHrlmyoiiFK8cGQafz02fZQrvZylvGJgLcZoxqdeZPe2zSz0SxZL6M5fopp7g5FWStrKSNOULEtJ05SiuMGRbMeOB3g/o1t4Or2CvKz4x2yHv8yc5f6NG7i8sMT8ub/hrnYYGVlD1mrRSjOyLKszMIK1Qxn4oMfwdizcMqJZ6Oecmevyrd1buPvWlNnXjiPFEu12m9H2GO3RNqPtNmPtNjePjZEkMW75cywibtft7vja7z6/tbSOITfec4iEm0XzaHqOjXGfiVcrHMGicCQPL/PeD13hYJqmaQ9AZmZm1hRFsaEoiludc+YDivG+R5Ik83Ecv2WAPI7jc1EUzXnv1UdFQEQKEen9GxBUOLHjDYGLAAAAAElFTkSuQmCC',
          ),
          32 => 
          array (
            'extension' => 'png',
            'contenu' => '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',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACV0lEQVQ4jZWTzW+MURTGf/e+9513SlJsfFdSLNiRUDOC6VdQpMT4isSysbD1T0gXVhWJxMaCoBRRRVuKmU5b0URorDSpSDBUlbbz8d57LGYilSD1bJ6cxXnOc06eowYun5aar5cADSjmBgEcg4tOoOT8cqGhDZz7v36toecUBg/IvoA37aDm4kJAHKxLggfqyNUxuXC4ircF0F5Z4x9wDsTC6gi0XHuH8fITBFQxT2Co/QZKa5Sa7UKhKKmKCDYsUHMgSQB4+Ql0wRbxgKgG39PUHzqA73nUJffjG0NDspmoqWRnch9Rv5L6Y0kiunTygi1icqFFAUZDJIgw2HmfIIgydK+baFDB864Mz6YTpK+AdeDdfsympgQayIUWnQsdCvABoz1q9+wiahbQ0NRI1CykfncMDdTFSlNrmxP4irKAw+StQ5cdGD/g+aNhurI1PLwMViDoe4sV6E2X6sHuIWKNm/lehGkLOheGABgFEd9QV7cRBdTGQSvYnqjm4KrXtB4Xjla/pLFxM2PZGdpuZkguncbkig7KKyilSKdeETroSZUmplIjODVFun+AO28maX0ySlBRwZ4NVSyeGYe1Z3pFRKQgIn1P02JFpL9/RKTMTkRS6YyIiJw6d1fej0/J97zI8GhWOjpuiSmE9peD7dviAMRi63/jrfEtAIxOOjzj83rsEwvsBLl8AW2RWZH5e5DPdg6zd9MaPnz8wvz8Z1auWEZoLerkxQd919+5HUUryCyx2Um0StGy5BvxyjwFp3ACKIUx5ofKZDKrwzBc+O8P+DOUUu4n1NgJ8fF1wIEAAAAASUVORK5CYII=',
          ),
          24 => 
          array (
            'extension' => 'png',
            'contenu' => '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',
          ),
        ),
      ),
    ),
  ),
);

?>