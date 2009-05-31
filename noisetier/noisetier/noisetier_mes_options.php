<?php
include_spip('base/noisetier');

//Définition des pages gérées par le noisetier
global $noisetier_pages, $noisetier_description_pages;
if (!isset($noisetier_pages)) $noisetier_pages = array();
$noisetier_pages[]='404';
$noisetier_pages[]='accueil';
$noisetier_description_pages['accueil']="<multi>[fr]Cette page n'est pas utilisée dans la distribution {fraichdist}.</multi>";
$noisetier_pages[]='article';
$noisetier_pages[]='articles';
$noisetier_description_pages['articles']="<multi>[fr]Cette page est renvoy&eacute;e lorsque le squelette {article} est appel&eacute; sans id_article ou avec un id_article incorrect.</multi>";
$noisetier_pages[]='auteur';
$noisetier_pages[]='auteurs';
$noisetier_description_pages['auteurs']="<multi>[fr]Cette page est renvoy&eacute;e lorsque le squelette {auteur} est appel&eacute; sans id_auteur ou avec un id_auteur incorrect.</multi>";
$noisetier_pages[]='breve';
$noisetier_pages[]='breves';
$noisetier_description_pages['breves']="<multi>[fr]Cette page est renvoy&eacute;e lorsque le squelette {breve} est appel&eacute; sans id_breve ou avec un id_breve incorrect.</multi>";
$noisetier_pages[]='forum';
$noisetier_description_pages['forum']="<multi>[fr]Cette page permet de poster un message dans un forum.</multi>";
$noisetier_pages[]='login';
$noisetier_description_pages['login']="<multi>[fr]Cette page permet de se connecter à l'espace privé.</multi>";
$noisetier_pages[]='mot';
$noisetier_pages[]='mots';
$noisetier_description_pages['mots']="<multi>[fr]Cette page est renvoy&eacute;e lorsque le squelette {mot} est appel&eacute; sans id_mot ou avec un id_mot incorrect.</multi>";
$noisetier_pages[]='plan';
$noisetier_pages[]='recherche';
$noisetier_description_pages['recherche']="<multi>[fr]Cette page affiche les résultats d'une recherche.</multi>";
$noisetier_pages[]='rubrique';
$noisetier_pages[]='rubriques';
$noisetier_description_pages['rubriques']="<multi>[fr]Cette page est renvoy&eacute;e lorsque le squelette {rubrique} est appel&eacute; sans id_rubrique ou avec un id_rubrique incorrect.</multi>";
$noisetier_pages[]='site';
$noisetier_pages[]='sites';
$noisetier_description_pages['sites']="<multi>[fr]Cette page est renvoy&eacute;e lorsque le squelette {site} est appel&eacute; sans id_syndic ou avec un id_syndic incorrect.</multi>";
$noisetier_pages[]='sommaire';
$noisetier_description_pages['sommaire']="<multi>[fr]Il s'agit de la page d'accueil par défaut d'un site SPIP.</multi>";

?>