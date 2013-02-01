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
			if ($id = sql_getfetsel("id_auteur","spip_auteurs","id_auteur !='".intval($id_auteur)."' AND email = '$email'")) {

				// si un inscrit depuis moins de quelques minutes est dans la session
				// c'est qu'il cherche a corriger sa fiche
				// on le laisse passer
				include_spip("inc/inscription2_session");
				if($id_inscrit = i2_session_valide())
					return;

				// est-ce un email connu à qui il manque des champs obligatoires
				// (inscrit partiellement par spip listes par ex) ?
				// si oui, on pose une session et on le laisse passer.
				// Il parrait q'un admin spip peut créer un compte avec un email existant...
				// Ne faites pas ca, si vous êtes sain d'esprit et comptez utiliser ce code !
				include_spip("inc/inscription2_inscrit_partiel");
				//if(i2_inscrit_partiel($id))
				//	return;

				return _T('inscription2:email_deja_enregistre');

			}
		}
	}
	return;
}

?>