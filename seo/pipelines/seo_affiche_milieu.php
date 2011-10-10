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

if (!defined("_ECRIRE_INC_VERSION")) return;

function seo_affiche_milieu($vars) {
	
	include_spip('inc/abstract_sql');
	include_spip('inc/autoriser');
	
	$config = unserialize($GLOBALS['meta']['seo']);
	
	// Rubrique
	if ( $vars["args"]["exec"] == 'naviguer' && $vars["args"]["id_rubrique"] != '') {
		$type_object = 'rubrique';
		$id_object   = $vars["args"]["id_rubrique"];
	// Article
	} elseif ( $vars["args"]["exec"] == 'articles' && $vars["args"]["id_article"] != '') {
		$type_object = 'article';
		$id_object   = $vars["args"]["id_article"];
	// Other case we quit
	} else {
		return $vars;
	}
	
	// If meta tags are activates
	if ($config['meta_tags']['activate'] != 'yes' || $config['meta_tags']['activate_editing'] != 'yes') {
		return $vars;
	}
	
	$result = sql_select("*", "spip_seo", "id_object = $id_object AND type_object = '$type_object'");
	while($r = sql_fetch($result)){
			$meta_tag[$r['meta_name']] = $r['meta_content'];
	}
	
	$bouton = bouton_block_depliable(_T('seo:meta_tags'), false, "SEO");
	$ret .= debut_block_depliable(false, "SEO");
	
	// List		
	$list = ''
		. '<input type="hidden" name="id_object" value="'. $id_object .'"/>'
		. '<input type="hidden" name="type_object" value="'. $type_object .'"/>'
		. '<div id="list_meta" class="cadre cadre-liste">'
		. '<table id="test" cellspacing="0" cellpadding="2" border="0" width="100%"><tbody>'
		. '<tr class="tr_liste">'
		. 	'<td class="arial11" style="padding-left:8px; width: 70px;">'._T('seo:meta_title').'</td>'
		. 	'<td style="padding-right:8px;"><input type="text" name="meta_tag[title]" value="'.$meta_tag['title'].'" style="width:100%;" /></td>'
		. '</tr>'
		. '<tr class="tr_liste">'
		. 	'<td class="arial11" style="padding-left:8px; width: 70px;">'._T('seo:meta_description').'</td>'
		. 	'<td style="padding-right:8px;"><textarea style="width:100%;" rows="5" name="meta_tag[description]" type="text">'.$meta_tag['description'].'</textarea></td>'
		. '</tr>'
		. '<tr class="tr_liste">'
		. 	'<td class="arial11" style="padding-left:8px; width: 70px;">'._T('seo:meta_keywords').'</td>'
		. 	'<td style="padding-right:8px;"><input type="text" name="meta_tag[keywords]" value="'.$meta_tag['keywords'].'" style="width:100%;" /></td>'
		. '</tr>'
		. '<tr class="tr_liste">'
		. 	'<td class="arial11" style="padding-left:8px; width: 70px;">'._T('seo:meta_copyright').'</td>'
		. 	'<td style="padding-right:8px;"><input type="text" name="meta_tag[copyright]" value="'.$meta_tag['copyright'].'" style="width:100%;" /></td>'
		. '</tr>'
		. '<tr class="tr_liste">'
		. 	'<td class="arial11" style="padding-left:8px; width: 70px;">'._T('seo:meta_author').'</td>'
		. 	'<td style="padding-right:8px;"><input type="text" name="meta_tag[author]" value="'.$meta_tag['author'].'" style="width:100%;" /></td>'
		. '</tr>'
		. '<tr class="tr_liste">'
		. 	'<td  class="arial11" style="padding-left:8px; width: 70px;">'._T('seo:meta_robots').'</td>'
		. 	'<td style="padding-right:8px;">'
		.		'<select name="meta_tag[robots]" style="width:100%;">'
		.			'<option '.(($meta_tag['robots'] == '') ? "selected" : '').' value=""></option>'
		.			'<option '.(($meta_tag['robots'] == 'INDEX, FOLLOW') ? "selected" : '').' value="INDEX, FOLLOW">INDEX, FOLLOW</option>'
		.			'<option '.(($meta_tag['robots'] == 'INDEX, NOFOLLOW') ? "selected" : '').' value="INDEX, NOFOLLOW">INDEX, NOFOLLOW</option>'
		.			'<option '.(($meta_tag['robots'] == 'NOINDEX, FOLLOW') ? "selected" : '').' value="NOINDEX, FOLLOW">NOINDEX, FOLLOW</option>'
		.			'<option '.(($meta_tag['robots'] == 'NOINDEX, NOFOLLOW') ? "selected" : '').' value="NOINDEX, NOFOLLOW">NOINDEX, NOFOLLOW</option>'
		.			'<option '.(($meta_tag['robots'] == 'INDEX, FOLLOW, NOARCHIVE') ? "selected" : '').' value="INDEX, FOLLOW, NOARCHIVE">INDEX, FOLLOW, NOARCHIVE</option>'
		.			'<option '.(($meta_tag['robots'] == 'INDEX, NOFOLLOW, NOARCHIVE') ? "selected" : '').' value="INDEX, NOFOLLOW, NOARCHIVE">INDEX, NOFOLLOW, NOARCHIVE</option>'
		.			'<option '.(($meta_tag['robots'] == 'NOINDEX, NOFOLLOW, NOARCHIVE') ? "selected" : '').' value="NOINDEX, NOFOLLOW, NOARCHIVE">NOINDEX, NOFOLLOW, NOARCHIVE</option>'
		.		'</select>'
		.	'</td>'
		. '</tr>'
		. '</tbody></table>'
		. '</div>'
		;

	$ret .= ajax_action_post('seo_save_meta', "", "", "", $list, _T('bouton_valider'), " class='fondo spip_xx-small' style='float:right;'");
	$ret .= '<div id="seo_save_meta-" style="float:right; margin-right:10px;"></div>'; // For Ajax
	$ret .= fin_block();
		 
	// Create the border with the content
	$ret = debut_cadre_enfonce(_DIR_PLUGIN_SEO.'img_pack/meta_tags-24.png', true, "", $bouton) . $ret . fin_cadre_enfonce(true);

	$vars["data"] .= $ret;
	
	return $vars;
}