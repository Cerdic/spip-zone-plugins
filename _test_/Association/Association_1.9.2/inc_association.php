<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ASSOCIATION',(_DIR_PLUGINS.end($p)));

	function association_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		
		// on voit le bouton dans la barre "naviguer"		
		
		$boutons_admin['naviguer']->sousmenu['association']= new Bouton(
			"../"._DIR_PLUGIN_ASSOCIATION."/img_pack/annonce.gif",  // icone
			_T('asso:titre_menu_gestion_association') //titre
			);
		}
		return $boutons_admin;
	}

	function association_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}
	
	function association_header_prive($flux){
		$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('association.css')).'" />';
		return $flux;
	}
	
	function association_affiche_milieu($flux) {
		$exec =  $flux['args']['exec'];
		if ($exec=='auteur_infos'){
			include_spip('inc/association_adherents');
			$id_auteur = $flux['args']['id_auteur'];
			$flux['data'] .= association_adherents($id_auteur);
		}
		return $flux;
	}	

?>