<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function inc_plugonet_formater($erreurs) {
	// Nombre de fichiers traites
	$nb_fichiers = count($erreurs);	

	// Les resultats sont donnes sous deux formes :
	// -- une synthetique, le message global affiche en haut du formulaire : ce message resument
	//    le nombre de fichiers traites et les compteurs d'erreurs et de notices
	// -- une detaillee, affichee en bas du formulaire avec le resultat global de chaque fichier
	//    et la liste des erreurs eventuelles
	$nb_errlec = $nb_errinf = $nb_errval = $nb_notval = 0;
	$resume = $analyse = '';
	$texte = array('erreur' => '', 'notice' => '', 'succes' => '');
	foreach ($erreurs as $_pluginxml => $_erreurs) {
		// Chaque type d'erreur est exclusif
		if ($_erreurs['erreur_lecture_pluginxml']) {
			$texte['erreur'] .= formater_un_plugin('plugonet:message_nok_lecture_pluginxml', $_pluginxml);
			$nb_errlec += 1;
		}
		else if ($_erreurs['erreur_information_pluginxml']) {
			$texte['erreur'] .= formater_un_plugin('plugonet:message_nok_information_pluginxml',$_pluginxml);
			$nb_errinf += 1;
		}
		else if ($_erreurs['erreur_validation_paquetxml']){
			$texte['erreur'] .= formater_un_plugin(
									'plugonet:message_nok_validation_paquetxml', 
									$_pluginxml,
									$_erreurs['erreur_validation_paquetxml']);
			$nb_errval += 1;
		}
		else {
			if ($_erreurs['notice_validation_pluginxml']) {
				$texte['notice'] .= formater_un_plugin(
										'', 
										$_pluginxml,
										$_erreurs['notice_validation_pluginxml']);
				$nb_notval += 1;
			}
			else
				$texte['succes'] .= formater_un_plugin('', $_pluginxml);
		}
	}

	// Construction du message global	
	$nb_nok = $nb_errlec + $nb_errinf + $nb_errval;
	$nb_ok = $nb_fichiers - $nb_nok;
	$details = 
		($nb_errlec > 0 
			? '<br />-> ' . un_ou_plusieurs($nb_errlec, 'plugonet:message_nok_lecture_pluginxml')
			: '') .
		($nb_errinf > 0 
			? '<br />-> ' . un_ou_plusieurs($nb_errinf, 'plugonet:message_nok_information_pluginxml')
			: '') .
		($nb_errval > 0 
			? '<br />-> ' . un_ou_plusieurs($nb_errval, 'plugonet:message_nok_validation_paquetxml')
			: '') .
		($nb_ok > 0 
			? '<br />-> ' . un_ou_plusieurs($nb_ok, 'plugonet:message_ok_generation_paquetxml') .
			($nb_notval > 0 
				? ' (' . un_ou_plusieurs($nb_notval, 'plugonet:message_nok_validation_pluginxml') . ')'
				: '')
			: '');
	$resume = un_ou_plusieurs(
					$nb_fichiers,
					'plugonet:resume_generation_paquetxml',
					array('details' => $details));

	// Construction de l'analyse detaillee
	$analyse = formater_bloc('error', $texte['erreur'], 'plugonet:details_generer_erreur', $nb_nok) .
				formater_bloc('notice', $texte['notice'], 'plugonet:details_generer_notice', $nb_notval) .
				formater_bloc('success', $texte['succes'], 'plugonet:details_generer_succes', $nb_ok - $nb_notval);

	return array($resume, $analyse);
}

function formater_un_plugin($item, $pluginxml, $erreurs=array()) {
	include_spip('inc/layer');

	$contenu = '';
	$titre = _T($item, array('nb' => $pluginxml . ' :'));
	if (!$erreurs)
		// Generation ok, erreur de lecture, erreur d'information
		// -- On donne juste le message simple d'erreur ou de succes dans un bloc non depliable
		$contenu = '<div style="margin-bottom: 1em;"><strong>' . 
					($item ? $titre : $pluginxml) . 
					'</strong></div>';
	else {
		$contenu .= bouton_block_depliable($item ? $titre : $pluginxml, false) .
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
 * Prend aussi en compte les langues supportant le duel
 *
 * @param int				$nb
 * @param string			$chaine_un
 * @param array / string	$options
 * @return string
 */

// $nb					=> le compteur !
// $chaine_un			=> item de langue pour le singulier (format php)
// $extras				=> parametres additionnels valables pour le singulier et le pluriel
//						   la cle 'nb' n'est pas consideree comme un parametre mais comme un 
//						   renommage de parametre valeur par defaut fixe a @nb@
//						   Si on veut juste renommer 'nb' on peut passer la chaine directe
function un_ou_plusieurs($nb, $chaine_un, $options=array()) {
	static $spip_lang_duel = array('ar');
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

	// Traitement des langues qui supportent le duel : on verifie que la chaine existe
	$texte = '';
	if (in_array($spip_lang, $spip_lang_duel) AND ($nb == 2)) {
		$item = $chaine_un . '_duel';
		$texte = _T($item, $params);
		// On verifie que l'item duel existe bien et que le texte retourne est donc le bon
		// Sinon on essayera d'utiliser l'item pluriel comme pour les autres langues
		// -- Le test n'est pas terrible mais c'est la seule solution aujourd'hui !!!
		$item_non_trouve = str_replace('_', ' ', (($n = strpos($item,':')) === false ? $item : substr($item, $n+1)));
		if (!$texte OR ($texte == $item_non_trouve))
			$texte = '';
	}
	
	// Traitement des autres cas incluant aussi l'absence de chaine duel pour une langue le supportant
	if (!$texte)
		if ($nb == 1)
			$texte = _T($chaine_un, $params);
		else
			$texte = _T($chaine_un . '_pluriel', $params);
	
	return $texte;
}

?>