<?php
	/* public static */
	function w3cgh_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
			if (!defined(_DIR_PLUGIN_W3CGH))
				$icone = find_in_path('/images/w3cgh-icone.gif');
			else
				$icone = _DIR_PLUGIN_W3CGH."/images/w3cgh-icone.gif";
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['configuration']->sousmenu['w3c_go_home']= new Bouton(
			$icone,  // icone
			_L('Conformit&eacute;')	// titre
			);
		}
		return $boutons_admin;
	}
	function w3cgh_affiche_droite($flux){
		if ($flux['args']['exec']=='articles'){
			include_spip('inc/validateur_api');
			$s = "";
			$s .= debut_cadre_relief(_DIR_PLUGIN_W3CGH."images/w3cgh-icone.gif",true);

			$id_article = $flux['args']['id_article'];
			$url = generer_url_public('w3cgh_article',"id_article=$id_article");
			$nom = 'spip_xhtml_validator';

			$last_mod = time();
			$res = spip_query("SELECT date_modif FROM spip_articles WHERE id_article="._q($id_article));
			if ($row = spip_fetch_array($res))
				$last_mod=strtotime($row['date_modif']);
			
			$res = validateur_test_valide($nom,$url,$last_mod);
			$url_affiche = generer_url_ecrire('w3cgh_affiche',"nom=$nom&url=".urlencode($url),true);
			$url_voir = generer_url_ecrire('w3cgh_voir',"nom=$nom&url=".urlencode($url),true);
			if ($res){
				$s .= "<a href='$url_voir' id='t$id_test' onclick='return affiche_rapport(\"$url_voir\",\"t$id_test\")'>";
				$s .= "OK (".date('d-m-Y H:i',$res).")</a>";
			}
			else {
				$url_test = generer_url_ecrire('w3cgh_test',"nom=$nom&url=".urlencode($url),true);
				$s .= "<a href='$url_voir' id='w3cgh_test' onclick='return affiche_rapport(\"$url_voir\",\"t$id_test\")' rel='$url_test' class='test'></a>";
				$s .= "<script type='text/javascript'>$('#w3cgh_test').append(ajax_image_searching).load('$url_test');</script>";
				// ajouter la methode img en noscript
			}

			$s .= fin_cadre_relief(true);
			$flux['data'] .= $s;
		}
		return $flux;
	}
	
?>