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
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiLo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
// Insère les js et css dans head de l'espace public
function lilo_insert_head ($flux) {

	include_spip('inc/filtres');
	include_spip('inc/plugin_globales_lib');
	
	// masque les boutons admins standards
	$GLOBALS['flag_preserver'] = true;

	$page = _request('page');
	
	$config = __plugin_lire_key_in_serialized_meta('config', _LILO_META_PREFERENCES);

	if(!$config) $config = array();

	$lilo_values_array = unserialize(_LILO_DEFAULT_VALUES_ARRAY);

	$lilo_js_insert_head = "";
	
	foreach($lilo_values_array as $key => $value) {
		if(!isset($config[$key]) || !$config[$key] || empty($config[$key])) $config[$key] = $value;
		$lilo_js_insert_head .= "'".preg_replace(',(lilo_),','',$key)."':'".$config[$key]."',";
	}
	$lilo_js_insert_head = " jQuery().ready(function(){ var lilo_config = { " . rtrim($lilo_js_insert_head, ",") . " };";

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

		$lilo_js_insert_head .= 
		// Javascript
		"
			var alea_actuel = '', alea_futur = '';
			$('#var_login_id').blur(function(){
				if($(this).val().length) {
					$.ajax({
						type: 'post'
						, url: $('#lilo_url_action').val()
						, data: {var_login: $('#var_login_id').val(), url: $('input[@name=url]').val()}
						, success: function(data) {
							var result = data.split('" . _LILO_AJAX_RESULT_SEPARATOR . "');
							var id_auteur = result[0];
							alea_actuel = result[1];
							alea_futur = result[2];
							var logo_src = result[3];
							$('input[@name=session_password_md5]').val(alea_actuel);
							$('input[@name=next_session_password_md5]').val(alea_futur);
							/* change logo uniquement si OK et page login */
							if((login_voir_logo=='oui') && (logo_src.length)) {
								$('img.lilo-logo').attr({ src: logo_src, alt: 'Logo auteur' });
							}
							return true;
						}
						, error: function(xmlhttprequest, type, e) {
							if(lilo_config['login_identifiant_inconnu']) {
								alert(lilo_config['login_identifiant_inconnu']);
							}
						}
					}); /* end $.ajax */
				} /* end if */
			}); /* end blur */
			
			$('#lilo_login').submit( function() {
				if (
					(this.session_password.value.length > 0)
					&& (this.var_login.value.length > 0)
					) {
					this.session_password_md5.value = calcMD5(alea_actuel + this.session_password.value);
					this.next_session_password_md5.value = calcMD5(alea_futur + this.session_password.value);
					this.session_login_hidden.value = this.var_login.value;
					this.session_password.value = ''; 
					return(true);
					}
				return(false);
			}); /* end submit */
		"; // end $lilo_js_insert_head
		
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
		"; // end $lilo_css_insert_head

		$lilo_js_insert_head .= 
		// Javascript
		"
			$('#lilo-ventre').hide();
			$('#lilo-statut-public').hover(function(){
				$('#lilo-ventre').show('slow');
			 },function(){
				$('#lilo-ventre').hide('slow');
			});
		"; // end $lilo_js_insert_head
		
	} // end else
	
	$lilo_js_insert_head .= "});"; // fin de jQuery().ready(function(){
	
	$lilo_css_insert_head = lilo_envelopper_script(compacte_css($lilo_css_insert_head), 'css');
	$lilo_js_insert_head = lilo_envelopper_script(compacte_js($lilo_js_insert_head), 'js');
		
	// compacter un peu plus
	$lilo_js_insert_head = lilo_compacter_script($lilo_js_insert_head);
	$lilo_css_insert_head = lilo_compacter_script($lilo_css_insert_head);
	
	// inclure le résultat dans le head
	$flux .= "\n<!-- "._LILO_PREFIX." -->\n" . $lilo_css_insert_head . $lilo_js_insert_head . "<!-- /"._LILO_PREFIX." -->\n";

	return ($flux);
	
} // end lilo_insert_head()

function lilo_envelopper_script ($s, $type) {
	switch($type) {
		case 'css':
			$s = "
				<style type='text/css'>
				<!--
				" 
				. $s
				. "
				-->
				</style>
			";
			break;
		case 'js':
			$s = "
				<script type='text/javascript'>
				" 
				. $s
				. "
				</script>
			";
			break;
		default:
			$s = "\n\n<!-- erreur envelopper: type inconnu -->\n\n";
	}
	return($s);
}

// complément des deux 'compacte'. supprimer les espaces en trop.
function lilo_compacter_script ($s) {
	$s = preg_replace('=[[:space:]]+=', ' ', $s);
	return($s);
}

?>