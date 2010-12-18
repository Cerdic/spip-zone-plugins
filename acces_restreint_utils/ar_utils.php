<?php


function ar_utils_affiche_gauche($flux) {

	// echo'<script type="text/javascript">alert("OK affiche gauche")</script>';
	
	$exec =  $flux['args']['exec'];
	
	// si on est sur la page ?exec=naviguer
	if ($exec=='naviguer'){
	
		// on récupère l'id_rubrique
		$id_rubrique = $flux['args']['id_rubrique'];
		$afficher = true;

		if ($afficher) {
			$contexte = array();
			foreach($_GET as $key=>$val)
				$contexte[$key] = $val;
				
			// on charge le bout de squelette 
			$acces = recuperer_fond('prive/contenu/acces_rubrique',$contexte);
			$flux['data'] .= $acces;
		}
	}

	return $flux;
}

?>