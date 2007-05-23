<?php

global $theme_titre, $theme_descriptif, $theme_zones;

$theme_titre = 'Dist';
$theme_descriptif = '<multi>[fr]Th&egrave;me bas&eacute; sur les squelettes fournis en standard avec SPIP.</multi>';
$theme_zones = array();

$theme_zones['entete']['nom'] = "entete";
$theme_zones['entete']['titre'] = "<multi>[fr]En-T&ecirc;te de la page</multi>";
$theme_zones['entete']['pages_exclues'] = "login";

$theme_zones['soustete']['nom'] = "soustete";
$theme_zones['soustete']['titre'] = "<multi>[fr]Sous l'en-t&ecirc;te</multi>";
$theme_zones['soustete']['descriptif'] = "<multi>[fr]Situ&eacute;e sous l'en-t&ecirc;te de page, cette zone peut servir &agrave; afficher l'arborescence des pages ou bien un menu horizontal.</multi>";
$theme_zones['soustete']['pages_exclues'] = "login";

$theme_zones['navigation']['nom'] = "navigation";
$theme_zones['navigation']['titre'] = "<multi>[fr]Navigation</multi>";
$theme_zones['navigation']['descriptif'] = "<multi>[fr]Colonne de navigation dans le site. Elle est, selon le style s&eacute;lectionn&eacute;, affich&eacute;e sur la droite ou sur la gauche de la zone contenu.</multi>";
$theme_zones['navigation']['pages_exclues'] = "login";

$theme_zones['contenu']['nom'] = "contenu";
$theme_zones['contenu']['titre'] = "<multi>[fr]Contenu de la page</multi>";
$theme_zones['contenu']['descriptif'] = "<multi>[fr]Cette zone est destin&eacute;e &agrave; l'affichage du contenu principal de la page.</multi>";

$theme_zones['infosgauche']['nom'] = "infosgauche";
$theme_zones['infosgauche']['titre'] = "<multi>[fr]Infos &agrave; gauche</multi>";
$theme_zones['infosgauche']['descriptif'] = "<multi>[fr]Informations compl&eacute;mentaires dans une colonne &agrave; gauche.</multi>";
$theme_zones['infosgauche']['pages_exclues'] = "login";
$theme_zones['infosgauche']['insere_avant'] = "<div style='width:245px; float:left;'>";
$theme_zones['infosgauche']['insere_apres'] = "</div>";

$theme_zones['infosdroite']['nom'] = "infosdroite";
$theme_zones['infosdroite']['titre'] = "<multi>[fr]Infos &agrave; droite</multi>";
$theme_zones['infosdroite']['descriptif'] = "<multi>[fr]Informations compl&eacute;mentaires dans une colonne &agrave; droite.</multi>";
$theme_zones['infosdroite']['pages_exclues'] = "login";
$theme_zones['infosdroite']['insere_avant'] = "<div style='width:245px; float:right;'>";
$theme_zones['infosdroite']['insere_apres'] = "</div>";

$theme_zones['souscontenu']['nom'] = "souscontenu";
$theme_zones['souscontenu']['titre'] = "<multi>[fr]Sous-contenu</multi>";
$theme_zones['souscontenu']['descriptif'] = "<multi>[fr]Informations compl&eacute;mentaires affich&eacute;es sous le contenu principal et sous les bo&icirc;tes d'infos.</multi>";
$theme_zones['souscontenu']['pages_exclues'] = "login";
$theme_zones['souscontenu']['insere_avant'] = "<div style='width:100%; float:left;'>";
$theme_zones['souscontenu']['insere_apres'] = "</div></div>";

$theme_zones['pied']['nom'] = "pied";
$theme_zones['pied']['titre'] = "<multi>[fr]Pied de page</multi>";
$theme_zones['pied']['pages_exclues'] = "login";
$theme_zones['pied']['insere_avant'] = "<div style='width:100%; float:left;'>";
$theme_zones['pied']['insere_apres'] = "</div></div>";



?>