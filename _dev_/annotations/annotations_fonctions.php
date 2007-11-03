<?php
//
// #URL_ACTION_AUTEUR{converser,arg,redirect} -> ecrire/?action=converser&arg=arg&hash=xxx&redirect=redirect
//
// http://doc.spip.org/@balise_URL_ACTION_AUTEUR_dist
function balise_JS_ACTION_AUTEUR($p) {

	$p->code = interprete_argument_balise(1,$p);
	$args = interprete_argument_balise(2,$p);
	if ($args != "''" && $args!==NULL)
		$p->code .= ".'\",\"'.".$args;

	$p->code = "'<"."?php echo securiser_action_auteur_js(\"'." . $p->code . ".'\"); ?>'";

	$p->interdire_scripts = false;
	return $p;
}

function securiser_action_auteur_js($action,$arg) {
	include_spip("inc/securiser_action");
	static $id_auteur=0, $pass;
	if (!$id_auteur) {
		list($id_auteur, $pass) =  caracteriser_auteur();
	}
	$hash = _action_auteur("$action-$arg", $id_auteur, $pass, 'alea_ephemere');
	return "{
		\"action\": \"$action\",
		\"hash\": \"$hash\",
		\"arg\": \"$arg\"
	}";
}

function csv2json($url_document) {
	$json = "";
	$data = array();
	$handle = fopen($url_document, 'r');
	if ($handle)
	{
    //the top line is the field names
    $fields = fgetcsv($handle, 4096, ';');
    //loop through one row at a time
    while (($row = fgetcsv($handle, 4096, ';')) !== FALSE)
        $data[] = $row;

    fclose($handle);
	}
	
  $json = "{\n".
	"\"path\":"._q($url_document).",\n".
	"\"count\":".count($data).",\n". 
	"\"fields\": [\n\t".
	join(",\n\t",array_map(_q_json,$fields)).
	"\n],\n".
	"\"rows\": [\n";
	foreach($data as $row) {
		$json_row[] = "\t[\n\t\t".
		join(",\n\t\t",array_map(_q_json,$row)).
		"\n\t]";		
	}
	$json .= join(",\n",$json_row); 	
  $json .= "]\n".
	"}";
	return $json;
} 

function _q_json($text) {
	return '"' . str_replace('&', '\x26', addcslashes($text, "\"\\\n\r")) . '"';
}

?>
