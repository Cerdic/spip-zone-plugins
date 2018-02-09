<?php
define ('XRAY_LAB__AFFICHE_NB_ABSENTDUCONTEXTE', 0);
define ('XRAY_LAB__AFFICHE_NB', true);

// $chemin : une regexp (hors délimiteurs et modificateurs)
function filtre_cache($cle_objet, $id_objet, $chemin='') {

	if (XRAY_LAB__AFFICHE_NB) {
		if ($cle_objet)
			echo "Recherche dans contexte : $cle_objet==$id_objet<br>";
		else 
			echo "Pas de recherche dans le contexte<br>";
		if ($chemin)
			echo "Regexp recherchée dans les chemins : $chemin<br>";
		else
			echo "Pas de recherche dans les chemins<br>";
	}
	include_spip ('lib/microtime.inc');
	microtime_do ('begin');

	$listechemin = $listeobjet = array();
	$nb_valides=0;
	$nb_echecaccesdata=0;
	$nb_absentducontexte=0;
	$nb_accesdata=0;
	
	$cache = apcu_cache_info();
	foreach($cache['cache_list'] as $i => $entry) {
		$k = 'a_'.sprintf('%015d', $entry['creation_time']).$entry['info'];
		$entry ['date_crea'] = date(DATE_FORMAT, $entry['creation_time']);
		$entry ['info_exists'] = apcu_exists ($entry['info']);
		$d = $entry;
		if ($d and apcu_exists($d['info'])
			and ($meta_derniere_modif <= $d['creation_time'])
			) {
			$nb_valides++;
			if (preg_match(",$chemin,i", $d['info'])) {
				$listechemin[]=$d;
				$d['lab_invalide']=true;
			}
			elseif ($cle_objet) {
				if ($data = get_apc_data($entry['info'], $success)) {
					$nb_accesdata++;
					if (is_array($data)) {
						if (isset($data['contexte'])
							and isset ($data['contexte'][$cle_objet])
							and ($data['contexte'][$cle_objet]==$id_objet)) {
								$listeobjet[] = $d;
								$d['lab_invalide']=true;
							}
						else {
							$nb_absentducontexte++;
							if ($nb_absentducontexte < XRAY_LAB__AFFICHE_NB_ABSENTDUCONTEXTE) {
								if (isset($data['texte'])) 
									$data['texte'] = '(vidé)';
								echo "<b>Echec accés contexte</b><pre>".print_r($data, 1)."<pre><br>";
							}
						};
					}
				}
				else 
					$nb_echecaccesdata++;
			}
		}
	}
	if (defined('XRAY_LAB__AFFICHE_NB') and XRAY_LAB__AFFICHE_NB) {
		echo "<h3>Stats</h3>
		<pre>
			nb valides : $nb_valides
			nb echec accés data : $nb_echecaccesdata
			nb acces data : $nb_accesdata
			nb absent du contexte : $nb_absentducontexte
			time : ".microtime_do ('end')
		."</pre>";
	}
	return array($listeobjet, $listechemin);
}

$cle_objet='id_'.XRAY_OBJET_SPECIAL;

if (isset ($_GET[$cle_objet]))
	$id_objet = $_GET[$cle_objet];
else 
	$id_objet = XRAY_ID_OBJET_SPECIAL;
if (isset ($_GET['chemin']))
	$chemin = $_GET['chemin'];
else 
	$chemin = 'admin';
	
// list ($listeobjet, $listechemin) = filtre_cache('id_annonce', $id_annonce, $chemin);
list ($listeobjet, $listechemin) = filtre_cache('', $id_annonce, $chemin);

echo "<b>trouvé le chemin ".count($listechemin)."<br>trouvé l'objet :".count($listeobjet)."</b><br>";

echo "<h3>CHEMIN :".count($listechemin)."</h3><pre>".
	print_r($listechemin,1)
	."</pre><br>
	<h3>Objets annonce $id_annonce :".count($listeobjet)."</h3>
	<pre>".
	print_r($listeobjet,1)
	."</pre>";

?>

