<?php
function bible_insert_head($flux){

	return $flux.'<link rel="stylesheet" href="'.generer_url_public('bible.css').'" type="text/css" media="all" />';


}

function bible_affiche_droite($flux){
    global $spip_version_affichee;
    
    /* on n'affiche le presse-papier bible que si on est sur une page d'édition*/
    if (ereg('edit',$flux['args']['exec'])==false or ereg('^1',$spip_version_affichee) == true){
            return $flux;
    }
    
    $fond = recuperer_fond('fonds/bible_presse_papier');
    $flux['data'] .= $fond;
    return $flux;
}

?>
