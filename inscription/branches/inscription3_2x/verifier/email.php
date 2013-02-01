<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de validation d'un email
 *
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 * @param string $valeur L'email testé
 * @param array $options [optional]
 */
function verifier_email_dist($valeur,$options=array()) {
	if(!$valeur){
		return false;
	}
	else{
		// verifier que le mail est valide
		if(!email_valide($valeur))
			return _T('inscription3:erreur_email_valide');

		// Verifier si le mail est déjà connu
		if (strlen($valeur) > 0 AND email_valide($valeur)) {
			if ($id = sql_getfetsel("id_auteur","spip_auteurs","id_auteur !='".intval($options['id_auteur'])."' AND email = '$valeur'")) {

				// si un inscrit depuis moins de quelques minutes est dans la session
				// c'est qu'il cherche a corriger sa fiche
				// on le laisse passer
				include_spip("inc/inscription3_session");
				if($id_inscrit = i3_session_valide())
					return false;

				// est-ce un email connu à qui il manque des champs obligatoires
				// (inscrit partiellement par spip listes par ex) ?
				// si oui, on pose une session et on le laisse passer.
				// Il parrait q'un admin spip peut créer un compte avec un email existant...
				// Ne faites pas ca, si vous êtes sain d'esprit et comptez utiliser ce code !
				include_spip("inc/inscription3_inscrit_partiel");
				if(i3_inscrit_partiel($id))
					return false;

				return _T('inscription3:erreur_email_deja_enregistre');

			}
		}
	}
	return;
}

?>