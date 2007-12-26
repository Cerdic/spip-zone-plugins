<?php
/*
	+----------------------------+
	Date : mardi 28 janvier 2003
	Auteur :  "gpl"
	Serieuse refonte et integration en mars 2007 : Patrice Vanneufville
	+-------------------------------------------------------------------+
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// et calcule a l'avance les images trouvees dans le repertoire img/decoupe/
function decoupe_installe() {
//cs_log('decoupe_installe()');
	$images = array();
	$path = find_in_path('img/decoupe');
	$dossier = opendir($path);
	if($path) while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$images[$reg[1]] = "<img class=\"no_image_filtrer\" src=\"".url_absolue($path)."/$reg[1].$reg[2]\" $size";
		}
	}
	ecrire_meta('cs_decoupe', serialize($images));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('desc:un_outil:aide');
function decoupe_raccourcis() {
	$compat = defined('_decoupe_COMPATIBILITE')
		?_T('desc:decoupe:aide2', array('sep' => '<b>'._decoupe_COMPATIBILITE.'</b>')):'';
	return _T('desc:decoupe:aide', array('sep' => '<b>'._decoupe_SEPARATEUR.'</b>')).$compat;
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function decoupe_BarreTypo($tr) {
	return $tr.'<tr><td>'._T('desc:decoupe:nom').' (en projet)</td></tr>';
}

function decoupe_nettoyer_raccourcis($texte) {
	if (defined('_decoupe_COMPATIBILITE'))
		return str_replace(array(_decoupe_SEPARATEUR, _decoupe_COMPATIBILITE), '<p>&nbsp;</p>', $texte);
	return str_replace(_decoupe_SEPARATEUR, '<p>&nbsp;</p>', $texte);
}

?>