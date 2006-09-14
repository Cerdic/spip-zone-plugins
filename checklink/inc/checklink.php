<?php

function checklink_install(){
	checklink_verifier_base();
}

function checklink_uninstall(){
	include_spip('base/checklink');
	include_spip('base/abstract_sql');

	// suppression du champ evenements a la table spip_groupe_mots
	//spip_query("ALTER TABLE `spip_groupes_mots` DROP `evenements`");	
}

function checklink_verifier_base(){
	$version_base = 0.10;
	$current_version = 0.0;
	if(
		(!isset($GLOBALS['meta']['checklink_base_version']))
		||
		(
			($current_version = $GLOBALS['meta']['checklink_base_version'])!=$version_base
		)
	){
		include_spip('base/checklink');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			checklink_reconstruit_table();
			ecrire_meta('checklink_base_version',$current_version=$version_base);
		}
		
		ecrire_metas();
	}
}

function agrege_champs(&$row,&$index_desc){
	$out = "";
	foreach($index_desc as $quoi=>$poids){
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
					// appliquer le filtre
					if ($func != 'contenu_page_accueil') // ne pas recuperer les liens sur une page distante !
						$texte = $func($texte);
				}
			}
			$out .= $texte;
		}
	}
	return $out;
}

function checklink_extrait_liens($id_table,$id_objet,$texte){
	// recuperer les liens des balises a
	if (preg_match_all(
	',<(a|img) [^>]*>,UimsS',
	$texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {
			if (strtolower($reg[1])=='a')
				$url = extraire_attribut($reg[0], 'href');
			if (strtolower($reg[1])=='img'){
				$url = extraire_attribut($reg[0], 'src');
				if (!preg_match(',^http://,',$url)) $url = null;
			}
			// filtrer les ancres et les mailto:
			if ($url){
				$url = trim($url);
				if (preg_match(',^(#|mailto:),',$url)) $url = null;
			}
			// filtrer les documents locaux
			if ($url){
				if (strpos($url,_DIR_IMG)!==FALSE){
					if (spip_fetch_array(spip_query("SELECT id_document FROM spip_documents WHERE fichier=".spip_abstract_quote($url))))
						$url = null;
				}
			}
			if ($url){
				// prevoir les liens dont les attributs ont pu etre renseignes a la main
				$titre = extraire_attribut($reg[0], 'title');
				$lang = extraire_attribut($reg[0], 'lang');
				$titre_auto = strlen($titre)?'non':'oui';
				$lang_auto = strlen($lang)?'non':'oui';
				
				// regarder si le lien est deja reference pour cet objet
				// et le creer eventuellement sinon, en recuperant les infos dispos si le lien existe
				// dans un autre objet
				$id_lien = 0;
				if ($row = spip_fetch_array(spip_query("SELECT * FROM spip_liens WHERE url=".spip_abstract_quote($url)
							." AND id_table=$id_table AND id_objet=$id_objet"))){
						$id_lien = $row['id_lien'];
						// mettre a jour les titre, lang et obsolescence
						$titre = $titre_auto=='non' ? $titre : $row['titre'];
						$lang = $lang_auto=='non' ? $lang : $row['lang'];
						spip_query("UPDATE spip_liens SET
							titre = ".spip_abstract_quote($titre).",
							lang = ".spip_abstract_quote($lang).",
							maj = NOW(),
							obsolete = 'non',
							titre_auto = ".spip_abstract_quote($titre_auto).",
							lang_auto = ".spip_abstract_quote($lang_auto)."
						WHERE id_lien=".$id_lien);
				}
				else {
					if ($row = spip_fetch_array(spip_query("SELECT * FROM spip_liens WHERE url=".spip_abstract_quote($url)))){
						// recuperer les infos si le meme lien est reference autre part
						$titre = $titre_auto=='non' ? $titre : $row['titre'];
						$lang = $lang_auto=='non' ? $lang : $row['lang'];
						$statut = $row['statut'];
						$verification = $row['verification'];
						$date_verif = $row['date_verif'];
					}
					else{
						// nouveau lien, que l'on presume bon, mais a verifier
						$titre = $titre_auto=='non' ? $titre : "(sans titre)";
						$lang = $lang_auto=='non' ? $lang : "fr";
						$statut = 'ind';
						$verification = '1';
						$date_verif = gmdate("Y-m-d H:i:s",time()-365*24*3600);
					}
					$id_lien = spip_abstract_insert("spip_liens","(url,titre,lang,statut,obsolete,verification,date_verif,titre_auto,lang_auto,id_table,id_objet)",
						"(".spip_abstract_quote($url).",".
						spip_abstract_quote($titre).",".
						spip_abstract_quote($lang).",".
						spip_abstract_quote($statut).", 'non', ".
						spip_abstract_quote($verification).",".
						spip_abstract_quote($date_verif).",".
						spip_abstract_quote($titre_auto).",".
						spip_abstract_quote($lang_auto).",".
						spip_abstract_quote($id_table).",".spip_abstract_quote($id_objet).")");
				}
				
			}
		}
	}
}

// vider et reconstruire toute la table a partir des contenus
function checklink_reconstruit_table(){
	global $INDEX_elements_objet;
	$agregation_defaut = array(
		'titre'=>true,'soustitre'=>true,'surtitre'=>true,'descriptif'=>true,'chapo'=>true,'texte'=>true,'ps'=>true,'nom_site'=>true,'extra|unserialize_join'=>true,
		'nom'=>true,'bio'=>true,
		'nom_site'=>true,'url_site'=>true,'message'=>true,
		'description'=>true,'lieu'=>true
		);
	
	include_spip('inc/indexation');
	$liste_tables = liste_index_tables();
	
	// vider la table spip_liens
	spip_query("DELETE FROM spip_liens");
	
	// parcourir les tables et les champs
	foreach($liste_tables as $table){
		$id_table = id_index_table($table);
		$col_id = primary_index_table($table);
		$res = spip_query("SELECT * FROM $table");
		if (isset($INDEX_elements_objet[$table]))
			$agreg = $INDEX_elements_objet[$table];
		else 
			$agreg = $agregation_defaut;
		while ($row = spip_fetch_array($res)){
			$id_objet = $row[$col_id];
			$texte = propre(agrege_champs($row,$agreg));
			checklink_extrait_liens($id_table,$id_objet,$texte);
		}
	}
}
?>