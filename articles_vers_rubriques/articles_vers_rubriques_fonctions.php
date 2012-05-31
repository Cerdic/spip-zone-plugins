<?php
//
// Auteur : mmmx, Didier, JLuc, www.ouhpla.net
// 
// Licence GPL 
//
//
// Transforme les articles seletionne des site en rubriques,
// les met dans une même rubrique
// et leur attribue optionnellement un admin

include_spip('base/articles_vers_rubriques_base');

include_spip('plugins/installer'); // spip_version_compare 3.x
include_spip('inc/plugin'); // spip_version_compare 2.x
if (spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0alpha', '>=')) {
	define('_SPIP3', true);
	include_spip('action/editer_liens');
} else {
	define('_SPIP3', false);
}

function articles_vers_rubriques($id_article_list, $id_parent, $auteur_admin, $statut_br) {
	$nouvel_rubrique = array();
	$message = "";
	
	// Si on a deja fait le process sur la breve, en phase de test par exemple, alors on saute.
	$id_articles = explode(',', $id_article_list);
	
	foreach($id_articles as $id_marticle)
	{
		$id_newrubrique = sql_getfetsel('id_rubrique', TABLE_ARTICLES_RUBRIQUES, 'id_article='.$id_marticle);
		if($id_newrubrique!=false) {
			$message .= 'Article n°'.$id_marticle.'deja traitée, vers la rubrique '.$id_newrubrique;
			continue;
		}
		
		if(!$resultats = sql_select('*', 'spip_articles', 'id_article='.$id_marticle)) {
			$message .=  ' erreur sur sql_select, avec n°article:'.$id_marticle.'<br>'.sql_error();
			continue;
		}
		if(sql_count($resultats)<1) {
			$message .= ' Pas de articles n°'.$id_marticle.' trouvée';
			continue;
		}

		$res = sql_fetch($resultats);

		// une rubrique = > id_rubrique, id_parent, titre, descriptif, texte, id_secteur, maj,  statut, date, lang, langue_choisie, extra, statut_tmp, date_tmp
		// spip 3 => profondeur non export, id_import,
	
		// un article =>  id_article, surtitre, titre, soustitre, id_rubrique, descriptif, chapo, texte, ps, date, statut, id_secteur, maj, export, date_redac, visites, referers, popularite, accepter_forum, date_modif, lang, langue_choisie, id_trad, extra, id_version, nom_site, url_site
		// un article spip 3 =>  	id_version 		virtuel
		$nouvel_rubrique['descriptif'] = '';
		//$message .= print_r($res);
		if($res['surtitre']!='') $nouvel_rubrique['descriptif'] .= '{{'.$res['surtitre']."}} \n\n ";
		if($res['soustitre']!='') $nouvel_rubrique['descriptif'] .= '{'.$res['soustitre']."} \n\n ";
		if($res['url_site']!='') $nouvel_rubrique['descriptif'] .="[".$res['nom_site'].'->'.$res['url_site'].']'." \n\n ";
		$nouvel_rubrique['descriptif'] .= $res['descriptif'];

		$nouvel_rubrique['titre'] = $res['titre'];

		$nouvel_rubrique['texte'] = $res['texte'];
		if($res['ps']!='') $nouvel_rubrique['texte'] .= '{{{P.S.}}}'."\n\n ".$res['ps'];
		

		if($id_parent==false) {
			// Le nouvel rubrique est créé dans la rubrique d'origine de l'article
			$id_parent = $res['id_rubrique'];
		}
		// Sinon, tous les nouveux rubriques sont créés dans une rubrique unique ; rien à faire dans ce cas
		
		$nouvel_rubrique['id_parent'] = $id_parent;
		if (_SPIP3) {
			$parent_profondeur = sql_getfetsel('profondeur', 'spip_rubriques', 'id_rubrique='.intval($id_parent));
		
			$nouvel_rubrique['profondeur'] = $parent_profondeur+1;
			
			$link_exec='rubrique';
		}
		else{
			$nouvel_rubrique['export'] = $res['export'];
			$nouvel_rubrique['id_import'] = $res['id_import'];	
				$link_exec='naviguer';
		}
		$nouvel_rubrique['id_secteur'] =$res['id_secteur'];
		

		// recherche du titre du secteur de la breve
		if (ARTICLE_SECTION_VERS_RUBRIQUE_SURTITRE) {
			$secteur = sql_getfetsel('titre', 'spip_rubriques', 'id_rubrique='.$res['id_rubrique']);
			$nouvel_rubrique['descriptif'] = $secteur;
		}
		
		// champs par defaut sur la création d'une rubrique
		$nouvel_rubrique['lang'] = $res['lang'] ;
		$nouvel_rubrique['langue_choisie'] = 'non';
		$nouvel_rubrique['date'] = $res['date'];		//
		$nouvel_rubrique['extra'] = $res['extra'];
		$nouvel_rubrique['maj'] = $res['maj'];
		$nouvel_rubrique['statut'] = 'publie';
		$nouvel_rubrique['statut_tmp'] = 'publie';
		
		$id_rubrique = sql_insertq('spip_rubriques', $nouvel_rubrique);
		if($id_rubrique)	$message.="<br>Article n°".$id_marticle." vers la <a href=\"?exec=$link_exec&id_rubrique=$id_rubrique\">rubrique ".$id_rubrique."</a>";
		else {
			$message .=  ' erreur sur sql_insertq pour nouvel rubrique de l\'article '.$id_marticle.'<br>'.sql_error();
			continue;
		}
		
		// relation article <=> auteur
		
		if($auteur_admin) {
			if (_SPIP3) {
				$auteurs=sql_query('SELECT aa.id_auteur, statut FROM spip_auteurs_liens AS aa LEFT JOIN spip_auteurs AS a1 ON aa.id_auteur=a1.id_auteur WHERE id_objet='.$id_marticle." AND objet='article'");
				while($mauteur = sql_fetch($auteurs)) {						
					if('1comite'==$mauteur['statut']) 
					{
						if(sql_update('spip_auteurs', array('statut' => sql_quote('0minirezo')),'id_auteur='.$mauteur['id_auteur'])) 
						{
							$message .= "<br>auteur ".$mauteur['id_auteur']." est admin partiel de la rubrique $id_rubrique" ;
							objet_associer(
											array("auteur"=>$mauteur['id_auteur']),
											array("rubrique"=>$id_rubrique));					
														
						}
					}
					else if('0minirezo'==$mauteur['statut']) {
						$r=sql_query("SELECT id_auteur FROM spip_auteurs_liens WHERE objet='rubrique' AND id_auteur=".$mauteur['id_auteur']);
						if(mysql_num_rows( $r )>0)
						{
							objet_associer(
								array("auteur"=>$mauteur['id_auteur']),
								array("rubrique"=>$id_rubrique));
							$message .= "<br>auteur ".$mauteur['id_auteur']." est admin partiel aussi de la rubrique $id_rubrique" ;
						
						}	
					}					
				}
			}
			else {
				$auteurs=sql_query('SELECT aa.id_auteur, statut FROM spip_auteurs_articles AS aa LEFT JOIN spip_auteurs AS a1 ON aa.id_auteur=a1.id_auteur WHERE id_article='.$id_marticle);
				while($mauteur = sql_fetch($auteurs)) {	
					if('1comite'==$mauteur['statut']) {
						sql_insertq('spip_auteurs_rubriques', array('id_auteur' => $mauteur['id_auteur'], 'id_rubrique' => $id_rubrique));
						if(sql_update('spip_auteurs', array('statut' => sql_quote('0minirezo')),'id_auteur='.$mauteur['id_auteur'])) $message .= "<br>auteur ".$mauteur['id_auteur']." est admin partiel de la rubrique $id_rubrique" ;
					}
					else if('0minirezo'==$mauteur['statut']) {
						$r=sql_query('SELECT id_auteur FROM spip_auteurs_rubriques WHERE id_auteur='.$mauteur['id_auteur']);
						if(mysql_num_rows( $r )>0)
						{
							sql_insertq('spip_auteurs_rubriques', array('id_auteur' => $mauteur['id_auteur'], 'id_rubrique' => $id_rubrique));
							$message .= "<br>auteur ".$mauteur['id_auteur']." est admin partiel aussi de la rubrique $id_rubrique" ;
						
						}	
					}					
				}
			}
		}
		
		// on s'occupe du logo
		$logobr = IMG_SPIP_PATH."/arton".$id_marticle;
		$logoart = IMG_SPIP_PATH."/rubon".$id_rubrique;
		
		$ext = "";
		if(file_exists($logobr.".jpg")) $ext = ".jpg";
		else if(file_exists($logobr.".png")) $ext = ".png";
		else if(file_exists($logobr.".gif")) $ext = ".gif";
	//	else $message.= '<br>impossible de trouver le logo pour l'article :'.$id_marticle;

		if($ext!="") if(!rename($logobr.$ext, $logoart.$ext)) $message.="<br>impossible de renommer:".$logobr.$ext;

		// on s'occupe des mots clés
		if (_SPIP3) {
			
			$mots = sql_allfetsel('id_mot', 'spip_mots_liens', array('objet='.sql_quote('article'), 'id_objet='.$id_marticle));
			if ($mots and $mots = array_map('array_shift', $mots)) {
				objet_associer(
					array("mot"=>$mots),
					array("rubrique"=>$id_rubrique));
			}
		} else {
			$mots = sql_select('id_mot', 'spip_mots_articles', 'id_article='.$id_marticle);
			while($motscles = sql_fetch($mots)) {		
				sql_insertq('spip_mots_rubriques', array('id_mot' => $motscles['id_mot'], 'id_rubrique' => $id_rubrique));
			}
		}
		
		// relation article <=> documents
		if (_SPIP3) {
			
			$docs = sql_allfetsel('id_document', 'spip_documents_liens', array('objet='.sql_quote('article'), 'id_objet='.$id_marticle));
			if ($docs and $docs = array_map('array_shift', $docs)) {
				objet_associer(
					array("document"=>$docs),
					array("rubrique"=>$id_rubrique));
			}
		} else {
			$docs = sql_select('id_document', 'spip_documents_liens', array('objet='.sql_quote('article'), 'id_objet='.$id_marticle));
			while($docslie = sql_fetch($docs)) {		
				sql_insertq('spip_documents_liens', array('id_document' => $docslie['id_document'], 'id_objet' => $id_rubrique,'objet'=>'rubrique','vu'=>$docslie['vu']));
			}
		}
		
		// Gestion du statut de l'article
		switch($statut_br) {
			case 'idem':
				// Rien à faire
				break;
			case 'prop':
				sql_updateq('spip_articles', array('statut' => 'prop'), 'id_article='.$id_marticle);
				break;
			case 'refus':
				sql_updateq('spip_articles', array('statut' => 'refuse'), 'id_article='.$id_marticle);
				break;
		}

		// correspondance id_marticle <-> id_article
		sql_insertq(TABLE_ARTICLES_RUBRIQUES, array('id_article' => $id_marticle, 'id_rubrique' => $id_rubrique));
	}
	return $message;
}


function trouve_idrubrique($masque) {
	$id_rubrique = sql_getfetsel('id_rubrique', TABLE_ARTICLES_RUBRIQUES, 'id_article='.$masque[2]);
	
	if($id_rubrique != false)
		return '['.$masque[1].'->rubrique'.$id_rubrique.']';

	else {
		echo "<br><b>Pas de articles:".$masque[2]." trouvée dans la table ".TABLE_ARTICLES_RUBRIQUES."</b><br>";
		return '['.$masque[1].'->article'.$masque[2].']';
	}
}

function traite_table_champ2($table, $id, $champs) {
	
	$select = $id;
	foreach ($champs as $i => $nom_champ) {
		$select .= ', '.$nom_champ;
	}
	
	$pattern = '/\[([^]]*)-\>[article]{0,7}([0-9 ]+)\]/';
	//$pattern = '/\[([^]]*)-\>[article]{0,7}(['.$id.' ]+)\]/';
	if($resultats = sql_select($select, $table)) {
		while($res = sql_fetch($resultats)) {

			// on parcourt tous les champs pour la recherche de liens vers les articles
			foreach ($champs as $i => $nom_champ) {
				$string = $res[$nom_champ];
				if($string!='') {
					$count = 0;
					$new_string = preg_replace_callback($pattern, 'trouve_idrubrique', $string);
		//print "pattern  $newstring";
					if($new_string == NULL){
						echo "<br><b>Erreur sur preg_replace_callback ... sur champ:$nom_champ et id:$res[$id]</b><br>";
						//print "pattern $pattern, e stinga $string";
						}
					/*else if($count==0) {
						print "pattern  $newstring";exit;
						// echo "Rien à faire ...";
					}*/
					else if($new_string != ''){
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


// Fonction de recherche des liens vers une article
// Changer ces liens qui pointaient vers l'article vers la rubriqe 
function modif_liens2() {
	traite_table_champ2('spip_articles', 'id_article', array("surtitre","titre","soustitre","texte","chapo","ps"));
	traite_table_champ2('spip_rubriques', 'id_rubrique', array("titre","descriptif","texte"));
	traite_table_champ2('spip_auteurs', 'id_auteur', array("bio"));
	traite_table_champ2('spip_forum', 'id_forum', array("texte"));
	traite_table_champ2('spip_syndic', 'id_syndic', array("descriptif"));
}

?>
