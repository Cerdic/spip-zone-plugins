<?php
/*
 * Spip mymap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

include_spip('exec/mymap');

function mymap_mymapmot($flux){
	if (_request('exec')=='mots_edit'){
		include_spip('inc/parte_privada');
		$flux['data'] .= mymap_mots($flux['arg']['id_mot']);
	}
	return $flux;
}

function mymap_insertar_maparticle($flux){
	if (_request('exec')=='articles'){
		include_spip('inc/parte_privada');
		$flux['data'] .= mymap_cambiar_coord($flux['arg']['id_article']);
	}
	return $flux;
}

// --------------------------------
// inserta no head da parte PRIVADA
// --------------------------------
function mymap_insertar_head($flux){
	if (($r=_request('exec'))=='articles' OR _request('exec')=='mots_edit' OR $r=='mymap'){
		$flux .= '<script type="text/javascript" src="'.generer_url_public('mymap.js').'"></script>';
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_MYMAP.'js/swfobject.js"></script>';
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_MYMAP.'js/mymap.js"></script>';
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_MYMAP.'js/customControls.js"></script>';
		if ((_request('exec')=='articles'))
			$flux .= '<script type="text/javascript">
		$(document).ready(function() {
			//$(\'#cadroFormulario\').hide();
			mapTypeControl();
		});
		</script>';
	}
	return $flux;
}
// --------------------------------
// DEMANDE SI ON VEUT INSERER UN PLAN 
// --------------------------------
function mymap_affiche_droite($flux) {
	$exec = _request('exec');
	if ($exec == 'articles_edit' && isset($_GET["id_article"])) 	$flux['data'].= mymap_afficher_insertion_plan();
	if ($exec == 'articles') 	$flux['data'].= mymap_afficher_creation_plan();
	return $flux;
}

// --------------------------------
// CREE UNE  BOITE POUR INSERER DIRECTEMENT LE RACOURCI GOOGLEMAP
// --------------------------------
function mymap_afficher_insertion_plan() {	
	global $couleur_foncee, $couleur_claire, $options;
	global $spip_lang_left, $spip_lang_right;

	//SI ON EST DANS UN ARTICLE QUI A LE MOT-CLE PLAN
	$query ='SELECT id_article FROM spip_mymap_articles WHERE id_article ='.$_GET['id_article'] ;
	$requete= spip_query($query);
	if(mysql_num_rows($requete)!=0){			

		$s = "";
		$s.= "\n<p>";
		$s.= debut_cadre_enfonce(_DIR_PLUGIN_MYMAP."img_pack/correxir.png",true,"",_T('mymap:titre_inserer_gmap'));
		$s.= debut_block_depliable("ajouter_gmap");
		$s.= "<div class='verdana2'>";
		$s.= "<div title='Cliquez pour ins&eacute;rer la carte dans le texte' onclick=\"barre_inserer('\x3Ccarte_mymap1|id_article=".$_GET['id_article']."|type=carte>', $('.barre_inserer')[0]);\" style='cursor:pointer;text-align:center'>";
		$s.= "Ins&eacute;rer la carte";
		$s.= "</div>";
		$s.= "</div></p>";
		$s.= fin_block();
		$s.= fin_cadre_enfonce(true);
		return $s;
	}
}
function mymap_afficher_creation_plan() {	
	$query ='SELECT id_article FROM spip_mymap_articles WHERE id_article ='."'".$_GET['id_article']."'" ;
	//echo $query;
	$requete= spip_query($query);
	$s = "<script type='text/javascript'>";
	$s .= "function removeMap(){";
	$s .= "	$.ajax({";
	$s .= "type: 'POST',";
	$s .= "data: 'id_article=".$_GET['id_article']."',";
	$s .= "url:  '".'../spip.php?action=mymap_remove_gmap_from_article'."',";
	$s .= "async: false";
	$s .= "	}).responseText;";
	$s .= "	window.location.replace(\"". $GLOBALS['meta']['adresse_site']."/ecrire/?exec=articles&id_article=".$_GET['id_article']."\");";
	$s .= "}";
	$s .= "function addMap(){";
	$s .= "	$.ajax({";
	$s .= "			   type: 'POST',";
	$s .= "			   data: 'id_article=".$_GET['id_article']."',";
	$s .= "			   url:  '../spip.php?action=mymap_add_gmap_to_article',";
	$s .= "			   async: false";
	$s .= "	}).responseText;";
	$s .= "	window.location.replace(\"". $GLOBALS['meta']['adresse_site'].'/ecrire/?exec=articles&id_article='.$_GET['id_article']."\");";
	$s .= "}";
	$s .= "</script>";
	if(mysql_num_rows($requete)==0){	

		$s.= "\n<p>";
		$s.= debut_cadre_relief(_DIR_PLUGIN_MYMAP."img_pack/correxir.png",true,"",_T('mymap:joindre_gmap'));
		$s.= debut_block_depliable("ajouter_gmap");
		$s.= "<div class='verdana2'>";
		$s.= '<button class="fondo spip_xx-small"  onclick=\'addMap();\'">Ins&eacute;rer un plan Google Map</button>';
		$s.= "</div>";
		$s.= fin_block();
		$s.= fin_cadre_relief(true);
	}else{
		
		$s.= "\n<p>";
		$s.= debut_cadre_relief(_DIR_PLUGIN_MYMAP."img_pack/correxir.png",true,"",_T('mymap:joindre_gmap'));
		$s.= debut_block_depliable("ajouter_gmap");
		$s.= "<div class='verdana2'>";
		$s.= '<button class="fondo spip_xx-small" onclick=\'removeMap();\'">Retirer ce plan Google Map</button>';
		$s.= "</div>";
		$s.= fin_block();
		$s.= fin_cadre_relief(true);
	}
	return $s;
}

// --------------------------------
// inserta no head da parte PUBLICA
// --------------------------------
function mymap_affichage_final($flux){
    if ((strpos($flux, '<div id="map') == true) or (strpos($flux, '<div id="formMap') == true)){
	
		$incHead='
		<script type="text/javascript" src="'.generer_url_public('mymap.js').'"></script>
		<script type="text/javascript" src="'._DIR_PLUGIN_MYMAP.'js/swfobject.js"></script>
		<script type="text/javascript" src="'._DIR_PLUGIN_MYMAP.'js/mymap.js"></script>
		<script type="text/javascript" src="'._DIR_PLUGIN_MYMAP.'js/customControls.js"></script>';
        $incHead .= '<script type="text/javascript">
                $(document).unload(function(){
                	Gunload();
                });
                </script>';
        return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);
    } else {
		return $flux;
	}
}

function mymap_ajouter_boutons($boutons_admin){
	if(sql_countsel('spip_mymap_articles')) {			
		$boutons_admin['naviguer']->sousmenu['gmap_view'] = new Bouton(_DIR_PLUGIN_MYMAP."img_pack/correxir.png", _T('mymap:voir'));
	}
	return $boutons_admin;

}

?>