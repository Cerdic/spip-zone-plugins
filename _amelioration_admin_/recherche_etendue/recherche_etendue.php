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


	function RechercheEtendue_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" ) {

		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['accueil']->sousmenu["admin_index"]= new Bouton(
			"../"._DIR_PLUGIN_ADVANCED_SEARCH."/stock_index.png",  // icone
			_L("Gestion de l'indexation") //titre
			);

			$boutons_admin['accueil']->sousmenu["index_tous"]= new Bouton(
			"../"._DIR_PLUGIN_ADVANCED_SEARCH."/stock_book-alt.png",  // icone
			_L("Dictionnaire indexe") //titre
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
		$texte .= "<img src='$p/bar_middle.gif' width='$point' height='$height' alt='score $point' />";
		$texte .= "<img src='$p/bar_right.gif' alt='' />";
		return $texte;
	}
	
	function RechercheEtendue_star($texte,$starfilename = "") {
		if ($starfilename="")
			$starfilename=_DIR_PLUGIN_ADVANCED_SEARCH."/star.gif";
		$point = $texte;
		$texte = "";
		$star1="<img src='$starfilename' alt='1 etoile' />";
		$star2="<img src='$starfilename' alt='2 etoiles' />&nbsp;<img src='$starfilename' alt=''/>";
		$star3="<img src='$starfilename' alt='3 etoiles' />&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>";
		$star4="<img src='$starfilename' alt='4 etoiles' />&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>";
		$star5="<img src='$starfilename' alt='5 etoiles' />&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>&nbsp;<img src='$starfilename' alt=''/>";
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

	
	function RechercheEtendue_surligner_sans_accents_recherche ($mot) {
		$accents =
			/* A */ chr(192).chr(193).chr(194).chr(195).chr(196).chr(197).
			/* a */ chr(224).chr(225).chr(226).chr(227).chr(228).chr(229).
			/* O */ chr(210).chr(211).chr(212).chr(213).chr(214).chr(216).
			/* o */ chr(242).chr(243).chr(244).chr(245).chr(246).chr(248).
			/* E */ chr(200).chr(201).chr(202).chr(203).
			/* e */ chr(232).chr(233).chr(234).chr(235).
			/* Cc */ chr(199).chr(231).
			/* I */ chr(204).chr(205).chr(206).chr(207).
			/* i */ chr(236).chr(237).chr(238).chr(239).
			/* U */ chr(217).chr(218).chr(219).chr(220).
			/* u */ chr(249).chr(250).chr(251).chr(252).
			/* yNn */ chr(255).chr(209).chr(241);
	
		if ($GLOBALS['meta']['charset'] == 'utf-8') {
			include_spip('inc/charsets');
			$mot = unicode2charset(utf_8_to_unicode($mot), 'iso-8859-1');
		}
	
		return strtr($mot, $accents, "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
	}
	function RechercheEtendue_split_by_char_recherche($str) {
			$len = strlen($str);
			$streturn = array();
			for ($i=0; $i<$len; $i++) {
				$streturn[$i] = substr($str, $i, 1);
			}
		return $streturn;
	}
	function RechercheEtendue_surligner_regexp_accents_recherche ($mot) {
		$accents_regexp = array(
			"a" => "[a".chr(224).chr(225).chr(226).chr(227).chr(228).chr(229). chr(192).chr(193).chr(194).chr(195).chr(196).chr(197)."]",
			"o" => "[o".chr(242).chr(243).chr(244).chr(245).chr(246).chr(248). chr(210).chr(211).chr(212).chr(213).chr(214).chr(216)."]",
			"e" => "[e".chr(232).chr(233).chr(234).chr(235). chr(200).chr(201).chr(202).chr(203)."]",
			"c" => "[c".chr(199).chr(231)."]",
			"i" => "[i".chr(236).chr(237).chr(238).chr(239). chr(204).chr(205).chr(206).chr(207)."]",
			"u" => "[u".chr(249).chr(250).chr(251).chr(252). chr(217).chr(218).chr(219).chr(220)."]",
			"y" => "[y".chr(255)."]",
			"n" => "[n".chr(209).chr(241)."]"
		);
	
		$mot = RechercheEtendue_surligner_sans_accents_recherche ($mot);
		if ($GLOBALS['meta']['charset'] == 'utf-8') {
			while(list($k,$s) = each ($accents_regexp)) {
				$accents_regexp_utf8[$k] = "(".join("|", RechercheEtendue_split_by_char_recherche(preg_replace(',[\]\[],','',$accents_regexp[$k]))).")";
			}
			$mot = strtr(strtolower($mot), $accents_regexp_utf8);
			$mot = importer_charset($mot, 'iso-8859-1');
		} else
			$mot = strtr(strtolower($mot), $accents_regexp);
	
		return $mot;
	}
	function RechercheEtendue_surligner_mots_recherche($page, $mots) {
		// Remplacer les caracteres potentiellement accentues dans la chaine
		// de recherche par les choix correspondants en syntaxe regexp (!)
		$mots = preg_split(',\s+,ms', $mots);
	
		foreach ($mots as $mot) {
			if (strlen($mot) >= 2) {
				$mot = RechercheEtendue_surligner_regexp_accents_recherche(preg_quote(str_replace('/', '', $mot)));
				$mots_surligne[] = $mot;
			}
		}
	
		if (!$mots_surligne) return $page;
	
		$regexp = '/((^|>)([^<]*[^[:alnum:]_<\x80-\xFF])?)(('
		. join('|', $mots_surligne)
		. ')[[:alnum:]_\x80-\xFF]*?)/Uis';
		return $debut . RechercheEtendue_surligne_recherche($page, $regexp);
	}


	function RechercheEtendue_surligne_recherche($page, $regexp) {
		$page = preg_replace($regexp, '\1<span class="surligne">\4</span>', $page, 4);
		return $page ;
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
		  	$string .= attribut_html($texte);
		  	return "";
		}
		return "";
	}
	function google_like_string_raz($texte){
	  return RechercheEtendue_google_like_string($texte,'raz');
	}
	
	function RechercheEtendue_google_like($query,$alternative = ""){
	  $string = RechercheEtendue_google_like_string('','get');
	  $string = html_entity_decode($string);
		$qt = explode(" ", $query);
		$num = count ($qt);
		$cc = ceil(200 / $num);
		$string_re = "";
		for ($i = 0; $i < $num; $i++) {
			$tab[$i] = preg_split("/($qt[$i])/i",$string,2, PREG_SPLIT_DELIM_CAPTURE);
			if(count($tab[$i])>1){
				$avant[$i] = substr($tab[$i][0],-$cc,$cc);
				$pos = strpos($avant[$i], " ");
				$avant[$i]= substr($avant[$i],$pos);
				$apres[$i] = substr($tab[$i][2],0,$cc);
				$pos = strrpos($apres[$i], " ");
				$apres[$i] = substr($apres[$i],0,$pos);
				//$string_re .= "<em>[...]</em> $avant[$i]<strong>".$tab[$i][1]."</strong>$apres[$i] <em>[...]</em> ";
				$string_re .= "<em>[...]</em> $avant[$i]".$tab[$i][1]."$apres[$i] <em>[...]</em> ";
			}
		}
		if (strlen($string_re))
			return RechercheEtendue_surligner_mots_recherche($string_re,$query);
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
		$table_mots_semblables = array_map('RechercheEtendue_mot_semblable',$table_mots);
		if (count(array_diff($table_mots_semblables,$table_mots)))
			return implode(" ",$table_mots_semblables);
		else 
			return ""; // si pas mieux a proposer, le filtre ne retourne rien
	}
	// infame salmigondi
	// a remplacer par levensthein
	function RechercheEtendue_mot_err($mot1, $mot2, $beta = 10000){
		$len1 = strlen($mot1);
		$len2 = strlen($mot2);
		$minlen = min($len1,$len2);
		$err = 0;
		$err += 1*($len1+$len2-2*$minlen); // idem max()-min()
		for($k = 0;$k<$minlen;$k++){
			$err ++;
			if ($err>$beta) break;
		}
		return $err;
	}
	function RechercheEtendue_mot_match($mot1, $mot2, $beta = 10000){
		static $match_profondeur = 2;
		$len1 = strlen($mot1);
		$len2 = strlen($mot2);
		if ($len1>$len2)
			return RechercheEtendue_mot_match($mot2,$mot1,$beta);
		else {
			$err = RechercheEtendue_mot_err($mot1,$mot2,$beta);
			if (($len1<$len2)&&($len1>=$len2-$match_profondeur)){
				for($k=0;$k<$len1;$k++){
					$test = "";
					$test = substr($mot1,0,$k);
					$test .= substr($mot2,$k,1);
					$test .= substr($mot1,$k);
					$err = min($err,RechercheEtendue_mot_match($test, $mot2, $beta));
				}
			}
			return $err;
		}
	}
	function RechercheEtendue_mot_semblable($mot){
		static $mot_semblable_best = array();
	
		// eviter de recalculer deux fois pour le meme mot
		// surtout si le filtre est appelle plusieurs fois pour la meme recherche
		if (!isset($mot_semblable_best[$mot])){
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
		
			if (isset($_GET['dump'])) var_dump($candidats);// pour le debugage
			$confirmes = array();
			foreach ($candidats as $test){
				if (isset($_GET['dump'])) echo "::$test";// pour le debugage
				$hash = requete_hash($test);
			  $hashres = $hash[0]; // on peut prendre le non strict
				if ($hashres){
					$query = "SELECT * FROM spip_index_dico WHERE hash IN (".$hash[0].")";
					$res = spip_query($query);
					while ($row =spip_fetch_array($res)){
						if (isset($_GET['dump'])) echo "::".$row['dico'];
						$confirmes[$row['dico']]=0;
					}
				};
				if (isset($_GET['dump'])) echo "<br/>";// pour le debugage
			}
		
			$best = $mot;
			$best_match = 10000;
			if (count($confirmes)){
				// calcul de l'erreur absolue
				foreach(array_keys($confirmes) as $key){
					$confirmes[$key] = $score = RechercheEtendue_mot_match($mot,$key,$best_match);
					if ($score<$best_match){
						$best_match = $score;
						$best = $key;
					}
			 	}
				if (isset($_GET['dump'])) var_dump($confirmes);// pour le debugage
		 	}
		 	$mot_semblable_best[$mot]=$best;
		}
	
		return $mot_semblable_best[$mot];
	}


?>