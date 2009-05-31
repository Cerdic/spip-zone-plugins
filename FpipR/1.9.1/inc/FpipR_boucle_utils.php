<?php

function FpipR_utils_search_order(&$boucle,$possible_sort) {
  $arguments = array();
  if(is_array($boucle->order)) {
	for($i=0;$i<count($boucle->order);$i++) {
	  list($sort,$desc) = split(' . ',str_replace("'",'',$boucle->order[$i]));
	  if(in_array($sort,$possible_sort)) {
		$sort = str_replace('_','-',$sort);
		if($sort != 'relevance' && isset($desc)) 
		  $sort .= '-desc';
		else
		  $sort .= '-asc';
		$boucle->order[$i] = "'fpipr_photos.rang'";
		$arguments['sort'] = "'".$sort."'";
	  }
	}
  }
  return $arguments;
  }

function FpipR_utils_search_args_extras($boucle,$id_table,$possible_args,$possible_extras) {
  $arguments = array();
  $extras = array();
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	if (in_array($key,$possible_args)){
	  switch($w[0]) {
		case "'='":
		  if($key == 'taken_date' || $key == 'upload_date') {
			$arguments['min_'.$key] = $val;
			$arguments['max_'.$key] = $val;
		  } else {
			$arguments[$key] = $val;
		  }
		  break;
		case "'<'":
		  if($key == 'taken_date' || $key == 'upload_date') {
			$arguments['max_'.$key] = $val;
		  }
		  break;
		case "'>'":
		  if($key == 'taken_date' || $key == 'upload_date') {
			$arguments['min_'.$key] = $val;
		  }
		  break;
		default:
		  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
	  }
	}
  }
  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";
  return $arguments;
}

function FpipR_utils_calculer_hash($method, $arguments, &$boucle) {
  $hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  if($boucle) $hash .=   FpipR_utils_calcul_limit($boucle,$arguments);
  foreach($arguments as $key => $val) {
	if($val) {
	  $hash .= "\$v=$val;\n";
	  $hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}
  if($boucle && $boucle->id_boucle) $hash .= '$Numrows[\''.$boucle->id_boucle.'\']["fpipr_grand_total"] = ';
  $hash .= "FpipR_fill_table_boucle('$method',\$arguments);";
  return $hash;
}

function FpipR_utils_search_args($boucle,$id_table,$possible_args) {
  $arguments = array();
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] = "'='" && in_array($key,$possible_args)){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  return $arguments;
}

function FpipR_utils_search_extras($boucle,$id_table,$possible_extras) {
  $arguments = array();
  $extras = array();
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
  }


  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";
  return $arguments;
}

function FpipR_utils_calcul_limit(&$boucle) {
  //on calcul le nombre de page d'apres {0,10}
  $deubt=0;$pas=1;
  if($boucle->limit) {
	list($debut,$pas) = split(',',$boucle->limit);
	$boucle->limit = ($debut%$pas).','.$pas;
  } else {
	$debut = $boucle->partie;
	$pas = $boucle->total_parties;
	$boucle->partie = "($debut)%($pas)";
  }
  if($debut && $pas) {
	return "list(\$page,\$per_page) = FpipR_calcul_argument_page($debut,$pas);
\$arguments['page'] = \$page;
\$arguments['per_page'] = \$per_page;
";
  }
else return '';
}


function FpipR_utils_search_criteres(&$boucle,$possible_criteres,&$boucles,$id_boucle) {
  $arguments = array();
  foreach($boucle->criteres as $crit) {
	if (in_array($crit->op,$possible_criteres)){
                 $c = array();
                 foreach($crit->param as $p)
                   $c[] = calculer_liste($p, array(), $boucles, $boucles[$id_boucle]->id_parent);
		$val = join($c,".','.");
	  $arguments[$crit->op] = $val;
	}
  }
  return $arguments;
}



?>
