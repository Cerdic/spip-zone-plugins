<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

function plugins_preparer_sql_plugin($plugin)
{
	$champs = array();
	if (!$plugin)
		return $champs;

	// On initialise les champs ne necessitant aucune transformation
	$champs['categorie'] = $plugin['categorie'] ? $plugin['categorie'] : '';
	$champs['etat'] = $plugin['etat'] ? $plugin['etat'] : '';
	$champs['version'] = $plugin['version'] ? $plugin['version'] : '';
	$champs['version_base'] = $plugin['version_base'] ? $plugin['version_base'] : '';
	$champs['lien'] = $plugin['lien'] ? $plugin['lien'] : '';

	// Renommage de certains champs
	$champs['logo'] = $plugin['icon'] ? $plugin['icon'] : '';
	// On passe le prefixe en lettres majuscules comme ce qui est fait dans SPIP
	// Ainsi les valeurs dans la table spip_plugins coincideront avec celles de la meta plugin
	$champs['prefixe'] = strtoupper($plugin['prefix']);

	// Indicateurs d'etat numerique (pour simplifier la recherche des maj de STP)
	static $num = array('stable'=>4, 'test'=>3, 'dev'=>2, 'experimental'=>1);
	$champs['etatnum'] = isset($num[$plugin['etat']]) ? $num[$plugin['etat']] : 0;

	// Tags : liste de mots-cles
	$champs['tags'] = ($plugin['tags']) ? serialize($plugin['tags']) : '';
	
	// On passe en utf-8 avec le bon charset les champs pouvant contenir des entites html
	$champs['description'] = unicode2charset(html2unicode($plugin['description']));
	$champs['auteur'] = unicode2charset(html2unicode($plugin['auteur']));
	$champs['licence'] = unicode2charset(html2unicode($plugin['licence']));
	
	// Extrait d'un nom et un slogan normalises
	$plugin['slogan'] = unicode2charset(html2unicode($plugin['slogan']));
	$plugin['nom'] = unicode2charset(html2unicode($plugin['nom']));
	// Calcul *temporaire* de la nouvelles balise slogan si celle-ci n'est
	// pas renseignee et de la balise nom. Ceci devrait etre temporaire jusqu'a la nouvelle ere
	// glaciaire des plugins
	// - Slogan	:	si vide alors on prend la premiere phrase de la description limitee a 255
	$champs['slogan'] = (!$plugin['slogan']) ? normaliser_slogan($champs['description']) : $plugin['slogan'];
	// - Nom :	on repere dans le nom du plugin un chiffre en fin de nom
	//			et on l'ampute de ce numero pour le normaliser
	//			et on passe tout en unicode avec le charset du site
	$champs['nom'] = normaliser_nom($plugin['nom']);

	// Extraction de la compatibilite SPIP
	$champs['compatibilite_spip'] = ($plugin['compatible']) ? $plugin['compatible'] : '';
	
	// Construction du tableau des dependances necessite, lib et utilise
	$dependances['necessite'] = $plugin['necessite'];
	$dependances['librairie'] = $plugin['lib'];
	$dependances['utilise'] = $plugin['utilise'];
	$champs['dependances'] = serialize($dependances);

	return $champs;
}


function normaliser_slogan($description) {
	include_spip('inc/texte');

	// On extrait les traductions de l'eventuel multi
	// Si le nom n'est pas un multi alors le tableau renvoye est de la forme '' => 'nom'
	$descriptions = extraire_trads(str_replace(array('<multi>', '</multi>'), array(), $description, $nbr_replace));
	$multi = ($nbr_replace > 0) ? true : false;

	// On boucle sur chaque multi ou sur la chaine elle-meme en extrayant le slogan
	// dans les differentes langues
	$slogan = '';
	foreach ($descriptions as $_lang => $_descr) {
		$_descr = trim($_descr);
		if (!$_lang)
			$_lang = 'fr';
		$nbr_matches = preg_match(',^(.+)[.!?\r\n\f],Um', $_descr, $matches);
		$slogan .= (($multi) ? '[' . $_lang . ']' : '') . 
					(($nbr_matches > 0) ? trim($matches[1]) : couper($_descr, 150, ''));
	}

	if ($slogan)
		// On renvoie un nouveau slogan multi ou pas
		$slogan = (($multi) ? '<multi>' : '') . $slogan . (($multi) ? '</multi>' : '');

	return $slogan;
}


function normaliser_nom($nom) {
	include_spip('inc/texte');

	// On extrait les traductions de l'eventuel multi
	// Si le nom n'est pas un multi alors le tableau renvoye est de la forme '' => 'nom'
	$noms = extraire_trads(str_replace(array('<multi>', '</multi>'), array(), $nom, $nbr_replace));
	$multi = ($nbr_replace > 0) ? true : false;
	
	$nouveau_nom = '';
	foreach ($noms as $_lang => $_nom) {
		$_nom = trim($_nom);
		if (!$_lang)
			$_lang = 'fr';
		$nbr_matches = preg_match(',(.+)(\s+[\d._]*)$,Um', $_nom, $matches);
		$nouveau_nom .= (($multi) ? '[' . $_lang . ']' : '') . 
						(($nbr_matches > 0) ? trim($matches[1]) : $_nom);
	}
	
	if ($nouveau_nom)
		// On renvoie un nouveau nom multi ou pas sans la valeur de la branche 
		$nouveau_nom = (($multi) ? '<multi>' : '') . $nouveau_nom . (($multi) ? '</multi>' : '');
		
	return $nouveau_nom;
}

?>
