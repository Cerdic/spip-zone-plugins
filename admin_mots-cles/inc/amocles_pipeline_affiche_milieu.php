<?php

	// inc/amocles_pipeline_affiche_milieu.php

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

function amocles_affiche_milieu ($flux) {

	if(
		isset($flux['args']['exec']) && ($flux['args']['exec'] == "mots_edit")
	) {
	
		include_spip("inc/amocles_api_globales");
		include_spip("inc/amocles_api_prive");
		
		if(in_array($GLOBALS['auteur_session']['id_auteur'], amocles_admins_groupes_mots_get_ids())) {
			
			if(amocles_mots_edit_inserer_milieu()) {
				
				$langues_array = split(',', $GLOBALS['meta']['langues_utilisees']);
				
				if(count($langues_array) > 1) {
				
					$traduire = charger_fonction('traduire', 'inc');
					 
					$result = "\n"
						.	"<!-- amocles bloc -->\n"
						.	"<div id='amocles-corps'>\n"
						.	"<div id='amocles-menu' class='verdana2 amocles-lang-menu'>\n"
						;
				
					foreach($langues_array as $langue) {
						$nom_langue = "title='".ucwords(traduire_nom_langue($langue))."'";
						$class = "class='".(($langue == $GLOBALS['spip_lang']) ? "lang-sel" : "")."'";
						$result .= ""
							. "<a href='#amocles-ventre' $nom_langue lang='$langue'>[$langue]</a>&nbsp;\n"
							;
					}
				
					$alt = _T(_AMOCLES_LANG."selectionnez_langue");
				
					$result .= ""
						.	"<img src='"._DIR_IMG_PACK."langues-12.gif' alt=\"".$alt."\" title=\"".$alt."\" />\n"
						.	"</div>\n"
						;
						
					$result .= ""
						.	"<!-- amocles boite edition -->\n"
						.	"<div id='amocles-ventre' style='display:none'>"
						.	debut_cadre_formulaire('',true)
						.	"<strong id='amocles-ventre-titre'></strong><br />\n"
						;
						
					// les titres pour la boite d'edition dans les differentes langues utilisees
					foreach($langues_array as $langue) {
						$titre = $traduire('info_texte_explicatif', $langue);
						$result .= "<input type='hidden' name='$langue' value=\"".$titre."\" />\n";
					}
					
					$result .= ""
						.	"<textarea id='amocles-ventre-texte' rows='8' class='forml' cols='40'>\n"
						.	"" // texte
						.	"</textarea><br />\n"
						.	fin_cadre_formulaire(true) 
						.	"</div>\n" // ventre
						.	"</div>\n" // corps
						.	"\n" 
						;

					$flux['data'] .= $result;
				}
			}
		}
	}

	return ($flux);
}

?>