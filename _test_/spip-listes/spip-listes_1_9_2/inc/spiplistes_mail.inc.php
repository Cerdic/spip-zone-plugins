<?php

	include_spip('phpmailer/class.phpmailer');
	include_spip('phpmailer/class.smtp');

	class phpMail extends PHPMailer {

		function phpMail($email, $objet, $message_html, $message_texte, $charset) {
		
			//$charset = $GLOBALS['meta']['spiplistes_charset_envoi'];
					
			//$this->From		= lire_meta('email_webmaster');
			$this->FromName	=  $GLOBALS['meta']['nom_site'];
			$this->CharSet	= $charset ;
	    $this->Mailer	= 'mail';
			$this->Subject	= $objet;
			$this->AddAddress($email); 
			
			if ($smtp_sender =  $GLOBALS['meta']['smtp_sender']) {
				$this->Sender = $GLOBALS['meta']['spip_lettres_smtp_sender'];
			}
			
			$envoi_par_smtp =  $GLOBALS['meta']['mailer_smtp'] ;

			if($envoi_par_smtp == "oui"){
				if ($smpt_server =  $GLOBALS['meta']['smtp_server']) {
		    		$this->IsSMTP(); // telling the class to use SMTP
		    		$this->Mailer	= 'smtp';
				    $this->Host 	=  $GLOBALS['meta']['smtp_server'];
				    $this->Port 	=  $GLOBALS['meta']['smtp_port'];
					
					$smtp_identification =  $GLOBALS['meta']['smtp_identification'] ;

					if ($smtp_identification == "oui") {
					    $this->SMTPAuth = true;
					    $this->Username =  $GLOBALS['meta']['smtp_login'];
					    $this->Password =  $GLOBALS['meta']['smtp_pass'];
					} else {
					    $this->SMTPAuth = false;
					}
				}
			}
			if (!empty($message_html) AND !empty($message_texte)) {
	     		$this->Body = $message_html;
	     		$this->AltBody = $message_texte;
				//$this->spiplistes_JoindreImagesHTML();
			}
			if (!empty($message_texte) AND empty($message_html)) {
				
					$this->IsHTML(false);
					$this->Body = $message_texte;
				
			}
		}
		function SetAddress($address, $name = "") {
			$this->to=array();
			$this->AddAddress($address,$name);
		}       

	/**
	 * d'apres SPIP-Lettres : plugin de gestion de lettres d'information
	 * inutilisé mais ca pourra peut etre servir pour emabarquer les images
	 * Copyright (c) 2006
	 * Agence Atypik Creations
	 *  
	 *  
	 **/

		function spiplistes_JoindreImagesHTML() {
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
				if (file_exists($images[1][$i])) {
					$html_images[] = $images[1][$i];
					$this->Body = str_replace($images[1][$i], basename($images[1][$i]), $this->Body);
				}
			}

			$images = array();
			preg_match_all("/'([^']+\.(".implode('|', $extensions)."))'/Ui", $this->Body, $images);

			for ($i=0; $i<count($images[1]); $i++) {
				if (file_exists('../'.$images[1][$i])) {
					$html_images[] = '../'.$images[1][$i];
					$this->Body = str_replace($images[1][$i], basename($images[1][$i]), $this->Body);
				}
				if (file_exists($images[1][$i])) {
					$html_images[] = $images[1][$i];
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