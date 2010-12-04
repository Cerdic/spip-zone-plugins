<?php

include_spip('inc/bandeau');

function minibando_outils_rapides($boutons, $contexte = array()){
    $res = "";

	// la barre de raccourcis rapides
	if (isset($boutons['outils_rapides']))
			$res .= "<ul><li><p>"._T('minibando:titre_outils_rapides')."</p></li>"
				. bando_lister_sous_menu($boutons['outils_rapides']->sousmenu,$contexte,'bouton',true)
				. "</ul>";

	return "<li id='minibando_outils_rapides'>\n<a href='#'>"._T('minibando:titre_outils_rapides')."</a><span></span>$res</li>";
}

function minibando($boutons, $contexte = array()) {
	$res = "";

	$first = " class = 'first'";
	foreach($boutons as $page => $detail){
        // les outils rapides sont traites a part, dans une barre dediee
        if (!in_array($page,array('outils_rapides','outils_collaboratifs'))){

            // les icones de premier niveau sont ignoree si leur sous menu est vide
            // et si elles pointent vers exec=navigation
            if (
             ($detail->libelle AND is_array($detail->sousmenu) AND count($detail->sousmenu))
             OR ($detail->libelle AND $detail->url AND $detail->url!='navigation')) {
                $url = bandeau_creer_url($detail->url?$detail->url:$page, $detail->urlArg,$contexte);
                $bulle_accueil = ($detail->url == 'accueil') ? "<ul><li><p>" . _T('public:espace_prive') . "</p></li></ul>" : '';
                $res .= "<li$first>"
                 . "<a href='$url' id='bando1_$page'>"
                 . _T($detail->libelle)
                 . "</a>";
               	$res .= "<span></span>";
               	$res .= $bulle_accueil;
                if($first) {
                	$res .= "<li class='minibando_sep'>&nbsp;</li>";
                	$res .= minibando_outils_rapides($boutons, $contexte);
                	$res .= "<li class='minibando_sep'>&nbsp;</li>";
				}
            }

            $sous = bando_lister_sous_menu($detail->sousmenu, $contexte);
            $res .= $sous ? "<ul><li><p>"._T($detail->libelle)."</p></li>$sous</ul>":"";

            $res .= "</li>";
            $first = "";
        }
	}

	return "\n$res";
}

?>