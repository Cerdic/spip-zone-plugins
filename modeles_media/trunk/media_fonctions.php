<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Calcule la légende d'un document
// Balise placée dans une boucle DOCUMENTS et appelée dans un modèle <media>
// Les paramètres legende, titre, descriptif, credits, poids et type sont récupérés dans l'environnement
// Syntaxe (premier argument précise si en div ou en dl, le second la largeur de la légende) :
// #MEDIA_LEGENDE{'dl',#GET{width}} ou #MEDIA_LEGENDE{'div',#GET{width}} 
function balise_MEDIA_LEGENDE_dist($p) {
	$conteneur = interprete_argument_balise(1,$p);
	$width = interprete_argument_balise(2,$p);
	$sql_id_document = champ_sql('id_document', $p);
	$sql_titre = champ_sql('titre', $p);
	$sql_descriptif = champ_sql('descriptif', $p);
	$sql_credits = champ_sql('credits', $p);
	$sql_type = champ_sql('type_document', $p);
	$sql_poids = champ_sql('taille', $p);
	$connect = '';
	if (isset($p->boucles[$p->id_boucle]))
		$connect = $p->boucles[$p->id_boucle]->sql_serveur;
	$connect = _q($connect);
	$p->code = "calculer_balise_MEDIA_LEGENDE($conteneur,$width,$sql_id_document,$sql_titre,$sql_descriptif,$sql_credits,$sql_type,$sql_poids,\$Pile[0],$connect)";
	return $p;
}

function calculer_balise_MEDIA_LEGENDE($conteneur,$width,$sql_id_document,$sql_titre,$sql_descriptif,$sql_credits,$sql_type,$sql_poids,$args,$connect=''){
	$ret = '';
	$env_legende = isset($args['legende']) ? $args['legende'] : '0';
	$env_titre = isset($args['titre']) ? $args['titre'] : '0';
	$env_descriptif = isset($args['descriptif']) ? $args['descriptif'] : '0';
	$env_credits = isset($args['credits']) ? $args['credits'] : '0';
	$env_type = isset($args['args']['type']) ? $args['args']['type'] : '0'; // On regarde dans 'args' pour eviter interference avec variable type herite du contexte de l'article
	$env_poids = isset($args['poids']) ? $args['poids'] : '0';
	
	// Doit-on afficher une légende ?
	if ($env_legende || $env_titre || $env_descriptif || $env_credits || $env_poids || $env_type) {
		$media_largeur_max_legende = isset($GLOBALS['meta']['media_largeur_max_legende']) ? $GLOBALS['meta']['media_largeur_max_legende'] : 120;
		$media_largeur_min_legende = isset($GLOBALS['meta']['media_largeur_min_legende']) ? $GLOBALS['meta']['media_largeur_min_legende'] : 350;
		$width = is_numeric($width) ? min($media_largeur_max_legende,max($media_largeur_min_legende,intval($width))) : $media_largeur_max_legende;
		// Y a-t-il un modèle légende à utiliser ?
		if ($env_legende && find_in_path('modeles/legende_'.$env_legende.'.html')) {
			$ret = recuperer_fond('modeles/legende_'.$env_legende,array(
				'id' => $sql_id_document,
				'titre' => $env_titre,
				'descriptif' => $env_descriptif,
				'credits' => $env_credits,
				'type' => $env_type,
				'poids' => $env_poids,
				'width' => $width,
				'conteneur' => $conteneur
			),array(),$connect);
		} else {
			$width = is_numeric($width) ? 'max-width: '.intval($width).'px;' : '';
			$dt = $conteneur=='dl' ? 'dt' : 'div';
			$dd = $conteneur=='dl' ? 'dd' : 'div';
			$distant = ($connect) ? $connect.'__' : '';
			// Titre
			if ($env_titre && $env_titre!='titre') {
				$titre = typo($env_titre);
				$crayons_titre = '';
			} elseif ($env_titre=='titre' || $env_legende) {
				$titre = typo(supprimer_numero($sql_titre));
				$crayons_titre = defined('_DIR_PLUGIN_CRAYONS') ? ' crayon '.$distant.'document-titre-'.$sql_id_document : '';
			} else {
				$titre = '';
				$crayons_titre = '';
			}
			// Descriptif
			if ($env_descriptif && $env_descriptif!='descriptif') {
				$descriptif = propre($env_descriptif);
				$crayons_descriptif = '';
			} elseif ($env_descriptif=='descriptif' || $env_legende) {
				$descriptif = propre($sql_descriptif);
				$crayons_descriptif = defined('_DIR_PLUGIN_CRAYONS') ? ' crayon '.$distant.'document-descriptif-'.$sql_id_document : '';
			} else {
				$descriptif = '';
				$crayons_descriptif = '';
			}
			// Notes
			$notes = calculer_notes();
			if ($notes)
				$notes = '<p class="notes">'.PtoBR($notes).'</p>';
			// Crédits
			if ($env_credits && $env_credits!='credits') {
				$credits = typo($env_credits);
				$crayons_credits = '';
			} elseif ($env_credits=='credits' || $env_legende=='complete') {
				$credits = typo($sql_credits);
				$crayons_credits = defined('_DIR_PLUGIN_CRAYONS') && defined('_DIR_PLUGIN_MEDIAS') ? ' crayon '.$distant.'document-credits-'.$sql_id_document : '';
			} else {
				$credits = '';
				$crayons_credits = '';
			}
			// Type de document
			if ($env_type && $env_type!='type') 
				$type = typo($env_type);
			elseif ($env_type=='type' || $env_legende=='complete')
				$type = typo($sql_type);
			else
				$type = '';
			// Poids du document
			if ($env_poids || $env_legende=='complete')
				$poids = taille_en_octets($sql_poids);
			else
				$poids = '';
			if ($type && $poids)
				$poids = ' - '.$poids;
			
			if ($titre)
				$ret .= "<$dt class='spip_doc_titre$crayons_titre' style='$width'><strong>$titre</strong></$dt>";
			if ($descriptif)
				$ret .= "<$dd class='spip_doc_descriptif$crayons_descriptif' style='$width'>".PtoBR($descriptif).$notes."</$dd>";
			if ($credits)
				$ret .= "<$dd class='spip_doc_credits$crayons_credits' style='$width'>"._T('media:credits')." <span class='credit'>$credits</span></$dd>";
			if ($type || $poids)
				$ret .= "<$dd class='spip_doc_infos' style='$width'>$type$poids</$dd>";
		}
	}
	return $ret;
}

// Renvoie un espace si on doit afficher une légende
// Rien sinon
// Balise placée dans une boucle DOCUMENTS et appelée dans un modèle <media>
function balise_MEDIA_AFFICHER_LEGENDE_dist($p) {
	$conteneur = interprete_argument_balise(1,$p);
	$p->code = "!empty(\$Pile[0]['legende']) || !empty(\$Pile[0]['titre']) || !empty(\$Pile[0]['descriptif']) || !empty(\$Pile[0]['credits']) || !empty(\$Pile[0]['args']['type']) || !empty(\$Pile[0]['poids']) ? ' ' : ''";
	return $p;
}

// Renvoie le fichier d'une image retaillée selon les paramètres passés au modèle
// Balise placée dans une boucle DOCUMENTS et appelée dans un modèle <media>
// Les paramètres taille, hauteur, largeur, alt et titre sont récupérés dans l'environnement.
// Exemple de syntaxe : 
// #MEDIA_IMAGE_RETAILLEE{#LOGO_DOCUMENT} ou #MEDIA_IMAGE_RETAILLEE{#URL_DOCUMENT}
function balise_MEDIA_IMAGE_RETAILLEE_dist($p) {
	$image = interprete_argument_balise(1,$p);
	$sql_titre = champ_sql('titre', $p);
	$sql_type = champ_sql('type_document', $p);
	$sql_poids = champ_sql('taille', $p);
	$p->code = "calculer_balise_MEDIA_IMAGE_RETAILLEE($image,\$Pile[0]['args'],$sql_titre,$sql_type,$sql_poids)";
	return $p;
}

function calculer_balise_MEDIA_IMAGE_RETAILLEE($image,$args,$sql_titre,$sql_type,$sql_poids){
	$taille = isset($args['taille']) ? $args['taille'] : '0';
	$hauteur = isset($args['hauteur']) ? $args['hauteur'] : '0';
	$largeur = isset($args['largeur']) ? $args['largeur'] : '0';
	$alt = isset($args['alt']) ? $args['alt'] : '0';
	$titre = isset($args['titre']) ? $args['titre'] : '0';

	$src = extraire_attribut($image, 'src');
	$url_site_spip=$GLOBALS['meta']['adresse_site'];

	if (!$src)
		$src = $image;

	// Supprimer proprement le query string
	$url = parse_url($src);
	if (!empty($url['query'])) {
		$src = $url['scheme'].'://'.$url['host'].$url['path'];
	}

	if(substr($src,0,strlen($url_site_spip))==$url_site_spip) {
		$src = substr($src,strlen($url_site_spip));
		$src = str_replace('/IMG/', _DIR_IMG, $src);
	}

	$src_relative = $src;
    
	if(!preg_match('`^https?://`i',$src,$matches)){
		$src = realpath($src);
	}
	
	spip_log("src=$src","modeles_media");

	$src_imgsize = str_replace('https://', 'http://', $src); // No https for getimagesize
	list($width, $height) = @getimagesize($src_imgsize);
	
	// hauteur ou largeur en relatif
	if (substr(trim($hauteur),-1)=='%' || substr(trim($largeur),-1)=='%') {
		if (substr(trim($hauteur),-1)=='%')
			$hauteur = trim($hauteur);
		else
			$hauteur = "auto";
		
		if (substr(trim($largeur),-1)=='%')
			$largeur = trim($largeur);
		else
			$largeur = "auto";
		
		$img = "<img src=\"$src_relative\" height=\"$hauteur\" width=\"$largeur\" />";
	} else {
		// hauteur du redimensionnement
		if (is_numeric($hauteur) && intval($hauteur)>0)
			$hauteur = intval($hauteur);
		elseif (in_array($taille,array('icone','petit','moyen','grand')))
			$hauteur = $GLOBALS['meta']['media_taille_'.$taille.'_hauteur'];
		elseif (is_numeric($taille) && intval($taille)>0)
			$hauteur = intval($taille);
		elseif ($GLOBALS['meta']['media_taille_defaut_hauteur'] && is_null($args['largeur']))
			$hauteur = $GLOBALS['meta']['media_taille_defaut_hauteur'];
		else
			$hauteur = 100000;
		// largeur du redimensionnement
		if (is_numeric($largeur) && intval($largeur)>0)
			$largeur = intval($largeur);
		elseif (in_array($taille,array('icone','petit','moyen','grand')))
			$largeur = $GLOBALS['meta']['media_taille_'.$taille.'_largeur'];
		elseif (is_numeric($taille) && intval($taille)>0)
			$largeur = intval($taille); 
		elseif ($GLOBALS['meta']['media_taille_defaut_largeur'] && is_null($args['hauteur']))
			$largeur = $GLOBALS['meta']['media_taille_defaut_largeur'];
		else
			$largeur = 100000;
		// Doit-on redimensionner ?
		if ($height > $hauteur || $width > $largeur) {
			include_spip('inc/filtres_images_mini');
			$img = image_reduire($src,$largeur,$hauteur);
			}
		else
			$img = "<img src=\"$src_relative\" height=\"$height\" width=\"$width\" />";
	}
	// Ajouter une alternative
	// Variable alt si transmise, sinon le titre du document, sinon type et poids
	if ($alt)
		$alternative = typo($alt);
	elseif ($titre && $titre!='titre')
		$alternative = typo($titre);
	elseif ($sql_titre)
		$alternative = typo($sql_titre);
	else
		$alternative = typo($sql_type).' - '.taille_en_octets($sql_poids);
	$img = inserer_attribut($img,'alt',$alternative);
	return $img;
}

// Calcule le lien, si lien demandé, sur le document
// Balise placée dans une boucle DOCUMENTS et appelée dans un modèle <media>
// Les paramètres lien, titre_lien et sont récupérés dans l'environnement.
// Exemple de syntaxe : 
// #MEDIA_LIEN{#LOGO_DOCUMENT} ou #MEDIA_LIEN{#MEDIA_IMAGE_RETAILLEE{#LOGO_DOCUMENT}}
function balise_MEDIA_LIEN_dist($p) {
	$objet = interprete_argument_balise(1,$p);
	$forcer_lien = interprete_argument_balise(2,$p);
	$forcer_lien = is_null($forcer_lien) ? "''" : $forcer_lien;
	$id_document = champ_sql('id_document', $p);
	include_spip('balise/url_');
	$url_document = generer_generer_url_arg('document', $p, $id_document);
	$connect = '';
	if (isset($p->boucles[$p->id_boucle]))
		$connect = $p->boucles[$p->id_boucle]->sql_serveur;
	$connect = _q($connect);
	$p->code = "calculer_balise_MEDIA_LIEN($objet,$forcer_lien,$id_document,$url_document,\$Pile[0]['args'],isset(\$Pile[0]['lien']) ? \$Pile[0]['lien'] : '',\$Pile[0]['lang'],$connect)";
	return $p;
}

function calculer_balise_MEDIA_LIEN($objet,$forcer_lien,$id_document,$url_document,$args,$lien,$lang,$connect='') {
	$titre_lien = isset($args['titre_lien']) ? $args['titre_lien'] : '0';
	$titre = isset($args['titre']) ? $args['titre'] : '0';

	// A-t-on demandé un lien
	if (!$lien && !$forcer_lien)
		return $objet;
	// Si lien non spécifique, on pointe sur le document (en se basant sur $url_document pour ne pas pointer sur spip.php?page=document)
	if ($lien == 'lien' || !$lien) {
		$lien = 'doc'.$id_document;
		// Si on pointe sur le document, que titre_lien n'est pas spécifié mais qu'on a spécifié un titre au document, on prend le titre spécifique
		if (!$titre_lien && $titre && $titre != 'titre')
			$titre_lien = $titre;
		$l = calculer_url($lien, $titre_lien, 'tout', $connect);
		$l['url'] = $url_document;
	} else {
		$l = calculer_url($lien, $titre_lien, 'tout', $connect);
	}

	if (!$l['url']) {
		return $objet;
	}
	$a = '<a href="'.$l['url'].'"';
	$a .= $l['class'] ? ' class="'.$l['class'].'"' : '';
	$a .= $l['titre'] ? ' title="'.attribut_html(typo($l['titre'])).'"' : '';
	$a .= ($l['lang'] && $l['lang']!=$lang) ? ' hreflang="'.$l['lang'].'"' : ''; // Seulement si hreflang diffère de la langue en cours
	if(empty($l['mime'])) $a .= ' type="'.$l['mime'].'"';
	$a .= '>';
	return $a.$objet.'</a>';
}

// Renvoie la taille du fichier après redimensionnement si besoin
// Balise placée dans une boucle DOCUMENTS et appelée dans un modèle <media>
// Les paramètres taille, hauteur et largeur sont récupérés dans l'environnement.
// Exemple de syntaxe : 
// #MEDIA_TAILLE{largeur} ou #MEDIA_TAILLE{hauteur}
function balise_MEDIA_TAILLE_dist($p) {
	$dim = interprete_argument_balise(1,$p);
	$sql_largeur = champ_sql('largeur', $p);
	$sql_hauteur = champ_sql('hauteur', $p);
	$p->code = "calculer_balise_MEDIA_TAILLE($dim,\$Pile[0]['args'],$sql_largeur,$sql_hauteur)";
	return $p;
}

function calculer_balise_MEDIA_TAILLE($dim,$args,$sql_largeur,$sql_hauteur){
	$taille = isset($args['taille']) ? $args['taille'] : '0';
	$hauteur = isset($args['hauteur']) ? $args['hauteur'] : '0';
	$largeur = isset($args['largeur']) ? $args['largeur'] : '0';
	
	$hauteur_defaut = array(
		'icone' => 52,
		'petit' => 90,
		'moyen' => 240,
		'grand' => 480
	);
	$largeur_defaut = array(
		'icone' => 52,
		'petit' => 120,
		'moyen' => 320,
		'grand' => 640
	);
	// Une taille par défaut si le document n'en n'a pas
	if (!is_numeric($sql_hauteur) || intval($sql_hauteur)<=0) {
		if ($GLOBALS['meta']['media_taille_defaut_hauteur'])
			$sql_hauteur = $GLOBALS['meta']['media_taille_defaut_hauteur'];
		elseif ($GLOBALS['meta']['media_taille_grand_hauteur'])
			$sql_hauteur = $GLOBALS['meta']['media_taille_defaut_hauteur'];
		else
			$sql_hauteur = 480;
	}
	if (!is_numeric($sql_largeur) || intval($sql_largeur)<=0) {
		if ($GLOBALS['meta']['media_taille_defaut_largeur'])
			$sql_largeur = $GLOBALS['meta']['media_taille_defaut_largeur'];
		elseif ($GLOBALS['meta']['media_taille_grand_largeur'])
			$sql_largeur = $GLOBALS['meta']['media_taille_defaut_largeur'];
		else
			$sql_largeur = 640;
	}
	
	// Hauteur visée (on peut avoir passé une hauteur en %)
	if (substr(trim($hauteur),-1)=='%')
		$hauteur = trim($hauteur);
	elseif (is_numeric($hauteur) && intval($hauteur)>0)
		$hauteur = intval($hauteur);
	elseif (in_array($taille,array('icone','petit','moyen','grand')) && isset($GLOBALS['meta']['media_taille_'.$taille.'_hauteur']))
		$hauteur = $GLOBALS['meta']['media_taille_'.$taille.'_hauteur'];
	elseif (in_array($taille,array('icone','petit','moyen','grand')))
		$hauteur = $hauteur_defaut[$taille];
	elseif (substr(trim($taille),-1)=='%')
		$hauteur = trim($taille);
	elseif (is_numeric($taille) && intval($taille)>0)
		$hauteur = intval($taille);
	else
		$hauteur = $sql_hauteur;
	// Largeur visée (on peut avoir passé une largeur en %)
	if (substr(trim($largeur),-1)=='%')
		$largeur = trim($largeur);
	elseif (is_numeric($largeur) && intval($largeur)>0)
		$largeur = intval($largeur);
	elseif (in_array($taille,array('icone','petit','moyen','grand')) && isset($GLOBALS['meta']['media_taille_'.$taille.'_largeur']))
		$largeur = $GLOBALS['meta']['media_taille_'.$taille.'_largeur'];
	elseif (in_array($taille,array('icone','petit','moyen','grand')))
		$largeur = $largeur_defaut[$taille];
	elseif (substr(trim($taille),-1)=='%')
		$hauteur = trim($taille);
	elseif (is_numeric($taille) && intval($taille)>0)
		$largeur = intval($taille);
	else
		$largeur = $sql_largeur;
	// Doit-on redimensionner ? Si une deux dimensions est exprimée en %, on ne redimensionne pas.
	if (substr($hauteur,-1)=='%' || substr($largeur,-1)=='%')
		$t = array('largeur' => $largeur, 'hauteur' => $hauteur);
	elseif ($sql_hauteur > $hauteur || $sql_largeur > $largeur) {
		$ratio = max ($sql_hauteur/$hauteur,$sql_largeur/$largeur);
		$t = array('largeur' => round($sql_largeur/$ratio), 'hauteur' => round($sql_hauteur/$ratio));
	} else 
		$t = array('largeur' => $sql_largeur, 'hauteur' => $sql_hauteur);
	return $dim ? $t[$dim] : $t;
}

// Renvoie un tableau des paramètres à ignorer à utiliser avec env_to_params et env_to_attribute
// Exemple : [(#ENV*|env_to_attributs{#MEDIA_IGNORE_PARAMS})] ou [(#ENV*|env_to_params{#MEDIA_IGNORE_PARAMS})]
function balise_MEDIA_IGNORE_PARAMS_dist($p) {
	$p->code = "array('id_media', 'legende','titre','descriptif','credits','poids','type','taille','hauteur','largeur','class','lien','lien_class')";
	return $p;
}

// Filtre media_generer_vignette pour générer une vignette automatique à partir du fichier
// Recherche l'existence d'un filtre media_generer_vignette_ext et renvoie le résultat de ce filtre, sinon rien
// media_generer_vignette_ext doit renvoyer l'url de la vignette
function filtre_media_generer_vignette_dist($fichier,$ext) {
	$f = charger_fonction('media_generer_vignette_'.$ext,'filtre',true);
	if ($f)
		return $f($fichier);
	else
		return '';
}

// Pour les images jpg, png et gif, on renvoie simplement $fichier
// Le redimensionnement est assuré par le paramètre taille transmis aux modèles <media>
function filtre_media_generer_vignette_jpg_dist($fichier) {return $fichier;}
function filtre_media_generer_vignette_png_dist($fichier) {return $fichier;}
function filtre_media_generer_vignette_gif_dist($fichier) {return $fichier;}

// Extrait le groupe du mime_type
// utilisation [(#MIME_TYPE|groupe_mime)]
function filtre_groupe_mime_dist($m) {
	return substr($m,0,strpos($m,'/'));
}

?>
