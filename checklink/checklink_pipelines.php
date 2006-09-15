<?php

include_spip('inc/indexation');
include_spip('base/checklink');
include_spip('inc/checklink');
include_spip('inc/texte');

function checklink_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees") {
	  // on voit le bouton dans la barre "forum_admin"
		$boutons_admin['forum']->sousmenu["liens_tous"]= new Bouton(
		"../"._DIR_PLUGIN_CHECKLINK."/img_pack/checklink-24.png",  // icone
		_T("sites_web") //titre
		);
	}
	return $boutons_admin;
}

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
	checklink_extrait_liens($id_table,$id_objet,$letexte);
	return $flux;
}

function checklink_post_propre($flux){
	// recuperer les liens des balises a et img
	if (preg_match_all(
	',<(a) [^>]*>,UimsS',
	$flux, $regs, PREG_SET_ORDER)) {
		$a_tag=array();$a_tag_repl=array();	
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
			if ($url){
				$tag_repl = $tag = $reg[0];
				// prevoir les liens dont les attributs ont pu etre renseignes a la main
				$titre = extraire_attribut($reg[0], 'title');
				$lang = extraire_attribut($reg[0], 'lang');
				$titre_auto = strlen($titre)?'non':'oui';
				$lang_auto = strlen($lang)?'non':'oui';
				
				if ($titre_auto=='oui' OR $lang_auto=='oui')
					if ($row = spip_fetch_array(spip_query("SELECT * FROM spip_liens WHERE url=".spip_abstract_quote($url)." AND titre_auto='$titre_auto' AND lang_auto='$lang_auto'"))){
						if ($row['statut']=='sus' OR $row['statut']=='off'){
							$class = extraire_attribut($tag_repl,'class');
							$tag_repl = inserer_attribut($tag_repl,'class',"$class spip_url_obsolete");
							$tag_repl = inserer_attribut($tag_repl,'title',_T('syndic_lien_obsolete'));
						}
						else{
							if ($titre_auto=='oui' AND strlen($row['titre']))
								$tag_repl = inserer_attribut($tag_repl,'title',$row['titre']);
							if ($lang_auto=='oui' AND strlen($row['lang']))
								$tag_repl = inserer_attribut($tag_repl,'hreflang',$row['lang']);
						}
						if ($tag_repl!=$tag){
							$a_tag[] = $tag;
							$a_tag_repl[] = $tag_repl;
						}
					}
			}
		}
		$flux = str_replace($a_tag,$a_tag_repl,$flux);
	}
	return $flux;
}

?>