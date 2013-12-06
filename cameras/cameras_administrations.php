<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/* Paramètres par défaut */
function maj_parametres()
{
	/* On active les révisions pour les articles et les caméras*/
	$obj_versions = lire_config('objets_versions');
	if (!in_array('spip_articles', $obj_versions)){$obj_versions[]='spip_articles';}
	if (!in_array('cameras', $obj_versions)){$obj_versions[]='cameras';}
	ecrire_config('objets_versions',$obj_versions);

	/* Documents attachés aux objets */
	$obj_documents = explode(",",lire_config('documents_objets'));
	array_pop($obj_documents);
	spip_log($obj_documents,'config');
	if (!in_array('spip_articles', $obj_documents)){$obj_documents[]='spip_articles';}
	if (!in_array('spip_rubriques', $obj_documents)){$obj_documents[]='spip_rubriques';}
	if (!in_array('cameras', $obj_documents)){$obj_documents[]='cameras';}
	$obj_doc_mod = implode(',',$obj_documents).",";
	ecrire_config('documents_objets',$obj_doc_mod);


	/* Paramètres préconfigurés */
	ecrire_meta('articles_descriptif','oui','non');
	ecrire_meta('articles_texte','oui','non');
	ecrire_meta('articles_urlref','oui','non');
	ecrire_meta('rubriques_descriptif','oui','non');
	ecrire_meta('rubriques_texte','oui','non');
	ecrire_meta('articles_mots','oui','non');
	ecrire_meta('config_precise_groupes','oui','non');
	ecrire_meta('accepter_inscriptions','oui','non');
	ecrire_meta('accepter_visiteurs','oui','non');
	ecrire_meta('messagerie_agenda','oui','non');
	ecrire_meta('articles_modif','oui','non');
	ecrire_meta('forums_publics','priori','non');
	ecrire_meta('formats_documents_forum','jpg,png','non');
	ecrire_meta('forum_prive_objets','oui','non');
	ecrire_meta('creer_preview','oui','non');
	ecrire_meta('activer_statistiques','oui','non');
	
	spip_log(lire_config('objets_versions'),'config');
	spip_log(lire_config('documents_objets'),'config');
}

/* Gestion des mots-clés */
function maj_mots()
{
	/* Création des groupes de mots-clés si nécessaire */
	$groupetest=sql_fetsel('titre' , 'spip_groupes_mots', 'titre LIKE "Choix éditorial" ');
	if (empty($groupetest)) {
				sql_insertq('spip_groupes_mots', array('titre'=>'Choix éditorial', 'unseul'=>'oui', 'obligatoire'=>'non','tables_liees'=>'articles', 'minirezo'=>'oui', 'comite'=>'non', 'forum'=>'non'));
			}
	/* On récupère l'id du groupe */
	$sel_id = sql_fetsel('id_groupe' , 'spip_groupes_mots', 'titre LIKE "Choix éditorial"');
	
	/* un peu de nettoyage */
	$groupetest=sql_fetsel('id_groupe' , 'spip_groupes_mots', 'titre LIKE "Mise en page" ');
	if ($groupetest){
		$liste_mots = sql_allfetsel('id_mot' , 'spip_mots', 'id_groupe='. $groupetest['id_groupe']);
		foreach ($liste_mots as $mots) {
			sql_updateq('spip_mots', array('id_groupe' => $sel_id['id_groupe'],'type'=>'Choix éditorial'), 'id_mot=' . intval($mots['id_mot']));
		}
		/*on efface le groupe*/
		sql_delete('spip_groupes_mots', 'id_groupe = ' . $groupetest['id_groupe']);
	}
	
	$groupetest=sql_fetsel('id_groupe' , 'spip_groupes_mots', 'titre LIKE "éditorial" ');
	if ($groupetest){
		$liste_mots = sql_allfetsel('id_mot' , 'spip_mots', 'id_groupe='. $groupetest['id_groupe']);
		if ($liste_mots){
			foreach ($liste_mots as $mots) {
				sql_updateq('spip_mots', array('id_groupe' => $sel_id['id_groupe'],'type'=>'Choix éditorial'), 'id_mot=' . intval($mots['id_mot']));
			}
		}
		/*on efface le groupe*/
		sql_delete('spip_groupes_mots', 'id_groupe = ' . $groupetest['id_groupe']);
	}
	
	/* tous les mots-clés "Une" anciennement créé sont placés dans le bon groupe sinon on le crée et on l'ajoute dans le groupe */
	$liste_mots = sql_allfetsel('id_mot' , 'spip_mots', 'titre LIKE "Une"');
	if ($liste_mots){
			foreach ($liste_mots as $mots) {
				sql_updateq('spip_mots', array('id_groupe' => $sel_id['id_groupe'],'type'=>'Choix éditorial'), 'id_mot=' . intval($mots['id_mot']));
			}
	}else{
		sql_insertq('spip_mots', array('titre'=>'Une', 'id_groupe'=>$sel_id['id_groupe'], 'type'=>'Choix éditorial' ));
	}
}

/* Rubriques par défaut */
function maj_rubriques($rubliste)
{
	foreach ($rubliste as $rub){
		$rubtest=sql_allfetsel('titre' , 'spip_rubriques', array('id_parent=0','titre LIKE "'.$rub[0].'"'));
		if (empty($rubtest)) {
			sql_insertq('spip_rubriques', array('titre'=>$rub[0], 'id_parent'=>'0', 'descriptif'=>$rub[1]));
		}
	}
}

function maj_site()
{
	$zone = explode(".",$GLOBALS['domaine_site']);
	ecrire_meta('nom_site',ucwords($zone[0]),'oui');
	ecrire_meta('slogan_site',_T('camera:slogan_site_cameras'),'oui');
}


function cameras_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('cameras')),
		array('maj_parametres'),
		array('maj_rubriques',array(
			array('La carte','de la surveillance'),
			array('Revue de web','veille, dossiers'),
			array('Le projet','Présentation')
			)
		),
		array('maj_mots'),
		array('maj_site'),
	);
	$maj['0.5.8'] = array(
		array('maj_tables', array('cameras')),
		array('maj_rubriques',array(
			array('La carte','de la surveillance'),
			array('Revue de web','veille, dossiers'),
			array('Le projet','Présentation')
			)
		),
		array('maj_site'),
	);
	$maj['0.6.1'] = array(
		array('maj_tables', array('cameras')),
		array('maj_parametres'),
		array('maj_mots'),
		array('maj_site'),
	);
	$maj['0.6.2'] = array(
		array('maj_tables', array('cameras')),
		array('maj_parametres'),
		array('maj_mots'),
		array('maj_site'),
	);
	
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
	
}


function cameras_vider_tables($nom_meta_base_version) {
	
	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('camera')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('camera')));
	sql_delete("spip_forum",                 sql_in("objet", array('camera')));
	
	effacer_meta($nom_meta_base_version);
}
?>
