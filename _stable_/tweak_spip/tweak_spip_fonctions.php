<?php
// fichier charge a chaque recalcul
	include_spip('tweak_spip');
	global $tweaks_metas_pipes;
tweak_log("mes_fonctions : strlen=".strlen($tweaks_metas_pipes['fonctions']));
	eval($tweaks_metas_pipes['fonctions']);
?>