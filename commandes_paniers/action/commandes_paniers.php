<?php
    if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('inc/base');
	include_spip('inc/session');

    function action_commandes_paniers_dist(){

    	// On commence par chercher le panier du visiteur actuel s'il n'est pas donné
    	if (!$id_panier) $id_panier = session_get('id_panier');

        //Si aucun panier ne pas agir
        if (is_null($id_panier)) 
            return;        

        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();

        $champs = array(
            'statut' => 'encours'
        );

        $id_objet = sql_insertq(
            'spip_commandes',
            $champs
        );

        pipeline('post_insertion',
            array(
                'args' => array(
                    'table' => 'spip_commandes',
                    'id_objet' => $id_objet
                ),
                'data' => $champs
            )
        );

        $supprimer_panier = charger_fonction('supprimer_panier_encours', 'action/');
        $supprimer_panier();

        include_spip('inc/headers');
        redirige_par_entete(generer_url_public('commande','id_commande='.$id_objet,true));

    }
?>
