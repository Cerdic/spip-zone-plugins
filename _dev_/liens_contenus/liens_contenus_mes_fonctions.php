<?php
function liens_contenus_generer_url($type_objet, $id_objet)
{
    include_ecrire('inc/urls');
    $f = 'generer_url_ecrire_'.$type_objet;
    return $f($id_objet);
}

function generer_url_ecrire_modele($id_objet)
{
    return find_in_path($id_objet.'.html');
}
?>