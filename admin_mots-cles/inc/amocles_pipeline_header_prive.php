<?php

	// inc/amocles_pipeline_header_prive.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$
	
	/*****************************************************
	Copyright (C) 2007-2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiee par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Amocles est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

function amocles_header_prive ($flux) {

	include_spip("inc/actions");
	include_spip("inc/amocles_api_globales");
	
	$exec = _request('exec');
	
	$css_script = $js_script = "";
	
	$flag_autorise = 
		in_array($GLOBALS['auteur_session']['id_auteur'], amocles_admins_groupes_mots_get_ids())
		|| amocles_mots_edit_inserer_milieu()
		;
	
	if($flag_autorise 
		&& (in_array($exec, array("mots_edit", "amocles_configuration", "amocles_stopwords", "articles")))
	)
	{
		$css_script .= ""
			. "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/amocles_prive.css'))."' />\n"
			. "<!--[if IE]>\n"
			. "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/amocles_prive_ie.css'))."' />\n"
			. "<![endif]-->\n"
			;
		
		/* boite des mots dans l'édition des articles */
		if($exec == "articles")
		{
			$action = "amocles_boite_mots";
			$arg = $GLOBALS['connect_id_auteur'].","._request('id_article');
			$url = generer_action_auteur($action, $arg);
			// $.ajax n'aime pas &amp;
			$url = preg_replace("/&amp;/", "&", $url);
			
			$js_script .= "
<script type='text/javascript'>
//<![CDATA[
	var amocles_articles_boite_mots_url = \"" . $url . "\";
//]]>
</script>
"
			;
		}

		/* */
		$js_script .= ""
			. "<script src='".url_absolue(find_in_path('javascript/amocles_prive.js'))."' type='text/javascript'></script>\n"
			;
		
		if(!empty($css_script) || !empty($js_script)) 
		{
			$flux .= ""
				. "\n\n<!-- PLUGIN AMOCLES -->\n"
				. $css_script . $js_script
				. "<!-- / PLUGIN AMOCLES -->\n\n"
				;
		}
	}

	return ($flux);
}

?>