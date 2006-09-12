<?php

include_spip('inc/indexation');
include_spip('base/checklink');
include_spip('inc/checklink');
include_spip('inc/texte');

function checklink_pre_enregistre_contenu($flux){
	static $objet_traite=array();
	if (!isset($flux['args']['id_objet']) || !isset($flux['args']['table']))
		return; // rien a faire ici ...
	// renseigner la table
	$id_objet = $flux['args']['id_objet'];
	$table = $flux['args']['table'];
	$id_table = id_index_table($table);
	
	// si on a pas commence a traiter cet objet, marquer tous ses liens existants comme obsolete
	if (!count($objet_traite)) checklink_verifier_base();
	if (!isset($objet_traite[$id_table]) OR !isset($objet_traite[$id_table][$id_objet]) ){
		spip_query("UPDATE spip_liens SET obsolete='oui' WHERE id_table=$id_table AND id_objet=$id_objet");
		$objet_traite[$id_table][$id_objet] = true;
	}
	
	// passer le contenu dans propre pour transformer les liens internes et les modeles eventuels
	$letexte = propre(join(' ',$flux['data']));
	// recuperer les liens des balises a
	if (preg_match_all(
	',<a [^>]*>,UimsS',
	$letexte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {
			$url = extraire_attribut($reg[0], 'href');
			// prevoir les liens dont les attributs ont pu etre renseignes a la main
			$titre = extraire_attribut($reg[0], 'title');
			$lang = extraire_attribut($reg[0], 'lang');
			$titre_auto = strlen($titre)?'non':'oui';
			$lang_auto = strlen($lang)?'non':'oui';
			
			// regarder si le lien est deja reference
			// et le creer eventuellement pour cet objet
			//echo "SELECT FROM spip_liens WHERE url=".spip_abstract_quote($url);
			if ($row = spip_fetch_array(spip_query("SELECT * FROM spip_liens WHERE url=".spip_abstract_quote($url)))){
					$id_lien = $row['id_lien'];
			}
			else {
				$titre = 'test';
					$lang = 'fr';
					$status = 200;
					$verification = 'x';
					$date_verif = gmdate("Y-m-d H:i:s"); 
					$id_lien = spip_abstract_insert("spip_liens","(url,titre,lang,statut,verification,date_verif,titre_auto,lang_auto,id_table,id_objet)",
						"(".spip_abstract_quote($url).",".
						spip_abstract_quote($titre).",".
						spip_abstract_quote($lang).",".
						spip_abstract_quote($status).",".
						spip_abstract_quote($verification).",".
						spip_abstract_quote($date_verif).",".
						spip_abstract_quote($titre_auto).",".
						spip_abstract_quote($lang_auto).",".
						spip_abstract_quote($id_table).",".spip_abstract_quote($id_objet).")");
			}
			if (($titre_auto=='oui') AND (isset($row['titre'])))
					$titre = $row['titre'];
			if (($lang_auto=='oui') AND (isset($row['lang'])))
					$lang = $row['lang'];
			if (isset($row['statut'])){
				$statut = $row['statut'];
				$verification = $row['verification'];
				$date_verif = $row['date_verif'];
			}
			else {
				$statut = '';
				$verification = '1';
				$date_verif='';
			}
			
			spip_query("UPDATE spip_liens SET
				titre = ".spip_abstract_quote($titre).",
				lang = ".spip_abstract_quote($lang).",
				maj = NOW(),
				statut = ".spip_abstract_quote($statut).",
				verification = ".spip_abstract_quote($verification).",
				date_verif = ".spip_abstract_quote($date_verif).",
				obsolete = 'non',
				titre_auto = ".spip_abstract_quote($titre_auto).",
				lang_auto = ".spip_abstract_quote($lang_auto)."
			WHERE id_lien=".$id_lien);
		}
	}
	return $flux;
	
}

?>