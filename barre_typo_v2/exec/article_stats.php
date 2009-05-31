<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/meta"); // Pour avoir le charset du site
include_spip("inc/texte"); // Pour pouvoir utiliser propre

function exec_article_stats_dist()
{
	lire_metas();
	#header('Content-Type: text/html;charset='.lire_meta('charset'));
	#echo "<meta http-equiv='Content-Type' content='text/html; charset=".lire_meta('charset')."' />";
	$texte = propre($_POST['texte']);
	echo('<b>'._T('bartypenr:stats_statistiques').'</b> ');
	echo(nombre_de_mots($texte));
	echo(' '._T('bartypenr:stats_mots'));
	echo(strlen(supprimer_blancs(textebrut($texte))));
	echo(' '._T('bartypenr:stats_caracteres'));
	echo(strlen(textebrut($texte)));
	echo(' '._T('bartypenr:stats_signes'));
}

function supprimer_blancs($texte) {
	return trim(ereg_replace("[[:space:]]","",$texte));
}

# Compte le nombre de mots contenus dans un champ
# Nécessite supprimer_ponctuation et number_format_fr
# Auteur: François Schreuer <francois <at> schreuer.org>
# Licence GPL
function nombre_de_mots($texte) {
	$texte = supprimer_ponctuation($texte);
	// On met tout dans un tableau en séparant par espaces
	$tableau = explode(" ", $texte);
	// On compte le nombre d'éléments contenus dans le tableau
	// on formate le nombre obtenu et on renvoie
	return number_format_fr(count($tableau));
}

# Retirer toute ponctuation
# Utile pour nettoyer du texte avant de la passer dans un compteur
# Auteur: François Schreuer <francois <at> schreuer.org>
# Licence GPL
function supprimer_ponctuation($texte) {
	// On supprimes les balises html
	$texte = textebrut($texte);
	// On change les apostrophes en espaces normaux
	$texte = str_replace("'"," ",$texte);
	$texte = str_replace("'"," ",$texte);
	// On supprime les notes de bas de page
	$texte = supprimer_notes($texte);
	// On supprime tout le reste de la ponctuation
	$texte = str_replace('«','',$texte);
	$texte = str_replace('»','',$texte);
	$texte = str_replace('"','',$texte);
	$texte = str_replace('“','',$texte);
	$texte = str_replace('”','',$texte);
	$texte = str_replace('„','',$texte);
	$texte = str_replace('-','',$texte);
	$texte = str_replace('&mdash;','',$texte);
	$texte = ereg_replace('[[:punct:]]','',$texte);
	// On remplace les espaces multiples se suivant par un seul et on renvoie le tout
	return trim(ereg_replace('[[:space:]]+',' ',$texte));
}

# Petit filtre assez particulier: supprimer les appels de note dans
# un texte préalablement passé par "textebrut" ou "couper"
# ATTENTION: cette fonction doit être adaptée si vous avez
# personnalisé vos notes de bas de page
# Auteur: François Schreuer <francois <at> schreuer.org>
# Licence GPL
function supprimer_notes($texte) {
	return ereg_replace(' \[([0-9]+)\]', '', $texte);
}

# Version améliorée de number_format (format français + séparation avec des insécables)
# Auteur: François Schreuer <francois <at> schreuer.org>
# Licence GPL
function number_format_fr($nb,$dec=0) {
	return str_replace("-","&nbsp;",number_format($nb, $dec, ',', '-'));
}
?>