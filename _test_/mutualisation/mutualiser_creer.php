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


// http://doc.spip.org/@mutualiser_creer
function mutualiser_creer($e, $options) {
	include_spip('inc/minipres');
	include_spip('base/abstract_sql');
	include_spip('base/abstract_mutu');
	
	$GLOBALS['meta']["charset"] = 'utf-8'; // pour que le mail fonctionne
	
	//$GLOBALS['spip_connect_version'] = 0.7;
	
	if (!defined('_INSTALL_SERVER_DB'))
		define('_INSTALL_SERVER_DB','mysql');

	if ($options['code']) {
		$secret = md5($code.$options['code']);

		if ($options['code'] != $_REQUEST['code_activation']
		AND $_COOKIE['mutu_code_activation'] != $secret) {
			echo minipres(
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

	if ($options['creer_base']) {

		if (defined('_INSTALL_HOST_DB')
		AND (
			(defined('_INSTALL_USER_DB_ROOT') AND defined('_INSTALL_PASS_DB_ROOT'))
			OR
			(defined('_INSTALL_USER_DB') AND defined('_INSTALL_PASS_DB'))
			)
		AND defined('_INSTALL_NAME_DB')) {

			if (defined('_INSTALL_USER_DB_ROOT')) {
				$link = mutu_connect_db(_INSTALL_HOST_DB, 0,  _INSTALL_USER_DB_ROOT, _INSTALL_PASS_DB_ROOT, '', _INSTALL_SERVER_DB);
			} else {
				$link = mutu_connect_db(_INSTALL_HOST_DB, 0,  _INSTALL_USER_DB, _INSTALL_PASS_DB, '', _INSTALL_SERVER_DB);
			}

			// si la base n'existe pas, on va travailler
			if (!sql_selectdb(_INSTALL_NAME_DB, _INSTALL_SERVER_DB)) {
				if (_request('creerbase')) {
					if (sql_query('CREATE DATABASE '._INSTALL_NAME_DB, _INSTALL_SERVER_DB)
					AND sql_selectdb(_INSTALL_NAME_DB, _INSTALL_SERVER_DB)) {
							$GLOBALS['connexions'][_INSTALL_SERVER_DB]['prefixe'] = $GLOBALS['table_prefix'];
							$GLOBALS['connexions'][_INSTALL_SERVER_DB]['db'] = _INSTALL_NAME_DB;
								
						// Pour chaque base creee on cree aussi un user
						// MYSQL specifique qui aura les droits sur la base
						if ($options['creer_user_base']) {
							// le nom de la machine MySQL peut different 
							// du nom de la connexion via DNS
							define ('_INSTALL_HOST_DB_LOCALNAME', _INSTALL_HOST_DB); 							
							if (!sql_query("GRANT Alter,Select,Insert,Update,Delete,Create,Drop,Execute ON "
								. _INSTALL_NAME_DB.".* TO '" 
								. _INSTALL_USER_DB."'@'"._INSTALL_HOST_DB
								. "' IDENTIFIED BY '" . _INSTALL_PASS_DB."'")) 
							{
								die (__FILE__." " . __LINE__ 
									. ": Erreur sur  : GRANT Select,Insert,Update,Delete,Create,Drop,Execute ON "
									. _INSTALL_NAME_DB.".* TO '"
									. _INSTALL_USER_DB."'@'"._INSTALL_HOST_DB_LOCALNAME
									. "'  IDENTIFIED BY 'xxx'");
							}
							mutu_close();
							$link = mutu_connect_db(_INSTALL_HOST_DB,'',  _INSTALL_USER_DB, _INSTALL_PASS_DB, '', _INSTALL_SERVER_DB);
						}
						echo minipres(
							_L('La base de donn&#233;es <tt>'._INSTALL_NAME_DB.'</tt> a &#233;t&#233; cr&#233;&#233;e'),
							"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n".
							'<h3>'
							._L('<a href="'.parametre_url(self(), 'creerbase', null).'">Continuer...</a>')
							.'</h3>'
						);
						if ($options['mail']) {
							$mail = charger_fonction('envoyer_mail', 'inc');
							$mail($options['mail'],
								_L('Creation de la base de donn&#233;es '._INSTALL_NAME_DB),
								_L('La base de donn&#233;es '._INSTALL_NAME_DB.' a &#233;t&#233; cr&#233;&#233;e pour le site '.$e),
								$options['mail']
							);
						}
						exit;
					} else {
						echo minipres(
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
					echo minipres(
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
			echo minipres(
				_L('Creation de la base de donn&#233;es du site (<tt>'.joli_repertoire($e).'</tt>)'),

				"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n"
				.'<h3>'. _L('erreur') .'</h3>'
				. _L('Les donn&#233;es de connexion ' . uppercase(_INSTALL_SERVER_DB) . ' ne sont pas d&#233;finies, impossible de cr&#233;er automatiquement la base.')
			);
			exit;
		}
	}

	if ($options['creer_site']) {
		$ok_dir =
		is_dir(_DIR_RACINE . $options['repertoire'])
		AND is_writable(_DIR_RACINE . $options['repertoire']);

		if (!$ok_dir) {
			echo minipres(
				_L('Creation du r&eacute;pertoire du site (<tt>'.joli_repertoire($e).'</tt>)'),

				"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n"
				.'<h3>'. _L('erreur') .'</h3>'
				. _L('Le r&#233;pertoire <tt>'.$options['repertoire'].'/</tt> n\'est pas accessible en &#233;criture')
			);
			exit;
		}

		if (_request('creerrepertoire')) {
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

			echo minipres(
				_L('Creation du r&eacute;pertoire du site (<tt>'.joli_repertoire($e).'</tt>)'),

				"<div><img alt='SPIP' src='" . _DIR_IMG_PACK . "logo-spip.gif' /></div>\n"
				.'<h3>'
				. ($ok
					? _L('Cr&#233;ation des r&#233;pertoires OK. Vous pouvez <a href="'.generer_url_ecrire('install').'">installer votre site</a>.')
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

		} else {
			echo minipres(
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
		echo minipres(
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

?>
