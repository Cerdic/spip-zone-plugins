/** Appeler jquery table sorter
 * et lier les actions
**/
formidable_ts = '';
$(function() {
	/** Migration des vieux storages**/
	if (!localStorage.getItem('formidable_ts_version')) {
		$([
			'columnSelector',
			'columnSelector-auto',
			'filters',
			'pager',
			'resizable',
			'savesort',
			'table-original-css-width',
			'table-resized-width',
			'order',
		]).each(function (key, value) {
			value = 'tablesorter-'+value;
			storage = localStorage.getItem(value);
			if (storage) {
				newStorage = storage.replace('formidable_tablesorter', 'formidable_ts');
				localStorage.setItem(value, newStorage);
			}
		});
		localStorage.setItem('formidable_ts_version', 1);
	}

	/** Code principal **/
	formidable_ts = $(".tablesorter");
	formidable_ts.tablesorter({
		selectorSort: '.header-title',
		widgets: [
			"pager",
			"stickyHeaders",
			"filter",
			"print",
			"columnSelector",
			"output",
			"resizable",
			"savesort"
		],
		widgetOptions: {
			columnSelector_container: $('#columnSelector'),
			columnSelector_layout : '<label><input type="checkbox"><span>{name}</span></label>',
			columnSelector_mediaquery: false,
			columnSelector_updated : 'columnUpdate',
      print_columns: 's',
      print_rows: 'f',
			print_extraCSS: 'table{font-size:10pt}',
			filter_filterLabel: filter_filterLabel,
			filter_placeholder: {search:filter_placeholder},
      filter_saveFilters : true,
			output_separator: 'array',
			output_delivery: 'download',
			output_formatContent : function( c, wo, data ) {
				return data.content.replace('⬅ ➡', '');
			},
			stickyHeaders_xScroll : '.formidable_ts-wrapper',
			output_callback: function(config, data, url) {
				return call_formidable_ts_export(config, data, url);
			},
			pager_size: 100,//Nombre de lignes par page
			// css class names that are added
			pager_css: {
				container   : 'tablesorter-pager',    // class added to make included pager.css file work
				errorRow    : 'tablesorter-errorRow', // error information row (don't include period at beginning); styled in theme file
				disabled    : 'disabled'              // class added to arrows @ extremes (i.e. prev/first arrows "disabled" on first page)
			},
			pager_selectors: {
				container   : '.pager',       // target the pager markup (wrapper)
				first       : '.first',       // go to first page arrow
				prev        : '.prev',        // previous page arrow
				next        : '.next',        // next page arrow
				last        : '.last',        // go to last page arrow
				gotoPage    : '.gotoPage',    // go to page selector - select dropdown that sets the current page
				pageDisplay : '.pagedisplay', // location of where the "output" is displayed
				pageSize    : '.pagesize'     // page size selector - select dropdown that sets the "size" option
			},
		  pager_updateArrows: true,
			pager_savePages: true,
      pager_ajaxUrl : pager_ajaxUrl,
			pager_customAjaxUrl: function(table, url) {
				if (moving_flag) {
					return;
				}
				$('.formidable_ts-wrapper').addClass('loading');// Ceserait sans doute mieux ailleurs, mais bon
				obj = table.config.widgetOptions.pager_ajaxObject;
				if (order = $.tablesorter.storage(formidable_ts, 'tablesorter-order')) {
					obj.data['order'] = order;
				};
				if (filters = $.tablesorter.storage(formidable_ts, 'tablesorter-filters')) {
					obj.data['filter'] = filters;
				};
        return url; // required to return url, but url remains unmodified
			},
      pager_ajaxObject: {
        type: 'POST', // default setting
        dataType: 'json',
				data: {},
      },
			resizable_addLastColumn: true
		}
	}).bind('pagerComplete', function() {
		formidable_ts_saveOrder();
		formidable_ts_setColumnSelector();
		formidable_ts_set_move_arrows();
		formidable_ts.trigger('updateHeaders');
		$('.tablesorter-stickyHeader th .header-title').each(function() {
			$(this).bind('click', function(){
				dc = $(this).parents('th').attr('data-column');
				console.log(dc);
				$('th[data-column='+dc+'] .header-title',formidable_ts).trigger('click');
			});
		});
		formidable_ts_set_move_arrows_css();
		$('.formidable_ts-wrapper').removeClass('loading');
	}).bind('columnUpdate', function(){
		formidable_ts_set_move_arrows_css();
	});

  $('.print').click(function() {
    formidable_ts.trigger('printTable');
  });
	$('.output').click(function() {
		filename = $('table.tablesorter').data('identifiant');
		type_export = $(this).val();
		formidable_ts.trigger('outputTable');
		return false;
	});
	$('.resetFilter').click(function() {
		formidable_ts.trigger('filterReset');
	});
	$('.resetAll').click(function() {
		if (confirm(resetAllconfirm)) {
			formidable_ts_restart();
		}
	});
	formidable_ts_add_check_all_button();
	$('.puce_objet', formidable_ts).hover(function() {
		$('.formidable_ts-wrapper, .formidable_ts-wrapper td').addClass('puce_statut');
	},function() {
		$('.formidable_ts-wrapper, .formidable_ts-wrapper td').removeClass('puce_statut');
	});
});

/** Réglage du column selector **/
$(function() {
	flag_cs = false;
	$('#columnSelector').css('display','none');
	$('#columnSelectorButton').click(function () {
		if (flag_cs) {
			flag_cs = false;
			$('#columnSelector').hide(1200);
		} else {
			flag_cs = true;
			$('#columnSelector').show(1200);
		}
	}
	);
});

/**
 * Après chargement des entetes en ajax, remplir correctement le column selector
**/
function formidable_ts_setColumnSelector() {
	labels = $('#columnSelector label:not(#columnSelectorCheckAll) span');
	title = $('.header-title', formidable_ts);
	i = 0;
	title.each(function() {
		text = $(this).text();
		labels.eq(i).text(text);
		i++;
	});
}

function formidable_ts_add_check_all_button() {
	$('#columnSelector').prepend('<label id="columnSelectorCheckAll"><input type="checkbox" checked="checked" class="checked"><span>'+uncheckAll+'</span></label>');
	$('#columnSelectorCheckAll').change(function() {
		input = $('input', this);
		span = $('span', this);
		if ($('input', this).is(':checked')) {
			input.attr('class', 'checked');
			span.text(uncheckAll);
			$('#columnSelector input[data-column]').prop('checked',true).trigger('change');
		} else {
			input.attr('class');
			span.text(checkAll);
			$('#columnSelector input[data-column]').prop('checked',false).trigger('change');
		}
	});
}

/** Fonctions d'export tableur **/
function call_formidable_ts_export(config, data, url) {
	var form = $('<form></form>').attr('action', url_action_formidable_ts_export).attr('method', 'post');
	form.append($("<input></input>").attr('type', 'hidden').attr('name', 'data').attr('value', data));
	form.append($("<input></input>").attr('type', 'hidden').attr('name', 'type_export').attr('value', type_export));
	form.append($("<input></input>").attr('type', 'hidden').attr('name', 'filename').attr('value', filename));
	form.appendTo('body').submit().remove();
	return false;
}



/** Tout ce qui concerne le reordonnancement**/

/**
 * Sauver l'ordre initial
**/
function formidable_ts_saveOrder() {
	order = [];
	$('th div[data-col]',formidable_ts).each(function() {
		order.push($(this).attr('data-col'));
	});
	$.tablesorter.storage(formidable_ts, 'tablesorter-order', order, {});
};

moving_flag = false;

/**
 * Masquer la toute première flèche de gauche dans les colonens visibles, et la toute dernière de droite
 **/
function formidable_ts_set_move_arrows_css() {
	formidable_ts_sticky = $('#'+formidable_ts.attr('id')+'-sticky');
	tables = [formidable_ts_sticky, formidable_ts];
	for (table of tables) {
		$('.move-arrows').removeClass('leftOnly').removeClass('rightOnly');
		left = $('th:not(.filtered) .move-arrows .left', table);
		left.removeClass('disabled');
		left.first().addClass('disabled');
		left.first().parent().addClass('rightOnly');

		right = $('th:not(.filtered) .move-arrows .right', table);
		right.removeClass('disabled');
		right.last().addClass('disabled');
		right.last().parent().addClass('leftOnly');
	}
}
/**
 * Régler les évènemens sur les flèches
**/
function formidable_ts_set_move_arrows() {
	$('.move-arrows .left, .move-arrows .right').click(function(event) {
		col = $(this).parent().attr('data-col');
		th = $(this).parents('th');
		tr = th.parent();
		index = th.index();
		if ($(this).hasClass('left')) {
			prev = th.prevAll(':not(.filtered)').first();
			index_inserting = prev.index();
			move = 'left';
		} else {
			next = th.nextAll(':not(.filtered)').first();
			index_inserting = next.index();
			move = 'right';
		}
		// Ajuster le storage des paramètres de colonnes, sauf pour le tri qui se fait plus loins
		$([
			'columnSelector',
			'filters',
			'resizable',
			'order'
		]).each(function(key, storage) {
			order = $.tablesorter.storage(formidable_ts, 'tablesorter-' + storage);
			if (!order) {
				return true;
			}
			col = order[index];
			if (!order) {
				return true;
			}
			array_move(order, index, index_inserting);
			$.tablesorter.storage(formidable_ts, 'tablesorter-' + storage, order, {});
		});
		// Ajuster le storage des tri de colonne
		min = Math.min(index, index_inserting);
		max = Math.max(index, index_inserting);
		sortList = $.tablesorter.storage(formidable_ts, 'tablesorter-savesort');
		if (sortList) {
			sortList=sortList['sortList'];
			$(sortList).each(function(key, value) {
				if (value[0] == index) {
					value[0] = index_inserting;
				} else if (min <= value[0] && value[0] <= max) {
					if (move == 'right') {
						value[0] = value[0] - 1;
					} else {
						value[0] = value[0] + 1;
					}
				}
				sortList[key] = value;
			});
		}

		moving_flag = true;
		$.tablesorter.storage(formidable_ts, 'tablesorter-savesort',{'sortList':sortList}, {});
		$.tablesorter.setFilters(formidable_ts, $.tablesorter.storage(formidable_ts, 'tablesorter-filters'), false );
		formidable_ts.trigger('sorton', [sortList]);

		columnSelector = $.tablesorter.storage(formidable_ts, 'tablesorter-columnSelector');
		$(columnSelector).each( function(key, value) {
			$('#columnSelector input[data-column='+key+']').prop('checked',value).trigger('change');
		});

		$('.formidable_ts-wrapper').addClass('loading');
		moving_flag = false;
		formidable_ts.trigger('pagerUpdate');
	});
};

//https://stackoverflow.com/a/5306832/3206025
function array_move(arr, old_index, new_index) {
    if (new_index >= arr.length) {
        var k = new_index - arr.length + 1;
        while (k--) {
            arr.push(undefined);
        }
    }
    arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
};
/**
 * Réinitialisation de tout, aille, aille, aille
**/
function formidable_ts_restart() {
	$([
		'columnSelector',
		'columnSelector-auto',
		'filters',
		'pager',
		'resizable',
		'savesort',
		'table-original-css-width',
		'table-resized-width',
		'order',
	]).each(function(key, storage) {
			$.tablesorter.storage(formidable_ts, 'tablesorter-'+storage, null);
	});
	location.reload();
}
