<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction pour supprimer directement un objet du panier peut importe la quantité
 *
 * @access public
 */
function action_supprimer_element_panier_dist($arg=null) {
    if (is_null($arg)){
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }

    // On récupère l'objet à supprimer du panier
    @list($objet, $id_objet) = explode('-', $arg);

    // Il faut cherche le panier du visiteur en cours
    include_spip('inc/paniers');
    $id_panier_base = 0;
    if ($id_panier = paniers_id_panier_encours()){
        //est-ce que le panier est bien en base
        $id_panier_base = intval(sql_getfetsel(
            'id_panier',
            'spip_paniers',
            array(
                'id_panier = '.intval($id_panier),
                'statut = '.sql_quote('encours')
            )
        ));
    }

    // S'il n'y a pas de panier, on ne fait rien
    if (!$id_panier OR !$id_panier_base) {
        return false;
    }

    // On supprime l'objet du panier
    sql_delete(
        'spip_paniers_liens',
        array(
            'id_panier = '.intval($id_panier),
            'objet = '.sql_quote($objet),
            'id_objet = '.intval($id_objet)
        )
    );

    include_spip('inc/invalideur');
    suivre_invalideur("id='$objet/$id_objet'");
}
