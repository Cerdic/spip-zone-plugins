<?php
if (!isset($GLOBALS['spip_version_branche']) OR intval($GLOBALS['spip_version_branche'])<2){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FRIMOUSSES',(_DIR_PLUGINS.end($p)).'/');
}

// balises de tracage, directement compatibles regexpr
// le separateur _FRIMOUSSES_HTMLX est supprime en fin de calcul
@define('_FRIMOUSSES_HTMLA', '<span class="frimoussesfoo htmla"></span>');
@define('_FRIMOUSSES_HTMLB', '<span class="frimoussesfoo htmlb"></span>');
@define('_FRIMOUSSES_HTMLX', '<span class="frimoussesfoo \w+"></span>');


// fonction ajoutant un smiley au tableau $tab
// ex : frimousses_compile_smiley($tab, ':-*', 'icon_kiss', 'gif');
function frimousses_compile_smiley(&$tab, $smy, $img, $ext='png') {
    static $path, $path2;
    if(!isset($path)) {
            $path = find_in_path('frimousses');
            $path2 = url_absolue($path);
            $pp = defined('_DIR_PLUGIN_PORTE_PLUME');
    }
    $espace = strlen($smy)==2?' ':'';
    $file = "$img.$ext";
    list(,,,$size) = @getimagesize("$path/$file");
    $tab['0']['0'][] = $espace.$smy;
    // frimousses_code_echappement evite que le remplacement se fasse a l'interieur des attributs de la balise <img>
    $tab[0][1][] = frimousses_code_echappement("$espace<img alt=\"$smy\" title=\"$smy\" class=\"no_image_filtrer format_$ext\" src=\"$path2/$file\" $size/>", 'FRIMOUSSES');
    
    $tab[0][2][] = $file;
    $tab['racc'][] = $smy;
    // pour le porte-plume
    $tab[0][4]['smiley_'.$img] = $file;
}

// cette fonction appelee automatiquement a chaque affichage de la page privee du Couteau Suisse renvoie un tableau
function frimousses_liste_smileys($tab = array(0 => array(), 'racc' => array())) {
    // l'ordre des smileys ici est important :
    //  - les doubles, puis les simples, puis les courts
    //  - le raccourci insere par la balise #SMILEYS est la premiere occurence de chaque fichier
    $smileys = array(
    // attention ' est different de ’ (&#8217;) (SPIP utilise/ecrit ce dernier)
        ":&#8217;-))"=> 'pleure_de_rire',
        ":&#8217;-)"=> 'pleure_de_rire',
        ":&#8217;-D"   => 'pleure_de_rire',
        ":&#8217;-("   => 'triste',
    
    // les doubles :
        ':-))' => 'mort_de_rire',
        ':))'  => 'mort_de_rire',
        ":'-))"=> 'pleure_de_rire',
        ':-((' => 'en_colere',
        ':-)*' => 'bisou',
        ':-...' => 'rouge',
        ':...' => 'rouge', 
        ':-..' => 'rouge', 
        ':..' => 'rouge', 
        ':-.' => 'rouge', 

        
        
    // les simples :
        ';-)'  => 'clin_d-oeil',
        ':-)'  => 'sourire',
        ':-D'  => 'mort_de_rire',
        ":'-)"=> 'pleure_de_rire',
        ":'-D" => 'pleure_de_rire',
        ':-('  => 'pas_content',
        ":'-(" => 'triste',
        ':~(' => 'triste',
        ':-&gt;' => 'diable',
        ':o)'  => 'rigolo',
        'B-)'  => 'lunettes',
        ':-P'  => 'tire_la_langue',
        ':-p'  => 'tire_la_langue',
        ':-|'  => 'bof',
        '|-)'  => 'bof',        
        ':-/'  => 'mouais',
        ':-O'  => 'surpris',
        ':-o'  => 'surpris',
        ':-*'  => 'bisou',
        'o:)' => 'ange',
        'O:)' => 'ange',
        '0:)' => 'ange',
        ':.' => 'rouge', 
        ':-x' => 'bouche_cousu',
        ':-@' => 'dort',
        ':-$' => 'argent',
        ':-!' => 'indeci',
        
    // les courts : tester a l'usage...
    // attention : ils ne sont reconnus que s'il y a un espace avant !
        ':)'   => 'sourire',
        ':('   => 'pas_content',
        ';)'   => 'clin_d-oeil',
        ':|'   => 'bof',
        '|)'   => 'bof',
        ':/'   => 'mouais',
    );
    
    foreach ($smileys as $smy=>$val)
            frimousses_compile_smiley($tab, $smy, $val);

    return $tab;
}


// fonction qui renvoie un tableau de smileys uniques
function frimousses_smileys_uniques($smileys) {
        $max = count($smileys[1]);
        $new = array(array(), array(), array());
        for ($i=0; $i<$max; $i++) {
            if(!in_array($smileys[2][$i], $new[2])) {
                $new[0][] = $smileys[0][$i]; // texte
                $new[1][] = $smileys[1][$i]; // image
                $new[2][] = $smileys[2][$i]; // nom de fichier
            }
        }
        return $new;
}

// fonction principale (pipeline pre_typo)
function frimousses_pre_typo($texte) {
        if (strpos($texte, ':')===false && strpos($texte, ')')===false) return $texte;
        // appeler frimousses_rempl_smileys() une fois que certaines balises ont ete protegees
        return frimousses_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'frimousses_rempl_smileys', $texte);
}


// evite les transformations typo dans les balises $balises
// par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
// $fonction est la fonction prevue pour transformer $texte
// si $fonction = false, alors le texte est retourne simplement protege
// $texte est le texte d'origine
// si $balises = '' alors la protection par defaut est : html|code|cadre|frame|script
// si $balises = false alors le texte est utilise tel quel
function frimousses_echappe_balises($balises, $fonction, $texte, $arg=NULL){
        if(!strlen($texte)) return '';
        if (($fonction!==false) && !function_exists($fonction)) {
                // chargement des fonctions
                include_spip('frimousses_fonctions');
                if (!function_exists($fonction)) {
                        spip_log("Erreur - frimousses_echappe_balises() : $fonction() non definie dans : ".$_SERVER['REQUEST_URI']);
                        return $texte;
                }
        }
        // trace d'anciennes balises <html></html> ou autre echappement SPIP ?
        if(strpos($texte, _FRIMOUSSES_HTMLA)!==false) {
                $texte = preg_replace(',<p[^>]*>(\s*'._FRIMOUSSES_HTMLX.')</p>,', '$1', $texte);
                $texte = preg_replace_callback(','._FRIMOUSSES_HTMLA.'(.*?)(?='._FRIMOUSSES_HTMLB.'),s', 'frimousses_echappe_html_callback', $texte);
        }
        // protection du texte
        if($balises!==false) {
                if(!strlen($balises)) $balises = 'html|code|cadre|frame|script';
                $balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
                include_spip('inc/texte');
                $texte = echappe_html($texte, 'FRIMOUSSES', true, $balises);
        }
        // retour du texte simplement protege
        if ($fonction===false) return $texte;
        // transformation par $fonction
        $texte = $arg==NULL?$fonction($texte):$fonction($texte, $arg);
        // deprotection en abime, notamment des modeles...
        if(strpos($texte, 'base64FRIMOUSSES')!==false) $texte = echappe_retour($texte, 'FRIMOUSSES');
        if(strpos($texte, 'base64FRIMOUSSES')!==false) return echappe_retour($texte, 'FRIMOUSSES');
        return $texte;
}

// fonction callback pour frimousses_echappe_balises
function frimousses_echappe_html_callback($matches) {
 return _FRIMOUSSES_HTMLA.frimousses_code_echappement($matches[1], 'FRIMOUSSES');
}


// Echapper les elements perilleux en les passant en base64
// Creer un bloc base64 correspondant a $rempl ; au besoin en marquant
// une $source differente ; optimisation du code spip !
// echappe_retour() permet de revenir en arriere
function frimousses_code_echappement($rempl, $source='', $mode='span') {
        // Convertir en base64
        $base64 = base64_encode($rempl);
        // guillemets simples dans la balise pour simplifier l'outil 'guillemets'
        return "<$mode class='base64$source' title='$base64'></$mode>";
}



function frimousses_echappe_balises_callback($matches) {
 return frimousses_code_echappement($matches[1], 'FRIMOUSSES');
}

// fonction de remplacement
// les balises suivantes sont protegees : html|code|cadre|frame|script|acronym|cite
function frimousses_rempl_smileys($texte) {
        if (strpos($texte, ':')===false && strpos($texte, ')')===false) return $texte;
        $smileys_rempl = frimousses_liste_smileys()[0];
        $texte = preg_replace_callback(',(<img .*?/>),ms', 'frimousses_echappe_balises_callback', $texte);
        // smileys a probleme :
        $texte = str_replace(':->', ':-&gt;', $texte); // remplacer > par &gt;
        // remplacer ’ (apostrophe curly) par &#8217;
        $texte = str_replace(':’-', ':&#8217;-', $texte);
        $texte = str_replace(':'.chr(146).'-', ':&#8217;-', $texte);
        // voila, on remplace tous les smileys d'un coup...
        $texte = str_replace($smileys_rempl[0], $smileys_rempl[1], $texte);

        return echappe_retour($texte, 'FRIMOUSSES');
}

?>