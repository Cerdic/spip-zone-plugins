<?php
			/*#################################################################################
			# Project: Web Hyphenation 1.2 (c) yellowgreen designbüro                         #
			# For more information, go to http://yellowgreen.de/hyphenation.                  #
			# License: http://creativecommons.org/licenses/by-sa/3.0/                         #
			# Hyphenation online generator: http://yellowgreen.de/soft-hyphenation-generator  #
			#################################################################################*/

// SETTINGS


		define(_PB_HYPHEN, "&#173;");
		
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_PB_CESURE',(_DIR_PLUGINS.end($p)));

		define (_PB_PATH_TO_PATTERNS, _DIR_PLUGIN_PB_CESURE."patterns/");
		define(_PB_DICTIONARY, "dictionary.txt");
		define (_PB_EXCLUDE_TAGS, "code,pre,script,style,pbperso");

		// supply a mbstring alternative
		// should use spip_strpos if any in the future
		// inspired from inc/charsets.php (SPIP)
		function pb_strpos($haystack, $needle, $offset=NULL, $encoding=NULL) {
		$encoding = $GLOBALS['meta']['charset'];

			// Si ce n'est pas utf-8, utiliser strlen
			if ($GLOBALS['meta']['charset'] != 'utf-8')
				return strpos($haystack, $needle, $offset);

			// Sinon, utiliser mb_strpos() si disponible
			if (init_mb_string())
				return mb_strpos($haystack, $needle, $offset, $encoding);

			// Methode manuelle : on supprime les bytes 10......,
			// on compte donc les ascii (0.......) et les demarrages
			// de caracteres utf-8 (11......)
			return strpos($haystack, $needle, $offset);
		}
		
		function pb_strtolower($str, $encoding=NULL ) {
			$encoding = $GLOBALS['meta']['charset'];
			// Si ce n'est pas utf-8, utiliser strlen
			if ($GLOBALS['meta']['charset'] != 'utf-8')
				return strtolower($str);

			// Sinon, utiliser mb_strtolower() si disponible
			if (init_mb_string())
				return mb_strtolower($str, $encoding);

			// Methode manuelle : on supprime les bytes 10......,
			// on compte donc les ascii (0.......) et les demarrages
			// de caracteres utf-8 (11......)
			return strtolower($str);
		}

// FUNCTIONS
			
			// Function: Replace by comparing
			function pb_str_replace_comp($needle, $replace, $string_a, $string_b) {
				$position = 0;
				while($position !== false && $position < spip_strlen($string_a)) {
					$position = pb_strpos($string_a, $needle, $position + 1);
					if($position != 0) $string_b = spip_substr($string_b, 0, $position) . $replace . spip_substr($string_b, $position);
				}
				return $string_b;
			}

			// Function: Word hyphenation
			function pb_word_hyphenation($word) {
				if(spip_strlen($word) < $GLOBALS["pb_charmin"]) return $word;
				
				if(($key = array_search(strtolower($word), $GLOBALS["dictionary_words"])) !== false)
					return pb_str_replace_comp("/", _PB_HYPHEN, trim($GLOBALS["pb_dictionary"][$key]), $word);
			
				$positions = ""; $hyphenated_word = ""; $word_without_PB_HYPHEN = "";
				$tex_word = " " . pb_strtolower($word) . " ";
				for($i = 0; $i < spip_strlen($tex_word); $i++) $positions .= 0;
				
				for($start = 0; $start < spip_strlen($tex_word); $start++) {
					for($length = 1; $length <= spip_strlen($tex_word) - $start; $length++) {
						$patterns_index = spip_substr(spip_substr($tex_word, $start), 0, $length);
						if(isset($GLOBALS["patterns"][$patterns_index])) {
							$values = $GLOBALS["patterns"][$patterns_index];
							$i = $start;
							
							for($p = 0; $p < spip_strlen($values); $p++) {
								$value = spip_substr($values, $p, 1);
 								if($value > $positions[$i - 1]) $positions[$i - 1] = $value;
								$i++;
							}
						}
					}
				}
				
				$positions = trim($positions);
		
				for($i = 0; $i < spip_strlen($word); $i++) {
					$word_without_PB_HYPHEN = str_replace(_PB_HYPHEN, "", $hyphenated_word);
					if($positions[$i] % 2 != 0 && $i != 0 && $i >= $GLOBALS["pb_leftmin"] && $i <= spip_strlen($word) - $GLOBALS["pb_rightmin"]) $hyphenated_word .= spip_substr($word, spip_strlen($word_without_PB_HYPHEN), $i - spip_strlen($word_without_PB_HYPHEN)) . _PB_HYPHEN;
				}
				
				$hyphenated_word .= spip_substr($word, spip_strlen($word_without_PB_HYPHEN), $i - spip_strlen($word_without_PB_HYPHEN));
				
				return $hyphenated_word;
			}



			function pb_effectuer_cesure($text, $lang="xxx") {


				$GLOBALS["pb_leftmin"] = 3;
				$GLOBALS["pb_rightmin"] = 4;
				$GLOBALS["pb_charmin"] = 7;
				if (init_mb_string()) mb_internal_encoding("utf-8");
			
			
				if(file_exists(_PB_PATH_TO_PATTERNS . $lang . ".php")) { include(_PB_PATH_TO_PATTERNS . $lang . ".php"); $GLOBALS["patterns"] = $patterns; } else $GLOBALS["patterns"] = array();
				file_exists(_PB_DICTIONARY) ? $GLOBALS["pb_dictionary"] = file(_PB_DICTIONARY) : $GLOBALS["pb_dictionary"] = array();
				$GLOBALS["dictionary_words"] = $GLOBALS["pb_dictionary"];
				for($i = 1; $i < count($GLOBALS["pb_dictionary"]); $i++) $GLOBALS["dictionary_words"][$i] = str_replace("/", "", strtolower(trim($GLOBALS["pb_dictionary"][$i])));

				$word = ""; $tag = ""; $tag_jump = 0; $output = array();
				$word_boundaries = "<>\t\n\r\0\x0B !\"§$%&/()=?….,;:-–_„”«»‘’'/\\‹›()[]{}*+´`^|©℗®™℠¹²³";


				$text = $text . " ";
				
				for($i = 0; $i < spip_strlen($text); $i++) {
					$char = spip_substr($text, $i, 1);
					if(pb_strpos($word_boundaries, $char) === false && $tag == "") {
						$word .= $char;
					} else {
						if($word != "") { 
							// Couper les mots, sauf ceux avec majuscule initiale (francais, anglais)
							if (($lang == "fr" || $lang =="en")) {
								if (ereg("^[A-ZÀÉÈÎ]", $word)) $output[] = $word; 
								else $output[] = pb_word_hyphenation($word);
							}
							else {
								$output[] = pb_word_hyphenation($word);
							}
							$word = ""; 
						}
						if($tag != "" || $char == "<") $tag .= $char;
						if($tag != "" && $char == ">") {
							$tag_name = (pb_strpos($tag, " ")) ? spip_substr($tag, 1, pb_strpos($tag, " ") - 1) : spip_substr($tag, 1, pb_strpos($tag, ">") - 1);
							if($tag_jump == 0) {
								if(in_array($tag_name, explode(',',_PB_EXCLUDE_TAGS))) $tag_jump = 1; else { $output[] = $tag; $tag = ""; }
							} else { $output[] = $tag; $tag = ""; }
						}
						if($tag == "" && $char != "<" && $char != ">") $output[] = $char;
					}
				}
				
				$text = join($output);

				if (strlen($regexp_finale))
					$text = preg_replace($regexp_finale, '\1\2', $text);

				return substr($text, 0, strlen($text) - 1);			
			}
			// Function: Text hyphenation
			function cesure($text, $lang="xxx") {
			
			

				if (ereg("<p[^>]*>", $text)) {
					$text = preg_replace("/<p([^>]*)>(.*)<\/p>/miseU", "'<p\\1>'.stripslashes(pb_effectuer_cesure('\\2',$lang)).'</p>'", $text);
				} else {
					$text = pb_effectuer_cesure($text, $lang);
				}
				
				// Corriger quand cesure a l'interieur d'un caractere special, genre &ccedil;
				$text = preg_replace("/&([a-zA-Z]+)".str_replace("#", "\#", preg_quote(_PB_HYPHEN))."([a-z]+);/", "&\\1\\2;", $text);
				
				return $text;
			}
?>