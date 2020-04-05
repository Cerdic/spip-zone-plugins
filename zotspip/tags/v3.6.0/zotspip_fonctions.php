<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Cette balise teste si la connexion à Zotero fonctionne
function balise_TESTER_CONNEXION_ZOTERO_dist($p) {
		$p->code = "zotspip_tester_connexion_zotero()";
	return $p;
}

function zotspip_tester_connexion_zotero() {
	include_spip('inc/zotspip');
	$feed = zotero_get('items/?format=atom&limit=1');
	return $feed ? ' ' : '';
}

// Mets en forme une référence bibliographique
// On peut passer le nom d'un style CSL en argument (optionel).
// Le second argument (optionel) peut être une liste d'auteurs, tableau ou string séparée par des points-virgules, à souligner.
function balise_REFERENCE_dist($p) {
	$csljson = champ_sql('csljson', $p);
	$annee = champ_sql('annee', $p);
	$_lang = champ_sql('lang', $p);
	$style = interprete_argument_balise(1,$p);
	$souligne = interprete_argument_balise(2,$p);
	$date = champ_sql('date', $p);
	if (!$style) $style='""';
	if (!$souligne) $souligne='array()';
	
	$p->code = "zotspip_calculer_reference($csljson,$annee,$style,$souligne,$date,htmlentities($_lang ? $_lang : \$GLOBALS['spip_lang']))";
	return $p;
}

function zotspip_calculer_reference($csljson,$annee,$style,$souligne,$date,$lang) {
	include_spip('lib/citeproc-php/CiteProc');
	include_spip('inc/config');
	static $citeproc = array();
	if (!$style) {
		include_spip('inc/config');
		$style = lire_config('zotspip/csl_defaut') ? lire_config('zotspip/csl_defaut') : 'apa';
	}
	$data = json_decode($csljson);
	
	if (isset($data->issued->raw) && lire_config('zotspip/corriger_date')) { // Correction de la date de publication (si fournie brute et si option activée)
		unset($data->issued->raw);
		// Gestion des cas où la date est de la forme yyyy-mm ou yyyy-mm-dd
		if (preg_match('#^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$#',trim($date),$matches))
			$data->issued->{'date-parts'} = array(array($matches[1],$matches[2],$matches[3]));
		elseif (preg_match('#^([0-9]{4})-([0-9]{1,2})$#',trim($date),$matches))
			$data->issued->{'date-parts'} = array(array($matches[1],$matches[2]));
		else
			$data->issued->{'date-parts'} = array(array($annee));
	}

	if (!is_array($souligne)) $souligne = explode(';',$souligne);
	
	if (count($souligne)){
		foreach ($souligne as $aut_souligne) {
			if(!strpos($aut_souligne,',')) $aut_souligne .= ', ';
			$aut_souligne = explode(',',$aut_souligne);
			if (is_array($data->author))
				foreach ($data->author as $cle => $author)
					if ($author->family == trim($aut_souligne[0]) && $author->given == trim($aut_souligne[1]))
						$data->author[$cle]->family = '§§'.$data->author[$cle]->family.'§§';
			if (is_array($data->editor))
				foreach ($data->editor as $cle => $editor)
					if ($editor->family == trim($aut_souligne[0]) && $editor->given == trim($aut_souligne[1]))
						$data->editor[$cle]->family = '§§'.$data->editor[$cle]->family.'§§';
		}
	}
	
	if (!isset($citeproc[$style])) {
		include_spip('inc/distant');
		$csl = spip_file_get_contents(find_in_path("csl/$style.csl"));
		// Si le style demande n'est pas disponible, message d'erreur et se rabattre sur apa.csl
		if (!$csl) {
			erreur_squelette(_T('zotspip:message_erreur_style_csl',array('style'=>$style)));
			$csl = spip_file_get_contents(find_in_path("csl/apa.csl"));
		}
		$citeproc[$style] = new citeproc($csl,$lang);
	}
	
	$ret = $citeproc[$style]->render($data, 'bibliography');
	if (count($souligne))
		$ret = preg_replace(',§§(.+)§§,U','<span style="text-decoration:underline;">$1</span>',$ret);
	
	return $ret;
}

// Lister les styles CSL disponibles
function balise_LISTER_CSL_dist($p) {
	$p->code = "zotspip_lister_csl()";
	return $p;
}

function zotspip_lister_csl(){
	static $liste_csl = null;
	if (is_null($liste_csl)){
		$liste_csl = array();
		$match = ".+[.]csl$";
		$liste = find_all_in_path('csl/', $match);
		if (count($liste)){
			foreach($liste as $fichier=>$chemin) {
				$style = spip_file_get_contents($chemin);
				$csl = substr($fichier,0,-4);
				if (preg_match('#\<title\>(.*)\</title\>#',$style,$matches))
					$liste_csl[$csl] = $matches[1];
				else
					$liste_csl[$csl] = $csl;
			}
		}
	}
	return $liste_csl;
}

// Traduire le type de reference
function zotspip_traduire_type($type) {
	return ($type!='') ? _T('zotero:itemtypes_'.strtolower($type)) : '';
}

// Traduire le champ Zotero
function zotspip_traduire_champ($champ) {
	return ($champ!='') ? _T('zotero:itemfields_'.strtolower($champ)) : '';
}

// Traduire le type d'auteur
function zotspip_traduire_createur($type) {
	return ($type!='') ? _T('zotero:creatortypes_'.strtolower($type)) : '';
}

// Afficher l'icône du document
// On peut optionnellement ajouter mimetype et fichier pour distinguer les icônes dans le cadre des pièces jointes
function zotspip_icone_type($type, $mimetype=NULL, $fichier=NULL) {
	$alt = zotspip_traduire_type($type);
	if ($type=='attachment' && !is_null($fichier) && !$fichier) $type = 'attachment-web-link'; // Si fichier n'est pas renseigné, c'est un lien sur le web
	if ($type=='attachment' && $fichier && $mimetype=='text/html') $type = 'attachment-snapshot';
	if ($type=='attachment' && $fichier && $mimetype=='application/pdf') $type = 'attachment-pdf';
	$chemin = find_in_path("images/zotero/$type.png");
	if (!$chemin) $chemin = find_in_path("images/zotero/item.png");
	return "<img src=\"$chemin\" height=\"16\" width=\"16\" alt=\"$alt\"/>";
}

// Renvoie un tableau HTML avec le détail de l'item
function balise_ZITEM_DETAILS_dist($p) {
	$json = champ_sql('json', $p);
	$p->code = "zotspip_calculer_zitem_details($json)";
	return $p;
}

function zotspip_calculer_zitem_details($json) {
	$ret = '<table class="zitem_details spip">';
	$data = json_decode($json,true);
	if (!is_array($data)) return '';
	foreach ($data as $champ => $valeur) {
		if ($champ=='itemType')
			$ret .= "<tr><td class=\"champ\"><strong>".zotspip_traduire_champ($champ)."</strong></td><td class=\"valeur\">".zotspip_traduire_type($valeur)."</td></tr>";
		elseif ($champ=='creators')
			foreach ($valeur as $creator)
				$ret .= "<tr><td class=\"champ\"><strong>".zotspip_traduire_createur($creator['creatorType'])."</strong></td><td class=\"valeur\">".(isset($creator['name'])?$creator['name']:($creator['lastName'].(isset($creator['firstName'])?', '.$creator['firstName']:'')))."</td></tr>";
		elseif ($champ=='tags' && count($valeur)>0) {
			$tags = array();
			foreach ($valeur as $tag)
				$tags[] = $tag['tag'];
			$tags = implode(' &middot; ',$tags);
			$ret .= "<tr><td class=\"champ\"><strong>"._T('zotero:itemfields_tags')."</strong></td><td class=\"valeur\">$tags</td></tr>";
		}
		elseif ($champ=='mimeType')
			$ret .= "<tr><td class=\"champ\"><strong>MIME</strong></td><td class=\"valeur\">$valeur</td></tr>";
		elseif ($champ=='url')
			$ret .= "<tr><td class=\"champ\"><strong>".zotspip_traduire_champ($champ)."</strong></td><td class=\"valeur\"><a href=\"$valeur\">$valeur</a></td></tr>";
		elseif ($valeur)
			$ret .= "<tr><td class=\"champ\"><strong>".zotspip_traduire_champ($champ)."</strong></td><td class=\"valeur\">$valeur</td></tr>";
	}
	$ret .= '</table>';
	return $ret;
}

// Exporte les items dans le format demandé
function zotspip_export ($id, $format) {
	if (is_array($id)) $id = implode(',',$id);
	include_spip('inc/zotspip');
	return zotero_get("items/?itemKey=$id&format=$format");
}

// Renvoie le content-type correspondant à un format d'export
function zotspip_content_type ($format) {
	switch ($format) {
		case 'bibtex':
			$ctype = "application/x-bibtex";
			break;
		case 'mods':
			$ctype = "application/mods+xml";
			break;
		case 'refer':
			$ctype = "application/x-research-info-systems" ;
			break;
		case 'rdf_bibliontology':
		case 'rdf_dc':
		case 'rdf_zotero':
			$ctype = "application/rdf+xml";
			break;
		case 'ris':
			$ctype = "application/x-research-info-systems";
			break;
		case 'wikipedia':
			$ctype = "text/x-wiki";
			break;
		default:
			$ctype = '';
			break;
	}
	return ($ctype) ? 'Content-Type: '.$ctype : '';
}

// Indique le nom du fichier à télécharger
function zotspip_content_disposition ($format) {
	switch ($format) {
		case 'bibtex':
			$ext = 'bib';
			break;
		case 'mods':
			$ext = 'xml';
			break;
		case 'refer':
		case 'wikipedia':
			$ext = 'txt';
			break;
		case 'rdf_bibliontology':
		case 'rdf_dc':
		case 'rdf_zotero':
			$ext = 'rdf';
			break;
		case 'ris':
			$ext = 'ris';
			break;
		default:
			$ext = '';
			break;
	}
	return ($ext) ? 'Content-Disposition: attachment; filename=export.'.$ext : '';
}

// Récupère un fichier distant
function zotspip_recuperer_fichier($fichier, $titre, $id_zitem, $mimetype, $json) {
	$snapshot = (substr($fichier,-4) == 'view'); // Il s'agit d'un snapshot
	if ($snapshot) { 
		$url_snapshot = substr($fichier,0,-5).'?key='.lire_config('zotspip/api_key');
		$json = json_decode($json, true);
		$filename = $json['filename'];
	}
	$url_distante = $fichier.'?key='.lire_config('zotspip/api_key');
	$titre = translitteration($titre);
	// Recuperer la bonne extension de fichier en fonction du type mime
	include_spip('base/abstract_sql');
	$ext = sql_getfetsel('extension','spip_types_documents','mime_type='.sql_quote($mimetype));
	// On nettoie et on ajoute l'extension
	if ($ext) {
		if (substr($titre,-1*(strlen($ext)+1))=='.'.$ext) $titre = substr($titre,0,-1*(strlen($ext)+1));
		$titre = preg_replace(',[[:punct:][:space:]]+,u', ' ', $titre);
		$titre = preg_replace(',\.([^.]+)$,', '', $titre);
		$titre .= '.'.$ext;
	}
	$url_locale = _DIR_VAR."cache-zotspip/$id_zitem/$titre";
	
	if (!@file_exists($url_locale)) {
		include_spip('inc/distant');
		include_spip('inc/flock');
		sous_repertoire(_DIR_VAR."cache-zotspip");
		sous_repertoire(_DIR_VAR."cache-zotspip/$id_zitem");
		ecrire_fichier($url_locale,recuperer_page($url_distante,false,false,_COPIE_LOCALE_MAX_SIZE));
		if ($snapshot) {
			// Dans ce cas, il faut télécharger le zip, le dezipper et renommer les fichiers en appliquant base64_decode().
			sous_repertoire(_DIR_CACHE."tmp-zotspip");
			ecrire_fichier(_DIR_CACHE."tmp-zotspip/$id_zitem.zip",recuperer_page($url_snapshot,false,false,_COPIE_LOCALE_MAX_SIZE));
			include_spip('inc/pclzip');
			$zip = new PclZip(_DIR_CACHE."tmp-zotspip/$id_zitem.zip");
			$zip->extract(PCLZIP_OPT_PATH,_DIR_VAR."cache-zotspip/$id_zitem",PCLZIP_CB_PRE_EXTRACT, 'zotspip_decode_64_nom');
			supprimer_fichier(_DIR_CACHE."tmp-zotspip/$id_zitem.zip");
		}
	}
	include_spip('inc/headers');
	redirige_par_entete($url_locale);
}

// Utiliser pour renommer correctement les fichiers du snapshot
function zotspip_decode_64_nom($p_event, &$p_header){
	$info = pathinfo($p_header['filename']);
	$p_header['filename'] = $info['dirname'].'/'.base64_decode(substr($info['basename'],0,-5));
	return 1;
}


// Récupérer les références les plus récentes
// La variable d'environnement depuis peut être de la forme depuis=2008, depuis=2ans ou depuis=1an
function critere_zotsip_depuis($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$boucle->where[] = "zotspip_calcul_depuis(\$Pile[0]['depuis'],$table)";
}
function zotspip_calcul_depuis($depuis,$table) {
	$annee = false;
	if (is_numeric($depuis)) $annee = intval($depuis);
	elseif (substr($depuis,-2)=='an' && is_numeric(substr($depuis,0,-2))) $annee = 1 + intval(date('Y')) - intval(substr($depuis,0,-2)); // L'année en cours compte pour un
	elseif (substr($depuis,-3)=='ans' && is_numeric(substr($depuis,0,-3))) $annee = 1 + intval(date('Y')) - intval(substr($depuis,0,-3));
	elseif (substr($depuis,-1)=='a' && is_numeric(substr($depuis,0,-1))) $annee = 1 + intval(date('Y')) - intval(substr($depuis,0,-1));
	elseif (substr($depuis,-5)=='years' && is_numeric(substr($depuis,0,-5))) $annee = 1 + intval(date('Y')) - intval(substr($depuis,0,-5)); // Prise en charge de l'anglais
	elseif (substr($depuis,-4)=='year' && is_numeric(substr($depuis,0,-4))) $annee = 1 + intval(date('Y')) - intval(substr($depuis,0,-4));
	elseif (substr($depuis,-1)=='y' && is_numeric(substr($depuis,0,-1))) $annee = 1 + intval(date('Y')) - intval(substr($depuis,0,-1));
	elseif (substr($depuis,-4)=='años' && is_numeric(substr($depuis,0,-4))) $annee = 1 + intval(date('Y')) - intval(substr($depuis,0,-4)); // Prise en charge et de l'espagnol
	elseif (substr($depuis,-3)=='año' && is_numeric(substr($depuis,0,-3))) $annee = 1 + intval(date('Y')) - intval(substr($depuis,0,-3));
	if ($annee) return array('>=',"$table.annee",$annee);
	else return array();
}

// Renvoie le schéma de données Zotero
function schema_zotero($entree = '') {
	static $schema = NULL;
	if (is_null($chema)) {
		lire_fichier_securise(_DIR_TMP . 'schema_zotero.php', $schema);
		$schema = @unserialize($schema);
	}
	if (!$entree)
		return $schema;
	else
		return $schema[$entree];
}

function balise_SCHEMA_ZOTERO_dist($p) {
	$entree = interprete_argument_balise(1,$p);
	if (!$entree) $entree='""';
	$p->code = "schema_zotero($entree)";
	return $p;
}

// Permet de trier la liste des types de références à partir de leur traduction
// Utilisation : [(#SCHEMA_ZOTERO{itemTypes}|zotspip_trier_itemTypes})]
function zotspip_trier_itemTypes($itemTypes,$inclure_note=false) {
	$l = array();
	foreach ($itemTypes as $itemType)
		if($inclure_note || $itemType != 'note')
			$l[$itemType] = zotspip_traduire_type($itemType);
	asort($l);
	return array_keys($l);
}

// Fournit une liste complète de l'ensemble des types d'auteurs (en fusionnant les listes de chaque itemType)
// Utilisation : [(#SCHEMA_ZOTERO{creatorTypes}|zotspip_liste_creatorTypes_complete)]
function zotspip_liste_creatorTypes_complete($creatorTypes) {
	$retour = array();
	foreach ($creatorTypes as $creatorType)
		$retour = array_merge($retour,array_diff($creatorType,$retour));
	return $retour;
}

// Renvoie l'URL de visualisation d'un item sur zotero.org
function voir_sur_zotero($id_zitem){
	if(lire_config('zotspip/type_librairie')=='user')
		return "https://www.zotero.org/".lire_config('zotspip/username')."/items/itemKey/$id_zitem";
	else
		return "https://www.zotero.org/groups/".lire_config('zotspip/username')."/items/itemKey/$id_zitem";
}

// Renvoie l'URL de modification d'un item sur zotero.org
function modifier_sur_zotero($id_zitem){
	if(lire_config('zotspip/type_librairie')=='user')
		return "https://www.zotero.org/".lire_config('zotspip/username')."/items/itemKey/$id_zitem/mode/edit";
	else
		return "https://www.zotero.org/groups/".lire_config('zotspip/username')."/items/itemKey/$id_zitem/mode/edit";
}

// Fonction renvoyant le tableau adequat pour la configuration de l'ordre des types de documents
function zotspip_configurer_ordre_types($ordre) {
	if (!is_array($ordre)) $ordre=array();
	$ordre = array_flip($ordre);
	// Il faut completer par rapport au schema Zotero (au cas ou le schema change)
	$zotero = schema_zotero('itemTypes');
	$zotero[]='attachment'; // Ajouter les pieces jointes non presentes dans le schema
	$ordre = array_merge($ordre,array_flip($zotero));
	// Ajout des chaines de langue
	foreach ($ordre as $cle => $val)
		$ordre[$cle] = zotspip_traduire_type($cle);
	return $ordre;
}

// Le critere qui permet le tri par type (selon l'ordre defini)
function critere_par_type_zotero($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;
	include_spip('inc/config');
	$config = lire_config('zotspip/ordre_types');
	if (is_array($config) && count($config))
		$boucle->order[] = "\"FIELD($id_table.type_ref,'".implode("','",$config)."')\"";
	else
		$boucle->order[] = "'$id_table.type_ref'";
}

// Renvoie le premier auteur a partir du champs auteurs de la table zitems
function zotspip_premier_auteur($auteurs) {
	$auteurs = explode(', ',$auteurs);
	return $auteurs[0];
}

// Renvoie le tableau des id passes à [ref=XXX]
function zotspip_ids_ref($ids) {
	$ids = explode(',',$ids);
	foreach ($ids as $cle => $id) $ids[$cle] = trim($id); // (on supprime les espaces inutiles)
	foreach ($ids as $cle => $id) {
		if ($p=strpos($id,'@'))
			$ids[$cle] = substr($id,0,$p); // on ne garde que la partie avant le @
	}
	return $ids;
}

// Renvoie le tableau des positions/suffixes passés à [ref=XXX]
function zotspip_suffixes_ref($ids) {
	$ret = array();
	$ids = explode(',',$ids);
	foreach ($ids as $cle => $id) $ids[$cle] = trim($id); // (on supprime les espaces inutiles)
	foreach ($ids as $cle => $id) {
		if ($p=strpos($id,'@')) {
			$id_zitem = substr($id,0,$p); // id_zitem est avant le @
			$ret[$id_zitem] = substr($id,$p+1); // suffixe apres le @
			}
	}
	return $ret;
}

// Utilise pour les [ref=XXX] les div ne sont pas pertinents dans une note
function zotspip_div_en_span($texte) {
	return preg_replace('#div#U','span',$texte);
}


?>