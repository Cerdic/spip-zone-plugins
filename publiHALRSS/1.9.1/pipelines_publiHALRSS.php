<?php
/**
 * pipeline : pre_syndication
 * 
 * Effectue des modification du flux $rss
 * avant traitement de la syndication
 */
function publiHAL_pre_syndication($rss) {
	include_spip('inc/publiHAL_gestion');
	if(!publiHAL_test_installation()) return '';
	//spip_log("debut publiHAL_pre_syndication !!!!!");
	// A chercher les articles déjà présents avec FAIRE publiHAL_get_URLs puis les supprimer de $rss
	
	// copie des lignes 128 à 142 de syndic.php
	// Echapper les CDATA
	$echappe_cdata = array();
	if (preg_match_all(',<!\[CDATA\[(.*)]]>,Uims', $rss,
	$regs, PREG_SET_ORDER)) {
		foreach ($regs as $n => $reg) {
			$echappe_cdata[$n] = $reg[1];
			$rss = str_replace($reg[0], "@@@SPIP_CDATA$n@@@", $rss);
		}
	}

	// supprimer les commentaires
	$rss = preg_replace(',<!--\s+.*\s-->,Ums', '', $rss);

	// supprimer pulisher sans intéret :<dc:publisher>XXX - CCSd (ccsd.cnrs.fr)</dc:publisher>
//	$rss = str_replace('<dc:publisher>HAL - CCSd (ccsd.cnrs.fr)</dc:publisher>', '', $rss);
//	$rss = str_replace('<dc:publisher>INRIA - CCSd (ccsd.cnrs.fr)</dc:publisher>', '', $rss);
//	$rss = str_replace('<dc:publisher>TEL - CCSd (ccsd.cnrs.fr)</dc:publisher>', '', $rss);
	$rss = preg_replace('@<dc:publisher>((?U)[^<]+) - CCSd \(ccsd\.cnrs\.fr\)</dc:publisher>@is', '', $rss);
	$rss = str_replace('<dc:type>', '<dc:typedoc>', $rss);
	$rss = str_replace('</dc:type>', '</dc:typedoc>', $rss);
	
	// simplifier le backend, en supprimant les espaces de nommage type "dc:"
	$rss = preg_replace(',<(/?)(dc):,i', '<\1', $rss);
	
	// suppression des items déjà présents 
	global $publiHAL_itemBidon;
	$publiHAL_itemBidon="<item><link>http://bidon.complet.fr/</link><title>bidon</title></item>";
	$rss = preg_replace_callback(
			',<(item|entry)([:[:space:]][^>]*)?>(.*)</\1>,Uims',// copie de la ligne 165 de syndic.php
			"publiHAL_supprime_si_deja_la",
			$rss);//*/
		
	// Capture des noms et prénoms
	$rss = preg_replace('|\<creator\>\s*(.*)\s*,\s*(.*)\s*\</creator\>|Uims' ,
				 '<creator>$1@@@SepPreNom@@@$2</creator>', 
				 $rss);
	// remet les CDATA
//	cdata_echappe_retour($rss, $echappe_cdata);
	//spip_log("passage 1 !!!!!!");
	return $rss;
}

/***********************************
 * pipeline : post_syndication
 * 
 * modification d'un <item>...</item> dans la base à partir des valeurs suivantes
 * $le_lien (url de l'article),
 * $id_syndic (id de la syndication),
 * $data (tableau des données extraites sur l'article)
 */
function publiHAL_post_syndication() {
	include_spip('inc/mots');
	//spip_log("passage 2 !!!!!!");
	// voir inc/syndic.php
	list($le_lien, $id_syndic, $data) = func_get_arg(0);
	$req="SELECT id_syndic_article FROM spip_syndic_articles WHERE id_syndic=$id_syndic AND url=" . spip_abstract_quote($le_lien). " LIMIT 1";
	$res=spip_fetch_array(spip_query($req));
	if(!$res) return;
	$id_syndic_article=$res['id_syndic_article'];
	
	$miseAjour=array();
	if(ereg('@@@SepPreNom@@@',$data['lesauteurs'])){
		//$auteurs = explode(',',$data['lesauteurs']);
		$data['lesauteurs'] = str_replace(',',';',$data['lesauteurs']);
		$data['lesauteurs'] = preg_replace('|\s*@@@SepPreNom@@@\s*|Us', ',', $data['lesauteurs']);
		// MAJ !!
		$miseAjour[]="lesauteurs=" . spip_abstract_quote($data['lesauteurs']);
	}

	if(preg_match_all(',<(publisher|typedoc|coverage)>(.*)</\1>,Uims',$data['item'],$matches,PREG_SET_ORDER)){
		foreach ($matches as $match) {
			if(strcmp($match[1],'coverage')==0) $match[2]=str_replace(array(',',' / '),';',$match[2]);// MOT1/MOT2 -> MOT1;MOT2
			if($t = creer_tag($match[2], $match[1], "")) $data['tags'][]=$t;
		}
		# copie lignes 383 à 390 de syndic.php
		# eviter les doublons (cle = url+titre) et passer d'un tableau a une chaine
		if ($data['tags']) {
			$vus = array();
			foreach ($data['tags'] as $tag) {
				$cle = supprimer_tags($tag).extraire_attribut($tag,'href');
				$vus[$cle] = $tag;
			}
			$tags .= ($tags ? ', ' : '') . join(', ', $vus);
			// MAJ !!
			$miseAjour[]="tags=" . spip_abstract_quote($tags);
		}
	}
	// s'il y a des mises à jour à faire
	if($miseAjour){
		$req="UPDATE spip_syndic_articles SET " . implode(' , ',$miseAjour) ."	WHERE id_syndic_article=$id_syndic_article ";
		//spip_log("Requete les auteurs : ".$req);
		spip_query($req);	
	}
	// traitement des mots clefs ! **********
	// type de document
	if(isset($GLOBALS['meta']['publiHAL_Type_de_document'])){
		$id_groupe=$GLOBALS['meta']['publiHAL_Type_de_document'];
		if($data['tags']) {
			foreach ($data['tags'] as $tag) {
				if(strcmp("typedoc",extraire_attribut($tag,'rel'))==0){
					$val=supprimer_tags($tag);
					$req="SELECT id_mot FROM spip_mots WHERE id_groupe=$id_groupe AND descriptif=". spip_abstract_quote($val). " LIMIT 1";
					$res=spip_fetch_array(spip_query($req));
					if($res){
						$id_mot=$res['id_mot'];
						// on a le type de document on met le mot clef
						inserer_mot('spip_mots_syndic_articles', 'id_syndic_article', $id_syndic_article, $id_mot);
					}	
				}
			}
		}
	}
	//spip_log('le labo **');
	// le labo
	if(isset($GLOBALS['meta']['publiHAL_Labo_publi']) && isset($GLOBALS['meta']['publiHAL_Ce_Labo_publi'])){
		$id_groupe=$GLOBALS['meta']['publiHAL_Labo_publi'];
		$id_mot=$GLOBALS['meta']['publiHAL_Ce_Labo_publi'];
		// on a le type de document on met le mot clef
		inserer_mot('spip_mots_syndic_articles', 'id_syndic_article', $id_syndic_article, $id_mot);
	}	
	// les auteurs
	if(isset($GLOBALS['meta']['publiHAL_auteurs_publi'])){
		include_spip('inc/publiHAL_gestion');
		publiHAL_traite_mots_auteurs($id_syndic_article,$data['lesauteurs']);
	}
//fin
}

/**
 * Suppression de chaque <item>...</item> du backend déjà présent dans la syndication
 */
function publiHAL_supprime_si_deja_la($matches){
	// $matches[0] représente la valeur totale
	$item=$matches[0];
	/**
	 *  copie des lignes 178 à 208 de syndic.php avec remplacement de $data['url'] par $data_url
	 */
	// URL (semi-obligatoire, sert de cle)

	// guid n'est un URL que si marque de <guid ispermalink="true"> ;
	// attention la valeur par defaut est 'true' ce qui oblige a quelque
	// gymnastique
	if (preg_match(',<guid.*>[[:space:]]*(https?:[^<]*)</guid>,Uims',
	$item, $regs) AND preg_match(',^(true|1)?$,i',
	extraire_attribut($regs[0], 'ispermalink')))
		$data_url = $regs[1];

	// <link>, plus classique
	else if (preg_match(
	',<link[^>]*[[:space:]]rel=["\']?alternate[^>]*>(.*)</link>,Uims',
	$item, $regs))
		$data_url = $regs[1];
	else if (preg_match(',<link[^>]*[[:space:]]rel=.alternate[^>]*>,Uims',
	$item, $regs))
		$data_url = extraire_attribut($regs[0], 'href');
	else if (preg_match(',<link[^>]*>(.*)</link>,Uims', $item, $regs))
		$data_url = $regs[1];
	else if (preg_match(',<link[^>]*>,Uims', $item, $regs))
		$data_url = extraire_attribut($regs[0], 'href');

	// Aucun link ni guid, mais une enclosure
	else if (preg_match(',<enclosure[^>]*>,ims', $item, $regs)
	AND $url = extraire_attribut($regs[0], 'url'))
		$data_url = $url;

	// pas d'url, c'est genre un compteur...
	else
		$data_url = '';
	//--------------------------
	/*
	 * SELECT COUNT(*) 
	 * FROM spip_syndic s, spip_syndic_articles a 
	 * WHERE s.id_syndic=a.id_syndic // jointure
	 * AND s.syndication<>'non'
	 * AND s.statut='publie' AND a.url like '%' 
	 */
	$req =	"SELECT COUNT(*) AS n FROM spip_syndic AS s, spip_syndic_articles AS a WHERE s.id_syndic=a.id_syndic".
			" AND s.syndication<>'non' AND s.statut='publie' AND a.url=" . spip_abstract_quote($data_url);
	$cpt = spip_fetch_array(spip_query($req));
	if($cpt['n']) {
		global $publiHAL_itemBidon;
		$r=$publiHAL_itemBidon;
		return $r;
	}// rien de neuf supprimer l'item
	//spip_log("publiHAL_supprime_PAS : $req");
	return $matches[0];
}
/**
 * Styles pour l'affichage ; créer un fichier spip_publihalrss.css pour changer le style
 */
function publiHAL_insert_head($flux){
	$flux .=     "<link rel='stylesheet' href='".find_in_path('spip_publihalrss.css')."' type='text/css' media='all' />\n";
	return $flux;
}

?>