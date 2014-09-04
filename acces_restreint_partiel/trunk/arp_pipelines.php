<?php
/**
 * Utilisations de pipelines par Accès Restreint Partiel
 *
 * @plugin     Accès Restreint Partiel
 * @copyright  2014
 * @author     Bruno Caillard
 * @licence    GNU/GPL
 * @package    SPIP\Arp\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


if (!function_exists('critere_tout_voir_dist')){
function critere_tout_voir_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->modificateur['tout_voir'] = true;
}
}

/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */
function arp_pre_boucle(&$boucle){
//	return $boucle;

//include_once '/home/spip3/public_html/krumo/class.krumo.php';

	switch ($boucle->type_requete){
		case 'hierarchie':
		case 'articles':
		//case 'breves':
		case 'rubriques':
		case 'documents':
//krumo($boucle);
//			echo "<br>arp_pre_boucle:".$boucle->type_requete;
			$boucle->modificateur['tout_voir'] = true;

			// le plugin AccesRestreint a t'il ajouté des conditions dans le where ?
			// C'est le cas où AccesRestrient est appelé avant arp.
			//---------------------------------------------------
			foreach($boucle->where as $key => $where)
			{
//echo "<br>arp_pre_boucle : boucle=".$boucle->type_requete." : key=$key : where=".$where;
				if (is_string($where))
				{
					if (strstr($where, 'accesrestreint')) // Y'a du code de accesretreint dans le where ?
					{
						unset($boucle->where[$key]); // On détruit la condition ajoutée par accesrestreint
					}
				}
			}

			// Le plugin AccesRestreint a t'il ajouté un hash ?
			//-----------------------------------
			//if (strstr($boucle->hash, 'accesrestreint')) $boucle->hash = ''; // supprimer le hash
			
//krumo($boucle);
			break;
	}

	if ($boucle->type_requete == 'articles')
	{
//krumo($boucle);
		$boucle->modificateur['tout_voir'] = true;
//krumo($boucle);
	}
	return $boucle;

}

/* Pipeline : declarer_tables_interfaces
Insérer le filtre de arp dans les traitements effectué sur #TEXTE, pour la boucle ARTICLES, avant propre
*/
function arp_declarer_tables_interfaces($interface){
//include_once '/home/spip3/public_html/krumo/class.krumo.php';
	// tafbt : traitement à faire sur la balise texte
	if (isset($interface['table_des_traitements']['TEXTE']['articles'])) $tafbt = $interface['table_des_traitements']['TEXTE']['articles']; // déjà défini pour articles ?
	else $tafbt = $interface['table_des_traitements']['TEXTE'][0]; // si pas défini par défaut, alors on prend le traitement par défaut
	$tafbt = str_replace('%s', 'arp_filtrage(%s, $connect, $Pile[0])', $tafbt);
//	$tafbt = 'arp_filtrage(%s, $connect, $Pile[0])'; 
	$interface['table_des_traitements']['TEXTE']['articles'] = $tafbt;

//	krumo($interface);

	return $interface;

	}


/* Pipeline : insert_head_css
Insérer le filtre le fichier css dans le header, avec les autres
*/
function arp_insert_head_css($flux)
{
	$css = find_in_path('css/arp.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}

?>