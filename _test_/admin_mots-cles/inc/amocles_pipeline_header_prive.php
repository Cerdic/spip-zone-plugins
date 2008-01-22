<?php

	// inc/amocles_pipeline_header_prive.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$
	
	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
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
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	Amocles est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function amocles_header_prive ($flux) {

	include_spip("inc/amocles_api");
	
	$exec = _request('exec');
	
	if (
		$exec == "mots_edit"
		&& in_array($GLOBALS['auteur_session']['id_auteur'], amocles_admins_groupes_mots_get_ids())
		&& amocles_mots_edit_inserer_milieu()
		) {
	  
	  $css_script = ""
		  . "
<!-- amocles header_prive -->
<style type='text/css'>
#amocles-menu {
	margin-bottom:0.5em;
	text-align:right;
}
.lang-sel { 
	color:black;
	font-weight:700;
}
#amocles-ventre {
	margin-bottom:1em;
}
#amocles-ventre-texte { 
}
</style>
		"
		;
		
	  $js_script = ""
		. "
$(document).ready(function(){
	var amocles_lang = false
	, amocles_spip_textarea = '#page>table>tbody>tr>td.serif>div.cadre-formulaire>form>div>div.serif>textarea.forml:eq(1)'
	, amocles_values
	;
	amocles_values = new Array();
	
	$('#amocles-menu a').click( function() { 
		$('#amocles-menu a').removeClass('lang-sel');
		$(this).addClass('lang-sel');
		amocles_lang = $(this).attr('lang');
		
		var ori, ii = $('#amocles-ventre input[@type=hidden][@name=' + amocles_lang + ']').val();
		
		$('#amocles-ventre-titre').html(ii);
		$('#amocles-ventre').show();
		
		ori = ii = $.trim($(amocles_spip_textarea).text());
		ii = ii.replace(/\s+/gm, ' ');
		
		/* valide l'entrée. Si multi, explode, sinon, place le texte
			dans la valeur de langue courante
		*/
		ii = ii.match(/^<multi>(.*?)<\/multi>$/);
		if(ii) {
			ii = ii[1];
			ii = ii.split('[');
			var z, m, r, x;
			for(z = 0, m = ii.length; z < m; z++)  {
				r = $.trim(ii[z]);
				if(r && r.length > 0) {
					x = r.substr(0,2);
					amocles_values[x] = $.trim(r.substr(3));
				}
			}
		}
		else {
			amocles_values[amocles_lang] = ori;
		}
		
		if(amocles_values[amocles_lang]) {
			$('#amocles-ventre-texte').val(amocles_values[amocles_lang]);
		}
	});
	
	$('#amocles-ventre-texte').change( function() { 
		amocles_values[amocles_lang] = this.value;
		var ii = '<multi>';
		for (var cle in amocles_values) {
      	ii += '[' + cle + ']' + amocles_values[cle];
		}
		ii += '</multi>';
		$(amocles_spip_textarea).text(ii);
	});

});
		";

	  //$script = preg_replace('=[[:space:]]+=', ' ', $script);
	  
	  $css_script = compacte_css($css_script);
	  $js_script = "\n"
	  . "<script type='text/JavaScript'>\n"
	  . "//<![CDATA[\n"
	  . compacte_js($js_script)
	  . "\n"
	  . "//]]>\n</script>\n"
	  ;
	  
	  $flux .= $css_script . $js_script;
	}

//
	return ($flux);
}

?>