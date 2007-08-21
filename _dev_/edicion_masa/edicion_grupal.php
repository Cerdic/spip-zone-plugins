<?php

/*
 * edicion grupal
 *
 * interfaz para la gestion masiva de articulos
 *
 * Auteur : Martin Gaitan gaitan@gmail.com
 * © 2007 - codigo bajo licencia GNU/GPL
 *
 */

if (!defined('_DIR_PLUGIN_GESTIONDOCUMENTS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_GESTIONDOCUMENTS',(_DIR_PLUGINS.end($p)));
}

	function EdicionGrupal_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" ) {

		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['naviguer']->sousmenu["edicion_masa"]= new Bouton(
			"../"._DIR_PLUGIN_GESTIONDOCUMENTS."/img_pack/icono.png",  // icone
			_T("edicion:editar_masa") //titre
			);
		}
		return $boutons_admin;
	}


	function EdicionGrupal_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}


	function EdicionGrupal_header_prive($flux){
		 $flux .=  "<script type=\"text/javascript\">
<!--
function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == \"checkbox\") {
		if(form.elements[i].name != \"redac_anterior\"){

			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
		}
	}
}

function getNumChecked(form)
{
	var num = 0;
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == \"checkbox\") {
			if(form.elements[i].checked == true)
				num++;
		}
	}
	return num;
}
//-->
</script>";
		return $flux;
	}

?>
