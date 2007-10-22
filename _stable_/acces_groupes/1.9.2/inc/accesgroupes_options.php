<?php
// MES_OPTIONS pour ACCESGROUPE : toutes les fonctions utilis�es pour le controle d'acc�s espaces public / priv�

include_spip('base/accesgroupes_tables');
function debug_var($var){
// fonction pour d�buggage / affichage variable
$r = "<pre>";
$r .= print_r($var);
$r .= "<pre>";
return $r;
}

// SURCHARGE des fonctions de l'espace priv�
//   inclure les fichiers originaux de /ecrire/exec pour que toutes les fonctions natives du core soient disponibles
//   mais ne le faire que si on est sur une page de l'espace priv� le n�cessitant
//	 !!! EXCEPTION : breves_voir est surcharg� par le fichier /exec/breves_voir.php puisque le bridage d'acc�s se fait dans 
//	 la fonction afficher_breves_voir() et non pas la fonction exec_breves_voir() !!!
//	 merci ESJ pour la subtilit� du include() php � la place du inclure_spip()

if (!_DIR_RESTREINT){
	$exec = _request('exec'); // si on est dans l'espace priv� : int�grer le fichier concern� par la surcharge
	// appel de la gestion de l'espace priv�...
	include_spip('inc/accesgroupes_prive');
	include_spip('inc/accesgroupes_lib');
}
else {
	// CACHE : n�cessit� d'un cache diff�renci� selon les rubriques autoris�es/restreintes 
	//   ajouter un marqueur de cache pour permettre de differencier le cache en fonction des rubriques autorisees
	// 	 potentiellement une version de cache differente par combinaison de rubriques autoris�es pour un utilisateur + le cache de base sans autorisation
	//   merci Cedric pour la m�thode (plugin acces_restreint) 
	if ($exec == '') {  // si on on est dans l'espace public g�rer le marqueur de cache
		if (isset($auteur_session['id_auteur'])) {
			//echo '<br>d�but cache';
			$combins = accesgroupes_combin();
			$combins = join("-",$combins);
			if (!isset($GLOBALS['marqueur'])) {
				$GLOBALS['marqueur'] = "";
			}
			$GLOBALS['marqueur'] .= ":accesgroupes_combins $combins";
		}
	}

	// fct pour construire et renvoyer le tableau des rubriques � acc�s restreint dans la partie PUBLIQUE
	// 	 clone de la fct accesgroupes_liste_rubriques_restreintes() de inc/accesgroupes_fonctions.php 
	function accesgroupes_combin($id_parent = 0) {
		include_spip('inc/accesgroupes_lib');
		$id_parent = intval($id_parent); // securite					 
		static $Trub_restreintes; // n�cessaire pour que la suite ne soit �x�cut�e qu'une fois par hit (m�me si on � n BOUCLES)
		if (!is_array($Trub_restreintes)) {
			$Trub_restreintes = array();
			// attaquer � la racine pour mettre tout de suite les �ventuels secteurs restreints dans le tableau ce qui acc�l�rera la suite
			$sql1 = "SELECT id_rubrique, id_parent, id_secteur FROM spip_rubriques";	
			$result1 = spip_query($sql1);
			while ($row1 = spip_fetch_array($result1)) {
				$rub_ec = $row1['id_rubrique'];
				$parent_ec = $row1['id_parent'];
				$sect_ec = $row1['id_secteur'];
				// si le parent ou le secteur est d�ja dans le tableau : vu le principe d'h�ritage pas la peine d'aller plus loin :)
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
