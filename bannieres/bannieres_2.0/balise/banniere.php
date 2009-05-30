<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

	include_spip('public/assembler');
	define('_DIR_IMG', $f.'IMG/');

	// Balise independante du contexte ici
	function balise_BANNIERE ($p) {
		return calculer_balise_dynamique($p, 'BANNIERE', array());
	}
 
	// Balise de traitement des donn�es 
	function balise_BANNIERE_dyn() {
		$query=spip_query("SELECT * FROM spip_bannieres WHERE debut<=CURRENT_DATE() AND fin>=CURRENT_DATE() ORDER BY RAND() LIMIT 0,1");
		while ($data=spip_fetch_array($query)) {
			echo "<div>";
			echo "<a href='".generer_url_action('visit_url','ban='.$data['id_banniere'])."' title='".$data['alt']."' ><img src='"._DIR_IMG."ban_".$data['id_banniere'].".".$data['ext']."'></a>";
			echo "</div>";
		}
 	}

?>