<?php
/* Fonction permettant de stocker en meta une info transmisse par POST*/
function setVariable($v) {
  ecrire_meta('fenetresflottantes_'.$v, $_POST[$v]);
}

function fenetresflo_form_reg1($parm='')
{
  global $spip_lang_right;

  $out = "";
  $url = self();


  if(isset($_POST["sauve_reg1"])) {
    // Modification de la configuration
    setVariable("reglage1_hauteur");
    setVariable("reglage1_largeur");
    setVariable("reglage1_posx");
    setVariable("reglage1_posy");
	setVariable("reglage1_couleur");
setVariable("reglage1_couleurbordure");

    ecrire_metas();
  }


 //Récupération de la configuration pour l'affichage
  if($freg1_hauteur =="")
  {
	$freg1_hauteur = "300";
  }
  else
 {
	$freg1_hauteur = $GLOBALS['meta']['fenetresflottantes_reglage1_hauteur'];
 }
 if($freg1_largeur =="")
 {
	$freg1_largeur = "400";
 }
 else
 {
	$freg1_largeur = $GLOBALS['meta']['fenetresflottantes_reglage1_largeur'];
 }
 if($freg1_posx =="")
 {
	$freg1_posx = "100";
 }
 else
 {
	$freg1_posx = $GLOBALS['meta']['fenetresflottantes_reglage1_posx'];
 }
 if($freg1_posy =="")
 {
	$freg1_posy = "200";
 } 
  else
 {
	$freg1_posy = $GLOBALS['meta']['fenetresflottantes_reglage1_posy'];
 }

if($freg1_couleur =="")
 {
	$freg1_couleur = "vert";
 } 
  else
 {
	$freg1_couleur = $GLOBALS['meta']['fenetresflottantes_reglage1_couleur'];
 }
if($freg1_couleurbordure =="")
 {
	$freg1_couleurbordure = "#6caf00";
 } 
  else
 {
	$freg1_couleurbordure = $GLOBALS['meta']['fenetresflottantes_reglage1_couleurbordure'];
 }

  

  //Affichage
  //Formulaire
  $out .=  "<form name='dimenssion fenetre' action='$url' method='post'>\n";

  //DESCRIPTION
  $out .="<p>"._T('fenetresflottantes:reglage1_description')."<br /><br />\n";

  // HAUTEUR
  $out .=  "<p>"._T('fenetresflottantes:reglage1_hauteur')."<br />\n";
  $out .=  "<input type='text' name='reglage1_hauteur' value=\"$freg1_hauteur\" class='formo' />";
  $out .=  "</p>\n";

  // LARGEUR
  $out .=  "<p>"._T('fenetresflottantes:reglage1_largeur')."<br />\n";
  $out .=  "<input type='text' name='reglage1_largeur' value=\"$freg1_largeur\" class='formo' />";
  $out .=  "</p>\n";
 
   // POSITION EN X
  $out .=  "<p>"._T('fenetresflottantes:reglage1_posx')."<br />\n";
  $out .=  "<input type='text' name='reglage1_posx' value=\"$freg1_posx\" class='formo' />";
  $out .=  "</p>\n";

   // POSITION EN Y
  $out .=  "<p>"._T('fenetresflottantes:reglage1_posy')."<br />\n";
  $out .=  "<input type='text' name='reglage1_posy' value=\"$freg1_posy\" class='formo' />";
  $out .=  "</p>\n";

  // COULEUR
  $out .=  "<p>"._T('fenetresflottantes:reglage1_couleur')."<br />\n";
  $out .=  "<input type='text' name='reglage1_couleur' value=\"$freg1_couleur\" class='formo' />";
  $out .=  "</p>\n";

// COULEUR BORDURE
  $out .=  "<p>"._T('fenetresflottantes:reglage1_couleurbordure')."<br />\n";
  $out .=  "<input type='text' name='reglage1_couleurbordure' value=\"$freg1_couleurbordure\" class='formo' />";
  $out .=  "</p>\n";


  //SUBMIT
  $out .=  "<div class='edition-bouton'>";
  $out .=  "<div style='text-align:$spip_lang_right'><input type='submit' name='sauve_reg1' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
  $out .=  "</div>\n";

  $out .=  "</form>\n";
 
  return $out;
}

function fenetresflo_form_reg2($parm='')
{

  global $spip_lang_right;

  $out = "";
  $url = self();


  if(isset($_POST["sauve_reg2"])) {
    // Modification de la configuration
 
    setVariable("reglage2_attribut");


    ecrire_metas();

  }


 //Récupération de la configuration pour l'affichage
 
       

	$freg2_attribut = $GLOBALS['meta']['fenetresflottantes_reglage2_attribut'];


  //Affichage
  //---------
  //Formulaire
  $out .=  "<form name='dimenssion fenetre' action='$url' method='post'>\n";

  //DESCRIPTION
  $out .="<p>"._T('fenetresflottantes:reglage2_description')."<br /><br />\n";

  // ATTRIBUT
  $out .=  "<p>"._T('fenetresflottantes:reglage2_attribut')."<br />\n";
  $out .=  "<input type='text' name='reglage2_attribut' value=\"$freg2_attribut\" class='formo' />";
  $out .=  "</p>\n";

  //SUBMIT
  $out .=  "<div class='edition-bouton'>";
  $out .=  "<div style='text-align:$spip_lang_right'><input type='submit' name='sauve_reg2' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
  $out .=  "</div>\n";

  $out .=  "</form>\n";

  return $out;


}

?>
