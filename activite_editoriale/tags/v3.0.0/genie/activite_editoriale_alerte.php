<?php
/**
 * Plugin Activite Editoriale
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function genie_activite_editoriale_alerte_dist() {
	if (function_exists('lire_config')) {
		$config_champ = lire_config('activite_editoriale/champ','maj_rubrique');
	}
	switch ($config_champ) {
		case 'maj_rubrique':
			activite_tester_maj_rubrique();
			break;
		case 'date_modif_branche':
			activite_tester_date_modif_branche($alerter_auteur);
			break;
		case 'date_modif_rubrique':
			activite_tester_date_modif_rubrique($alerter_auteur);
			break;
	}
	return 0;
}

function activite_tester_maj_rubrique() {
	if ($rubLists = sql_select("*", "spip_rubriques", "`extras_delai` != '' and TO_DAYS(NOW()) - TO_DAYS(maj) >= `extras_delai`")) {
		while($list = sql_fetch($rubLists)) {
			activite_editoriale_envoyer_mail($list);
		}
	}
}

function activite_tester_date_modif_branche() {
	if ($rubLists = sql_select(array('id_rubrique','extras_delai','extras_identifiants','extras_emails','titre','extras_frequence'), "spip_rubriques", "`extras_delai` != ''")) {
		include_spip('inc/utils');
		while($list = sql_fetch($rubLists)) {
			$date_modif = trim(recuperer_fond('inclure/maj_branche',array('id_rubrique'=>$list['id_rubrique'])));
			$age = age_rubrique($date_modif);
			$debutfrequence = ($age + $list['extras_delai']);
			$frequence = $list['extras_frequence'];
			// on teste les dates + la frequence
			if ( $list['extras_frequence'] != '' ) {
				if ( ($age > $list['extras_delai']) && ($debutfrequence % $frequence === 0) ) {
					$list['maj'] = $date_modif;
					activite_editoriale_envoyer_mail($list,$alerter_auteur);
					spip_log('date modif branche', 'activite_editoriale');
				}
			} else {
				if ( $age > $list['extras_delai'] ) {
					$list['maj'] = $date_modif;
					activite_editoriale_envoyer_mail($list,$alerter_auteur);
					spip_log('date modif branche', 'activite_editoriale');
				}
			}
		}
	}
}

function activite_tester_date_modif_rubrique() {
	if ($rubLists = sql_select(array('id_rubrique','extras_delai','extras_identifiants','extras_emails','titre','extras_frequence'), "spip_rubriques", "`extras_delai` != ''")) {
		include_spip('inc/utils');
		while($list = sql_fetch($rubLists)) {
			$date_modif = trim(recuperer_fond('inclure/maj_rubrique',array('id_rubrique' => $list['id_rubrique'])));
			$age = age_rubrique($date_modif);
			$debutfrequence = ($age + $list['extras_delai']);
			$frequence = $list['extras_frequence'];
			// on teste les dates + la frequence
			if ( $list['extras_frequence'] != '' ) {
				if ( ($age > $list['extras_delai']) && ($debutfrequence % $frequence === 0) ) {
					$list['maj'] = $date_modif;
					activite_editoriale_envoyer_mail($list,$alerter_auteur);
						spip_log('date modif rubrique', 'activite_editoriale');
				}
			} else {
				if ( $age > $list['extras_delai'] ) {
					$list['maj'] = $date_modif;
					activite_editoriale_envoyer_mail($list,$alerter_auteur);
						spip_log('date modif rubrique', 'activite_editoriale');
				}
			}
		}
	}
}

function activite_editoriale_envoyer_mail($list) {
	include_spip('activite_editoriale_fonctions');
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	$subject = _T('activite_editoriale:rubrique_doit_maj');
	$url = $GLOBALS['meta']['adresse_site'].'/ecrire/?exec=rubrique&id_rubrique='.$list['id_rubrique'];
	$body = _T('activite_editoriale:titre_message')."\n\n";
	$body = $body._T('activite_editoriale:prevenir_responsable',array('titre'=>$list['titre']))."\n\n";
	$body = $body._T('activite_editoriale:rubrique_pas_maj',array('jours' => age_rubrique($list['maj'])))."\n\n";
	$body = $body.$url;
	
	if ($auteurLists = sql_select("*", "spip_auteurs", "id_auteur in (".$list['extras_identifiants'].")")) {
		while($auteurs = sql_fetch($auteurLists)) {
			$to = $auteurs['email'];
			if ($envoyer_mail($to, $subject, $body)) {
				spip_log("Message envoyé à".$to, "activite_editoriale");
			} else {
				spip_log('Message n\'a pu être envoyé à '.$to, 'activite_editoriale');
			}
		}
	}
	$to = '';
	foreach (explode(',',$list['extras_emails']) as $to) {
		if ($to != '') {
			if ($envoyer_mail($to, $subject, $body)) {
				spip_log('Message envoyé à '.$to, 'activite_editoriale');
			} else {
				spip_log('Message n\'a pu être envoyé à '.$to, 'activite_editoriale');
			}
		}
	}
	// envoyer mail a l'auteur de l'article, systematiquement si configure
	if (function_exists('lire_config')) {
		$alerter_auteur = lire_config('activite_editoriale/alerter_auteur');
		if ($alerter_auteur == 'oui') {
			$auteur = trim(recuperer_fond('inclure/auteurs_article',array('id_rubrique' => $list['id_rubrique'])));
			$to = $auteur;
			if ($to != '') {
				$body = '';
				$body = _T('activite_editoriale:titre_message')."\n\n";
				$body = $body._T('activite_editoriale:prevenir_auteur',array('titre'=>$list['titre']))."\n\n";
				$body = $body._T('activite_editoriale:article_pas_maj',array('jours' => age_rubrique($list['maj'])))."\n\n";
				$body = $body.$url;
				if ($envoyer_mail($to, $subject, $body)) {
					spip_log('Message envoyé à '.$to, 'activite_editoriale');
				} else {
					spip_log('Message n\'a pu être envoyé à '.$to, 'activite_editoriale');
				}
			}
		}
	}
}
