<?php


if (!defined("_ECRIRE_INC_VERSION")) return;


// 20 minutes de repit avant notification (+0 a 10 minutes de cron)
define('_DELAI_NOTIFICATION_MESSAGERIE', 60 * 20);


function Notifications_taches_generales_cron($taches_generales){
	$taches_generales['notifications'] = 60 * 10; // toutes les 10 minutes
	return $taches_generales;
}


// Les notifications de la messagerie privee et de son forum se font par cron
// base sur le champ 'vu' de spip_auteurs_messages
// L'idee est
// 1) de ne pas spammer les gens qui sont en ligne
// 2) de ne pas notifier un auteur qu'on vient d'ajouter a une discussion,
//    alors qu'on va peut-etre le supprimer (erreur de choix de destinataire)
function cron_notifications($time) {
	if (!is_array($GLOBALS['notifications']
	= @unserialize($GLOBALS['meta']['notifications'])))
		$GLOBALS['notifications'] = array();

	if (!$GLOBALS['notifications']['messagerie'])
		return;

	$s = spip_query("SELECT lien.id_auteur,lien.id_message, message.titre, message.texte, message.date_heure as date, auteur.nom, auteur.email, auteur.en_ligne FROM spip_auteurs_messages AS lien, spip_messages AS message, spip_auteurs AS auteur WHERE lien.id_message = message.id_message AND lien.id_auteur = auteur.id_auteur AND lien.`vu`='non'");

	while ($t = spip_fetch_array($s)) {
		// si le message est tout nouveau (ou n'a pas de date), on l'ignore
		if (!$d = strtotime($t['date'])
		OR $d > time() - _DELAI_NOTIFICATION_MESSAGERIE)
			continue;

		// Si l'auteur est en ligne (ou ne l'a jamais ete), on l'ignore aussi
		if (!$d = strtotime($t['en_ligne'])
		OR $d > time() - _DELAI_NOTIFICATION_MESSAGERIE)
			continue;

		// Si l'auteur n'a pas de mail ou est a la poubelle, on l'ignore
		if (!$t['email'] OR $t['statut'] == '5poubelle')
			continue;

		// OK on peut lui envoyer le mail
		include_spip('inc/notifications');
		include_spip('inc/texte');

		// Chercher les forums les plus recents de ce message, pour afficher
		// des extraits
		$body =  _T('form_forum_message_auto')."\n\n";
		$body .= "* " . textebrut(propre(couper(
				$t['titre']."<p>".$t['texte'], 700)))."\n\n";

		$f = spip_query("SELECT titre,texte FROM spip_forum"
			." WHERE id_message = " .$t['id_message']
			." AND UNIX_TIMESTAMP(date_heure) > "._q($time));
		while ($ff = spip_fetch_array($f)) {
			$body .= "----\n"
				.textebrut(propre(couper(
					"** ".$ff['titre']."<p>".$ff['texte'], 700)))."\n\n";
		}

		$u = generer_url_ecrire('message', 'id_message='.$t['id_message'],'&');
		$body .= "$u\n";

		$subject = "[" .
	  entites_html(textebrut(typo($GLOBALS['meta']["nom_site"]))) .
	  "] ["._T('onglet_messagerie')."] ".typo($t['titre']);

		// Ne pas recommencer la prochaine, meme en cas de plantage du mail :)
		spip_query("UPDATE spip_auteurs_messages SET vu='oui' WHERE id_auteur=".$t['id_auteur']." AND id_message=".$t['id_message']);

		include_spip('inc/mail');
		envoyer_mail($t['email'], $subject, $body);
	}

	if ($t)
		return 1;
}

// en 1.9.3 c'est genie_ et pas cron_ (pfff)
function genie_notifications($time) {
	return cron_notifications($time);
}

?>
