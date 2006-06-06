<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Atypik Créations
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('phpmailer/class.phpmailer');
	include_spip('phpmailer/class.smtp');

	class LettresMail extends PHPMailer {

		function LettresMail($email, $objet, $message_html, $message_texte) {
			$this->From		= $GLOBALS['meta']['email_webmaster'];
			$this->FromName	= $GLOBALS['meta']['nom_site'];
			$this->CharSet	= $GLOBALS['meta']['charset'];
	    	$this->Mailer	= 'mail';
			$this->Subject	= $objet;
			$this->AddAddress($email);

/*
			if (isset($GLOBALS['meta']['spip_lettres_smtp']) AND $GLOBALS['meta']['spip_lettres_smtp'] == true) {
	    		$this->Mailer	= 'smtp';
			    $this->Host = $GLOBALS["spip_lettres_smtp_host"];
			    $this->Port = $GLOBALS["spip_lettres_smtp_port"];
			    $this->SMTPAuth = true;
			    $this->Username = $GLOBALS["spip_lettres_smtp_username"];
			    $this->Password = $GLOBALS["spip_lettres_smtp_password"];
	       		$this->Sender	= $GLOBALS["spip_lettres_smtp_sender"];
	       		$this->AddCustomHeader("Errors-To: ".$this->Sender);
			}
*/

			if (!empty($message_html)) {
	     		$this->Body = $message_html;
	     		$this->IsHTML(true);
				$this->JoindreImagesHTML();
			}
			if (!empty($message_texte)) {
				if (!$this->Body) {
					$this->IsHTML(false);
					$this->Body = $message_texte;
				} else {
					$this->AltBody = $message_texte;
				}
			}
		}


		function JoindreImagesHTML() {
			$image_types = array(
								'gif'	=> 'image/gif',
								'jpg'	=> 'image/jpeg',
								'jpeg'	=> 'image/jpeg',
								'jpe'	=> 'image/jpeg',
								'bmp'	=> 'image/bmp',
								'png'	=> 'image/png',
								'tif'	=> 'image/tiff',
								'tiff'	=> 'image/tiff',
								'swf'	=> 'application/x-shockwave-flash'
							);
			while (list($key,) = each($image_types))
				$extensions[] = $key;

			preg_match_all('/"([^"]+\.('.implode('|', $extensions).'))"/Ui', $this->Body, $images);

			for ($i=0; $i<count($images[1]); $i++) {
				if (file_exists('../'.$images[1][$i])) {
					$html_images[] = '../'.$images[1][$i];
					$this->Body = str_replace($images[1][$i], basename($images[1][$i]), $this->Body);
				}
			}
			if (!empty($html_images)) {
				$html_images = array_unique($html_images);
				sort($html_images);
				for ($i=0; $i<count($html_images); $i++) {
					$cid = md5(uniqid(time()));
					$this->AddEmbeddedImage($html_images[$i], $cid);
					$this->Body = str_replace(basename($html_images[$i]), "cid:$cid", $this->Body);
				}
			}
		}


	}

?>