<?php

include_once("inc_forms.php");
include_ecrire("inc_admin.php");
include_ecrire("inc_session.php");

function valide_sondage()
{
	global $verif_cookie;
	global $id_reponse;
	global $hash;
	global $mel_confirm;

	$renvoyer_image = false;

	if ($verif_cookie == 'oui' AND ($id_reponse = intval($id_reponse))) {
		$query = "SELECT * FROM spip_reponses WHERE id_reponse=$id_reponse AND statut='attente'";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			$cookie = $row['cookie'];
			$nom_cookie = nom_cookie_form($id_form);
			// D'abord verifier que l'URL est legitime, donc que la demande a bien
			// ete generee par SPIP
			if ($cookie && $cookie == $_COOKIE[$nom_cookie]
				&& verifier_action_auteur("cookie $id_reponse", $hash)) {
				// Ensuite verifier que le cookie n'a pas deja ete utilise pour le meme formulaire
				$query = "SELECT id_reponse FROM spip_reponses ".
					"WHERE id_form=$id_form AND id_reponse!=$id_reponse AND cookie='".addslashes($cookie)."'";
				if (!spip_num_rows(spip_query($query))) {
					$query = "UPDATE spip_reponses SET statut='valide' WHERE id_reponse=$id_reponse";
					spip_query($query);
				}
			}
		}
		$renvoyer_image = true;
	}
	else if ($mel_confirm == 'oui' AND ($id_reponse = intval($id_reponse))) {
		$query = "SELECT * FROM spip_reponses WHERE id_reponse=$id_reponse";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			// D'abord verifier que l'URL est legitime, donc que la demande a bien
			// ete generee par SPIP
			if (verifier_action_auteur("confirm $id_reponse", $hash)) {
				generer_mail_reponse_formulaire($id_form, $id_reponse, $mailconfirm);
			}
		}
		$renvoyer_image = true;
	}

	if ($renvoyer_image) {
		$image = "47494638396118001800800000ffffff00000021f90401000000002c0000000018001800000216848fa9cbed0fa39cb4da8bb3debcfb0f86e248965301003b";
		$image = pack("H*", $image);
		$size = strlen($image);

		Header("Content-Type: image/gif");
		Header("Content-Length: ".$size);
		Header("Cache-Control: no-cache,no-store");
		Header("Pragma: no-cache");
		Header("Connection: close");

		echo $image;
	}

}

?>
