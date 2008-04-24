<?php

	// on regarde le provenance de l'utilisateur
	// S'il n'est pas autorisé, on le redirige vers une autre URL !!!
	
	//print_r($_SERVER);
	
	$addressC = $_SERVER['REMOTE_ADDR'];
	//echo 'address remote : ', $addressC,'<br>';
	
	$adressipsConf = lire_config('env/addressip');
	$addressips = explode(',', $adressipsConf);
	
	$redirection = lire_config('env/redirection');
	
	$find = matchAddress($addressips,$addressC);
	
	$environnement = lire_config('env/environnement');
	
	
	
	if ($environnement != 'NON') {
	
		if (! $find && $environnement != 'PROD') {
			header('Location:'.$redirection);
		} else if ( $environnement == 'PROD') {
			// on interdit donc certainnes adresses
			$banniesipConf = lire_config('env/banniesip');
			$banniesips = explode(',', $banniesipConf);
			
			if (matchAddress($banniesips,$addressC)) {
				header('Location:'.$redirection);
			}
		}
	}
	
	
	
	function matchAddress($addressips,$addressC) {
	
		foreach ($addressips as $cle => $valeur) {
			//echo $valeur,'  ',$addressC,'<br>';
			if (trim($valeur) == trim($addressC)) {
				// on a trouve l'adresse, on ne fait rien
				return true;
			}
			
			// on regarde si l'adresse IP contientun caractère Joker (dans notre cas x)
			//echo "l'adresse : ", $valeur;
			if (strripos($valeur, 'x')) {
				// on a trouvé un caractère joker.
				$pattern = "^".str_ireplace('x', '[0-9]*', $valeur)."$";
				//echo "<br/>the pattern : ", $pattern, " avec value : ", $addressC;
				
				if (eregi($pattern,$addressC, $regs)) {
					// la regexp matche l'adresse, on ne fait rien
					return true;
				}
			}
		}
		return false;
	}
?>