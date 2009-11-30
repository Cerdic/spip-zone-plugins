<?php
function jeux_supprimer_tout_jeu($id_jeu){
    sql_delete('spip_jeux_resultats', "id_jeu=$id_jeu");
}

function jeux_supprimer_tout_auteur($id_auteur){
    sql_delete('spip_jeux_resultats', "id_auteur=$id_auteur");

}

function jeux_supprimer_tout_tout(){
    sql_delete('spip_jeux_resultats');
}
?>