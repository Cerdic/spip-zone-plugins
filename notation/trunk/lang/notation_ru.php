<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/notation?lang_cible=ru
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'acces' => 'Доступ к голосованию',
	'afficher_tables' => 'Показать оценки',
	'aide' => 'Помощь',
	'articles' => 'Статьи',
	'auteur' => 'Автор',

	// B
	'bouton_radio_fermee' => 'Отключить',
	'bouton_radio_ouvert' => 'Включить',
	'bouton_voter' => 'Проголосовать',

	// C
	'change_note_label' => 'Предоставить такую возможность посетителям',
	'configuration_notation' => 'Настройки голосования',
	'creation' => 'Создание таблиц',
	'creation_des_tables_mysql' => 'Создание таблиц',
	'cree' => 'Таблица создана',
	'creer_tables' => 'Создать таблицу',

	// D
	'date' => 'Дата',
	'derniers_votes' => 'Последние голосования',
	'destruction' => 'Удалить таблицу',
	'detruire' => '<strong style="color:red">Внимание! эта опция очищает таблицы!</strong><br />Используйте, только при отключении плагина.',
	'detruit' => 'Таблицы удалены...',

	// E
	'effacer_tables' => 'Удаление таблицы',
	'err_balise' => '[NOTATION_ERR: тег вне статьи]',
	'err_db_notation' => '[LOG ERROR: только одна оценка по каждому пункту]',
	'exemple' => 'Таблица распределения голосовых коэффициентов (количество звезд = 5, коэффициент = @ponderation@) : ',
	'explication_accepter_note' => 'Si "fermée", la notation sera activable au cas par cas sur les objets ayant cette fonctionnalité.',

	// I
	'info_acces' => 'Голосование открыто для : ',
	'info_etoiles' => 'Этот парамеир позволяет изменять к-во звезд в голосовании (по умолчанию - 5). <br />
                    <strong style="color:red">/!\\ Внимание</strong> : Не рекомендуется изменять этот параметр, так как после его изменения пересчет голосов согласно новому значению не произойдет! Таким образом, данные голосование будут искажены. <br /> 
                    Этот пареметр устанавливается ТОЛЬКО ЕДИНОЖДЫ при настройке работы модуля голосования',
	'info_fonctionnement_note' => 'Функционал голосования',
	'info_ip' => 'Доступ к голосованию предоставляется посетителям сайта. Для получения более точного результата голосования с одного IP-адреса возможно проголосавть единожды.',
	'info_modifications' => 'Возможность переголосовать',
	'info_ponderation' => 'В этом поле указываем весовой коэффициент для голоса. <br /> Ниже предсталенна таблица, по которой он расчитывается. РЕКОМЕНДУЕТСЯ ОБОЗНАЧИТЬ КОЭФФИЦИЕНТ 1.',
	'ip' => 'IP-адрес',
	'item_adm' => 'администраторов ',
	'item_all' => 'для всех посетителей сайта',
	'item_aut' => 'авторов ',
	'item_id' => '1 голос пользователя ',
	'item_ide' => 'зарегистрированных посетителей  ',
	'item_ip' => 'Один голос с одного IP-адреса', # MODIF

	// J
	'jaidonnemonavis' => 'Голосование осуществлено !',
	'jaime' => 'Мне нравится',
	'jaimepas' => 'Не нравится',
	'jaimeplus' => 'Нравится',
	'jechangedavis' => 'Отозвать голос',

	// L
	'label_accepter_note' => 'Статус-рейтинг всех объектов',

	// M
	'moyenne' => 'Средний результат',
	'moyennep' => 'С учетом коэфф.',

	// N
	'nb_etoiles' => 'Примечание',
	'nbobjets_note' => 'К-во статтей в которых проголосовали : ',
	'nbvotes' => 'К-во голосов', # MODIF
	'nbvotes_moyen' => 'Среднее к-во голосов на объект',
	'nbvotes_total' => 'Общее к-во голосов : ',
	'notation' => 'Голосование',
	'note' => 'К-во звезд : ',
	'note_1' => 'Оценка : 1',
	'note_10' => 'Оценка : 10',
	'note_2' => 'Оценка : 2',
	'note_3' => 'Оценка : 3',
	'note_4' => 'Оценка : 4',
	'note_5' => 'Оценка : 5',
	'note_6' => 'Оценка : 6',
	'note_7' => 'Оценка : 7',
	'note_8' => 'Оценка : 8',
	'note_9' => 'Оценка : 9',
	'note_pond' => '',
	'notes' => 'Оценки',

	// O
	'objets' => 'Объект голосования',

	// P
	'param' => 'Настройка',
	'ponderation' => 'Вес голоса (для корректных результатов - 1)',

	// T
	'titre_ip' => 'Режим работы',
	'topnb' => '10 найболее рейтинговых объектов',
	'topten' => 'ТОП-10',
	'toptenp' => 'Рейтинг',
	'totaux' => '',

	// V
	'valeur_nb_etoiles' => 'Изначальное к-во звезд ',
	'valeur_ponderation' => 'Множитель голоса',
	'vos_notes' => 'Топ голосований',
	'vote' => 'Оценка',
	'voter' => 'Проголосовать : ',
	'votes' => 'голоса',
	'votre_note' => 'Оценка'
);

?>
