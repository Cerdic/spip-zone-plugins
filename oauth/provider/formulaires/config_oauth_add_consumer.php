<?php
    
    #include_spip('inc/ab')
    
    function formulaires_config_oauth_add_consumer_charger($id_consumer = NULL, $user_id = NULL) {
    
        $valeurs = array();
        
        if (!empty($id_consumer)) {
            $select = array(
                'osr_id AS user_id',
                'osr_requester_name',
                'osr_requester_email',
                'osr_callback_uri',
                'osr_application_uri',
                'osr_application_title',
                'osr_application_descr',
                'osr_application_notes',
                'osr_application_type',
                'osr_application_commercial'
            );
            $from = 'spip_oauth_server_registry';
            $where = 'osr_id='.$id_consumer;
            
            $valeurs = sql_fetsel(
                $select, 
                $from, 
                $where
            );
        }
        
        if (!empty($user_id) && !empty($row['user_id']))
            $valeurs['user_id'] = $user_id;
        
        return $valeurs;
    
    }
    
    function formulaires_config_oauth_add_consumer_verifier($id_consumer = NULL, $user_id = NULL) {
        $erreurs = array();
        
        if (!_request('osr_requester_name'))
            $erreurs['osr_requester_name'] = 'Veullez saisir le nom du référent';
        if (!_request('osr_requester_email'))
            $erreurs['osr_requester_email'] = 'Veuillez saisir le courriel du réferent';
        if (!_request('user_id'))
            $erreurs['user_id'] = 'Veuillez choisir un Auteur référent';

    
        if ($erreurs)
            $erreurs['message_erreur'] = 'Le formulaire est incomplet, veuillez le vérifier';
        
        return $erreurs;
    
    }
    
    
    function formulaires_config_oauth_add_consumer_traiter($id_consumer = NULL, $user_id= NULL) {
        include_spip('inc/connecteur_sql');

        # The currently logged on user
        $user_id = _request('user_id');

        # This should come from a form filled in by the requesting user
        $consumer = array(
            # These two are required
            'requester_name' => _request('osr_requester_name'),
            'requester_email' => _request('osr_requester_email'),

            # These are all optional
            'callback_uri' => _request('osr_callback_uri'),
            'application_uri' => _request('osr_application_uri'),
            'application_title' => _request('osr_application_title'),
            'application_descr' => _request('osr_application_descr'),
            'application_notes' => _request('osr_application_notes'),
            'application_type' => _request('osr_application_type'),
            'application_commercial' => _request('osr_application_commercial') ? 1 : 0,
        );
        

        # Register the consumer
        $store = connecteur_sql();
        $key  = $store->updateConsumer($consumer, $user_id);

        # Get the complete consumer from the store
        $consumer = $store->getConsumer($key, $user_id);

        # Some interesting fields, the user will need the key and secret
        $consumer_id = $consumer['id'];
        $consumer_id = $consumer['usa_id_ref'];        
        $consumer_key = $consumer['consumer_key'];
        $consumer_secret = $consumer['consumer_secret'];
        
        return array(
            'message_ok' => array(
                'id' => $consumer['id'],
                'consumer_key' =>  $consumer['consumer_key'],
                'consumer_secret' => $consumer['consumer_secret']
            )
        );    
    }
?>
