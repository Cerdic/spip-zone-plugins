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
	function RechercheEtendue_jauge($texte,$nom_barre,$gain=1){
		static $barfilename;
		static $barheight;
		$point = round($texte*$gain);
		$barre_path = dirname(__FILE__)."/$nom_barre/";
		if (!isset($barfilename)) $barfilename='';
		if ($barre_path.'bar_middle.gif' != $barfilename){
			$barfilename = $barre_path.'bar_middle.gif';
			list($width, $barheight, $type, $attr) = getimagesize($barfilename);
		}
		$texte = "";
		$texte = "<img src='"._DIR_PLUGIN_ADVANCED_SEARCH."/$nom_barre/bar_left.gif' alt='' />";
		$texte .= "<img src='"._DIR_PLUGIN_ADVANCED_SEARCH."/$nom_barre/bar_middle.gif' width='$point' height='$barheight' alt='score $point' />";
		$texte .= "<img src='"._DIR_PLUGIN_ADVANCED_SEARCH."/$nom_barre/bar_middle.gif' alt='' />";
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
		  	$string .= $texte;
		  	return "";
		}
		return "";
	}
	function google_like_string_raz($texte){
	  return RechercheEtendue_google_like_string($texte,'raz');
	}
	
	function RechercheEtendue_google_like($query){
	  $string = RechercheEtendue_google_like_string('','get');
		$qt = explode(" ", $query);
		$num = count ($qt);
		$cc = ceil(200 / $num);
		for ($i = 0; $i < $num; $i++) {
			$tab[$i] = preg_split("/($qt[$i])/i",$string,2, PREG_SPLIT_DELIM_CAPTURE);
			if(count($tab[$i])>1){
				$avant[$i] = substr($tab[$i][0],-$cc,$cc);
				$pos = strpos($avant[$i], " ");
				$avant[$i]= substr($avant[$i],$pos);
				$apres[$i] = substr($tab[$i][2],0,$cc);
				$pos = strrpos($apres[$i], " ");
				$apres[$i] = substr($apres[$i],0,$pos);
				$string_re .= "<em>[...]</em> $avant[$i]<strong>".$tab[$i][1]."</strong>$apres[$i] <em>[...]</em> ";
			}
		}
		return $string_re;
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
	   // On met tout en minuscule
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
		$regs = separateurs_indexation(true)." ";
		$recherche = strtr($recherche, $regs, ereg_replace('.', ' ', $regs));
		$table_mots = preg_split("/ +/", $recherche);
	
		return implode(" ",array_map('mot_semblable',$table_mots));
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
			$translitteration_complexe = true;
			$base = nettoyer_chaine_indexation($mot);
			foreach(array_keys($confirmes) as $key){
				$confirmes[$key] = $score = RechercheEtendue_mot_match($base,$key,$best_match);
				if ($score<$best_match){
					$best_match = $score;
					$best = $key;
				}
		 	}
			if (isset($_GET['dump'])) var_dump($confirmes);// pour le debugage
	 	}
	
		return $best;
	}


?>