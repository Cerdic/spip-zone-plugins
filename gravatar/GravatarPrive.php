<?php

function gravatar_affiche_gauche($flux) {
	if ((_request('exec') == 'auteur_infos')) {
		if ($id_auteur = intval(_request('id_auteur'))
		AND $email = sql_fetsel('email', 'spip_auteurs', 'id_auteur='.$id_auteur)
		AND $email = $email['email']) {
			include_spip('inc/gravatar');
			if ($grav = gravatar($email))
				$flux['data'] .= "<b>Gravatar :</b><br />"
					. inserer_attribut('<img />', 'src', $grav);
		}
	}

	return $flux;
}
