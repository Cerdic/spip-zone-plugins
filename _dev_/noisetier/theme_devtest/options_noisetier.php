<?php

global $theme_titre, $theme_zones;

$theme_titre = 'Devtest';

$theme_zones = array();
$theme_zones['entete']['nom'] = "entete";
$theme_zones['entete']['titre'] = "<multi>[fr]En-T&ecirc;te de la page[en]Head of the page</multi>";
$theme_zones['entete']['descriptif'] = "Non indispensable. Possibilit&eacute; de d&eacute;crire la zone";
$theme_zones['entete']['insere_avant'] = "<div style='width:100%'>";
$theme_zones['entete']['insere_apres'] = "</div>";
$theme_zones['infos']['nom'] = "infos";
$theme_zones['infos']['infos'] = "Infos contextuelles";
$theme_zones['infos']['descriptif'] = "Pour afficher des &eacute;l&eacute;ments sp&eacute;cifiques à la page en cours.";
$theme_zones['infos']['insere_avant'] = "<div style='width:150px;float:right'>";
$theme_zones['infos']['insere_apres'] = "</div>";
$theme_zones['infos']['pages_exlues'] = "sommaire";
$theme_zones['contenu']['nom'] = "contenu";
$theme_zones['contenu']['titre'] = "Contenu";
$theme_zones['contenu']['insere_avant'] = "<div style='width:190px; float:right; margin-right:5px;'>";
$theme_zones['contenu']['insere_apres'] = "</div>";
$theme_zones['menu']['nom'] = "menu";
$theme_zones['menu']['titre'] = "Menu";
$theme_zones['menu']['insere_avant'] = "<div style='width:150px; float:left;'>";
$theme_zones['menu']['insere_apres'] = "</div>";
$theme_zones['menu']['pages'] = "toutes";
$theme_zones['pied']['nom'] = "pied";
$theme_zones['pied']['titre'] = "<multi>[fr]Pied de page[en]Foot of the page</multi>";
$theme_zones['pied']['insere_avant'] = "<div style='width:100%; clear:both;'>";
$theme_zones['pied']['insere_apres'] = "</div>";




?>