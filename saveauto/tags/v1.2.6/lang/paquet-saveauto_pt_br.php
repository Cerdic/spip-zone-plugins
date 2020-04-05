<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-saveauto?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'saveauto_description' => 'Permite realizar o backup MySQL de toda a base de dados usada pelo SPIP. O arquivo .zip (ou .sql) gerado é gravado num diretório (por padrão /tmp/dump, configurável) e pode ser enviado por e-mail.

O sbackups gravados considerados obsoletos em função dos parâmetros de configuração correspondente) são automaticamente destruídos.

Uma interface permite disparar manualmente os backups e gerenciar os arquivos gerados.',
	'saveauto_nom' => 'Backup automático',
	'saveauto_slogan' => 'Backup MySQL automático da base de dados do SPIP'
);
