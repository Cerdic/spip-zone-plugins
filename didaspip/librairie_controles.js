/* auteur: Jean-Benoît Murat */
/* Date de création: 07/10/2004 */

/*     Fonctions de contrôle de champs de formulaires coté client   */

//Diverses expressions régulières utiles

// Expressions régulières de test de longueur
var regExpEmpty=/^$/g;					// Accepte une chaine vide
var regExp8Chars=/^[0-9a-zA-Z]{8,}$/g;			// Accepte une chaine d'au moins 8 carctères alphanumeriques (pour un mot de passe par exemple).

// Expressions régulières de test de type de caractère
var regExpAlphanumeric=/[0-9a-zA-Z]+/g;			// Accepte une chaine alphanumérique
var regExpAlphanumericWithWhitespace=/[0-9a-zA-Z ]+/g;	// Accepte une chaine alphanumérique + ' '
var regExpAlphabetic=/[a-zA-Z]+/g;			// Accepte une chaine alphabétique
var regExpNumeric=/[0-9]+/g;				// Accepte une chaine numérique

// Expressions régulières de test de type
var regExpInt=/^[0-9]+$/g;				// Accepte une chaine de type 'int'
var regExpDouble=/^[-+]?[0-9]+(\.[0-9]+)?$/g;		// Accepte une chaine de type 'double'
var regExpFloat=/^[-+]?[0-9]+(\.[0-9]+)?([eE][-+]?[0-9]+)?$/g;	// Accepte une chaine de type 'float'
var regExpTime=/^([01][0-9]|2[0123])\:([012345][0-9])(\:([012345][0-9])(.([0-9]{3})+)?)?$/g;		 // Accepte une chaine de type 'time'. Ex : 12:51 ou 21:45:35.654
var regExpFrenchDate=/^(0[1-9]|[12][0-9]|3[01])[\- \/\.](0[1-9]|1[012])[\- \/\.](19|20)\d\d$/g;  // date au format jj/mm/aaaa ou jj-mm-aaaa ou jj mm aaaa ou jj.mm.aaaa avec aaaa compris entre 1900 et 2099.
var regExpEnglishDate=/^(19|20)\d\d[\- \/\.](0[1-9]|1[012])[\- \/\.](0[1-9]|[12][0-9]|3[01])$/g; // idem ci-dessus mais format anglais (Ex : aaaa/mm/jj)
var regExpBoolean=/^true|false$/g;			// Accepte une chaine de type 'boolean'

// Expressions régulières de test de types administratifs français
var regExpCodePostal=/^([A-Z]+[A-Z]?\-)?[0-9]{1,2} ?[0-9]{3}$/g;							// Accepte une chaine de type 'code postal'. Ex : F-33370 ou 33 370 ou 33370 ou F-1 370
var regExpTelephoneFixe=/^(01|02|03|04|05)[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}$/g;		// Accepte un numero de téléphone de type 'fixe'. Ex : 01.34.12.52.30 ou 0134125230
var regExpTelephonePortable=/^(06)[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}$/g;			// Accepte un numero de téléphone de type 'portable'.
var regExpTelephoneNational=/^(0[1234568])[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}$/g;		// Accepte un numero de téléphone de type 'national' y compris numéros en '08'.
var regExpTelephoneInternational=/^(\+[0-9]{2})[ \.\-]?[0-9][ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{2}$/g;	// Accepte un numero de téléphone de type 'international'. Ex : (+33) 1 34 12 52 30

var regExpNumeroSecuriteSociale=/^[12][ \.\-]?[0-9]{2}[ \.\-]?(0[1-9]|[1][0-2])[ \.\-]?([0-9]{2}|2A|2B)[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{2}$/g; // Accepte un numero de sécurité sociale français. Ex : 1 85 34 33 354 450 45

var regExpTVAIntracommunautaire=/^[A-Z]{2}[ \.\-]?[0-9]{2}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{3}$/g;		// Accepte un numero de TVA Intra-communautaire. Ex : FR 02 254 254 254
var regExpNumeroSiren=/^[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{3}$/g;							// Accepte un numero SIREN. Ex : 254 254 254
var regExpNumeroSiret=/^[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{5}$/g;					// Accepte un numero SIRET. Ex : 254 254 254 12345
var regExpCodeApe=/^[0-9]{2}[ \.\-]?[0-9]{1} ?[a-zA-Z]$/g;								// Accepte un code APE. Ex : 25.4Z

// Expressions régulières de test de types liés à internet

var regExpEmailAdress=/^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/g;										// Accepte une adresse email. Ex : toto@toto.com
var regExpIpAdress=/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/g;	// Accepte une adresse ip. Ex : 192.168.0.1
var regExpDomainName=/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/g;													// Accepte un nom de domaine. Ex : toto.com
var regExpUrl=/^(((ht|f)tp(s?))\:\/\/)?(([a-zA-Z0-9]+([@\-\.]?[a-zA-Z0-9]+)*)(\:[a-zA-Z0-9\-\.]+)?@)?(www.|ftp.|[a-zA-Z]+.)?[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,})(\:[0-9]+)?\/?/g;			// Accepte une url ftp, http ou https, avec ou sans login/mot de passe, avec ou sans numero de port. Ex : http://www.toto.com, ftp://toto:toto@ftp.toto.com:21/

var regExpHexColor=/^#[0-9A-Fa-f]{6}$/g; // Accepte une couleur hexadécimale

//Les deux fonction suivantes servent à identifier si une chaine de caractère est compatible ou non avec une expression régulière passée en paramètre
function matchRegularExpression(valeur, regularExpression)
{
	var resultat = valeur.match(regularExpression);
	if(resultat!=null && resultat.length==1) return true;
	else return false;
}

function doesntMatchRegularExpression(valeur, regularExpression)
{
	if(matchRegularExpression(valeur, regularExpression)) return false;
	else return true;
}

/*
les fonctions de contrôle suivantes prennent toutes comme argument la valeur de l'attribut "value" d'un champ de formulaire de type "text" ou "password"
*/
function isEmpty(valeur)
{
	return matchRegularExpression(valeur, regExpEmpty);
}

function isNotEmpty(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpEmpty);
}

function isNot8CharsLength(valeur)
{
	return doesntMatchRegularExpression(valeur, regExp8Chars);
}

function isNotAlphanumeric(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpAlphanumeric);
}

function isNotAlphanumericWithWhitespace(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpAlphanumericWithWhitespace);
}

function isNotAlphabetic(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpAlphabetic);
}

function isNotNumeric(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpNumeric);
}

function isNotInt(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpInt);
}

function isNotDouble(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpDouble);
}

function isNotFloat(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpInt);
}

function isNotBoolean(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpBoolean);
}

function isNotTime(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpTime);
}

function isNotDate(valeur, mode)
{
	switch (mode)
	{
		case "fr" : 
			return doesntMatchRegularExpression(valeur, regExpFrenchDate);
			break;
		case "en" :
			return doesntMatchRegularExpression(valeur, regExpEnglishDate);
			break;
		default : 
			return doesntMatchRegularExpression(valeur, regExpFrenchDate);
			break;
	}
}

function isNotCodePostal(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpCodePostal);
}

function isNotTelephone(valeur, mode)
{
	switch (mode)
	{
		case "fixe" : 
			return doesntMatchRegularExpression(valeur, regExpTelephoneFixe);
			break;
		case "port" :
			return doesntMatchRegularExpression(valeur, regExpTelephonePortable);
			break;
		case "nati" :
			return doesntMatchRegularExpression(valeur, regExpTelephoneNational);
			break;
		case "inte" :
			return doesntMatchRegularExpression(valeur, regExpTelephoneInternational);
			break;
		default : 
			return doesntMatchRegularExpression(valeur, regExpTelephoneNational);
			break;
	}
}

function isNotNumeroSecuriteSociale(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpNumeroSecuriteSociale);
}

function isNotTVAIntracommunautaire(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpTVAIntracommunautaire);
}

function isNotNumeroSiren(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpNumeroSiren);
}

function isNotNumeroSiret(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpNumeroSiret);
}

function isNotCodeApe(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpCodeApe);
}

function isNotEmailAdress(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpEmailAdress);
}

function isNotIpAdress(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpIpAdress);
}

function isNotDomainName(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpDomainName);
}

function isNotUrl(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpUrl);
}

function isNotHexColor(valeur)
{
	return doesntMatchRegularExpression(valeur, regExpHexColor);
}

