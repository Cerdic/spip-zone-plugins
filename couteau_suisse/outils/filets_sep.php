<?php
/*
*	+----------------------------------+
*	Nom de l'outil : Filets de Separation
*	Idee originale : FredoMkb
*	Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
*	+-------------------------------------+
*	Toutes les infos sur : http://www.spip-contrib.net/?article1564
*/

// Constantes surchargeables
//@define('_FILETS_SEP_BALISE_DEBUT', '<hr');
//@define('_FILETS_SEP_BALISE_FIN', '/>');
@define('_FILETS_SEP_BALISE_DEBUT', '<p');
@define('_FILETS_SEP_BALISE_FIN', '></p>');

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function filets_sep_installe() {
//cs_log('filets_sep_installe()');
	include_spip('inc/texte');
	$liste = $filets = array();
	$path = find_in_path('img/filets');
	$dossier = opendir($path);
	if($path) while ($image = readdir($dossier)) {
		if (preg_match(',^(([a-z0-9_-]+)\.(png|gif|jpg)),', $image, $reg)) { 
			$liste[] = '<b>__'.$reg[1].'__</b>';	
			$filets[0][] = '__'.$reg[1].'__';	
			list(,$haut) = @getimagesize("$path/$reg[1]");
			if ($haut) $haut="height:{$haut}px;";
			$f = url_absolue($path).'/'.$reg[1];
			$filets[1][] = code_echappement(_FILETS_SEP_BALISE_DEBUT." class=\"filet_sep filet_sep_image\" style=\"$haut background-image: url($f);\""._FILETS_SEP_BALISE_FIN);
		}
	}
	ecrire_meta('cs_filets_sep_racc', join(', ', $liste));
	ecrire_meta('cs_filets_sep', serialize($filets));
	ecrire_metas();
}

// liste des nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('couteauprive:un_outil:aide');
function filets_sep_raccourcis() {
	return _T('couteauprive:filets_sep:aide', array('liste' => $GLOBALS['meta']['cs_filets_sep_racc']));
}

// Fonction pour generer des filets de separation selon les balises presentes dans le texte fourni.
// Cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function filets_sep_rempl($texte) {
	if (strpos($texte, '__')===false) return $texte;

	// On memorise les modeles d'expression rationnelle a utiliser pour chercher les balises numeriques.
	$modele_nombre = "#(?:\s*[\n\r]\s*)__(\d+)__(?=\s*[\n\r]\s*)#iU";

	// On remplace les balises filets numeriques dans le texte par le code HTML correspondant
	// le resultat est protege pour eviter que la typo de SPIP y touche
	while (preg_match($modele_nombre, $texte))
		$texte = preg_replace_callback($modele_nombre, 
			create_function('$matches', 'return code_echappement("'._FILETS_SEP_BALISE_DEBUT.' class=\'filet_sep filet_sep_$matches[1]\''._FILETS_SEP_BALISE_FIN.'");'), $texte); 
	if (strpos($texte, '__')===false) return $texte;

	// On remplace les balises filets images dans le texte par le code HTML correspondant.
	// le resultat est protege pour eviter que la typo de SPIP y touche
	$filets_rempl = unserialize($GLOBALS['meta']['cs_filets_sep']);
	return str_replace($filets_rempl[0], $filets_rempl[1], $texte);
}

// fonction pipeline pre_typo
function filets_sep($texte) {
	if (strpos($texte, '__')===false) return $texte;
	return cs_echappe_balises('', 'filets_sep_rempl', $texte);
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function filets_sep_BarreTypo($tr) {
	if (!isset($GLOBALS['meta']['cs_filets_sep'])) filets_sep_installe();
	// le tableau des filets est present dans les metas
	$filets = unserialize($GLOBALS['meta']['cs_filets_sep']);
	$res = array();
	@define('_FILETS_SEP_MAX_CSS', 7);
	for ($i=0; $i<=_FILETS_SEP_MAX_CSS; $i++)
		$res[] = "<a title=\"__{$i}__\" href=\"javascript:barre_inserer('\\n\\n__{$i}__\\n\\n',@@champ@@)\"><span class=\"cs_BT\">CSS {$i}</span></a>";
	$max = count($filets[0]);
	for ($i=0; $i<$max; $i++)
		$res[] = "<a title=\"{$filets[0][$i]}\" href=\"javascript:barre_inserer('\\n\\n{$filets[0][$i]}\\n\\n',@@champ@@)\"><span class=\"cs_BT\">{$filets[0][$i]}</span></a>";
	$res = join(' ', $res);
	return $tr.'<tr><td><p style="margin:0; line-height:1.8em;">'._T('couteauprive:filets_sep:nom')."&nbsp;$res</p></td></tr>";
}

?>