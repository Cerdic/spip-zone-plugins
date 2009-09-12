<?php

/*
   Cet outil 'blocs' permet aux redacteurs d'un site spip d'inclure des blocs visibles ou invisibles dans leurs textes
   balises : <bloc></bloc> ou <invisible></invisible>, et <visible></visible>
   le titre est obtenu en sautant deux lignes a l'interieur du bloc
   Attention : seules les balises en minuscules sont reconnues.
*/

@define('_BLOC_TITRE_H', 'h4');

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('couteauprive:un_outil:aide');
function blocs_raccourcis() {
	return _T('couteauprive:blocs:aide');
}

function blocs_callback($matches) {
	list($titre, $corps) = preg_split(',(\n\n|\r\n\r\n|\r\r),', trim($matches[3]), 2);
	// pas de corps !
	if(!strlen($corps=trim($corps))) {
		$corps = $titre;
		$titre = preg_replace(',[\n\r]+,s', ' ', couper(propre($titre), 30));
	}
	// pas d'intertitre !
	$titre = preg_replace(',^{{{(.*)}}}$,', '$1', trim($titre));
	if(!strlen($titre)) $titre = '???';
	// un resume facultatif
	if(preg_match(',<resume>(.*)</resume>\s?(.*)$,ms', $corps, $res))
		{ $corps = $res[2]; $res = $res[1]; } else $res = '';
	// types de blocs : bloc|invisible|visible
	if ($matches[1]=='visible' || defined('_CS_PRINT')) {
		$h = $d = '';
		$r = ' blocs_invisible blocs_slide ';
	} else {
		$h = ' blocs_replie';
		$d = ' blocs_invisible blocs_slide';
		$r = '';
	}

	// blocs numerotes
	$b = strlen($matches[2])?" cs_bloc$matches[2]":''; 
	return "<div class='cs_blocs$b'><"._BLOC_TITRE_H." class='blocs_titre$h blocs_click'><a href='javascript:;'>$titre</a></"._BLOC_TITRE_H.">"
		.(strlen($res)?"<div class='blocs_resume$r'>\n$res\n</div>":"")
		."<div class='blocs_destination$d'>\n".blocs_rempl($corps)."\n</div></div>";
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function blocs_rempl($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// balises blocs|visible|invisible : il faut un callback pour analyser l'interieur du texte
	return preg_replace_callback(',<(bloc#?|visible#?|invisible#?|blocintertitre#?)([0-9]*)>(.*?)</\1\2>,ms', 'blocs_callback', $texte);
}

// fonction pipeline
function blocs_pre_typo($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// on remplace apres echappement
	return cs_echappe_balises('', 'blocs_rempl', $texte);
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function blocs_BarreTypo($tr) {
	return $tr.'<tr><td>'._T('couteauprive:blocs:nom').' (en projet)</td></tr>';
}

?>