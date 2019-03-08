<?php
/**
 * Utilisations de pipelines par Spipr-Dane Config
 *
 * @plugin     Spipr-Dane Config
 * @copyright  2019
 * @author     Webmestre DANE
 * @licence    GNU/GPL
 * @package    SPIP\Sdc\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline header_prive
 * 
 * Insertion de la feuille de style de l'espace privé
 * 
 * @param string $flux
 * 		Le code html du head de l'espace privé
 * @return string $flux
 * 		Le code html du head complété
 */
function sdc_header_prive($flux){
	if ( $flux["args"]["exec"] == 'configurer_sdc') {
        include_spip('inc/filtres');
        $css=find_in_path('prive/themes/spip/css/prive_perso.css');
        if (function_exists('produire_fond_statique'))
            $css_html = produire_fond_statique(find_in_path('prive/themes/spip/css/style_prive_sdc.css'));
        else
             $css_html = generer_url_public('prive/themes/spip/css/style_prive_sdc.css');

        $flux .= "\n<link rel=\"stylesheet\" href=\"".$css_html."\" type=\"text/css\" />\n";
        $flux .= "\n<link rel=\"stylesheet\" href=\"".$css."\" type=\"text/css\" />\n";
 
  }  

	return $flux;
}
