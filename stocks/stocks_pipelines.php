<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function stocks_formulaire_charger($flux) {

    $form = $flux['args']['form'];

    if ($form == "editer_produit")  {
        include_spip('inc/stocks');
        $id_produit = intval($flux['args']['args'][0]);
        $quantite = get_quantite("produit",$id_produit);

        // La quantité produit
        $flux['data']['_saisies'][] = array(
            'saisie' => 'input',
            'options' => array(
                'nom' => 'quantite_produit',
                'label' => '<:stocks:quantite_produit:>',
                'defaut' => $quantite
            )
        );
    }
    
    return $flux;
}

function stocks_formulaire_traiter($flux) {

    $form = $flux['args']['form'];

    if ($form == "editer_produit")  {
        include_spip('inc/stocks');
        $id_produit = $flux['data']['id_produit'];
        $quantite = intval(_request('quantite_produit'));

        set_quantite("produit",$id_produit,$quantite);
    }
    
    return $flux;

}


?>
