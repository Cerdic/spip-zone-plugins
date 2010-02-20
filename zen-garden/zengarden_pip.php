<?php
function zengarden_affichage_final($texte){
    global $html;
    if ($html and lire_config('zengarden/switcher')){
        include_spip('inc/utils');
        $code = recuperer_fond('inc/switcher_zen');
        // On rajoute le code du selecteur de squelettes avant la balise </body>
		$texte=str_replace("</body>",$code."</body>",$texte);
    
    }
    return $texte;
}

function zengarden_insert_head($flux){
    if(lire_config('zengarden/switcher')){
        //$flux .= "<script src='".find_in_path('switcher_zen.js')."' type='text/javascript'></script>\n";
        $flux .= "<link type='text/css' href='".find_in_path('switcher_zen.css')."' rel='stylesheet' />";
    }
    return $flux;
}    
?>