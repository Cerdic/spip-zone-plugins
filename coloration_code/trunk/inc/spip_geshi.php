<?php

/**
 * Surcharge de la classe GESHI 1.0.8
 * 
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('geshi/geshi');

// a la sauce GESHI
define('SPIP_GESHI_REGEXP_FUNCTION', 99);



class SPIP_GeSHi extends GeSHi {

	// fonction de traitement de certaines regexp spip
	var $_hmr_func = '';

	/**
	 * Cette surcharge implemente simplement
	 * une nouvelle cle dans $language_data
	 * intitulee 'SPIP_GESHI_COLOR_FUNCTION' permettant d'appeler une fonction
	 * existante avec le code source a colorier, utilise dans la declaration geshi/spip3.php
	 *
	 * Cette fonction doit retourner le code avec les instructions
	 * en plus de geshi pour colorier, qui sont des choses comme : 
	 * <| class="br0">contenu|>
	 */
	function parse_non_string_part($stuff_to_parse) {

		// Fonction de coloration definie
		// doit s'occuper de retourner des <| et d'echapper les textes (hsc)
		if (isset($this->language_data['SPIP_GESHI_COLOR_FUNCTION']) and $this->language_data['SPIP_GESHI_COLOR_FUNCTION']) {
			$parse = $this->language_data['SPIP_GESHI_COLOR_FUNCTION'];
			$stuff_to_parse = $parse($stuff_to_parse);
			# on reprend le minimum syndical de parse_non_string_part()
			# en esperant que ca suffise pour ce qu'on a a faire.
			# Ca semble que c'est ok.
			$stuff_to_parse = str_replace('<|', '<span', $stuff_to_parse);
			$stuff_to_parse = str_replace ( '|>', '</span>', $stuff_to_parse );
			return $stuff_to_parse;
		}

		return parent::parse_non_string_part($stuff_to_parse);
	}


	/**
	 * Cette surcharge implemente simplement
	 * une nouvelle cle dans le tableau de declaration d'une REGEXP
	 * permettant de passer une fonction via la cle SPIP_GESHI_REGEXP_FUNCTION.
	 * Cette fonction, si presente, sera appelee avec
	 * le resultat du match de la regexp, a la place des traitements
	 * habituels de GESHI, et doit alors retourner le code attendu par GESHI,
	 * a savoir quelque chose comme : "avant <|!REG3XP31!>contenu|> apres"
	 * 
	 * Cette possibilite est utilise dans geshi/spip2.php
	 */
	function handle_multiline_regexps($matches) {
		$key = $this->_hmr_key;
		if (    isset($this->language_data['REGEXPS'][$key][SPIP_GESHI_REGEXP_FUNCTION])
		  and $func = $this->language_data['REGEXPS'][$key][SPIP_GESHI_REGEXP_FUNCTION]) {
			return $func($matches, $this);
		}

		return parent::handle_multiline_regexps($matches);
	}


	/**
	 * Cette surcharge d'une surcharge de Geshi
	 * implemente pour le non multiligne la meme chose que
	 * pour handle_multiline_regexps() du dessus
	 *
	**/
	function handle_singleline_regexps($stuff_to_parse, $regexp, $key) {

		if (    isset($regexp[SPIP_GESHI_REGEXP_FUNCTION])
		  and $func = $regexp[SPIP_GESHI_REGEXP_FUNCTION]) {

			$this->_hmr_key  = $key;
			$this->_hmr_func = $func;

			$stuff_to_parse = preg_replace_callback(
				'/' . $regexp[GESHI_SEARCH] . '/' . $regexp[GESHI_MODIFIERS],
				array($this, 'handle_singleline_regexps_bis'),
				$stuff_to_parse);

			$this->_hmr_func = '';

			return $stuff_to_parse;
		}

		return parent::handle_singleline_regexps($stuff_to_parse, $regexp, $key);
	}



	/**
	 * Renvoyer sur la fonction du colorieur SPIP demandee 
	 *
	**/
	function handle_singleline_regexps_bis($matches) {
		$func = $this->_hmr_func;
		return $func($matches, $this);
	}

}

?>
