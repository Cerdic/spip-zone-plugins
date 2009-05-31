<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_addclaves_dist(){
    include_spip('inc/utils');
    include_spip('inc/headers');

    $id_article = $_POST['id_article'];

    //agrego cada palabra seleccionada
    foreach($_POST['palabras'] as $value){
        $query = spip_query("INSERT IGNORE INTO spip_mots_articles (`id_mot`, `id_article`) VALUES ($value, $id_article)");

    }    

    spip_header("Location: ./?exec=articles&id_article=".$id_article);
    
}


?>
