<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/core21_filtres');

/**
 * une fonction pour générer des menus avec liens
 * ou un span lorsque l'item est sélectionné
 *
 * @param string $url
 * @param string $libelle
 * @param bool $on
 * @param string $class
 * @param string $title
 * @return string
 */
function aoustrong($url,$libelle,$on=false,$class="",$title="",$rel=""){
	return lien_ou_expose($url,$libelle,$on,$class,$title,$rel);
}


/**
 * une fonction pour générer une balise img à partir d'un nom de fichier
 *
 * @param string $img
 * @param string $alt
 * @param string $class
 * @return string
 */
function tag_img($img,$alt="",$class=""){
	$balise_img = chercher_filtre('balise_img');
	return $balise_img($img,$alt,$class);
}

/**
 * Afficher un message "un truc"/"N trucs"
 *
 * @param int $nb
 * @return string
 */
function affiche_un_ou_plusieurs($nb,$chaine_un,$chaine_plusieurs,$var='nb'){
	return singulier_ou_pluriel($nb,$chaine_un,$chaine_plusieurs,$var);
}

/**
 * Ajouter un timestamp à une url de fichier
 *
 * @param unknown_type $fichier
 * @return unknown
 */
function timestamp($fichier){
	if (!$fichier) return $fichier;
	$m = filemtime($fichier);
	return "$fichier?$m";
}

/**
 * Transformer un tableau d'entrées array("rubrique|9","article|8",...)
 * en un tableau contenant uniquement les identifiants d'un type donné.
 * Accepte aussi que les valeurs d'entrées soient une chaîne brute
 * "rubrique|9,article|8,..." 
 *
 * @param array/string $selected liste des entrées : tableau ou chaîne séparée par des virgules
 * @param string $type type de valeur à récuperer ('rubrique', 'article')
 * 
 * @return array liste des identifiants trouvés.
**/
function picker_selected($selected, $type){
	$select = array();
	$type = preg_replace(',\W,','',$type);

	if ($selected and !is_array($selected)) 
		$selected = explode(',', $selected);
		
	if (is_array($selected))
		foreach($selected as $value)
			if (preg_match(",".$type."[|]([0-9]+),",$value,$match)
			  AND strlen($v=intval($match[1])))
			  $select[] = $v;
	return $select;
}

function picker_identifie_id_rapide($ref,$rubriques=0,$articles=0){
	include_spip("inc/json");
	include_spip("inc/lien");
	if (!($match = typer_raccourci($ref)))
		return json_export(false);
	@list($type,,$id,,,,) = $match;
	if (!in_array($type,array($rubriques?'rubrique':'x',$articles?'article':'x')))
		return json_export(false);
	$table_sql = table_objet_sql($type);
	$id_table_objet = id_table_objet($type);
	if (!$titre = sql_getfetsel('titre',$table_sql,"$id_table_objet=".intval($id)))
		return json_export(false);
	$titre = attribut_html(extraire_multi($titre));
	return json_export(array('type'=>$type,'id'=>"$type|$id",'titre'=>$titre));
}

/**
 * Donner n'importe quelle information sur un objet de manière générique.
 *
 * La fonction va gérer en interne deux cas particuliers les plus utilisés :
 * l'URL et le titre (qui n'est pas forcemment la champ SQL "titre").
 *
 * On peut ensuite personnaliser les autres infos en créant une fonction
 * generer_<nom_info>_entite($id_objet, $type_objet, $ligne).
 * $ligne correspond à la ligne SQL de tous les champs de l'objet, les fonctions
 * de personnalisation n'ont donc pas à refaire de requête.
 *
 * @param int $id_objet
 * @param string $type_objet
 * @param string $info
 * @return string
 */
function generer_info_entite($id_objet, $type_objet, $info, $etoile=''){
	// On vérifie qu'on a tout ce qu'il faut
	$id_objet = intval($id_objet);
	if (!($id_objet and $type_objet and $info))
		return '';
	
	// Si on demande l'url, on retourne direct la fonction
	if ($info == 'url')
		return generer_url_entite($id_objet, $type_objet);
	
	// Si on demande le titre, on le gère en interne
	if ($demande_titre = ($info == 'titre')){
		global $table_titre;
		$champ_titre = $table_titre[table_objet($type_objet)];
		if (!$champ_titre) $champ_titre = 'titre';
		$champ_titre = ", $champ_titre";
	}
	
	// Sinon on va tout chercher dans la table et on garde en mémoire
	static $objets;
	
	// On ne fait la requête que si on n'a pas déjà l'objet ou si on demande le titre mais qu'on ne l'a pas encore
	if (!$objets[$type_objet][$id_objet] or ($demande_titre and !$objets[$type_objet][$id_objet]['titre'])){
		include_spip('base/abstract_sql');
		include_spip('base/connect_sql');
		$objets[$type_objet][$id_objet] = sql_fetsel(
			'*'.$champ_titre,
			table_objet_sql($type_objet),
			id_table_objet($type_objet).' = '.intval($id_objet)
		);
	}
	$ligne = $objets[$type_objet][$id_objet];
	
	if ($demande_titre)
		$info_generee = $objets[$type_objet][$id_objet]['titre'];
	// Si la fonction generer_TRUC_entite existe, on l'utilise
	else if ($generer = charger_fonction("generer_${info}_entite", '', true))
		$info_generee = $generer($id_objet, $type_objet, $ligne);
	// Sinon on prend le champ SQL
	else
		$info_generee = $ligne[$info];
	
	// On va ensuite chercher les traitements automatiques à faire
	global $table_des_traitements;
	$maj = strtoupper($info);
	$traitement = $table_des_traitements[$maj];
	$table_objet = table_objet($type_objet);
	
	if (!$etoile and is_array($traitement)){
		$traitement = $traitement[isset($traitement[$table_objet]) ? $table_objet : 0];
		$traitement = str_replace('%s', '"'.str_replace('"', '\\"', $info_generee).'"', $traitement);
		eval("\$info_generee = $traitement;");
	}
	
	return $info_generee;
}

/**
 * Protéger les champs passés dans l'url et utilisés dans {tri ...}
 * préserver l'espace pour interpréter ensuite num xxx et multi xxx
 * @param string $t
 * @return string
 */
function tri_protege_champ($t){
	return preg_replace(',[^\s\w.+],','',$t);
}

/**
 * Interpréter les multi xxx et num xxx utilisés comme tri
 * pour la clause order
 * 'multi xxx' devient simplement 'multi' qui est calculé dans le select
 * 'hasard' est calculé dans le select
 * @param string $t
 * @return string
 */
function tri_champ_order($t,$table=NULL,$field=NULL){
	if (strncmp($t,'num ',4)==0){
		$t = substr($t,4);
		$t = preg_replace(',\s,','',$t);
		// Lever une ambiguïté possible si le champ fait partie de la table (pour compatibilité de la balise tri avec compteur, somme, etc.)
		if (!is_null($table) && !is_null($field) && in_array($t,unserialize($field)))
			$t = "0+$table.$t";
		else
			$t = "0+$t";
		return $t;
	}
	elseif(strncmp($t,'multi ',6)==0){
		return "multi";
	}
	else {
		$t = preg_replace(',\s,','',$t);
		// Lever une ambiguïté possible si le champ fait partie de la table (pour compatibilité de la balise tri avec compteur, somme, etc.)
		if (!is_null($table) && !is_null($field) && in_array($t,unserialize($field)))
			return $table.'.'.$t;
		else
			return $t;
	}
}

/**
 * Interpréter les multi xxx et num xxx utilisés comme tri
 * pour la clause select
 * 'multi xxx' devient select "...." as multi
 * les autres cas ne produisent qu'une chaîne vide '' en select
 * 'hasard' devient 'rand() AS hasard' dans le select
 *
 * @param string $t
 * @return string
 */
function tri_champ_select($t){
	if(strncmp($t,'multi ',6)==0){
		$t = substr($t,6);
		$t = preg_replace(',\s,','',$t);
		$t = sql_multi($t,$GLOBALS['spip_lang']);
		return $t;
	}
	if(trim($t)=='hasard'){
		return 'rand() AS hasard';
	}
	return "''";
}

/**
 * Rediriger une page suivant une autorisation,
 * et ce, n'importe où dans un squelette, même dans les inclusions.
 *
 * @param bool $ok Indique si l'on doit rediriger ou pas
 * @param string $url Adresse vers laquelle rediriger
 * @param int $statut Statut HTML avec lequel on redirigera
 * @return string
 */
function filtre_sinon_interdire_acces_dist($ok=false, $url='', $statut=0){
	if ($ok) return '';
	
	// vider tous les tampons
	while (ob_get_level())
		ob_end_clean();
	
	include_spip('inc/headers');
	$statut = intval($statut);
	
	// Si aucun argument on essaye de deviner quoi faire
	if (!$url and !$statut){
		// Si on est dans l'espace privé, on génère du 403 Forbidden
		if (test_espace_prive()){
			http_status(403);
			$echec = charger_fonction('403','exec');
			$echec();
		}
		// Sinon on redirige vers une 404
		else{
			$statut = 404;
		}
	}
	
	// Sinon on suit les directives indiquées dans les deux arguments
	
	// S'il y a un statut
	if ($statut){
		// Dans tous les cas on modifie l'entité avec ce qui est demandé
		http_status($statut);
		// Si le statut est une erreur 4xx on va chercher le squelette
		if ($statut >= 400)
			echo recuperer_fond("$statut");
	}
	
	// S'il y a une URL, on redirige (si pas de statut, la fonction mettra 302)
	if ($url) redirige_par_entete($url, '', $statut);
	
	exit;
}

/**
 * Calculer et retourner la profondeur de la rubrique
 * (dans spip3, c'est un champ de la table rubrique)
 * les rubriques à la racine sont à une profondeur de 1
 *
 * @param string
 * @return int
*/
function filtre_profondeur_dist($id_rubrique) {
	$id_rubrique = intval($id_rubrique);
	
	// sauver les calculs déjà faits
	static $profs = array();
	if (isset($profs[$id_rubrique])) {
		return $profs[$id_rubrique];
	}

	// récupérer le parent.
	$id_parent = sql_getfetsel('id_parent', 'spip_rubriques', 'id_rubrique='.$id_rubrique);

	// pas de parent : id_rubrique n'existe pas
	if (is_null($id_parent)) {
		return '';
	}

	// parent zéro : on est tout en haut (racine)
	if ($id_parent == '0') {
		return $profs[$id_rubrique] = 1;
	}

	// sinon, on trouve la profondeur du parent
	$parent = filtre_profondeur_dist($id_parent);
	$profs[$id_rubrique] = ($parent + 1);
	return $profs[$id_rubrique];
}

?>