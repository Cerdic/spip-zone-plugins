<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!function_exists('syndication_en_erreur')){
		// filtre |syndication_en_erreur
	function syndication_en_erreur($statut_syndication) {
		if ($statut_syndication == 'off'
		OR $statut_syndication == 'sus')
			return _T('sedna:probleme_de_syndication');
	}
}
	
?>