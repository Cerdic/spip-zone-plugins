<?php
/**
 * 
 * Fonction de validation d'un email
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire 
 * @param string $email L'email testé
 * @param int $id_auteur[optional]
 */
 
function inc_inscription2_valide_email_dist($email,$id_auteur=NULL) {
	if(!$email){
		return;
	}
	else{
		// verifier que le mail est valide
		if(!email_valide($email))
			return _T('inscription2:saisir_email_valide');

		// Verifier si le mail est déjà connu
		if (strlen($email) > 0 AND email_valide($email)) {
			if (sql_getfetsel("id_auteur","spip_auteurs","id_auteur !='".intval($id_auteur)."' AND email = '$email'")) {
				
				// si un inscrit depuis moins de quelques minutes est dans la session
				// c'est qu'il cherche a corriger sa fiche
				// on le laisse passer
				include_spip("inc/inscription2_session");
				if($id_inscrit = i2_session_valide())
					return ;
				else
					return _T('form_forum_email_deja_enregistre');	
				
			}
		}
	}
	return;
}

?>