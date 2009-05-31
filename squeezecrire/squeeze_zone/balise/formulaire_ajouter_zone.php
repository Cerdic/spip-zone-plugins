<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip ("inc/meta");
include_spip ("inc/session");
include_spip ("inc/autoriser");
include_spip('base/abstract_sql');

function balise_FORMULAIRE_AJOUTER_ZONE ($p) {
	$p = calculer_balise_dynamique($p,'FORMULAIRE_AJOUTER_ZONE', array('id_article'));
	return $p;
}

function balise_FORMULAIRE_AJOUTER_ZONE_dyn($id_article) {

	//verifier si la personne en question a le droit de creer un acces prive (s'il est l'auteur de l'article)
	if (!autoriser('modifier','article', $id_article)) {
		return;
	}

	//on recupere les infos de l'article necessaires
	$art = sql_select("*","spip_articles","id_article = "._q($id_article));
	$artinfos = sql_fetch($art);
	
	$id_rub_orig = $artinfos["id_rubrique"];
	$id_secteur = $artinfos["id_secteur"];
	$titre = $artinfos["titre"];
	$lang = $artinfos["lang"];
	
	// Si on est deja dans une zone on recup l'id_zone pour le supprimer plus tard
	$zone = sql_select("id_zone","spip_zones_rubriques","id_rubrique ="._q($id_rub_orig));
	$z = sql_fetch($zone);
	$id_zone = $z['id_zone'];
	
	// Si on est deja dans une zone on aura besoin de la rubrique parente de la rubrique
	$rub_parente = sql_select("*","spip_rubriques","id_rubrique = "._q($id_rub_orig));
	$a = sql_fetch($rub_parente);
	$id_rub_parente = $a['id_parent'];	
	
	//recuperer les donnees qui nous interessent
	$creer_zone = _request('creer_zone'); // true ou false = checkbox
	$supprimer_zone = _request('supprimer_zone'); // true ou false = checkbox
	
	$add_as_auteur = _request('add_as_auteur');
	$add_id_auteur = _request('add_id_auteur');

	$valider= _request('valider');
	
	if ($valider){
		if ($creer_zone){
		
			//creer la bonne rubrique avec le meme nom que l'article
			sql_insertq("spip_rubriques",  array("id_parent" => $id_rub_orig, "titre" => $titre, "id_secteur" => $id_secteur, "lang" => $lang));
			spip_log("Creation rubrique $titre", "squeeze_zone");
			
			//on recupere l'id de la rubrique
			$id_rub = mysql_insert_id();
			
			//on deplace l'article
			sql_updateq("spip_articles", array("id_rubrique" => $id_rub),"id_article=".$id_article);
			spip_log("Deplacement article $id_article $titre", "squeeze_zone");
			
			// on cree une zone "publique" qui a le meme nom que l'article (pas privee sinon les admin y auraient pas acces)
			sql_insertq("spip_zones", array("titre" => $titre, "publique" => "oui"));
			spip_log("Creation zone $titre", "squeeze_zone");
			
			// on recupere l'id de la zone en question
			$id_zone_creee = mysql_insert_id();
			
			// on applique cette zone a la rubrique
			sql_insertq("spip_zones_rubriques", array("id_zone" => $id_zone_creee, "id_rubrique" =>$id_rub));
			spip_log("Insertion article $id_article in zone $titre", "squeeze_zone");
			
			// on ajoute le ou les auteur(s) egalement
			$s = sql_select("id_auteur","spip_auteurs_articles","id_article ="._q($id_article));
			while($r = sql_fetch($s)){
				$id_auteur = $r["id_auteur"];
				sql_insertq("spip_zones_auteurs", array("id_zone" => $id_zone_creee, "id_auteur" =>$id_auteur));
				spip_log("Insertion auteur $id_auteur in zone $id_zone_creee - $titre", "squeeze_zone");
			}
			$invalider = true;	
		}
	
		else if ($supprimer_zone){
			// suppression des references a la zone dans les 3 tables zones
			sql_delete("spip_zones_auteurs", "id_zone = ".$id_zone);
			sql_delete("spip_zones_rubriques", "id_zone = ".$id_zone);
			sql_delete("spip_zones", "id_zone = ".$id_zone);
			spip_log("Suppression zone $id_zone ($titre)", "squeeze_zone");
	
			//on deplace l'article vers la rubrique parente
			sql_updateq("spip_articles", array("id_rubrique" => $id_rub_parente), "id_article=".$id_article);
			spip_log("Deplacement article $id_article $titre vers $id_rub_parente", "squeeze_zone");
			
			// on supprime definitivement l'ancienne rubrique
			sql_delete("spip_rubriques","id_rubrique = '$id_rub_orig'");
			spip_log("Suppression definitive de la rubrique $id_rubrique", "squeeze_zone");
	
			$invalider = true;
		}
		
		else {
			if ($add_id_auteur){
				if ($add_id_auteur = intval($add_id_auteur)) {
					$res =sql_select("id_auteur","spip_zones_auteurs","id_zone = $id_zone AND id_auteur=$add_id_auteur");
					if(!sql_fetch($res)){
						sql_insertq("spip_zones_auteurs",  array("id_zone" => $id_zone, "id_auteur" => $add_id_auteur));
						if($add_as_auteur){
							//on ajoute l'auteur a l'article
							sql_insertq("spip_auteurs_articles",  array("id_auteur" => $add_id_auteur, "id_article" => $id_article));
						}
						$invalider = true;
						spip_log("ajouter utilisateur $add_id_auteur a la zone $id_zone","squeeze_zone");
					}
				}
			}
		}
		if ($invalider){
			//invalider le cache afin de prendre en consideration les changements
			include_spip('inc/invalideur');
			suivre_invalideur("0",true);
			spip_log('invalider', 'squeeze_zone');
		}
		return header("Location: ".generer_url_article($id_article)."");
	}
	
	return array('formulaires/ajouter_zone', 0,
		array(
				'article' => $id_article,
				'rubrique' => $id_rub_orig,
				'titre' => $titre,
				'erreur' => $erreur
		)
	);
}
?>