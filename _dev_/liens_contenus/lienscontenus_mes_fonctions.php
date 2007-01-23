<?php

// Les filtres pour les squeletes
function lienscontenus_generer_url($type_objet, $id_objet)
{
    include_ecrire('inc/urls');
    $f = 'generer_url_ecrire_'.$type_objet;
    return $f($id_objet);
}

function generer_url_ecrire_modele($id_objet)
{
    return find_in_path($id_objet.'.html');
}

function lienscontenus_verifier_si_existe($type_objet, $id_objet)
{
    if ($type_objet == 'modele') {
    	if(find_in_path($id_objet.'.html')) {
            return 'ok';
        } else {
            return 'ko';
        }
    } else {
        include_spip('base/abstract_sql');
        if ($type_objet == 'site') {
            $query = 'SELECT COUNT(*) AS nb FROM spip_syndic WHERE id_syndic='._q($id_objet);
        } else {
            $query = 'SELECT COUNT(*) AS nb FROM spip_'.$type_objet.'s WHERE id_'.$type_objet.'='._q($id_objet);
        }
        $res = spip_query($query);
        $row = spip_fetch_array($res);
        if ($row['nb'] == 1) {
            return 'ok';
        } else {
            return 'ko';
        }
    }
}
?>