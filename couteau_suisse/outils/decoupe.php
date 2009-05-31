<?php
/*
	+----------------------------+
	Date : mardi 28 janvier 2003
	Auteur :  "gpl"
	Serieuse refonte et integration en mars 2007 : Patrice Vanneufville
	+-------------------------------------------------------------------+
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('couteauprive:un_outil:aide');
function decoupe_raccourcis() {
	$compat = defined('_decoupe_COMPATIBILITE')
		?_T('couteauprive:decoupe:aide2', array('sep' => '<b>'._decoupe_COMPATIBILITE.'</b>')):'';
	return _T('couteauprive:decoupe:aide', array('sep' => '<b>'._decoupe_SEPARATEUR.'</b>')).$compat;
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function decoupe_BarreTypo($tr) {
	return $tr.'<tr><td>'._T('couteauprive:decoupe:nom').' (en projet)</td></tr>';
}

function decoupe_nettoyer_raccourcis($texte) {
	if (defined('_decoupe_COMPATIBILITE'))
		return str_replace(array(_decoupe_SEPARATEUR, _decoupe_COMPATIBILITE), '<p>&nbsp;</p>', $texte);
	return str_replace(_decoupe_SEPARATEUR, '<p>&nbsp;</p>', $texte);
}

?>