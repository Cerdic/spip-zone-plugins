<?php

// Outils CHATONS - 30 janvier 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article2166

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function cs_rempl_chatons($texte) {
	if (strpos($texte, ':')===false) return $texte;
	$chatons_rempl = unserialize($GLOBALS['meta']['cs_chatons']);
	return str_replace($chatons_rempl[0], $chatons_rempl[1], $texte);
}

function chatons_pre_typo($texte) {
	if (strpos($texte, ':')===false) return $texte;
	if (!isset($GLOBALS['meta']['cs_chatons']))
		chatons_installe();
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'cs_rempl_chatons', $texte);
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function chatons_installe() {
//cs_log('chatons_installe()');
	$liste = $chatons = array();
	$path = find_in_path('img/chatons');
	$dossier = opendir($path);
	$bt = defined('_DIR_PLUGIN_PORTE_PLUME');
	if($path) while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			$chatons[0][] = ':'.$reg[1];
			$liste[] = '<b>:'.$reg[1].'</b>';	
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$chatons[1][] = "<img class=\"no_image_filtrer\" alt=\"$reg[1]\" title=\"$reg[1]\" src=\"".url_absolue($path)."/$reg[1].$reg[2]\" $size/>";
			if($bt)
				$chatons[3]['chaton_'.$reg[1]] = chatons_creer_icone_barre("$reg[1].$reg[2]", $path.'/');
		}
	}
	ecrire_meta('cs_chatons_racc', join(', ', $liste));
	ecrire_meta('cs_chatons', serialize($chatons));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('couteauprive:un_outil:aide');
function chatons_raccourcis() {
	return _T('couteauprive:chatons:aide', array('liste' => $GLOBALS['meta']['cs_chatons_racc']));
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function chatons_BarreTypo($tr) {
	if (!isset($GLOBALS['meta']['cs_chatons']))	chatons_installe();
	// le tableau des chatons est present dans les metas
	$chatons = unserialize($GLOBALS['meta']['cs_chatons']);
	$max = count($chatons[0]);
	$res = '';
	for ($i=0; $i<$max; $i++)
		$res .= "<a href=\"javascript:barre_inserer('{$chatons[0][$i]}',@@champ@@)\">{$chatons[1][$i]}</a>";
	return $tr.'<tr><td><@@span@@>'._T('couteauprive:chatons:nom')."</span>&nbsp;$res</td></tr>";
}

// les 2 fonctions suivantes inserent les boutons pour le plugin Porte Plume, s'il est present (SPIP>=2.0)
function chatons_PP_pre_charger($flux) {
	// les chatons sont-il dispo ?
	if (!isset($GLOBALS['meta']['cs_chatons']))	chatons_installe();
	// le tableau des chatons est present dans les metas
	$chatons = unserialize($GLOBALS['meta']['cs_chatons']);
	$max = count($chatons[0]);
	$r = array();
	for ($i=0; $i<$max; $i++) {
		$c = &$chatons[0][$i];
		$id = 'chaton_'.str_replace(':','',$c);
		$r[] = array(
				"id"          => $id,
				"name"        => _T('couteau:chatons_inserer', array('chaton'=>$c)),
				"className"   => $id,
				"replaceWith" => $c,
				"display"     => true);
	}
	$r = array(
		"id"          => 'cs_chatons_drop',
		"name"        => _T('couteau:chatons_inserer_drop'),
		"className"   => 'cs_chatons_drop',
		"replaceWith" => '',
		"display"     => true,
		"dropMenu"	=> $r,
	);
	foreach(cs_pp_liste_barres('chatons') as $b)
		$flux[$b]->ajouterApres('grpCaracteres', $r);
	return $flux;
}
function chatons_PP_icones($flux){
	// les chatons sont-il dispo ?
	if (!isset($GLOBALS['meta']['cs_chatons']))	chatons_installe();
	// le tableau des chatons est present dans les metas
	$chatons = unserialize($GLOBALS['meta']['cs_chatons']);
	// icones utilisees. Attention : mettre les drop-boutons en premier !!
	$flux = array_merge($flux, array(
		'cs_chatons_drop' => chatons_creer_icone_barre('lol.png', find_in_path('img/chatons').'/')
	), $chatons[3]);
	return $flux;
}
// creation d'icone pour le plugin porte-plume
function chatons_creer_icone_barre($file, $dir) {
	static $icones_barre;
	if(!isset($icones_barre))
		$icones_barre = sous_repertoire(sous_repertoire(_DIR_VAR, 'couteau-suisse'), 'icones_barre');
	// au stade mes_options, cette constante n'est pas encore definie...
	if(!defined('_IMG_GD_MAX_PIXELS'))
		define('_IMG_GD_MAX_PIXELS', (isset($GLOBALS['meta']['max_taille_vignettes'])&&$GLOBALS['meta']['max_taille_vignettes']<5500000)?$GLOBALS['meta']['max_taille_vignettes']:0);
	// la config "Methode de fabrication des vignettes" doit etre renseignee pour 'image_reduire'
	$img = filtrer('image_reduire', $dir.$file, 19, 19);
	$img = filtrer('image_recadre', $img, 16, 16, 'topleft');
	$nom = basename($src = extraire_attribut($img, 'src'));
	@copy($src, $icones_barre.$nom);
	return $nom;
}
?>