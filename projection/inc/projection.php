<?php

# la projection d'un objet c'est le contenu de cet objet au format
# HTML pour affichage plaisant mais contenant toutes les données
# pour permettre une recopie

# dans quel répertoire on fait ça… local/projection/


function projection($objet, $id_objet) {
	spip_log("projection $objet:$id_objet", "projection");
	spip_log($_SERVER['REQUEST_URI'], 'projection'); # verifier qu'on s'execute bien sur le cron

	if ($projection = charger_fonction('projection_'.$objet, 'inc', true)) {
	spip_log("a $projection", 'projection');
		$projection($objet, $id_objet);
	} else {
		projection_dist($objet, $id_objet);
	}

	spip_log("a $projection", 'projection');
}


function projection_dist($objet, $id_objet) {
	spip_log("Je ne sais pas faire la projection de $objet:$id_objet", "projection");

}

function inc_projection_articles_dist($objet, $id_objet) {
	if (!$dir = projection_dir($objet, $id_objet)) {
		spip_log('echec', 'projection');
		return false;
	}

	# contenu à enregistrer
	include_spip('abstract_sql');
	$obj = sql_fetsel('*', table_objet($objet), id_table_objet($objet).'='.$id_objet);
	# todo : retirer les champs inutiles, ajouter les jointures (auteurs, mots, documents)

	# fichier de projection
	$type = objet_type($objet); # 'article'
	$f = $dir.$type.'-'.$id_objet.'.yaml';
	spip_log($f, 'projection');

	# recuperer la representation complete de l'objet
	$representation = projection_representation($obj, $type);

	# on l'écrit et zou
	return ecrire_fichier($f, $representation);
}



function projection_dir($objet, $id_objet) {
	if ($p = sous_repertoire(_DIR_VAR, 'projection')
	AND $p = sous_repertoire($p,$objet))  # on pourrait organiser par rubrique
		return $p;
}

# à noter : json_encode est temporaire, on veut un vrai format
# avec de belles propriétés
function projection_representation($obj, $type) {
	include_spip('inc/yaml');

	// fallback json si yaml absent
	if (!function_exists('yaml_encode'))
		return json_encode($obj);

	// eliminer les champs vides ou null ou ayant une valeur par defaut
	$data = array_filter($obj);
	if ($data['date_redac'] == '0000-00-00 00:00:00')
		unset($data['date_redac']);
	if ($data['statut'] == 'publie')
		unset($data['statut']);

	// eliminer les champs inutiles
	foreach (explode(' ',
	'id_article id_rubrique id_secteur export date_modif lang langue_choisie accepter_forum maj'
	) as $i)
		unset($data[$i]);

	//
	// ajouter les jointures
	//
	
	# authors
	if (count($auteurs = sql_allfetsel('nom, bio', 'spip_auteurs a left join spip_auteurs_articles b on a.id_auteur=b.id_auteur', 'b.id_article='.$obj['id_article']))) {
		$data['authors'] = array_filter(array_map('projection_auteur', $auteurs));
		if (count($data['authors']) == 1)
			$data['authors'] = array_pop($data['authors']);
	} else
		$data['error'] .= sql_error();

	# tags
	if (count($mots = sql_allfetsel('titre, descriptif, texte', 'spip_mots a left join spip_mots_articles b on a.id_mot=b.id_mot', 'b.id_article='.$obj['id_article']))) {
		$data['tags'] = array_filter(array_map('projection_mot', $mots));
	} else
		$data['error'] .= sql_error();

	# documents
	if (count($docs = sql_allfetsel('titre, descriptif, fichier, a.id_document, vu, mode', 'spip_documents a left join spip_documents_liens b on a.id_document=b.id_document', "b.objet='$type' AND b.id_objet=".$obj['id_article']))) {
		$data['docs'] = array_filter(array_map('projection_doc', $docs));
	} else
		$data['error'] .= sql_error();

	# category
	if ($rub = sql_allfetsel('titre, descriptif, texte', 'spip_rubriques', "id_rubrique=".$obj['id_rubrique'])) {
		$data['category'] = projection_rubrique($rub[0]);
	} else
		$data['error'] .= sql_error();


	## le texte, c'est l'essentiel mais il ne figure pas dans l'entete
	unset($data['texte']);
	## le chapo et le PS sont du texte,
	## ils peuvent contenir des raccourcis <docX>
	foreach(explode(' ', 'chapo ps') as $i)
		if (isset($data[$i]))
			$data[$i] = projection_texte($data[$i], $data);

	#
	# Envoyer la representation YAML + content
	#
	$rep = "##### projection de l'article $obj[id_article]\n"
		. "--- # metadata\n"
		. yaml_encode(array_filter($data))
		. "--- # content\n";

	$rep .= projection_texte($obj['texte'], $data);

	return $rep;
}


function projection_auteur($auteur) {
	$auteur = array_filter($auteur);
	if (count($auteur) == 1
	AND isset($auteur['nom']))
		$auteur = $auteur['nom'];
	return $auteur;
}
function projection_mot($mot) {
	$mot = array_filter($mot);
	if (count($mot) == 1
	AND isset($mot['titre']))
		$mot = $mot['titre'];
	return $mot;
}
function projection_doc($doc) {
	$doc = array_filter($doc);
	$conf['url_de_base'] = 'http://rezo.pagekite.net/spip2.1/';

	#'<doc http://…………… copy|nocopy>
	# conf : copy / nocopy / default: copy|nocopy

	// URL absolue de maniere a pouvoir exporter
	if (isset($doc['fichier'])
	AND !preg_match(',://,', $fichier))
		$doc['fichier'] = url_absolue(_DIR_IMG.$doc['fichier'], $conf['url_de_base']);

	if ($doc['vu'] == 'non') {
		unset($doc['id_document']);
		if ($doc['mode'] == 'image')
			$doc = array();
	}
	unset($doc['vu']);
	if ($doc['mode'] == 'document') unset($doc['mode']);

	if (count($doc) == 1
	AND isset($doc['fichier']))
		$doc = $doc['fichier'];
	return $doc;
}
function projection_rubrique($rub) {
	$rub = array_filter($rub);
	if (count($rub) == 1
	AND isset($rub['titre']))
		$rub = $rub['titre'];
	return $rub;
}

# on va nettoyer un peu le texte notamment les liens !
# mais c'est très difficile car dn SPIP tout est fait
# pour exporter du HTML
## le code ci-dessous casse pas mal de choses dans le texte
function projection_texte($txt, &$data) {
	include_spip('inc/texte');
	$txt = echappe_html($txt, 'P', true);
	#$txt = expanser_liens($txt /*,$connect */);

	$txt = projection_dereferencer($txt, &$data);

#	$txt = iconv_wordwrap($txt, 80);
	$txt = echappe_retour($txt, 'P');
	return $txt;
}

## dereferencer les liens et modeles
function projection_dereferencer($texte, $data) {
	$texte = projection_dereferencer_liens($texte, $data);
	$texte = projection_dereferencer_modeles($texte, $data);
	return $texte;
}

// cf. https://code.spip.net/@traiter_modeles
function projection_dereferencer_modeles($texte, &$data, $liens=null) {

	// detecter les modeles (rapide)
	if (strpos($texte,"<")!==false AND
	  preg_match_all('/<[a-z_-]{3,}\s*[0-9|]+/iS', $texte, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			// Recuperer l'appel complet (y compris un eventuel lien)
			$a = strpos($texte,$match[0]);
			preg_match(_RACCOURCI_MODELE_DEBUT,
			substr($texte, $a), $regs);
			$regs[]=""; // s'assurer qu'il y a toujours un 5e arg, eventuellement vide
			list(,$mod, $type, $id, $params, $fin) = $regs;
			if ($fin AND
			preg_match('/<a\s[^<>]*>\s*$/i',
					substr($texte, 0, $a), $r)) {
				$lien = array(
					'href' => extraire_attribut($r[0],'href'),
					'class' => extraire_attribut($r[0],'class'),
					'mime' => extraire_attribut($r[0],'type')
				);
				$n = strlen($r[0]);
				$a -= $n;
				$cherche = $n + strlen($regs[0]);
			} else {
				$lien = false;
				$cherche = strlen($mod);
			}

/*
				// si un tableau de liens a ete passe, reinjecter le contenu d'origine
				// dans les parametres, plutot que les liens echappes
				if (!is_null($liens))
					$params = str_replace($liens[0], $liens[1], $params);
*/

			  $modele = projection_dereferencer_modele($regs[0], $type, $id, $params, $lien, $connect);

/*
				// en cas d'echec, 
				// si l'objet demande a une url, 
				// creer un petit encadre vers elle
				if ($modele === false) {
					if (!$lien)
						$lien = traiter_lien_implicite("$type$id", '', 'tout', $connect);
					if ($lien)
						$modele = '<a href="'
						  .$lien['url']
						  .'" class="spip_modele'
						  . '">'
						  .sinon($lien['titre'], _T('ecrire:info_sans_titre'))
						  ."</a>";
					else {
						$modele = "";
						if (test_espace_prive()) {
							$modele = entites_html(substr($texte,$a,$cherche));
							if (!is_null($liens))
								$modele = "<pre>".str_replace($liens[0], $liens[1], $modele)."</pre>";
						}
					}
				}
*/

				// le remplacer dans le texte
				if ($modele !== false) {
					#$modele = protege_js_modeles($modele);
					$rempl = code_echappement($modele, 'P');
					$texte = substr($texte, 0, $a)
						. $rempl
						. substr($texte, $a+$cherche);
				}
			}
		}

	return $texte;
}

function projection_dereferencer_modele($appel, $type, $id, $params, $lien, $connect) {
#	$a = func_get_args();
#	return var_export($a, true);
	switch($type) {
		case 'img':
		case 'doc':
		case 'emb':
			if (is_numeric($id) AND $id>0) {
				$url = projection_doc($doc = sql_fetsel('fichier', 'spip_documents', 'id_document='.$id));
				$appel = preg_replace("/$id/", "| href=".$url." ", $appel, 1);
				# <media|href=xxxxxx|small> ?
			}
			break;
	}

	return $appel;
}

// cf. https://code.spip.net/@expanser_liens
define('_RACCOURCI_LIEN', "/\[([^][]*?([[]\w*[]][^][]*)*)->(>?)([^]]*)\]/msS");
function projection_dereferencer_liens($texte, &$data) {
	$sources = $inserts = $regs = array();
	if (preg_match_all(_RACCOURCI_LIEN, $texte, $regs, PREG_SET_ORDER)) {
		$lien = 'projection_lien'; #charger_fonction('lien', 'inc');
		foreach ($regs as $k => $reg) {

			$inserts[$k] = '@@SPIP_ECHAPPE_LIEN_' . $k . '@@';
			$sources[$k] = $reg[0];
			$texte = str_replace($sources[$k], $inserts[$k], $texte);

			list($titre, $bulle, $hlang) = traiter_raccourci_lien_atts($reg[1]);
			$r = $reg[count($reg)-1];
			// la mise en lien automatique est passee par la a tort !
			// corrigeons pour eviter d'avoir un <a...> dans un href...
			if (strncmp($r,'<a',2)==0){
				$href = extraire_attribut($r, 'href');
				// remplacons dans la source qui peut etre reinjectee dans les arguments
				// d'un modele
				$sources[$k] = str_replace($r,$href,$sources[$k]);
				// et prenons le href comme la vraie url a linker
				$r = $href;
			}
			$regs[$k] = $lien($reg, $r, $titre, '', $bulle, $hlang, '', $connect);
		}
	}

	// on passe a traiter_modeles la liste des liens reperes pour lui permettre
	// de remettre le texte d'origine dans les parametres du modele
#	$texte = traiter_modeles($texte, false, false, $connect, array($inserts, $sources));
# 	$texte = corriger_typo($texte);
	$texte = str_replace($inserts, $regs, $texte);
	return $texte;
}

function projection_lien($reg, $lien, $texte='', $class='', $title='', $hlang='', $rel='', $connect='') {
	if ($match = typer_raccourci($lien)) { 
		@list($type,,$id,,$args,,$ancre) = $match;

		// Si une langue est demandee sur un raccourci d'article, chercher
		// la traduction ;
		// - [{en}->art2] => traduction anglaise de l'article 2, sinon art 2
		// - [{}->art2] => traduction en langue courante de l'art 2, sinon art 2
		if ($hlang
		AND $type == 'article'
		AND $id_trad = sql_getfetsel('id_trad', 'spip_articles', "id_article=$id")
		AND $id_dest = sql_getfetsel('id_article', 'spip_articles',
			"id_trad=$id_trad  AND statut<>'refuse' AND lang=" . sql_quote($hlang))
		)
			$id = $id_dest;

		# (article, 2) => URL publique de l'article 2
		$url = generer_url_entite_absolue($id, $type, $args, $ancre, $connect);

		# si le texte est vide aller chercher le titre
		$lien = calculer_url("$type$id", $texte, 'tout', $connect);

		$titre = strlen($reg[1]) ? $reg[1] : $lien['titre'];

		return "[$titre->$url]";
	}

	return $reg[0];
}

## optionnellement, wordwrap (ne vaut pas un sentencewrap)
## http://fr2.php.net/manual/fr/function.wordwrap.php#106088
/**
 * Word wrap
 *
 * @param  string  $string
 * @param  integer $width
 * @param  string  $break
 * @param  boolean $cut
 * @param  string  $charset
 * @return string
 */
function iconv_wordwrap($string, $width = 75, $break = "\n", $cut = false, $charset = 'utf-8')
{
    $stringWidth = iconv_strlen($string, $charset);
    $breakWidth  = iconv_strlen($break, $charset);

    if (strlen($string) === 0) {
        return '';
    } elseif ($breakWidth === null) {
        throw new Zend_Text_Exception('Break string cannot be empty');
    } elseif ($width === 0 && $cut) {
        throw new Zend_Text_Exception('Can\'t force cut when width is zero');
    }

    $result    = '';
    $lastStart = $lastSpace = 0;

    for ($current = 0; $current < $stringWidth; $current++) {
        $char = iconv_substr($string, $current, 1, $charset);

        if ($breakWidth === 1) {
            $possibleBreak = $char;
        } else {
            $possibleBreak = iconv_substr($string, $current, $breakWidth, $charset);
        }

        if ($possibleBreak === $break) {
            $result    .= iconv_substr($string, $lastStart, $current - $lastStart + $breakWidth, $charset);
            $current   += $breakWidth - 1;
            $lastStart  = $lastSpace = $current + 1;
        } elseif ($char === ' ') {
            if ($current - $lastStart >= $width) {
                $result    .= iconv_substr($string, $lastStart, $current - $lastStart, $charset) . $break;
                $lastStart  = $current + 1;
            }

            $lastSpace = $current;
        } elseif ($current - $lastStart >= $width && $cut && $lastStart >= $lastSpace) {
            $result    .= iconv_substr($string, $lastStart, $current - $lastStart, $charset) . $break;
            $lastStart  = $lastSpace = $current;
        } elseif ($current - $lastStart >= $width && $lastStart < $lastSpace) {
            $result    .= iconv_substr($string, $lastStart, $lastSpace - $lastStart, $charset) . $break;
            $lastStart  = $lastSpace = $lastSpace + 1;
        }
    }

    if ($lastStart !== $current) {
        $result .= iconv_substr($string, $lastStart, $current - $lastStart, $charset);
    }

    return $result;
}


