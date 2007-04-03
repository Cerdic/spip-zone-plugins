<?php

/*
 * mediatheque
 *
 */

	function mediatheque_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" ) {

		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['naviguer'] -> sousmenu["mediatheque_img_browser"] = 
			new Bouton(
			_DIR_PLUGIN_MEDIATHEQUE."/img_pack/phototheque_icon.jpg",  // icone
			"Phototheque" //titre
			);
			
			
			 // on voit les bouton dans la barre "accueil"
			$boutons_admin['naviguer'] -> sousmenu["mediatheque_doc_browser"] = 
			new Bouton(
			_DIR_PLUGIN_MEDIATHEQUE."/img_pack/bibliotheque_icon.jpg",  // icone
			"Documentheque" //titre
			);
		}
		
		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['configuration'] -> sousmenu["mediatheque_admin_start"] = 
			new Bouton(
			_DIR_PLUGIN_MEDIATHEQUE."/img_pack/phototheque_icon.jpg",  // icone
			"Admin<br/> Mediath&egrave;que" //titre
			);
		
		return $boutons_admin;
	}


	function mediatheque_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}


?>