<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

#function notifications_instituersouhait_dist($quoi, $id, $options){
#	include_spip('action/editer_liens');
#	$souhait = sql_fetsel('titre, propositions', 'spip_souhaits', 'id_souhait = '.$id_souhait);
#	$destinataires = array();
#	if ($auteurs = objet_trouver_liens(array('auteur' => '*'), array('souhait' => $id_souhait))){
#		array_push($destinataires, sql_getfetsel('email', 'spip_auteurs', 'id_auteur = '.intval($auteurs['id_auteur'])));
#	}
#	
#	$destinataires = pipeline('notifications_destinataires',
#		array(
#			'args'=>array('quoi'=>$quoi,'id'=>$id_article,'options'=>$options),
#			'data'=>$destinataires
#		)
#	);
#	
#	
#}

?>
