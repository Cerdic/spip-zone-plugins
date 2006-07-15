<?php
	include_spip('inc/forms');
	
	function generer_mail_ecard_formulaire($id_form, $id_reponse, $id_article){
		$texte = "";
		$res = spip_query("SELECT titre,texte FROM spip_articles WHERE id_article=".spip_abstract_quote($id_article));
		if ($row = spip_fetch_array($res)){
			$titre = $row['titre'];
			$texte = liens_absolus(propre($row['texte']));
		}
		$texte .= "<br />\n<a href='".url_absolue(generer_url_article($id_article))."'>Retrouvez cette e-card en ligne</a><br/>\n"; 
	
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$champconfirm = $row['champconfirm'];
			//$email = unserialize($row['email']);

			$email_dest = $email['defaut'];
			$mailconfirm = "";

			$form_summary = '';
			$structure = unserialize($row['structure']);
			// Ici on parcourt les valeurs entrees pour les champs demandes
			foreach ($structure as $index => $t) {
				$type = $t['type'];
				$code = $t['code'];
				$type_ext = $t['type_ext'];

				if (!in_array($type,array('separateur','textestatique'))){
					if ($code != $champconfirm)					// on ne remet pas l'email du destinataire dans le mail
						$form_summary .= "<strong>".$t['nom'] . "</strong> : ";
		
					$query2 = "SELECT * FROM spip_reponses_champs WHERE id_reponse='$id_reponse' AND champ='$code'";
					$result2 = spip_query($query2);
					$reponses = '';
					while ($row2 = spip_fetch_array($result2)) {
						if ($email['route']==$code && isset($email[$row2['valeur']]))
							$email_dest = $email[$row2['valeur']];
						if ($code == $champconfirm)
							$mailconfirm = $row2['valeur'];
							
						$reponses .= Forms_traduit_reponse($type, $code,$type_ext,$row2['valeur']).", ";
					}
					if ($code != $champconfirm)					// on ne remet pas l'email du destinataire dans le mail
						if (strlen($reponses) > 2)
							$form_summary .= substr($reponses,0,strlen($reponses)-2);
					$form_summary .= "<br />\n";
				}
			}
	
			include_spip('inc/charset');
			$trans_tbl = get_html_translation_table (HTML_ENTITIES);
			$trans_tbl = array_flip ($trans_tbl);
		 	if ($mailconfirm !== '') {
				$head="From: ecard@".$_SERVER["HTTP_HOST"]."\n";
				$head .= "Content-type : text/html\n";

				$message = "";
				$message .= $texte . "<br />\n" . $form_summary;
				$sujet = $titre;
				$dest = $mailconfirm;
				
				// mettre le texte dans un charset acceptable
				$mess_iso = unicode2charset(charset2unicode($message),'iso-8859-1');
				// regler les entites si il en reste
				$mess_iso = strtr($mess_iso, $trans_tbl);

				mail($dest, $sujet, $mess_iso, $head);
			}
		}
	}	

?>