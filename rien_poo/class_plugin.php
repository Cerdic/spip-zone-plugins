<?php

// classe par defaut pour la definition des plugins

class Plugin {
	/* public static */
	function ajouterBoutons($boutons_admin) {
		return $boutons_admin;
	}

	/* public static */
	function ajouterOnglets($onglets, $rubrique) {
		return $onglets;
	}

	/** fonction permettant d'insérer un element dans un tableau */
	/* public static */
	function insertBefore(&$t, $marque, $cle, $valeur) {
		$pos= array_keys(array_keys($t), $marque);
		if(count($pos)==1) {
			$pos= $pos[0];
		} else {
			$pos= count($t);
		}
		$t= array_merge(array_slice($t, 0, $pos),
						array($cle => $valeur),
						array_slice($t, $pos));
	}
}

?>
