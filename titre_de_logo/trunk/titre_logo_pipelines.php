<?php

/*
 * Plugin Titre de logo
 *
 * @plugin     Titre de logo
 *
 * @copyright  2015
 * @author     Arno*
 * @licence    GPL 3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}



function titre_logo_recuperer_fond($flux) {
	
	
	if ($flux["args"]["fond"] == "formulaires/editer_logo") {

		$id_objet = $flux["args"]["contexte"]["id_objet"];
		$objet = $flux["args"]["contexte"]["objet"];
		$editable = $flux["args"]["contexte"]["_options"]["editable"];
		
		if ($editable) {
			
			$objets_autorises = lire_config('titre_logo/objets_autorises');
			$objets_autorises = (isset($objets_autorises))
				? array_filter($objets_autorises)
				: array();
		
		
		 $table_objet = table_objet_sql($objet);
		 
			$texte = $flux["data"]["texte"] ;
			
			// Bof bof: repérer la mention «taille» dans le texte
			// ce qui indique que le formulaire de logo contient cette mention
			if (in_array($table_objet, $objets_autorises) 
					&& strpos($texte, 'taille') > 0) {
				$cont = array(
					"objet" => "article", 
					"id_objet" => $id_objet
				);
				
				$ajouter = recuperer_fond("prive/inc_editer_titre_logo", $cont);
				
				$flux["data"]["texte"] = str_replace("</form>", "</form>".$ajouter, $texte);
			}
		}
	}
	return $flux;
}