<?php
	function aa_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton comme  sous-menu de "naviguer"
			$boutons_admin['naviguer']->sousmenu['cfg&cfg=aa']= new Bouton("plugin-24.gif", _T('Article accueil') );
		}
		return $boutons_admin;
	}

// inserer l'article aa dans la page d'accueil automatiquement si faire se peut
	function aa_affichage_final(&$page) {
		// ne pas se fatiguer si on est pas sur la page sommaire
		if (basename($GLOBALS['page']['source']) != "sommaire.html")
			return $page;

		// generer le fragment de page avec aa.html
		if (!function_exists('recuperer_fond'))
			include_spip('public/assembler');
		$aa = recuperer_fond('aa');

		// si il existe un div d'id = aa on insere l'article aa dedans
		// sinon on chope le debut de la liste des derniers articles et on insere l'article aa juste avant
		// ou si la liste n'existe pas on chope le début du bloc navigation
		preg_match(',<div [^>]*?id=[\'"]aa[\'"].*?>,i', $page, $res);
		if ($res)
			$page = substr_replace($page, $aa, (strpos($page, $res[0]) + strlen($res[0])), 0);
		else {
			preg_match(',<div [^>]*?class=[\'"]liste articles sommaire[\'"].*?>,i', $page, $res)	// cas du sommaire de Z
			|| preg_match(',<div [^>]*?class=[\'"]menu article[\'"].*?>,i', $page, $res) // cas du sommaire de la dist
			|| preg_match(',</div>[\s ]*?</div>\s*?<div [^>]*?id=[\'"]navigation[\'"].*?>,i', $page, $res) // cas du sommaire Z si pas de "derniers articles"
			|| preg_match(',</div><!--#contenu-->,i', $page, $res); // cas du sommaire de la dist si pas de "derniers ..." articles, breves,forums et sites
			
			if ($res)
				$page = substr_replace($page, $aa, (strpos($page, $res[0])), 0);
		}
		return $page;
	}
	
?>
