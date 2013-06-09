<?php
/*charger*/
function rubrique_a_linscription_formulaire_charger($flux){
	$meta = unserialize(lire_meta('rubrique_a_linscription'));
	if (
	    ($flux['args']['form']=='inscription' or $flux['args']['form']=='inscription_avec_rubrique') 
	    and lire_meta('accepter_inscriptions')=='oui' 
	    and (!$meta['formulaire_explicite']) 
	    or ($meta['formulaire_explicite']=='on' and $flux['args']['form']=='inscription_avec_rubrique')
	   ){
		
		
		$flux['args']['args'][0] = $meta['statut'];
		$flux['data']['_commentaire'] = _T('rubrique_a_linscription:rubrique_reserve_'.$meta['statut'].'_'.$meta['espace_prive_voir']);
		
	}
	return $flux;
}

function rubrique_a_linscription_formulaire_verifier($flux){
	$meta = unserialize(lire_meta('rubrique_a_linscription'));
	if ($flux['args']['form']=='editer_article' and $meta['espace_prive_creer']=='on') {
		$id_rubrique =_request('id_parent');
		settype($id_rubrique,"string");
		if (! autoriser('creerarticledans','rubrique',$id_rubrique)){
			$flux['data']['erreurs']['id_parent'] = _T('rubrique_a_linscription:pas_autoriser_rubriquer_creerarticledans');
		}
	}
	return $flux;
}


/* Traiter */
function rubrique_a_linscription_formulaire_traiter($flux){
	$meta = unserialize(lire_meta('rubrique_a_linscription'));
	if (
	    ($flux['args']['form']=='inscription' or $flux['args']['form']=='inscription_avec_rubrique') 
	    and (!$meta['formulaire_explicite']) 
	    or ($meta['formulaire_explicite']=='on' 
	    and $flux['args']['form']=='inscription_avec_rubrique')
	   ){

		// Récuperation des paramètres
		$mail = _request('mail_inscription');
		$nom_inscription = str_replace('@',' (chez) ',_request('nom_inscription'));
		include_spip('base/abstract_sql');
		$id_auteur = sql_getfetsel('id_auteur','spip_auteurs','email='.sql_quote($mail));
		
		
		//Modification du statut temporaire
		sql_updateq('spip_auteurs',array('prefs'=>$meta['statut']),'id_auteur='.$id_auteur); 

		
		// Création de la rubrique
		if (!$meta['id_parent'] or $meta['id_parent']==0){
			$id_rubrique = sql_insertq("spip_rubriques", array('titre'=> _T('rubrique_a_linscription:titre_rubrique',array('nom'=>$nom_inscription)), 'id_secteur'=> 0));
			sql_update("spip_rubriques",array("id_secteur"=>$id_rubrique), "id_rubrique=".$id_rubrique);
		}
		else{
			$id_secteur  	= sql_getfetsel('id_secteur','spip_rubriques','id_rubrique='.$meta['id_parent']);
			$id_rubrique 	= sql_insertq("spip_rubriques", array('titre'=> _T('rubrique_a_linscription:titre_rubrique',array('nom'=>$nom_inscription)), 'id_secteur'=> $id_secteur,'id_parent'=>$meta['id_parent']));	
		}
		
		sql_insertq('spip_auteurs_liens', array(
		'id_auteur' => $id_auteur,
		'objet'		=>'rubrique',
		'vu'		=>'non',
		'id_objet' => $id_rubrique));
		spip_log('Création de la rubrique '.$id_rubrique.' pour l\'auteur '.$nom_inscription.' ( '.$mail.' )','rubrique_a_linscription');
		
		//On ajoute la rubrique chez l'auteur
		sql_update('spip_auteurs',array('rubrique_a_linscription'=>$id_rubrique),"id_auteur=$id_auteur");
		
		//Création du mot clef associé
		if($meta['groupe_mots']){
			$type   = sql_getfetsel('titre','spip_groupes_mots','id_groupe='.$meta['groupe_mots']);
			if ($type){
				$id_mot = sql_insertq('spip_mots',array('id_groupe'=>$meta['groupe_mots'],'type'=>$type,'titre'=>_T('rubrique_a_linscription:mot_clef_de',array('nom'=>$nom_inscription))));
				spip_log("Création du mot clef dans le groupe $type pour l'auteur $nom_inscription (id mot = $id_mot)",'rubrique_a_linscription');
			}
		}
		
		//Envoyer mails
		
		if ($meta['mail_public'] or $meta['mail_prive']){
			
			$envoyer_mail 	= charger_fonction('envoyer_mail','inc');
			
			$corps		  	= _T('rubrique_a_linscription:mail_adresse_rubrique');
			include_spip('inc/utils');
			
			if ($meta['mail_public']){
				$corps 		.= 	"-".url_absolue(generer_url_public("rubrique","id_rubrique=$id_rubrique"))."\n";
			}
			
			if ($meta['mail_prive']){
				$corps 		.= 	"-".generer_url_ecrire("rubrique","id_rubrique=$id_rubrique")."\n";
			}
			include_spip('inc/filtres');
			
			$envoyer_mail($mail,'['.extraire_multi(lire_meta('nom_site')).']'. _T('rubrique_a_linscription:titre_mail_adresse_rubrique'),$corps);
		}
		
	}

	
	
	return $flux;	
}