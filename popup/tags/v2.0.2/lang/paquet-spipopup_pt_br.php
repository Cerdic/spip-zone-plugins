<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-spipopup?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'spipopup_description' => 'Gerenciamento de uma janela popup ({janela externa}) única sob a forma de template SPIP, com dimensões reguláveis para diferentes usos.

{{Uso da tag #POPUP }}
<code>
#POPUP{objeto SPIP,template,largura,altura,título,opções}
</code>
- {{objeto SPIP}}: ’article1’ ou ’id_article=1’ (válido por padrão para qualquer objeto editorial do SPIP).
- {{template}}: template usado para exibir a janela ({opcional - por padrão: ’{{popup_defaut.html}}’}).
- {{largura}}: a largura da janela em pixels ({opcional - {{620px}} por padrão}).
- {{altura}}: a altura da janela em pixels ({opcional - {{640px}} por padrão}).
- {{título}} : o título adicionado ao link.
- {{opções}} : uma tabela de opções JavaScript para a nova janela ({posição, status ...}).

{{Uso do modelo  nas matérias}}
<code>
<popup
|texte=o texto do link (obrigatório)
|lien=objeto SPIP para o link (obrigatório)
|skel=template (opcional)
|width=XX (opcional)
|height=XX (opcional)
|titre=o título (opcional)
>
</code>
Mesmas opções que a tag, mais o texto do link.

{{Retorno da tag #POPUP }}

A tag retorna uma âncora (<code>a</code>) com os seguintes atributos:
- href = "url"
- onclick = "_popup_set(’url’, width, height, options) ; return false;" 
- title = "titre - nova janela"
',
	'spipopup_slogan' => 'Gerenciamento de uma janela popup única num template SPIP'
);
