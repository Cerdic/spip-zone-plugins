<?php
// doc unicode:
// http://www.unicode.org/charts/
// http://tlt.its.psu.edu/suggestions/international/bylanguage/japanesecharthiragana.html
// http://tlt.its.psu.edu/suggestions/international/bylanguage/japanesechartkatakana.html

//
//  valeurs romaji-hiragana
//
function nihongo_hiragana_table() {
    // unicode range 3041-3094
    $hiragana_ref = 12353;
    $hiragana = array("a*","a","i*","i","u*","u","e*","e","o*","o",
                       "ka","ga","ki","gi","ku","gu","ke","ge","ko","go",
                       "sa","za","shi","ji","su","zu","se","ze","so","zo",
                       "ta","da","chi","di","tsu*","tsu","du","te","de","to","do",
                       "na","ni","nu","ne","no",
                       "ha","ba","pa","hi","bi","pi","fu","bu","pu","he","be","pe","ho","bo","po",
                       "ma","mi","mu","me","mo",
                       "ya*","ya","yu*","yu","yo*","yo",
                       "ra","ri","ru","re","ro",
                       "wa*","wa","wi","we","wo",
                       "n","vu"
    );
    return array($hiragana_ref,$hiragana);    
}

function nihongo_katakana_table() {
    // unicode range 30A0-30FA
    $katakana_ref = 12449;
    $katakana = array( "a*","a","i*","i","u*","u","e*","e","o*","o",
                       "ka","ga","ki","gi","ku","gu","ke","ge","ko","go",
                       "sa","za","shi","ji","su","zu","se","ze","so","zo",
                       "ta","da","chi","di","tsu*","tsu","du","te","de","to","do",
                       "na","ni","nu","ne","no",
                       "ha","ba","pa","hi","bi","pi","fu","bu","pu","he","be","pe","ho","bo","po",
                       "ma","mi","mu","me","mo",
                       "ya*","ya","yu*","yu","yo*","yo",
                       "ra","ri","ru","re","ro",
                       "wa*","wa","wi","we","wo",
                       "n","vu",
                       "ka*","ke*",
                       "va","vi","ve","vo",
                       "ten","choonpu"
                      
    );
    return array($katakana_ref,$katakana);    
}



//
// fonction de traitement
//

// parse une chaine en syllale
function nihongo_romaji($str,$parse_char="."){  
   $str = str_replace("*","",$str);  
   return str_replace($parse_char,"",$str);
}

// convertir une chaine romaji en hiragana, katakana
//
// mode: 
// hiragana, h (default, facultatif): affichage en hiragana
// katakana, k                      : affichage en katakana
function nihongo_convert($str,$mode="hiragana",$parse_char="."){
    // mode ?
    if ($mode=="katakana"||$mode=="k") {
        $nihongo_katakana_table = nihongo_katakana_table();
        $charset_table = $nihongo_katakana_table[1];
        $charset_ref = $nihongo_katakana_table[0];
    } else {
        $nihongo_hiragana_table = nihongo_hiragana_table();
        $charset_table = $nihongo_hiragana_table[1];
        $charset_ref = $nihongo_hiragana_table[0];
    }                         
    
    // action : parser la phrase
    $output ="";
    $str = strtolower($str); // peut poser pb charset ?    
    $romajis = explode($parse_char,$str);    
    foreach($romajis as $k=>$romaji) {        
        $keyfound = array_search($romaji, $charset_table); // php 4.0.5+
        if ($keyfound===FALSE) {
           $output .= $romaji;
        } else {
           $html_val = $charset_ref+(int) $keyfound;
           $output .= "&#$html_val;";
        } 
    }
    return $output; 
}

// affiche un caractere par jour
//
// mode: 
// hiragana, h (default, facultatif): affichage en hiragana
// katakana, k                      : affichage en katakana
function nihongo_random($str,$mode="hiragana"){
    // mode ?
    if ($mode=="katakana"||$mode=="k") {
        $nihongo_katakana_table = nihongo_katakana_table();
        $charset_table = $nihongo_katakana_table[1];
        $charset_ref = $nihongo_katakana_table[0];
    }    else {
        $nihongo_hiragana_table = nihongo_hiragana_table();
        $charset_table = $nihongo_hiragana_table[1];
        $charset_ref = $nihongo_hiragana_table[0];
    }                         
    
    // action : parser la phrase
    $key_rnd = rand(0,count($charset_table)-1);    
    $html_val = $charset_ref + $key_rnd;
    $romaji = $charset_table[$key_rnd];   
    $romaji = str_replace("*","",$romaji);
    
    $output = "<dl class=\"nihongo nihongo_rnd\">\n";
    $output .= "<dt>&#$html_val;</dt>\n";
    $output .= "<dd>$romaji</dd>\n";
    $output .= "</dl>\n";
    

    return $output; 
}

?>