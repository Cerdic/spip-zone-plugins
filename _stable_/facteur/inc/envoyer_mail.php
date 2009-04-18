<?php


	include_spip('inc/facteur_classes');


	function inc_envoyer_mail($destinataire, $sujet, $corps, $from = "", $headers = "") {
		$message_html	= '';
		$message_texte	= '';
		if (is_array($corps)) {
			$message_html	= $corps['html'];
			$message_texte	= $corps['texte'];
			$pieces_jointes	= $corps['pieces_jointes'];
		} else {
			$message_texte	= $corps;
		}
		$notification = new Facteur($destinataire, $sujet, $message_html, $message_texte);
		if (!empty($from))
			$notification->From = $from;
		if (count($pieces_jointes)) {
			foreach ($pieces_jointes as $piece) {
				$notification->AddAttachment($piece['chemin'], $piece['nom'], $piece['encodage'], $piece['mime']);
			}
		}
		return $notification->Send();
	}



/** Conserver les fonctions de SPIP car il en utilise certaines ! **/

// utilisee par inc/notifications.php
// http://doc.spip.org/@nettoyer_titre_email
function nettoyer_titre_email($titre) {
	return str_replace("\n", ' ', supprimer_tags(extraire_multi($titre)));
}


?>
