<?php

/**
 * Inserer le step 1 avant la premiere CSS du head
 * Inserer le step 3 avant la fin du head
 *
 * @param string $head
 * @return string
 */

/**
 * http://spip.pastebin.fr/48351
 */

function daisy_order($head){
    $search = "<link";
    if (preg_match(",<link\s[^>]*stylesheet,Uims", $head, $match))
        $search = $match[0];
    $p = stripos($head, $search);
    $h = recuperer_fond('inclure/daisy-head-1',array());
    $h = "\n".trim($h)."\n";
 
    $head = substr_replace($head, $h, $p, 0);
 
    $code = recuperer_fond('inclure/daisy-head-3',array());
    if (false !== strpos($head, '</head>')) {
        $head = preg_replace(',</head>,', $code . "\n" . '</head>', $head, 1);
    } else {
        $head .= "\n" . $code;
    }
 
    return $head;
}

/**
 * Inserer dans le head
 *
 * @param string $flux
 * @return string
 */
function daisy_insert_head($flux){
	if (!test_plugin_actif('Zcore')){
		$flux .= "<"."?php header(\"X-Spip-Filtre: daisy_order\"); ?".">";
	}
	return $flux;
}

?>