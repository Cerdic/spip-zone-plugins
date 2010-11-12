<?php 


if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_objet_edit_dist(){
	

  $objet=_request('objet');
  $id_objet=_request('id_objet');
  $id_rubrique=_request('id_rubrique');
  $id_article=_request('id_article');
  $new=_request('new');
  $objets_installes=liste_objets_meta();
  $nom_objet=objets_nom_objet($objet);

  //if(!in_array($objet,$objets_installes)) return; // l'objet n'existe pas ou n'est pas défini
  
  //pas besoin de faire compliqué et d'avoir un id_objet spécifique pour chaque objet
  //donc pas de variable dynamique 
  
  
  $commencer_page = charger_fonction('commencer_page', 'inc');
  pipeline('exec_init',array('args'=>array('exec'=>'objet_edit','objet'=>$objet,'id_objet'=>$id_objet),'data'=>''));
  
  if ($id_version) $titre.= ' ('._T('objets:version')." $id_version)";

  echo $commencer_page(_T('objets:titre_page_objet_edit', array('titre' => $titre)), "naviguer", $objet, $id_rubrique);

  echo debut_grand_cadre(true);
  //echo afficher_hierarchie($id_rubrique,'',$id_article,'article');
  //TODO : ce serait bien de conserver la hiérarchie a ce niveau ( meilleure visibilité )
  
  echo fin_grand_cadre(true);

  echo debut_gauche("",true);

  // Pave "documents associes a l'objet"
  
  /*if (!$new){
    # affichage sur le cote des pieces jointes, en reperant les inserees
    # note : traiter_modeles($texte, true) repere les doublons
    # aussi efficacement que propre(), mais beaucoup plus rapidement
    traiter_modeles(join('',$row), true);
    echo afficher_documents_colonne($id_objet, $nom_objet);
  } else {
    # ICI GROS HACK
    # -------------
    # on est en new ; si on veut ajouter un document, on ne pourra
    # pas l'accrocher a l'article (puisqu'il n'a pas d'id_article)...
    # on indique donc un id_article farfelu (0-id_auteur) qu'on ramassera
    # le moment venu, c'est-a-dire lors de la creation de l'article
    # dans editer_article.
    
  	
  	//TODO : permettre d'ajouter des documents aux objets de type new
    //echo afficher_documents_colonne( 0-$GLOBALS['visiteur_session']['id_auteur'], $objet);
  }
  */

  //on ajoute les documents dans affiche_gauche 
  echo pipeline('affiche_gauche',array('args'=>array('exec'=>'objet_edit','objet'=>$objet,'id_objet'=>$id_objet,'id_rubrique'=>$id_rubrique,'id_article'=>$id_article),'data'=>''));
  echo creer_colonne_droite("",true);
  
  echo pipeline('affiche_droite',array('args'=>array('exec'=>'objet_edit','objet'=>$objet,'id_objet'=>$id_objet),'data'=>''));
  echo debut_droite("",true);
  

  // on va faire un recuperer fond du squelette contenant le CVT
	$contexte=array(
		'objet'=>$objet,
		'nom_objet'=>$nom_objet,
		'id_objet'=>$id_objet,
	  'id_'.$nom_objet=>$id_objet,
		'id_rubrique'=>$id_rubrique,
		'id_article'=>$id_article,
		'retour'=>_request('retour'),
		//'parents'=>objets_get_parents($id_objet,$objet),
		//TODO : modifier le findinpath pour l'icone ici ...
		'icone_retour'=>icone_inline(_T('icone_retour'), parametre_url(generer_url_ecrire('naviguer'),'id_rubrique',$id_rubrique), objets_vignette_objet($objet,"24","gif"), "rien.gif",$GLOBALS['spip_lang_left'])
	);
	if(intval($id_objet)==$id_objet && $id_objet!="new" && $new!="oui"){
		$contexte['titre']= sql_getfetsel("titre","spip_".$objet,"id_".$nom_objet."=".$id_objet);
	}
		
	//TODO : on va plutôt aller chercher le fichier spécifique  a l'objet s'il existe,
	// sinon le fichier générique qui appelera le CVT générique
	  
	$milieu = recuperer_fond("prive/editer/objet_edit", $contexte);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'objet_edit'),'data'=>$milieu));
  
	echo fin_gauche(), fin_page();
  
  
}

?>