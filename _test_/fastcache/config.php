<?php

# A mettre dans spip.php a la racine :

#
# Reglages : decommenter ce que vous voulez modifier
#

// chemin du plugin (obligatoire !)
define ('_FC_PLUGIN', 'plugins/fastcache/');

// debug : ajoute un commentaire en bas des pages concernees
#define ('_FC_DEBUG', true);

// nombre de secondes de validite d'un cache rapide
#define ('_FC_PERIODE', 180);

// charset du site
#define ('_FC_CHARSET', 'utf-8');

// gerer les stats de spip ? contradictoire et couteux
#define ('_FC_STATS_SPIP', true);

// repertoire du cache (*doit* exister)
#define ('_FC_DIR_CACHE', 'tmp/cache/');

// chemin du fichier meta_cache (s'il change, on invalide)
#define ('_FC_META', 'tmp/meta_cache.txt');

// Ajouter le code pour les png transparents sous MSIE
#define ('_FC_IE_PNGHACK', true);

// regexp des urls a optimiser
define ('_FC_QS_REGEXP', ',^(/|.*backend)$,');

require _FC_PLUGIN.'fastcache.php';

?>