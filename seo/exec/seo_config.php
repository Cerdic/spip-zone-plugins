<?php
/**
 * BouncingOrange SPIP SEO plugin
 *
 * @category   SEO
 * @package    SPIP_SEO
 * @author     Pierre ROUSSET (p.rousset@gmail.com)
 * @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
 * @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function exec_seo_config(){

	if (!autoriser('configurer', 'configuration')){
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	///// PAGE /////

	$titre_page = _T('icone_configuration_site');
	$rubrique = 'configuration';
	$sous_rubrique = 'seo';

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('seo:seo') . ' - ' . $titre_page, $rubrique, $sous_rubrique));

	$page_result = ''
		. '<br /><br /><br />'
		. gros_titre(_T('titre_page_config_contenu'), '', false)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. pipeline('affiche_gauche', array('args' => array('exec' => 'seo_config'), 'data' => ''))
		. creer_colonne_droite($rubrique, true)
		. pipeline('affiche_droite', array('args' => array('exec' => 'seo_config'), 'data' => ''))
		. debut_droite($rubrique, true);

	// Insert Head //

	// Meta tag //

	// Canonical URL //

	// Google Webmaster Tools //

	// Google Analytics //

	$page_result .= recuperer_fond('prive/configurer_seo');

	echo $page_result, pipeline('affiche_milieu', array('args' => array('exec' => $sous_rubrique), 'data' => '')), fin_gauche(), fin_page();
}

?>
