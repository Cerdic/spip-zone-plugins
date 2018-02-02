<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;
/*

function filtre_helloworld($v, $add){
    return "Titre:" . $v . ' // Suivi de: ' . $add;
}
*/

function filtre_estUnContenu($input)
{
if(empty($input)) return "";
if(preg_match('/(contenu)([0-9]+)/', $input,$match)) return " ";

return "";
}
function filtre_valeurContenu($input)
{
if(empty($input)) return "0";
if(preg_match('/(contenu)([0-9]+)/', $input,$match)) return $match[2];

return "0";
}

function filtre_estUnConteneur($input)
{
if(empty($input)) return "";
if(preg_match('/(conteneur)([0-9]+)/', $input,$match)) return " ";

return "";
}
function filtre_valeurConteneur($input)
{
if(empty($input)) return "0";
if(preg_match('/(conteneur)([0-9]+)/', $input,$match)) return $match[2];

return "0";
}


function balise_MODALE_APPARAIT($p) {
	$id = interprete_argument_balise (1, $p);
	if(!$id) return $p;
		$str ="'$(\'.ajax-id-myModalContainer'.$id.'\').ajaxReload({args:{modale:\'voir\'}});return false;'";
	
  $p->code = $str;
  return $p;
}


?>