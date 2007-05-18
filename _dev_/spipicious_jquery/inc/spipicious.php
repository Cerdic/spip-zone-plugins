<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_PREG_FORMARTICLE', ',form_spipicious_ajax\b[^<>\'"]+\b((\w+)-(\w+)-(\d+))\b,');

function spipiciouscfgform() {
	$prepare_spipiciouscfg = function_exists('formulaire_article_config') ? formulaire_article_config() : array();
	$spipiciouscfg = array();
	foreach (array('msgNoChange' => false, 'msgAbandon' => true)
				as $prepare_spipiciouscfgi => $def) {
		if (isset($prepare_spipiciouscfg[$prepare_wdgcfgi])) {
			$spipiciouscfg[$prepare_wdgcfgi] = $prepare_spipiciouscfg[$prepare_spipiciouscfgi];
		} else {
			$spipiciouscfg[$prepare_spipiciouscfgi] = $def;
		}
	}
	return $spipiciouscfg;
}
?>