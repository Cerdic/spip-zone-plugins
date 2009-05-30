<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function preparar_baliza_cambiar_esqueleto() {

global $dossier_squelettes;

// Revisa si tiene que a–adir ? o &

$enlace = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 if(ereg("\?",$enlace)){
	$enlace=$enlace."&";
	}
	else{
	$enlace=$enlace."?";
	}
$partes=explode("esqueleto",$enlace);
$enlace=$partes[0];
$enlace .= "esqueleto=";

// Guarda en una cookie el esqueleto seleccionado por un a–nho o lo utiliza si existe
// Comprueba que exista y si no deja el "predeterminada"

// PREPARAMOS EL MENU DESPLEGABLE

//comprobamos que exista el directorio "dist"
if (file_exists("dist")){
		if (!is_file("dist")) {
			$lista["dist"]="dist";
		}			
}

//comprobamos que exista el directorio "squelettes"
if (file_exists("squelettes")){
		if (!is_file("squelettes")) {
			$lista["squelettes"]="squelettes";
		}			
}

//comprobamos que exista el directorio "esqueletos"
if (file_exists("esqueletos")){
//definimos el path de acceso
$path = "esqueletos";
//abrimos el directorio
$dir = opendir($path);
while (false !== ($esqueleto = readdir($dir))) {
		if (!is_file($esqueleto) and $esqueleto!="." and $esqueleto!="..") {
			$lista[strtolower($esqueleto)]=$esqueleto;
		}			
}
//Cerramos el directorio
closedir($dir);
}

//comprobamos que exista el directorio "squelettes-test"
if (file_exists("squelettes-test")){
//definimos el path de acceso
$path = "squelettes";
//abrimos el directorio
$dir = opendir($path);
while (false !== ($esqueleto = readdir($dir))) {
		if (!is_file($esqueleto) and $esqueleto!="." and $esqueleto!="..") {
			$lista[strtolower($esqueleto)]=$esqueleto;
		}			
}
//Cerramos el directorio
closedir($dir);
}

//comprobamos que exista el directorio "themes"
if (file_exists("themes")){
//definimos el path de acceso
$path = "squelettes";
//abrimos el directorio
$dir = opendir($path);
while (false !== ($esqueleto = readdir($dir))) {
		if (!is_file($esqueleto) and $esqueleto!="." and $esqueleto!="..") {
			$lista[strtolower($esqueleto)]=$esqueleto;
		}			
}
//Cerramos el directorio
closedir($dir);
}

//Si existe una lista de esquletos la ordenamos y la mostramos

if (isset($lista)){

ksort($lista);

$texto= "<div style='width:100%; margin:auto'>";
$texto .= "\n<form method='get' action='ver_esqueletos.php' style=\"display:inline;font-size:85%\">";
$texto .= "\n<select name='select' onChange='if (options[selectedIndex].value) { location = options[selectedIndex].value; }' style=\"width:100%;border:1px solid gray;background-color:white;color:green;font-size:85%\">";
$texto .= "\n<option selected style=\"padding-left:.4em;border-bottom: 1px solid silver;color:silver;\">Cambiar visualizaci&oacute;n</option>";

foreach ($lista as $esqueleto) {

			$enlace_esqueleto= "esqueletos/".$esqueleto;
 					if ($enlace_esqueleto==$dossier_squelettes){
$texto .="\n<option value='$enlace$esqueleto' style=\"padding-left:.4em;border-bottom: 1px solid silver;background-color:gray;color:orange\">&bull; ".$esqueleto."</option>";
					}
					else {
$texto .="\n<option value='$enlace$esqueleto' style=\"padding-left:1.2em;border-bottom: 1px solid silver;background-color:#FFFFE0;color:#4682B4\">".$esqueleto."</option>";
					}
			}

$texto .="\n</select>\n</form>\n</div>";

return $texto;

}

}

function balise_CAMBIAR_ESQUELETO($p) {

   $p->code ="preparar_baliza_cambiar_esqueleto()";
   $p->statut = 'html';
   return $p;
}

?>