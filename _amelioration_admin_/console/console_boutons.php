<?php

/**
 * definition du plugin "console" version "classe statique"
 * utilisee comme espace de nommage
 */
define('_DIR_PLUGIN_CONSOLE',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));
 

	/* static public */

	/* public static */
	function Console_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['configuration']->sousmenu['console']= new Bouton(
			"../"._DIR_PLUGIN_CONSOLE."/img_pack/console.png",  // icone
			_L('Console')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function Console_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}

function Console_body_prive($flux){
	global $connect_statut;
	global $connect_id_auteur;
	global $connect_toutes_rubriques;
	
	if (isset($GLOBALS['meta']['console'])&& $connect_statut == "0minirezo" && $connect_toutes_rubriques) {
		$liste_auteur_console_active = array();
		$liste_auteur_console_active = unserialize($GLOBALS['meta']['console']);
		$console_active = in_array($connect_id_auteur,$liste_auteur_console_active);
		if ($console_active){
			
			$urlspiplog = urlencode(generer_url_ecrire('spiplog','logfile=spip',true));
			$urlsqllog = urlencode(generer_url_ecrire('spiplog','logfile=mysql',true));
			$flash = find_in_path('console.swf');
			$flux.="
			<object type='application/x-shockwave-flash' 
			id='console'
			data='$flash?spiplog=$urlspiplog&sqllog=$urlsqllog' width='300' height='600' style='position:absolute;left:0;bottom:0;'>
				<param name='movie' value='$flash?spiplog=$urlspiplog&sqllog=$urlsqllog' />
				<param name='wmode' value='transparent' />
			</object>	";
		}	
	}
	return $flux;
}
?>