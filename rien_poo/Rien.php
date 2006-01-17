<?php

/**
 * definition du plugin "rien" version "classe statique"
 */
class Rien extends Plugin {
	/* static public */
	function leFiltre($quelquechose) {
		// ne rien faire = retourner ce qu'on nous a envoye
		return $quelquechose.'<!-- rien_poo -->';
	}

	/* public static */
	function ajouterBoutons($boutons_admin) {
	  // en vrai, il faudrait prevoir des icones differents ...
		$entree= array(
			'libelle' => 'rien !',
			'icone' => '../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png');

		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton dans la barre "naviguer"
		  Rien::insertBefore($boutons_admin['naviguer']['sousmenu'],
							 'breves', 'rien_poo', $entree);

		  // et on accede a la config
		  $boutons_admin['configuration']['sousmenu']['riens']= $entree;
		} else {
		  // sinon, on voit un icone de plus dans la barre du haut
		  Rien::insertBefore($boutons_admin,
					 'naviguer', 'rien_poo', $entree);
		}

		return $boutons_admin;
	}

	/* public static */
	function ajouterOnglets($onglets, $rubrique) {
		$onglets['riens']=array(
			'libelle' => 'rien ...',
			'icone' => '../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png');
		return $onglets;
	}
}
?>
