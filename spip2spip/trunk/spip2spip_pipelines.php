<?php
/**
 * Plugin Spip2Spip
 * (c) 2013 erational
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajout des CSS du spip2spip au head privé
 *
 * @pipeline header_prive_css
 * @param string $flux  Contenu du head
 * @return string Contenu du head complété
 */
function spip2spip_insert_head_prive_css($flux){
		$cssprive = find_in_path('css/spip2spip_prive.css');
		$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$cssprive' />\n";		
    
    return $flux;
}

 
/**
 * Ajouter les spip2spips sur les vues de rubriques
 *
 * @param array $flux
 * @return array
**/
function spip2spip_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['type'] == 'rubrique'
	  AND $e['edition'] == false) {
		    $id_rubrique = $flux['args']['id_rubrique'];

    if (autoriser('voir', 'spip2spip')) {           
       $bouton_sites .= icone_verticale(_T('spip2spip:ajout_via_spip2spip'), generer_url_ecrire('spip2spip_add', "id_rubrique=$id_rubrique"), "spip2spip-24.png", "new", 'right')
					. "<br class='nettoyeur' />";   
      
       $flux['data'] .= $bouton_sites;    
		}

    
	}
	return $flux;
}






?>