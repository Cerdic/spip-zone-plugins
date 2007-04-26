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
cout_log("couleurs_installe()");

	$couleurs = array(
		array('noir', 'rouge', 'marron', 'vert', 'vert olive', 'bleu marine', 'violet', 'gris', 'argent', 'vert clair', 'bleu', 'fuchia', 'bleu clair', 
		'blanc', 'bleu azur', 'beige', 'brun', 'bleu violet', 'brun clair', 'rose clair', 'vert fonce', 'orange fonce', 'mauve fonce', 'bleu ciel', 'or', 
		'ivoire', 'orange', 'lavande', 'rose', 'prune', 'saumon', 'neige', 'turquoise', 'jaune paille', 'jaune'),
		array('black', 'red', 'maroon', 'green', 'olive', 'navy', 'purple', 'gray', 'silver', 'chartreuse', 'blue', 'fuchsia', 'aqua', 
		'white', 'azure', 'bisque', 'brown', 'blueviolet', 'chocolate', 'cornsilk', 'darkgreen', 'darkorange', 'darkorchid', 'deepskyblue', 'gold', 
		'ivory', 'orange', 'lavender', 'pink', 'plum', 'salmon', 'snow', 'turquoise', 'wheat', 'yellow') );

	foreach ($couleurs[0] as $c=>$val) $couleurs[2][$val] = $couleurs[1][$c];
	$aide = '<strong>'.join('</strong>, <strong>', array_merge($couleurs[0], $couleurs[1])).'</strong>';
	$couleurs[0] = join('|', $couleurs[0]);
	$couleurs[1] = join('|', $couleurs[1]);

	// sauvegarde en meta : aide
	ecrire_meta('tweaks_couleurs_racc', $aide);
	// sauvegarde en meta : couleurs
	ecrire_meta('tweaks_couleurs', serialize($couleurs));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par le tweak
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function couleurs_raccourcis() {
	return _T('cout:couleurs:aide', array('liste' => $GLOBALS['meta']['tweaks_couleurs_racc']));
}

// callbacks
function couleurs_texte_callback($matches) {
	global $tweak_couleurs;
	return "<span style=\"color:{$tweak_couleurs[2][$matches[1]]};\">";
}
function couleurs_fond_callback($matches) {
	global $tweak_couleurs;
	return "<span style=\"background-color:{$tweak_couleurs[2][$matches[2]]};\">";
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function couleurs_rempl($texte) {
	if (strpos($texte, '[')===false || strpos($texte, '/')===false) return $texte;
	// pour les callbacks
	global $tweak_couleurs;
	// lecture des metas
	$tweak_couleurs = unserialize($GLOBALS['meta']['tweaks_couleurs']);
	// voila, on remplace tous les raccourcis francais...
	$texte = preg_replace_callback(",\[($tweak_couleurs[0])\],", 'couleurs_texte_callback', $texte);
	$texte = preg_replace_callback(",\[(bg|fond)\s+($tweak_couleurs[0])\],", 'couleurs_fond_callback', $texte);
	// raccourcis anglais, plus facile...
	$texte = preg_replace(",\[($tweak_couleurs[1])\],", '<span style="color:$1;">', $texte);
	$texte = preg_replace(",\[(bg|fond)\s+($tweak_couleurs[1])\],", '<span style="background-color:$2;">', $texte);
	// et toutes les balises de fin...
	$texte = preg_replace(",\[/(couleur|$tweak_couleurs[0]|color|$tweak_couleurs[1])\],", '</span>', $texte);
	// menage
	unset($tweak_couleurs);
	return $texte;  
}

function couleurs_pre_typo($texte) {
	if (strpos($texte, '[')===false || strpos($texte, '/')===false) return $texte;
	if (!isset($GLOBALS['meta']['tweaks_couleurs']) || isset($GLOBALS['var_mode']))
		couleurs_installe();
	// appeler couleurs_rempl() une fois que certaines balises ont ete protegees
	return tweak_echappe_balises('', 'couleurs_rempl', $texte);
}

?>