<?php
/**
 * Plugin Partageur
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajout des CSS du partageur au head privé
 *
 * @pipeline header_prive_css
 * @param string $flux  Contenu du head
 * @return string Contenu du head complété
 */
function partageur_insert_head_prive_css($flux){
		$cssprive = find_in_path('css/partageur_prive.css');
		$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$cssprive' />\n";		
    
    return $flux;
}

 
/**
 * Ajouter les partageurs sur les vues de rubriques
 *
 * @param array $flux
 * @return array
**/
function partageur_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['type'] == 'rubrique'
	  AND $e['edition'] == false) {
		    $id_rubrique = $flux['args']['id_rubrique'];

    if (autoriser('voir', 'partageur')) {           
       $bouton_sites .= icone_verticale(_T('partageur:ajout_via_partageur'), generer_url_ecrire('partageur_add', "id_rubrique=$id_rubrique"), "partageur-24.png", "new", 'right')
					. "<br class='nettoyeur' />";   
      
       $flux['data'] .= $bouton_sites;    
		}

    
	}
	return $flux;
}






?>