<?php

    function formulaires_oauth_request_token_charger_dist() {
        include_spip('inc/library/OAuthServer');
        include_spip('inc/OAuthVerifier');    
        include_spip('inc/connecteur_sql');

        $valeurs = array();    

        $store = connecteur_sql();

        #Creer le token de réponse            
        $server = new OAuthServer(); 
        $valeurs = $server->requestToken();

        #var_dump($valeurs);

        return $valeurs;
    }

?>
