<?php

/*
   Cet outil 'blocs' permet aux redacteurs d'un site spip d'inclure des blocs visibles ou invisibles dans leurs textes
   balises : <bloc></bloc> ou <invisible></invisible>, et <visible></visible>
   le titre est obtenu en sautant deux lignes a l'interieur du bloc
   Attention : seules les balises en minuscules sont reconnues.
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveaux raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('desc:un_outil:aide');
function blocs_raccourcis() {
	return _T('desc:blocs:aide');
}

function blocs_callback($matches) {
	$t = preg_split(',(\n\n|\r\n\r\n|\r\r),', $matches[3], 2);
	if ($matches[1]=='visible')
		$h = $div = '';
	else {
		$h = ' blocs_replie';
		$div = ' class="blocs_invisible"';
	}
	$b = strlen($matches[2])?" cs_bloc$matches[2]":''; 
	return "<div class=\"cs_blocs$b\"><h4 class=\"blocs_titre$h\"><a href=\"#\">$t[0]</a></h4><div$div>$t[1]</div></div>";
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function blocs_rempl($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// balises blocs|visible|invisible : il faut un callback pour analyser l'interieur du texte
	return preg_replace_callback(',<(bloc|visible|invisible)([0-9]*)>(.*?)</\1\2>,ms', 'blocs_callback', $texte);
}

// fonction pipeline
function blocs_pre_typo($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// on remplace apres echappement
	return cs_echappe_balises('', 'blocs_rempl', $texte);
}

// cette fonction renvoie une ligne de tableau entre <tr></tr> afin de l'inserer dans la Barre Typo V2, si elle est presente
function blocs_BarreTypo($tr) {
	return $tr.'<tr><td>'._T('desc:blocs:nom').' (en projet)</td></tr>';
}

?>