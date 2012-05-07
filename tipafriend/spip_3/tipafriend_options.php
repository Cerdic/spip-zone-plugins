<?php
/**
 * Tip A Friend | Un plugin pour SPIP 2.0
 *
 * <b>PRÉSENTATION</b>
 *
 * Ce plugin gère un formulaire d'envoi d'une page par mail (ou de son adresse). Il fonctionne de façon
 * autonome (<i>pas besoin d'autres plugins</i>) mais certains paramètres sont configurables depuis
 * le plugin "CFG" ({@link http://www.spip-contrib.net/Config-CFG}) et certaines options, notamment l'envoi
 * d'un mail en version HTML, fonctionne grâce au plugin "Facteur" ({@link http://www.spip-contrib.net/Facteur}). 
 *
 * Il est réglé pour permettre l'envoi d'un mail d'information pour toute page d'un site SPIP : 
 * il va charger s'il peut les objets et ID-objets de SPIP (<i>articles, brèves ...</i>),
 * l'url et le titre de la page courante sinon.
 *
 * <b>UTILISATION</b>
 * 
 * La balise s'appelle comme ceci :
 * <code>
 * # TIPAFRIEND{ 
 *		type modèle , 
 *		url à transmettre , 
 *		adresse de l'expéditeur ,
 *		nom de l'expéditeur ,
 *		adresse(s) du(des) destinataire(s)
 * }
 * </code>
 * <br>où <b>tous les arguments sont optionnels!</b>
 * <br>L'utilisation classique n'utilisera que le premier argument.
 *
 * Pour rappel, un paramètre de balise SPIP se définit sur FALSE en indiquant '' en remplacement
 * de sa valeur.
 *
 * <b>PARAMÈTRES / FONCTIONNEMENT</b>
 *
 * Les paramètres ci-dessus correspondent :
 * - pour '<b>type modèle</b>' : au type de lien que vous voulez voir afficher en lieu et
 * place de la balise : 
 * -* <b>type normal</b> si vous ne précisez pas cet argument (<i>une image + un texte</i>) 
 * -* <b>type simple</b> si vous indiquez 'mini' (<i>une image seule</i>)
 * -* <b>un nom de squelette</b> pour l'utiliser en remplacement du modèle par défaut ;
 * - pour '<b>url</b>' : à l'adresse URL que vous souhaitez réellement envoyer, qui sera par
 * défaut l'adresse courante du navigateur ;
 * - pour '<b>adresse de l'expéditeur</b>' : à l'adresse mail qui sera pré-remplie dans le formulaire
 * (<i>s'il s'agit d'un utilisateur connecté, elle sera ajoutée automatiquement</i>) ;
 * - pour '<b>nom de l'expéditeur</b>' : au nom qui sera pré-rempli dans le formulaire
 * (<i>s'il s'agit d'un utilisateur connu il sera ajouté automatiquement</i>) ;
 * - pour '<b>adresse de destination</b>' : la ou les adresses mail qui seront pré-remplies dans le formulaire
 * à séparer par un point-virgule.
 *
 * La balise calcule automatiquement le type de boucles et l'ID de l'objet dans laquelle elle 
 * se trouve et intègre vos réglages personnels si vous disposez du plugin CFG (<i>cf. ci-dessous</i>).
 *
 * <b>NOTES</b>
 *
 * Le plugin inscrit des logs commençant par 'TIPAFRIEND' (muy original !)
 *
 * Un debugger est installé pour vous permettre de visualiser les différents paramètres passés
 * de fonction en fonction. Pour le voir, vous devez passer la gobale '_TIAPFRIEND_TEST' sur 'true'.
 * <br /><b>!! - À n'utiliser que pour vérifications !!</b>
 *
 * @name 		OptionsConfiguration
 * @author 		Piero Wbmstr <http://www.spip-contrib.net/PieroWbmstr>
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package		Tip-a-friend
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Mode debugger : !! NE PAS AUTORISER EN PRODUCTION !!
 * FALSE pour annuler | TRUE pour visualiser
 */
define('_TIPAFRIEND_TEST', 0);

/**
 * Dimensions par défaut de la popup
 * => ces dimensions ne sont pas prises en compte en fenêtre javascript (FancyBox, MediaBox)
 */
define('_TIPAFRIEND_POPUP_WIDTH', 640);
define('_TIPAFRIEND_POPUP_HEIGHT', 540);

/**
 * Config par défaut
 *
 * Il est préférable de les modifier en utilisant CONFIG
 * (donc ne pas modifier ici tant qu'à faire : on reste sûr de pouvoir récupérer des valeurs
 * correctes ...)
 */
$GLOBALS['TIPAFRIEND_DEFAULTS'] = array(
	// patron par defaut du corps du mail
	'patron' 				=> 'tipafriend_mail_default.html',
	// patron par defaut du corps du mail version HTML
	'patron_html' 			=> 'tipafriend_mail_default_html.html', 
	// squelette par defaut du formulaire
	'squelette' 			=> 'tip_a_friend.html',
	// modele du bouton (non configurable)
	'modele' 				=> 'tipafriend.html',
	// Afficher les en-tetes ?
	'header' 				=> 'oui',
	// Ajouter le CSS tipafriend ?
	'taf_css' 				=> 'oui',
	// Afficher le bouton 'Fermer' ?
	'close_button'			=> 'oui',
	// options ajoutees comme attributs au lien
	'options' 				=> '',
	// arguments ajoutes au lien
	'options_url' 				=> '',
	// fonctions JS standards (ouverture de popup) ?
	'javascript_standard' 	=> 'oui',
	// contenus des objets inclus au mail
	'contenu_objets' 		=> 'tout',
	// action du bouton "Fermer" du formulaire
	'form_reset'			=> 'window.close();window.opener.focus();',
);

/**
 * Nom du meta CFG de configuration
 */
define('TIPAFRIEND_CFGMETA', 'tipafriend');

/**
 * Nom de la page de documentation interne pour generation des liens
 */
define('TIPAFRIEND_DOC', 'tipafriend_documentation');

/**
 * URL de la page de documentation sur spip-contrib (documentation officielle)
 */
define('TIPAFRIEND_DOC_CONTRIB', 'http://www.spip-contrib.net/?article3638');

?>