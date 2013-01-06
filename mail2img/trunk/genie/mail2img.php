<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('receivemail.class');
include_spip('action/editer_objet');
include_spip('action/editer_auteur');
include_spip('inc/autoriser');
include_spip('inc/filtres');
include_spip('inc/filtres_images_mini');


function genie_mail2img_dist($t){
	$log = '';

// CONFIG
	// paramètres de connexion du compte imap/pop à scanner
	$login_boite = 'login_compte_mail';
	$passe_boite = 'pass_compte_mail';
	$mail_boite = 'adresse.mail@fournisseur.tld';
	$serveur_boite = 'serveur-imap.fournisseur.tld';
	$protocole_boite = 'imap';
	$port_boite = '143';
	$ssl_boite = FALSE;
	
	// trouver l'article auquel attacher les photos envoyées: il doit avoir "mail2img" dans le surtitre 
	if ($id_article = sql_getfetsel("id_article", "spip_articles", array("surtitre = 'mail2img'")))
$log .= "\r\n id_article: $id_article";
	else
$log .= "\r\n erreur: pas d'article avec le surtitre = 'mail2img'";

	// dimensions de retaillage des images envoyées
	$hauteur_img = 1280;
	$largeur_img = 1280;
// FIN CONFIG
	
	// generer l'array des visiteurs existant (ceux ayant un mail)
	$Tparticipants = array();
	$res_r = sql_select("*", "spip_auteurs", array("statut = '6forum'", "email != ''"));	
	while ($row = sql_fetch($res_r)) 
		$Tparticipants[$row['id_auteur']] = $row['email'];
//$log .= "\r\n".'$Tparticipants: '.join("\r\n", $Tparticipants);

	// connecter et scanner la boite mail 
	$obj = new receiveMail($login_boite, $passe_boite, $mail_boite, $serveur_boite, $protocole_boite, $port_boite, $ssl_boite);
	$obj->connect();

	// on boucle dans les messages
	$tot = $obj->getTotalMails();
	for ($i = $tot; $i>0; $i--) {
$log .= "\r\n ----------------------------------------------------------"; 
		// recuperer le header du message
		$head = $obj->getHeaders($i);
		// generer les variables a partir des objets recup dans le header
		foreach ($head as $attr => $val) 
			$$attr = $val;
$log .= "\r\n from1: $from";

		// construire le pseudo
		if (!isset($pseudo)) {
			if ($fromName == '') {
				$Tfrom = explode('@', $from);
				$fromName = $Tfrom[0];
			}
			$pseudo = mail2img_recup_nom($fromName);
		}
$log .= "\r\n mail: $from / pseudo: $pseudo";
			
		if (!in_array($from, $Tparticipants)) {
			$id_auteur = auteur_inserer();
			autoriser_exception('modifier','auteur',$id_auteur);
			$res = auteur_modifier($id_auteur, array(
				'nom'=>$pseudo,
				'bio'=>'genere par mail2img',
				'statut'=>'6forum',
				'email'=>$from,
				'webmestre'=>'non')
			);
			autoriser_exception('modifier','auteur',$id_auteur, FALSE);
			$Tparticipants[$id_auteur] = $from;
$log .= "\r\n Création de l'auteur $id_auteur / $pseudo";
		}

		// recuperer les images attachees: pour chacune la copier dans tmp/upload, la reduire et switcher sur l'image réduite
		$Tattaches = $obj->GetAttach($i, _DIR_TMP.'upload/', array('JPEG','PNG'));
		foreach ($Tattaches as $key=>$img) {
			$Tfic_reduit = explode('?', extraire_attribut(image_reduire(_DIR_TMP.'upload/'.$img, $largeur_img, $hauteur_img), 'src'));
			$fic_reduit = $Tfic_reduit[0];
			$Tattaches[$key] = $fic_reduit;
$log .= "\r\n fic_reduit: $fic_reduit";
			unlink(_DIR_TMP.'upload/'.$img);
		}

		// enregistrer l'image comme doc attaché à l'article du concours
		foreach ($Tattaches as $tof) {
			$f = charger_fonction('ajouter_documents', 'action');
			$Tid_tof = $f('new',
					array(array('tmp_name'=>$tof, 'name'=>basename($tof), 'titrer'=>FALSE, 'distant'=>FALSE, 'mode'=>'document')),
					'article',
					$id_article,
					'image'
			);
			$id_tof = $Tid_tof[0];
			
			// donner l'autorisation temporaire pour la modification du doc
			autoriser_exception('modifier', 'document', $id_tof);
			$res = objet_modifier('document', $id_tof, array(
				'titre'	=> 'photo envoyee par '.$pseudo,
				'credits'=> $from,
				'statut' =>'publie'
			));
			// retirer l'autorisation exceptionnelle
			autoriser_exception('modifier', 'document', $id_tof, FALSE);

			// plugin auteurpartout: attacher le participant comme auteur de l'image
			sql_insertq("spip_auteurs_liens", array(
				'id_auteur' => $id_auteur,
				'id_objet' => $id_tof,
				'objet' => 'document'));

			// mail d'accusé de reception ou d'erreur
			if ($res != '') {
$log .= "\r\n erreur modification doc $id_tof: $res";
				$sujet = "[mail2img] Re: $subject";
				$body = "Bonjour $pseudo, \n \n Suite a un problème technique, votre photo n'a pu être enregistrée par mail2img.";
				$body .= "\n Il est fortement possible que cela soit du à une taille de photo dépassant les capacités du serveur (5 Mo).";
				$body .= "Si ce n'est pas le cas, vous pouvez essayer de renvoyer cette photo.";
				$body .= "Si le problème se reproduisait, merci de transmettre copie de ce message à contact@fournisseur.tld .";
				$body .= "\n\n Veuillez nous excuser pour les complications entraînées par cet incident.";
				$body .= "\n Merci de votre compréhension.";
				$body .= "\n \n l'équipe de mail2img";
				$body .= "\n\n -----------------------------------------";
				$body .= "\n Données techniques de l'erreur:\n";
				$body .= $res;
				$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
				$envoyer_mail($from, $sujet, $body);
				$res = $obj->deleteMails($i);
			}
			else {
$log .= "\r\n Integration du document $id_tof / from: $from / pseudo: $pseudo = OK";
				$sujet = "[mail2img] Re: $subject => OK!";
				$body = "Bonjour $pseudo, \n \n mail2img vient d'intégrer votre photo.";
				$body .= "\n Merci de votre participation,";
				$body .= "\n \n L'équipe de mail2img";
				$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
				$envoyer_mail($from, $sujet, $body);
				$res = $obj->deleteMails($i);
			}
		}

		// TODO: sinon recuperer le body du message et y trouver une url d'image ou de page avec oembed pour générer un doc distant
		if (!isset($Tattaches) OR count($Tattaches) == 0) {
//			$body = $obj->getBody($i);
			$res = $obj->deleteMails($i);
		}		
	}	// fin de la boucle dans les messages

	spip_log($log, 'mailimg');

	// fermer la connexion IMAP
	$obj->close_mailbox();
	
	return 1;
}
