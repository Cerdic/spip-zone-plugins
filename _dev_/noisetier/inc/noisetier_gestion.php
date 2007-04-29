<?php

function noisetier_gestion_zone ($zone) {
	global $theme_zones;

	if (isset($theme_zones[$zone]['insere_avant']))
		echo $theme_zones[$zone]['insere_avant'];

	debut_cadre_formulaire();

		echo "<b>$zone :</b> ".typo($theme_zones[$zone]['titre']);

	fin_cadre_formulaire();
	echo '<br />';

	if (isset($theme_zones[$zone]['insere_apres']))
		echo $theme_zones[$zone]['insere_apres'];


}

?>