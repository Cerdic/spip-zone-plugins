<?php

function kconf_install($req) {
	$kconf_base_version = "0.2";
	if ($req=='test') { // vrai si installe
		spip_log($GLOBALS['meta']["kconf_base_version"]);
		return (isset($GLOBALS['meta']["kconf_base_version"]) 
			  AND version_compare($GLOBALS['meta']["kconf_base_version"],$kconf_base_version,'>='));
	}
	if ($req=='install') {
		kconf_upgrade($GLOBALS['meta']["kconf_base_version"],$kconf_base_version);
	}
	if ($req=='uninstall') {
		kconf_vider_tables();
	}
}

function kconf_upgrade($kconf_base_version,$version_cible) {
	include_spip('inc/kconf_utils');
	include_spip('public/kconf_balise_admin');
	if (!$kconf_base_version) {
		$res = spip_query("SHOW FULL COLUMNS FROM spip_kconfs");
		if ($row = spip_fetch_array($res))
			$kconf_base_version="0.1";
		else {
			kconf_creer_tables();
			spip_log("Installation toute fraîche de kconf faite !");
		}
	}
	if ($kconf_base_version=="0.1") {
		spip_log("besoin d'upgrade ********");
		include_spip('base/create');
		// prend la base en mémoire
		$req = sql_select('id_rubrique, valeurs','spip_kconfs');
		while ($row = sql_fetch($req)) {
			$id = intval($row['id_rubrique']);
			$valeurs[$id] = unserialize($row['valeurs']);
		}
		$kconf_chemin = $valeurs[0]['kconf_chemin'];
		// efface et prend la nouvelle
		sql_drop_table('spip_kconfs');
		kconf_creer_tables();
		global $kconf;

		// remplis kconf avec les valeurs par defaut des fichiers
		if (!is_dir("../$kconf_chemin")) {
			spip_log('kca:probleme_dossier '.$kconf_chemin);
		} else {
			$skels = preg_files(_DIR_RACINE."$kconf_chemin", '.html$');
			$parametrer = charger_fonction('parametrer', 'public');
			foreach ($skels as $skel) {
				$skel = preg_replace("/.html$/","",basename($skel));
				$type = ($skel=="rubrique") ? "public" : "prive";
				spip_log("on va lancer $skel");
				$t = time();
				$GLOBALS['var_mode']='recalcul';
				$envs['date'] = date('Y-m-d H:i:s', $t);
				$envs['id_rubrique'] = 0;
				$envs['kconf']['page'] = array('fichier'=>$skel,'type'=>$type,"I"=>'rubrique',"id_objet"=>0, 'O'=>'o');
				$envs['kconf']['contexte'] = array("I"=>'rubrique',"id_objet"=>0, 'O'=>'o');
				$parametrer(_DIR_RACINE.$kconf_chemin.$skel, $envs,'kconf');
				sql_insertq('spip_kconfs' ,array('fichier'=>"$skel", 'id_objet'=>0, 'objet'=>'rubrique', 'type'=>$type, 'mtime'=>'NOW()', "valeur"=>serialize($kconf['fichiers'][$skel]['valeur']) ));
			}
		}

		// remplis kconf avec les valeurs en base
		foreach ($valeurs as $id => $vals) {
			$type = $id ? 'public' : 'prive';
// 			spip_log("pour rubrique $id:");
			foreach ($vals as $clef => $val) {
// 				spip_log("$clef => $val,");
				if ($kconf['i']['rubrique']['o'][0]['clefs'][$clef]['defaut']!=$val) {
					$kconf['i']['rubrique']['o'][$id]['clefs'][$clef]['valeur'] = $val;
					$kconf['i']['rubrique']['o'][$id]['clefs'][$clef]['type'] = $type;
				}
			}
			kconf_enregistre('rubrique',$id,'o');
		}
// 		spip_log(var_export($kconf,true).", $kconf_chemin");

		effacer_meta('kconf');
		ecrire_metas();
		$kconf_base_version = "0.2"; // OUF
	}
	
	kconf_nettoyage();
	ecrire_meta('kconf_base_version',$version_cible);
	ecrire_metas();
	spip_log("Mise à jour de kconf fait !");
}

function kconf_vider_tables() {
	include_spip('base/abstract_sql');
	include_spip('base/kconf_base');
	foreach (array_keys(kconf_declarer_tables_auxiliaires(kconf_declarer_tables_principales(array()))) as $table)
		sql_drop_table($table);
	effacer_meta('kconf_base_version');
	ecrire_metas();
	spip_log("désinstallation de kconf fait !");
}

function kconf_creer_tables() {
	include_spip('base/create');
	creer_base();
	include_spip('base/abstract_sql');
	sql_insertq('spip_kconfs' ,array('fichier'=>'kconf', 'id_objet'=>0, 'objet'=>'rubrique', 'type'=>'prive', 'mtime'=>'NOW()'));
}


/*
rubrique => toutes les rubriques (alias de rubrique-0) (public)
racine => les rubriques racine (alias de rubrique=0) (protege)
kconf => alias de rubrique==0 (prive)
rubrique-2 => la rubrique 2 et toute la hiérarchie (public)
rubrique=2 => la rubrique 2 et juste ses enfants (protege)
rubrique==2 => la rubrique 2 (prive)
article => tous les articles
article-2 => les articles de la rubrique 2 et toute la hiérarchie (public)
article=2 => les articles de la rubrique 2 (protege)
article==2 => l'article 2 (prive)


$id_hierarchie represente le chemin jusqu'a la racine incluant la rubrique et 0
3 types: prive, protege, public

trouver une valeur pour un article:
if (isset($ka[$id_article]['bla']['valeur'])) {
	$val = $ka[$id_article]['bla']['valeur'];
} else {
	if (!isset($ka[$id_article]['hierarchie'])
		$ka[$id_article]['hierarchie'] = recuperer_hierarchie($id_article);
	foreach ($ka[$id_article]['hierarchie'] as $i => $id_rubrique) {
		if (!isset($kar[$id_rubrique])) {
			charger_hierarchie($kar,array_splice($ka[$id_article]['hierarchie'],$k));
		}
		if (isset($kar[$id_rubrique]['bla']['valeur'])) {
			$type = $kar[$id_rubrique]['bla']['type'];
			if (($type=='protege' && $i==1) || $type=='public') {
				$val = $kar[$id_rubrique]['bla']['valeur'];
				$cascade = $id_rubrique;
			}
		}
	}
}
if (!$val) return false;
return array($val,$cascade),

trouver une valeur pour une rubrique:
if (isset($kr[$id_rubrique]['bla']['valeur'])) {
	$val = $kr[$id_rubrique]['bla']['valeur'];
} else {
	if (!isset($kr[$id_rubrique]['hierarchie'])
		$kr[$id_rubrique]['hierarchie'] = recuperer_hierarchie($id_rubrique);
	foreach ($kr[$id_rubrique]['hierarchie'] as $i => $id_rubrique) {
		if (!isset($kr[$id_rubrique])) {
			charger_hierarchie($kr,array_splice($kr[$id_rubrique]['hierarchie'],$k));
		}
		if (isset($kr[$id_rubrique]['bla']['valeur'])) {
			$type = $kr[$id_rubrique]['bla']['type'];
			if (($type=='protege' && $i==1) || $type=='public') {
				$val = $kr[$id_rubrique]['bla']['valeur'];
				$cascade = $id_rubrique;
			} else {
			  $error = "prive"
			}
			break;
		}
	}
}
if (!$val) return false;
return array($val,$cascade),

la hierarchie se construit en remontant la chaine des rubriques et en empilant les morceaux qui doivent y être.

$ka['8']['bla']['valeur'] = "hu";
$ka['8']['bla']['type'] = "prive|public|protege";
$ka['8']['bla']['defaut'] = "hi";

un kca: bla article 8 - avec article=5 protege
cherche la cle, si elle n'existe pas
$kar['5']['bla']['valeur'] = $kar['5']['bla']['defaut'] = $valeur;
$kar['5']['bla']['type'] = 'protege';
à ce moment là c'est le calcul de la widget qui donne la $valeur par defaut
le réenregistrement de cette valeur se fait à la mise à jour du fichier
le test sur la mise à jour se fait lors de la lecture

une widget recoit nom, les valeurs de la balise, la valeur (si défini)
elle renvoie la widget et la valeur par defaut

enregistrement, bla article 8 - avec article=5 (protege)
si pas de type
 $type = type du parent=protege ? prive : public


soit faire un cache dans privé et produire un bouton nettoyer le cache
soit recalculer les valeurs à chaque recalcul du squelette
proposer un bouton pour passer de l'un à l'autre
le cache serait à priori trop couteux lors des updates

kconf.article	.objet = article
							.conteneur = rubrique
							.o.id_article.clefs.clef.type
																	.valeur
																	.defaut (uniquement si il est définit)
														.parent
							.c.id_rubrique.clefs.clef.type
																		.valeur
																		.defaut (uniquement si il est définit)
															.parent

dans kconfs on a:
objet id_objet type fichier mtime valeurs
 valeurs: array(array('nom de la variable', type de widget, valeur du widget))




// VIEUX
$ka['8']['bla']['public'] = "ha";
$kr['5']['bla']['prive'] = "ha";
$kr['5']['bla']['protege'] = "hi";
$kr['5']['bla']['public'] = "ho";

#KCONF_ART{bla,2}
 cherche si bla est défini pour art 2
  FROM kconf_articles WHERE id_article=2
 sinon cherche si bla est défini pour les rubriques parentes de 2
  FROM kconf_artrubs WHERE id_rubrique=$id_hierarchie

#KCONF_RUB{bla,3}
 cherche si bla est défini pour rub 3
  FROM kconf_rubriques WHERE id_rubrique=3
 sinon cherche si bla est défini pour les rubriques parentes de 3
  FROM kconf_rubriques WHERE id_rubrique=$id_hierarchie

*/

?>