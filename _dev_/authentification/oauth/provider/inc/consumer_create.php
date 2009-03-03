<?php

    include('library/OAuthRequestVerifier.php');
    include('library/OAuthSotre.php');
    #include_spip('inc/OAuthVerifier');

    echo "topt";

#    function exec_oauth_register() {

        #tester si on a bien une reçu une requete OAuth
        #OauthVerifier();

        #Pamétres de connexion à la base de données
        #$options['conn'] = ;
        $options['server'] = '127.0.0.1';
        $options['username'] = 'developpement';                
        $options['password'] = '2d2l1m1t';        
        $options['database'] = 'developpement_lha'; 


        // The currently logged on user
        $user_id = 1;

        // This should come from a form filled in by the requesting user
        $consumer = array(
            // These two are required
            'requester_name' => 'John Doe',
            'requester_email' => 'john@example.com',

            // These are all optional
            'callback_uri' => 'http://www.myconsumersite.com/oauth_callback',
            'application_uri' => 'http://www.myconsumersite.com/',
            'application_title' => 'John Doe\'s consumer site',
            'application_descr' => 'Make nice graphs of all your data',
            'application_notes' => 'Bladibla',
            'application_type' => 'website',
            'application_commercial' => 0
        );

        // Register the consumer
        $store = OAuthStore::instance('MySQL', $options);
        $key  = $store->updateConsumer($consumer, $user_id);

        // Get the complete consumer from the store
        $consumer = $store->getConsumer($key);

        // Some interesting fields, the user will need the key and secret
        $consumer_id = $consumer['osr_id'];
        $consumer_key = $consumer['osr_consumer_key'];
        $consumer_secret = $consumer['osr_consumer_secret'];
        
        echo $consumer_id ." ". $consumer_key ." ".$consumer_secret
        
#    }
?>
