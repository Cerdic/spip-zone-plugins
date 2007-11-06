<?php

// menu_lang plat sans URL sur la langue en cours
// Voir http://www.spip-contrib.net/Formulaire-menu-lang-plat-sans-URL et FORUM
function traductions_page_lang_plat ($langues) {
    include_spip('inc/charsets');
    $list_lang = '';
    $tab_langues = explode(",",$langues);
    while ( list($clef, $valeur) = each($tab_langues) ) 
	if ($valeur == $GLOBALS['spip_lang']) { 
	$list_lang .='<li lang="'.$valeur.'" xml:lang="'.$valeur.'" dir="ltr" class="on"><span>['.traduire_nom_langue($valeur).']</span></li> '; 
}	else { 
        $list_lang .='<li lang="'.$valeur.'" xml:lang="'.$valeur.'" dir="ltr" class="off"><a href="'.parametre_url(self(true), 'lang', ''.$valeur.'').'"><span>['.traduire_nom_langue($valeur).']</span></a></li> '; 
	}
    return $list_lang;
}
//fin
?>