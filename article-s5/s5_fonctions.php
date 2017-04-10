<?php
/* ========================================================================
 *
 *   +----------------------------------+
 *    Nom du Filtre :    creer_slides
 *   +----------------------------------+
 *    Date : mercredi 20/01/2006 19:42:20
 * Pour l'adaptation du syst�me d'eric Meyer http://www.meyerweb.com
 * les slides sont les paragraphes contenus entre deux intertitres
 * on utilise en partie le travail de 
	*    Date : mercredi 27 juillet 2005
	*    Auteurs :
	*		St�phane Deschamps https://contrib.spip.net/auteur.php3?id_auteur=327
	*		Yann Ducrocq https://contrib.spip.net/auteur.php3?id_auteur=1833
	*   +-------------------------------------+
	*    donner un identifiant unique � chaque intertitre de la page
	*   +-------------------------------------+
 * auteur cogefip le 20/01/2006 18:46:38
 * ATTENTION si le texte ne commence pas par un intertitre,
 * le texte du d�but jusqu'au premier intertitre ne sera pas diffus�
 * si le texte ne comporte aucun intertitre l'int�gralit� du texte forme le slide
 ========================================================================== */

/*
Pour m�moire d�fnition des intertitre
// $GLOBALS['debut_intertitre'] = "\n<h3 class=\"typo_intertitre\">";
// $GLOBALS['fin_intertitre'] = "</h3>\n";
*/
// filtre � appliquer sur #TEXTE
// [(#TEXTE|creer_slides)]

$cId =0;

function creer_slides($str) {
  global $cId;

   $cId=0;

	$reg_intertitre = "/<h3[^>]*>(.*?)<\/h3>/i";
	//Il conviendrait de r�cup�rer le contenu de $GLOBALS['debut_intertitre'] et $GLOBALS['fin_intertitre']
	//?? $reg_intertitre = "/" . addslashes($GLOBALS['debut_intertitre']) . "(.*?)" . addslashes($GLOBALS['fin_intertitre']) ."/i";
	
	// appel de la fonction de remplacement par callback
	$str = preg_replace_callback($reg_intertitre,'transforme_intertitre',$str);
	if ($cId==0) { return '<div class="slide"><div class="slidecontent">' .$str. '</div></div>';};  // aucune chaine d'intertitre n'a �t� trouv�e
	// le texte est maintenant non appari�,
  // placer � la fin les div fermant
      $str = $str . '</div></div>';
  return $str;
}

function transforme_intertitre($trouve) {
  global $cId;

  $cgfp_avant = "\n".'<div class="slide">'."\n\t<h1>";
  //il faut fermer le pr�c�dent slide si n�cessaire
  if ($cId>0) {$cgfp_avant = "\n\t</div><!-- slidecontent -->\n</div><!-- slide -->".$cgfp_avant;};
	// incrementation du compteur global
	$cId++;

  return $cgfp_avant . $trouve[1] . '</h1>'."\n\t".'<div class="slidecontent">';
}

function creer_slides_incremental($str) {

  $str=creer_slides($str);
  $str=preg_replace_callback('/<li[^>]*>(.*?)/i', 'remplace_li', $str);
  $str=preg_replace('/<\/ul>/i', "\n\t\t</ul>\n", $str);
  // les ul vont recevoir une class incremental d�fini dans les css de s5
  return preg_replace_callback('/<ul[^>]*>(.*?)/i', 'remplace_ul', $str);
}

function remplace_li($trouve) {
  return "\n\t\t\t".'<li>' . $trouve[1]; // on supprime les attributs de <li> dont on a pas besoin ici (pr�paration � pr�sentacular � venir)
}

function remplace_ul($trouve) {
  return "\n\t\t".'<ul class="incremental">' . $trouve[1];
}
?>