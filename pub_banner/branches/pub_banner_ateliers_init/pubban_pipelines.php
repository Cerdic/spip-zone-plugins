<?php
/**
 * @name 		Pipelines
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajout d'une tache CRON
 * Appel de {@link genie_pubban_cron} une fois par jour
 */
function pubban_taches_generales_cron($taches_generales){
	$taches_generales['pubban_cron'] = 60 * 60 * 24;
	return $taches_generales;
}

function pubban_jquery_plugins($scripts){
	$scripts[] = "javascripts/jquery.bgiframe.js";
	$scripts[] = "javascripts/jquery.dimensions.js";
	$scripts[] = "javascripts/jquery.tooltip.js";
	return $scripts;
}

/**
 * Header des pages exec
 */
function pubban_header_prive($texte) {
	$texte .= "<link rel='stylesheet' type='text/css' href='".find_in_path("javascripts/jquery.tooltip.css")."' media='all' />"
		."<script type=\"text/javascript\"><!--
$(document).ready(function(){
	$('.pubban_tltp').tooltip({ track: true, showURL: false, delay: 0, left: -30, extraClass: 'pubban' });
});
//--></script>";
	return $texte;
}

?>