<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_KCA_dist($p) {
	$widget = interprete_argument_balise(1,$p);
	$clef = interprete_argument_balise(2,$p);
	$valeurs = interprete_argument_balise(3,$p);
	
	if (!$clef) $clef="''";
	if (!$valeurs) $valeurs="''";
// 	spip_log("ici");

	if ($widget)
		$p->code = "kconf_calcul($widget,$clef,$valeurs,\$Pile[0]['kconf'])";
	else
		$p->code = "''";
// print_r($p);
	$p->interdire_scripts = false; // la balise ne renvoie rien
	return $p;
}

function kconf_calcul($widget_type=false,$clef=false,$valeurs=false,$contexte) {
	global $kconf;
	if (!function_exists($f = "kconf_$widget_type")) {
		$clef = $widget_type;
		$widget_type = "defaut";
		$f = "kconf_$widget_type";
	}

	// construction de la description des widgets du fichier
	$kconf['fichiers'][$contexte['page']['fichier']]['valeur'][] = array ($clef,$widget_type,$valeurs);
	
	$I = $contexte['contexte']['I'];
	$id_I = $contexte['contexte']['id_objet'];
	$O = $contexte['contexte']['O'];
	$Ip = $contexte['page']['I']; // interface de la page
	$id_Ip = $contexte['page']['id_objet']; // id de l'objet de la page
	$Op = $contexte['page']['O'];
	list($valeur, $type, $heritage) = kconf_recevoir_valeur($I, $id_I, $clef, $O);
	
	// fabrique la widget
	if ($widget_type=='logo') {
		list($widget,$defaut) = $f($clef, $valeurs, $valeur, array($contexte['page']['fichier'], $I, $id_I, $O));
		$kconf['fichiers'][$contexte['page']['fichier']]['logo'] = true; // invalideur logo
	} else
		list($widget,$defaut) = $f($clef,$valeurs,$valeur);
		
	// enregistre les valeurs par defaut
// 	spip_log("valeur de $clef: ".var_export($valeur,true));
	if ($valeur == null) {
		$kconf['i'][$Ip]['c'][$id_Ip]['clefs'][$clef]['valeur'] = $defaut;
		$kconf['i'][$Ip]['c'][$id_Ip]['clefs'][$clef]['type'] = $contexte['page']['type'];
		$kconf['i'][$Ip]['c'][$id_Ip]['clefs'][$clef]['defaut'] = $defaut;
// 		spip_log("met valeur par defaut $defaut pour $clef: interface $Ip id $id_Ip");
	}
	
	// bouton pour remettre la valeur par defaut
	// si la valeur n'est pas hérité ou si elle n'est pas comme défaut
// 	spip_log($kconf['i'][$Ip]['c'][$Id_op]['clefs'][$clef]['defaut']."==$valeur && $I==$Ip && $id_I==$id_Ip");
	if ($widget_type!=='logo' && $valeur!==null && !is_int($heritage) && !($kconf['i'][$Ip]['c'][$id_Ip]['clefs'][$clef]['defaut']==$valeur && $I==$Ip && $id_I==$id_Ip) ) {
		if ($O=='o') {
			$exec = $kconf['i'][$I]['exec'];
			$nom_objet = $kconf['i'][$I]['objet'];
		} else {
			$exec = $kconf['i'][$I]['exec_c'];
			$nom_objet = $kconf['i'][$I]['conteneur'];
		}
		$widget .= ajax_action_auteur('kconf_admin',
			"$id_I,$I,$O,$clef", // id_objet, objet, clef
			($O=='o'?$kconf['i'][$I]['exec']:$kconf['i'][$I]['exec_c']), // naviguer
			"id_$nom_objet=${id_I}&kconf=".$contexte['page']['fichier']."&kobjet=$I", // id_rubrique = 12
			array("<img src='"._DIR_PLUGIN_KCONF."images/edit-undo.png' height='16' width='16' alt='revenir'/>",''),'','');
	}
	
	return "$widget";//,$clef,$valeurs,$cont,$valeur, $type, $heritage";
}

function kconf_defaut($nom=false,$valeurs=false,&$K,$id_rubrique) {
	if (!isset($K[$nom]))
		$K[$nom] = '';
	return $nom;
}

function kconf_select($clef,$valeurs,$konf) {
	if ($valeurs) {
		$val = explode(',',$valeurs);
		$defaut = $val[0];
		if ($konf==null) $konf = $defaut;
		$ret = "<select name='$clef'>";
		foreach ($val as $v) {
			$selected = ($konf==$v) ? "selected='selected' " : "";
			$ret .= "<option value='$v' $selected>"._T("kconf:$v")."</option>";
		}
		$ret .= "</select>";
	}
	return array($ret,$defaut);
}

function kconf_radio($clef,$valeurs,$konf) {
	if ($valeurs) {
		$val = explode(',',$valeurs);
		$defaut = $val[0];
		if ($konf==null) $konf = $defaut;
		foreach ($val as $v) {
			$select = ($konf==$v) ? 'checked="checked" ' : '';
			$ret .= "<input style='vertical-align:bottom;' type='radio' name='$clef' value='$v' $select/ > : "._T("kconf:$v");
		}
	}
	return array($ret,$defaut);
}

function kconf_texte($clef,$valeurs,$konf) {
	$defaut = $valeurs;
	if ($konf==null) $konf = $defaut;
	$ret = "<input type='text' class='forml' name='$clef' value=\"".entites_html($konf)."\" />\n";
	return array($ret,$defaut);
}

function kconf_couleur($clef,$valeurs,$konf) {
	$defaut = ($valeurs) ? $valeurs : '#FFEEDD';
	if ($konf==null) $konf = $defaut;
	$ret = "<input type='text' name='$clef' id='$clef' value=\"".entites_html($konf)."\" />\n";
	$ret .= "&nbsp;<img id='btcol-$clef' src='"._DIR_IMG_PACK."noeud_plus.gif' alt='' />\n";
	$ret .= "<div style='display:none;' id='picker-$clef'></div>\n";
	$ret .= '<script type="text/javascript">';
	$ret .= 'jQuery( function($) {
		$("#picker-'.$clef.'").farbtastic("#'.$clef.'");
		$("#btcol-'.$clef.'").css({cursor:"pointer"})
		.unbind("click")
		.bind("click",function(){
			$("#picker-'.$clef.'").slideToggle();})
		.toggle(
			function(){this.src="'._DIR_IMG_PACK.'noeud_moins.gif"},
			function() {this.src="'._DIR_IMG_PACK.'noeud_plus.gif"}
		);})';
	$ret .= "</script>\n";
	return array($ret,$defaut);
}

function kconf_checkbox($clef,$valeurs,$konf) {
	$defaut = ($valeurs) ? 'oui' : 'non';
	if ($konf==null) $konf = $defaut;
	$ret = "<input style='vertical-align:bottom;' type='checkbox' name='$clef' id='$clef' value='oui' ".($konf=='oui'?'checked="checked"':'')." />\n";	
	$ret .= "<input type='hidden' name='chk$clef' value='oui' />\n";
	return array($ret,$defaut);
}

function kconf_logo($clef,$valeurs,$konf,$contexte) {
	global $kconf;
	$defaut = ($valeurs) ? $valeurs : '';
	if ($konf==null) $konf = $defaut;
	list($page,$I,$id_I,$O) = $contexte;
	$exec = $kconf['i'][$I]['exec'];
	// l'image existe
	if ($konf) {
// 		$ret = "<img src='"._DIR_LOGOS."$konf' alt='$clef' />\n";
		include_spip('inc/filtres_images_mini');
		$url_logo = _DIR_LOGOS.$konf;
		$ret = image_reduire("<img src='$url_logo' alt='$clef' />", 400, 200);
		$ret = "<a href='$url_logo'>$ret</a>";
		if ($taille = @getimagesize($url_logo))
			$taille = _T('info_largeur_vignette', array('largeur_vignette' => $taille[0], 'hauteur_vignette' => $taille[1]));
		$ret .= "<div class='spip_xx-small'>" . $taille . "\n [";
		// lien d'effacement
		$ret .= ajax_action_auteur("kconf_admin", "${id_I},$I,$O,-logo-$clef",
			$exec, "kconf=$page&id_${I}=${id_I}",
			array(_T('lien_supprimer')),
			'',"function(r,status) {this.innerHTML = r; \$('form.form_upload_icon',this).async_upload(async_upload_icon);}");
		$ret .= "]</div>";
	} else  {
	// formulaire d'upload
		$logo = "\n<input name='image' type='file' style='font-size:9px;' size='15' />".
			" <input name='sousaction1' type='submit' value='" .
			_T('bouton_telecharger') .
			"' class='fondo' style='font-size:9px' />"."\n";
		
		$script = generer_url_ecrire("$exec", "kconf=$page&id_${I}=${id_I}",true);
		$iframe_script = generer_url_ecrire('kconf_admin',"kconf=$page&id_${I}=${id_I}&script=$exec",true);
		$iframe = "<input type='hidden' name='iframe_redirect' value='".rawurlencode($iframe_script)."' />\n";

		$logo = redirige_action_post('kconf_admin',
		"${id_I},$I,$O,+logo-$clef",
		$exec,
		"kconf=$page&id_${I}=${id_I}",
		$iframe.$logo,
		" enctype='multipart/form-data' class='form_upload_icon'");
		
		$ret = "</p>".$logo."<p>";
	}

	return array($ret,$defaut);
}

?>