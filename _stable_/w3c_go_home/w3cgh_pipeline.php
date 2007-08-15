<?php
	/* public static */
	function w3cgh_ajouter_boutons($boutons_admin) {
		if (version_compare($spip_version_code,'1.9260','<')){
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
				_T('w3cgh:titre_page')	// titre
				);
			}
		}
		return $boutons_admin;
	}
	function w3cgh_affiche_droite($flux){
		global $spip_lang_right;
		if ($flux['args']['exec']=='articles'){
			include_spip('inc/validateur_api');
			$s = "";
			$s .= debut_cadre_relief(_DIR_PLUGIN_W3CGH."images/xml-valid-24.png",true);
			$s .= _T('w3cgh:titre_conformite_page')."<br />";

			$id_article = $flux['args']['id_article'];
			$url = generer_url_public('w3cgh_article',"id_article=$id_article");
			if (defined('_DIR_PLUGIN_PHPMV'))
				$url = parametre_url($url,'var_nophpmv',1);
			$nom = 'spip_xhtml_validator';

			$last_mod = time();
			$res = spip_query("SELECT date_modif FROM spip_articles WHERE id_article="._q($id_article));
			if ($row = spip_fetch_array($res))
				$last_mod=strtotime($row['date_modif']);
			
			$res = validateur_test_valide($nom,$url,$last_mod);
			$url_affiche = generer_url_ecrire('w3cgh_affiche',"nom=$nom&url=".urlencode($url),true);
			$url_voir = generer_url_ecrire('w3cgh_voir',"nom=$nom&url=".urlencode($url),true);
			$url_test = generer_url_ecrire('w3cgh_test',"nom=$nom&url=".urlencode($url),true);
			if ($res){
				$s .= "<a href='$url_voir' id='w3cgh_test' onclick='return affiche_rapport(\"$url_voir\",\"t$id_test\")'>";
				$s .= "OK (".date('d-m-Y H:i',$res).")</a>";
			}
			else {
				$s .= "<a href='$url_voir' id='w3cgh_test' onclick='return affiche_rapport(\"$url_voir\",\"t$id_test\")'></a>";
				$s .= "<script type='text/javascript'>$('#w3cgh_test').append(ajax_image_searching).load('$url_test');</script>";
				// ajouter la methode img en noscript
			}
			$s .= "<div style='text-align:$spip_lang_right'>";
			$s .= "<a href='#' onclick=\"$('#w3cgh_test').prepend(ajax_image_searching).load('$url_test');return false;\">";
			$s .= _T("w3cgh:tester_page")."</a></div>";

			$s .= fin_cadre_relief(true);
			$flux['data'] .= $s;
		}
		return $flux;
	}
	
?>