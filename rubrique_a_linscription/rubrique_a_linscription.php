<?php

function rubrique_a_linscription_formulaire_traiter($flux){
	if ($flux['args']['form']=='inscription' and $flux['args']['args'][0]=='0minirezo'){
		
		$mail = _request('mail_inscription');
		$nom_inscription = str_replace('@',' (chez) ',_request('nom_inscription'));
		include_spip('base/abstract_sql');
		$id_auteur = sql_getfetsel('id_auteur','spip_auteurs','email='.sql_quote($mail));
		
		include_spip('inc/meta');
		$meta = unserialize(lire_meta('rubrique_a_linscription'));
		if (!$meta['id_parent'] or $meta['id_parent']==0){
			$id_rubrique = sql_insertq("spip_rubriques", array( 'titre'=> _T('Rubrique de '.$nom_inscription), 'id_secteur'=> 0));
			sql_update("spip_rubriques",array("id_secteur"=>$id_rubrique), "id_rubrique=".$id_rubrique);
		}
		else{
			$id_secteur  	= sql_getfetsel('id_secteur','spip_rubriques','id_rubrique='.$meta['id_parent']);
			$id_rubrique 	= sql_insertq("spip_rubriques", array( 'titre'=> _T('Rubrique de '.$nom_inscription), 'id_secteur'=> $id_secteur,'id_parent'=>$meta['id_parent']));	
		}
		
		sql_insertq('spip_auteurs_rubriques', array(
		'id_auteur' => $id_auteur,
		'id_rubrique' => $id_rubrique));
		spip_log('Création de la rubrique '.$id_rubrique.' pour l\'auteur '.$nom_inscription.' ( '.$mail.' )','rubrique_a_linscription');
		var_dump($meta);
		//Envoyer mails
		if ($meta['mail_public'] or $meta['mail_prive']){
			$envoyer_mail 	= charger_fonction('envoyer_mail','inc');
			var_dump($envoyer_mail);
			$corps		  	= "L'adresse de votre rubrique reservée est : \n";
			include_spip('inc/utils');
			
			if ($meta['mail_public']){
				$corps 		.= 	"-".generer_url_public("rubrique","id_rubrique=$id_rubrique")."\n";
			}
			
			if ($meta['mail_prive']){
				$corps 		.= 	"-".generer_url_ecrire("naviguer","id_rubrique=$id_rubrique")."\n";
			}
			
			$envoyer_mail($mail,'['.lire_meta('nom_site').'] Votre rubrique réservée',$corps,lire_meta('email_webmaster'));
		}
	}
	
	
	
	return $flux;	
}