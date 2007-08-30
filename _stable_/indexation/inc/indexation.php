<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/*
	Valeurs du champ 'idx' de la table spip_indexation
	0 ne sait pas
	1 à (re)indexer
	2 en cours
	3 indexe
	-1 ne jamais indexer (?)
*/

// Quels formats sait-on extraire ?
$GLOBALS['extracteur'] = array (
	'txt'   => 'extracteur_txt',
	'pas'   => 'extracteur_txt',
	'c'     => 'extracteur_txt',
	'css'   => 'extracteur_txt',
	'html'  => 'extracteur_html'
);

// Filtres d'indexation
// http://doc.spip.org/@unserialize_join
function unserialize_join($extra){
	return @join(' ', unserialize($extra));
}
// http://doc.spip.org/@contenu_page_accueil
function contenu_page_accueil($url){
	$texte = '';
	if ($GLOBALS['meta']["visiter_sites"] == "oui") {
		include_spip('inc/distant');
		spip_log ("indexation contenu syndic $url");
		$texte = supprimer_tags(recuperer_page($url, true, false, 50000));
	}
	return $texte;
}

// Extracteur des documents 'txt'
// http://doc.spip.org/@extracteur_txt
function extracteur_txt($fichier, &$charset) {
	lire_fichier($fichier, $contenu);

	// Reconnaitre le BOM utf-8 (0xEFBBBF)
	include_spip('inc/charsets');
	if (bom_utf8($contenu))
		$charset = 'utf-8';

	return $contenu;
}

// Extracteur des documents 'html'
// http://doc.spip.org/@extracteur_html
function extracteur_html($fichier, &$charset) {
	lire_fichier($fichier, $contenu);

	// Importer dans le charset local
	include_spip('inc/charsets');
	$contenu = transcoder_page($contenu);
	$charset = $GLOBALS['meta']['charset'];

	return $contenu;
}

// Indexer le contenu d'un document
// http://doc.spip.org/@indexer_contenu_document
function indexer_contenu_document ($row, $min_long=3) {
	global $extracteur;

	if ($row['mode'] == 'vignette') return;
	$extension = $row['extension'];

	// Voir si on sait lire le contenu (eventuellement en chargeant le
	// fichier extract/pdf.php dans find_in_path() )
	include_spip('extract/'.$extension);
	if (function_exists($lire = $extracteur[$extension])) {
		// Voir si on a deja une copie du doc distant
		// Note: si copie_locale() charge le doc, elle demande une reindexation
		include_spip('inc/distant');
		include_spip('inc/documents');
		if (!$fichier = copie_locale(get_spip_doc($row['fichier']), 'test')) {
			spip_log("pas de copie locale de '$fichier'");
			return;
		}
		// par defaut, on pense que l'extracteur va retourner ce charset
		$charset = 'iso-8859-1'; 
		// lire le contenu
		$contenu = $lire(_DIR_RACINE.$fichier, $charset);
		if (!$contenu) {
			spip_log("Echec de l'extraction de '$fichier'");
		} else {
			// Ne retenir que les 50 premiers ko
			$contenu = substr($contenu, 0, 50000);
			// importer le charset
			$contenu = importer_charset($contenu, $charset);
			// Indexer le texte
			indexer_chaine($contenu, 1, $min_long);
		}
	} else {
		spip_log("pas d'extracteur '$extension' fonctionnel");
	}
}

// n'indexer que les objets publies
// http://doc.spip.org/@critere_indexation
function critere_indexation($table) {
	global $INDEX_critere_indexation;
	if (isset($INDEX_critere_indexation[$table]))
	  return $INDEX_critere_indexation[$table];
	else
	  return '1=1'; // indexation par defaut
}

// http://doc.spip.org/@deja_indexe
function deja_indexe($table, $id_objet) {
	$id_table = id_index_table($table);
	$n = sql_countsel(
		array('spip_indexation'),
		array('id='._q($id_objet), 'type='._q($id_table), 'idx=2')
	);
	return ($n > 0);
}

// http://doc.spip.org/@indexer_objet
function indexer_objet($table, $id_objet, $forcer_reset = true) {

	global $index, $mots, $translitteration_complexe;
	global $INDEX_elements_objet;
	global $INDEX_objet_associes;

	$min_long = isset($GLOBALS['INDEX_mots_min_long'])
		? intval($GLOBALS['INDEX_mots_min_long'])
		: 3;

	$table_index = 'spip_indexation';
	$col_id = primary_index_table($table);
	$id_table = id_index_table($table);

	if (!$id_objet) return;
	if (!$forcer_reset AND deja_indexe($table, $id_objet)) {
		spip_log ("$table $id_objet deja indexe");
		return;
	}

	// marquer "en cours d'indexation"
	spip_query("UPDATE spip_indexation SET idx=1 WHERE type="._q($id_table)." AND idx="._q($id_objet));

	include_spip('inc/texte');

	spip_log("indexation $table $id_objet");
	$index = '';
	$mots = '';

	$result = spip_query("SELECT * FROM $table WHERE $col_id=$id_objet");

	$row = sql_fetch($result);
	if (!$row) return;

	// translitteration complexe ?
	if (!$lang = $row['lang']) $lang = $GLOBALS['meta']['langue_site'];
	if ($lang == 'de' OR $lang=='vi') {
		$translitteration_complexe = 1;
		spip_log ('-> translitteration complexe');
	} else $translitteration_complexe = 0;

	if (isset($INDEX_elements_objet[$table])){

		// Cas tres particulier du forum :
		// on indexe le thread comme un tout
		if ($table=='spip_forum') {

			// 1. chercher la racine du thread
			$id_forum = $id_objet;
			while ($row['id_parent']) {
				$id_forum = $row['id_parent'];
				$s = spip_query("SELECT id_forum,id_parent FROM spip_forum WHERE id_forum=$id_forum");
				$row = sql_fetch($s);
			}

			// 2. chercher tous les forums du thread
			// (attention le forum de depart $id_objet n'appartient pas forcement
			// a son propre thread car il peut etre le fils d'un forum non 'publie')
			$thread="$id_forum";
			$fini = false;
			while (!$fini) {
				$s = spip_query("SELECT id_forum FROM spip_forum WHERE id_parent IN ($thread) AND id_forum NOT IN ($thread) AND statut='publie'");
				if (spip_num_rows($s) == 0) $fini = true;
				while ($t = sql_fetch($s))
					$thread.=','.$t['id_forum'];
			}

			// 3. marquer le thread comme "en cours d'indexation"
			spip_log("-> indexation thread $thread");
			spip_query("UPDATE spip_forum SET idx='idx' WHERE id_forum IN ($thread,$id_objet) AND idx!='non'");

			// 4. Indexer le thread
			$s = spip_query("SELECT * FROM spip_forum WHERE id_forum IN ($thread) AND idx!='non'");
			while ($row = sql_fetch($s)) {
		    indexer_les_champs($row,$INDEX_elements_objet[$table],1,$min_long);
		    if (isset($INDEX_objet_associes[$table]))
		      foreach($INDEX_objet_associes[$table] as $quoi=>$poids)
						indexer_elements_associes($table, $id_objet, $quoi, $poids, $min_long);
				break;
			}

			// 5. marquer le thread comme "indexe"
			spip_query("UPDATE spip_forum SET idx='oui' WHERE id_forum IN ($thread,$id_objet) AND idx!='non'");

			// 6. Changer l'id_objet en id_forum de la racine du thread
			$id_objet = $id_forum;
		} else {
			indexer_les_champs($row,$INDEX_elements_objet[$table],1,$min_long);
			if (isset($INDEX_objet_associes[$table]))
				foreach($INDEX_objet_associes[$table] as $quoi=>$poids)
					indexer_elements_associes($table, $id_objet, $quoi, $poids, $min_long);

			if ($table=='spip_syndic'){
				// 2. Indexer les articles syndiques
				if (($row['syndication'] = "oui")&&(isset($INDEX_elements_objet['syndic_articles']))) {
					$result_syndic = spip_query("SELECT titre FROM spip_syndic_articles WHERE id_syndic=$id_objet AND statut='publie' ORDER BY date DESC LIMIT 100");

					while ($row_syndic = sql_fetch($result_syndic)) {
		    		indexer_les_champs($row,$INDEX_elements_objet['syndic_articles'],1,$min_long);
					}
				}
			}
			if ($table=='spip_documents'){
				// 2. Indexer le contenu si on sait le lire
				indexer_contenu_document($row,$min_long);
			}
	 	}
	} else
		spip_log("je ne sais pas indexer '$table'");

#	sql_replace('spip_indexation', SET idx=3, titre='xxx', texte='xx' WHERE id=$id_objet AND type=$id_table");
	spip_query($q = 'REPLACE spip_indexation (id, type, titre, texte, meta, date, idx) VALUES '
		. '('._q($id_objet).', '._q($id_table).', "titre", '._q(trim($mots)).', "meta", "2001-01-01", 3)');
	spip_log($q);

}


// http://doc.spip.org/@primary_index_table
function primary_index_table($table){
	global $tables_principales;
	/*global $table_des_tables;
	$t = $table_des_tables[$table];
	// pour les tables non Spip
	if (!$t) $t = $table; else $t = "spip_$t";*/
	$t = $table;
	$p = $tables_principales["$t"]['key']["PRIMARY KEY"];
	if (!$p){
		$p = preg_replace("{^spip_}","",$table);
		$p = "id_" . $p;
		if (substr($p,-1,1)=='s')
		  $p = substr($p,0,strlen($p)-1);
	}
	return $p;
}


function effectuer_une_indexation($nombre_indexations = 1) {
	global $INDEX_iteration_nb_maxi;
	$vu = array();

	// chercher un objet a indexer dans chacune des tables d'objets
	foreach (liste_index_tables() as $cle => $table) {

		$table_primary = primary_index_table($table);
		$critere = critere_indexation($table);

		$limit = $nombre_indexations;
		if (isset($INDEX_iteration_nb_maxi[$table]))
			$limit = min($limit,$INDEX_iteration_nb_maxi[$table]);

		// indexer en priorite les 1 (a reindexer), ensuite les 0
		// (statut d'indexation inconnu), enfin les 2 (ceux dont
		// l'indexation a precedemment echoue, p. ex. a cause d'un timeout)
		foreach (array(1, 0, 2) as $mode) {
			$s = spip_query($q = "SELECT a.$table_primary AS n, id,idx FROM $table AS a LEFT JOIN spip_indexation AS l ON (a.$table_primary = l.id AND l.type=$cle) WHERE (id IS NULL OR idx=$mode) AND $critere LIMIT $limit");
			spip_log($q);
			while ($t = sql_fetch($s)) {
				$vu[$table] .= $t['n'].", ";
				indexer_objet($table, $t['n'], $mode);
				spip_log($t);
			}
			if ($vu[$table]) break;
		}
	}
	return $vu;
}


// http://doc.spip.org/@creer_liste_indexation
function creer_liste_indexation() {
	spip_query("UPDATE spip_indexation SET idx=0");
}

// http://doc.spip.org/@liste_index_tables
function liste_index_tables() {
	$liste_tables = array();
	if (!isset($GLOBALS['meta']['index_table'])) {
		lire_metas();
	}
	if (isset($GLOBALS['meta']['index_table']))
		$liste_tables = unserialize($GLOBALS['meta']['index_table']);
	return $liste_tables;
}

// http://doc.spip.org/@id_index_table
function id_index_table($table){
	/*global $table_des_tables;
	$t = $table_des_tables[$table];
	// pour les tables non Spip
	if (!$t) $t = $table; else $t = "spip_$t";*/
	$t = $table;
	$id_table = 0;
	$l = liste_index_tables();
	$l = @array_flip($l);
	if (isset($l[$t]))
		$id_table=$l[$t];
	return $id_table;
}

// id_table = 1...9 pour les tables spip, et ensuite 100, 101, 102...
// pour les tables additionnelles, selon l'ordre dans lequel
// elles sont reperees. On stocke le tableau de conversion table->id_table
// dans spip_meta
// http://doc.spip.org/@update_index_tables
function update_index_tables(){
	global $tables_principales;
	global $INDEX_tables_interdites;
	
	$old_liste_tables = liste_index_tables();
	$rev_old_liste_tables = array_flip($old_liste_tables);

	$liste_tables = array();
	// les tables SPIP conventionnelles en priorite
	$liste_tables[1]='spip_articles';
	$liste_tables[2]='spip_auteurs';
	$liste_tables[3]='spip_breves';
	$liste_tables[4]='spip_documents';
	$liste_tables[5]='spip_forum';
	$liste_tables[6]='spip_mots';
	$liste_tables[7]='spip_rubriques';
	$liste_tables[8]='spip_signatures';
	$liste_tables[9]='spip_syndic';

	// detection des nouvelles tables
	$id_autres = 100;
	foreach(array_keys($tables_principales) as $new_table){
		if (	(!in_array($new_table,$INDEX_tables_interdites))
				&&(!in_array($new_table,$liste_tables))
				&&($id_autres<254) ){
			// on utilise abstract_showtable car cela permet d'activer l'indexation
			// en ajoutant simplement le champ idx, sans toucher au core
			$desc = spip_abstract_showtable($new_table, '', true);
			if (isset($desc['field']['idx'])){
			  // la table a un champ idx pour gerer l'indexation
			  if ( 	(isset($rev_old_liste_tables[$new_table]))
						&&(!isset($liste_tables[$rev_old_liste_tables[$new_table]])) )
			  		$liste_tables[$rev_old_liste_tables[$new_table]] = $new_table; // id conserve
				else{
					while (isset($liste_tables[$id_autres])&&($id_autres<254)) $id_autres++;
					$liste_tables[$id_autres] = $new_table;
				}
			}
		}
	}

	// Cas de l'ajout d'une nouvelle table a indexer :
	// mise en coherence de la table d'indexation avec les nouveaux id
	$rev_old_liste_tables = array_flip($old_liste_tables);
	foreach($liste_tables as $new_id=>$new_table){
		if (isset($rev_old_liste_tables[$new_table])){
		  if (	($old_id = ($rev_old_liste_tables[$new_table]))
					&&($old_id!=$new_id)){
				$temp_id = 254;
				// liberer le nouvel id
				spip_query("UPDATE spip_index SET id_table=$temp_id WHERE id_table=$new_id");
				// deplacer les indexation de l'ancien id sous le nouvel id
				spip_query("UPDATE spip_index SET id_table=$new_id WHERE id_table=$old_id");
				// remettre les indexation deplacees sous l'id qui vient d'etre libere
				spip_query("UPDATE spip_index SET id_table=$old_id WHERE id_table=$temp_id");
	
				$old_liste_tables[$old_id] = $old_liste_tables[$new_id]; 
				unset($old_liste_tables[$new_id]);
				$rev_old_liste_tables = array_flip($old_liste_tables);
			}
		}
	} 

	ecrire_meta('index_table',serialize($liste_tables));
	ecrire_metas();
}




// tables que l'on ne doit pas indexer
global $INDEX_tables_interdites;
$INDEX_tables_interdites=array();

// Indexation des elements de l'objet principal
// 'champ'=>poids, ou 'champ'=>array(poids,min_long)
$reindex = false;
global $INDEX_elements_objet;
$INDEX_elements_objet = FALSE;
if (isset($GLOBALS['meta']['INDEX_elements_objet']))
	$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
if (!$INDEX_elements_objet) {
	$INDEX_elements_objet['spip_articles'] = array('titre'=>8,'soustitre'=>5,'surtitre'=>5,'descriptif'=>4,'chapo'=>3,'texte'=>1,'ps'=>1,'nom_site'=>1,'extra|unserialize_join'=>1);
	$INDEX_elements_objet['spip_breves'] = array('titre'=>8,'texte'=>2,'extra|unserialize_join'=>1);
	$INDEX_elements_objet['spip_rubriques'] = array('titre'=>8,'descriptif'=>5,'texte'=>1,'extra|unserialize_join'=>1);
	$INDEX_elements_objet['spip_auteurs'] = array('nom'=>array(5,2),'bio'=>1,'extra|unserialize_join'=>1);
	$INDEX_elements_objet['spip_mots'] = array('titre'=>8,'descriptif'=>5,'texte'=>1,'extra|unserialize_join'=>1);
	$INDEX_elements_objet['spip_signatures'] = array('nom_email'=>array(2,2),'ad_email'=>2,'nom_site'=>2,'url_site'=>1,'message'=>1);
	$INDEX_elements_objet['spip_syndic'] = array('nom_site'=>50,'descriptif'=>30,'url_site|contenu_page_accueil'=>1);
	$INDEX_elements_objet['spip_syndic_articles'] = array('titre'=>5);
	$INDEX_elements_objet['spip_forum'] = array('titre'=>3,'texte'=>2,'auteur'=>array(2,2),'email_auteur'=>2,'nom_site'=>2,'url_site'=>1);
	$INDEX_elements_objet['spip_documents'] = array('titre'=>20,'descriptif'=>10,'fichier'=>1);
	ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
	ecrire_metas();
	$reindex = true;
}

// Indexation des objets associes
// 'objet'=>poids
global $INDEX_objet_associes;
$INDEX_objet_associes = FALSE;
if (isset($GLOBALS['meta']['INDEX_objet_associes']))
	$INDEX_objet_associes = unserialize($GLOBALS['meta']['INDEX_objet_associes']);
if (!$INDEX_objet_associes) {
	$INDEX_objet_associes['spip_articles'] = array('spip_documents'=>1,'spip_auteurs'=>10,'spip_mots'=>3);
	$INDEX_objet_associes['spip_breves'] = array('spip_documents'=>1,'spip_mots'=>3);
	$INDEX_objet_associes['spip_rubriques'] = array('spip_documents'=>1,'spip_mots'=>3);
	$INDEX_objet_associes['spip_documents'] = array('spip_mots'=>3);
	ecrire_meta('INDEX_objet_associes',serialize($INDEX_objet_associes));
	ecrire_metas();
	$reindex = true;
}

// Indexation des elements des objets associes
// 'champ'=>poids, ou 'champ'=>array(poids,min_long)
global $INDEX_elements_associes;
$INDEX_elements_associes = FALSE;
if (isset($GLOBALS['meta']['INDEX_elements_associes']))
	$INDEX_elements_associes = unserialize($GLOBALS['meta']['INDEX_elements_associes']);
if (!$INDEX_elements_associes){
	$INDEX_elements_associes['spip_documents'] = array('titre'=>2,'descriptif'=>1);
	$INDEX_elements_associes['spip_auteurs'] = array('nom'=>1);
	$INDEX_elements_associes['spip_mots'] = array('titre'=>4,'descriptif'=>1);
	ecrire_meta('INDEX_elements_associes',serialize($INDEX_elements_associes));
	ecrire_metas();
	$reindex = true;
}
// Criteres d'indexation
global $INDEX_critere_indexation;
$INDEX_critere_indexation = FALSE;
if (isset($GLOBALS['meta']['INDEX_critere_indexation']))
	$INDEX_critere_indexation = unserialize($GLOBALS['meta']['INDEX_critere_indexation']);
if (!$INDEX_critere_indexation){
	$INDEX_critere_indexation['spip_articles']="statut='publie'";
	$INDEX_critere_indexation['spip_breves']="statut='publie'";
	$INDEX_critere_indexation['spip_rubriques']="statut='publie'";
	$INDEX_critere_indexation['spip_syndic']="statut='publie'";
	$INDEX_critere_indexation['spip_forum']="statut='publie'";
	$INDEX_critere_indexation['spip_signatures']="statut='publie'";
	ecrire_meta('INDEX_critere_indexation',serialize($INDEX_critere_indexation));
	ecrire_metas();
	$reindex = true;
}

// Criteres de des-indexation (optimisation dans base/optimiser)
global $INDEX_critere_optimisation;
$INDEX_critere_optimisation = FALSE;
if (isset($GLOBALS['meta']['INDEX_critere_optimisation']))
	$INDEX_critere_optimisation = unserialize($GLOBALS['meta']['INDEX_critere_optimisation']);
if (!$INDEX_critere_optimisation) {
	$INDEX_critere_optimisation['spip_articles']="statut<>'publie'";
	$INDEX_critere_optimisation['spip_breves']="statut<>'publie'";
	$INDEX_critere_optimisation['spip_rubriques']="statut<>'publie'";
	$INDEX_critere_optimisation['spip_syndic']="statut<>'publie'";
	$INDEX_critere_optimisation['spip_forum']="statut<>'publie'";
	$INDEX_critere_optimisation['spip_signatures']="statut<>'publie'";
	ecrire_meta('INDEX_critere_optimisation',serialize($INDEX_critere_optimisation));
	ecrire_metas();
	$reindex = true;
}

// Nombre d'elements maxi a indexer a chaque iteration
global $INDEX_iteration_nb_maxi;
$INDEX_iteration_nb_maxi = FALSE;
if (isset($GLOBALS['meta']['INDEX_iteration_nb_maxi']))
	$INDEX_iteration_nb_maxi = unserialize($GLOBALS['meta']['INDEX_iteration_nb_maxi']);
if (!$INDEX_iteration_nb_maxi) {
	$INDEX_iteration_nb_maxi['spip_documents']=10;
	$INDEX_iteration_nb_maxi['spip_syndic']=1;
	ecrire_meta('INDEX_iteration_nb_maxi',serialize($INDEX_iteration_nb_maxi));
	ecrire_metas();
	$reindex = true;
}


// Si la liste des correspondances tables/id_table n'est pas la, la creer
if ((isset($GLOBALS['meta']))&&(!isset($GLOBALS['meta']['index_table'])))
	update_index_tables();
// S'il faut reinitialiser, go
if ($reindex)
	creer_liste_indexation();


// http://doc.spip.org/@indexer_chaine
function indexer_chaine($texte, $val = 1, $min_long = 3) {
	global $index, $mots;
	global $translitteration_complexe;

	// Point d'entree pour traiter le texte avant indexation 
	$texte = pipeline('pre_indexation', $texte);
	$mots = trim($mots.' ** '.$texte);
}

// http://doc.spip.org/@indexer_les_champs
function indexer_les_champs(&$row,&$index_desc,$ponderation = 1, $min_long=3){
	reset($index_desc);
	while (list($quoi,$poids) = each($index_desc)){
		$pipe=array();
		if (strpos($quoi,"|")){
			$pipe = explode("|",$quoi);
			$quoi = array_shift($pipe);
		}
		if (isset($row[$quoi])){
			$texte = $row[$quoi];
			if (count($pipe)){
				foreach ($pipe as $func){
					$func = trim($func);
					if (!function_exists($func)) {
						spip_log("Erreur - $func n'est pas definie (indexation)");
					}
					else
						// appliquer le filtre
						$texte = $func($texte);
				}
			}
			//spip_log(":$quoi:$poids:$texte");
			if (is_array($poids))
				indexer_chaine($texte,array_shift($poids) * $ponderation,array_shift($poids));
			else
				indexer_chaine($texte,$poids * $ponderation, $min_long);
		}
	}
}

// Indexer les documents, auteurs, mots-cles associes a l'objet
// http://doc.spip.org/@indexer_elements_associes
function indexer_elements_associes($table, $id_objet, $table_associe, $valeur, $min_long=3) {
	global $INDEX_elements_associes, $tables_jointures, $tables_auxiliaires, $tables_principales;

	if (isset($INDEX_elements_associes[$table_associe])){
		$table_abreg = preg_replace("{^spip_}","",$table);
		$col_id = primary_index_table($table);
		$col_id_as = primary_index_table($table_associe);
		if (is_array($rel = $tables_jointures[$table])) {
			foreach($rel as $joint) {
				if (@in_array($col_id_as, @array_keys($tables_auxiliaires['spip_' . $joint]['field'])))
					{$table_rel = $joint; break;}
				if (@in_array($col_id_as, @array_keys($tables_principales['spip_' . $joint]['field'])))
					{$table_rel = $joint; break;}
			}
			if (!$table_rel){
				spip_log("Indexation de $table echouee : element associe $table_associe, jointure sur $col_id_as introuvable");
				return;
			}

			$select="assoc.$col_id_as";
			foreach(array_keys($INDEX_elements_associes[$table_associe]) as $quoi)
				$select.=',assoc.' . $quoi;
			$r = spip_query("SELECT $select FROM $table_associe AS assoc,	spip_$table_rel AS lien	WHERE lien.$col_id=$id_objet AND assoc.$col_id_as=lien.$col_id_as");

			while ($row = spip_abstract_fetch($r)) {
				indexer_les_champs($row,$INDEX_elements_associes[$table_associe],$valeur);
			}
		}
 	}
}

// API pour l'espace prive pour marquer un objet d'une table a reindexer
// http://doc.spip.org/@marquer_indexer
function marquer_indexer ($table, $id_objet) {
	if (!isset($GLOBALS['INDEX_elements_objet'][$table]))
		return;
	spip_log ("demande indexation $table id=$id_objet");
	$type = id_index_table($table);
	spip_query("UPDATE spip_indexation SET idx=1 WHERE id=$id_objet AND type=$type AND idx!=-1");
}

// A garder pour compatibilite bouton memo...
// http://doc.spip.org/@indexer_article
function indexer_article($id_article) {
	marquer_indexer('spip_articles', $id_article);
}


?>
