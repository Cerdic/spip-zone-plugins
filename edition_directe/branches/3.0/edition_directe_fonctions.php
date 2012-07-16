<?php
// teste si l'objet est en mode edition directe ou non
function objets_edition_directe(){
	include_spip('inc/config');
	include_spip('inc/session');


	// Récuperer la séléction des objets depuis la config
	$objets=lire_config('edition_directe/objets');

	//Récupère les préférence de l'auteur, pour émodifier le choix par défaut 
	$prefs=session_get('prefs');

	// Apparament session_get  retourne suivantr le contenxte, un tableau serialisé, assuré qu'il ne soir pas sérialisé
	if(!is_array($prefs))$prefs=unserialize($prefs);	
	
	// Sie rien n'est configuré, on se base sur les objets éditables a la séléction personelle de l'auteur
	if(count($objets)<1){
		$objets=lister_objets($prefs);
		}
	else{
		//Sinon on prend les objets de la config, si l'auteur ne la pas désactivé
		$objets2=array();
		foreach($objets AS $objet){
		if($prefs['edition_directe'][$objet]!='inactive')$objets2[]=$objet;
			}	
		$objets=$objets2;
		}
	// Si l'auteur à activé des objets qui ne sont pas dans la configuration initiale, les prendre quand même en compte
	if(is_array($prefs['edition_directe'])){
		$objets_prefs=array();
		foreach($prefs['edition_directe'] AS $o=>$pref){
			if($pref!='inactive')$objets_prefs[]=$o;
			}
		$objets=array_merge($objets,$objets_prefs);
		}
		
	//Pipeline 
	$pipeline= pipeline('edition_directe_controle',array(
		    'args'=>array(
			'objet'=>$objet
		    ), 
		    'data'=>$objets
		));
	return $objets;
}

	
// Liste les objets disponible pour l'édition directe
function lister_objets($prefs){
	include_spip('base/objets');
	
	// Lister les objets éditables auf ceux qui sont désactivé par l'utilisateur	
	$liste_objets=lister_tables_objets_sql();
	$objets=array();
	foreach($liste_objets AS $o=>$valeur){
		if($valeur['editable'] AND $valeur['page'] AND $prefs['edition_directe'][$valeur['page']]!='inactive')$objets[]=$valeur['page'];
		}
	return $objets;	
	}	
?>
