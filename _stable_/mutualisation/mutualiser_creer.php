<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_PRIVILEGES_MYSQL_USER_BASE','Alter, Select, Insert, Update, Delete, Create, Drop');

include_spip('inc/minipres');

/* centrage...  */
function mutu_minipres($titre="",$contenu=""){
	return minipres($titre,"<div class='petit-centre'>" . $contenu . "</div>");
}

// http://doc.spip.org/@mutualiser_creer
function mutualiser_creer($e, $options) {
	include_spip('base/abstract_sql');
	include_once(dirname(__FILE__).'/base/abstract_mutu.php');

	$GLOBALS['meta']["charset"] = 'utf-8'; // pour que le mail fonctionne
	
	//$GLOBALS['spip_connect_version'] = 0.7;
	
	if (!defined('_INSTALL_SERVER_DB'))
		define('_INSTALL_SERVER_DB','mysql');

	mutu_etape_code_activation($e, $options);
	mutu_etape_creer_repertoires($e, $options);
	mutu_etape_creer_base($e, $options);
	mutu_etape_fin($e, $options);
}


/*
 * Code d'activation du site
 * 
 * Demander le code pour autoriser cette creation de site
 * Si le code est valide, poser un cookie
 * 
 */
function mutu_etape_code_activation($e, $options){
	if ($options['code']) {
		$secret = md5($code.$options['code']);

		if ($options['code'] != $_REQUEST['code_activation']
		AND $_COOKIE['mutu_code_activation'] != $secret) {
			echo mutu_minipres(
				_L('Installation de votre site SPIP'),
				"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n".
			
				(isset($_REQUEST['code_activation'])
					? _L('Erreur...')
					: ''
				) .

				'<h3>'.
				_L('Veuillez entrer le code d\'activation du site :').
				'</h3>'.

				"<form method='post' action='".self()."'>
				<input type='text' name='code_activation' size='10' />
				<input type='submit' value='ok' />
				</form>
				"
			);
			exit;
		} else {
			setcookie('mutu_code_activation', $secret);
		}
	}	
}


/*
 * Creation de la base
 * 
 * Cree la base de donnee
 * Cree eventuellement un utilisateur pour cette base
 * 
 */
function mutu_etape_creer_base($e, $options){

	if ($options['creer_base']) {

		if (defined('_INSTALL_SERVER_DB')
		AND defined('_INSTALL_NAME_DB')) {

			if (defined('_INSTALL_USER_DB_ROOT')) {
				$link = mutu_connect_db(_INSTALL_HOST_DB, 0,  _INSTALL_USER_DB_ROOT, _INSTALL_PASS_DB_ROOT, '', _INSTALL_SERVER_DB);
			} else {
				$link = mutu_connect_db(_INSTALL_HOST_DB, 0,  _INSTALL_USER_DB, _INSTALL_PASS_DB, '', _INSTALL_SERVER_DB);
			}

			// si la base n'existe pas, on va travailler
			if (!sql_selectdb(_INSTALL_NAME_DB, _INSTALL_SERVER_DB)) {
				if (_request('creerbase') == 'oui') {

					// mode de creation par un ping sur une URL (AlternC)
					// on le fait en local et en POST, donc pas de trou de secu
					// curl indispensable pour le https... devrait aller dans inc/distant
					if ($options['url_creer_base']
					AND defined('_INSTALL_NAME_DB')) {
						$url = str_replace('%x', _INSTALL_NAME_DB, $options['url_creer_base']);
						$ch = curl_init($url);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						$page = curl_exec($ch);
						#var_dump($page);
						curl_close($ch);
						if (!sql_selectdb(_INSTALL_NAME_DB, _INSTALL_SERVER_DB)) {
							echo mutu_minipres(
								_L('La cr&#233;ation de la base de donn&#233;es <tt>'._INSTALL_NAME_DB.'</tt> a &#233;chou&#233;.'),
								"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n".
								'<h3>'
								._L('<a href="'.parametre_url(self(), 'creerbase', 'oui').'">R&#233;essayer...</a>')
								.'</h3>'
							);
							exit;
						}
					}

					else if (sql_query('CREATE DATABASE '._INSTALL_NAME_DB, _INSTALL_SERVER_DB)
					AND sql_selectdb(_INSTALL_NAME_DB, _INSTALL_SERVER_DB)) {
							$GLOBALS['connexions'][_INSTALL_SERVER_DB]['prefixe'] = $GLOBALS['table_prefix'];
							$GLOBALS['connexions'][_INSTALL_SERVER_DB]['db'] = _INSTALL_NAME_DB;
						
						/*
						 * Creation d'un utilisateur pour la base nouvellement cree
						 *		
						 * Pour chaque base creee on cree aussi un user
						 * MYSQL specifique qui aura les droits sur la base
						 */
						if ($options['creer_user_base']) {
	
							// le nom de la machine MySQL peut etre different 
							// du nom de la connexion via DNS
							define ('_INSTALL_HOST_DB_LOCALNAME', _INSTALL_HOST_DB); 
							
							// requete differente entre pg et mysql...
							$req = $err = array();
							switch (strtolower(_INSTALL_SERVER_DB)){
									
								case 'pg':
									// d'abord creer l'utilisateur
									$req[] = "CREATE USER " . _INSTALL_USER_DB . " WITH PASSWORD '" . _INSTALL_PASS_DB . "'";
									$err[] = "CREATE USER " . _INSTALL_USER_DB . " WITH PASSWORD 'xxx'";
									// l'affecter a sa base de donnee
									$req[] = $r = "GRANT ALL PRIVILEGES ON DATABASE " 
										. _INSTALL_NAME_DB . " TO ". _INSTALL_USER_DB;
									$err[] = $r;
									break;
									
								case 'mysql':
								default:
									$req[] = "GRANT " . _PRIVILEGES_MYSQL_USER_BASE . " ON "
										. _INSTALL_NAME_DB.".* TO '" 
										. _INSTALL_USER_DB."'@'"._INSTALL_HOST_DB_LOCALNAME
										. "' IDENTIFIED BY '" . _INSTALL_PASS_DB . "'";
									$err[] = "GRANT " . _PRIVILEGES_MYSQL_USER_BASE . " ON "
										. _INSTALL_NAME_DB.".* TO '" 
										. _INSTALL_USER_DB."'@'"._INSTALL_HOST_DB_LOCALNAME
										. "' IDENTIFIED BY 'xxx'";
									break;	
								
							}
							foreach ($req as $n=>$sql){
								if (!sql_query($sql, _INSTALL_SERVER_DB)) {
									die (__FILE__." " . __LINE__ . ": Erreur (" ._INSTALL_SERVER_DB . ") sur  :" . $err[$n]);
								}
							}
							mutu_close();
							$link = mutu_connect_db(_INSTALL_HOST_DB,'',  _INSTALL_USER_DB, _INSTALL_PASS_DB, '', _INSTALL_SERVER_DB);
						}
						
						// creation ok
						// supprimer le fichier d'installation
						include_spip('inc/flock');
						@supprimer_fichier($e . _NOM_TEMPORAIRES_INACCESSIBLES . _MUTU_INSTALLATION_FILE);
						
						echo mutu_minipres(
							_L('La base de donn&#233;es <tt>'._INSTALL_NAME_DB.'</tt> a &#233;t&#233; cr&#233;&#233;e'),
							"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n".
							'<h3>'
							._L('Vous pouvez <a href="'.generer_url_ecrire('install').'">poursuivre l\'installation de SPIP</a>.')
							.'</h3>'
						);

						if ($options['mail']) {
							$mail = charger_fonction('envoyer_mail', 'inc');
							$mail($options['mail'],
								_L('Creation de la base de donn&#233;es '._INSTALL_NAME_DB),
								_L('La base de donn&#233;es '._INSTALL_NAME_DB.'@'._INSTALL_HOST_DB.' ('._INSTALL_USER_DB.':'._INSTALL_PASS_DB.') a &#233;t&#233; cr&#233;&#233;e pour le site '.$e),
								$options['mail']
							);
						}
						exit;
					} else {
						echo mutu_minipres(
							_L('Cr&#233;ation de la base de donn&#233;es <tt>'._INSTALL_NAME_DB.'</tt>'),
							"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n".
							'<h3>'
							._L('erreur')
							.'</h3>'
						);
						exit;
					}

				}
				else {
					echo mutu_minipres(
						_L('Cr&#233;ation de la base de donn&#233;es <tt>'._INSTALL_NAME_DB.'</tt>'),
						"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n".
						'<h3>'
						._L('Voulez-vous <a href="'.parametre_url(self(), 'creerbase', 'oui').'">cr&#233;er cette base ?</a>')
						.'</h3>'
					);
					exit;
				}
			}

			// ici la base existe, on passe aux repertoires
		}
		
		else {
			echo mutu_minipres(
				_L('Creation de la base de donn&#233;es du site (<tt>'.joli_repertoire($e).'</tt>)'),

				"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n"
				.'<h3>'. _L('erreur') .'</h3>'
				. _L('Les donn&#233;es de connexion ' . strtoupper(_INSTALL_SERVER_DB) . ' ne sont pas d&#233;finies, impossible de cr&#233;er automatiquement la base.')
			);
			exit;
		}
	}	
}


/*
 * Cree les dossiers necessaires au site mutualise
 */
function mutu_etape_creer_repertoires($e, $options){
	if ($options['creer_site']) {
		$ok_dir =
		is_dir(_DIR_RACINE . $options['repertoire'])
		AND is_writable(_DIR_RACINE . $options['repertoire']);

		if (!$ok_dir) {
			echo mutu_minipres(
				_L('Creation du r&eacute;pertoire du site (<tt>'.joli_repertoire($e).'</tt>)'),

				"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n"
				.'<h3>'. _L('erreur') .'</h3>'
				. _L('Le r&#233;pertoire <tt>'.$options['repertoire'].'/</tt> n\'est pas accessible en &#233;criture')
			);
			exit;
		}

		if (_request('creerrepertoire') && _request('creerrepertoire')=='oui') {
			$ok =
			mkdir($e, _SPIP_CHMOD)
			AND chmod($e, _SPIP_CHMOD)
			AND mkdir($e._NOM_PERMANENTS_INACCESSIBLES, _SPIP_CHMOD)
			AND mkdir($e._NOM_PERMANENTS_ACCESSIBLES, _SPIP_CHMOD)
			AND mkdir($e._NOM_TEMPORAIRES_INACCESSIBLES, _SPIP_CHMOD)
			AND mkdir($e._NOM_TEMPORAIRES_ACCESSIBLES, _SPIP_CHMOD)
			AND chmod($e._NOM_PERMANENTS_INACCESSIBLES, _SPIP_CHMOD)
			AND chmod($e._NOM_PERMANENTS_ACCESSIBLES, _SPIP_CHMOD)
			AND chmod($e._NOM_TEMPORAIRES_INACCESSIBLES, _SPIP_CHMOD)
			AND chmod($e._NOM_TEMPORAIRES_ACCESSIBLES, _SPIP_CHMOD);
			
			// pour signaler qu'il reste des etapes a realises, 
			// malgre la presence des repertoires
			if ($ok){
				include_spip('inc/flock');
				ecrire_fichier($e . _NOM_TEMPORAIRES_INACCESSIBLES . _MUTU_INSTALLATION_FILE, 'ok');
			}
			
			echo mutu_minipres(
				_L('Creation du r&eacute;pertoire du site (<tt>'.joli_repertoire($e).'</tt>)'),

				"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n"
				.'<h3>'
				. ($ok
					? _L('Cr&#233;ation des r&#233;pertoires OK. Vous pouvez <a href="'.parametre_url(self(), 'creerrepertoire', '').'">Continuer...</a>.')
					: _L('erreur')
				).'</h3>'
			);

			if ($options['mail']) {
				$mail = charger_fonction('envoyer_mail', 'inc');
				$mail($options['mail'],
					_L('Creation du site '.joli_repertoire($e)),
					_L('Les r&#233;pertoires du site '.$e.' ont &#233;t&#233; cr&#233;&#233;s.'),
					$options['mail']
				);
			}
			exit;

		} elseif (
			   !is_dir($e._NOM_PERMANENTS_INACCESSIBLES)
			|| !is_dir($e._NOM_PERMANENTS_ACCESSIBLES)
			|| !is_dir($e._NOM_TEMPORAIRES_INACCESSIBLES)
			|| !is_dir($e._NOM_TEMPORAIRES_ACCESSIBLES)
		) {
			echo mutu_minipres(
				_L('Creation du r&eacute;pertoire du site (<tt>'.joli_repertoire($e).'</tt>)'),

				"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n"
				.'<h3>'.
					_L('Voulez-vous <a href="'.parametre_url(self(), 'creerrepertoire', 'oui').'">cr&#233;er les r&#233;pertoires de ce site ?</a>')
				.'</h3>'
				. (!$ok_dir ? _L('Le r&#233;pertoire <tt>'.$options['repertoire'].'/</tt> n\'est pas accessible en &#233;criture') : '')
			);
			exit;

		}

	} else {
		echo mutu_minipres(
			_L('Le r&eacute;pertoire du site (<tt>'.joli_repertoire($e).'</tt>) n\'existe pas'),
			"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n".
			'<h3>'
			._L('Veuillez cr&#233;er le r&#233;pertoire '.joli_repertoire($e).' et ses sous r&#233;pertoires:')
			.'</h3>'
			.'<ul>'
			.'<li>'.joli_repertoire($e)._NOM_PERMANENTS_INACCESSIBLES.'</li>'
			.'<li>'.joli_repertoire($e)._NOM_PERMANENTS_ACCESSIBLES.'</li>'
			.'<li>'.joli_repertoire($e)._NOM_TEMPORAIRES_INACCESSIBLES.'</li>'
			.'<li>'.joli_repertoire($e)._NOM_TEMPORAIRES_ACCESSIBLES.'</li>'
			.'</ul>'
		);
		exit;

	}	
}


/*
 * Fin de la procedure, proposer l'installation de SPIP
 */
function mutu_etape_fin($e, $options){
	// supprimer le fichier d'installation
	include_spip('inc/flock');
	@supprimer_fichier($e . _NOM_TEMPORAIRES_INACCESSIBLES . _MUTU_INSTALLATION_FILE);
	
	echo mutu_minipres(
		_L('Les r&#233;pertoires et la base de donn&#233;e du site sont maintenant cr&#233;&#233;s.'),

		"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n"
		.'<h3>'.
			_L('Vous pouvez <a href="'.generer_url_ecrire('install').'">poursuivre l\'installation de SPIP</a>.')
		.'</h3>'
	);
	exit;		
}
?>
