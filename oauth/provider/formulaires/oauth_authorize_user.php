<?php

    global $rs;
    global $server;
        

    function formulaires_oauth_authorize_user_charger() {
        $valeurs = array();

        $valeurs['oauth_token'] = _request('oauth_token');
        $valeurs['oauth_callback'] = _request('oauth_callback');
                    
        return $valeurs;
        
    }

    function formulaires_oauth_authorize_user_verifier() {
               
        global $rs;
        global $server;
         
        $erreurs = array();
        
        include_spip('inc/library/OAuthStore');
        include_spip('inc/library/OAuthServer');
    
        include_spip('inc/connecteur_sql');
        $store = connecteur_sql();    
    
        # Fetch the oauth store and the oauth server.
        #$store  = OAuthStore::instance();
        $server = new OAuthServer();

        try
        {
            # Check if there is a valid request token in the current request
            # Returns an array with the consumer key, consumer secret, token, token secret and token type.
            $rs = $server->authorizeVerify();
            //$erreurs['message_erreur'] = var_dump($rs,true);

        }
        catch (OAuthException $e)
        {
            # No token to be verified in the request, show a page where the user can enter the token to be verified
            # **your code here**
            $erreurs['message_erreur'] = 'La requete est erronnée, le token fournit est invalide' . $e->getMessage();
            $erreurs['token'] = "Le token a été mal transmis, nous vous invitons à le saisir manuellement";
        }
        
        return $erreurs;
    }
    
    

    function formulaires_oauth_authorize_user_traiter() {
        
        global $rs;
        global $server;
                
        $server->authorizeFinish(true,$GLOBALS['visiteur_session']['id_auteur']);
    }
?>
