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

function exec_seo_config () {

	include_spip('inc/distant');
	include_spip('inc/meta');
	include_spip('inc/config');
	
    if (!autoriser('configurer', 'configuration')) {
        include_spip('inc/minipres');
        echo minipres();
        exit();
    }
       
	///// Config /////
	
	// Get the current config
	$config = unserialize($GLOBALS['meta']['seo']);
	
	// Save it if needed
	if (_request('insert_head_submit')) {
		$config['insert_head'] = _request('insert_head');
		ecrire_meta('seo', serialize($config));
	} elseif (_request('meta_tags_submit')) {
		$config['meta_tags'] = _request('meta_tags');
		ecrire_meta('seo', serialize($config));
	} elseif (_request('webmaster_tools_submit')) {
		$config['webmaster_tools'] = _request('webmaster_tools');
		ecrire_meta('seo', serialize($config));
	} elseif (_request('analytics_submit')) {
		$config['analytics'] = _request('analytics');
		ecrire_meta('seo', serialize($config));
	} elseif (_request('canonical_url_submit')) {
		$config['canonical_url'] = _request('canonical_url');
		ecrire_meta('seo', serialize($config));
	} elseif (_request('alexa_submit')) {
		$config['alexa'] = _request('alexa');
		ecrire_meta('seo', serialize($config));
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
		. pipeline('affiche_gauche', array('args'=>array('exec'=>'seo_config'),'data'=>''))
		. creer_colonne_droite($rubrique, true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>'seo_config'),'data'=>''))
		. debut_droite($rubrique, true)
		;
		
	// Insert Head //
	$page_result .= ''
		. debut_cadre_trait_couleur('', true, '', _T('seo:insert_head'))
		. '<form action="" method="post">'
		
		. debut_cadre_relief('', true, '', _T('seo:insert_head_descriptif'))
		. '<input type="checkbox" value="yes" name="insert_head[activate]" '.(($config['insert_head']['activate'] == 'yes') ? "checked='checked'" : "").'/>'
		. '<label for="statut_simple">'._T('seo:insert_head_activate').'</label>'
		. fin_cadre_relief(true)
				
		// Submit button
		. '<div style="text-align:right;"><input type="submit" name="insert_head_submit" class="fondo" value="'._T('bouton_valider').'" /></div>'
		. '</form>'
		. fin_cadre_trait_couleur(true)
		;
		
	// Meta tag //
	$page_result .= ''
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SEO.'img_pack/meta_tags-24.png', true, '', _T('seo:meta_tags'))
		. '<form action="" method="post">'
		
		. debut_cadre_relief('', true, '', _T('seo:meta_tags'))
		. '<input type="checkbox" value="yes" name="meta_tags[activate]" '.(($config['meta_tags']['activate'] == 'yes') ? "checked='checked'" : "").' onChange="activeForm($(this), $(\'.meta_tags_slide\'))"/>'
		. '<label for="meta_tags[activate]">'._T('seo:meta_tags_activate').'</label>'
		. fin_cadre_relief(true)
		
		. debut_cadre_relief('', true, '', _T('seo:meta_tags_editing'), '', 'meta_tags_slide')
		. '<input type="checkbox" value="yes" name="meta_tags[activate_editing]"  '.(($config['meta_tags']['activate_editing'] == 'yes') ? "checked='checked'" : "").'/>'
		. '<label for="meta_tags[activate_editing]">'._T('seo:meta_tags_edit_activate').'</label>'
		. fin_cadre_relief(true)

		. debut_cadre_relief('', true, '', _T('seo:meta_tags_sommaire'), '', 'meta_tags_slide')		
		. '<table cellspacing="0" cellpadding="2" border="0" width="100%"><tbody>'
		. '<tr><td style="width:80px;">'._T('seo:meta_title').'</td><td><input type="text" name="meta_tags[tag][title]" value="'.$config['meta_tags']['tag']['title'].'" style="width:100%;" /></td></tr>'
		. '<tr><td>'._T('seo:meta_description').'</td><td><textarea style="width:100%;" rows="5" name="meta_tags[tag][description]" type="text">'.$config['meta_tags']['tag']['description'].'</textarea></td></tr>'
		. '<tr><td>'._T('seo:meta_keywords').'</td><td><input type="text" name="meta_tags[tag][keywords]" value="'.$config['meta_tags']['tag']['keywords'].'" style="width:100%;" /></td></tr>'
		. '<tr><td>'._T('seo:meta_copyright').'</td><td><input type="text" name="meta_tags[tag][copyright]" value="'.$config['meta_tags']['tag']['copyright'].'" style="width:100%;" /></td></tr>'
		. '<tr><td>'._T('seo:meta_author').'</td><td><input type="text" name="meta_tags[tag][author]" value="'.$config['meta_tags']['tag']['author'].'" style="width:100%;" /></td></tr>'
		. '<tr>'
		. 	'<td>'._T('seo:meta_robots').'</td>'
		. 	'<td>'
		.		'<select name="meta_tags[tag][robots]" style="width:100%;">'
		.			'<option '.(($config['meta_tags']['tag']['robots'] == '') ? "selected" : '').' value=""></option>'
		.			'<option '.(($config['meta_tags']['tag']['robots'] == 'INDEX, FOLLOW') ? "selected" : '').' value="INDEX, FOLLOW">INDEX, FOLLOW</option>'
		.			'<option '.(($config['meta_tags']['tag']['robots'] == 'INDEX, NOFOLLOW') ? "selected" : '').' value="INDEX, NOFOLLOW">INDEX, NOFOLLOW</option>'
		.			'<option '.(($config['meta_tags']['tag']['robots'] == 'NOINDEX, FOLLOW') ? "selected" : '').' value="NOINDEX, FOLLOW">NOINDEX, FOLLOW</option>'
		.			'<option '.(($config['meta_tags']['tag']['robots'] == 'NOINDEX, NOFOLLOW') ? "selected" : '').' value="NOINDEX, NOFOLLOW">NOINDEX, NOFOLLOW</option>'
		.			'<option '.(($config['meta_tags']['tag']['robots'] == 'INDEX, FOLLOW, NOARCHIVE') ? "selected" : '').' value="INDEX, FOLLOW, NOARCHIVE">INDEX, FOLLOW, NOARCHIVE</option>'
		.			'<option '.(($config['meta_tags']['tag']['robots'] == 'INDEX, NOFOLLOW, NOARCHIVE') ? "selected" : '').' value="INDEX, NOFOLLOW, NOARCHIVE">INDEX, NOFOLLOW, NOARCHIVE</option>'
		.			'<option '.(($config['meta_tags']['tag']['robots'] == 'NOINDEX, NOFOLLOW, NOARCHIVE') ? "selected" : '').' value="NOINDEX, NOFOLLOW, NOARCHIVE">NOINDEX, NOFOLLOW, NOARCHIVE</option>'
		.		'</select>'
		.	'</td>'
		. '</tr>'
		. '</tbody></table>'
		. fin_cadre_relief(true)
		
		. debut_cadre_relief('', true, '', _T('seo:meta_tags_default'), '', 'meta_tags_slide')		
		. '<table cellspacing="0" cellpadding="2" border="0" width="100%"><tbody>'
		. '<tr>'
		. 	'<td style="width:80px;">'._T('seo:meta_title').'</td>'
		. 	'<td>'
		.		'<select name="meta_tags[default][title]" style="width:100%;">'
		.			'<option '.(($config['meta_tags']['default']['title'] == '') ? "selected" : '').' value=""></option>'
		.			'<option '.(($config['meta_tags']['default']['title'] == 'page') ? "selected" : '').' value="page">'._T('seo:meta_page_title_value').'</option>'
		.			'<option '.(($config['meta_tags']['default']['title'] == 'sommaire') ? "selected" : '').' value="sommaire">'._T('seo:meta_sommaire_value').'</option>'
		.			'<option '.(($config['meta_tags']['default']['title'] == 'page_sommaire') ? "selected" : '').' value="page_sommaire">'._T('seo:meta_page_title_sommaire_value').'</option>'
		.		'</select>'
		.	'</td>'
		. '</tr>'
		. '<tr>'
		. 	'<td>'._T('seo:meta_description').'</td>'
		. 	'<td>'
		.		'<select name="meta_tags[default][description]" style="width:100%;">'
		.			'<option '.(($config['meta_tags']['default']['description'] == '') ? "selected" : '').' value=""></option>'
		.			'<option '.(($config['meta_tags']['default']['description'] == 'page') ? "selected" : '').' value="page">'._T('seo:meta_page_description_value').'</option>'
		.			'<option '.(($config['meta_tags']['default']['description'] == 'sommaire') ? "selected" : '').' value="sommaire">'._T('seo:meta_sommaire_value').'</option>'
		.			'<option '.(($config['meta_tags']['default']['description'] == 'page_sommaire') ? "selected" : '').' value="page_sommaire">'._T('seo:meta_page_description_sommaire_value').'</option>'
		.		'</select>'
		.	'</td>'
		. '</tr>'
		. 	'<td>'._T('seo:meta_keywords').'</td>'
		. 	'<td>'
		.		'<select name="meta_tags[default][keywords]" style="width:100%;">'
		.			'<option '.(($config['meta_tags']['default']['keywords'] == '') ? "selected" : '').' value=""></option>'
		.			'<option '.(($config['meta_tags']['default']['keywords'] == 'sommaire') ? "selected" : '').' value="sommaire">'._T('seo:meta_sommaire_value').'</option>'
		.		'</select>'
		.	'</td>'
		. '</tr>'
		. 	'<td>'._T('seo:meta_copyright').'</td>'
		. 	'<td>'
		.		'<select name="meta_tags[default][copyright]" style="width:100%;">'
		.			'<option '.(($config['meta_tags']['default']['copyright'] == '') ? "selected" : '').' value=""></option>'
		.			'<option '.(($config['meta_tags']['default']['copyright'] == 'sommaire') ? "selected" : '').' value="sommaire">'._T('seo:meta_sommaire_value').'</option>'
		.		'</select>'
		.	'</td>'
		. '</tr>'
		. 	'<td>'._T('seo:meta_author').'</td>'
		. 	'<td>'
		.		'<select name="meta_tags[default][author]" style="width:100%;">'
		.			'<option '.(($config['meta_tags']['default']['author'] == '') ? "selected" : '').' value=""></option>'
		.			'<option '.(($config['meta_tags']['default']['author'] == 'sommaire') ? "selected" : '').' value="sommaire">'._T('seo:meta_sommaire_value').'</option>'
		.		'</select>'
		.	'</td>'
		. '</tr>'
		. '</tr>'
		. 	'<td>'._T('seo:meta_robots').'</td>'
		. 	'<td>'
		.		'<select name="meta_tags[default][robots]" style="width:100%;">'
		.			'<option '.(($config['meta_tags']['default']['robots'] == '') ? "selected" : '').' value=""></option>'
		.			'<option '.(($config['meta_tags']['default']['robots'] == 'sommaire') ? "selected" : '').' value="sommaire">'._T('seo:meta_sommaire_value').'</option>'
		.		'</select>'
		.	'</td>'
		. '</tr>'
		. '</tbody></table>'
		. fin_cadre_relief(true)
		
		// Submit button
		. '<div style="text-align:right;"><input type="submit" name="meta_tags_submit" class="fondo" value="'._T('bouton_valider').'" /></div>'
		. '</form>'
		. fin_cadre_trait_couleur(true)
		;
		
	// Canonical URL //
	$page_result .= ''
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SEO.'img_pack/canonical_url-24.png', true, '', _T('seo:canonical_url'))
		. '<form action="" method="post">'
		
		. debut_cadre_relief('', true, '', _T('seo:canonical_url'))
		. '<input type="checkbox" value="yes" name="canonical_url[activate]" '.(($config['canonical_url']['activate'] == 'yes') ? "checked='checked'" : "").'/>'
		. '<label for="statut_simple">'._T('seo:canonical_url_activate').'</label>'
		. fin_cadre_relief(true)
				
		// Submit button
		. '<div style="text-align:right;"><input type="submit" name="canonical_url_submit" class="fondo" value="'._T('bouton_valider').'" /></div>'
		. '</form>'
		. fin_cadre_trait_couleur(true)
		;
		
	// Google Webmaster Tools //
	$page_result .= ''
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SEO.'img_pack/google_webmaster-24.png', true, '', _T('seo:google_webmaster_tools'))
		. '<form action="" method="post">'
		
		. debut_cadre_relief('', true, '', _T('seo:google_webmaster_tools'))
		. '<input type="checkbox" value="yes" name="webmaster_tools[activate]" '.(($config['webmaster_tools']['activate'] == 'yes') ? "checked='checked'" : "").' onChange="activeForm($(this), $(\'.webmaster_tools_slide\'))"/>'
		. '<label for="statut_simple">'._T('seo:google_webmaster_tools_activate').'</label>'
		. fin_cadre_relief(true)
		
		. debut_cadre_relief('', true, '', _T('seo:google_webmaster_tools_id'), '', 'webmaster_tools_slide')
		. '<input type="text" name="webmaster_tools[id]" value="'.$config['webmaster_tools']['id'].'" class="formo"/>'
		. fin_cadre_relief(true)
				
		// Submit button
		. '<div style="text-align:right;"><input type="submit" name="webmaster_tools_submit" class="fondo" value="'._T('bouton_valider').'" /></div>'
		. '</form>'
		. fin_cadre_trait_couleur(true)
		;
		
	// Google Analytics //
	$page_result .= ''
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SEO.'img_pack/google_analytics-24.png', true, '', _T('seo:google_analytics'))
		. '<form action="" method="post">'
		
		. debut_cadre_relief('', true, '', _T('seo:google_analytics'))
		. '<input type="checkbox" value="yes" name="analytics[activate]" '.(($config['analytics']['activate'] == 'yes') ? "checked='checked'" : "").' onChange="activeForm($(this), $(\'.analytics_slide\'))"/>'
		. '<label for="statut_simple">'._T('seo:google_analytics_activate').'</label>'
		. fin_cadre_relief(true)
		
		. debut_cadre_relief('', true, '', _T('seo:google_analytics_id'), '', 'analytics_slide')
		. '<input type="text" name="analytics[id]" value="'.$config['analytics']['id'].'" class="formo"/>'
		. fin_cadre_relief(true)
		
		// Submit button
		. '<div style="text-align:right;"><input type="submit" name="analytics_submit" class="fondo" value="'._T('bouton_valider').'" /></div>'
		. '</form>'
		. fin_cadre_trait_couleur(true)
		;
	
	// Alexa meta //
	$page_result .= ''
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SEO.'img_pack/alexa-24.png', true, '', _T('seo:alexa'))
		. '<form action="" method="post">'
		
		. debut_cadre_relief('', true, '', _T('seo:alexa'))
		. '<input type="checkbox" value="yes" name="alexa[activate]" '.(($config['alexa']['activate'] == 'yes') ? "checked='checked'" : "").' onChange="activeForm($(this), $(\'.alexa_slide\'))"/>'
		. '<label for="alexa">'._T('seo:alexa_activate').'</label>'
		. fin_cadre_relief(true)
		
		. debut_cadre_relief('', true, '', _T('seo:alexa_id'), '', 'alexa_slide')
		. '<input type="text" name="alexa[id]" value="'.$config['alexa']['id'].'" id="alexa" class="formo"/>'
		. fin_cadre_relief(true)
				
		// Submit button
		. '<div style="text-align:right;"><input type="submit" name="alexa_submit" class="fondo" value="'._T('bouton_valider').'" /></div>'
		. '</form>'
		. fin_cadre_trait_couleur(true)
		;
			
	// JavaScript for fun //
	$page_result .= ''
		. '<script type="text/javascript">'
		. 'function activeForm(checkbox, form) {'
		. 	'(checkbox.attr(\'checked\')==true) ? form.slideDown()  : form.slideUp();'
		. '}'
		. (($config['meta_tags']['activate'] != 'yes') ? "$('.meta_tags_slide').hide();" : "")
		. (($config['webmaster_tools']['activate'] != 'yes') ? "$('.webmaster_tools_slide').hide();" : "")
		. (($config['analytics']['activate'] != 'yes') ? "$('.analytics_slide').hide();" : "")
		. (($config['alexa']['activate'] != 'yes') ? "$('.alexa_slide').hide();" : "")
		. '</script>'
		;
		
	echo $page_result, pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=> '')), fin_gauche(), fin_page();
}

?>
