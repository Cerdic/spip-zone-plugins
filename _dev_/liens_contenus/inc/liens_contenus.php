<?php
/*
 * liens_contenus
 * Gestion des liens inter-contenus
 *
 * Auteur :
 * Nicolas Hoizey
 * � 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip('base/abstract_sql');

function liens_contenus_verifier_version_base()
{
	static $version_base_active = 0;

	// Version de la base pour cette version du code
	$version_base_code = 1;

	if ($version_base_active == 0) {
		$version_base_active = isset($GLOBALS['meta']['liens_contenus_version_base']) ? $GLOBALS['meta']['liens_contenus_version_base'] : 0;
	}
	if ($version_base_active != $version_base_code) {
		include_spip('base/liens_contenus');
		if ($version_base_active == 0) {
			// Premi�re installation
			include_spip('base/create');
            spip_log('Plugin liens_contenus : creation de la base');
			creer_base();
    		$version_base_active = $version_base_code;
    		ecrire_meta('liens_contenus_version_base', $version_base_active);
    		ecrire_metas();
			liens_contenus_initialiser();
		} else {
			// Mise � jour
		}
	}
}

function liens_contenus_referencer_liens($type_objet_contenant, $id_objet_contenant, $contenu)
{
    spip_log('Plugin liens_contenus : referencer liens contenus dans '.$type_objet_contenant.' '.$id_objet_contenant.' :');

	liens_contenus_verifier_version_base();

    $liens_trouves = array();

	// Types et aliases
	$liens_contenus_types = array('article', 'breve', 'rubrique', 'auteur', 'document', 'mot', 'site');
	$liens_contenus_aliases = array('art' => 'article', 'br' => 'breve', 'br�ve' => 'breve', 'rub' => 'rubrique', 'aut' => 'auteur', 'doc' => 'document', 'im' => 'document', 'img' => 'document', 'image' => 'document', 'emb' => 'document', 'mot' => 'mot', 'syndic' => 'site');

	// Effacer les liens connus
	spip_query("DELETE FROM spip_liens_contenus WHERE type_objet_contenant="._q($type_objet_contenant)." AND id_objet_contenant="._q($id_objet_contenant));
	
	// Echapper les <a href>, <html>...< /html>, <code>...< /code>
	include_spip('inc/texte');
	$contenu = echappe_html($contenu);

	// Raccourcis de liens [xxx->url]
	$regexp = ',\[([^][]*)->(>?)([^]]*)\],msS';
	if (preg_match_all($regexp, $contenu, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$lien = trim($match[3]);
			if (preg_match(',^(\S*?)\s*(\d+)(\?.*?)?(#[^\s]*)?$,S', $lien, $match)) {
				list(, $type_objet_contenu, $id_objet_contenu, $params, $ancre) = $match;
				// article par d�faut
				if (!$type_objet_contenu) $type_objet_contenu = 'article';
				$type_objet_contenu = isset($liens_contenus_aliases[$type_objet_contenu]) ? $liens_contenus_aliases[$type_objet_contenu] : $type_objet_contenu;
				if (in_array($type_objet_contenu, $liens_contenus_types)) {
				    $liens_trouves[$type_objet_contenu.' '.$id_objet_contenu] = array('type' => $type_objet_contenu, 'id' =>$id_objet_contenu);
				}
			}
		}
	}

	// Raccourcis d'insertion de mod�les
	$regexp = '/<([a-z_-]{3,})\s*([0-9]+)?(|[^>]*)?>/iS';
	if (preg_match_all($regexp, $contenu, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			list(,$type_objet_contenu, $id_objet_contenu, $params) = $match;
			$type_objet_contenu = isset($liens_contenus_aliases[$type_objet_contenu]) ? $liens_contenus_aliases[$type_objet_contenu] : $type_objet_contenu;
			if ($type_objet_contenu != 'document') {
				$id_objet_contenu = $type_objet_contenu;
				$type_objet_contenu = 'modele';
			}
    	    $liens_trouves[$type_objet_contenu.' '.$id_objet_contenu] = array('type' => $type_objet_contenu, 'id' =>$id_objet_contenu);
		}
	}
	if (count($liens_trouves) > 0) {
	   foreach ($liens_trouves as $lien) {
            spip_log('Plugin liens_contenus : - lien '.$type_objet_contenant.' '.$id_objet_contenant.' vers '.$lien['type'].' '.$lien['id']);
            spip_abstract_insert(
                'spip_liens_contenus',
                '(type_objet_contenant, id_objet_contenant, type_objet_contenu, id_objet_contenu)',
                '('._q($type_objet_contenant).','._q($id_objet_contenant).','._q($lien['type']).','._q($lien['id']).')');
	   }
	} else {
        spip_log('Plugin liens_contenus : - aucun lien');
	}
}

// (re)initialisation de la table des liens
function liens_contenus_initialiser()
{
	// vider la table
	spip_query("DELETE FROM spip_liens_contenus");
	
	include_spip('inc/indexation');
	$liste_tables = liste_index_tables();

	// parcourir les tables et les champs
	foreach ($liste_tables as $table) {
		$type_objet_contenant = ereg_replace("^spip_(.*[^s])s?$", "\\1", $table);
        if ($type_objet_contenant == 'syndic') {
            $type_objet_contenant = 'site';
        }
		$col_id = primary_index_table($table);
		$res = spip_query("SELECT * FROM $table");
		while ($row = spip_fetch_array($res)) {
		    $id_objet_contenant = $row[$col_id];
			// implode() n'est pas forcement le plus propre conceptuellement, mais ca doit convenir et c'est rapide
			liens_contenus_referencer_liens($type_objet_contenant, $id_objet_contenant, implode(' ', $row));
		}
	}
}

function liens_contenus_boite_liste($type_objet, $id_objet)
{
    $data = "\n";

    $data .= debut_cadre_relief('../'._DIR_PLUGIN_LIENS_CONTENUS.'/images/liens_contenus-24.gif', true);
    include_spip('public/assembler');
	$contexte = array('type_objet' => $type_objet, 'id_objet' => $id_objet);
	$data .= recuperer_fond('exec/liens_contenus_liste', $contexte);

    $data .= fin_cadre_relief(true);

    return $data;
}
?>