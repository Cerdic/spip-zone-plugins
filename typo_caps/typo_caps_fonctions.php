<?php

/* code tire de typogrify
 * http://jeffcroft.com/sidenotes/2007/may/29/typogrify-easily-produce-web-typography-doesnt-suc/
 */


/**
 * This is necessary to keep dotted cap strings to pick up extra spaces
 * used in preg_replace_callback later on
 */
function _cap_wrapper( $matchobj )
{
    if ( !empty($matchobj[2]) )
    {
        return sprintf('<span class="caps">%s</span>', $matchobj[2]);
    }
    else 
    {
        $mthree = $matchobj[3];
        if ( ($mthree{strlen($mthree)-1}) == " " )
        {
            $caps = substr($mthree, 0, -1);
            $tail = ' ';
        }
        else
        {
            $caps = $mthree;
            $tail = '';
        }            
        return sprintf('<span class="caps">%s</span>%s', $caps, $tail);
    }
}

function typo_caps_caps($t){
	// eviter les tags
	$t = preg_split(',(<[^>]+>),UmsS', $t, null, PREG_SPLIT_DELIM_CAPTURE);
	$cap_finder = "/(
            (\b[A-Z\d]*        # Group 2: Any amount of caps and digits
            [A-Z]\d*[A-Z]      # A cap string much at least include two caps (but they can have digits between them)
            [A-Z\d]*\b)        # Any amount of caps and digits
            | (\b[A-Z]+\.\s?   # OR: Group 3: Some caps, followed by a '.' and an optional space
            (?:[A-Z]+\.\s?)+)  # Followed by the same thing at least once more
            (?:\s|\b|$))/xS";
	for ($i = 0; $i < count($t); $i+=2)
		$t[$i] = preg_replace_callback($cap_finder, _cap_wrapper, $t[$i]);
	return join('', $t);
}

?>