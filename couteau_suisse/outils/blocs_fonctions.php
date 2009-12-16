<?php
	
/* Trois ou quatre balises pour creer des blocs depliables : 

#BLOC_TITRE{fragment}     / fragment si ajax, pas de fragment sinon /
Mon titre
#BLOC_RESUME              / facultatif /
Mon resume qui disparait si on clique
#BLOC_DEBUT
Mon bloc depliable        / qui est aussi l'emplacement pour l'Ajax si le fragment est donne /
#BLOC_FIN

*/

@define('_BLOC_TITRE_H', 'h4');

// Un bloc replie, titre numerote : #BLOC_TITRE_NUM{numero} ou #BLOC_TITRE_NUM{numero, fragment}
function balise_BLOC_TITRE_NUM($p) {
	$p->code = blocs_balise_titre(interprete_argument_balise(2, $p), interprete_argument_balise(1, $p));
	$p->interdire_scripts = false;
	return $p;
}
// Un bloc deplie, titre numerote : #BLOC_VISIBLE_TITRE_NUM{numero} ou #BLOC_VISIBLE_TITRE_NUM{numero, fragment}
function balise_BLOC_VISIBLE_TITRE_NUM($p) {
	$p->code = blocs_balise_titre(interprete_argument_balise(2, $p), interprete_argument_balise(1, $p), -1);
	$p->interdire_scripts = false;
	return $p;
}

// Un bloc replie, titre simple : #BLOC_TITRE ou #BLOC_TITRE{fragment}
function balise_BLOC_TITRE($p) {
	$p->code = blocs_balise_titre(interprete_argument_balise(1, $p));
	$p->interdire_scripts = false;
	return $p;
}
// Un bloc deplie, titre simple : #BLOC_VISIBLE_TITRE ou #BLOC_VISIBLE_TITRE{fragment}
function balise_BLOC_VISIBLE_TITRE($p) {
	$p->code = blocs_balise_titre(interprete_argument_balise(1, $p), NULL, -1);
	$p->interdire_scripts = false;
	return $p;
}

function balise_BLOC_RESUME($p) {
	global $bloc_stade;
	$k=count($bloc_stade)-1;
	$stade=abs($bloc_stade[$k]);
	$class=$bloc_stade[$k]>0?'':' blocs_invisible blocs_slide';
	
	if($stade<1 || $stade>2)
		die("erreur de compilation #BLOC_RESUME sans #BLOC_TITRE_($stade)");
	$p->code = "'</a></"._BLOC_TITRE_H."><div class=\"blocs_resume$class\">'";
	$bloc_stade[$k]=$bloc_stade[$k]>0?3:-3;	/* 3 = resume */
	$p->interdire_scripts = false;
	return $p;
}

function balise_BLOC_DEBUT($p) {
	global $bloc_stade;
	$k=count($bloc_stade)-1;
	$stade=abs($bloc_stade[$k]);
	$class=$bloc_stade[$k]<0?'':' blocs_invisible blocs_slide';

	if($stade == 3)	/* on arrive du resume, fermer la div resume seulement */
		$p->code = "'</div><div class=\"blocs_destination$class\">'";
	else	{
		if($stade<1 || $stade>2)
					/* on DOIT arriver de titre */
			die("erreur de compilation #BLOC_DEBUT sans #BLOC_TITRE_($stade)");
		$p->code = "'</a></"._BLOC_TITRE_H."><div class=\"blocs_destination$class\">'";
		}
	$bloc_stade[$k]=$bloc_stade[$k]>0?4:-4; /* 4=debut */
	$p->interdire_scripts = false;
	return $p;
}

function balise_BLOC_FIN($p) {
	global $bloc_stade;
	$k=abs(array_pop($bloc_stade));
	if($k!=4)	
		die("erreur de compilation #BLOC_FIN sans #BLOC_DEBUT_($k)");

	$p->code = "'</div></div>'";
	$p->interdire_scripts = false;
	return $p;
}

// Renvoie un code JQuery pour deplier un bloc au chargement de la page.
// Exemple pour deplier le 5eme bloc : #BLOC_DEPLIER{4} (l'index commence a zero)
function balise_BLOC_DEPLIER($p) {
	$eq = interprete_argument_balise(1, $p);
	$p->code = "bloc_deplier_script(intval($eq))";
	$p->interdire_scripts = false;
	return $p;
}
// Renvoie un code JQuery pour deplier un bloc numerote au chargement de la page.
// Exemple pour deplier le bloc .cs_bloc4 : #BLOC_DEPLIER_NUM{4}
function balise_BLOC_DEPLIER_NUM($p) {
	$eq = interprete_argument_balise(1, $p);
	$p->code = "bloc_num_deplier_script(intval($eq))";
	$p->interdire_scripts = false;
	return $p;
}

// Renvoie un code JQuery pour courcuiter la variable cnfigurant les blocs uniques
// Argument : oui/non ou 0/1
function balise_BLOC_UNIQUE($p) {
	$arg = interprete_argument_balise(1, $p);
	$p->code = "bloc_unique_script($arg)";
	$p->interdire_scripts = false;
	return $p;
}

// fonction (SPIP>=2.0) pour le calcul de la balise #BLOC_DEPLIER
function bloc_deplier_script($num=0) {
	return $num<0?'':http_script("jQuery(document).ready(function() { jQuery('"._BLOC_TITRE_H.".blocs_titre').eq($num).click(); });");
}
// fonction (SPIP>=2.0) pour le calcul de la balise #BLOC_DEPLIER_NUM
function bloc_num_deplier_script($num=-1) {
	return $num<0?'':http_script("jQuery(document).ready(function() { jQuery('div.cs_bloc$num').children('.blocs_titre').eq(0).click(); });");
}
// fonction (SPIP>=2.0) pour le calcul de la balise #BLOC_UNIQUE
function bloc_unique_script($num=1) {
	$num = ($num==='oui' || intval($num)>0)?1:0;
	return http_script("var blocs_replier_tout = $num;");
}

// fonction pour le calcul de la balise #BLOC_TITRE_NUM
function bloc_is_num($num) {
	 return preg_match(',^\s*(\d+)\s*$,', $num, $regs)?" cs_bloc$regs[1]":"''";
}

// fonction pour le calcul des 4 balises de type #BLOC_TITRE
function blocs_balise_titre($fragment, $numero=NULL, $replie=1) {
	// statut binaire : bit1=ajax bit2=titre bit3=resume bit4=debut
	global  $bloc_stade; /* 2 = ajax; 1 = titre pas d'ajax; idem negatif = bloc visible */
	if ($fragment!==NULL){
		$ajax=' blocs_ajax ';
		$bloc_stade[]=2*$replie;
	} else {
		$fragment="'javascript:;'";
		$ajax="";
		$bloc_stade[]=1*$replie;
	}
	$numero = $numero===NULL?"''":"bloc_is_num($numero)";
	$replie = $replie>0?' blocs_replie':'';
 	return " '<div class=\"cs_blocs'.$numero.'\"><"._BLOC_TITRE_H." class=\"blocs_titre$replie $ajax\"><a href=\"'.".$fragment.".'\">' ";
}

?>