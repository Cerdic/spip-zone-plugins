<?php 

function balise_DISQUS_ID($p){
    // take the name of the object's primary key to calculate its value
    $_id_objet = $p->boucles[$p->id_boucle]->primary;
    $id_objet = champ_sql($_id_objet, $p);
    $objet = $p->boucles[$p->id_boucle]->id_table;
    $p->code = "calculer_balise_DISQUS_ID('$objet', $id_objet)";
    return $p;
}
function calculer_balise_DISQUS_ID($objet, $id_objet){
    $objet = objet_type($objet);
    return "$objet_$id_objet";
}
?>
