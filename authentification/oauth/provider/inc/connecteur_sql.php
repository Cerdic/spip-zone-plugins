<?php

    function connecteur_sql($serveur = 'MySQL') {

        if (function_exists(include_spip))
            include_spip('inc/library/OAuthStore');

        #Pamètres de connexion à la base de données
        #$options['conn'] = ;
        $options['server'] = '127.0.0.1';
        $options['username'] = 'developpement';                
        $options['password'] = '2d2l1m1t';        
        $options['database'] = 'developpement_lha'; 
        
        #Créer une instance à la base de donnée        
        $store = OAuthStore::instance($serveur, $options);               
        
        return $store;    
    }
?>
