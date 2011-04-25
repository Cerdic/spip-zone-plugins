<?php

function notifications_ajouterami_dist($quoi, $id, $options) {
	
	$modele = "notifications/ajouterami";
	
	$destinataires = array();
	
	$destinataires[] = sql_getfetsel("email","spip_auteurs","id_auteur =". intval($options['id_auteur']));
	
	$destinataires = pipeline('notifications_destinataires',
		array(
			'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
		,
			'data'=>$destinataires)
	);
	
	$envoyer_mail = charger_fonction('envoyer_mail','inc'); // pour nettoyer_titre_email
	$texte = recuperer_fond($modele,array('id_ami'=>$id,'id_auteur' => $options['id_auteur']));
	
	notifications_envoyer_mails($destinataires, $texte);
}

?>