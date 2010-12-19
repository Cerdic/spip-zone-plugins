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

function action_proteger_rubrique_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_rubrique = intval($arg);
	$les_auteurs = array(1,2,45);

	// Protection rubrique
	if (!$id_zone=proteger_rubrique($id_rubrique,$les_auteurs)) {
		return false;
	}

	// Retour
	include_spip('inc/headers');
	redirige_par_entete(urldecode(_request('redirect')));

}


?>