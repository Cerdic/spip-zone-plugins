<?php

	include_spip('inc/charsets');
	
	if (intval(phpversion()) == 5) {
		include_spip('phpmailer-php5/class.phpmailer');
		include_spip('phpmailer-php5/class.smtp');
	} else {
		include_spip('phpmailer-php4/class.phpmailer');
		include_spip('phpmailer-php4/class.smtp');
	}
	include_spip('facteur_fonctions');

	class Facteur extends PHPMailer {

		function Facteur($email, $objet, $message_html, $message_texte) {

			if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui') {
				$this->From		= $GLOBALS['meta']['facteur_adresse_envoi_email'];
				$this->FromName	= $GLOBALS['meta']['facteur_adresse_envoi_nom'];
			} else {
				$this->From		= $GLOBALS['meta']['email_webmaster'];
				$this->FromName	= $GLOBALS['meta']['nom_site'];
			}

			$this->CharSet	= $GLOBALS['meta']['charset'];
	    	$this->Mailer	= 'mail';
			$this->Subject	= $objet;
			
			//Pour un envoi multiple de mail, $email doit être un tableau avec les adresses.
			if (is_array($email)) {
				foreach ($email as $cle => $adresseMail) {
					$this->AddAddress($adresseMail);
				}
			}
			else
			$this->AddAddress($email);

			if (isset($GLOBALS['meta']['facteur_smtp_sender'])) {
	       		$this->Sender = $GLOBALS['meta']['facteur_smtp_sender'];
	       		$this->AddCustomHeader("Errors-To: ".$this->Sender);
			}

			if (isset($GLOBALS['meta']['facteur_smtp']) AND $GLOBALS['meta']['facteur_smtp'] == 'oui') {
	    		$this->Mailer	= 'smtp';
			    $this->Host 	= $GLOBALS['meta']['facteur_smtp_host'];
			    $this->Port 	= $GLOBALS['meta']['facteur_smtp_port'];
				if ($GLOBALS['meta']['facteur_smtp_auth'] == 'oui') {
				    $this->SMTPAuth = true;
				    $this->Username = $GLOBALS['meta']['facteur_smtp_username'];
				    $this->Password = $GLOBALS['meta']['facteur_smtp_password'];
				} else {
				    $this->SMTPAuth = false;
				}
				if (intval(phpversion()) == 5) {
					if ($GLOBALS['meta']['facteur_smtp_secure'] == 'ssl')
					    $this->SMTPSecure = 'ssl';
					if ($GLOBALS['meta']['facteur_smtp_secure'] == 'tls')
					    $this->SMTPSecure = 'tls';
				}
			}

			if (!empty($message_html)) {
				$message_html = unicode_to_utf_8(charset2unicode($message_html,$GLOBALS['meta']['charset']));
	     		$this->Body = $message_html;
	     		$this->IsHTML(true);
				if ($GLOBALS['meta']['facteur_filtre_css'])
					$this->ConvertirStylesEnligne();
				if ($GLOBALS['meta']['facteur_filtre_images'])
					$this->JoindreImagesHTML();
			}
			if (!empty($message_texte)) {
				$message_texte = unicode_to_utf_8(charset2unicode($message_texte,$GLOBALS['meta']['charset']));
				if (!$this->Body) {
					$this->IsHTML(false);
					$this->Body = $message_texte;
				} else {
					$this->AltBody = $message_texte;
				}
			}

			if ($GLOBALS['meta']['facteur_filtre_iso_8859'])
				$this->ConvertirUtf8VersIso8859();

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
					
					// Bug Fix: dans thunderbird, il faut etre strict avec le header envoy� avec l'image
					$bouts = explode(".", basename($html_images[$i]));
    				$extension = strtolower(array_pop($bouts));
    				$header_extension = $image_types[$extension];
					
					$cid = md5(uniqid(time()));
					$this->AddEmbeddedImage($html_images[$i], $cid, basename($html_images[$i]),'base64',$header_extension);
					$this->Body = str_replace(basename($html_images[$i]), "cid:$cid", $this->Body);
				}
			}
		}


		function ConvertirStylesEnligne() {
			/*

			Written by Eric Dols - edols@auditavenue.com

			You may freely use or modify this, provided
			you leave credits to the original coder.
			Feedback about (un)successfull uses, bugs and improvements done
			are much appreciated, but don't expect actual support.

			PURPOSE OF THIS FUNCTION
				It is designed to process html emails relying
				on a css stylesheet placed in the <head> for layout in
				order to enhance compatibility with email clients,
				including webmail services.
				Provided you use minimal css, you can keep styling separate
				from the content in your email template, and let this function
				"inject" those styles inline in your email html tags on-the-fly,
				just before sending.
				Technically, it grabs the style declarations found in the
				<head> section and inserts each declaration inline,
				inside the corresponding html tags in the email message.

				Supports both HTML and XHTML markup seamlessly. Thus
				tolerant to email message writers using non-xhtml tag,
				even when template is xhtml compliant (e.g. they would
				add <img ...> instead of a xhtml compliant <img ... />).

			NEW 10 dec. 2003:
				- code revised, including a few regexp bugs fixed.
				- multiple class for a tag are now allowed <p class="firstclass secondclass">
				- all unsupported css styles are now moved to the body section (not just a:hover etc...)

			USE
				Add this function to a function library include, like "inline.inc"
				and include it near the beginning of your php page:
				require ("inline.inc");

				load the html source of message into a variable
				like $html_source and process it using:
				$html_source = sheet2inline($html_source)


			STYLE DEFINITIONS SUPPORTED
				TAG { ... }
				TAG1, TAG2, ... { ... }
				TAG.class { ... }
				.class { ...)
				TAG:pseudo { ... }


				CSS definitions may be freely formatted (spaces, tabs, linefeeds...),
				they are converted to oneliners before inserting them inline in the html tags.

				.class definitions are processed AFTER tag definitions,
				thus appended inline after any existing tag styling to
				preserve the normal css priority behavior.

				Existing style="..." attributes in tags are NOT stripped. However they MUST
				be with double quotes. If not, an addtional style="..." attribute will be added


			KNOWN LIMITATIONS
				- style info should be placed in <head> section. I believe
				  it shouldnt be too hard to modify to point to an external
				  stylesheet instead.
				- no support (yet?):
				  * chains like P UL LI { .... } or P UL LI.class { .... }
				  * #divname p { ... } and <tag id="...">
				  * a:hover, a:visited {...} multiple class:pseudo
				  They require a significantly more complicated processing likely
				  based on stylesheet and document trees parsing.
				  Many email clients don't handle more than what is supported
				  by this script anyway.
				- pseudo-classes like a:hover {...} can't be inserted inline
				  in the html tags: they are moved to a <style> declaration in
				  the <body> instead. This is a limitation from html, not this script.
				- It is still up to you to check if target email clients render
				  your css styled templates correctly, especially webmail services
				  like Hotmail, in which the email becomes a sub-part of an html page,
				  with styles already in place.
			*/

			// variables to be accessed in the callback sub-function too
			global $styledefinition, $styletag, $styleclass;

			// Let's first load the stylesheet information in a $styles array using a regexp
			preg_match_all ( "/^[ \t]*([.]?)([\w, #]+)([.:])?(\S*)\s+{([^}]+)}/mi", $this->Body , $styles);
			/*
				$styles[1] = . or ''  => .class or tag (empty)
				$styles[2] = name of class or tag(s)
				$styles[3] = : . or '' => followed by pseudo-element, class separator or nothing (empty)
				$styles[4] = name of pseudo-element after a tag, if any
				$styles[5] = the style definition itself, i.e. what's between the { }
			*/

			// Now loop through the styles found and act accordingly;

			// process TAG {...} & TAG1, TAG2,... {...} definitions only first by order of appearance
			foreach ($styles[1] as $i => $type) {
				if ($type=="" && $styles[3][$i]=="") {
					$styledefinition = trim($styles[5][$i]);
					$styletag = preg_replace("/ *, */", "|", trim($styles[2][$i])); //echo $styletag."<br />";
					$styleclass = "";
					// process TAG {...} and TAG1, TAG2 {...} but not TAG1 TAG2 {...} or #divname styles
					if (!preg_match("/ /", $styletag) && !preg_match("/#/", $styletag)) {
						$pattern = "!<(".$styletag.")([^>]*(?= /)|[^>]*)( /)?>!mi";
						$this->Body = preg_replace_callback ($pattern, 'facteur_addstyle' , $this->Body);
						$styles[6][$i]=1; // mark as injected inline
					}
				}
			}

			// append additional .CLASS {...} and TAG.CLASS {...} styling by order of appearance
			// important to do so after TAG {...} definitions, so that class attributes override TAG styles when needed
			foreach ($styles[1] as $i => $type) {
				if ($type!="." && $styles[3][$i]=="." ) {	// class definition for a specific tag
					$styledefinition = trim($styles[5][$i]);
					$styletag = trim($styles[2][$i]);
					$styleclass = trim($styles[4][$i]);
					$pattern = "!<(".$styletag.")([^>]* class\=['\"][^'\"]*".$styleclass."[^'\"]*['\"][^>]*(?= /)|[^>]* class\=['\"][^'\"]*".$styleclass."[^'\"]*['\"][^>]*)( />)?>!mi";
					$this->Body = preg_replace_callback ($pattern, 'facteur_addstyle' , $this->Body);
					$styles[6][$i]=1; // mark as injected inline

				} elseif ($type=="." && $styles[3][$i]=="" ) {	// general class definition for any tag
					$styledefinition = trim($styles[5][$i]);
					$styletag = "";
					$styleclass = trim($styles[2][$i]);
					$pattern = "!<(\w+)([^>]* class\=['\"]".$styleclass."['\"][^>]*(?= /)|[^>]* class\=['\"]".$styleclass."['\"][^>]*)( />)?>!mi";
					$this->Body = preg_replace_callback ($pattern, 'facteur_addstyle' , $this->Body);
					$styles[6][$i]=1; // mark as injected inline
				}
			}


			/* move all style declarations that weren't injected from <head> to a <body> <style> section,
			   including but not limited to:
			   - pseudo-classes like a:hover {...} as they can't be set inline
			   - declaration chains like UL LI {...}
			   - #divname {...}. These are not supported by email clients like Mac/Entourage anyway, it seems. */
			foreach ($styles[1] as $i => $type) {
				if ($styles[6][$i]=="") {
					// add a <style type="text/css"> section after <body> if there's isn't one yet
					if (preg_match ("!<body[^>]*>\s*<style!mi", $this->Body)==0) {
						$this->Body = preg_replace ("/(<body[^>]*>)/i", "\n\$1\n".'<style type="text/css">'."\n<!--\n-->\n</style>\n", $this->Body);
					}
					// append a copy of the pseudo-element declaration to that body style section
					$styledefinition = trim($styles[5][$i]);
					$styledefinition = preg_replace ("!\s+!mi", " ", $styledefinition ); // convert style definition to a one-liner (optional)
					$declaration = $styles[1][$i].trim($styles[2][$i]).$styles[3][$i].trim($styles[4][$i])." { ".$styledefinition." }";
					$this->Body = preg_replace ("!(<body[^>]*>\s*<style[^>]*>\s*<\!\-\-[^>]*)"."(\s*\-\->\s*</style>)!si", "\$1".$declaration."\n\$2", $this->Body);
					$styles[6][$i]= 2; // mark as moved to <style> section in <body>
				}
			}

			// remove stylesheet declaration(s) from <head> section (comment following line out if not wanted)
			//$this->Body = preg_replace ("!(<head>.*)<style type.*</style>(.*</head>)!si", "\$1\$2" , $this->Body);

			// check what styles have been injected
#			print_r($styles);
	
		}


		function ConvertirUtf8VersIso8859() {
			$this->Body		= str_replace('’',"'",$this->Body);
			$this->AltBody	= str_replace('’',"'",$this->AltBody);
			$this->CharSet	= 'iso-8859-1';
			$this->Body		= str_replace('charset=utf-8', 'charset=iso-8859-1', $this->Body);
			$this->Body		= utf8_decode($this->Body);
			$this->AltBody	= utf8_decode($this->AltBody);
			$this->Subject	= utf8_decode($this->Subject);
			$this->FromName	= utf8_decode($this->FromName);
		}

		function ConvertirAccents() {
			// tableau à compléter au fur et à mesure
			$cor = array(
							'à' => '&agrave;',
							'â' => '&acirc;',
							'ä' => '&auml;',
							'ç' => '&ccedil;',
							'é' => '&eacute;',
							'è' => '&egrave;',
							'ê' => '&ecirc;',
							'ë' => '&euml;',
							'î' => '&icirc;',
							'ï' => '&iuml;',
							'ò' => '&ograve;',
							'ô' => '&ocirc;',
							'ö' => '&ouml;',
							'ù' => '&ugrave;',
							'û' => '&ucirc;',
							'œ' => '&oelig;',
							'€' => '&euro;'
						);

			$this->Body = strtr($this->Body, $cor);
		}

	}

?>