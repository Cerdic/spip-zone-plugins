<?php
/*
*	+----------------------------------+
*	Nom du Tweak : Filets de Separation
*	Idee originale : FredoMkb
*	Serieuse refonte : Patrice Vanneufville
*	+-------------------------------------+
*	Toutes les infos sur : http://www.spip-contrib.net/?article1564
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
function filets_sep_installe() {
//tweak_log('chatons_installe()');
	$path = dirname(find_in_path('img/filets/test'));
	$liste = $filets = array();
	$dossier = opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^(([a-z0-9_-]+)\.(png|gif|jpg)),', $image, $reg)) { 
			$liste[] = '<strong>__'.$reg[1].'__</strong>';	
			$filets[0][] = '__'.$reg[1].'__';	
			list(,$haut) = @getimagesize("$path/$reg[1]");
			if ($haut) $haut="height:{$haut}px;";
			$f = tweak_htmlpath($path).'/'.$reg[1];
			$filets[1][] = "<html><p class=\"spip filet_sep filet_sep_image\" style=\"$haut background-image: url($f);\">&nbsp; &nbsp; &nbsp;</p></html>";
		}
	}
	ecrire_meta('tweaks_filets_sep_racc', join(', ', $liste));
	ecrire_meta('tweaks_filets_sep', serialize($filets));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par le tweak
// si cette fonction n'existe pas, le plugin cherche alors  _T('tweak:mon_tweak:aide');
function filets_sep_raccourcis() {
	return _T('tweak:filets_sep:aide', array('liste' => $GLOBALS['meta']['tweaks_filets_sep_racc']));
}

// Fonction pour generer des filets de separation selon les balises presentes dans le texte fourni.
// Cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function filets_sep_rempl($texte) {
	if (strpos($texte, '__')===false) return $texte;
	
	// On memorise les modeles d'expression rationnelle a utiliser pour chercher les balises numeriques.
	$modele_nombre = "#([\n\r]\s*)__(\d+)__(\s*[\n\r])#iU";

	// On remplace les balises filets numeriques dans le texte par le code HTML correspondant.
	while (preg_match($modele_nombre, $texte))
		$texte = preg_replace($modele_nombre,'$1<html><p class="spip filet_sep filet_sep_$2">&nbsp; &nbsp; &nbsp;</p></html>$3',$texte); 
	if (strpos($texte, '__')===false) return $texte;

	// On remplace les balises filets images dans le texte par le code HTML correspondant.
	$filets_rempl = unserialize($GLOBALS['meta']['tweaks_filets_sep']);
	return str_replace($filets_rempl[0], $filets_rempl[1], $texte);
}

// fonction pipeline
function filets_sep($texte) {
	if (strpos($texte, '__')===false) return $texte;
	return tweak_echappe_balises('', 'filets_sep_rempl', $texte);
}
?>
