<?php
/*
 *   +----------------------------------+
 *    Nom du Filtre : decouper_en_pages
 *   +----------------------------------+
 *    Date : mardi 28 janvier 2003
 *    Auteur :  "gpl"
 *    Serieuse refonte et integration en mars 2007 : Patrice Vanneufville
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     Presenter un article sur plusieurs pages
 *   +-------------------------------------+ 
 *
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// et calcule a l'avance les images trouvees dans le repertoire img/decoupe/
function decoupe_installe() {
//cs_log('decoupe_installe()');
	$path = dirname(find_in_path('img/decoupe/test'));
	$images = array();
	$dossier=opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$images[$reg[1]] = "<img class=\"no_image_filtrer\" src=\"".cs_htmlpath($path)."/$reg[1].$reg[2]\" $size";
		}
	}
	ecrire_meta('cs_decoupe', serialize($images));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function decoupe_raccourcis() {
	$compat = defined('_decoupe_COMPATIBILITE')
		?_T('cout:decoupe:aide2', array('sep' => '<b>'._decoupe_COMPATIBILITE.'</b>')):'';
	return _T('cout:decoupe:aide', array('sep' => '<b>'._decoupe_SEPARATEUR.'</b>')).$compat;
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function decoupe_BarreTypoEnrichie($tr) {
	return $tr.'<tr><td>'._T('cout:decoupe:nom').' (en projet)</td></tr>';
}

?>