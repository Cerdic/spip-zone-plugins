<?php

    include('library/OAuthSotre.php');


    #Pamétres de connexion à la base de données
    #$options['conn'] = ;
    $options['server'] = '127.0.0.1';
    $options['username'] = 'developpement';                
    $options['password'] = '2d2l1m1t';        
    $options['database'] = 'developpement_lha'; 

    echo "tot";

    // The currenly logged on user
    $user_id = 1;

    // Fetch all consumers registered by the current user
    $store = OAuthStore::instance('MySQL', $options);

    echo "totzerzer";

    $list = $store->listConsumers($user_id);

    echo "toezrzer787987987t";

    var_dump($list);
?>
