<?php
//
// Auteur : Didier, JLuc, www.ouhpla.net
// 
// Licence GPL 
//
//
// Transforme les brèves de tout le site en articles,
// les met dans une même rubrique
// et leur attribue optionnellement un même auteur

include_spip('base/breves_vers_articles_base');

include_spip('plugins/installer'); // spip_version_compare 3.x
include_spip('inc/plugin'); // spip_version_compare 2.x
if (spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0alpha', '>=')) {
	define('_SPIP3', true);
	include_spip('action/editer_liens');
} else {
	define('_SPIP3', false);
}

function breves_vers_articles($id_breve, $id_rubrique, $id_auteur, $statut_br) {
	$nouvel_article = array();
	$message = "";
	
	// Si on a deja fait le process sur la breve, en phase de test par exemple, alors on saute.
	$id_article = sql_getfetsel('id_article', TABLE_BREVES_ARTICLES, 'id_breve='.$id_breve);
	if($id_article!=false) {
		return 'Breve n°'.$id_breve.'deja traitée, vers l\'article '.$id_article;
	}
	
	if(!$resultats = sql_select('*', 'spip_breves', 'id_breve='.$id_breve)) {
		return ' erreur sur sql_select, avec n°breve:'.$id_breve.'<br>'.sql_error();
	}
	if(sql_count($resultats)<1) {
		return ' Pas de brève n°'.$id_breve.' trouvée';
	}

	$res = sql_fetch($resultats);

	// une breve = > date_heure titre texte lien_titre lien_url statut id_rubrique
	
	// un article => surtitre titre soustitre id_rubrique descriptif chapo texte ps date statut id_secteur
	//               maj export date_redac visites referers popularite accepter_forum date_modif
	//               lang langue_choisie id_trad nom_site url_site

	$nouvel_article['titre'] = $res['titre'];

	$nouvel_article['texte'] = $res['texte'];

	$nouvel_article['date'] = $res['date_heure'];
	$nouvel_article['date_modif'] = $res['date_heure'];
	$nouvel_article['date_redac'] = $res['date_heure'];

	$nouvel_article['nom_site'] = $res['lien_titre'];

	$nouvel_article['url_site'] = $res['lien_url'];

	$nouvel_article['statut'] = $res['statut'];

	if($id_rubrique==false) {
		// Le nouvel article est créé dans la rubrique d'origine de la bréve
		$id_rubrique = $res['id_rubrique'];
	}
	// Sinon, tous les nouveux articles sont créés dans une rubrique unique ; rien à faire dans ce cas
	
	$nouvel_article['id_rubrique'] = $id_rubrique;
	$secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique));
	$nouvel_article['id_secteur'] = $secteur;
	

	// recherche du titre du secteur de la breve
	if (BREVE_SECTION_VERS_ARTICLE_SURTITRE) {
		$secteur = sql_getfetsel('titre', 'spip_rubriques', 'id_rubrique='.$res['id_rubrique']);
		$nouvel_article['surtitre'] = $secteur;
	}
	
	// champs par defaut sur la création d'un article
	$nouvel_article['accepter_forum'] = 'pos';
	$nouvel_article['lang'] = 'fr';
	$nouvel_article['langue_choisie'] = 'oui';
	//$nouvel_article['id_trad'] = '0';
	//$nouvel_article['id_version'] = '1';
	//$nouvel_article['export'] = 'oui';
	
	$id_article = sql_insertq('spip_articles', $nouvel_article);
	if (!$id_article){
		$message.="<br>ECHEC Bréve n°".$id_breve;
	}
	else {
		$message.="<br>Bréve n°".$id_breve." vers l'article ".$id_article;

		// relation article <=> auteur
		if($id_auteur != '') {
			if (_SPIP3) {
				objet_associer(
					array("auteur"=>$id_auteur),
					array("article"=>$id_article));
			} else {
				sql_insertq('spip_auteurs_articles', array('id_auteur' => $id_auteur, 'id_article' => $id_article));
			}
		}

		// on s'occupe du logo
		$logobr = IMG_SPIP_PATH."/breveon".$id_breve;
		$logoart = IMG_SPIP_PATH."/arton".$id_article;

		$ext = "";
		if(file_exists($logobr.".jpg")) $ext = ".jpg";
		else if(file_exists($logobr.".png")) $ext = ".png";
		else if(file_exists($logobr.".gif")) $ext = ".gif";
	//	else $message.= '<br>impossible de trouver le logo pour la breve :'.$id_breve;

		if($ext!="") if(!rename($logobr.$ext, $logoart.$ext)) $message.="<br>impossible de renommer:".$logobr.$ext;

		// on s'occupe des mots clés
		if (_SPIP3) {

			$mots = sql_allfetsel('id_mot', 'spip_mots_liens', array('objet='.sql_quote('breve'), 'id_objet='.$id_breve));
			if ($mots and $mots = array_map('array_shift', $mots)) {
				objet_associer(
					array("mot"=>$mots),
					array("article"=>$id_article));
			}
		} else {
			$mots = sql_select('id_mot', 'spip_mots_breves', 'id_breve='.$id_breve);
			while($motscles = sql_fetch($mots)) {
				sql_insertq('spip_mots_articles', array('id_mot' => $motscles['id_mot'], 'id_article' => $id_article));
			}
		}

		// on s'occupe des forums : ok

		$desc_forum = sql_showtable('spip_forum', true);
		if($desc_forum['field']['id_breve']) {
		// Utilisation d'une table forum classique
			$forums = sql_select('id_forum', 'spip_forum', 'id_breve='.$id_breve);
			while($forum = sql_fetch($forums)) {
				sql_updateq('spip_forum', array('id_breve' => '', 'id_article' => $id_article), 'id_forum='.$forum['id_forum']);
			}
		}
		else if($desc_forum['field']['id_objet']){
		// Utilisation de la version objet de gestion des forums
			$forums = sql_select('id_forum', 'spip_forum', 'objet="breve" AND id_objet='.$id_breve);
			while($forum = sql_fetch($forums)) {
				sql_updateq('spip_forum', array('id_objet' => $id_article, 'objet' => 'article'), 'id_forum='.$forum['id_forum']);
			}
		}
		else
			$message .= "<br>Impossible de determiner quelle gestion est utilisée sur les forums (id_breve ou id_objet)";

		// Gestion du statut de la bréve
		switch($statut_br) {
			case 'idem':
				// Rien à faire
				break;
			case 'prop':
				sql_updateq('spip_breves', array('statut' => 'prop'), 'id_breve='.$id_breve);
				break;
			case 'refus':
				sql_updateq('spip_breves', array('statut' => 'refuse'), 'id_breve='.$id_breve);
				break;
		}

		// correspondance id_breve <-> id_article
		sql_insertq(TABLE_BREVES_ARTICLES, array('id_breve' => $id_breve, 'id_article' => $id_article));
	}

	return $message;
}


function trouve_idarticle($masque) {
	$id_article = sql_getfetsel('id_article', TABLE_BREVES_ARTICLES, 'id_breve='.$masque[2]);
	
	if($id_article != false)
		return '['.$masque[1].'->article'.$id_article.']';

	else {
		echo "<br><b>Pas de breve:".$masque[2]." trouvée dans la table ".TABLE_BREVES_ARTICLES."</b><br>";
		return '['.$masque[1].'->breve'.$masque[2].']';
	}
}

function traite_table_champ($table, $id, $champs) {
	
	$select = $id;
	foreach ($champs as $i => $nom_champ) {
		$select .= ', '.$nom_champ;
	}
	
	$pattern = '/\[([^]]*)-\>br[ev]{0,3}([0-9 ]+)\]/';

	if($resultats = sql_select($select, $table)) {
		while($res = sql_fetch($resultats)) {

			// on parcourt tous les champs pour la recherche de liens vers les breves
			foreach ($champs as $i => $nom_champ) {
				$string = $res[$nom_champ];
				if($string!='') {
					$count = 0;
					$new_string = preg_replace_callback($pattern, 'trouve_idarticle', $string, -1, $count);
	
					if($new_string == NULL)
						echo "<br><b>Erreur sur preg_replace_callback ... sur champ:$nom_champ et id:$res[$id]</b><br>";
					else if($count==0) {
						// echo "Rien à faire ...";
					}
					else {
						// echo "table:$table,id:".$res[$id].", String <br>$string<br>devient<br>".$new_string."<br>";

						sql_updateq($table, array($nom_champ => $new_string), $id.'='.$res[$id]);
					}
				}
			}
		}

	}
	else
		echo 'Erreur sur sql_select<br>'.sql_error();
}


// Fonction de recherche des liens vers une breve
// Changer ces liens qui pointaient vers la breve vers l'article (issu de la bréve)
function modif_liens() {
	traite_table_champ('spip_articles', 'id_article', array("surtitre","titre","soustitre","texte","chapo","ps"));
	traite_table_champ('spip_rubriques', 'id_rubrique', array("titre","descriptif","texte"));
	traite_table_champ('spip_auteurs', 'id_auteur', array("bio"));
	traite_table_champ('spip_forum', 'id_forum', array("texte"));
	traite_table_champ('spip_syndic', 'id_syndic', array("descriptif"));
}

?>
