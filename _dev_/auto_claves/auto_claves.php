<?php

include_spip('inc/texte');
include_spip('inc/layer');
include_spip("inc/presentation");
// A�do JSJ
// V0.03
//
define ("MODO_AUTOCLAVE","auto"); // on /off Modo prefijado para el plugin palabra clave: Automático, Manual= s�bot�utoclave
define ("ESTADO_NUEVAS","on"); // on /off Define el estado para nuevas palabra clave encontradas
define ("PARCIAL_NUEVAS","on"); // on /off Define el estado para nuevas palabra clave encontradas solo parcialmente
define ("HEREDADAS","heredada"); // Define la clase para las palabras heredadas de las secciones padre del art�lo
define ("ESTADO_HEREDADAS","on"); // on /off Define el estado para las palabra clave marcadas en secciones padre del art�lo
define ("EDITAR_HEREDADAS","on"); // on /off Define el si el estado para las palabra clave heredadas (procedenes de ls secciones ascendentes) son editables o mantiene el estado prefijado con ESTADO_HEREDADAS. Ajuste del adminitsrador


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_AUTO_CLAVE',(_DIR_PLUGINS.end($p))); 

function build_checkbox($name, $editable, $value='')
{
	$checkbox_string="<input type='checkbox'  ";
	if($editable=="off") {
		$checkbox_string.=" name='off-".$name."' onclick='return false' ";  // checkbox no editable y off
		}
	else {
		$checkbox_string.=" name='".$name."' checked='checked' ";  // checkbox editable y marcada on
	}
	
	$checkbox_string.= "value='".$value."' /> ";
	return $checkbox_string;
}

function autoclaves_cabecera_grupo($grupo, $numerogrupo) {
	$grupo = "<b>$grupo-$numerogrupo</b><input type='radio' name='todas$numerogrupo' value='$numerogrupo-on' checked='checked' '-' id='todas$numerogrupo'>" ._T('autoclaves:todas') ."<input type='radio' name='todas$numerogrupo' value='$numerogrupo-off' id='ninguna$numerogrupo'>" ._T('autoclaves:ninguna') .'<br />';
	return $grupo;
}

function autoclaves_palabras($palabra, $numerogrupo, $numeropalabra, $tipo) {
	$editable = ($tipo == "previa") ? "off" : "on" ;  // no editable si es una palabra ya existente en el art�lo
	$palabra = '<div class=' .$tipo .'>' .build_checkbox('palabras[]', $editable, $numeropalabra) .$palabra .'</div>';
	return $palabra;
}

function autoclaves_heredadas($palabra, $numerogrupo, $numeropalabra) {
	$activo = "off" ;
	$palabra = '<div class=' .HEREDADAS .'>' .build_checkbox($palabra, $activo) .$palabra .'</div>';
	return $palabra;
}

// http://leadnetwork.nordpasdecalais.fr/squelette/fonctionadm.html
// http://osdir.com/ml/web.spip.zone.cvs/2006-12/msg00467.html
// http://osdir.com/ml/web.spip.zone.cvs/2006-12/msg00467.html
// 

function autoclaves_leer_palabras($ret)
{
// Lee todas las palabras, clasificadas por el (n�) grupo
// Recoge el n� y t�lo del grupo, id_groupe y type, as�omo n� y texto de cada palabra clave
 $ret .= " --- leer palabras <br />";
 $r = spip_query("SELECT id_mot, titre, id_groupe, type FROM spip_mots ORDER BY id_groupe");
	while($o = spip_fetch_array($r))
		{
		$ret .= $o[id_groupe] .'-'.$o[type] .'=' .$o[id_mot] .'-'.$o[titre].'<br />';
	$palabras[]=$row['titre'];
	$palabras[]=$row['id_mot'];
	$palabras[]=$row['id_groupe'];
	$palabras[]=$row['type'];
	$palabras[]=$row['id_groupe'];
		}
	return $ret;
}

function autoclaves_definirspan($letexte) {
	$letexte = preg_replace('`<ac_(.*)>(.*)</ac_\1>`iU', '<span class="ac_$1">$2</span>', $letexte);
	return $letexte;
}

function autoclaves_inclurecss($flux) {
	$flux .= '<!-- plugin auto_claves v1 -->'."\n";
	$flux .= '<link href="'. _DIR_PLUGIN_AUTO_CLAVE .'/auto_claves.css" rel="stylesheet" type="text/css" />'."\n";
	$flux .= '<script src="'. _DIR_PLUGIN_AUTO_CLAVE .'/check.js" type="text/javascript"></script>'."\n"; 
// plugins/auto_claves/ s�en parte p�a
// ../plugins/auto_claves/  mal en p�a
// /spip/plugins/auto_claves/ OK en parte privada y privada
//	$flux .= '<link href="'.'/spip/plugins/auto_claves/'.'auto_claves.css" rel="stylesheet" type="text/css" />'."\n";
	$flux .= '<!-- /plugin auto_claves -->'."\n";
	return $flux;
}

function autoclaves_affiche_droite($arguments) {  
//  global $connect_statut, $connect_toutes_rubriques, $connect_id_rubrique;
  global $connect_statut, $connect_toutes_rubriques;
  if(_request('exec') == 'articles') {

	//busco las palabras claves encontradas 
	$id_art = intval($arguments['args']['id_article']);
	$palabras = autoclaves_buscar_mots($id_art);
	
	

	$to_ret = '';
	$to_ret .= '<div>&nbsp;</div>';
	$to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
	$to_ret .= "<div style='position: relative;'>";
	$to_ret .= "<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>
	<img src=\""._DIR_PLUGIN_AUTO_CLAVE."/img/updown.png\"/></div>";
	$to_ret .= "<div style='background-color: white; color: black; padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2'><b>"._T('autoclaves:palabras_clave')."</b></div>";
	$to_ret .= "</div>";
	
	$to_ret .= '<div class="plan-articles">';
	$to_ret .= '<div id="lista_auto_claves"><form action="?exec=addclaves" method="post" name="myform">'; 
	$to_ret .= '<b>' ._T('autoclaves:Selec_loc'). '</b>  '. count($palabras).'<br/>'; /*  /XX<br />'; */
	
	$to_ret .= _T('autoclaves:claves') ."<input type='radio' name='todosc' value='todost' id='todos1c' checked='CHECKED' onclick='checkAll(\"palabras[]\")'>" ._T('autoclaves:todas') ."<input type='radio' name='todosc' value='ningunot' id='todos1c' onclick='uncheckAll(\"palabras[]\")'>" ._T('autoclaves:ninguna') .'<br />';
	
/*	$to_ret .= _T('autoclaves:claves') ."<input type='radio' name='todosc' value='todost' '-' id='todos1c' CHECKED>" ._T('autoclaves:todas') ."<input type='radio' name='todosc' value='ningunot' '-' id='todos1c'>" ._T('autoclaves:ninguna') .'<br />'; */

	
	foreach($palabras as $palabra) {
		$to_ret .= autoclaves_palabras($palabra['titre'], $palabra['id_groupe'], $palabra['id'], $palabra['previa']);

	}

    $arguments['data'] .= $to_ret ;
	
	
	
     	
// Leer datos actuales
/*

  if (($connect_statut == '0minirezo') AND $connect_toutes_rubriques) {
	if($arguments['args']['exec'] == 'articles') {
	  $arguments['data'] .= autoclaves_boite_tri_mots($arguments['args']['id_article'],'articles','id_article','articles');
	}
	else if($arguments['args']['exec'] == 'naviguer') {
	  $arguments['data'] .= autoclaves_boite_tri_mots($arguments['args']['id_rubrique'],'rubriques','id_rubrique','naviguer');
	}
	else if($arguments['args']['exec'] == 'auteurs_edit') {
	  $arguments['data'] .= autoclaves_boite_tri_mots($arguments['args']['id_auteur'],'auteurs','id_auteur','auteurs_edit');
	}
	else if($arguments['args']['exec'] == 'mots_edit') {
	  $arguments['data'] .= icone(_T('trimots:titre_page',array('objets' => _T('public:articles'))),generer_url_ecrire('tri_mots','objet=articles&ident_objet=id_article&id_mot='.$arguments['args']['id_mot'].'&retour='.urlencode(generer_url_ecrire('mots_edit',"id_mot=".$arguments['args']['id_mot']))), '../'._DIR_PLUGIN_AUTO_CLAVE.'/img/updown.png', "rien.gif");
	$arguments['data'] .= icone(_T('trimots:titre_page',array('objets' => _T('public:rubriques'))),generer_url_ecrire('tri_mots','objet=rubriques&ident_objet=id_rubrique&id_mot='.$arguments['args']['id_mot'].'&retour\
='.urlencode(generer_url_ecrire('mots_edit',"id_mot=".$arguments['args']['id_mot']))), '../'._DIR_PLUGIN_AUTO_CLAVE.'/img/updown.png', "rien.gif");
	}
  }
*/

  $arguments['data'] .= "<input type='hidden' name='id_article' value='".$id_art."' />"; 
  $arguments['data'] .= "<input type='hidden' name='exec' value='".$id_art."' />"; 
  $arguments['data'] .=   "<br /> <input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
  $arguments['data'] .=  '</div>';
  $arguments['data'] .= '</div></div>'; 
  $arguments['data'] .=  "\n" ;

// fin_cadre(true);

//	$arguments['data'] .= $to_ret;

  }
  return $arguments;
} 

function autoclaves_boite_tri_mots($id,$objet,$id_objet,$retour) {
  global $spip_lang_left;
  include_spip('base/abstract_sql');
//  $installe = unserialize(lire_meta('TriMots:installe'));
  $from = array("spip_mots_$objet as lien",'spip_mots as mots');
  $select = array('lien.id_mot','mots.titre','mots.id_groupe','mots.type');
  $where = array('lien.id_mot=mots.id_mot',"lien.$id_objet=$id");
  $un_mot = false;

  $palabras=array();

  $rez = spip_abstract_select($select,$from,$where);
  while($row = spip_abstract_fetch($rez)) {
	$to_ret .= autoclaves_palabras($row['titre'].'='.$row['id_mot'] .'-:'.$row['id_groupe'].'--'.$row['type'],  $row['id_groupe'], $row['id_mot'], "previa");
	$palabras[]=$row['titre'];
	$palabras[]=$row['id_mot'];
	$palabras[]=$row['id_groupe'];
	$palabras[]=$row['type'];
	$palabras[]=$row['id_groupe'];

	$un_mot =true;
  }

 
  	$from = array('spip_articles');
	$select = array('id_rubrique');
	$where = array("id_article="._request('id_article'));
	
	$row =  spip_abstract_fetch(spip_abstract_select($select,$from,$where));
	$id_rubrique = $row['id_rubrique'];

//	$to_ret .= '<div class=' .HEREDADAS .'>' ." Secci&oacute;n=" .$id_rubrique .'</div>';
	
	$from = array("spip_mots_rubriques as lien",'spip_mots as mots');
	$select = array('lien.id_mot','mots.titre','mots.id_groupe','mots.type');
	$where = array('lien.id_rubrique=$id_rubrique',"mot.id_mot=lien.$id_mot");
	$row = spip_abstract_select($select,$from,$where);

//	$to_ret .= '<div class=' .HEREDADAS .'>' .build_checkbox($row['titre'] ."#" .$row['id_mot'] ." heredada", "on") ." S=".$id_rubrique ."#" .$row['id_mot'] .$row['type'] .$row['titre'] .'</div>';

	$result = spip_query("SELECT id_mot, id_rubrique FROM spip_mots_rubriques WHERE id_rubrique=$id_rubrique");
	if ($row = spip_fetch_array($result))
		$type = (corriger_caracteres($row['id_mot']));
	else $type = (corriger_caracteres($type));

	$to_ret .= '<div class=' .HEREDADAS .'>' .build_checkbox($row['id_mot'] .$type .' heredada', "on") .$row['titre'] ."#" .$row['id_mot'] ."#" .$type ." S2=".$id_rubrique .'</div>';
	

       // Distinction unique / multiple selon le parametrage du groupe de mots
       $id_groupe = $id_rubrique ;
       $query = "SELECT unseul FROM spip_mots_rubriques WHERE id_groupe=$id_rubrique";
       $row = spip_fetch_array(spip_query($query));
       $multiple = ($row['unseul'] != 'oui');


       // Recuperer les choix
       $query = "SELECT id_mot, id_rubrique FROM spip_mots_rubriques WHERE id_rubrique=$id_rubrique";
       $result = spip_query($query);
       $liste = array();
       while ($row = spip_fetch_array($result)) {
		$id_mot = $row['id_mot'];
//			$titre = $row['titre'];
//			$liste[$id_mot] = $titre;
		$titre = $row['titre'];
		$liste[$id_mot] = $titre;
		$to_ret .= '<div class=' .HEREDADAS .'>' .build_checkbox($row['id_mot'].' heredada', "on") ."-" .$row['id_mot'] ."-S3=" .$id_rubrique .'</div>';
       }


      // Recuperer les choix
       $query = "SELECT r.id_mot, r.id_rubrique, mots.titre, mots.id_groupe, mots.type FROM spip_mots_rubriques AS r, spip_mots as mots WHERE r.id_rubrique=$id_rubrique, mot.id_mot=r.id_mot";
       $result = spip_query($query);
       $liste = array();
       while ($row = spip_fetch_array($result)) {
		$id_mot = $row['id_mot'];
			$titre = $row['titre'];
//			$liste[$id_mot] = $titre;
		$titre = $row['titre'];
		$liste[$id_mot] = $titre;
		$to_ret .= '<div class=' .HEREDADAS .'>' .build_checkbox($row['id_mot']." heredada", "on") ."-" .$row['id_mot'] .$row['titre'] ."-S4=" .$id_rubrique .'</div>';
       }
	
 // Listar las palabra claves
//	$to_ret .= autoclaves_leer_palabras($id_rubrique);


  if($un_mot)
   return $to_ret;
  else
   return '';
}

function autoclaves_boite_auto_claves($id_article) {
  global $spip_lang_left,$connect_id_auteur;
  
  include_spip('base/abstract_sql'); 
	$to_ret .= '<div>&nbsp;</div>';
	$to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
	$to_ret .= "<div style='position: relative;'>";
	$to_ret .= "<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>
	<img src=\""._DIR_PLUGIN_AUTO_CLAVE."/img/updown.png\"/></div>";
	$to_ret .= "<div style='background-color: white; color: black; padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2'><b>"._T('autoclaves:palabras_clave')."</b></div>";
	$to_ret .= "</div>";
	
	$to_ret .= '<div class="plan-articles" id="lista_tri_auto_claves">';
	$to_ret .= '<div id="lista_auto_claves">'; 

	$to_ret .= '</div>';
	$to_ret .= '</div></div>';
  return $to_ret;
} 


function autoclaves_buscar_mots($id_article, $id_groupe_mot = '') {
/* dado un articulo, busca todas las palabras claves mencionadas en su texto y devuelve un array. */

/*original code from 
http://trac.rezo.net/trac/spip-zone/browser/_plugins_/_test_/definitions_mots/definitions_mes_fonctions.php */

					$query = "SELECT surtitre, titre,soustitre,descriptif, chapo, texte, ps FROM spip_articles WHERE id_article=$id_article";
					
					$result_article = spip_query($query);
					
					$article = spip_fetch_array($result_article);
					$palabras = array();
					$ids = array();					
					
					foreach($article as $text){
					

					if ($id_groupe_mot == '')
						$result = spip_query("SELECT id_mot, id_groupe, titre from spip_mots");
					else
						$result = spip_query("SELECT id_mot, id_groupe, titre from spip_mots WHERE id_groupe=$id_groupe_mot");
								
					while( $row = spip_fetch_array($result))
					{
						if (stripos($text,$row['titre']) !== false)
						{	
						
						$id_mot = $row['id_mot'];
							
							if(!in_array($row['id_mot'],$ids) ){ //check for duplicated
								
																											
			                if( spip_num_rows(spip_query("SELECT * FROM spip_mots_articles WHERE id_mot=$id_mot AND id_article = $id_article"))>=1){
								$previa = "previa";
								}else{
								$previa = "";
								}
																			
																												
								$ids[] = $row['id_mot'];
								$palabras[] = array("id" => $row['id_mot'], "id_groupe" => $row['id_groupe'], "titre" => $row['titre'], "previa"=>$previa);
							}
						}//end stripos
						
					}
					}
	
	return $palabras;
	
}





?>