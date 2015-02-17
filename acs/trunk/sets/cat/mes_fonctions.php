<?php
/*
             ACS
         (Plugin Spip)
         Squelette Cat
    http://acs.geomaticien.org

Copyright Daniel FAIVRE, 2007-2015
Copyleft: licence GPL - Cf. LICENCES.txt in acs plugin dir
*/
define('_SURLIGNE_RECHERCHE_REFERERS',true);
if (isset($_REQUEST['recherche'])) {
	$_GET['var_recherche'] = $_REQUEST['recherche'];
}

$GLOBALS['ouvre_ref'] = '';
$GLOBALS['ferme_ref'] = '';
$GLOBALS['ouvre_note'] = '';
$GLOBALS['ferme_note'] = ': ';

function sans_guillemets($texte) {
	 $texte = str_replace('"', '', $texte);
	 return $texte;
}

function coupe($texte, $taille=50, $suite) {
	if ($taille < 0)
		return $texte;
	$texte = textebrut($texte); // filtre spip qui supprime les tags HTML
	$texte = couper($texte, $taille);
	$texte = PtoBR(propre($texte));
	$texte = str_replace('&nbsp;(...)', $suite, $texte);
	return $texte;
}


// filtre askeywords: transforme un texte en liste de mots-clés pour meta-tag keywords
// exemple d'usage: [<meta name="keywords" content="(#TITRE|askeywords)" />]
function askeywords($texte) {
	$texte = sans_guillemets($texte);
	 $notkeys = _T('acs:fond_meta_not_keywords');

	 $notkeys = explode(',', $notkeys);
	 // Transforme tous les mots inutilisables comme keywords en expression régulière "mot entier", insensible à la casse
	 foreach ($notkeys as $key=>$notkey ) {
			$notkeys[$key] = '/\b'.$notkey.'\b/u';
	 }
	 $texte = strtolower(textebrut($texte));
	 // Suppression des mots qui ne conviennent pas comme keywords (liste meta_not_keywords du fichier de langue fond_xx.php)
	 $texte = preg_replace($notkeys, '', $texte);
	 // Suppression de la ponctuation et des espaces de début et de fin
	 $texte = preg_replace(array('/[;:,.?!\']/', '/(\s+$)/', '/(^\s+)/'), '', $texte);
	 // remplacement des espaces restants par des virgules
	 $texte = preg_replace('/(\s+)/', ',', $texte);
	 return $texte;
}

/*
 *	 +----------------------------------+
 *		Nom du Filtre :	cm (crypt_mail)
 *	 +----------------------------------+
 *		Date : dimanche 6 juillet 2003
 *		Auteur :	Jean-Pierre KUNTZ
 *				alias Coyote
 *	 +-------------------------------------+
 *		Fonctions de ce filtre :
 *		 Crypter une chaînee de texte (email, URL)
 *		 sans en empêcher l'affichage à l'écran
 *		 ni l'utilisation par un logiciel de messagerie
 *	 +-------------------------------------+
 *
 *	 exemple d'utilisation dans un squelette :
 *
 *	 <a href="mailto:[(#EMAIL|cm)]">[(#EMAIL|cm)]</a>
 *
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=197
*/


function cm($texte) {
	$s = "";
	for ($i=0; $i < strlen($texte); $i++) {
		$s.="&#".ord($texte{$i}).";";
	}
	return $s;
}

/*
 * detection automatique de la langue selon le navigateur
 * necessite [(#CONFIG{langues_utilisees}|detecte_langue{#SELF})]
 * en DEBUT de squelette, AVANT toute sortie html (cookie)
 * 
 * cf. http://www.spip-contrib.net/Multilinguisme-Non-structure
 */
function detecte_langue($langues, $url_encours) {
	if (!$_COOKIE['spip_lang']) {
		// récupérer le tableau des langues utilisées dans le site
			$tab_langues = explode(",",$langues);
		// traiter (dans l'ordre !) les langues acceptées par le navigateur
			$Tlangues_brutes = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach ($Tlangues_brutes as $l) {
				// ne garder que le code de pays à 2 lettres pour les valeurs de langues
				// (exemple de code langue renvoyé par le navigateur : 
				// es,de-de;q=0.9,ca;q=0.7,fr-fr;q=0.6,en;q=0.4,fr;q=0.3,en-us;q=0.1)
					if (strpos($l, '-') !== false) {
					$Tl = explode('-', $l);
					$l = $Tl[0];
					}
					if (strpos($l, ';') !== false) {
	 				$Tl = explode(';', $l);
					$l = $Tl[0];
					}
				// si la langue en cours est utilisée dans le site, converser() vers cette langue
					if (in_array($l, $tab_langues)) {
					include_spip('inc/headers');
					redirige_par_entete('spip.php?action=converser&redirect='.$url_encours.'&var_lang='.$l);
					break;
					}
			}
	}
}
?>