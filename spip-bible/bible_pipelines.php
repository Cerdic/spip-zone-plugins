<?php
function bible_insert_head($flux){

	return $flux.'<link rel="stylesheet" href="'.generer_url_public('bible.css').'" type="text/css" media="all" />';


}

function bible_affiche_droite($flux){
    global $spip_version_affichee;
    
    /* on n'affiche le presse-papier bible que si on est sur une page d'édition*/
    if (preg_match('/edit/',$flux['args']['exec'])==false or preg_match('/^1/',$spip_version_affichee) == true){
            return $flux;
    }

    $type = str_replace('s_edit','',$flux['args']['exec']);
    $id   = 'id_'.$type;

    $fond = recuperer_fond('fonds/bible_presse_papier', array($id=>$flux['args'][$id],'id_syndic'=>$flux['args']['id_syndic']));
    $flux['data'] .= $fond;
    return $flux;
}

?>
