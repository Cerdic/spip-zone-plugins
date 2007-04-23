<?php


	function attributs_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu['attributs']= new Bouton(
			"../"._DIR_PLUGIN_ATTRIBUTS."/img_pack/attribut-24.png",  // icone
			_T('attributs:attributs')	// titre
			);
		}
		return $boutons_admin;
	}

	function attributs_affiche_milieu($flux){
		switch($flux['args']['exec']) {
			case 'articles':
				include_spip('inc/attributs_gestion');
				$id_article = $flux['args']['id_article'];
				$nouv_attribut = _request('nouv_attribut');
				$supp_attribut = _request('supp_attribut');
				// le formulaire qu'on ajoute
				$flux['data'] .= attributs_formulaire('articles', $id_article, $nouv_attribut, $supp_attribut, autoriser('modifier','article',$id_article), generer_url_ecrire('articles',"id_article=$id_article"));
				break;
			case 'naviguer':
				include_spip('inc/attributs_gestion');
				$id_rubrique = $flux['args']['id_rubrique'];
				$nouv_attribut = _request('nouv_attribut');
				$supp_attribut = _request('supp_attribut');
				// le formulaire qu'on ajoute
				if ($id_rubrique)
					$flux['data'] .= attributs_formulaire('rubriques', $id_rubrique, $nouv_attribut, $supp_attribut, autoriser('modifier','rubrique',$id_auteur), generer_url_ecrire('naviguer',"id_rubrique=$id_rubrique"));
				break;
				case 'breves_voir':
				include_spip('inc/attributs_gestion');
				$id_breve = $flux['args']['id_breve'];
				$nouv_attribut = _request('nouv_attribut');
				$supp_attribut = _request('supp_attribut');
				// le formulaire qu'on ajoute
				$flux['data'] .= attributs_formulaire('breves', $id_breve, $nouv_attribut, $supp_attribut, autoriser('modifier','breve',$id_breve), generer_url_ecrire('breves_voir',"id_breve=$id_breve"));
				break;
			case 'auteurs_edit':
			case 'auteur_infos':
				global $connect_statut;
				include_spip('inc/attributs_gestion');
				$id_auteur = $flux['args']['id_auteur'];
				$nouv_attribut = _request('nouv_attribut');
				$supp_attribut = _request('supp_attribut');
				// le formulaire qu'on ajoute
				$flux['data'] .= attributs_formulaire('auteurs', $id_auteur, $nouv_attribut, $supp_attribut, autoriser('modifier','auteur',$id_auteur)&&$connect_statut=='0minirezo', generer_url_ecrire('auteurs_edit',"id_auteur=$id_auteur"));
				break;
			case 'sites':
				include_spip('inc/attributs_gestion');
				$id_syndic = $flux['args']['id_syndic'];
				$nouv_attribut = _request('nouv_attribut');
				$supp_attribut = _request('supp_attribut');
				global $id_rubrique;
				// le formulaire qu'on ajoute
				$flux['data'] .= attributs_formulaire('syndic', $id_syndic, $nouv_attribut, $supp_attribut, autoriser('publierdans','rubrique',$id_rubrique), generer_url_ecrire('sites',"id_syndic=$id_syndic"));
				break;
			default:
				break;
		}

		return $flux;
	}




?>