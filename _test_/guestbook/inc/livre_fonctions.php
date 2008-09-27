<?php
/**
	 * Livre d'or
	 *
	 * Copyright (c) 2008
	 * Bernard Blazin  http://www.libertyweb.info & Yohann Prigent (potter64)
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/

function livre_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "forum"
	  $boutons_admin['forum']->sousmenu['livre']= new Bouton(
		"../"._DIR_PLUGIN_LIVRE."/img_pack/livredor.png",  // icone
		'Livre d&acute;or'	// titre
		);
	}
	return $boutons_admin;
}

function livre_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

	
?>