<?php
// fichier charge a chaque hit
	include_spip('tweak_spip');
	global $tweaks_metas_pipes;
tweak_log("mes_options : strlen=".strlen($tweaks_metas_pipes['options']));
	eval($tweaks_metas_pipes['options']);
?>