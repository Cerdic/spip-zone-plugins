<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('lettres_fonctions');


	function action_progression_envoi_lettre() {
		$id_lettre = $_REQUEST['id_lettre'];

		header("Content-type: text/xml"); 
		header("Cache-Control: no-cache");

		if (autoriser('editer', 'lettres')) {
			$lettre = new lettre($id_lettre);
			if ($lettre->statut == 'envoi_en_cours')
				$fin = $lettre->enregistrer_statut('envoi_en_cours', false, true);
		}

		echo "<?xml version=\"1.0\"?>\n"; 
		echo "<reponse>\n"; 
		echo "\t<fin>".$fin."</fin>\n";
		echo "</reponse>";

	}

?>