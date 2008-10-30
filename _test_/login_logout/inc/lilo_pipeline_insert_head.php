<?php 

	// exec/lilo_pipeline_insert_head.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiLo.
	
	LiLo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiLo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiLo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiLo. 
	
	LiLo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiee par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	LiLo est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
// Insere les js et css dans head de l'espace public
function lilo_insert_head ($flux) {

	include_spip('inc/filtres');
	include_spip('inc/plugin_globales_lib');
	
	// masque les boutons admins standards
	$GLOBALS['flag_preserver'] = true;

	$page = _request('page');
	
	$config = __plugin_lire_key_in_serialized_meta('config', _LILO_META_PREFERENCES);

	$config_default = unserialize(_LILO_DEFAULT_VALUES_ARRAY);

	/*
	 * si plugin non configure', reprendre les valeurs par defaut
	 */
	foreach($config_default as $key => $value) {
		if(!isset($config[$key])) {
			$config[$key] = $value;
		}
	}
	
	if(!$config) $config = array();

	$lilo_values_array = unserialize(_LILO_DEFAULT_VALUES_ARRAY);

	$lilo_js_insert_head = "";

	if($page == 'login') {
	
		$lilo_css_insert_head = 
		// CSS
		"
			#lilo_login {
				font: normal 10px/normal 'Myriad Web Pro', 'Myriad Web', Verdana, Arial, Helvetica, sans-serif;
				color: #000;
				background: #fff;
				text-align: left;
				white-space: nowrap;
				display: block;
				border: none;
				position: static;
				margin: 0;
				padding: 0;
				height: auto;
				width: auto;
			}
			#lilo_login label, #lilo_login .forml {
				height: 1.4em;
				width: auto;
				display:block;
			}
			@media print {
				#lilo-statut-public { display:none; }
			}
		"; // end $lilo_css_insert_head
		
	} // end if($page == 'login')
	
	else {
	
		// si pas dans la page login, le css et js pour la boite statut
		
		$lilo_css_insert_head = 
		// CSS
		"
			#lilo-statut-public {
				font: normal 11px/normal 'Myriad Web Pro', 'Myriad Web', Verdana, Arial, Helvetica, sans-serif;
				color: #fff;
				background: #00f;
				height: auto;
				border: 1px solid black;
				z-index:1024;
				margin:0;
				padding:2px;
				text-align:left;
				display:table;
			}
			#lilo-statut-public .row {
				display:table-row; font-size:100%;
			}
			#lilo-statut-public * {
				margin:0; padding:0;
			}
			#lilo-statut-public #lilo-tete {
				width:26px; height:26px;
				margin-right:2px;
				display:block;
				float:left;
				height:100%;
				padding:1px;
			}
			#lilo-statut-public>#lilo-tete {
				display:table-cell;
				float:none;
			}
			#lilo-statut-public #lilo-tete img {
				width:24px; height:24px; display:block;
			}
			#lilo-statut-public #lilo-buste {
				display:block; height:24px; padding:0; line-height:1.4em; height: auto;
			}
			#lilo-statut-public>#lilo-buste {
				display:table-cell;
				padding-left:24px;
				font-size:90%;
			}
			#lilo-statut-public .lilo-nom {
				font-weight:700;
			}
			#lilo-statut-public .lilo-login {
				font-size:75%; text-align:center;
			}
			#lilo-statut-public .spip-admin-bloc-lilo {
			}
			#lilo-statut-public .spip-admin-boutons-lilo {
				white-space: nowrap;
			}
			#lilo-statut-public a {
				display:block;
				color: #ff0;
				text-decoration: underline;
				margin:3px 0 0 !important;
				line-height:1.4em;
			}
			@media print {
				#lilo-statut-public { display:none; }
			}
		"; // end $lilo_css_insert_head

		/*
		 * 
		 */
		if($config['lilo_statut_sans_animation'] == 'non') {
			$lilo_js_insert_head .= 
			// Javascript
			"
			jQuery().ready(function(){
				$('#lilo-ventre').hide();
				$('#lilo-statut-public').hover(function(){
					$('#lilo-ventre').show('slow');
				 },function(){
					$('#lilo-ventre').hide('slow');
				});
			});
			"; // end $lilo_js_insert_head
		}

	} // end else
	
	
	// compacte_css() compresse une chaine et renvoie le resultat compresse' (string)
	// compacte() compresse et place le resultat dans le cache 'local'

	if($config['lilo_statut_css_perso'] == 'non') {
		// prendre le css par defaut
		$lilo_css_insert_head = lilo_envelopper_script(lilo_compacter_script($lilo_css_insert_head, 'css'), 'css');
	}
	else {
		// rechercher le css personnalise'
		$lilo_css_insert_head = ""
			. "<link rel='stylesheet' type='text/css' href='" 
			. url_absolue(compacte(find_in_path('lilo_public.css'), 'css'))
			. "' />";
	}
	
	if(!empty($lilo_js_insert_head)) {
		$lilo_js_insert_head = lilo_envelopper_script(lilo_compacter_script($lilo_js_insert_head, 'js'), 'js');
	}
		

	// inclure le resultat dans le head
	$flux .= "\n\n<!-- "._LILO_PREFIX." -->\n" 
		. $lilo_css_insert_head 
		. $lilo_js_insert_head 
		. "\n<!-- /"._LILO_PREFIX." -->\n";

	return ($flux);
	
} // end lilo_insert_head()


/*
 * 
 */
function lilo_envelopper_script ($source, $format) {
	$source = trim($source);
	if(!empty($source)) {
		switch($format) {
			case 'css':
				$source = "<style type='text/css'>\n<!--\n" 
					. $source
					. "\n-->\n</style>";
				break;
			case 'js':
				$source = "\n<script type='text/javascript'>\n//<![CDATA[\n" 
					. $source
					. "\n//]]>\n</script>";
				break;
			default:
				$source = "\n\n<!-- erreur envelopper: format inconnu [$format] -->\n\n";
		}
	}
	return($source);
} // end lilo_envelopper_script()

/*
 * complement des deux 'compacte'. supprimer les espaces en trop.
 */ 
function lilo_compacter_script ($source, $format) {
	$source = trim($source);
	if(!empty($source)) {
		$source = compacte($source, $format);
		$source = preg_replace(",/\*.*\*/,Ums","",$source); // pas de commentaires
		$source = preg_replace('=[[:space:]]+=', ' ', $source); // réduire les espaces
		$source = trim($source);
	}
	return($source);
} // end lilo_compacter_script()

?>