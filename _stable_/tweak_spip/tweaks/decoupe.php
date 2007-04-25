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

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
// et calcule a l'avance les images trouvees dans le repertoire img/decoupe/
function decoupe_installe() {
cout_log('decoupe_installe()');
	$path = dirname(find_in_path('img/decoupe/test'));
	$images = array();
	$dossier=opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$images[$reg[1]] = "<img class=\"no_image_filtrer\" src=\"".tweak_htmlpath($path)."/$reg[1].$reg[2]\" $size";
		}
	}
	ecrire_meta('tweaks_decoupe', serialize($images));
	ecrire_metas();
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par le tweak
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function decoupe_raccourcis() {
	return _T('cout:decoupe:aide', array('sep' => '<strong>'._decoupe_SEPARATEUR.'</strong>'));
}
?>