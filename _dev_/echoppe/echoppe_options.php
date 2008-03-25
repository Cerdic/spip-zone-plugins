<?php
	include_spip('base/echoppe');
	
	if (strlen(session_get('echoppe_token_panier')) < 10){
		
		$test_existance_token = 0;
		$new_token = md5(uniqid(rand(), true));
		while ($test_existance_token > 0){
			$new_token = md5(uniqid(rand(), true));
			$test_existance_token = spip_num_rows(spip_query("SELECT id_panier FROM spip_echoppe_paniers WHERE token_panier = '".$new_token."' ;"));
		}
		
		session_set('echoppe_token_panier', $new_token );
		
	}

	if (strlen(session_get('echoppe_token_client')) < 10){
		
		$test_existance_token = 0;
		$new_token = md5(uniqid(rand(), true));
		while ($test_existance_token > 0){
			$new_token = md5(uniqid(rand(), true));
			$test_existance_token = spip_num_rows(spip_query("SELECT id_client FROM spip_echoppe_client WHERE token_client = '".$new_token."' ;"));
		}
		
		session_set('echoppe_token_client', md5(uniqid(rand(), true)) );
		
	}

	if (strlen(session_get('echoppe_statut_panier')) < 5){
		session_set('echoppe_statut_panier', 'temporaire' );
	}
?>
