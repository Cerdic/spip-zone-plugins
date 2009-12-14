<?php

function genie_activite_editoriale_alerte_dist() {
	if ($rubLists = sql_select("*", "spip_rubriques", "`extras_delai` != '' and TO_DAYS(NOW()) - TO_DAYS(maj) >= `extras_delai`")) {
		while($list = sql_fetch($rubLists)) {
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$subject = 'Une rubrique doit etre mise a jour';
			include_spip('activite_editoriale_fonctions');
			$url = $GLOBALS['meta']['adresse_site'].'/ecrire/?exec=naviguer&id_rubrique='.$list['id_rubrique'];
			$body = 'Attention, la rubique "'.$list['titre'].'" n\'a pas ete mise a jour depuis '.age_rubrique($list['maj']).' jours. '."\n\n";
			$body = $body.'Vous etes identifie comme faisant partie des gestionnaires de la rubrique'."\n\n";
			$body = $body.$url;
			if ($auteurLists = sql_select("*", "spip_auteurs", "id_auteur in (".$list['extras_identifiants'].")")) {
				while($auteurs = sql_fetch($auteurLists)) {
					$to = $auteurs['email'];
					if ($envoyer_mail($to, $subject, $body)) {
						spip_log("Message sent to ".$to, "activite_editoriale");
					} else {
						spip_log("Message could not be sent to ".$to, "activite_editoriale");
					}
				}
			}
		}
	}
	return 0;
}

?>