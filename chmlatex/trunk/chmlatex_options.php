<?php
function to_win1252($texte)
{
$texte = @iconv("UTF-8","Windows-1252//TRANSLIT",$texte);
return $texte;
}

function chmlatex_propre_latex($t)
{

    $t = latex_echappe_equation($t,true);
    //$t = latex_traiter_modeles($t);
    $t = traiter_modeles($t);

    // Application du filtre Belles Puces http://contrib.spip.net/Belles-puces
    $t = preg_replace('/^-\s+/m','-* ',$t);

    $t = appliquer_regles_wheel($t,array('latex/latex.yaml'));
    $t = latex_echappe_coloration($t);
    $t = echappe_html($t);

    $t = echappe_retour(echappe_retour($t),'latex');
    $t = appliquer_regles_wheel($t,array('latex/latex-retour.yaml'));
    $t = preg_replace("#\^#iU",'\$\\hat{~}\$',$t);
    $t = latex_echappe_equation($t,false);

    $t = preg_replace("#<u>#iU",'\\underline{',$t);
    $t = preg_replace("#</u>#iU",'}',$t);
    $t = preg_replace("#<p.*>#iU",' ',$t);
    $t = preg_replace("#</p>#iU",' ',$t);
    $t = preg_replace("#<sup>#iU",'\$\^{',$t);
    $t = preg_replace("#</sup>#iU",'}\$',$t);
    $t = preg_replace("#<sub>#iU",'\$_{',$t);
    $t = preg_replace("#</sub>#iU",'}\$',$t);
    $t = preg_replace("#<code.*>#iU",' ',$t);
    $t = preg_replace("#</html>#iU",' ',$t);
    $t = preg_replace("#<html>#iU",' ',$t);
    $t = preg_replace("#</strike>#iU",'}',$t);
    $t = preg_replace("#<tr>#iU",' ',$t);
    $t = preg_replace("#</td> </tr>#iU",' ',$t);
    $t = preg_replace("#</tr>#iU",' ',$t);
    $t = preg_replace("#<td>#iU",' ',$t);
    $t = preg_replace("#</td>#iU",' ',$t);
    $t = preg_replace("#</table>#iU",' ',$t);
    $t = preg_replace("#<table>#iU",' ',$t);
    $t = preg_replace("#</span>#iU",' ',$t);
    $t = preg_replace("#<span class='(.*)'>#iU",' ',$t);

    $t = str_replace("<latex\\_strike>",'\\sout{',$t);
    $t = str_replace("<latex\\_table>",'',$t);
    $t = str_replace("\\begin{english}",'',$t);
    $t = str_replace("\\end{english}",'',$t);
    $t = str_replace("\\end{minted}",'',$t);
    $t = str_replace("\\begin{minted}",'',$t);
    $t = str_replace("\\end\\emph{english}",'',$t);
    $t = str_replace("\\end\\emph{minted}",'',$t);
    $t = str_replace("\\verb¡",'',$t);
    $t = str_replace("\\$",'$',$t);

    $t = str_replace("\\&gt;",'>',$t);
    $t = str_replace("\\&lt;",'<',$t);
    $t = str_replace("\\&eacute;",'é',$t);
    $t = str_replace("\\&egrave;",'è',$t);
    $t = str_replace("\\&aegrave;",'à',$t);
    $t = str_replace("\\&quot;",'"',$t);
    $t = str_replace("\\_",'_',$t);
    //$t = str_replace("latex\\_",'',$t);
    $t = str_replace("\\&reg;",' \textregistered',$t);
    $t = str_replace("\\&ge;",'≥',$t);
    $t = str_replace("\\&le;",'≤',$t);

    return $t;
}
?>
