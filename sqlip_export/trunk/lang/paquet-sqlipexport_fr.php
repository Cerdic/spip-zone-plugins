<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

        'sqlipexport_description' => 'Appliqu&#233; sur un site install&#233; en SQLite, ce plugin tente de cr&#233;er un fichier mysql-dump.csv importable dans un base MySQL. Il utilise un squelette et les it&#233;rateurs pour produire ce fichier contenant les d&#233;finitions de tables et les contenus du site en SQLite. Pour lancer le t&#233;l&#233;chargement, le webmestre doit appeler [->../spip.php?page=dumpmysql]',
        'sqlipexport_slogan' => 'Un dump MySQL de votre base en Sqlite'
);
?>
