<?php

/* Balise #CALENDRIER_MINI
   Auteur James (c) 2006
   Plugin pour spip 1.9
   Licence GNU/GPL
*/

function balise_DATE_ARCHIVES($p) {
	$p->code = "_request('".VAR_DATE."')";

	#$p->interdire_scripts = true;
	return $p;
}

function critere_archives($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
 $champ_date = "'" . $boucle->id_table ."." .
  $GLOBALS['table_date'][$boucle->type_requete] . "'";
 $boucle->where[] = array(
  'REGEXP',
  $champ_date, 
  "spip_abstract_quote(('^' . interdire_scripts(entites_html(\$Pile[0]['".VAR_DATE."']))))"
 );
 
}

function thead($lang){
	$ret = '';
	$debut = 2;
	if($lang == 'en') $debut = 1;
	for($i=0;$i<7;$i++) {
		$ret .= "\n\t\t\t\t".'<th scope="col"><abbr title="'._T('date_jour_'.$debut).'">' .
		_T('minical:date_jour_abbr_'.$debut) . '</abbr></th>';
		$debut = $debut == 7 ? 1 : $debut+1;
	}
	return "\n\t\t".'<thead>
			<tr>' .$ret. '
			</tr>
		</thead>'."\n";
}

?>