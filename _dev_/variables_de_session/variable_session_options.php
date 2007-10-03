<?php


	//charge les fonctions de CFG, si variable_session est charg� avant CFG
	include_spip('cfg_options');

/*
 * \param $variable_nom nom de la variable communiqu� par GET ou POST (doit appartenir � la liste definie par CFG)
 * \param $mode, determine si on la donn�e est sauvegard� par session php ou cookie
 * \return : rien, c'est juste une proc�dure 
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
 * \param $variable_nom,  nom de la variable � lire
 * \param $mode,  determine si on la donn�e est sauvegard�e dans une session php ou un cookie
 * \return $valeur, valeur contenue dans la session 
 *
 */   
function lire_variable($variable_nom, $mode='session') {
	switch ($mode) {
		case 'session' :
			//on lance une session
			session_start();
			//on r�cupere la variable de session
			$valeur = $_SESSION['variable_session_'.$variable_nom];
		break;
		case 'cookie' :
			//ne fait encore rien		
		break;
	
	}
	return $valeur;
}

/*
 * /brief Fonction qui liste les variable de session d�clar�e et met � jour les #ENV{} en cons�quence
 *
 * parcours la liste de variables declar�es via CFG
 * met � jour si transmis via POST ou GET
 * transmet la valeur de session si la variable n'est pas declar�e via POST ou GET   
 *
 */   

	//on liste les variables de session
	$variables = lire_config('variable_session/variable');
	//cr�ation d'un tableau des noms de variables de session
	$arr_variables = explode(",",$variables);
	foreach($arr_variables as $variable_nom) {
		//on retire les espaces eventuels avant et apres les ,
		$variable_nom = trim($variable_nom);
		//recup�re le contenue de la variable pass�e en GET ou POST
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
