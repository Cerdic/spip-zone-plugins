<?php


	//charge les fonctions de CFG, si variable_session est chargé avant CFG
	include_spip('cfg_options');

/*
 * \param $variable_nom nom de la variable communiqué par GET ou POST (doit appartenir à la liste definie par CFG)
 * \param $mode, determine si on la donnée est sauvegardé par session php ou cookie
 * \return : rien, c'est juste une procédure 
 */
function ecrire_variable($variable_nom, $mode='session') {
	switch ($mode) {
		case 'session' :
			//on lance une session
			session_start();
			//sauvegarde dans une variable de session
			$_SESSION['variable_session_'.$variable_nom]= _request($variable_nom);
		break;
		case 'cookie' :
			//ne fait encore rien		
		break;
	}
}

/*
 * \param $variable_nom,  nom de la variable à lire
 * \param $mode,  determine si on la donnée est sauvegardée dans une session php ou un cookie
 * \return $valeur, valeur contenue dans la session 
 *
 */   
function lire_variable($variable_nom, $mode='session') {
	switch ($mode) {
		case 'session' :
			//on lance une session
			session_start();
			//on récupere la variable de session
			$valeur = $_SESSION['variable_session_'.$variable_nom];
		break;
		case 'cookie' :
			//ne fait encore rien		
		break;
	
	}
	return $valeur;
}

/*
 * /brief Fonction qui liste les variable de session déclarée et met à jour les #ENV{} en conséquence
 *
 * parcours la liste de variables declarées via CFG
 * met à jour si transmis via POST ou GET
 * transmet la valeur de session si la variable n'est pas declarée via POST ou GET   
 *
 */   

	//on liste les variables de session
	$variables = lire_config('variable_session/variable');
	//création d'un tableau des noms de variables de session
	$arr_variables = explode(",",$variables);
	foreach($arr_variables as $variable_nom) {
		//on retire les espaces eventuels avant et apres les ,
		$variable_nom = trim($variable_nom);
		//recupére le contenue de la variable passée en GET ou POST
		$variable_valeur = _request($variable_nom);
		//si celle ci est vide on la remplace par celle connue en session
		if (!isset($variable_valeur)) {
			$_POST[$variable_nom] = lire_variable($variable_nom); 
		}
		//sinon on la sauvegarde en lieu et place en session
		else {
			ecrire_variable($variable_nom);
		}
	}
?>
