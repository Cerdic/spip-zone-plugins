<?php 
	
	// inc/barrac_pipeline_header_prive.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of BarrAc.
	
	BarrAc is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	BarrAc is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with BarrAc; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de BarrAc. 
	
	BarrAc est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	BarrAc est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
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
// Ajoute l'appel aux feuilles de style dans le header privé
function barrac_header_prive ($flux) {

	global $connect_statut
		, $connect_toutes_rubriques
		;

	$exec = _request('exec');
	
	if(
		($exec == 'barrac_configuration')
		&& ($connect_statut == '0minirezo')
		&& $connect_toutes_rubriques
		) {
		$flux .= "
<!-- Barrac CSS -->
<link href='"._DIR_PLUGIN_BARRAC."barrac_prive.css' rel='stylesheet' type='text/css' media='screen' />
<!--[if lte IE 7]>
<link href='"._DIR_PLUGIN_BARRAC."barrac_prive_ie.css' rel='stylesheet' type='text/css' media='screen' />
<![endif]-->
<!-- end Barrac CSS -->

<!-- Barrac JS -->
<script language='JavaScript' type='text/JavaScript'>
<!--
	
	$(document).ready(function(){

		if($('#id_pointer').attr('checked')) {	$('#bloc_barrac_pointer').show(); } else { $('#bloc_barrac_pointer').hide(); }
		if($('#id_grossir').attr('checked')) { $('#bloc_barrac_grossir').show(); } else { $('#bloc_barrac_grossir').hide(); }
		if($('#id_espacer').attr('checked')) { $('#bloc_barrac_espacer').show(); } else { $('#bloc_barrac_espacer').hide(); }
		if($('#id_encadrer').attr('checked')) { $('#bloc_barrac_encadrer').show(); } else { $('#bloc_barrac_encadrer').hide(); }
		if($('#id_inverser').attr('checked')) { $('#bloc_barrac_inverser').show();	} else { $('#bloc_barrac_inverser').hide(); }

		$('#id_pointer').click(function(){
			if($('#id_pointer').attr('checked')) $('#bloc_barrac_pointer').show();
			else $('#bloc_barrac_pointer').hide();
		});
		$('#id_grossir').click(function(){
			if($('#id_grossir').attr('checked')) $('#bloc_barrac_grossir').show();
			else $('#bloc_barrac_grossir').hide();
			configure_switch();
		});
		$('#id_espacer').click(function(){
			if($('#id_espacer').attr('checked')) $('#bloc_barrac_espacer').show();
			else $('#bloc_barrac_espacer').hide();
			configure_switch();
		});
		$('#id_encadrer').click(function(){
			if($('#id_encadrer').attr('checked')) $('#bloc_barrac_encadrer').show();
			else $('#bloc_barrac_encadrer').hide();
			configure_switch();
		});
		$('#id_inverser').click(function(){
			if($('#id_inverser').attr('checked')) $('#bloc_barrac_inverser').show();
			else $('#bloc_barrac_inverser').hide();
			configure_switch();
		});
		
		function configure_switch() {
			if( 
				!$('#id_pointer').attr('checked')
				&& !$('#id_grossir').attr('checked') && !$('#id_espacer').attr('checked') 
				&& !$('#id_encadrer').attr('checked') && !$('#id_inverser').attr('checked')) {
				$('#fieldset_configurer_position_boutons').hide();
				$('#fieldset_configurer_presentation_boutons').hide();
				$('#fieldset_configurer_espace_taille_boutons').hide();
			}
			else {
				$('#fieldset_configurer_position_boutons').show();
				$('#fieldset_configurer_presentation_boutons').show();
				$('#fieldset_configurer_espace_taille_boutons').show();
			}
		}
		
		configure_switch();
		
		$('input[@name=barrac_grossir_cssfile]').bind('focus', function(){
			$('input[@name=barrac_grossir_global]:eq(1)').click();
		});
		$('input[@name=barrac_espacer_cssfile]').bind('focus', function(){
			$('input[@name=barrac_espacer_global]:eq(1)').click();
		});
		$('input[@name=barrac_encadrer_cssfile]').bind('focus', function(){
			$('input[@name=barrac_encadrer_global]:eq(1)').click();
		});
		$('input[@name=barrac_inverser_cssfile]').bind('focus', function(){
			$('input[@name=barrac_inverser_global]:eq(1)').click();
		});
		
		$('#grossir_taille').bind('focus', function(){
			$('input[@name=barrac_grossir_global]:eq(0)').click();
		});
		
	});
	
//-->
</script>		
<!-- end Barrac JS -->
		"
		. "\n"
		;
	}

	return ($flux);
}

?>