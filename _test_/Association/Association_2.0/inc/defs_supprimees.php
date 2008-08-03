<?php
function debut_raccourcis(){
	
	global $spip_display;
		
	if ($spip_display != 4) echo "</font>";
	else echo "</ul>";
		
	echo fin_cadre_enfonce(true);
};
function fin_raccourcis(){
	global $spip_display;
	
	if ($spip_display != 4) echo "</font>";
	else echo "</ul>";
	
	echo fin_cadre_enfonce(true);
}
?>