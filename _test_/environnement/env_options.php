<?php

	// on regarde le provenance de l'utilisateur
	// S'il n'est pas autorisé, on le redirige vers une autre URL !!!
	
	//print_r($_SERVER);
	
	$addressC = $_SERVER['REMOTE_ADDR'];
	//echo 'address remote : ', $addressC,'<br>';
	
	$adressipsConf = lire_config('env/addressip');
	$addressips = explode(',', $adressipsConf);
	
	$redirection = lire_config('env/redirection');
	
	$find = false;
	foreach ($addressips as $cle => $valeur) {
		//echo $valeur,'  ',$addressC,'<br>';
		if (trim($valeur) == trim($addressC)) {
			// on a trouve l'adresse, on ne fait rien
			$find = true;
			break;
		}
	}
	
	$environnement = lire_config('env/environnement');
	
	if ($environnement != 'NON') {
	
		if (! $find && $environnement != 'PROD') {
			header('Location:'.$redirection);
		} else if ( $environnement == 'PROD') {
			// on interdit donc certainnes adresses
			$banniesipConf = lire_config('env/banniesip');
			$banniesips = explode(',', $banniesipConf);
			foreach ($banniesips as $cle => $valeur) {
				if (trim($valeur) == trim($addressC)) {
					header('Location:'.$redirection);
				}
			}
		}
	}
	
	
?>