<?php

/*
 * Recherche entendue
 * plug-in d'outils pour la recherche et l'indexation
 * Panneaux de controle admin_index et index_tous
 * Boucle INDEX
 * filtre google_like
 *
 *
 * Auteur :
 * cedric.morin@yterium.com
 * pdepaepe et Nicolas Steinmetz pour google_like
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */


// critere recherche utilisant l'indexation
// {recherche} ou {recherche susan}
// http://www.spip.net/@recherche
function critere_recherche($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	$t = $boucle->id_table;
	if (in_array($t,$table_des_tables))
		$t = "spip_$t";

	if (isset($crit->param[0]))
		$quoi = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	else
		$quoi = '@$Pile[0]["recherche"]';

	// Ne pas executer la requete en cas de hash vide
	$boucle->hash = '
	// RECHERCHE
	list($rech_select, $rech_where) = prepare_recherche('.$quoi.', "'.$boucle->primary.'", "'.$boucle->id_table.'", "'.$t.'", "'.$crit->cond.'");
	';

	// Sauf si le critere est conditionnel {recherche ?}
	if (!$crit->cond)
		$boucle->hash .= '
	if ($rech_where) ';

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$idb]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici
	$boucle->select[]= '$rech_select as points';

	// et la recherche trouve
	$boucle->where[]= '$rech_where';
}



	function RechercheEtendue_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" ) {

		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['accueil']->sousmenu["admin_index"]= new Bouton(
			"../"._DIR_PLUGIN_INDEXATION."/img_pack/stock_index.gif",  // icone
			_T("rechercheetendue:gestion_idexation") //titre
			);

			$boutons_admin['accueil']->sousmenu["index_tous"]= new Bouton(
			"../"._DIR_PLUGIN_INDEXATION."/img_pack/stock_book-alt.gif",  // icone
			_T("rechercheetendue:dictionnaire_indexe") //titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function RechercheEtendue_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}

	//*********************************************
	// RECHERCHE
	//*********************************************
	function RechercheEtendue_jauge_init($maxi,$largeur){
		global $gain_jauge;
		$gain_jauge = $largeur/$maxi;
	}
	function RechercheEtendue_jauge($texte,$nom_barre){
		global $gain_jauge;
		static $barfilename='';
		static $barre_path=array();
		static $barheight=array();
		$point = round($texte*$gain_jauge);
		if (!isset($barre_path[$nom_barre])){
			$p = $barre_path[$nom_barre] = find_in_path($nom_barre);	
			list($width, $barheight[$nom_barre], $type, $attr) = getimagesize("$p/bar_middle.gif");
		}
		$p = $barre_path[$nom_barre];
		$height = $barheight[$nom_barre];
		$texte = "";
		$texte = "<img src='$p/bar_left.gif' alt='' />";
		$texte .= "<img src='$p/bar_middle.gif' width='$point' height='$height' alt='"._T("rechercheetendue:score")." $point' />";
		$texte .= "<img src='$p/bar_right.gif' alt='' />";
		return $texte;
	}
	
	function RechercheEtendue_star($texte,$starfilename = "") {
		if ($starfilename=="")
			$starfilename=_DIR_PLUGIN_INDEXATION."/star.gif";
		$point = $texte;
		$texte = "";
		$star1="<img src='$starfilename' alt='"._T("rechercheetendue:etoile_1")."' />";
		$star2="<img src='$starfilename' alt='"._T("rechercheetendue:etoile_2")."' />&nbsp;<img src='$starfilename' alt=''/>";
		$star3="<img src='$starfilename' alt='"._T("rechercheetendue:etoile_3")."' />&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>";
		$star4="<img src='$starfilename' alt='"._T("rechercheetendue:etoile_4")."' />&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>";
		$star5="<img src='$starfilename' alt='"._T("rechercheetendue:etoile_5")."' />&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>";
		if ($point > 2) {
			$texte= $star1;
		}
		if ($point > 6) {
			$texte= $star2;
		}
		if ($point > 8) {
			$texte= $star3;
		}
		if ($point > 12) {
			$texte= $star4;
		}
		if ($point > 20) {
			$texte= $star5;
		}
	
		return $texte;
	}

	
	function RechercheEtendue_google_like_string($texte,$action='store'){
	  static $string;
	  switch ($action){
		  case 'get':
		  	return $string;
		  	break;
		  case 'raz':
		  	$string='';
		  	return "";
		  	break;
		  case 'store':
		  default:
		  	$string .= attribut_html($texte)." ";
		  	return "";
		}
		return "";
	}
	function RechercheEtendue_google_like_string_raz($texte){
	  return RechercheEtendue_google_like_string($texte,'raz');
	}
	
	function RechercheEtendue_google_like($query,$alternative = ""){
		include_spip('inc/surligne');
	  $string = RechercheEtendue_google_like_string('','get');
		$qt = preg_split(',\s+,ms', $query);
		foreach($qt as $key=>$mot){
			if (strlen($mot) >= 2) {
				$qt[$key] = surligner_regexp_accents(preg_quote(str_replace('/', '', $mot)));
			}
		}
		$num = count ($qt);
		$cc = ceil(200 / $num);
		$string_re = "";
		for ($i = 0; $i < $num; $i++) {
			$tab[$i] = preg_split("/($qt[$i])/i",$string,2, PREG_SPLIT_DELIM_CAPTURE);
			if(count($tab[$i])>1){
				$avant[$i] = reset($tab[$i]);
				if (strlen($avant[$i])>$cc){
					$avant[$i] = substr($avant[$i],-$cc,$cc);
					$pos = strpos($avant[$i], " ");
					$avant[$i]= " <em>[...]</em> " . substr($avant[$i],$pos);
				}
				$apres[$i] = end($tab[$i]);
				if (strlen($apres[$i])>$cc){
					$apres[$i] = substr($apres[$i],0,$cc);
					$pos = strrpos($apres[$i], " ");
					$apres[$i] = substr($apres[$i],0,$pos) . " <em>[...]</em> ";
				}
				$string_re .= $avant[$i].$tab[$i][1].$apres[$i];
			}
		}
		if (strlen($string_re)){
			$regexp = '/((^|>)([^<]*[^[:alnum:]_<\x80-\xFF])?)(('
	. join('|', $qt)
	. ')[[:alnum:]_\x80-\xFF]*?)/Uis';
			$string_re = preg_replace($regexp, '\1<span class="spip_surligne">\4</span>', $string_re);
			return charset2unicode($string_re);
		}
		else
			return $alternative;
	}

	//*********************************************
	// Recherche approchante pour spelling disabilities
	//*********************************************
	// fonction soundex pour la langue francaise
	// je sais plus ou je l'ai trouvee
	function RechercheEtendue_soundex_fr( $sIn )
	{
	   // Si il n'y a pas de mot, on sort immÈdiatement
	   if ( $sIn === '' ) return '    ';
	   // On met tout en majuscule
	   $sIn = strtoupper( $sIn );
	   // On supprime les accents
	   $sIn = strtr( $sIn, '¬ƒ¿«»… ÀåŒœ‘÷Ÿ€‹', 'AAASEEEEEIIOOUUU' );
	   // On supprime tout ce qui n'est pas une lettre
	   $sIn = preg_replace( '`[^A-Z]`', '', $sIn );
	   // Si la chaÓne ne fait qu'un seul caractËre, on sort avec.
	   if ( strlen( $sIn ) === 1 ) return $sIn . '   ';
	   // on remplace les consonnances primaires
	   $convIn = array( 'GUI', 'GUE', 'GA', 'GO', 'GU', 'CA', 'CO', 'CU',
	'Q', 'CC', 'CK' );
	   $convOut = array( 'KI', 'KE', 'KA', 'KO', 'K', 'KA', 'KO', 'KU', 'K',
	'K', 'K' );
	   $sIn = str_replace( $convIn, $convOut, $sIn );
	   // on remplace les voyelles sauf le Y et sauf la premiËre par A
	   $sIn = preg_replace( '`(?<!^)[EIOU]`', 'A', $sIn );
	   // on remplace les prÈfixes puis on conserve la premiËre lettre
	   // et on fait les remplacements complÈmentaires
	   $convIn = array( '`^KN`', '`^(PH|PF)`', '`^MAC`', '`^SCH`', '`^ASA`',
	'`(?<!^)KN`', '`(?<!^)(PH|PF)`', '`(?<!^)MAC`', '`(?<!^)SCH`',
	'`(?<!^)ASA`' );
	   $convOut = array( 'NN', 'FF', 'MCC', 'SSS', 'AZA', 'NN', 'FF', 'MCC',
	'SSS', 'AZA' );
	   $sIn = preg_replace( $convIn, $convOut, $sIn );
	   // suppression des H sauf CH ou SH
	   $sIn = preg_replace( '`(?<![CS])H`', '', $sIn );
	   // suppression des Y sauf prÈcÈdÈs d'un A
	   $sIn = preg_replace( '`(?<!A)Y`', '', $sIn );
	   // on supprime les terminaisons A, T, D, S
	   $sIn = preg_replace( '`[ATDS]$`', '', $sIn );
	   // suppression de tous les A sauf en tÍte
	   $sIn = preg_replace( '`(?!^)A`', '', $sIn );
	   // on supprime les lettres rÈpÈtitives
	   $sIn = preg_replace( '`(.)\1`', '$1', $sIn );
	   // on ne retient que 4 caractËres ou on complËte avec des blancs
	   return substr( $sIn . '    ', 0, 4);
	}
	
	function RechercheEtendue_recherche_semblable($recherche) {
		// recupere les mots de la recherche
		//$regs = separateurs_indexation(true)." ";
		//$recherche = strtr($recherche, $regs, ereg_replace('.', ' ', $regs));
		//$table_mots = preg_split("/ +/", $recherche);
		$table_mots = mots_indexation($recherche);
		$table_mots = array_unique($table_mots);
		$table_mots_semblables = array_map('RechercheEtendue_mot_semblable',$table_mots);
		if (count(array_diff($table_mots_semblables,$table_mots))
		 && (strpos($s=implode(" ",$table_mots_semblables),'_')===FALSE)) // pas de caractere joker
			return implode(" ",$table_mots_semblables);
		else 
			return ""; // si pas mieux a proposer, le filtre ne retourne rien
	}

	function RechercheEtendue_mot_match($mot1, $mot2, $beta = 10000){
		if (($d = abs(strlen($mot1)-strlen($mot2)))>$beta)
			return $d; // minorant de la distance
		else
			return levenshtein($mot1,$mot2);
	}
	function RechercheEtendue_mot_semblable($mot){
		static $mot_semblable_best = array();
		static $fcache = array();
		// Premier passage : chercher eventuel un cache des donnees sur le disque
		if (!$mot_semblable_best[$mot]) {
			$dircache = _DIR_CACHE.creer_repertoire(_DIR_CACHE,'simi');
			$fcache[$mot] =
				$dircache.'simi_'.substr(md5($mot),0,10).'.txt';
			if (lire_fichier($fcache[$mot], $contenu))
				$mot_semblable_best[$mot] = @unserialize($contenu);
		}

		global $auteur_session;
		$dump = (isset($_GET['dump'])&&$auteur_session['statut']=='0minirezo');
	
		// eviter de recalculer deux fois pour le meme mot
		// surtout si le filtre est appelle plusieurs fois pour la meme recherche
		if (!isset($mot_semblable_best[$mot])||$dump){
			$candidats = array();
			for ($k=0;$k<strlen($mot)-1;$k++){
				$permut = "_";
				// permutations de lettre : les meilleurs candidats
				$test = substr($mot,0,$k);
				$test .= $permut . $permut;
				$test .= substr($mot,$k+2,strlen($mot)-$k-2);
				$candidats[] = $test;
		
				// 1 lettre en trop
				$test = substr($mot,0,$k);
				$test .= substr($mot,$k+1,strlen($mot)-$k-1);
				$candidats[] = $test;
		
				// 1 lettre manquante
				$test = substr($mot,0,$k);
				$test .= $permut;
				$test .= substr($mot,$k,strlen($mot)-$k);
				$candidats[] = $test;
			}
			// debuts identiques
			for ($k=2;$k<strlen($mot)-1;$k++){
				$test = substr($mot,0,$k);
				if ($k<4) $test.= substr("___",0,4-$k);
				$candidats[] = $test;
			}
		
			if ($dump) var_dump($candidats);// pour le debugage
			$confirmes = array();
			foreach ($candidats as $test){
				if ($dump) echo "::$test";// pour le debugage
				$hash = requete_hash($test);
			  $hashres = $hash[0]; // on peut prendre le non strict
				if ($hashres){
					$query = "SELECT * FROM spip_index_dico WHERE hash IN (".$hash[0].")";
					$res = spip_query($query);
					while ($row =spip_fetch_array($res)){
						if ($dump) echo "::".$row['dico'];
						$confirmes[$row['dico']]=abs(strlen($row['dico'])-strlen($mot));
					}
				};
				if ($dump) echo "<br/>";// pour le debugage
			}
		
			$best = array($mot=>0);
			$best_match = 10000;
			if (count($confirmes)){
				//asort($confirmes);
				// calcul de l'erreur absolue
				foreach(array_keys($confirmes) as $key){
					$confirmes[$key] = $score = RechercheEtendue_mot_match($mot,$key,$best_match);
					if ($score==$best_match)
						$best[$key] = 0;
					else if ($score<$best_match){
						$best_match = $score;
						$best = array($key=>0);
					}
			 	}
				if ($dump) var_dump($confirmes);// pour le debugage
		 	}
		 	// TODO : trouver le plus pertinent en cas d'exaequo
		 	/*if (count($best)>1){
		 		$soundex = RechercheEtendue_soundex_fr($mot);
		 		if($dump) echo "$mot:$soundex"."<br/>";
		 		foreach($best as $test=>$dummy)
		 		{
		 			if($dump) echo "$test:".RechercheEtendue_soundex_fr($test)."<br/>";
		 		}
		 	}*/
		 	$mot_semblable_best[$mot]=reset(array_keys($best));
			// ecrire le cache de la recherche sur le disque
			ecrire_fichier($fcache[$mot], serialize($mot_semblable_best[$mot]));
			// purger le petit cache
			nettoyer_petit_cache('simi', 300);
		}
	
		return $mot_semblable_best[$mot];
	}


?>
