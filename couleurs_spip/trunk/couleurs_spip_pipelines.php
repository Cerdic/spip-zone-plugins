<?php
/*
 * Plugin Couleurs_SPIP
 * (c) 2009-2013
 * Distribue sous licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion de la CSS sur le site public
 * et dans l'espace prive
 *
 * @param string $flux
 * @return string
 */
function couleurs_spip_insert_head_css($flux) {
	$flux .= '<link href="'.find_in_path('css/couleurs_spip.css').'" rel="stylesheet" type="text/css" />'."\n";
	return $flux;
}


/**
 * Liste des couleurs supportees
 *
 * @return array
 */
function couleurs_spip_constantes() {
	return array(array(
		array('noir', 'rouge', 'marron', 'vert', 'vert olive', 'bleu marine', 'violet', 'gris', 'argent', 'vert clair', 'bleu', 'fuchia', 'bleu clair', 'blanc', 'bleu azur', 'beige', 'brun', 'bleu violet', 'brun clair', 'rose clair', 'vert fonce', 'orange fonce', 'mauve fonce', 'bleu ciel', 'or', 'ivoire', 'orange', 'lavande', 'rose', 'prune', 'saumon', 'neige', 'turquoise', 'jaune paille', 'jaune'),
		array('black', 'red', 'maroon', 'green', 'olive', 'navy', 'purple', 'gray', 'silver', 'chartreuse', 'blue', 'fuchsia', 'aqua', 'white', 'azure', 'bisque', 'brown', 'blueviolet', 'chocolate', 'cornsilk', 'darkgreen', 'darkorange', 'darkorchid', 'deepskyblue', 'gold', 'ivory', 'orange', 'lavender', 'pink', 'plum', 'salmon', 'snow', 'turquoise', 'wheat', 'yellow') ),
	array('aliceblue'=>'F0F8FF','antiquewhite'=>'FAEBD7','aqua'=>'00FFFF','aquamarine'=>'7FFFD4','azure'=>'F0FFFF','beige'=>'F5F5DC','bisque'=>'FFE4C4','black'=>'000000','blanchedalmond'=>'FFEBCD','blue'=>'0000FF','blueviolet'=>'8A2BE2','brown'=>'A52A2A','burlywood'=>'DEB887','cadetblue'=>'5F9EA0','chartreuse'=>'7FFF00','chocolate'=>'D2691E','coral'=>'FF7F50','cornflowerblue'=>'6495ED','cornsilk'=>'FFF8DC','crimson'=>'DC143C','cyan'=>'00FFFF','darkblue'=>'00008B','darkcyan'=>'008B8B','darkgoldenrod'=>'B8860B','darkgray'=>'A9A9A9','darkgreen'=>'006400','darkkhaki'=>'BDB76B','darkmagenta'=>'8B008B','darkolivegreen'=>'556B2F','darkorange'=>'FF8C00','darkorchid'=>'9932CC','darkred'=>'8B0000','darksalmon'=>'E9967A','darkseagreen'=>'8FBC8F','darkslateblue'=>'483D8B','darkturqoise'=>'00CED1','darkslategray'=>'2F4F4F','darkviolet'=>'9400D3','deeppink'=>'FF1493','deepskyblue'=>'00BFFF','dimgray'=>'696969','dodgerblue'=>'1E90FF','firebrick'=>'B22222','floralwhite'=>'FFFAF0','forestgreen'=>'228B22','fuchsia'=>'FF00FF','gainsboro'=>'DCDCDC','ghostwhite'=>'F8F8FF','gold'=>'FFD700','goldenrod'=>'DAA520','gray'=>'808080','green'=>'008000','greenyellow'=>'ADFF2F','honeydew'=>'F0FFF0','hotpink'=>'FF69B4','indianred'=>'CD5C5C','indigo'=>'4B0082','ivory'=>'FFFFF0','khaki'=>'F0E68C','lavender'=>'E6E6FA','lavenderblush'=>'FFF0F5','lawngreen'=>'7CFC00','lemonchiffon'=>'FFFACD','lightblue'=>'ADD8E6','lightcoral'=>'F08080','lightcyan'=>'E0FFFF','lightgoldenrodyellow'=>'FAFAD2','lightgreen'=>'90EE90','lightgrey'=>'D3D3D3','lightpink'=>'FFB6C1','lightsalmon'=>'FFA07A','lightseagreen'=>'20B2AA','lightskyblue'=>'87CEFA','lightslategray'=>'778899','lisghtsteelblue'=>'B0C4DE','lightyellow'=>'FFFFE0','lime'=>'00FF00','limegreen'=>'32CD32','linen'=>'FAF0E6','magenta'=>'FF00FF','maroon'=>'800000','mediumaquamarine'=>'66CDAA','mediumblue'=>'0000CD','mediumorchid'=>'BA55D3','mediumpurple'=>'9370DB','mediumseagreen'=>'3CB371','mediumslateblue'=>'7B68EE','mediumspringgreen'=>'00FA9A','mediumturquoise'=>'48D1CC','mediumvioletred'=>'C71585','midnightblue'=>'191970','mintcream'=>'F5FFFA','mistyrose'=>'FFE4E1','moccasin'=>'FFE4B5','navajowhite'=>'FFDEAD','navy'=>'000080','navyblue'=>'9FAFDF','oldlace'=>'FDF5E6','olive'=>'808000','olivedrab'=>'6B8E23','orange'=>'FFA500','orangered'=>'FF4500','orchid'=>'DA70D6','palegoldenrod'=>'EEE8AA','palegreen'=>'98FB98','paleturquoise'=>'AFEEEE','palevioletred'=>'DB7093','papayawhip'=>'FFEFD5','peachpuff'=>'FFDAB9','peru'=>'CD853F','pink'=>'FFC0CB','plum'=>'DDA0DD','powderblue'=>'B0E0E6','purple'=>'800080','red'=>'FF0000','rosybrown'=>'BC8F8F','royalblue'=>'4169E1','saddlebrown'=>'8B4513','salmon'=>'FA8072','sandybrown'=>'F4A460','seagreen'=>'2E8B57','seashell'=>'FFF5EE','sienna'=>'A0522D','silver'=>'C0C0C0','skyblue'=>'87CEEB','slateblue'=>'6A5ACD','snow'=>'FFFAFA','springgreen'=>'00FF7F','steelblue'=>'4682B4','tan'=>'D2B48C','teal'=>'008080','thistle'=>'D8BFD8','tomato'=>'FF6347','turquoise'=>'40E0D0','violet'=>'EE82EE','wheat'=>'F5DEB3','white'=>'FFFFFF','whitesmoke'=>'F5F5F5','yellow'=>'FFFF00','yellowgreen'=>'9ACD32') );
}

/**
 * Mise en forme du tableau ci-dessus
 * @return mixed
 */
function couleurs_spip_html(){
	list($couleurs, $html) = couleurs_spip_constantes();
	foreach ($couleurs[0] as $c=>$val){
		$val_en = $couleurs[1][$c];
		$couleurs[2][$val] = $couleurs[3][$val_en] = isset($html[$val_en])?'#'.$html[$val_en]:$val_en;
	}
	return $couleurs;
}

function couleurs_spip_couleur2classname($couleur){
	return trim(strtolower(str_replace(" ","",$couleur)));
}

/**
 * Liste des remplacements de couleur
 * @return array
 */
function couleurs_spip_liste_remplacements(){
	static $rempl = null;
	if (is_null($rempl)){
		$rempl = array();

		$couleurs = couleurs_spip_html();
		// d'abord generer les remplacements fr=>en
		foreach($couleurs[0] as $k=>$c_fr){
			$class_c_en = $couleurs[1][$k];
			$class_c_fr = couleurs_spip_couleur2classname($c_fr);
			$rempl["[$c_fr]"] = "[$class_c_en]";
			$rempl["[/$c_fr]"] = "[/$class_c_en]";
			$rempl["[fond $c_fr]"] = "[bg $class_c_en]";
			$rempl["[/fond $c_fr]"] = "[/bg $class_c_en]";
			$rempl["<cs_$class_c_fr>"] = "<cs_$class_c_en>";
			$rempl["</cs_$class_c_fr>"] = "</cs_$class_c_en>";
			$rempl["<cs_fond_$class_c_fr>"] = "<cs_bg_$class_c_en>";
			$rempl["</cs_fond_$class_c_fr>"] = "</cs_bg_$class_c_en>";
		}

		// ensuite les remplacement des raccourcis en
		$compat = array(
			'black'=>'cs_noir',
			'maroon'=>'cs_marron',
			'red'=>'cs_rouge',
			'orange'=>'cs_orange',
			'yellow'=>'cs_jaune',
			'green'=>'cs_vert',
			'blue'=>'cs_bleu',
			'purple'=>'cs_violet',
			'gray'=>'cs_gris',
			'white'=>'cs_blanc',
		);
		foreach($couleurs[3] as $class_c_en=>$v){
			$span_class = "cs_$class_c_en";
			$span_class_bg = "cs_bg_$class_c_en";
			if (isset($compat[$class_c_en]))
				$span_class .= " ".$compat[$class_c_en];
			$rempl["[$class_c_en]"] = "<span class=\"$span_class\">";
			$rempl["[/$class_c_en]"] = "</span>";
			$rempl["[bg $class_c_en]"] = "<span class=\"$span_class_bg\">";
			$rempl["[/bg $class_c_en]"] = "</span>";
			$rempl["<cs_$class_c_en>"] = "<span class=\"$span_class\">";
			$rempl["</cs_$class_c_en>"] = "</span>";
			$rempl["<cs_bg_$class_c_en>"] = "<span class=\"$span_class_bg\">";
			$rempl["</cs_bg_$class_c_en>"] = "</span>";
		}
		// et enfin les fermetures generiques
		$rempl["[/fond]"] = "</span>";
		$rempl["[/bg]"] = "</span>";
		$rempl["[/couleur]"] = "</span>";
		$rempl["[/color]"] = "</span>";

		// remplacement des raccourcis font-size
		$sizes = array('l','xl','xxl','xxxl','xxxxl','s','xs','xxs');
		foreach($sizes as $s){
			$rempl["<cs_$s>"] = "<span class=\"cs_$s\">";
			$rempl["</cs_$s>"] = "</span>";
		}
	}
	return $rempl;
}

/**
 * creation d'icone pour le plugin porte-plume
 *
 * @param string $texte
 * @param string $color
 * @return string
 */
function couleurs_spip_creer_icone_barre($texte, $color, $bg_color='transparent') {
	static $dir = null;
	if (is_null($dir)){
		$dir = sous_repertoire(_DIR_VAR, 'couleurs_spip');
		// ajouter au path SPIP
		_chemin($dir);
		$dir = sous_repertoire($dir, 'icones_barre');
	}
	$taille = 14;
	$dest = $dir . substr(md5("2-$texte-$color-$taille-$bg_color"),0,8).".png";
	if (file_exists($dest))
		return basename($dest);

	$img = image_typo($texte, 'couleur='.$color, "taille=$taille","hauteur_ligne=16", 'police=dustismo_bold.ttf');
	$img = filtrer('image_recadre',$img,16,16,'center bottom','transparent');
	if ($bg_color!=='transparent'){
		$img = filtrer('image_aplatir',$img,"png",$bg_color);
	}
	$src = extraire_attribut($img, 'src');
	@rename($src, $dest);
	return basename($dest);
}



/**
 * evite les transformations typo dans les balises $balises
 * par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
 *
 * @param $texte
 *   $texte a filtrer
 * @param $filtre
 *   le filtre a appliquer pour transformer $texte
 *   si $filtre = false, alors le texte est retourne protege, sans filtre
 * @param $balises
 *   balises concernees par l'echappement
 *   si $balises = '' alors la protection par defaut est sur les balises de _PROTEGE_BLOCS
 *   si $balises = false alors le texte est utilise tel quel
 * @param null|array $args
 *   arguments supplementaires a passer au filtre
 * @return string
 */
function couleurs_spip_filtre_texte_echappe($texte, $filtre, $balises='', $args=NULL){
	if(!strlen($texte)) return '';

	if ($filtre!==false){
		$fonction = chercher_filtre($filtre,false);
		if (!$fonction) {
			spip_log("orthotypo_filtre_texte_echappe() : $filtre() non definie",_LOG_ERREUR);
			return $texte;
		}
		$filtre = $fonction;
	}

	// protection du texte
	if($balises!==false) {
		if(!strlen($balises)) $balises = _PROTEGE_BLOCS;//'html|code|cadre|frame|script';
		else $balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
		if (!function_exists('echappe_html'))
			include_spip('inc/texte_mini');
		$texte = echappe_html($texte, 'FILTRETEXTECHAPPE', true, $balises);
	}
	// retour du texte simplement protege
	if ($filtre===false) return $texte;
	// transformation par $fonction
	if (!$args)
		$texte = $filtre($texte);
	else {
		array_unshift($args,$texte);
		$texte = call_user_func_array($filtre, $args);
	}

	// deprotection des balises
	return echappe_retour($texte, 'FILTRETEXTECHAPPE');
}


/**
 * Remplacer les raccourcis de couleurs dans un bloc de texte
 *
 * @param string $texte
 * @return string
 */
function couleurs_spip_remplacer($texte) {
	if (
		(strpos($texte, '[/')===false) OR strpos($texte, '[')===false
		AND
	  (strpos($texte, '</cs_')===false OR strpos($texte, '<cs_')===false)
		)
		return $texte;

	$rempl = couleurs_spip_liste_remplacements();
	$texte = str_replace(array_keys($rempl),array_values($rempl),$texte);

	// patch de conformite : les <span> doivent etre inclus dans les paragraphes
	while (preg_match(",(<span class=\"\w+\">)([^<]*)\n[\n]+,Sms", $texte, $regs))
		$texte = str_replace($regs[0], "$regs[1]$regs[3]</span>\n\n$regs[1]", $texte);
	return $texte;
}


/**
 * Lancer le remplacement de couleurs en dehors des balises, si besoin
 *
 * @param string $texte
 * @return string
 */
function couleurs_spip_pre_typo($texte) {
	if (
		(strpos($texte, '[/')===false) OR strpos($texte, '[')===false
		AND
	  (strpos($texte, '</cs_')===false OR strpos($texte, '<cs_')===false)
		)
		return $texte;

	// appeler couleurs_spip_remplacer() une fois que certaines balises ont ete protegees
	return couleurs_spip_filtre_texte_echappe($texte, 'couleurs_spip_remplacer');
}



/**
 * Inserer les icones dans le porte_plume
 * @param array $flux
 * @return array mixed
 */
function couleurs_spip_porte_plume_barre_pre_charger($flux) {
	$couleurs = couleurs_spip_html();
	$r1 = $r2 = array();
	foreach($couleurs[2] as $i=>$v) {
		$id = 'couleur_texte_'.couleurs_spip_couleur2classname($i);
		$r1[] = array(
				"id" => $id,
				"name" => _T('couleursspip:pp_couleur_texte', array('couleur'=>$i)),
				"className" => $id,
				"openWith" => "[$i]",
				"closeWith" => "[/$i]",
				"selectionType" => "word",
				"display" => true);
	}
	foreach($couleurs[2] as $i=>$v) {
		$id = 'couleur_fond_'.couleurs_spip_couleur2classname($i);
		$r2[] = array(
				"id" => $id,
				"name" => _T('couleursspip:pp_couleur_fond', array('couleur'=>$i)),
				"className" => $id,
				"openWith" => "[fond $i]",
				"closeWith" => "[/fond $i]",
				"selectionType" => "word",
				"display" => true);
	}

	$a = array(
		"id" => 'cs_couleur_texte',
		"name" => _T('couleursspip:colorer_texte'),
		"className" => 'cs_couleur_texte',
		"replaceWith" => '',
		"display" => true,
		"dropMenu"	=> $r1,
	);

	$barres = array('edition');
	foreach($barres as $b)
		$flux[$b]->ajouterApres('stroke_through', $a);
	if(!count($r2)) return $flux;

	$a = array(
		"id" => 'cs_couleur_fond',
		"name" => _T('couleursspip:colorer_fond'),
		"className" => 'cs_couleur_fond',
		"replaceWith" => '',
		"display" => true,
		"dropMenu"	=> $r2,
	);
	foreach($barres as $b)
		$flux[$b]->ajouterApres('cs_couleur_texte', $a);

	return $flux;
}

/**
 * Definir les icones correspondant a chaque bouton couleur du PP
 * @param array $flux
 * @return array
 */
function couleurs_spip_porte_plume_lien_classe_vers_icone($flux) {

	$couleurs = couleurs_spip_html();
	// icones utilisees. Attention : mettre les drop-boutons en premier !!
	$flux['cs_couleur_texte'] = array(couleurs_spip_creer_icone_barre(_T('couleursspip:pp_couleur_icone_texte'), '9932CC'),"center");
	$flux['cs_couleur_fond'] = array(couleurs_spip_creer_icone_barre(_T('couleursspip:pp_couleur_icone_fond'), '888888','9932CC'),"center");

	$textes = array(
		'texte' => _T('couleursspip:pp_couleur_icone_texte'),
		'fond' => _T('couleursspip:pp_couleur_icone_fond'),
	);
	foreach ($couleurs[2] as $i=>$c) {
		// icone de la couleur $i
		$color = str_replace('#','',$c);
		$i = couleurs_spip_couleur2classname($i);
		foreach(array('texte','fond') as $x) {
			$img = $x=='texte'?couleurs_spip_creer_icone_barre($textes[$x], $color):couleurs_spip_creer_icone_barre($textes[$x], '888888', $color);
			$flux['couleur_'.$x.'_'.$i] = array($img,"center");
		}
	}
	return $flux;
}

