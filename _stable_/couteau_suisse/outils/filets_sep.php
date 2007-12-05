<?php
/*
*	+----------------------------------+
*	Nom de l'outil : Filets de Separation
*	Idee originale : FredoMkb
*	Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
*	+-------------------------------------+
*	Toutes les infos sur : http://www.spip-contrib.net/?article1564
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function filets_sep_installe() {
//cs_log('filets_sep_installe()');
	$path = dirname(find_in_path('img/filets/test'));
	$liste = $filets = array();
	$dossier = opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^(([a-z0-9_-]+)\.(png|gif|jpg)),', $image, $reg)) { 
			$liste[] = '<b>__'.$reg[1].'__</b>';	
			$filets[0][] = '__'.$reg[1].'__';	
			list(,$haut) = @getimagesize("$path/$reg[1]");
			if ($haut) $haut="height:{$haut}px;";
			$f = cs_htmlpath($path).'/'.$reg[1];
			$filets[1][] = "<html><p class=\"spip filet_sep filet_sep_image\" style=\"$haut background-image: url($f);\">&nbsp; &nbsp; &nbsp;</p></html>";
		}
	}
	ecrire_meta('cs_filets_sep_racc', join(', ', $liste));
	ecrire_meta('cs_filets_sep', serialize($filets));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function filets_sep_raccourcis() {
	return _T('cout:filets_sep:aide', array('liste' => $GLOBALS['meta']['cs_filets_sep_racc']));
}

// Fonction pour generer des filets de separation selon les balises presentes dans le texte fourni.
// Cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function filets_sep_rempl($texte) {
	if (strpos($texte, '__')===false) return $texte;
	
	// On memorise les modeles d'expression rationnelle a utiliser pour chercher les balises numeriques.
	$modele_nombre = "#([\n\r]\s*)__(\d+)__(\s*[\n\r])#iU";

	// On remplace les balises filets numeriques dans le texte par le code HTML correspondant.
	while (preg_match($modele_nombre, $texte))
		$texte = preg_replace($modele_nombre,'$1<p class="spip filet_sep filet_sep_$2">&nbsp; &nbsp; &nbsp;</p>$3',$texte); 
	if (strpos($texte, '__')===false) return $texte;

	// On remplace les balises filets images dans le texte par le code HTML correspondant.
	$filets_rempl = unserialize($GLOBALS['meta']['cs_filets_sep']);
	return str_replace($filets_rempl[0], $filets_rempl[1], $texte);
}

// fonction pipeline
function filets_sep($texte) {
	if (strpos($texte, '__')===false) return $texte;
	return cs_echappe_balises('', 'filets_sep_rempl', $texte);
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function filets_sep_BT($tr) {
	return $tr.'<tr><td>'._T('cout:filets_sep:nom').' (en projet)</td></tr>';
}

?>
