<?php

## fonctions pour hasher les documents

if (!defined("_ECRIRE_INC_VERSION")) return;


/* pour un fichier d'origine situé dans IMG/{$ext}/xxxx.ext,
 * prendre les 3 premiers caractères (a, b, c) du md5(xxxx.ext),
 * et déplacer le fichier dans IMG/{$ext}/a/b/c/xxxx.ext
 * attention on ignore le IMG/ eventuel dans $doc, et on retourne sans IMG/
 * $rev sert a faire l'inverse
 * @param string $doc
 * @param bool $rev
 * @return string
 */
function hasher_adresser_document($doc, $rev=false) {
	switch ($rev) {
		case false:
			if (!preg_match(',^(?:IMG/)?([^/]+)/([^/]+\.\1)$,S', $doc, $r))
				return false;
			$m = md5($r[2]);
			return $r[1].'/'.$m[0].'/'.$m[1].'/'.$m[2].'/'.$r[2];
		case true:
			if (!preg_match(',^(?:IMG/)?([^/]+)/./././([^/]+\.\1)$,S', $doc, $r))
				return false;
			return $r[1].'/'.$r[2];
	}
}

/* Deplacer un fichier et sa reference dans la base de donnees
 * avec tous les controles d'erreur
 * 
 * @param int $id_document
 * @param bool $rev
 * @return bool
 */
function hasher_deplacer_document($id_document, $corriger=false, $rev=false) {

// 1. recuperer les donnees du document
	// et verifier qu'on peut le hasher
	if (!$id_document = intval($id_document))
		return false;
	if (!$s = spip_query('SELECT fichier FROM spip_documents WHERE id_document='.$id_document)
	OR !$t = spip_fetch_array($s)) {
		spip_log("Erreur hasher_deplacer_document select doc=$id_document ".var_export($s, true), 'hash');
		return false;
	}
	$src = $t['fichier'];

	// savoir si on a IMG/ devant (en SPIP 1.9.2) ou pas (SPIP 2)
	$img = preg_match(',^IMG/,', $src)
		? 'IMG/' : '';
	$dir_ref = preg_match(',^IMG/,', $src)
		? _DIR_RACINE : _DIR_IMG;

	// On fabrique le nom  du fichier dest
	if (!$dest = hasher_adresser_document($src, $rev)) {
		spip_log("Erreur hasher_adresser_document($src) rev : $rev", 'hash');
		return false;
	}

	// si le src n'existe pas, ciao, enfin presque
	if (!file_exists($dir_ref.$src)) {
		spip_log("Erreur hasher_deplacer_document fichier $dir_ref $src n'existe pas", 'hash');

		// si le src n'existe pas, on verifie qu'il n'a pas deja ete déplace (ie le dest existe),
		// et si oui, on modifie juste le chemin en base... 
		if($corriger) {
			if(file_exists(_DIR_IMG.$dest)){
				// on note la destination finale
				if (!spip_query('UPDATE spip_documents SET fichier="'.$img.$dest.'" WHERE id_document='.$id_document)) {
					spip_log("erreur update correction $img $dest doc $id_document", 'hash');
					return false;
				} else {
					spip_log("hasher_deplacer_document fichier "._DIR_IMG."$dest existe deja, Table corrigee", 'hash');
					return true ;
				}
			} else {
				spip_log("hasher_deplacer_document fichier "._DIR_IMG."$dest n'existe pas", 'hash');
			}
		}
		return false ;
	}

	// si le dest existe deja, renommer jusqu'a trouver un creneau libre
	$i = 0;
	while (file_exists(_DIR_IMG.$dest)) {
		$i++;
		$dest = preg_replace(',(-\d+)?(\.[^.]+)$,', '-'.$i.'\2', $dest);
	}

	// 2. creer au besoin les sous-repertoires
	if (!is_dir(_DIR_IMG.$dir = dirname($dest))
	AND !mkdir(_DIR_IMG.$dir, _SPIP_CHMOD, /* recursive, php5 */ true)) {
		spip_log("erreur hasher_deplacer_document mkdir($dir)", 'hash');
		return false;
	}

	// 3. Section critique : il faut modifier dans la base *et* deplacer
	// on note les fichiers en cours de deplacement avec un - devant ; si
	// ca casse on saura reparer
	if (!spip_query('UPDATE spip_documents SET fichier=CONCAT("-", fichier) WHERE id_document='.$id_document)) {
		spip_log("erreur update 1", 'hash');
		return false;
	}
	// on deplace
	if (!rename($dir_ref.$src, _DIR_IMG.$dest)) {
		spip_log("erreur rename", 'hash');
		spip_query('UPDATE spip_documents SET fichier="'.$src.'" WHERE id_document='.$id_document);
		return false;
	}
	// on note la destination finale
	if (!spip_query('UPDATE spip_documents SET fichier="'.$img.$dest.'" WHERE id_document='.$id_document)) {
		spip_log("erreur update 2", 'hash');
		return false;
	}

	// 4. Ouf c'est fini et sans erreur
	return true;
}


/* Cette fonction prend les n documents non hashés les plus récents,
 * et appelle hasher_deplacer_document() sur chacun d'eux. Elle renvoie
 * un array() contenant les id_document des documents qu'elle a déplacés.
 * @param int $n
 * @param bool $rev
 * @return array
 * @return bool
 */
function hasher_deplacer_n_documents($n, $corriger=false, $rev=false) {
	if (!$n = intval($n)
	OR !$s = spip_query($q = "SELECT id_document FROM spip_documents WHERE fichier REGEXP '^(IMG/)?[^/]+/"
	. ($rev ? "./././" : "")
	."[^/]+$' AND distant='non' ORDER BY date DESC LIMIT $n")) {
		spip_log("erreur requete $q", 'hash');
		return false;
	}

	$docs = array();
	while ($t = spip_fetch_array($s)) {
		$id_document = $t['id_document'];
		if (hasher_deplacer_document($id_document, $corriger, $rev))
			$docs[] = $id_document;
	}

	return $docs;
}

/* Compte les documents hashes et non hashes
 * @return array
 */
function hasher_compter_documents() {
	$s = spip_query($q = "SELECT COUNT(*) FROM spip_documents WHERE fichier REGEXP '^(IMG/)?[^/]+/"
	."[^/]+$' AND distant='non'");
	$non = array_pop(spip_fetch_array($s));
	$s = spip_query($q = "SELECT COUNT(*) FROM spip_documents WHERE fichier REGEXP '^(IMG/)?[^/]+/"
	. "./././"
	."[^/]+$' AND distant='non'");
	$oui = array_pop(spip_fetch_array($s));

	return array($oui, $non);
}

/* Pipeline post_edition pour agir apres ajout de nouveaux documents via upload
 * @param array $flux
 * @return array
 */
function hasher_post_edition($flux) {
	if ($flux['args']['operation'] == 'ajouter_document'
	AND $id = intval($flux['args']['id_objet'])) {
		hasher_deplacer_document($id);
		hasher_deplacer_n_documents(10);
	}
	return $flux;
}

?>
