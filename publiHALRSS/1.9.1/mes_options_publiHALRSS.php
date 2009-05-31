<?php
include_spip('base/mots_syndic_articles');
## valeurs modifiables dans mes_options
## attention il est tres mal vu de prendre une periode < 20 minutes
//define('_PERIODE_SYNDICATION', 2*60);
//define('_PERIODE_SYNDICATION_SUSPENDUE', 24*60);
define('_PERIODE_SYNDICATION', 3*2*60);
define('_PERIODE_SYNDICATION_SUSPENDUE', 3*24*60);

// Controler les dates des item dans les flux RSS ?
// si $controler_dates_rss = true; alors met la date actuelle pour les publi !!!
$controler_dates_rss = false;

?>