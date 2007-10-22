<?php
// MES_OPTIONS pour ACCESGROUPE : toutes les fonctions utilisées pour le controle d'accès espaces public / privé

include_spip('base/accesgroupes_tables');
function debug_var($var){
// fonction pour débuggage / affichage variable
$r = "<pre>";
$r .= print_r($var);
$r .= "<pre>";
return $r;
}

// SURCHARGE des fonctions de l'espace privé
//   inclure les fichiers originaux de /ecrire/exec pour que toutes les fonctions natives du core soient disponibles
//   mais ne le faire que si on est sur une page de l'espace privé le nécessitant
//	 !!! EXCEPTION : breves_voir est surchargé par le fichier /exec/breves_voir.php puisque le bridage d'accès se fait dans 
//	 la fonction afficher_breves_voir() et non pas la fonction exec_breves_voir() !!!
//	 merci ESJ pour la subtilité du include() php à la place du inclure_spip()

if (!_DIR_RESTREINT){
	$exec = _request('exec'); // si on est dans l'espace privé : intégrer le fichier concerné par la surcharge
	// appel de la gestion de l'espace privé...
	include_spip('inc/accesgroupes_prive');
	include_spip('inc/accesgroupes_lib');
}
else {
	// CACHE : nécessité d'un cache différencié selon les rubriques autorisées/restreintes 
	//   ajouter un marqueur de cache pour permettre de differencier le cache en fonction des rubriques autorisees
	// 	 potentiellement une version de cache differente par combinaison de rubriques autorisées pour un utilisateur + le cache de base sans autorisation
	//   merci Cedric pour la méthode (plugin acces_restreint) 
	if ($exec == '') {  // si on on est dans l'espace public gérer le marqueur de cache
		if (isset($auteur_session['id_auteur'])) {
			//echo '<br>début cache';
			$combins = accesgroupes_combin();
			$combins = join("-",$combins);
			if (!isset($GLOBALS['marqueur'])) {
				$GLOBALS['marqueur'] = "";
			}
			$GLOBALS['marqueur'] .= ":accesgroupes_combins $combins";
		}
	}

	// fct pour construire et renvoyer le tableau des rubriques à accès restreint dans la partie PUBLIQUE
	// 	 clone de la fct accesgroupes_liste_rubriques_restreintes() de inc/accesgroupes_fonctions.php 
	function accesgroupes_combin($id_parent = 0) {
		include_spip('inc/accesgroupes_lib');
		$id_parent = intval($id_parent); // securite					 
		static $Trub_restreintes; // nécessaire pour que la suite ne soit éxécutée qu'une fois par hit (même si on à n BOUCLES)
		if (!is_array($Trub_restreintes)) {
			$Trub_restreintes = array();
			// attaquer à la racine pour mettre tout de suite les éventuels secteurs restreints dans le tableau ce qui accélèrera la suite
			$sql1 = "SELECT id_rubrique, id_parent, id_secteur FROM spip_rubriques";	
			$result1 = spip_query($sql1);
			while ($row1 = spip_fetch_array($result1)) {
				$rub_ec = $row1['id_rubrique'];
				$parent_ec = $row1['id_parent'];
				$sect_ec = $row1['id_secteur'];
				// si le parent ou le secteur est déja dans le tableau : vu le principe d'héritage pas la peine d'aller plus loin :)
				/*	 if (in_array($parent_ec, $Trub_restreintes) OR in_array($sect_ec, $Trub_restreintes)) {
						$Trub_restreintes[] = $rub_ec;
					}
				// sinon c'est plus couteux : il faut faire le test complet de la restriction de la rubrique pour espace public
					else {*/
				if (accesgroupes_verif_acces($rub_ec, 'public') == 1 OR accesgroupes_verif_acces($rub_ec, 'public') == 2) {
					$Trub_restreintes[] = $rub_ec;
				}
				//	 }
			}
		}
		//echo '<br>tableau des rubriques = ';
		//print_r($Trub_restreintes);
		return $Trub_restreintes;
	}
}


?>
