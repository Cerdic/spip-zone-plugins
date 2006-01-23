<?php

/**
 * definition du plugin "rien" version "classe statique"
 */
class Rien {
	/* static public */
	function leFiltre($quelquechose) {
		// ne rien faire = retourner ce qu'on nous a envoye
		return $quelquechose.'<!-- rien_poo -->';
	}

	/* public static */
	function ajouterBoutons($boutons_admin) {
	  // en vrai, il faudrait prevoir des icones differents ...
		$entree= new Bouton(
			'../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png', 'rien !');

		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton dans la barre "naviguer"
		  Rien::insertBefore($boutons_admin['naviguer']->sousmenu,
							 'breves', 'rien_poo', $entree);

		  // et on accede a la config
		  $boutons_admin['configuration']->sousmenu['riens']= $entree;
		} else {
		  // sinon, on voit un icone de plus dans la barre du haut
		  Rien::insertBefore($boutons_admin,
					 'naviguer', 'rien_poo', $entree);
		}

		return $boutons_admin;
	}

	/* public static */
	function ajouterOnglets($onglets, $rubrique) {
		if($rubrique=='configuration')
		  $onglets['riens']= new Bouton(
			'../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png', 'rien ...');
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
