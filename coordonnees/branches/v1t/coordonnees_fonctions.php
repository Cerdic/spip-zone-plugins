<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction privee mutualisee utilisee par les filtres logo_type_xx
 *
 * @param string $id
 *  Suffixe du xx du filtre logo_type_xx appelant ;
 *  Infixe du logo "images/type_xx_yy.???" a associer ;
 *  Correspond normalement a la classe vCard : adr, tel, email
 * @param string $val
 *  Valeur associee transmise par le filtre logo_type_xx ;
 *  Suffixe du logo "images/type_xx_yy.???" a associer ;
 *  Correspond au "type" de liaison de la la coordonnee (home, work, etc.)
 * @return string
 *  Balise <IMG> (s'il existe un logo "images/type_$id_$val") ou <ABBR> (sinon),
 * avec classes semantiques micro-format et traduction des valeurs cles RFC2426
 * @note
 *  http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html
 *  http://www.alsacreations.com/tuto/lire/1223-microformats-composes.html
 *
**/
function logo_type_($id='', $val='') {
	global $formats_logos;
	$type = strtolower($val);
	$lang = _T( ($id ? ('coordonnees:type_'. $id) : 'perso:type' )  . '_'.$type ); // les types libres sont traites par le fichier de langue perso
	foreach ($formats_logos as $format) { // inspiration source: ecrire/inc/chercher_logo.php
		$fichier = 'images/type'. ($id ? ('_' . $id) : '') . ($type ? ('_' . $type) : '') . '.' . $format;
		if ( $chemin = find_in_path($fichier) )
			$im = $chemin;
	}
	if ($im)
		return '<img class="type" src="' . $im . '" alt="' . $type . '" title="' . $lang . '" />';
	elseif ($type)
		return '<abbr class="type" title="' . $type . '">' . $lang . '</abbr>';
	else
		return '';
}

/**
 * Filtre d'affichage du type d'une adresse
 *
 * @param string $type_adresse
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X520/RFC2426 (section 3.2.1) : dom home intl parcel postal pref work
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function logo_type_adr($type_adresse) {
	return logo_type_('adr', $type_adresse);
}

/**
 * Filtre d'affichage du type d'un numero
 *
 * @param string $type_numero
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X500/RFC2426 (section 3.3.1) : bbs car cell fax home isdn modem msg pager pcs pref video voice work
 * CCITT.X520.1988/RFC6350 (section 6.4.1) : cell fax pager text textphone video voice x-... (iana-token)
 * ainsi que : dsl <http://fr.wikipedia.org/wiki/Digital_Subscriber_Line#Familles>
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_tel($type_numero) {
	return logo_type_('tel', $type_numero);
}

/**
 * Filtre d'affichage du type (format) d'un courriel
 *
 * @param string $format_email
 *  Valeur du format d'adresse de courriel (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * IANA/RFC2426 (section 3.3.2) : internet (SMTP) pref x400 (CCITT_F.400/ITU-T_X.400)
 * @note
 *  Ces formats definis precisement par des RFC sont associes a des "services" ;
 * et, pour Internet, certaines applications utilisent leur nom de service
 * proprietaire :
 * AOL (America On-Line) 1983--???? http://en.wikipedia.org/wiki/America_Online
 * AppleLink (Apple Computer's online service) 1986--1994 http://en.wikipedia.org/wiki/AppleLink
 * CIS (CompuServe Information Service) 1969--2009 http://en.wikipedia.org/wiki/CompuServe
 * eWorld (eWorld) 1994--1996 http://en.wikipedia.org/wiki/EWorld
 * IBMMail (IBM Mail) http://en.wikipedia.org/wiki/IBMMAIL
 * MCIMail (MCI Mail) 1983--2003 http://en.wikipedia.org/wiki/MCI_Mail
 * POWERSHARE (PowerShare : Apple Open Collaboration Environment) 1993-1996 http://en.wikipedia.org/w/Apple_Open_Collaboration_Environment
 * PRODIGY (Prodigy information service) 1984--???? http://en.wikipedia.org/wiki/Prodigy_%28online_service%29
 * TTMail (AT&T Mail) http://en.wikipedia.org/wiki/AT%26T_Internet_Services ?
 * TLX (Telex number), etc. http://www.perlmonks.org/bare/?node_id=168011
 * Bien que non geres de base, ils peuvent etre utilise en surchargeant
 * "saisie/type_email" puis en rajoutant le logo (Compuserve-GIF/JPEG/PNG) dans
 * "images/" et en donnant l'intitule dans "lang/perso_??.php" (ces 3 repertoires
 * sont a creer dans votre dossier "squelettes" !)
 *   Bien entendu, garder a l'esprit que ces valeurs hors standards ajoutent des
 * problemes d'interoperabilite et des disfonctionnements meme dans ces solutions
 * proprios http://www.abc.se/~m8827/prog/mailserv.html
 *   Mais, in fine, seuls SMTP et X.400 sont largement supportes...
 * http://www.powershellcommunity.org/Forums/tabid/54/aft/5249/Default.aspx
 * http://www.msexchange.org/articles-tutorials/exchange-server-2010/management-administration/x400-addresses-exchange-2010-part1.html
 * http://www.isode.com/whitepapers/ic-6036.html
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_email($format_email) {
	return logo_type_('email', $format_email);
}

/**
 * Filtre d'affichage du type (usage) d'un courriel
 *
 * @param string $type_email
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X520+RFC5322/RFC6350 (section 6.4.2) : home (perso) intl work (pro)
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_mel($type_email) {
	return logo_type_('mel', $type_email);
}

/**
 * filtre d'affichage du type d'une messagerie de presence
 *
 * @param string $type_messagerie
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X520+RFC5322/RFC6350 (section 6.4.3) : pref
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_impp($type_messagerie) {
	return logo_type_('impp', $type_messagerie);
}

/**
 * filtre d'elements du lien hypertexte d'une adresse de courriel
 *
 * @param string $adresse
 *  Valeur de l'email
 * @param string $format
 *  Type de formatage : internet|x400
 * @return string
 *  Attributs CLASS et HREF de la balise A
**/
function filtre_lien_type_mel($adresse, $format='smtp') {
	$adresse = trim($adresse);
	$prefixe = ''; // defaut
	switch (strtolower($format)) {
		case 'smtp':
		case 'inet':
		case 'internet':
			break;
		case 'x400':
		case 'x500': // encapsule "X.400" pour le courrier electronique
		case 'ex': // Exchange : synonyme de X.500
		case 'ad': // Active Dircetory : synonyme de X.500
			$delimiteur = ctype_alpha($adresse[0])?';':$adresse[0]; // X.400 recommande le delimiteur ';' ...mais on peut en utiliser un autre comme '/' a condition de le specifier au debut... cf. note 4 de la RFC 1685
			$prefixe = 'IMCEAEX-'.($delimiteur==';'?'_':''); // http://www.outlookforums.com/threads/8929-how-can-i-hide-my-x400-address-going-senders-exchange-2007-when-person-hidden-gal/
			$adresse = str_replace($delimiteur, '_', $adresse); // http://www.experts-exchange.com/Software/Server_Software/Email_Servers/Exchange/A_9650-NDRs-and-the-legacyExchangeDN.html
			break;
		case 'ldap': // "LDAP" est l'equivalent "X.500" sur IP (creer initialement pour)
			$prefixe = 'IMCEAEX-_'; // cf "X.500"
			$adresse = str_replace(',', '_', $adresse); // cf "X.500"
			break;
		case 'ccmail':
			$adresse = str_replace(' at ', '@', $adresse);
			break;
		case 'ms':
			$last_slash = strrpos($adresse, '/');
			$adresse = substr($adresse, $last_slash+1) .'@'. str_replace('/', '.', substr($adresse, 0, $last_slash)); // correspond probablement au UPN http://msdn.microsoft.com/en-us/library/windows/desktop/aa380525%28v=vs.85%29.aspx
			break;
		default:
			$prefixe = '';
	}
	return  " class='email' href='mailto:$prefixe$adresse' ";
}

/**
 * Interdire l'acces a une page si on n'a pas l'autorisation
 *
 * @param bool $autorisation
 *   Resultat de l'appel a autoriser('UneAction', 'coordonnees')
 * @return void
 *   Affichage de la page d'acces refuse/interdit si l'autorisation est a FALSE
 * @note
 *   C'est une reprise (r67625) du filtre "sinon_interdire_acces" de Bonux
**/
function coordonnees_interdit_sinon($autorisation) {
	if (!$autorisation) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
}

?>