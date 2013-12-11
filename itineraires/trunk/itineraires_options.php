<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS['itineraires_locomotions'] = array(
	'canoe' => _T('itineraire:locomotion_canoe'),
	'cheval' => _T('itineraire:locomotion_cheval'),
	'pied' => _T('itineraire:locomotion_pied'),
	'velo' => _T('itineraire:locomotion_velo'),
	'vtt' => _T('itineraire:locomotion_vtt'),
);

function itineraires_locomotions($type=null){
	$locomotions = $GLOBALS['itineraires_locomotions'];
	
	if ($type and is_string($type)){
		if (isset($locomotions[$type])){
			return $locomotions[$type];
		}
		else{
			return '';
		}
	}
	
	return $locomotions;
}
