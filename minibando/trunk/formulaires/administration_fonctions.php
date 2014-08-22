<?php

include_spip('inc/bandeau');

function minibando_definir_rubrique_contexte($contexte) {
    if( !isset($contexte['id_rubrique']) && isset($contexte['id_article']) && $contexte['id_article'] ){
        $contexte['id_rubrique'] = sql_getfetsel('id_rubrique','spip_articles','id_article='.(int)$contexte['id_article']);
    }
    return $contexte;
}

?>