<?php

function genie_activite_editoriale_alerte_dist() {
	if ($rubLists = sql_select("*", "spip_rubriques", "`extras_delai` != '' and TO_DAYS(NOW()) - TO_DAYS(maj) >= `extras_delai`")) {
		while($list = sql_fetch($rubLists)) {
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$subject = _T('activite_editoriale:rubrique_doit_maj');
			include_spip('activite_editoriale_fonctions');
			$url = $GLOBALS['meta']['adresse_site'].'/ecrire/?exec=naviguer&id_rubrique='.$list['id_rubrique'];
			$body = _T('activite_editoriale:rubrique_pas_maj',array('titre'=>$list['titre'],'jour'=>age_rubrique($list['maj'])))."\n\n";
			$body = $body._T('activite_editoriale:gestionnaire')."\n\n";
			$body = $body.$url;
			if ($auteurLists = sql_select("*", "spip_auteurs", "id_auteur in (".$list['extras_identifiants'].")")) {
				while($auteurs = sql_fetch($auteurLists)) {
					$to = $auteurs['email'];
					if ($envoyer_mail($to, $subject, $body)) {
						spip_log("Message envoyé à".$to, "activite_editoriale");
					} else {
						spip_log("Message n'a pu être envoyé à ".$to, "activite_editoriale");
					}
				}
			}
		}
	}
	return 0;
}

?>