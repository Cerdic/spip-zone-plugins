<?php

/**
 * definition du plugin "rien" version "classe statique"
 */

	/* static public */
	function Rien_leFiltre($quelquechose) {
		// ne rien faire = retourner ce qu'on nous a envoye
		return $quelquechose.'<!-- rien_poo -->';
	}

	/* public static */
	function Rien_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu['rien_poo']= new Bouton(
			'../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png', 'rien !');

		  // et on accede a la config
		  $boutons_admin['configuration']->sousmenu['rien_config']= new Bouton(
			'../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png', 'rien ?');
		} else {
		  // sinon, on voit un icone de plus dans la barre du haut
		  $boutons_admin['rien_poo']= new Bouton(
			'../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png', 'rien !');
		}

		return $boutons_admin;
	}

	/* public static */
	function Rien_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		if($rubrique=='configuration')
		  $flux['data']['rien_config']= new Bouton(
			'../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png', 'rien ...');
		return $flux;
	}

?>