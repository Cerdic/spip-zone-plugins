<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function inc_plugonet_formater($traitement, $erreurs, $duree, $affichage_complet='true') {
	// Nombre de fichiers traites
	$nb_fichiers = count($erreurs);	

	// Les resultats sont donnes sous deux formes :
	// -- une synthetique, le message global affiche en haut du formulaire : ce message resument
	//    le nombre de fichiers traites et les compteurs d'erreurs et de notices
	// -- une detaillee, affichee en bas du formulaire avec le resultat global de chaque fichier
	//    et la liste des erreurs eventuelles
	$nb_erreurs = array(
					'lecture_pluginxml' => 0,
					'lecture_paquetxml' => 0,
					'information_pluginxml' => 0,
					'validation_pluginxml' => 0,
					'validation_paquetxml' => 0);
	$nb_notices = array('validation_pluginxml' => 0);
	$resume = $analyse = '';
	$texte = array('erreur' => '', 'notice' => '', 'succes' => '');
	foreach ($erreurs as $_pluginxml => $_erreurs) {
		$erreur_trouvee = false;
		foreach ($_erreurs as $_type => $_valeur) {
			if ($_valeur) {
				// On determine le type d'erreur et le message
				if ($_type !== 'validation_pluginxml') {
					// Ce sont toujours des cas d'erreur
					$message = 'plugonet:message_nok_' . $_type;
					$bloc = 'erreur';
				}
				else {
					// Le cas validation plugin.xml est :
					// - une notice pour la generation du paquet.xml
					// - une erreur pour la verification du plugin.xml
					$message = ($traitement == 'verification_pluginxml') ? 'plugonet:message_nok_' . $_type : '';
					$bloc = ($traitement == 'generation_paquetxml') ? 'notice' : 'erreur';
				}
				
				// On formate le texte d'erreur ou de notice
				$texte[$bloc] .= formater_un_plugin(
										$message,
										$_pluginxml,
										$affichage_complet,
										($_valeur !== true ? $_valeur : null));
					
				// On a bien trouve une erreur pour ce fichier xml
				if ($bloc == 'erreur')
					$nb_erreurs[$_type] += 1;
				else
					$nb_notices[$_type] += 1;
				$erreur_trouvee = true;
			}
		}
		// Si on a pas trouve d'erreur on affiche un message de succes pour le plugin
		if (!$erreur_trouvee)
				$texte['succes'] .= formater_un_plugin('', $_pluginxml, $affichage_complet);
	}

	// Construction du message de synthese
	// -- on determine les compteurs globaux
	$nb_nok = array_sum($nb_erreurs);
	$nb_ok = $nb_fichiers - $nb_nok;
	// -- construction du detail sur chaque compteur d'erreurs
	$details = '';
	foreach ($nb_erreurs as $_type => $_compteur) {
		if ($_compteur > 0)
			$details .= '<br />-> ' . un_ou_plusieurs(
										$_compteur, 
										'plugonet:message_nok_' . $_type);
	}
	// -- construction du detail sur les traitements ok et les notices
	$details .=
		($nb_ok > 0 
			? '<br />-> ' . un_ou_plusieurs($nb_ok, 'plugonet:message_ok_' . $traitement) .
			($nb_notices['validation_pluginxml'] > 0 
				? ' (' . un_ou_plusieurs($nb_notices['validation_pluginxml'], 'plugonet:message_notice_validation_pluginxml') . ')'
				: '')
			: '');
	// -- consolidation de la synthese finale
	$duree = $duree > 0 ? $duree : '<1';
	$resume = un_ou_plusieurs(
					$nb_fichiers,
					'plugonet:resume_' . $traitement,
					array('details' => $details, 'duree' => $duree));

	// Construction de l'analyse detaillee
	$analyse = formater_bloc('error', $texte['erreur'], 'plugonet:details_' . $traitement . '_erreur', $nb_nok) .
				formater_bloc('notice', $texte['notice'], 'plugonet:details_' . $traitement . '_notice', $nb_notices['validation_pluginxml']) .
				formater_bloc('success', $texte['succes'], 'plugonet:details_' . $traitement . '_succes', $nb_ok);

	return array($resume, $analyse);
}

function formater_un_plugin($item, $pluginxml, $affichage_complet=true, $erreurs=array()) {
	include_spip('inc/layer');

	$contenu = '';
	$titre = _T($item, array('nb' => $pluginxml . ' :'));
	if (!$affichage_complet)
		$texte_titre = '';
	else
		$texte_titre = $item ? $titre : $pluginxml;
	if (!$erreurs) {
		// Generation ok, erreur de lecture, erreur d'information
		// -- On donne juste le message simple d'erreur ou de succes dans un bloc non depliable
		$contenu = '<div style="margin-bottom: 1em;"><strong>' .
					$texte_titre .
					'</strong></div>';
	}
	else {
		$contenu .= bouton_block_depliable($texte_titre, false) .
		            debut_block_depliable(false);
		foreach ($erreurs as $_erreur) {
             $contenu .= "<p style=\"padding-left:2em;\">\n\tL$_erreur[1] : $_erreur[0]\n</p>";
		}
		$contenu .= fin_block();
	}
	return $contenu;
}

function formater_bloc($classe, $texte, $titre, $nb) {
	$bloc = '';
	if ($texte AND $classe)
		$bloc .= "<div class=\"$classe\">\n" . 
					"\t" . un_ou_plusieurs($nb, $titre) . "\n" .
					"\t<div style=\"background-color: #fff; margin-top: 10px;\">\n" .
					"\t\t$texte\n" .
					"\t</div><br />\n" .
					"</div>\n";
	return $bloc;
}


/**
 * Gestion du singulier et du pluriel pour une chaine de langue
 *
 * @param int				$nb
 * @param string			$chaine_un
 * @param array / string	$options
 * @return string
 */

// $nb					=> le compteur !
// $chaine_un			=> item de langue pour le singulier (format php)
// $options				=> parametres additionnels valables pour le singulier et le pluriel
//						   la cle 'nb' n'est pas consideree comme un parametre mais comme un 
//						   renommage de parametre valeur par defaut fixe a @nb@
//						   Si on veut juste renommer 'nb' on peut passer la chaine directe
function un_ou_plusieurs($nb, $chaine_un, $options=array()) {
	global $spip_lang;

	if (!$nb=intval($nb)) 
		return '';

	// Par defaut, le parametre eventuel designant la valeur dans la chaine est reperer par @nb@
	// On teste si il est precise une option differente : dans ce cas on supprime aussi cette cle
	// des parametres optionnels
	$params = array('nb' => $nb);
	if (is_array($options)) {
		if (array_key_exists('nb', $options)) {
			$params = array($options['nb'] => $nb);
			unset($options['nb']);
		}
		$params = array_merge($params, $options);
	}
	else if ($options)
		$params = array($options => $nb);
	
	// Traitement des autres cas incluant aussi l'absence de chaine duel pour une langue le supportant
	if ($nb == 1)
		$texte = _T($chaine_un, $params);
	else
		$texte = _T($chaine_un . '_pluriel', $params);

	return $texte;
}

?>
