<?php
/**
 * Créer un article à partir d'un fichier au format odt
 *
 * @author cy_altern
 * @license GNU/LGPL
 *
 * @package plugins
 * @subpackage odt2spip
 * @category import
 *
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Création de l'Array contenant les paramètres du futur article
 *
 * Le fichier content.xml a été extrait de l'archive .odt, et placé dans le dossier
 * temporaire propre à l'utilisateur courant. Un premier traitement est effectué
 * par cette fonction pour qu'il soit finalement transformé en texte utilisant les
 * balises SPIP. On tient compte de la présence des plugins enluminure_typo et
 * intertitre_enrichis. Les images sont extraites du document .odt et sont prêtes
 * à être insérées dans le futur article SPIP.
 *
 * @param string $rep_dezip Répertoire où est dezippé le fichier odt
 * @param string $fichier_source Chemin du fichier source (permet d’affecter un titre si le document n’en a pas trouvé)
 * @return array Couples (nom de champ d’article => valeur)
 * @throws \Exception
 */
function inc_odt2spip_generer_sortie($rep_dezip, $fichier_source = '') {
	// variables en dur pour xml en entree et xslt utilisee
	$xml_entre = $rep_dezip . 'content.xml';  // chemin du fichier xml a lire
	$xslt_texte = _DIR_PLUGIN_ODT2SPIP . 'inc/odt2spip.xsl'; // chemin de la xslt a utiliser pour le texte

	// determiner si le plugin enluminure_typo ou intertitres_enrichis est present & actif
	include_spip('inc/plugin');
	$Tplugins = liste_plugin_actifs();
	$intertitres_riches = (
		(array_key_exists('TYPOENLUMINEE', $Tplugins) or array_key_exists('INTERTITRESTDM', $Tplugins))
		? 'oui'
		: 'non'
	);

	// si il n'existe pas de titre:h dans le doc, on parametre ici la longueur max du paragraphe utilise pour remplacer
	$nb_caracteres_titre = 50;

	// faut il mettre les images en mode document?
	$type = (_request('mode_image') and _request('mode_image') == 'document') ? 'document' : 'image';
	$ModeImages = ($type == 'document' ? 'doc' : 'img');

	// récupérer la langue de publication + verifier la valeur envoyée
	$Tlangues = explode(',', $GLOBALS['meta']['langues_proposees']);
	$LanguePublication = (
		in_array(_request('lang_publi'), $Tlangues)
		? _request('lang_publi')
		: $GLOBALS['meta']['langue_site']
	);

	// date pour les champs date et date_modif
	$date_jour = date('Y-m-d H:i:s');

	// appliquer la transformation XSLT sur le fichier content.xml
	// on est php5: utiliser les fonctions de la classe XSLTProcessor
	// verifier que l'extension xslt est active
	if (!class_exists('XSLTProcessor')) {
		throw new \Exception(_T('odtspip:err_extension_xslt'));
	}
	$proc = new XSLTProcessor();

	// passage des parametres a la xslt
	$proc->setParameter(null, 'IntertitresRiches', $intertitres_riches);

	$xml = new DOMDocument();
	$xml->load($xml_entre);
	$xsl = new DOMDocument();
	$xsl->load($xslt_texte);
	$proc->importStylesheet($xsl); // attachement des regles xsl

	// lancer le parseur
	if (!$xml_sortie = $proc->transformToXml($xml)) {
		throw new \Exception(_T('odtspip:err_transformation_xslt'));
	}

	// construire l'array des parametres de l'article
	preg_match('/<titre>(.*?)<\/titre>/', $xml_sortie, $t);
	$Tarticle['titre'] = $t[1];
	preg_match('/<texte>(.*?)<\/texte>/s', $xml_sortie, $a);
	$Tarticle['texte'] = $a[1];
	$Tarticle['date_redac'] = '0000-00-00 00:00:00';
	$Tarticle['date'] = $Tarticle['date_modif'] = $date_jour;
	$Tarticle['lang'] = $LanguePublication;
	$Tarticle['statut'] = 'prop';
	$Tarticle['accepter_forum'] = 'non';

	// traitements complementaires du texte de l'article
	// remplacer les &gt; et &lt;
	$a_remplacer = array('&#60;', '&#62;', '&lt;', '&gt;', '"');
	$remplace = array('<', '>', '<', '>', "'");

	// si plugin TYPOENLUMINE est en version 3 (ou plus) utiliser la syntaxe {{{**titre 2}}} a la place de {2{titre 2}2}
	// (cf https://contrib.spip.net/odt2spip-creation-d-articles-a-partir-de-fichiers#forum435614)
	if (
		array_key_exists('TYPOENLUMINEE', $Tplugins)
		and intval(substr($Tplugins['TYPOENLUMINEE']['version'], 0, 1)) >= 3
	) {
		array_push($a_remplacer, '{2{', '}2}', '{3{', '}3}', '{4{', '}4}', '{5{', '}5}');
		array_push($remplace, '{{{**', '}}}', '{{{***', '}}}', '{{{****', '}}}', '{{{*****', '}}}');
	}

	$Tarticle['texte'] = str_replace($a_remplacer, $remplace, $Tarticle['texte']);

	// gerer la conversion des <math>Object X</math> => on delegue a /inc/odt2spip_traiter_mathml.php
	if (preg_match_all('/<math>(.*?)<\/math>/', $Tarticle['texte'], $match, PREG_PATTERN_ORDER) > 0) {
		include_spip('inc/odt2spip_traiter_mathml');
		foreach ($match[1] as $balise) {
			$fic_content = $rep_dezip . $balise . '/content.xml';
			// si le fichier /Object X/content.xml ne contient pas du mathML, virer la balise <math>
			if (substr_count(file_get_contents($fic_content), '<!DOCTYPE math:math') < 1) {
				$Tarticle['texte'] = str_replace('<math>' . $balise . '</math>', '', $Tarticle['texte']);
				continue;
			}
			// sinon faire la transfo xsl du contenu du fichier pour obtenir le LateX qu'on place dans la balise
			$Tarticle['texte'] = str_replace($balise, odt2spip_traiter_mathml($fic_content), $Tarticle['texte']);
		}
	}

	// virer les sauts de ligne multiples
	$Tarticle['texte'] = preg_replace('/([\r\n]{2})[ \r\n]*/m', '$1', $Tarticle['texte']);

	// si malgré toutes les magouille xslt la balise  <titre> est vide, mettre le nom du fichier odt
	if ($Tarticle['titre'] == '') {
		$Tarticle['titre'] = str_replace(array('_', '-', '.odt'), array(' ', ' ', ''), basename($fichier_source));
	}

	// traiter les images: dans tous les cas il faut les integrer dans la table documents
	$rep_pictures = $rep_dezip . 'Pictures/';

	// parametres de conversion de taille des images : cm -> px (en 96 dpi puisque c'est ce que semble utiliser Writer)
	$conversion_image = 96 / 2.54;

	preg_match_all('/<img([;a-zA-Z0-9\.]*)/', $Tarticle['texte'], $match, PREG_PATTERN_ORDER);
	if (@count($match) > 0) {
		if (!isset($odt2spip_retailler_img)) {
			$odt2spip_retailler_img = charger_fonction('odt2spip_retailler_img', 'inc');
		}
		if (!isset($ajouter_documents)) {
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		}
		$Timages = array();
		foreach ($match[1] as $ch) {
			$Tdims = explode(';;;', $ch);
			$img = $Tdims[0];
			// si l'extension du fichier image n'est pas jpg/gif/png virer la balise
			if (!in_array(strtolower(substr($img, -3)), array('jpg', 'gif', 'png'))) {
				$Tarticle['texte'] = str_replace($ch, '', $Tarticle['texte']);
			} elseif (file_exists($rep_pictures . $img)) {
				// retailler l'image en fct des parametres ;;;largeur;;;hauteur;;;
				$largeur = round($Tdims[1] * $conversion_image);
				$hauteur = round($Tdims[2] * $conversion_image);
				$odt2spip_retailler_img($rep_pictures . $img, $largeur, $hauteur);
				$id_document = $ajouter_documents(
					'new',
					array(
						array(
							'tmp_name' => $rep_pictures . $img,
							'name' => $img,
							'titrer' => 0,
							'distant' => 0,
							'type' => $type
						),
					),
					'',
					0,
					$type
				);
				if (
					$id_document
					and $id_img = intval($id_document[0])
					and $id_img == $id_document[0]
				) {
					$Timages[] = $id_img;
					// remplacer les noms de fichier par leur id_document dans les <imgLeNomDuFichier.jpg> du texte
					$Tarticle['texte'] = str_replace($ch, $id_img, $Tarticle['texte']);
				}
			}
		}

		// si les images doivent êtres intégrées en mode document, remplacer la balise <imgXY> par <docXY>
		if ($type == 'document') {
			preg_replace('/<img/', '<doc', $Tarticle['texte']);
		}

		// intégrer l'array des images dans les parametres de l'article
		// ce qui permettra de faire la liaison lorsqu'on aura l'id_article
		$Tarticle['Timages'] = $Timages;
	}

	// encodage des caracteres pour gerer aussi les SPIP 3 en ISO-8859-1
	// cf https://contrib.spip.net/odt2spip-creation-d-articles-a-partir-de-fichiers#forum466929
	if ($GLOBALS['meta']['charset'] != 'utf-8') {
		include_spip('inc/charsets');
		$Tarticle['texte'] = importer_charset($Tarticle['texte'], 'utf-8');
		$Tarticle['titre'] = importer_charset($Tarticle['titre'], 'utf-8');
	}

	return $Tarticle;
}
