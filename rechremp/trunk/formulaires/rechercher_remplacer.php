<?php
/**
 * Plugin Rechercher/Remplacer
 * Licence GPL-v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_rechercher_remplacer_charger_dist(){
	$valeurs = array(
		'search'=>'',
		'replace_yes' => '',
		'replace'=>''
	);

	return $valeurs;
}

function formulaires_rechercher_remplacer_verifier_dist(){
	$erreurs = array();

	if(!_request('search'))
		$erreurs['search'] = _T('info_obligatoire');

	else
		if(!_request('remplacer'))
			// recherche a blanc pour voir/confirmer le remplacement
			$erreurs['search_results'] =
				"<input type='hidden' name='replace_check_table[dummy]' value='yes' />"
				. rechremp_search_and_replace(_request('search'),'',false,_request('replace_yes')?"replace_check_table":null);

	return $erreurs;
}

function formulaires_rechercher_remplacer_traiter_dist(){

	$res = array();

	// remplacer si demande
	if (_request('remplacer')
	  AND _request('replace_yes')){
		$check_replace = _request('replace_check_table');
		$res['message_ok'] =
			"<h3>"._T("rechremp:resultat_remplacement")."<small>&#171;&nbsp;".entites_html(_request('search'))."&nbsp;&#187;</small></h3>"
		. rechremp_search_and_replace(_request('search'),_request('replace'),true,$check_replace);
	}
	else
		// sinon simple recherche, mais normalement on arrive pas la
		$res['message_ok'] = rechremp_search_and_replace(_request('search'));

	return $res;
}




function rechremp_search_and_replace($search,$replace=null,$do_replace=false,$check_replace=null){
	include_spip("base/objets");
	$tables_exclues = array('spip_messages','spip_depots','spip_paquets','spip_plugins');
	$champs_exclus = array("extra","tables_liees","obligatoire","comite","minirezo","forum","mode","fichier","distant","media");
	$liste = lister_tables_objets_sql();
	$trouver_table = charger_fonction("trouver_table","base");

	$out = array();
	foreach($liste as $table => $desc){
		if (!in_array($table,$tables_exclues)){
			$champs = array();
			if (isset($desc['champs_editables']) AND $desc['champs_editables'])
				$champs = $desc['champs_editables'];
			elseif(isset($desc['champs_versionnes']))
				$champs = $desc['champs_versionnes'];

			// trouver les champs de la vrai table
			$desc = $trouver_table($table);
			// pas touche au champ extra serialize
			$champs = array_diff($champs,$champs_exclus);
			// que les champs qui existent
			$champs = array_intersect($champs,array_keys($desc['field']));
			// et qui sont en texte
			foreach($champs as $c){
				if (!preg_match(",text|varchar,",$desc['field'][$c]))
					$champs = array_diff($champs,array($c));
			}

			if (count($champs)){

				$replace_here = $do_replace;
				if  (is_array($check_replace) AND !isset($check_replace[$table]))
					$replace_here = false;

				$t = rechremp_search_and_replace_table($table,$champs,$search,$replace,$replace_here);
				if ($t AND is_string($check_replace)){
					$i = "<input type='checkbox' name='{$check_replace}[$table]' />";
					$t = preg_replace(",<label[^>]*>,","\\0$i",$t,1);
				}
				if ($t){
					if ($do_replace AND !$replace_here)
						$t = _T('rechremp:aucun_remplacement_sur',array('objets'=>_T(objet_info(objet_type($table),"texte_objets"))));
					$out[] = $t;
				}
			}
		}
	}
	$out = array_filter($out);
	if (count($out))
		$out = implode("<br />",$out);
	else
		$out = _T('rechremp:aucune_occurence_trouvee');

	return $out;
}

function rechremp_search_and_replace_table($table, $champs, $search,$replace=null,$do_replace=false) {
	if (!count($champs) OR !$search)
		return "";

	include_spip("action/editer_objet");
	include_spip("inc/filtres");
	include_spip("inc/texte");

	$objet = objet_type($table);
	$primary = id_table_objet($table);
	$select = "$primary,".implode(",",$champs);

	$nb_occurences = 0;
	$founds = array();
	$res = sql_select($select,$table);

	while($row = sql_fetch($res)) {

		$set = array();
		foreach($champs as $c){
			$nb = 0;
			$v = str_replace($search, $replace, $row[$c], $nb);
			if ($nb){
				$set[$c] = $v;
				if (!isset($founds[$row[$primary]]))
					$founds[$row[$primary]] = 0;
				$founds[$row[$primary]] += $nb;
				$nb_occurences += $nb;
			}
		}

		// Mise à jour d'un champ de la table
		if($do_replace AND count($set)) {
			objet_modifier($objet,$row[$primary],$set);
		}
	}

	if (!$nb_occurences)
		return "";

	$out = singulier_ou_pluriel($nb_occurences,'rechremp:1_occurence_dans','rechremp:nb_occurences_dans');

	$out .= " ".objet_afficher_nb(count($founds),$objet);
	$out = "<label><strong>$out</strong></label><ul class='spip'>";

	foreach($founds as $id_objet=>$nb){
		$l = singulier_ou_pluriel($nb,'rechremp:1_occurence_dans','rechremp:nb_occurences_dans');
		$l .= " <a href=\"".generer_url_entite($id_objet,$objet)."\">".generer_info_entite($id_objet,$objet,"titre")."</a>";
		$out .="<li>$l</li>\n";
	}

	$out .= "</ul>";


	return $out;
}

?>
