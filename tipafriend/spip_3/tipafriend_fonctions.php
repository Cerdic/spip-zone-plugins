<?php
/**
 * Bibliothèque de fonctions du plugin
 *
 * Ce fichier n'est pas indiqué dans le fichier 'plugin.xml' car il n'est nécessaire que
 * pour le traitement du formulaire. Il est donc inclus par ce fichier.
 * @name 		Fonctions
 * @author 		Piero Wbmstr <http://www.spip-contrib.net/PieroWbmstr>
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package		Tip-a-friend
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction renvoyant les valeurs globales pour le contexte d'appel des patrons
 *
 * Surchargé par le traitement du formulaire.
 * @return	Array Tableau du contexte pour l'intégration des patrons de mail
 */
function tipafriend_contexte(){
	return array(
		'nom_site' => $GLOBALS['meta']['nom_site'],
		'adresse_site' => $GLOBALS['meta']['adresse_site'],
		'mail_titre' => '',
		'mail_charset' => $GLOBALS['meta']['charset'],
		'mail_patron' => tipafriend_config('patron'),
		'mail_patron_html' => tipafriend_config('patron_html'),
		'mail_lang' => $GLOBALS['meta']['langue_site'],
	);
}

/**
 * Fonction de formatage du titre du mail à 128 caractères
 *
 * => ?? !! une fonction SPIP fait ça si je n'm'abuse ! => à revoir
 *
 * @param	String	Le titre de depart
 * @return	String	Le titre d'arrivee
 */
function tipafriend_titrage($titre){
	$titre = substr($titre, 0, 128);
	return $titre;
}

/**
 * Constructeur des listes d'email
 *
 * Remplace tout caractère de séparaion (typiquement tout ce qui ne peut pas être dans une
 * adresse mail) par un point-virgule puis sépare aux points-virgules
 */
function tipafriend_multimails($str='') {
	$m = explode(';', str_replace( array(',', '/', ':', ' ') , ';', $str));
	$m = array_filter($m);
	return $m;
}

?>