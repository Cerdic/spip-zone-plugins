<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

define('_SE_EXTENSIONS_IMG',"jpg|png|gif|ico|bmp");
define('_SE_EXTENSIONS',_SE_EXTENSIONS_IMG."|htm|html|xml|yaml|svg|php|php3|php4|py|sh|sql|css|less|rdf|txt|nfo|log|js|as|csv");

/**
 * Determiner le dossier de travail
 *
 * @return string
 */
function skeleditor_path_editable(){
	static $path = null;
	if (!is_null($path))
		return $path;
	// charger les plugin qui peuvent definir un squelette
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){
		// chargement optimise precompile
		include_once(_DIR_SESSIONS."charger_plugins_fonctions.php");
	}

	$c = creer_chemin();
	while (count($c)){
		$d = array_shift($c);
		if ($path AND strncmp($d,$path,strlen($d))!==0)
			break;
		if (strncmp($d,_DIR_PLUGINS, strlen(_DIR_PLUGINS))!==0
			AND strncmp($d,defined('_DIR_PLUGINS_DIST')?_DIR_PLUGINS_DIST:_DIR_EXTENSIONS,strlen(defined('_DIR_PLUGINS_DIST')?_DIR_PLUGINS_DIST:_DIR_EXTENSIONS))!==0
			AND strncmp($d,_DIR_RACINE.'prive/', strlen(_DIR_RACINE.'prive/'))!==0
			AND strncmp($d,_DIR_RACINE.'squelettes-dist', strlen(_DIR_RACINE.'squelettes-dist'))!==0
			AND $d!==_DIR_RACINE
			AND $d!==_DIR_SITE
			AND sous_repertoire($d,'',false,true)
			)
			$path = $d;
	}

	// si pas de dossier skel mais qu'on peut creer squelettes/ c'est ok
	
	// Traiter le cas des sites mutualises
	$dir_squelettes_site = (defined('_DIR_SITE')) ? _DIR_SITE : _DIR_RACINE;

	if ((!$path OR defined('_DIR_SITE'))
		AND !is_dir($dir_squelettes_site."squelettes")
		AND sous_repertoire($dir_squelettes_site, "squelettes", false, true)){
		$path = $dir_squelettes_site."squelettes/";
	}

	return $path;
}

/**
 * Lister les fichiers editables
 */
function skeleditor_files_editables($path=null){
	if (is_null($path))
		$path = skeleditor_path_editable();
	if (!$path) return array();

	$files_editable = preg_files($path,'[.]('._SE_EXTENSIONS.')$');
	$files_editable = sort_directory_first($files_editable,$path); // utile ?

	return $files_editable;
}


// tri la liste des fichiers en placant ceux a la racine en premier
function sort_directory_first($files,$root) {
	$files_root = array();
	$files_directory = array();
	foreach($files as $file) {
		if (dirname($file)."/" != $root) $files_directory[] = $file;
		else $files_root[] = $file;
	}
	return array_merge($files_directory,$files_root);
}

function skeleditor_get_file_content_type_ctrl($fichier){
	if (!$fichier){
		$type = 'txt';
		$content='';
		$ctrl = md5($content);
	}
	elseif (preg_match(",("._SE_EXTENSIONS_IMG.")$,ims",$fichier)){
		$type = 'img';
		$ctrl = md5(filemtime($fichier));
		$content = null;
	}
	else {
		$type = 'txt';
		lire_fichier($fichier, $content);
		$ctrl = md5($content);
	}
	return array($content,$type,$ctrl);
}


// variante repliee de la fonction de l'affichage de l'arbre des repertoires
// http://doc.spip.org/@tree_open_close_dir
function skeleditor_tree_open_close_dir(&$current,$target,$current_file){
	if ($current == $target) return "";
	$tcur = explode("/",$current);
	$ttarg = explode("/",$target);
	$tcom = array();
	$output = "";
	// la partie commune
	while (reset($tcur)==reset($ttarg)){
		$tcom[] = array_shift($tcur);
		array_shift($ttarg);
	}
	// fermer les repertoires courant jusqu'au point de fork
	while($close = array_pop($tcur)){
		$output .= fin_block();
	}
	$chemin = implode("/",$tcom)."/";
	// ouvrir les repertoires jusqu'a la cible
	while($open = array_shift($ttarg)){
		$chemin .= $open . "/";
		$closed = ((strncmp($current_file, ltrim($chemin,'/'), strlen(ltrim($chemin,'/')))==0)?"":" closed");

		$output .= bouton_block_depliable("<img src='"._DIR_PLUGIN_SKELEDITOR."images/se-folder-16.png' alt='directory'/> $open",!$closed,md5($chemin));
		$output .= "<div class='dir$closed' id='".md5($chemin)."'>\n";
	}
	$current = $target;
	return $output;
}

function skeleditor_verifie_nouveau_nom($path_base,$filename,$img=false){
	$erreur = "";
	if (strpos($filename,'../')!==FALSE){
		$erreur = _T('skeleditor:erreur_sansgene'); // fichier existe deja
	}
	elseif (file_exists($path_base.$filename))
		$erreur = _T('skeleditor:erreur_overwrite'); // fichier existe deja
	else{
		if (!preg_match(",[.]("._SE_EXTENSIONS.")$,i", $filename)
			OR (!$img AND preg_match(",[.]("._SE_EXTENSIONS_IMG.")$,i", $filename)))
			$erreur = _T('skeleditor:erreur_type_interdit');

		else {
			list($chemin,$echec) = skeleditor_cree_chemin($path_base,$filename);
			if (!$chemin)
				if (!is_dir($echec))
					$erreur = _T('skeleditor:erreur_creation_sous_dossier',array('dir'=>joli_repertoire("$echec")));
				else
					$erreur = _T('skeleditor:erreur_ecrire_dans_sous_dossier',array('dir'=>joli_repertoire("$echec")));
		}
	}
	return $erreur;
}

function skeleditor_cree_chemin($path_base,$file){
	$chemin = $path_base;
	$sous = explode('/',$file);
	$filename = array_pop($sous); // inutilise ici

	$chemin_ok = "";
	while($chemin AND count($sous) AND $s = array_shift($sous)){
		$chemin_ok = $chemin;
		$chemin = sous_repertoire($chemin, $s, false, true);
	}

	return array($chemin, $chemin?'':"$chemin_ok$s");
}

/**
 * retrouver le chemin dont il vient pour extraire la racine
 * permet aussi de s'assurer que le fichier qu'on copie provient uniquement
 * du path, et pas d'autre part sur le serveur
 *
 * @param string $source
 * @return string
 */
function skeleditor_nom_copie($source){
	$file = "";
	// cas particulier des squelettes obtenus par scaffolding
	if (strncmp($source,_DIR_CACHE."scaffold/",$l=strlen(_DIR_CACHE."scaffold/"))==0)
		return substr($source,$l);
	$spip_path = creer_chemin();
	$spip_path = array_diff($spip_path, array(_DIR_RACINE));
	$spip_path[] = _DIR_RACINE;
	foreach($spip_path as $dir) {
		if (strncmp($source,$dir,strlen($dir))==0){
			$file = substr($source,strlen($dir));
			break;
		}
	}
	return $file;
}

/**
 * Commenter l'entete d'un fichier copie
 *
 * @param string $source
 * @param string $content
 * @return string
 */
function skeleditor_commente_copie($source,$content){
	/* preparer un commenaite */
	$comment = _T('skeleditor:copy_comment',array('date'=>date('Y-m-d H:i:s'),'nom'=>$GLOBALS['visiteur_session']['nom'],'source'=>joli_repertoire($source)));
	$infos = pathinfo($source);
	if (in_array($infos['extension'],array('php','php3','php4','php5','php6','css','js','as')))
		$comment = "/*\n$comment\n*/\n";
	elseif ($infos['extension']==_EXTENSION_SQUELETTES AND preg_match(',(<BOUCLE|<INCLU[RD]E|#ENV|#ID_|#REM|#INCLU[RD]E|#TITRE|#[F-Z][A-Z]*),Ums',$content))
		$comment = "[(#REM)\n$comment\n]\n";
	elseif (in_array($infos['extension'],array('htm','html','xml','svg','rdf')))
		$comment = "<!--\n$comment\n-->\n";
	else $comment='';
	return $comment.$content;
}


?>
