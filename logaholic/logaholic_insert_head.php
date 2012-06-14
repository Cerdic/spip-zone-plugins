<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Dans SPIP 3 on utilise insert_head_css qui est safe
 * et on insere avant les CSS, pour ne pas bloquer celles-ci
 * (qui sont bloquees par du js inline)
 *
 * @param $flux
 * @return string
 */
function logaholic_insert_head_css($flux){
	return logaholic_snippet().$flux;
}

/**
 * Dans SPIP 2 on utilise insert_head et on ajoute a la fin
 * simplement
 *
 * @param $flux
 * @return string
 */
function logaholic_insert_head($flux){
	return $flux . logaholic_snippet();
}

/**
 * Morceau de code a inserer dans la page pour traquer avec GA
 * @return string
 */
function logaholic_snippet(){
	include_spip('inc/config');
	$id_logaholic = lire_config('logaholic/id_logaholic');
	$lwa_server = lire_config('logaholic/lwa_server');
	if ($id_logaholic
	  AND $id_logaholic !== '_'
	  AND (strncmp($id_logaholic,"xxx",6)!=0)) {
		return '
<!-- /* Logaholic Web Analytics Code */ -->
<script type="text/javascript">
var lwa_id = "LWA_p'.$id_logaholic.'";
if (document.location.protocol=="https:") { var ptcl = "https:" } else { var ptcl = "http:" } 
var lwa_server = ptcl + "//'.$lwa_server.'//";    
document.write(unescape("%3Cscript type="text/javascript" src="" + lwa_server + "lwa.js"%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var lwa = trackPage();
</script>
<noscript><a href="http://'.$lwa_server.'//logaholictracker.php?conf=LWA_p'.$id_logaholic.'"><img src="http://'.$lwa_server.'//logaholictracker.php?conf=LWA_p'.$id_logaholic.'" alt="web stats" border="0" /></a> <a href="http://www.logaholic.com/">Web Analytics</a> by Logaholic</noscript>'."\n";

	}
	return "";
}
?>
