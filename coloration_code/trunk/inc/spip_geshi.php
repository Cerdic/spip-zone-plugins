<?php

/**
 * Surcharge de la classe GESHI 1.0.8
 * 
**/

include_spip('geshi/geshi');

// a la sauce GESHI
define('SPIP_GESHI_REGEXP_FUNCTION', 99);



class SPIP_GeSHi extends GeSHi {

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
}

?>
