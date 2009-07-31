<?php




function assoc_affiche_droite($flux){
	return $flux;
}


/* recuperation des liens pour appeler des panel 
 * ainsi que liens existants
 * $type : type utilise , par exemple article , rubrique ..
 * $id : id de l'element concerne
 * 
 */
function get_liste_assoc_type($type,$id){
	include_spip('inc/cfg_config');
	$tab = lire_config("php::assoc/$type");
	$retour ="";
	// recuperation des boutons d'appel de panel
	// si le type de relation est autorisé
	
	if (count($tab)>0){

		$retour = "
			<div id='choix_assoc'>
				<p class='titre'>Associer des elements à votre $type</p>
					<ul id='type_association'>
		";
		foreach ($tab as $cle=>$val) {
			$retour .="
					<li class='une_association' onclick='class_assoc(\"assoc_panel\",\"panel_$cle\",\"liste/$cle\",$id,\"$type\",\"$cle\")'>
								$cle
					</li>";
		}
		$retour .= "</ul></div>";
	}


	$val = lire_config("php::type_assoc");
	if (count($val)==0)return;
	$liste = array_keys($val);	
	$fonds = array("id"=>$id,"type"=>$type);
	
	for ($i = 0; $i < count($liste); $i++) {
		$retour .= recuperer_fond("fonds/liste/".$liste[$i],$fonds);
	}
		
	return $retour;
}


function assoc_affiche_milieu($flux){

	if (_request('exec') == 'articles') {
		$id = _request('id_article');
		$flux['data'] .= get_liste_assoc_type("article",$id);
	}

	if (_request('exec') == 'naviguer' && _request('id_rubrique')!="" && _request('id_rubrique')!=0) {
		$id = _request('id_rubrique');
		$flux['data'] .= get_liste_assoc_type("rubrique",$id);
	}	
	
	return $flux;
}

function assoc_header_prive($flux){
		$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_ASSOC."css/assoc_page.css' type='text/css'  />";
		$flux .= "<link rel='stylesheet' href='../plugins/assoc/css/ui.theme.css' type='text/css'  />";
		$flux .= "<link rel='stylesheet' href='../plugins/assoc/css/css/ui.datepicker.css' type='text/css'  />";
	
	
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_ASSOC."js/jquery-date-fr.js'></script>";
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_ASSOC."js/ui.draggable.js'></script>";
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_ASSOC."js/assoc.js'></script>";
		return $flux;
}


?>