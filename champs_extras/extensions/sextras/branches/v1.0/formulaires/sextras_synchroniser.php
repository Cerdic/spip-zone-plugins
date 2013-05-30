<?php

define(CONNECTION_REF, 'sextras_reference'); // nom du fichier contenant les paramètres de connexion à la base de référence
// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_sextras_synchroniser_charger()
{
	$t_objets = array('articles', 'rubriques', 'auteurs', 'breves', 'documents', 'groupe_mots', 'mots', 'syndic', 'messages');

	$datas = array();
	$defaut = array();
	$erreur = "";
	$ok = _T('sextras:ok');


	// Détermine si la définition de l'objet est plus récente dans la base de référence. Si oui, précoche le checkbox correspondant
	include_spip('base/connect_sql');
	$serveur = spip_connect(CONNECTION_REF);
	if (!$serveur) $erreur = _T('sextras:erreur_conbase');
	else
	{
		// La connexion a réussi.
		foreach ($t_objets as $key=>$objet) // Pour chaque objet éditoriaux
		{

			$nom_champs = 'champs_extras_spip_'.$objet;

			// on lit la date de mise à jour de la déf des champs extra dans la base courante
			$result = sql_select(array('maj'), array('spip_meta'), array("nom='$nom_champs'"));
			$row = sql_fetch($result);
			$majc = $row['maj'];

			// on lit la date de mise à jour de la déf des champs extra dans la base de référence
			$result = sql_select(array('maj'), array('spip_meta'), array("nom='$nom_champs'"), '', '', '', '', CONNECTION_REF);
			$row = sql_fetch($result);
			$majr = $row['maj'];
//echo "<br>$objet --- majr=$majr  ".strtotime($majr)."   majc=$majc".strtotime($majc) ;

			// Si la date de la référence est plus récente,
			// OU si le champs n'existe pas encore dans la base courante mais qu'il existe dans la base de référence
			if ((strtotime($majr) > strtotime($majc)) OR (!$majc AND $majr))
			{
				$txt_maj = _T('sextras:datemaj', array('date'=>$majr));;
				$ok = '';
				$warning = _T('sextras:majafaire');
				array_push($defaut, 1);
				array_push($defaut, $objet);
			}
			else
			{
				$txt_maj = "";
			}

			$datas[$objet] = ucfirst($objet).$txt_maj; // La pair clé/valeur affichée
		}
	}


	$mes_saisies = array(
		array( // Département => Zone
			'saisie' => 'checkbox',
			'options' => array(
				'nom' => 'maj_objet',
				'label' => _T('sextras:objet'),
				'datas' => $datas,
				'defaut' => $defaut
			)
		)
	);


	$valeurs = array(
    	'mes_saisies'    => $mes_saisies,
		'message_erreur' => $erreur.$warning,
		'message_ok'     => $ok
	);

	return $valeurs;
}

function formulaires_sextras_synchroniser_verifier()
{
//	include_spip('inc/saisies');
	$erreurs = array();

	// RIEN pour le moment. En prévision de l'utilisation de paramètre dans le formulaire

	return $erreurs;
}

function formulaires_sextras_synchroniser_traiter()
{
	$retours = array();
	$t_objets = _request('maj_objet');

	if (count($t_objets)==0) return $retours;


	// Lit la définition meta des champs extra dans la base de référence
	//******************************************************************
	include_spip('base/connect_sql');
	$serveur = spip_connect(CONNECTION_REF);
	if (!$serveur) {$retours['message_erreur'] = _T('sextras:erreur_conbase'); return $retours;}
	else
	{
		// La connexion a réussi. Pour chaque objet pouvant avoir des champs extra

		foreach ($t_objets as $key=>$objet)
		{
			echo '<br>'._T('sextras:objet_en_cours', array('objet'=>$objet));

			$nom_champs = 'champs_extras_spip_'.$objet;

			// on lit les meta dans la base de référence
			$result = sql_select(array('valeur'), array('spip_meta'), array("nom='$nom_champs'"), '', '', '', '', CONNECTION_REF);
			if (!$result) {$retours['message_erreur'] = _T('sextras:erreur_lecture_ce', array('objet'=>$objet)); return $retours;}
			else
			{
				$row = sql_fetch($result);
				$valeur = $row['valeur'];

				if (!$valeur) {echo _T('sextras:erreur_lecture_defvide', array('objet'=>$objet));}
				else
				{
					// Il y a bien une définition dans la meta de référence
					// On écrase la déf de la base courante par la valeur de la base référence
					$r = sql_insertq('spip_meta', array('nom'=>$nom_champs, 'valeur'=>$valeur)); // Si le champs n'existe pas
					if (is_null($r)) $r = sql_updateq('spip_meta', array('valeur'=>$valeur, 'maj'=>'NOW()'),'nom='.sql_quote($nom_champs));


					// On créé les champs dans la table
					//*********************************
					$valeur = unserialize($valeur);
//include_once '/home/spip3/public_html/krumo/class.krumo.php';
//echo "<br>52"; krumo($valeur);
					$valeur = saisies_lister_par_nom($valeur); // met tout à plat. Sinon les champs imbriqués dans des fieldset ne seront pas pris en compte au moment de la création du champs en base (function champs_extras_creer)
//echo "<br>139"; krumo($valeur);

					// Remplace les clés numériques par le nom de la table
					$t = array();
					foreach ($valeur as $key => $value) {
						$t['spip_'.$objet][$value['options']['nom']] = $value;
					}
	//echo "<br>59 t="; var_dump($t);
					//$valeur = array('champs_extras_spip_articles', $valeur);
	//echo "<br>54"; var_dump($valeur);
					include_spip('inc/cextras');
					$maj = array();
					$r = cextras_api_upgrade($t, $maj['create']);
	//echo "<br>58"; var_dump($maj);

					if ($r==false) {$retours['message_erreur'] = __LINE__._T('sextras:erreur_majce', array('objet'=>$objet)); return $retours;}
					else
					{
						//L'opération de remplissage du tableau $maj a réussi
	//echo '<br>maj[create]='; var_dump($maj['create']);

						include_spip('base/upgrade');
						$r = maj_plugin('', '', $maj); // retourne un tableau vide en cas de réussite
						if (count($r)>0) {$retours['message_erreur'] = __LINE__._T('sextras:erreur_majce', array('objet'=>$objet)); return $retours;}
						else
						{
							// La MAJ de la meta a réussi. YOUPI.
							$retours['message_ok'] = _T('sextras:synchro_ok');
						}
					}
				}
			}
		}
	}

	return $retours;
}

?>
