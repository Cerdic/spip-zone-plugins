<?

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_centre_image_forcer() {
		$md5 = md5($_GET["url"]);
		$forcer = sous_repertoire(_DIR_IMG, "cache-centre-image");
		
		$fichier_json = "$forcer$md5.json";
		$res = array("x" => $_GET["x"], "y" => $_GET["y"]);
		
		file_put_contents($fichier_json, json_encode($res,TRUE));
}