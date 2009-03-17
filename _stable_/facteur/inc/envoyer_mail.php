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


?>