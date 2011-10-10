<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * #SEO_URL
 * Renvoyer la balise <link> pour URL CANONIQUES
 */
function balise_SEO_URL($p){
    $p->code = "calculer_balise_SEO_URL()";
    return $p;
}
function calculer_balise_SEO_URL(){
    $flux = generer_urls_canoniques();
    return $flux;
}

/**
 * #SEO_GA
 * Renvoyer la balise SCRIPT de Google Analytics
 */
function balise_SEO_GA($p){
    $p->code = "calculer_balise_SEO_GA()";
    return $p;
}
function calculer_balise_SEO_GA(){
    $flux = generer_google_analytics();
    return $flux;
}

/**
 * #SEO_META_TAGS
 * Renvoyer les META Classiques
 * - Meta Titre / Description / etc.
 */
function balise_SEO_META_TAGS($p){
    $p->code = "calculer_balise_SEO_META_TAGS()";
    return $p;
}
function calculer_balise_SEO_META_TAGS(){
    $flux = generer_meta_tags();
    return $flux;
}

/**
 * #SEO_META_BRUTE{nom_de_la_meta}
 * Renvoyer la valeur de la meta appelée (sans balise)
 */
function balise_SEO_META_BRUTE($p){
	return calculer_balise_META_BRUTE($p);
}
function calculer_balise_META_BRUTE($p){	
	$_nom = str_replace("'","",interprete_argument_balise(1,$p));
	$retour = generer_meta_brute($_nom);

	$p->code = "'$retour'";	
	$p->interdire_scripts = false;
	return $p;
}


/**
 * #SEO_GWT
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 */
function balise_SEO_GWT($p){
    $p->code = "calculer_balise_SEO_GWT()";
    return $p;
}
function calculer_balise_SEO_GWT(){
    $flux = generer_webmaster_tools();
    return $flux;
}
