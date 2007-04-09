<?php

/*
   Ce tweak en couleurs permet aux redacteurs d'un site spip de d'appliquer facilement des couleurs aux textes SPIP
   Utilisation pour le redacteur : 
		[rouge]Lorem ipsum dolor sit amet[/rouge]
		[red]Lorem ipsum dolor sit amet[/red]
   Les balises anglaises sont les couleurs utilisees dans les feuilles de style.
   Attention : seules les balises en minuscules sont reconnues.
*/
/*
 *   +----------------------------------+
 *    Nom du Tweak : Couleurs dans vos textes
 *   +----------------------------------+
 *    Date : Vendredi 11 août 2003
 *    Idee originale :  Aurelien PIERARD : aurelien.pierard(a)dsaf.pm.gouv.fr
 *    Serieuse refonte et integration en tweak : Patrice Vanneufville, mars 2007
 *   +-------------------------------------+
 *  
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
function couleurs_installe() {
tweak_log("couleurs_installe()");

	$couleurs = array(
		array('black', 'red', 'maroon', 'green', 'olive', 'navy', 'purple', 'gray', 'silver', 'chartreuse', 'blue', 'fuchsia', 'aqua', 'white', 'azure', 'bisque', 'brown', 'blueviolet', 'chocolate', 'cornsilk', 'darkgreen', 'darkorange', 'darkorchid', 'deepskyblue', 'gold', 'ivory', 'orange', 'lavender', 'pink', 'plum', 'salmon', 'snow', 'turquoise', 'wheat', 'yellow'),
		array('noir', 'rouge', 'marron', 'vert', 'vert olive', 'bleu marine', 'violet', 'gris', 'argent', 'vert clair', 'bleu', 'fuchia', 'bleu clair', 'blanc', 'bleu azur', 'beige', 'brun', 'bleu violet', 'brun clair', 'rose clair', 'vert fonce', 'orange fonce', 'mauve fonce', 'bleu ciel', 'or', 'ivoire', 'orange', 'lavande', 'rose', 'prune', 'saumon', 'neige', 'turquoise', 'jaune paille', 'jaune') );

//foreach ($couleurs[0] as $c=>$val) echo "<span style=\"background-color:$val;\">$val/{$couleurs[1][$c]}</span>, ";

	// liste d'aide
	$liste = array_merge($couleurs[1], $couleurs[0]);
	foreach ($liste as $c=>$val) { $liste[$c] = "<strong>$val</strong>"; }
	// raccourcis francais
	foreach ($couleurs[1] as $c=>$val) { $couleurs[1][$c] = "[$val]"; $couleurs[2][$c] = "[/$val]"; }
	// raccourcis anglais
	foreach ($couleurs[0] as $val) { $couleurs[1][] = "[$val]"; $couleurs[2][] = "[/$val]"; $couleurs[0][] = $val; }
	// html a substituer aux racourcis
	foreach ($couleurs[0] as $c=>$val) $couleurs[0][$c] = "<span style=\"color:$val;\">";
	// sauvegarde en meta	
	ecrire_meta('tweaks_couleurs_racc', join(', ', $liste));
	ecrire_meta('tweaks_couleurs', serialize($couleurs));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par le tweak
// si cette fonction n'existe pas, le plugin cherche alors  _T('tweak:mon_tweak:aide');
function couleurs_raccourcis() {
	return _T('tweak:couleurs:aide', array('liste' => $GLOBALS['meta']['tweaks_couleurs_racc']));
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function couleurs_rempl($texte) {
	if (strpos($texte, '[')===false || strpos($texte, '/')===false) return $texte;
	$couleurs = unserialize($GLOBALS['meta']['tweaks_couleurs']);
	// voila, on remplace tous les raccouris d'un coup...
	$texte = str_replace($couleurs[1], $couleurs[0], $texte);
	$texte = str_replace($couleurs[2], "</span>", $texte);
	return $texte;  
}

function couleurs_pre_typo($texte) {
	if (strpos($texte, '[')===false || strpos($texte, '/')===false) return $texte;
	if (!isset($GLOBALS['meta']['tweaks_couleurs']) || isset($GLOBALS['var_mode']))
		couleurs_installe();
	// appeler couleurs_rempl() une fois que certaines balises ont ete protegees
	return tweak_exclure_balises('', 'couleurs_rempl', $texte);
}

?>