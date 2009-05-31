<?php

function vhosts_ajouterOnglets($flux) {
	global $connect_statut, $connect_toutes_rubriques;
	if($connect_statut == '0minirezo' AND $connect_toutes_rubriques)
		if($flux['args']=='configuration')
			$flux['data']['vhosts']= new Bouton("", 
				'Gestion des virtual hosts',
				generer_url_ecrire("vhosts_config"));
	return $flux;
}

?>
