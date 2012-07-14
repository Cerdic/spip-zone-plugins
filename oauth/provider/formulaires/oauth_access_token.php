<?php

    function formulaires_oauth_access_token_charger_dist() {
        include_spip('inc/library/OAuthServer');
        include_spip('inc/OAuthVerifier');    
        include_spip('inc/connecteur_sql');

        $valeurs = array();    

        $store = connecteur_sql();

        #Creer le token de réponse            
        $server = new OAuthServer(); 
        $valeurs = $server->accessToken();

        #var_dump($valeurs);

        return $valeurs;
    }

?>
