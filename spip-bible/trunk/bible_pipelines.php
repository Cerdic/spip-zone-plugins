<?php
function bible_insert_head($flux){

	return $flux.'<link rel="stylesheet" href="'.timestamp(produire_fond_statique('bible.css')).'" type="text/css" media="all" />';


}

function bible_affiche_droite($flux){

    
    /* on n'affiche le presse-papier bible que si on est sur une page d'édition*/
    if (preg_match('/edit/',$flux['args']['exec'])==false){
            return $flux;
    }

    $type = str_replace('s_edit','',$flux['args']['exec']);
    $id   = 'id_'.$type;

    $fond = recuperer_fond('fonds/bible_presse_papier', array($id=>$flux['args'][$id],'id_syndic'=>$flux['args']['id_syndic']));
    $flux['data'] .= $fond;
    return $flux;
}

?>
