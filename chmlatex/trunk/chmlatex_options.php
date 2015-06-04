<?php
function to_win1252($texte)
{
$texte = @iconv("UTF-8","Windows-1252//TRANSLIT",$texte);
return $texte;
}
/**
 * Conversion des listes SPIP en listes latex avec l'environnement itemize
 * @param $t le texte à traiter
 * @param $type caractère de définition du type de liste et environnement latex correspondant
 * @return le texte traité
 * @author David Dorchies
 * @date 02/06/2015
 */
function latex_liste($t,$type=array('-*','itemize')){
    // On découpe le texte en lignes
    $lignes = explode("\n",$t);
    $bDansListe = false;
    $patsize = strlen($type[0]);
    $ret = array();
    foreach($lignes as $ligne) {
        if(substr($ligne,0,$patsize)==$type[0]) {
            // On est sur un item de liste !
            if(!$bDansListe) {
                // C'est un début de liste
                $ret[]='\begin{'.$type[1].'}';
                $bDansListe = true;
            }
            $ret[] = '\item '.substr($ligne,$patsize);
        }
        else {
            if ($bDansListe && trim($ligne)!='') {
                // On est sorti de la liste
                if(trim(end($ret))=='') {array_pop($ret);}
                $ret[] = '\end{'.$type[1].'}';
                $ret[] = '';
                $bDansListe = false;
            }
            $ret[] = $ligne;
        }
    }
    if($bDansListe) {
        // Il faut fermer la liste
        $ret[] = '\end{'.$type[1].'}';
    }
    return implode("\n",$ret);
}

function propre_exportdoclatex($t)
{

	$t = latex_echappe_equation($t,true);
	//$t = latex_traiter_modeles($t);
	$t = traiter_modeles($t);
	
	
    
	$t = appliquer_regles_wheel($t,array('latex/latex.yaml'));
	$t = latex_echappe_coloration($t);
	$t = echappe_html($t);
	
	$t = echappe_retour(echappe_retour($t),'latex');
	$t = appliquer_regles_wheel($t,array('latex/latex-retour.yaml'));
	$t = preg_replace("#\^#iU",'\$\\hat{~}\$',$t);
    $t = latex_echappe_equation($t,false);
	 
	// On traite les listes à puce et numérotées
    $t = preg_replace('/^-\s+/m','-* ',$t);
    $t = latex_liste($t,array('-*','itemize'));
    $t = latex_liste($t,array('-#','enumerate'));
	
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
	//$t = preg_replace("#<latex\_strike>#iU",'\\sout{',$t);
	$t = preg_replace("#</strike>#iU",'}',$t);
	//$t = preg_replace("#<latex\_table>#iU",' ',$t);
	$t = preg_replace("#<tr>#iU",' ',$t);
	$t = preg_replace("#</td> </tr>#iU",' ',$t);
	$t = preg_replace("#</tr>#iU",' ',$t);
	$t = preg_replace("#<td>#iU",' ',$t);
	$t = preg_replace("#</td>#iU",' ',$t);
	$t = preg_replace("#</table>#iU",' ',$t);
	$t = preg_replace("#<table>#iU",' ',$t);
	$t = preg_replace("#</span>#iU",' ',$t);
	$t = preg_replace("#<span class='(.*)'>#iU",' ',$t);
	
	//$t = preg_match_all("#<span class='(.*)'>#iU",$t,$m);
	//spip_log($m,'m');
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
	//$t = str_replace("\\&\\#039;",'`',$t);
	$t = str_replace("\\&quot;",'"',$t);
	$t = str_replace("\\_",'_',$t);
	//$t = str_replace("latex\\_",'',$t);
	$t = str_replace("\\&reg;",' \textregistered',$t);
	$t = str_replace("\\&ge;",'≥',$t);
	$t = str_replace("\\&le;",'≤',$t);
	//$t = str_replace("<latex\\_table>",'',$t);
	//$t = str_replace("\\pmatrix\\emph{",'\\pmatrix{',$code);
	
	
	
	return $t;
}
?>
