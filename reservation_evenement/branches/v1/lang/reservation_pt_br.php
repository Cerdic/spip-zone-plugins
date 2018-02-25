<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/reservation?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'affichage_par' => 'Exibir por:',
	'afficher_inscription_agenda_explication' => 'Inscrições via formulário do plugin Agenda',
	'ajouter_lien_reservation' => 'Incluir esta reserva',

	// B
	'bonjour' => 'Olá',

	// C
	'choix_precis' => 'Escolha estrita',
	'complet' => 'Completo',
	'cron_explication' => 'Para qualquer evento com o fechamento automático ativado. Quando o evento da reserva passar, a reserva será automaticamente fechada pelo sistema. Se "fechamento" for selecionado em "Disparador", um e-mail de fechamento será enviado. Limpe o cahce para que a função seja ativada corretamente.',
	'cron_fieldset' => 'Fechamento automático',
	'cron_label' => 'Fechar automaticamente uma reserva',

	// D
	'designation' => 'Designação',
	'details_reservation' => 'Detalhes da reserva:',
	'duree_vie_explication' => 'Informe a duração (em horas) de um comando com o status "@statut_defaut@". Se nenhum valor ou o valor 0 forem informados - a duração é iliimitada.',
	'duree_vie_label' => 'Duração:',

	// E
	'erreur_email_utilise' => 'Esse email já está cadastrado. Favor conectar-se, ou inserir outro endereço de email',
	'erreur_pas_evenement' => 'Não há nenhum evento com inscrição aberta, no momento.',
	'evenement_cloture' => 'Evento encerrado',
	'evenement_ferme_inscription' => 'Este evento está com as inscrições encerradas, atualmente. Clique no botâo Inscrição para visualizar a disponibilidade atual.',
	'explication_client' => 'Escolha um cliente entre os autores ou informe os dados do cliente, abaixo',
	'explication_email_reutilisable' => 'Permitir reutilizar um e-mail de um autor Spip no momento de uma reserva sem registro',
	'explication_enregistrement_inscrit' => 'Registrar como autor Spip',
	'explication_envoi_separe' => 'A troca de status de um Detalhe da Reserva para 
<div><b>"@statuts@"</b></div> 
gerará o envio de uma notificação!',
	'explication_envoi_separe_detail' => 'A troca do status para <div><strong>"@statuts@"</strong></div> gerará o envio de uma notificação!',
	'explication_login' => '<a rel="nofollow" class="login_modal" href="@url@" title="@titre_login@">Conecte-se</a> se você já tem cadastro no site',
	'explication_nombre_evenements' => 'O número mínimo de reservas para que a promoção seja disponibilizada.',
	'explication_nombre_evenements_choix' => 'Se vazio ou 0, este número será igual ao número de @objet_promotion@s escolhidos acima',
	'explication_objet_promotion' => 'Se definido no nível de matéria, serão incluídos todos os eventos disponíveis para a reserva da matéria.',

	// F
	'formulaire_public' => 'Formulário público',

	// I
	'icone_cacher' => 'Esconder',
	'icone_creer_reservation' => 'Criar uma reserva',
	'icone_modifier_client' => 'Alterar este cliente',
	'icone_modifier_reservation' => 'Modificar esta reserva',
	'info_1_client' => 'Um cliente',
	'info_1_reservation' => 'Uma reserva',
	'info_1_reservation_liee' => 'Uma reserva vinculada',
	'info_aucun_client' => 'Nenhum cliente',
	'info_aucun_reservation' => 'Nenhuma reserva',
	'info_nb_clients' => '@nb@ clientes',
	'info_nb_reservations' => '@nb@ reservas',
	'info_nb_reservations_liees' => '@nb@ reservas vinculadas',
	'info_reservations_auteur' => 'Reservas desse autor',
	'info_voir_reservations_poubelle' => 'Ver reservas postas na lixeira',
	'inscription' => 'Inscrição',
	'inscrire' => 'Inscrever-se',
	'inscrire_liste_attente' => 'Escolha outro curso ou se inscreva na lista de espera.',

	// L
	'label_action_cloture' => 'Encerramento automático:',
	'label_afficher_inscription_agenda' => 'Exibir os resultados de inscrição da Agenda',
	'label_client' => 'Cliente:',
	'label_date' => 'Data:',
	'label_date_paiement' => 'Data do pagamento:',
	'label_donnees_auteur' => 'Dados Autor:',
	'label_email' => 'Email:',
	'label_email_reutilisable' => 'Permitir reutilizar um endereço de e-mail:',
	'label_enregistrement_inscrit' => 'Permitir que o visitante se registre ao fazer uma reserva:',
	'label_enregistrement_inscrit_obligatoire' => 'Tornar o registro obrigatório:',
	'label_enregistrer' => 'Quero me cadastrar no site:',
	'label_id_auteur' => 'Id autor:',
	'label_inscription' => 'Inscrição:',
	'label_lang' => 'Língua:',
	'label_maj' => 'Gerado em:',
	'label_modifier_identifiants_personnels' => 'Alterar os dados pessoais:',
	'label_mot_passe' => 'Senha:',
	'label_mot_passe2' => 'Repita a senha:',
	'label_nom' => 'Nome:',
	'label_nombre_evenements' => 'Número de coincidências:',
	'label_objet_article' => 'Escolha as matérias cujos eventos serão disponibilizados para a promoção:',
	'label_objet_evenement' => 'Escolha os eventos disponíveis para a promoção:',
	'label_objet_promotion' => 'Definir a qual nível se aplica a promoção:',
	'label_reference' => 'Referência:',
	'label_reservation' => 'Reserva :',
	'label_statut' => 'Status:',
	'label_statut_calculer_auto' => 'Calcular automaticamente o status aceito da reserva:',
	'label_statut_calculer_auto_explication' => 'Ao alterar o status para aceito, verificar se todos os detalhes de reserva estão com o status aceito, caso contrário, será definido o status aceito parcialmente para a reserva.',
	'label_statut_defaut' => 'Status padrão :',
	'label_statuts_complet' => 'O(s) status completo(s) :',
	'label_type_paiement' => 'Tipo de pagamento:',
	'label_type_selection' => 'Tipo de seleção:',
	'legend_donnees_auteur' => 'Os dados do cliente',
	'legend_donnees_reservation' => 'Os daods da reserva',

	// M
	'merci_de_votre_reservation' => 'Agradecemos a sua inscrição, que foi efetuada corretamente.',
	'message_erreur' => 'Seu preenchimento contém erros!',
	'message_evenement_cloture' => 'O evento @titre@ acaba de encerrar. <br />Agradecemos pela sua participação.',
	'message_evenement_cloture_vendeur' => 'O evento @titre@ acaba de encerrar. <br />O sistema acaba de enviar uma mensagem de encerramento para @client@ - @email@.',
	'montant' => 'Montante',

	// N
	'nom_reservation_multiples_evenements' => 'Reserva de vários eventos',
	'notifications_activer_explication' => 'Enviar as notificações de reservas por email?',
	'notifications_activer_label' => 'Ativar',
	'notifications_cfg_titre' => 'Notificações',
	'notifications_client_explication' => 'Enviar notificações ao cliente?',
	'notifications_client_label' => 'Cliente',
	'notifications_destinataire_explication' => 'Escolher o(s) destinatário(s) das notificações',
	'notifications_destinataire_label' => 'Destinatário',
	'notifications_envoi_separe' => 'Ativar o modo Envio Separado para o status:',
	'notifications_envoi_separe_explication' => 'Permite ativar separadamente o envio de notificações para cada Detalhe de Reserva',
	'notifications_expediteur_administrateur_label' => 'Escolha um administrador:',
	'notifications_expediteur_choix_administrateur' => 'um administrador',
	'notifications_expediteur_choix_email' => 'um email',
	'notifications_expediteur_choix_facteur' => 'idem plugin Facteur',
	'notifications_expediteur_choix_webmaster' => 'um webmaster',
	'notifications_expediteur_email_label' => 'Informar um e-mail:',
	'notifications_expediteur_explication' => 'Escolher o remetente das notificações para o vendedor e o comprador',
	'notifications_expediteur_label' => 'Remetente',
	'notifications_expediteur_webmaster_label' => 'Escolher um webmaster:',
	'notifications_explication' => 'Notificações são usadas ​​para enviar emails após as mudanças no status da reserva: Esperando validação, em curso, enviado, parcialmente pago, pago, retornado, retorno parcial. Esse recurso requer o plugin Notificações Avançadas.',
	'notifications_parametres' => 'Parâmetros das notificações',
	'notifications_quand_explication' => 'Qual(is) troca(s) de status deflagra(m) o envio de notificação?',
	'notifications_quand_label' => 'Lançamento',
	'notifications_vendeur_administrateur_label' => 'Escolher um ou mais administradores:',
	'notifications_vendeur_choix_administrateur' => 'um ou vários administradores',
	'notifications_vendeur_choix_email' => 'um ou vários emails',
	'notifications_vendeur_choix_webmaster' => 'um ou vários webmasters',
	'notifications_vendeur_email_explication' => 'Insira um ou vários emails separados por vírgulas:',
	'notifications_vendeur_email_label' => 'Email(s):',
	'notifications_vendeur_label' => 'Vendedor',
	'notifications_vendeur_webmaster_label' => 'Escolher um ou vários webmasters:',

	// P
	'par_articles' => 'matérias',
	'par_evenements' => 'eventos',
	'par_reservations' => 'reservas',
	'periodicite_cron_explication' => 'Período após o qual o sistema verifica se as reservas deverão ser encerradas (min 600: 10 min)',
	'periodicite_cron_label' => 'Periodicidade do cron em segundos',
	'places_disponibles' => 'Lugares disponíveis:',

	// R
	'recapitulatif' => 'Resumo da reserva:',
	'remerciement' => 'Agradecemos sua inscrição<br/>Atenciosamente',
	'reservation_client' => 'Cliente',
	'reservation_date' => 'Data:',
	'reservation_de' => 'Reserva de',
	'reservation_enregistre' => 'Sua inscrição foi registrada corretamente. Você receberá um email de confirmação. Se não chegar, verifique sua caixa de spam.',
	'reservation_numero' => 'Reserva:',
	'reservation_reference_numero' => 'Referência n° ',
	'rubrique_reservation_explication' => 'Permite restringir o uso deste plugin a zona(s) específica(s)',
	'rubrique_reservation_label' => 'Definir a(s) zona(s) de utilização deste plugin',

	// S
	'simple' => 'Simples',
	'statuts_complet_explication' => 'Os status do detalhe da reserva levados em conta para determinar se o evento está completo.',
	'sujet_une_reservation_accepte' => 'Reserva confirmada de @nom@',
	'sujet_une_reservation_accepte_part' => 'Reserva parcialmente confirmada para @nom@',
	'sujet_une_reservation_cloture' => 'Evento encerrado para @nom@',
	'sujet_votre_reservation_accepte' => '@nom@ : confirmação de sua reserva',
	'sujet_votre_reservation_accepte_part' => '@nom@: confirmação parcial da sua reserva',
	'sujet_votre_reservation_cloture' => '@nom@: encerramento do evento',

	// T
	'texte_ajouter_reservation' => 'Incluir uma reserva',
	'texte_changer_statut_reservation' => 'Essa reserva está:',
	'texte_exporter' => 'exportar',
	'texte_statut_accepte' => ' aceito',
	'texte_statut_accepte_part' => 'aceita parcialmente',
	'texte_statut_attente' => ' em lista de espera',
	'texte_statut_attente_paiement' => ' aguardando pagamento',
	'texte_statut_cloture' => ' encerrado',
	'texte_statut_encours' => ' em andamento',
	'texte_statut_poubelle' => ' no lixo',
	'texte_statut_refuse' => 'Resposta negada',
	'texte_voir' => 'ver',
	'titre_client' => 'Cliente',
	'titre_clients' => 'Clientes',
	'titre_envoi_separe' => 'Modo Envio Separado ativado',
	'titre_reservation' => 'Reservas',
	'titre_reservations' => 'Reservas',
	'total' => 'Total',
	'type_lien' => 'Vinculado com a reserva @reference@',

	// U
	'une_reservation_de' => 'Uma reserva de: ',
	'une_reservation_sur' => 'Uma reserva de @nom@',

	// V
	'votre_reservation_sur' => '@nom@: sua reserva'
);
