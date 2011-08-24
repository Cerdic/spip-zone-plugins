<?php
    if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('inc/base');
	include_spip('inc/session');
    include_spip('inc/commandes');

    function action_commandes_paniers_dist(){

    	// On commence par chercher le panier du visiteur actuel s'il n'est pas donné
    	if (!$id_panier) $id_panier = session_get('id_panier');

        //Si aucun panier ne pas agir
        if (is_null($id_panier)) 
            return;        

        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
        
        $id_objet = creer_commande_encours();

        $supprimer_panier = charger_fonction('supprimer_panier_encours', 'action/');
        $supprimer_panier();

        if (is_null(_request('redirect'))) {   
            $redirect = generer_url_public('commande','id_commande='.$id_objet,true);
            include_spip('inc/headers');
            redirige_par_entete($redirect);
        }

    }
?>
