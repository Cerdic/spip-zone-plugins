<?php

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * 
 * @return
 * @param object $time
 */
function genie_pj2article_dist($time) {
	spip_log('début de tache cron','pjarticle');

	include_spip('inc/config');
	$nb_mails = lire_config('pj2article/nombre_mails', 10);
	$inbox = lire_config('pj2article/inbox');
	$ignorebox = lire_config('pj2article/ignorebox');
	$errorbox = lire_config('pj2article/errorbox');
	$outbox = lire_config('pj2article/outbox');
	$rep_upload = 'tmp/upload/';
	$id_auteur = lire_config('pj2article/auteur');
	$id_rubrique = lire_config('pj2article/rubrique');

	// Se connecter à la boite d'importation
	include_spip('imap_fonctions');
	$c = imap_open_mbox_from_config($inbox);
	$headers = imap_headers($c);
	// Boucle sur les mails dans la limite de $nb_mails
	for ($mid=1;$mid<=min(count($headers), $nb_mails);$mid++) {
		// Essayer d'écrire les pièces jointes dans tmp/upload/
		$fichiers = imap_save_attachments($c, $mid, _DIR_TRANSFERT);
		if (count($fichiers) == 0) {
			// Si aucune pièce jointe, déplacer vers la boite ignorebox
			imap_mail_move($c,$mid,$ignorebox);
		} elseif (count($fichiers) > 1) {
			// Si plusieurs pièces jointes, déplacer vers la boite errorbox (pour l'instant, on ne les gère pas)
			imap_mail_move($c,$mid,$errorbox);
		} else {
			// Si une seule pièce jointe, 
			foreach ($fichiers as $f) {
				if (!$f['saved']) {
					// Si une erreur dans la sauvegarde d'un fichier, annulation et déplacement du mail dans la boite d'erreur
					imap_mail_move($c,$mid,$errorbox);
				} else {
					// l'ajouter dans la table de file d'attente qui sera traitée par le cron de doc2article
					$ok = sql_insertq('spip_doc2article',array(
						'id_auteur' => $id_auteur,
						'id_rubrique' => $id_rubrique,
						'fichier' => basename($f['unique_name']),
						'date' => date('Y-m-d H:i:s')
					));
					if (!$ok) {
						// si erreur, le déplacer dans la boite d'erreur
						imap_mail_move($c,$mid,$errorbox);
					} else {
						// sinon le déplacer dans la boite outbox
						imap_mail_move($c,$mid,$outbox);
					}
				}
			}
		}
	}

	imap_close($c, CL_EXPUNGE); 
	spip_log('fin de tache cron','pjarticle');
	
	return 1;
}
