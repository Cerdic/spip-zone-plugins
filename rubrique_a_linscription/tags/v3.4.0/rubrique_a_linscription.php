<?php

function rubrique_a_linscription_formulaire_charger($flux){
	$explicite = lire_config('rubrique_a_linscription/formulaire_explicite');
	$statut = lire_config('rubrique_a_linscription/statut');
	$prive_voir = lire_config('rubrique_a_linscription/espace_prive_voir');

	// Ne pas modifier le flux si on avait un vrai false, c'est à dire que le formulaire ne doit pas être affiché
	if ($flux['data'] === false) {
		return $flux;
	}

	if (
			($flux['args']['form'] == 'inscription' 
			or $flux['args']['form'] == 'inscription_avec_rubrique') 
	    and lire_config('accepter_inscriptions') == 'oui' 
	    and (!$explicite) 
			or (
					$explicite == 'on' 
					and $flux['args']['form'] == 'inscription_avec_rubrique'
				)
	   ){
			 $flux['args']['args'][0] = $statut; 
			 $flux['data']['_commentaire'] = _T('rubrique_a_linscription:rubrique_reserve_'.$statut.'_'.$prive_voir);
	}
	return $flux;
}


function rubrique_a_linscription_formulaire_verifier($flux){
	$priver_creer = lire_config('rubrique_a_linscription/espace_prive_creer');

	if ($flux['args']['form'] == 'editer_article' 
			and $prive_creer == 'on') {

				$id_rubrique =_request('id_parent');
				settype($id_rubrique,"string");

				if (!autoriser('creerarticledans','rubrique',$id_rubrique)) {
					$flux['data']['erreurs']['id_parent'] = _T('rubrique_a_linscription:pas_autoriser_rubriquer_creerarticledans');
				}
			}
	return $flux;
}


function rubrique_a_linscription_formulaire_traiter($flux){
	$explicite = lire_config('rubrique_a_linscription/formulaire_explicite');
	$statut = lire_config('rubrique_a_linscription/statut');
	$groupe_mots = lire_config('rubrique_a_linscription/groupe_mots');
	$mail_public = lire_config('rubrique_a_linscription/mail_public');
	$mail_prive = lire_config('rubrique_a_linscription/mail_prive');

	//pour l'id_parent, c'est spécial car on utilise un picker
	$id_parent = picker_selected(lire_config('rubrique_a_linscription/rubrique_mere'),'rubrique');
	$id_parent = $id_parent[0];

	// cas particulier si duplicator
	if (test_plugin_actif('duplicator')) {
		$duplicator =  picker_selected(lire_config('rubrique_a_linscription/duplicator'),'rubrique');
		$duplicator = $duplicator[0];
		$duplicator_arbo = lire_config('rubrique_a_linscription/duplicator_arbo');
		if ($duplicator_arbo == 'arbo') {
			$duplicator_arbo = false;
		} else {
			$duplicator_arbo = true;
		}	
	}

	if (
			($flux['args']['form'] == 'inscription' 
			or $flux['args']['form'] == 'inscription_avec_rubrique') 
	    and (!$explicite) 
			or (
					$explicite == 'on' 
					and $flux['args']['form'] == 'inscription_avec_rubrique')
	   ){

				// Récuperation des paramètres
				$mail = _request('mail_inscription');
				$nom_inscription = str_replace('@',' (chez) ',_request('nom_inscription'));
				include_spip('base/abstract_sql');
				$id_auteur = sql_getfetsel('id_auteur','spip_auteurs','email='.sql_quote($mail));
		
		
				//Modification du statut temporaire
				sql_updateq('spip_auteurs',array('prefs'=>$statut),'id_auteur='.$id_auteur); 


				// Utiliser comme rubrique mere celle qui est passé explicitement au formulaire ou celle de la config ?
				if (isset ($flux["args"]["args"][0])) {
					$reqtest = sql_select('id_rubrique','spip_rubriques',"id_rubrique=".$flux["args"]["args"][0]);
					if ($reqtest) {
						$id_parent =  $flux["args"]["args"][0];
					}		
				}

				// Création de la rubrique
				include_spip('inc/rubriques');

				if ($duplicator) {
					include_spip('inc/duplicator');
					$titre_rubrique = _T('rubrique_a_linscription:titre_rubrique_duplicator',array('nom'=>$nom_inscription));
					$id_rubrique = dupliquer_rubrique($duplicator,$id_parent,$titre_rubrique,$duplicator_arbo);
					
					$calcul_branche_in = charger_fonction('calcul_branche_in', 'inc');
					$rubriques_filles = $calcul_branche_in($id_rubrique);
					if (lire_config("rubrique_a_linscription/duplicator_modif_auteur")) {
						$articles = sql_select('id_article','spip_articles',
							sql_in('id_rubrique',$rubriques_filles)
						);

						include_spip('action/editer_liens.php');
						while ($row = sql_fetch($articles)) {
							$id_article = $row['id_article'];
							objet_dissocier(
								array('auteur' => '*'),
								array('article' => $id_article)
							);
							objet_associer (
								array('auteur' => $id_auteur),
								array('article' => $id_article)
							);

						}
					}
				} else { // pas de duplicator : on se contente de créer la rubrique
					$titre_rubrique = _T('rubrique_a_linscription:titre_rubrique',array('nom'=>$nom_inscription));
					$id_rubrique = creer_rubrique_nommee($titre_rubrique, $id_parent);
	
				}
		
				sql_insertq('spip_auteurs_liens', array(
					'id_auteur' => $id_auteur,
					'objet'		=>'rubrique',
					'vu'		=>'non',
					'id_objet' => $id_rubrique)
				);

				spip_log('Création de la rubrique '.$id_rubrique.' pour l\'auteur '.$nom_inscription.' ( '.$mail.' )','rubrique_a_linscription');
		
				//On ajoute la rubrique chez l'auteur
				sql_update('spip_auteurs',array('rubrique_a_linscription'=>$id_rubrique),"id_auteur=$id_auteur");

				//Création du mot clef associé
				if($groupe_mots) {
					$type = sql_getfetsel('titre','spip_groupes_mots','id_groupe='.$groupe_mots);
					if ($type) {
						$id_mot = sql_insertq('spip_mots',array(
							'id_groupe' => $groupe_mots,
							'type' => $type,
							'titre' => _T('rubrique_a_linscription:mot_clef_de',array('nom'=>$nom_inscription))
							)
						);
						spip_log("Création du mot clef dans le groupe $type pour l'auteur $nom_inscription (id mot = $id_mot)",'rubrique_a_linscription');
			}
		}
		
				//Envoyer mails
				
				if ($mail_public or $mail_prive){
					
					$envoyer_mail = charger_fonction('envoyer_mail','inc');
					
					$corps = _T('rubrique_a_linscription:mail_adresse_rubrique');
					include_spip('inc/utils');
					
					if ($mail_public) {
						$corps .= "-".url_absolue(generer_url_public("rubrique","id_rubrique=$id_rubrique"))."\n";
					}
					
					if ($mail_prive) {
						$corps .= "-".generer_url_ecrire("rubrique","id_rubrique=$id_rubrique")."\n";
					}
					include_spip('inc/filtres');

					$titre = 	'['.extraire_multi(lire_config('nom_site')).']'. _T('rubrique_a_linscription:titre_mail_adresse_rubrique');
					$envoyer_mail(
						$mail,
						$titre,
						$corps);
				}
	}
	return $flux;	
}

