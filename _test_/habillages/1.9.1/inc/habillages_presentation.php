<?php

function habillages_menu_navigation() {
	# Definition des menus afin de construire un menu de navigation.
	$menus = array(
	'squelettes' => 'squelettes',
	'themes' => 'themes',
	'extras' => 'extras',
	'logos' => 'logos',
	'icones' => 'icones',
	'aide' => 'aide'
	);
	
# Definition des variables pour la navigation dans le menu.
	foreach ($menus as $menu) {
		# Recuperer la deuxieme partie de exec.
		$exploser_exec = explode('_', _request('exec'));
		
		# On defini sur quelle page nous sommes et on en tire
		# la classe css, le logo grise ou non de la rubrique,
		# le commentaire, et le lien.
		if ($menu == $exploser_exec[1]) {
			$classe = "used";
			$logo = $menu.'_bw';
			$lien = _T('habillages:lien_'.$menu.'_off');
		}
		else {
			$classe = "bold_just";
			$logo = $menu;
			$lien = "<a href='".generer_url_ecrire('habillages_'.$menu.'')."'>"._T('habillages:lien_'.$menu.'_on')."</a>";
		}
		
		# Debut ouverture div.
		echo "<div class='";
		# Nom de la classe
		echo $classe;
		# Fin ouverture div.
		echo "'>";
		# logo rubrique
		echo '<img src="'._DIR_PLUGIN_HABILLAGES.'/../img_pack/habillages_'.$logo.'-22.png">';
		# Titre contenu
		echo _T('habillages:accueil_'.$menu.'')."<br />";
		# Contenu
		echo $lien;
		# Fermeture div
		echo "</div><br />";
	}
}

?>