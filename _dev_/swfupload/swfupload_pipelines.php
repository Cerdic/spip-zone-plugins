<?
function swfupload_ajouterBoutons($boutons_admin) {
		// si on est admin ou admin restreint
		if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
		//AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu['swfupload_admin']= new Bouton(
			"../"._DIR_PLUGIN_SWFUPLOAD."/swfupload-24.png",  // icone
			_T('swfupload:SWFupload')	// titre
			);
		}
		return $boutons_admin;
	}
?>