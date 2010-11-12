<?php




function objets_objets_extensibles($objets){
	//Cette fonction permet d'ajouter les objets, aux objets auxquels on peut ajouter des champs extra
	
	$objets_installes=liste_objets_meta();
	
	foreach ($objets_installes as $objet) {
		// on va quand même utilise _T si on veut pouvoir définir un nom d'objet dans les fichiers de langue
		// TODO : il faut mieux gérer cela , une paire clef valeur pour chaque nouvel objet ? 
		// c'est a mon avis un peu trop limitant ... 
		//il faut un tableau array('table','nom_objet','lien_article','lien_rubrique',...) et ce pour chaque nouvel objet
			
		
		/* TODO : il y a une limitation dans Chaps extra avec les noms des table et les nom des objets
		* les tables prennent des S a la fin alors que les objets non
		* ici il faut donc supprimer le s final de notre objet pour que cela fonctionne
		* 
		*  Il faut bien utiliser les fonctions natives de SPIP table_objet , 
	 */ 
		
		//TODO : attention a ce nommage utiliser plutot le pipeline declarer_tables_objets_surnoms /ecrire/base/connect_sql
		//du coup il faut saisir l'objet sans le s
		$nom_objet=objets_nom_objet($objet);
				
		$objets=array_merge($objets, array($nom_objet => _T('objets:'.$objet)));  
	}
	return $objets;
}

function objets_rechercher_liste_des_champs($tables){
	
	$objets_installes=liste_objets_meta();
	
	foreach ($objets_installes as $objet) {
    $tables[$objet] = array('titre' => 8);
	}
  return $tables;
}


function objets_affiche_enfants($flux){
	
	//TODO : il faut ici que l'on sache quel objet on doit ajouter sur les articles et sur les rubriques... 
	// ce n'est pas le cas encore. tous s'affichent sur les rubriques et sur les articles
	
	// ajout du bouton créer un nouvel objet dans les rubriques et les articles
	switch($flux['args']['exec']){
		case "naviguer":
			if($flux['args']['id_rubrique']!=""){
						
				$id_rubrique=$flux['args']['id_rubrique'];
							
				//on est dans la page de rubrique
				//on va lister les différents type d'objets
				//on va afficher un bouton "Ajouter" pour chaque type d'objet
				$presenter_liste=charger_fonction('presenter_liste','inc');
								
				$objets_installes=liste_objets_meta();
				foreach ($objets_installes as $objet) {
					$nom_objet=objets_nom_objet($objet);
					// 	dans les rubriques 
					$flux['data'].= icone_inline(_T('objets:icone_creer_objet')." : ".$nom_objet, generer_url_ecrire("objet_edit","objet=".$objet."&new=oui&retour=nav&id_rubrique=$id_rubrique&type=rubrique"), objets_vignette_objet($objet,"24","gif"), "creer.gif","right");
					$flux['data'].="<div class='nettoyeur'></div>";
					//on va rajouter la liste des Objets dans cette rubrique
					
					// alias sur l'id pour simplifier la gestion dans la fonction presenter_boucle
					$requete=array(
						"SELECT"=> "o.id_".$nom_objet." as id_objet,o.titre,o.statut,'".$objet."' as objet", //.sql_quote($objet)." as type_objet"
						"FROM"=> "spip_".$objet." o,spip_".$objet."_liens ol",
						"WHERE"=> "o.id_".$nom_objet."=ol.id_".$nom_objet." AND ol.objet='rubrique' AND ol.id_objet='".$id_rubrique."'",
						"ORDERBY"=>"o.id_".$nom_objet." DESC"
						);
						
					//TODO Avoir une pagination + modification du lien de l'affichage de la liste
					//$res = 	$presenter_liste($requete, 'presenter_message_boucles', $les_messages, $afficher_auteur, $important, $styles, $tmp_var, $titre,  "messagerie-24.gif");
					$les_objets='id_'.$nom_objet;
					$styles=array();
					//il faut générer une variable spécifique pour éviter les conflits dans les retours ajax 
					$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
					
					$flux['data'].=$presenter_liste($requete,'presenter_objet_boucle',$les_objets,true,false,$styles,$tmp_var,_T('objets:titre_liste').$nom_objet,objets_vignette_objet($objet,"24","gif"));
					
				}
			}
		break;
		case "articles":
			if($flux['args']['id_article']!=""){
						
				$id_article=$flux['args']['id_article'];
							
				//on est dans la page de rubrique
				//on va lister les différents type d'objets
				//on va afficher un bouton "Ajouter" pour chaque type d'objet
				$presenter_liste=charger_fonction('presenter_liste','inc');
								
				$objets_installes=liste_objets_meta();
				foreach ($objets_installes as $objet) {
					$nom_objet=objets_nom_objet($objet);
					// 	dans les rubriques 
					$flux['data'].= icone_inline(_T('objets:icone_creer_objet')." : ".$nom_objet, generer_url_ecrire("objet_edit","objet=".$objet."&new=oui&retour=articles&id_article=$id_article&type=article"), objets_vignette_objet($objet,"24","gif"), "creer.gif","right");
					$flux['data'].="<div class='nettoyeur'></div>";
					//on va rajouter la liste des Objets dans cette rubrique
					
					// alias sur l'id pour simplifier la gestion dans la fonction presenter_boucle
					$requete=array(
						"SELECT"=> "o.id_".$nom_objet." as id_objet,o.titre,o.statut,'".$objet."' as objet", //on est obligé d'avoir le objet a ce niveau 
						"FROM"=> "spip_".$objet." o,spip_".$objet."_liens ol",
						"WHERE"=> "o.id_".$nom_objet."=ol.id_".$nom_objet." AND ol.objet='article' AND ol.id_objet='".$id_article."'", //$id_article
						"ORDERBY"=>"o.id_".$nom_objet." DESC"
						);
						
					//TODO Avoir une pagination + modification du lien de l'affichage de la liste
					//$res = 	$presenter_liste($requete, 'presenter_message_boucles', $les_messages, $afficher_auteur, $important, $styles, $tmp_var, $titre,  "messagerie-24.gif");
					$les_objets='id_'.$nom_objet;
					$styles=array();
					//il faut générer une variable spécifique pour éviter les conflits dans les retours ajax 
					$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
					
					$flux['data'].=$presenter_liste($requete,'presenter_objet_boucle',$les_objets,true,false,$styles,$tmp_var,_T('objets:titre_liste').$nom_objet,objets_vignette_objet($objet,"24","gif"));
					
				}
			}
		break;		
	}
	
	return $flux;
}

function presenter_objet_boucle($row,$afficher){
	//$row recoit chaque ligne de la requete passé a presenter_liste
	//on va aller chercher les puces pour pouvoir changer de statut 
	include_spip('inc/objets_puce');
		
	$id_objet=$row['id_objet'];
	$titre=$row['titre'];
	$statut=$row['statut'];
	$objet=$row['objet'];
	
	$nom_objet=objets_nom_objet($objet);
	$id_rubrique=_request('id_rubrique');
	$id_article=_request('id_article');
	
	
	
		
	$s = "<a href='" . generer_url_ecrire("objet_edit","id_objet=".$id_objet."&objet=".$objet."&id_rubrique=".$id_rubrique."&id_article=".$id_article) . "' style='display: block;'>";
	
	//$s .= http_img_pack("$puce", "", "width='14' height='7'");
	$s .= "&nbsp;&nbsp;". objets_puce_statut($id_objet, $statut, $id_rubrique, $objet,true); // le true permet d'avoir l'ajax 
	$s.=typo($titre)."</a>";
	$vals[] = $s;
	return $vals;
} 


/* Gestion de l'affichage des documents */

//$GLOBALS['gestdoc_exec_colonne_document'][] = 'objet_edit';

//TODO parser sur tous les champs extra ceux qui ont un traitement propre pour les ajouter ici
//$GLOBALS['gestdoc_liste_champs'][] = 'descriptif';



//function objets_post_edition($flux){}

function objets_affiche_gauche($flux){
	if($flux['args']['exec']=='objet_edit'){
		//ajout des documents
		$objet=$flux['args']['objet'];
		$nom_objet=objets_nom_objet($objet);
		
		// le titre du bloc logo est dans une globale
		$GLOBALS['logo_libelles']['id_'.$nom_objet]= _T('objets:titre_logo');
		
		$iconifier = charger_fonction('iconifier', 'inc'); 
		//TODO : gestion des droits a ce niveau
		//$flag_editable = autoriser('modifier', 'evenement', $id_evenement, null, array('id_article' => $id_article));
		
		// Gestion du logo 
		//$flux['data'] .= $iconifier('id_'.$nom_objet, $flux['args']['id_objet'], 'objet_edit', $flag_editable);
		$contexte = array(
			'id_objet'=>$flux['args']['id_objet'],
			'objet'=>$flux['args']['objet'],
			'nom_objet'=>$nom_objet,
			'retour'=>generer_url_ecrire("objet_edit","id_objet=".$flux['args']['id_objet']."&objet=".$flux['args']['objet']."&id_rubrique=".$flux['args']['id_rubrique']."&id_article=".$flux['args']['id_article'])
		);
    $flux['data'].= recuperer_fond("prive/navigation/logo_objets",$contexte);
		
				
		// Gestion des documents
		// TODO : on ne peut pas gerer ca avec mediatheque car il se base sur le nom de l'objet déduit de l'exec et donc objet_edit, et donc il récupére "objet"
		// Mais cela revient quasiment au même ppuisque c'est le code de mediatheque qui est reporté ici
		$flux['data'].="<div class='cadre'>".recuperer_fond('prive/editer/colonne_document',array('objet'=>$objet,'id_objet'=>$flux['args']['id_objet']))."</div>"; 		
		
		
	}
	return $flux;
}


function objets_declarer_url_objets($objets){
	$objets_installes=liste_objets_meta();
	return array_merge($objets,$objets_installes);
	
}

// Si pas de critère "statut", on affiche que les réponses publiées
function objets_pre_boucle($boucle){
	$objets_installes=liste_objets_meta();
	if (in_array($boucle->type_requete,$objets_installes)) {
		$id_table = $boucle->id_table;
		$statut = "$id_table.statut";
		if (!isset($boucle->modificateur['criteres']['statut']) and !isset($boucle->modificateur['tout'])){
			$boucle->where[] = array("'='", "'$statut'", "sql_quote('publie')");
		}
	}
	return $boucle;
}


?>