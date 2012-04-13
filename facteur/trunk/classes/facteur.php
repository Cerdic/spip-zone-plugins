<?php
/*
 * Plugin Facteur 2
 * (c) 2009-2011 Collectif SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');
include_spip('inc/texte');
include_spip('inc/filtres');

if (!class_exists('PHPMailer')) {
	include_spip('phpmailer-php5/class.phpmailer');
	include_spip('phpmailer-php5/class.smtp');
}

include_spip('facteur_fonctions');

class Facteur extends PHPMailer {

	function Facteur($email, $objet, $message_html, $message_texte) {

		if (defined('_FACTEUR_DEBUG_SMTP')) {
			$this->SMTPDebug = _FACTEUR_DEBUG_SMTP ;
		}
		if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui'
		  AND $GLOBALS['meta']['facteur_adresse_envoi_email'])
			$this->From = $GLOBALS['meta']['facteur_adresse_envoi_email'];
		else
			$this->From = (isset($GLOBALS['meta']["email_envoi"]) AND $GLOBALS['meta']["email_envoi"])?
				$GLOBALS['meta']["email_envoi"]
				:$GLOBALS['meta']['email_webmaster'];

		// Si plusieurs emails dans le from, pas de Name !
		if (strpos($this->From,",")===false){
			if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui'
			  AND $GLOBALS['meta']['facteur_adresse_envoi_nom'])
				$this->FromName = $GLOBALS['meta']['facteur_adresse_envoi_nom'];
			else
				$this->FromName = strip_tags(extraire_multi($GLOBALS['meta']['nom_site']));
		}

		$this->CharSet = "utf-8";
		$this->Mailer = 'mail';
		$this->Subject = unicode_to_utf_8(charset2unicode($objet,$GLOBALS['meta']['charset']));

		//Pour un envoi multiple de mail, $email doit être un tableau avec les adresses.
		if (is_array($email)) {
			foreach ($email as $cle => $adresseMail) {
				if (!$this->AddAddress($adresseMail))
					spip_log("Erreur AddAddress $adresseMail : ".print_r($this->ErrorInfo,true),'facteur');
			}
		}
		else
			if (!$this->AddAddress($email))
				spip_log("Erreur AddAddress $email : ".print_r($this->ErrorInfo,true),'facteur');

		if (!empty($GLOBALS['meta']['facteur_smtp_sender'])) {
			$this->Sender = $GLOBALS['meta']['facteur_smtp_sender'];
			$this->AddCustomHeader("Errors-To: ".$this->Sender);
		}

		if (!empty($GLOBALS['meta']['facteur_cc'])) {
			$this->AddCC( $GLOBALS['meta']['facteur_cc'] );
		}
		if (!empty($GLOBALS['meta']['facteur_bcc'])) {
			$this->AddBCC( $GLOBALS['meta']['facteur_bcc'] );
		}
		
		if (isset($GLOBALS['meta']['facteur_smtp']) AND $GLOBALS['meta']['facteur_smtp'] == 'oui') {
			$this->Mailer	= 'smtp';
			$this->Host 	= $GLOBALS['meta']['facteur_smtp_host'];
			$this->Port 	= $GLOBALS['meta']['facteur_smtp_port'];
			if ($GLOBALS['meta']['facteur_smtp_auth'] == 'oui') {
				$this->SMTPAuth = true;
				$this->Username = $GLOBALS['meta']['facteur_smtp_username'];
				$this->Password = $GLOBALS['meta']['facteur_smtp_password'];
			}
			else {
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
			$this->UrlsAbsolues();
		}
		if (!empty($message_texte)) {
			$message_texte = unicode_to_utf_8(charset2unicode($message_texte,$GLOBALS['meta']['charset']));
			if (!$this->Body) {
				$this->IsHTML(false);
				$this->Body = $message_texte;
			}
			else {
				$this->AltBody = $message_texte;
			}
		}

		if ($GLOBALS['meta']['facteur_filtre_iso_8859'])
			$this->ConvertirUtf8VersIso8859();

	}
	
	/*
	 * Transforme du HTML en texte brut, mais proprement, c'est-à-dire en essayant
	 * de garder les titrages, les listes, etc
	 *
	 * @param string $html Le HTML à transformer
	 * @return string Retourne un texte brut formaté correctement
	 */
	function html2text($html){
		// On remplace tous les sauts de lignes par un espace
		$html = str_replace("\n", ' ', $html);
		
		// Supprimer tous les liens internes
		$texte = preg_replace("/\<a href=['\"]#(.*?)['\"][^>]*>(.*?)<\/a>/ims", "\\2", $html);
	
		// Supprime feuille style
		$texte = preg_replace(";<style[^>]*>[^<]*</style>;i", "", $texte);
	
		// Remplace tous les liens	
		$texte = preg_replace("/\<a[^>]*href=['\"](.*?)['\"][^>]*>(.*?)<\/a>/ims", "\\2 (\\1)", $texte);
	
		// Les titres
		$texte = preg_replace(";<h1[^>]*>;i", "\n= ", $texte);
		$texte = str_replace("</h1>", " =\n\n", $texte);
		$texte = preg_replace(";<h2[^>]*>;i", "\n== ", $texte);
		$texte = str_replace("</h2>", " ==\n\n", $texte);
		$texte = preg_replace(";<h3[^>]*>;i", "\n=== ", $texte);
		$texte = str_replace("</h3>", " ===\n\n", $texte);
		
		// Une fin de liste
		$texte = preg_replace(";</(u|o)l>;i", "\n\n", $texte);
		
		// Une saut de ligne *après* le paragraphe
		$texte = preg_replace(";<p[^>]*>;i", "\n", $texte);
		$texte = preg_replace(";</p>;i", "\n\n", $texte);
		// Les sauts de ligne interne
		$texte = preg_replace(";<br[^>]*>;i", "\n", $texte);
	
		//$texte = str_replace('<br /><img class=\'spip_puce\' src=\'puce.gif\' alt=\'-\' border=\'0\'>', "\n".'-', $texte);
		$texte = preg_replace (';<li[^>]*>;i', "\n".'- ', $texte);
	
	
		// accentuation du gras
		// <b>texte</b> -> **texte**
		$texte = preg_replace (';<b[^>]*>;i','**' ,$texte);
		$texte = str_replace ('</b>','**' ,$texte);
	
		// accentuation du gras
		// <strong>texte</strong> -> **texte**
		$texte = preg_replace (';<strong[^>]*>;i','**' ,$texte);
		$texte = str_replace ('</strong>','**' ,$texte);
	
	
		// accentuation de l'italique
		// <em>texte</em> -> *texte*
		$texte = preg_replace (';<em[^>]*>;i','/' ,$texte);
		$texte = str_replace ('</em>','*' ,$texte);
		
		// accentuation de l'italique
		// <i>texte</i> -> *texte*
		$texte = preg_replace (';<i[^>]*>;i','/' ,$texte);
		$texte = str_replace ('</i>','*' ,$texte);
	
		$texte = str_replace('&oelig;', 'oe', $texte);
		$texte = str_replace("&nbsp;", " ", $texte);
		$texte = filtrer_entites($texte);
	
		// On supprime toutes les balises restantes
		$texte = supprimer_tags($texte);
	
		$texte = str_replace("\x0B", "", $texte); 
		$texte = str_replace("\t", "", $texte) ;
		$texte = preg_replace(";[ ]{3,};", "", $texte);
	
		// espace en debut de ligne
		$texte = preg_replace("/(\r\n|\n|\r)[ ]+/", "\n", $texte);
	
		//marche po
		// Bring down number of empty lines to 4 max
		$texte = preg_replace("/(\r\n|\n|\r){3,}/m", "\n\n", $texte);
	
		//saut de lignes en debut de texte
		$texte = preg_replace("/^(\r\n|\n|\r)*/", "\n\n", $texte);
		//saut de lignes en debut ou fin de texte
		$texte = preg_replace("/(\r\n|\n|\r)*$/", "\n\n", $texte);
	
		// Faire des lignes de 75 caracteres maximum
		//$texte = wordwrap($texte);
	
		return $texte;
	}
	
	/**
	 * Transformer les urls des liens et des images en url absolues
	 * sans toucher aux images embarquees de la forme "cid:..."
	 */
	function UrlsAbsolues(){
		include_spip('inc/filtres_mini');
		if (preg_match_all(',(<(a|link)[[:space:]]+[^<>]*href=["\']?)([^"\' ><[:space:]]+)([^<>]*>),imsS',
		$this->Body, $liens, PREG_SET_ORDER)) {
			foreach ($liens as $lien) {
				if (strncmp($lien[3],"cid:",4)!==0){
					$abs = url_absolue($lien[3], $base);
					if ($abs <> $lien[3] and !preg_match('/^#/',$lien[3]))
						$this->Body = str_replace($lien[0], $lien[1].$abs.$lien[4], $this->Body);
				}
			}
		}
		if (preg_match_all(',(<(img|script)[[:space:]]+[^<>]*src=["\']?)([^"\' ><[:space:]]+)([^<>]*>),imsS',
		$this->Body, $liens, PREG_SET_ORDER)) {
			foreach ($liens as $lien) {
				if (strncmp($lien[3],"cid:",4)!==0){
					$abs = url_absolue($lien[3], $base);
					if ($abs <> $lien[3])
						$this->Body = str_replace($lien[0], $lien[1].$abs.$lien[4], $this->Body);
				}
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

		preg_match_all('/["\'](([^"\']+)\.('.implode('|', $extensions).'))([?][^"\']+)?["\']/Ui', $this->Body, $images, PREG_SET_ORDER);

		$html_images = array();
		foreach($images as $im){
			if (!preg_match(",^[a-z0-9]+://,i",$im[1])
			 AND ($src = $im[1].$im[4])
			 AND (
			      file_exists($f=$im[1]) // l'image a ete generee depuis le meme cote que l'envoi
			      OR (_DIR_RACINE AND file_exists($f=_DIR_RACINE.$im[1])) // l'image a ete generee dans le public et on est dans le prive
			      OR (!_DIR_RACINE AND strncmp($im[1],"../",3)==0 AND file_exists($f=substr($im[1],3))) // l'image a ete generee dans le prive et on est dans le public
			     )
			 AND !isset($html_images[$src])){

				$extension = strtolower($im[3]);
				$header_extension = $image_types[$extension];
				$cid = md5($f); // un id unique pour un meme fichier
				// l'ajouter si pas deja present (avec un autre ?...)
				if (!in_array($cid,$html_images))
					$this->AddEmbeddedImage($f, $cid, basename($f),'base64',$header_extension);
				$this->Body = str_replace($src, "cid:$cid", $this->Body);
				$html_images[$src] = $cid; // marquer l'image comme traitee, inutile d'y revenir
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

			}
			elseif ($type=="." && $styles[3][$i]=="" ) {	// general class definition for any tag
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


	function safe_utf8_decode($text,$mode='texte_brut') {
		if (!is_utf8($text))
			return ($text);

		if (function_exists('iconv') && $mode == 'texte_brut') {
			$text = str_replace('’',"'",$text);
			$text = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $text);
			return str_replace('&#8217;',"'",$text);
		}
		else {
			if ($mode == 'texte_brut') {
				$text = str_replace('’',"'",$text);
			}
			$text = unicode2charset(utf_8_to_unicode($text),'iso-8859-1');
			return str_replace('&#8217;',"'",$text);
		}
	}

	function ConvertirUtf8VersIso8859() {
		$this->CharSet	= 'iso-8859-1';
		$this->Body		= str_ireplace('charset=utf-8', 'charset=iso-8859-1', $this->Body);
		$this->Body		= $this->safe_utf8_decode($this->Body,'html');
		$this->AltBody	= $this->safe_utf8_decode($this->AltBody);
		$this->Subject	= $this->safe_utf8_decode($this->Subject);
		$this->FromName	= $this->safe_utf8_decode($this->FromName);
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
	public function Send() {
		ob_start();
		$retour = parent::Send();
		$error = ob_get_contents();
		ob_end_clean();
		if( !empty($error) ) {
			spip_log("Erreur Facteur->Send : $error",'facteur.err');
		}
		return $retour;
	}
	public function AddAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {
		ob_start();
		$retour = parent::AddAttachment($path, $name, $encoding, $type);
		$error = ob_get_contents();
		ob_end_clean();
		if( !empty($error) ) {
			spip_log("Erreur Facteur->AddAttachment : $error",'facteur.err');
		}
		return $retour;
	}
	public function AddReplyTo($address, $name = '') {
		ob_start();
		$retour = parent::AddReplyTo($address, $name);
		$error = ob_get_contents();
		ob_end_clean();
		if( !empty($error) ) {
			spip_log("Erreur Facteur->AddReplyTo : $error",'facteur.err');
		}
		return $retour;
	}
	public function AddBCC($address, $name = '') {
		ob_start();
		$retour = parent::AddBCC($address, $name);
		$error = ob_get_contents();
		ob_end_clean();
		if( !empty($error) ) {
			spip_log("Erreur Facteur->AddBCC : $error",'facteur.err');
		}
		return $retour;
	}
	public function AddCC($address, $name = '') {
		ob_start();
		$retour = parent::AddCC($address, $name);
		$error = ob_get_contents();
		ob_end_clean();
		if( !empty($error) ) {
			spip_log("Erreur Facteur->AddCC : $error",'facteur.err');
		}
		return $retour;
	}
}

?>
