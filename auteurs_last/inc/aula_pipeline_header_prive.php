<?php 

	// inc/aula_pipeline_insert_head.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of AuLa.
	
	AuLa is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	AuLa is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with AuLa; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de AuLa. 
	
	AuLa est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	AuLa est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion du résultat dans le head de la page
 * des auteurs, en espace privé.
 * @return string
 */
function aula_header_prive ($flux) {
	global $connect_toutes_rubriques;

	$exec = _request('exec');
	
	if(
		($exec == 'auteurs')
		&& $connect_toutes_rubriques
		) {

		$script = "";
		
		if($ii = aula_last_array()) {
			foreach(aula_last_array() as $key => $value) {
				$script .= $key.":"
					.
						(
							(is_bool($value)) 
							?	(
									($value)
									? 'true' // actuellement connecté
									: 'false' // ne s'est jamais connecté
								)
							:	"'".affdate_jourcourt($value)." ".heures($value).":".minutes($value)."'"
						)
					.",";
			}
			$script = rtrim($script, ",");
		}
		
		// 
		$script = "var aula_auteurs={".$script."};";
		$script = "

<!-- aula -->
<style type='text/css'>
<!--
.aula-b {
	border-top: 1px solid #ccc;
}
-->
</style>
<script language='JavaScript' type='text/javascript'>
$script
var aula_class = 'class=\'aula-b\'';
function aula_completer () {
	$('#auteurs table tr').each(function(i){
		if(!i) v = '"._T(_AULA_PREFIX.':connexion')."';
		else {
			v = $(this).find('td.verdana1 a').attr('href');
			var e = /(id_auteur=)([0-9]+)/;
			e.exec(v);
			v = RegExp.$2;
			var c;
			if(aula_auteurs[v]) {
				v = aula_auteurs[v];
				if(v.length > 5) { 
					v = '<span class=\'arial1\'>' + v + '<\/span>'; 
				}
				else { 
					v = $(this).find('img').attr('src'); 
					v = '<img src=\'' + v + '\' title=\'".($ii = trim(_T(_AULA_PREFIX.':actuellement_connecte'),":"))."\' =alt=\'".$ii."\' \/>';
				}
				c = aula_class;
			}
			else if(v >= 1) {
				v = '<span class=\'arial1\' title=\'"._T(_AULA_PREFIX.':jamais_connecte')."\'>?<\/span>';
				c = aula_class;
			}
			else {
				c = v = '';
			}
		}
		$(this).append('<td ' + c + '>' + v + '</td>');
	 });
}
jQuery().ready(function(){
	aula_completer (); "
		/* supprime les appels ajax de la boite des auteurs */
	. "
	$('#auteurs td.arial1 a').removeAttr('onclick');
	$('#auteurs td.arial11 a').removeAttr('onclick');
	$('#auteurs #bas a').removeAttr('onclick');
});
</script>
<!-- /aula -->
		";
		
	// compression du script
	$script = preg_replace("=[[:space:]]+=", " ", $script);

	$flux .= $script;
	
	}
	
	return ($flux);
	
} // end aula_insert_head()

/**
 * Donne les dernières connexions.
 * Tableau dont l'index est l'id de l'auteur, la valeur :
 * - bool TRUE si l'auteur est en ligne, ou
 * - bool FALSE si jamais connecté, ou
 * - la date de dernière connexion
 * @return bool|array
 */
function aula_last_array () {
	include_spip('inc/filtres');
	
	$where = ($ii = _request('statut')) ? "statut="._q($ii) : "statut='0minirezo' OR statut='1comite'";

	//$current_date = date("Y-m-d H:i:s");
	// mieux vaut demander l'heure de la base (si distante ?)
	$current_date = spip_fetch_array(spip_query("SELECT NOW()"));
	$current_date = $current_date['NOW()'];

	// récupère les heures de connexion
	$sql_query = "SELECT id_auteur,maj,en_ligne FROM spip_auteurs WHERE $where";
	$sql_result = spip_query($sql_query);
	
	while($row = spip_fetch_array($sql_result)) {
		$en_ligne = vider_date($row['en_ligne']);
		$maj = vider_date($row['maj']);

		$result[$row['id_auteur']] = 
			(!empty($en_ligne))
			?	(
				(aula_last_interval($current_date, $en_ligne))
					? true // connexion en cours
					// date dernière connexion
					: (($maj > $en_ligne) ? $maj : $en_ligne)
				) 
			: false; // ne s'est pas encore connecté
	}
	return($result);
}

/**
 * Calcule de l'intervalle
 * @return int
 */
function aula_last_interval ($current_date, $en_ligne, $interval_minutes = _AULA_DELAY_MINUTES) {
	$en_ligne = strtotime($en_ligne);
	$current_date = strtotime($current_date);
	return($en_ligne > ($current_date - $interval_minutes) && $en_ligne < ($current_date + $interval_minutes));
}

