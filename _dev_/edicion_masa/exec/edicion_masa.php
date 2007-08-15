<?php

/*
 * 
 * Edicion en Masa. 
* Edit articles in bulk: state, keys link, section, and so on. 
 * 
 *
 */






if (!defined('_DIR_PLUGIN_GESTIONDOCUMENTS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_GESTIONDOCUMENTS',(_DIR_PLUGINS.end($p)));
}






// Fonction appelee dans une boucle, calculer les invariants au premier appel.







function generer_query_string($conteneur,$id_type,$nb_aff,$filtre){
  $query = ($conteneur?"conteneur=$conteneur&":"")
		.($id_type?"id_type=$id_type&":"")
		.(($nb_aff)?"nb_aff=$nb_aff&":"")
		.(($filtre)?"filtre=$filtre&":"");

  return $query;
}	

charger_generer_url();




function exec_edicion_masa(){
	global $updatetable;
	global $connect_statut,  $connect_id_auteur;
	//global $modif;
	
	include_spip ("inc/presentation");
	include_spip ('inc/indexation');
	include_spip ("inc/logos");
	include_spip ("inc/session");



	//
	// Recupere les donnees
	//

	debut_page(_T("edicion:edicion_masa"), "documents", "documents");
	debut_gauche();


	//////////////////////////////////////////////////////
	// Boite "voir en ligne"
	//

	debut_boite_info();

	echo propre(_T('edicion:info_doc'));

	fin_boite_info();

		//debut_raccourcis();
	        echo "<div>&nbsp;</div>";
	        creer_colonne_droite();

	        debut_cadre_enfonce();

		echo "<p><font face='Verdana,Arial,Sans,sans-serif' size=1>";
                echo "<b>"._T('edicion:filtrar')."</b></font></p>";



		if ($table_need_update){
			icone_horizontale (_T('edicion:lala'), 
				generer_url_ecrire('portfolio_edit',"updatetable=oui&".generer_query_string($conteneur,$id_type,$nb_aff,$filtre)),
				"administration-24.gif");
		}


/**** formulario de filtros ******/

//mostrar todos 
echo "<form action='?exec=edicion_masa' method='post'><input type='submit' name='enviar' value='"._T('edicion:mostrar_todos')."' class='fondo' /></form>";


//filtro por seccion

echo _T('edicion:Seccion') . "<br />";

/*
//version tipo desplegable

		echo "<form action='".generer_url_ecrire('portfolio_edit',generer_query_string($conteneur,"",$nb_aff,$filtre))."' method='post'><div>\n";
		echo "<select name='id_rubrique'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('edicion_masa',generer_query_string($conteneur,"",$nb_aff,$filtre).'id_rubrique=')."'+this.options[this.selectedIndex].value\"";
		echo " class='forml' >" . "\n";
		$s=spip_query('SELECT * FROM spip_rubriques');
		echo "<option value=''>"._T("edicion:elija_seccion")."</option>";
		echo "<option value='todas'";
		if ($_GET['id_rubrique'] == "todas") echo " selected='selected'";
		echo ">"._T("edicion:tous2")."</option>";

		while ($row=spip_fetch_array($s)){
			echo "<option value='".$row['id_rubrique']."'";
			if ($row['id_rubrique'] == $id_rubrique) echo " selected='selected'";
			echo ">" . $row['id_rubrique'].". ".$row['titre'] ."</option>\n";
		}
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";
*/
//tipo input text
/*
	if (!strlen($id_rubrique)) {
		$seccion_aff = _T('edicion:ingrese_seccion');
		$onfocus = " onfocus=\"this.value='';\"";
	} else $onfocus = '';

	$onfocus = '<input type="text" size="10" value="'.$seccion_aff.'" name="id_rubrique" class="spip_recherche" accesskey="r"' . $onfocus . ' />';
	echo "<div style='width:200px;float:$spip_lang_right;'>".generer_form_ecrire("edicion_masa", $onfocus, " method='get'")."</div>";
*/
	echo "<form action='./' method='get'><input type='hidden' name='exec' value='edicion_masa'><input type='text' class='spip_recherche' name='id_rubrique' value='' size='10'/></form>";




//filtro por busqueda
	echo _T('edicion:busqueda') . "<br />";

/*	
	if (!strlen($recherche)) {
		$recherche_aff = _T('info_rechercher');
		$onfocus = " onfocus=\"this.value='';\"";
	} else $onfocus = '';

	$onfocus = '<input type="text" size="10" value="'.$recherche_aff.'" name="recherche" class="spip_recherche" accesskey="r"' . $onfocus . ' />';
	echo "<div style='width:200px;float:$spip_lang_right;'>".generer_form_ecrire("edicion_masa", $onfocus, " method='get'")."</div>";
*/

	echo "<form action='./' method='get'><input type='hidden' name='exec' value='edicion_masa'><input type='text' class='spip_recherche' name='recherche' value='' size='10'/></form>";



//filtro por mot
	echo _T('edicion:por_mot') . "<br />";

/*	if (!strlen($id_mot)) {
		$ingrese_mot = _T('edicion:ingrese_mot');
		$onfocus = " onfocus=\"this.value='';\"";
	} else $onfocus = '';

	$onfocus = '<input type="text" size="10" value="'.$ingrese_mot.'" name="id_mot" class="spip_recherche" accesskey="m"' . $onfocus . ' />';
	echo "<div style='width:200px;float:$spip_lang_right;'>".generer_form_ecrire("edicion_masa", $onfocus, " method='get'")."</div>";
*/

	echo "<form action='./' method='get'><input type='hidden' name='exec' value='edicion_masa'><input type='text' class='spip_recherche' name='id_mot' value='' size='10'/></form>";


		fin_raccourcis();




/*******fin barra lateral */


	global $connect_statut;
	if ($connect_statut != '0minirezo') {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}
	
creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
debut_droite();

/*** Genero las clausulas where para el filtrado **/

if(_request(id_rubrique)){
	if(_request(id_rubrique)=='todas'){
			$where = "";
	}else{
			$where = "id_rubrique='"._request(id_rubrique)."'";
	}

}
if(_request(recherche)){
	$where = "id_rubrique='"._request(id_rubrique)."'";
}

if(_request(id_mot)){

	$where = "id_article IN (SELECT id_article FROM spip_mots_articles WHERE id_mot="._request(id_mot).")";

}



/******** CONTROL DE SUBMIT ***************/

//cambio seccion
if($_POST['enviar']==_T('edicion:cambiar_seccion') ){
$listvals=$_POST['lista_articulos'];
$n=count($listvals);

	if ($n>0){

		//verifico si la seccion existe
		
		$s=spip_query('SELECT id_rubrique FROM spip_rubriques WHERE id_rubrique='.$_POST['id_rubrique_destino']);
		if(spip_num_rows($s) > 0){

		$q = "UPDATE spip_articles SET id_rubrique=".$_POST['id_rubrique_destino']." WHERE id_article IN (";
		foreach($listvals AS $valor) $q .= $valor.", ";
		$q .= $listvals[0].")";
		//echo $q;
		spip_query($q);
		echo $n." "._T("edicion:cambiando_articulos")." ".$_POST['id_rubrique_destino'];

		}else{
			echo _T("edicion:seccion_invalida");
		}

	}else{
		echo _T("edicion:ningun_articulo");
	}
}

//mots asociar
if($_POST['enviar']==_T('edicion:mot_asociar') ){

$listvals=$_POST['lista_articulos'];
$n=count($listvals);
 
	if ($n>0){
	//verifico si la mot existe

	$s=spip_query('SELECT id_mot FROM spip_mots WHERE id_mot='.$_POST['id_mot']);
		if(spip_num_rows($s) > 0){
		//la mot esta ok

	
			foreach($listvals AS $valor){
				$q = "INSERT IGNORE INTO spip_mots_articles () VALUES (".$_POST['id_mot'].", ".$valor.") ";
				spip_query($q);
			}

			echo $n." "._T("edicion:asociados_articulos")." ".$_POST['id_mot'];	

		}else{
		//no existe la mot
			echo _T("edicion:mot_invalida");

		}

	}else{
	//ningun articulo seleccionado

	echo _T("edicion:ningun_articulo");
	}
}

//mots quitar
if($_POST['enviar']==_T('edicion:mot_quitar') ){

$listvals=$_POST['lista_articulos'];
$n=count($listvals);
 
	if ($n>0){
	//verifico si la mot existe

	$s=spip_query('SELECT id_mot FROM spip_mots WHERE id_mot='.$_POST['id_mot']);
		if(spip_num_rows($s) > 0){
		//la mot esta ok

	
			foreach($listvals AS $valor){
				$q = "DELETE IGNORE from spip_mots_articles WHERE id_mot=".$_POST['id_mot']." AND id_article=".$valor;
				spip_query($q);
				mysql_error();
			}

			echo $n." "._T("edicion:quitados_articulos")." ".$_POST['id_mot'];	

		}else{
		//no existe la mot
			echo _T("edicion:mot_invalida");

		}

	}else{
	//ningun articulo seleccionado

	echo _T("edicion:ningun_articulo");
	}
}

//cambiar estado

if($_POST['enviar']==_T('edicion:cambiar_statut') ){

	$listvals=$_POST['lista_articulos'];
	$n=count($listvals);

	if ($n>0){

		$q = "UPDATE spip_articles SET statut='".$_POST['statut_nouv']."' WHERE id_article IN (";
		foreach($listvals AS $valor) $q .= $valor.", ";
		$q .= $listvals[0].")";
		//echo $q;
		spip_query($q);
		echo $n." "._T("edicion:cambiando_articulos_statut");



	}else{
	//ningun articulo seleccionado

	echo _T("edicion:ningun_articulo");
	}
}





/************impresion de listado. ******/

echo "<form name=\"lista\" id=\"lista\" action=\"?exec=edicion_masa\" method=\"post\">";
echo "<input type=\"checkbox\" onclick=\"checkAll(document.getElementById('lista'));\" />"._T("edicion:invertir");



//echo "<p>";
echo afficher_articles(_T('edicion:listado'), array("FROM" =>"spip_articles AS articles", "WHERE" => $where, 'ORDER BY' => "articles.date DESC"));
//echo "</p>";



//echo afficher_objets('article',_T('edicion:listado'),array("FROM" =>"spip_articles AS articles", "WHERE" => $where, 'ORDER BY' => "articles.date DESC"));



/***** comienza form para modificaciones******* 
***********************************/



echo "<div id='forms_cambio' style='margin-top: 25px'><table cellpadding='10'><tr>";

echo "<tr>";

//Cambiar Seccion

echo "<td>";

echo _T("edicion:cambiar_a_seccion")."<br>";

/*
//version desplegable

		echo "<select name='id_rubrique_destino'";
		echo generer_url_ecrire('edicion_masa',generer_query_string($conteneur,"",$nb_aff,$filtre).'id_rubrique=')."'+this.options[this.selectedIndex].value\"";
		echo " class='forml' >" . "\n";
		$s=spip_query('SELECT * FROM spip_rubriques');
		while ($row=spip_fetch_array($s)){
			echo "<option value='".$row['id_rubrique']."'";
			if ($row['id_rubrique'] == $id_rubrique) echo " selected='selected'";
			echo ">" . $row['id_rubrique'].". ".$row['titre'] ."</option>\n";
		}
		echo "</select>";
*/
echo "<input name='id_rubrique_destino' value='' type='text' class='forml' size='3'>\n";
echo "<input type='submit' name='enviar' value='"._T('edicion:cambiar_seccion')."' class='fondo' />";


echo "</td>";

//asociar a palabra clave
echo "<td>\n";
echo _T("edicion:cambiar_mot")."<br>";
echo "<input name='accion' value='palabra' type='hidden' >\n";
echo "<input name='id_mot' value='' type='text' class='forml' size='3'>\n";
echo "<input type='submit' name='enviar' value='"._T('edicion:mot_asociar')."' class='fondo' />";
echo "<input type='submit' name='enviar' value='"._T('edicion:mot_quitar')."' class='fondo' />";
echo "</td>\n";


//cambiar estado
echo "<td>\n";
echo _T("edicion:cambiar_statut")."<br>";
echo "<input name='accion' value='estado' type='hidden' >\n";
echo "<select class='fondl' size='1' name='statut_nouv'>
<option style='background-color: white;' selected='selected' value='prepa'>"._T("texte_statut_en_cours_redaction")."</option>
<option style='background-color: rgb(255, 241, 198);' value='prop'>"._T("texte_statut_propose_evaluation")."</option>
<option style='background-color: rgb(180, 232, 197);' value='publie'>"._T("texte_statut_publie")."</option>
<option class='danger' value='poubelle'>"._T("texte_statut_poubelle")."</option>
<option style='background-color: rgb(255, 164, 164);' value='refuse'>"._T("texte_statut_refuse")."</option>
</select>";

echo "<input type='submit' name='enviar' value='"._T('edicion:cambiar_statut')."' class='fondo' />";
echo "</td>\n";




echo "</tr>";
echo "</table></div>";

echo "</form>";

}

?>
