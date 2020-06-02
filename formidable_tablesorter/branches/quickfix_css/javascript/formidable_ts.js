/** Appeler jquery table sorter
 * et lier les actions
**/
formidable_ts = '';
$(function() {
	formidable_ts = $(".tablesorter");
	formidable_ts.tablesorter({
		widgets: [
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
      print_columns: 's',
      print_rows: 'f',
			print_extraCSS: 'table{font-size:10pt}',
			filter_filterLabel: filter_filterLabel,
			filter_placeholder: {search:filter_placeholder},
      filter_saveFilters : true,
			output_separator: 'array',
			output_delivery: 'download',
			stickyHeaders_xScroll : '.formidable_ts-wrapper',
			output_callback: function(config, data, url) {
				return call_formidable_ts_export(config, data, url);
			},
			resizable_addLastColumn: true
		}
	}).bind('filterEnd', function(event, config) {
		total = $(this).find('tbody tr').length;
		filtres = $(this).find('tbody tr.filtered').length;
		$('#total').text(total-filtres);
		}
	).on('columnUpdate', function() {
		formidable_ts_add_reorder_arrows();
	});;
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
	formidable_ts_init_reorder();
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

/** Extraction de textes, notamment pour le tri **/
$.tablesorter.defaults.textExtraction = function(node, table, cellIndex){
    return $(node).attr('data-sort-value') || $(node).text();
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

/** Customisation du filtrage **/
// Filtrer sur les textes, pas sur data-sort-value
$.tablesorter.filter.types.start = function(config, data) {
	data.exact = data.$cells[data.index];
	data.exact = data.exact.innerText;
	data.iExact = data.exact.toLowerCase();
	return null;
}

/** Reordonnnancement des colonnes
 * Note sur le stockage en local storage :
 * - les filtres sont notés selon l'ordre EFFECTIF des colonnes
 * - les tri sont stockés selon l'attribut data-column effectif des colonnes MAIS par contre il faut les passer au triger sorton selon la position des colonnes <!>
 * - NE SURTOUT PAS MODIFIER data-column
 * **/

function formidable_ts_post_reorder_refresh_view() {
	// Mettre à jour tout les widgets
	formidable_ts.trigger('updateAll');
	formidable_ts.trigger('applyWidgets');
	formidable_ts_post_reorder_set_columnSelector();
	// 2 fois car le colonne selecteur peut avoir des impacts sur les css des autres widgets, mais il faut que les autres widgets soit rafraichi pour que le le refresh du CS fonction :(
	formidable_ts.trigger('updateAll');
	formidable_ts.trigger('applyWidgets');
	formidable_ts_add_reorder_arrows();
}

// Stockage des positions
function formidable_ts_post_reorder_storage() {
	headers = formidable_ts.find('.tablesorter-headerRow th');
	positions = [];
	headers.each(function () {
		index = $(this).index();
		positions.push({
			'original' : $(this).attr('data-column-original-position'),
			'final' : index
			});// Tableau position final  => position original
		}
	);
	$.tablesorter.storage(formidable_ts, 'tablesorter-reorder', positions, {});
}
//Retrouver le tri
function formidable_ts_post_reorder_set_sorting() {
	// Retrouver le tri
	sortList = $.tablesorter.storage(formidable_ts, 'tablesorter-savesort')['sortList'];
	formidable_ts.trigger('sortReset');
	sortList = formidable_ts_reorder_sortList_update_position(sortList);
	formidable_ts.trigger('sorton', [sortList]);

}
// Prend une sortList
// La parcourt et la modifie de manière à donner le bon index suivant le nouvel ordre post-déplacement de colonne
// Pour chaque entrée, trouve la bonne position avec nouveal indexation
function formidable_ts_reorder_sortList_update_position(sortList) {
	$(sortList).each(function(key, value) {
		console.log(value);
		selector = '.tablesorter-ignoreRow th[data-column='+value[0]+']';
		sort = value[1];
		new_position = $(selector, formidable_ts).index();
		value = [new_position, sort];
		sortList[key] = value;
	});
	return sortList
}

// Mettre à jour le colonne selector
function formidable_ts_post_reorder_set_columnSelector() {
	th = $('.tablesorter-headerRow th', formidable_ts);
	th.each(function() {
		selector = '#columnSelector label[data-column='+$(this).attr('data-column')+'] span';
		$(selector).text($(this).text());
	});
	formidable_ts.trigger('refreshColumnSelector', 'selectors');
}

function formidable_ts_post_reorder_set_filter(filter) {
	// Reinitialisation des filtres
	formidable_ts.trigger('filterResetSaved');
	formidable_ts.trigger('filterReset');
	console.log('Filter post reorder :');
	console.log(filter);
	$.tablesorter.setFilters(formidable_ts, filter, true);
}
// function appelé au tout début du chargement de formidable table_sorter
function formidable_ts_init_reorder() {
	$('[data-column]').each(function() {
		$(this).attr('data-column-original-position',$(this).attr('data-column'));
	});
	// Avoir directement sur le label les infos de data-column-position
	$('#columnSelector label:not(#columnSelectorCheckAll)').each(function() {
		$(this).attr('data-column',$('input', this).attr('data-column'));
	});
	formidable_ts_restore_reorder();
	formidable_ts_post_reorder_refresh_view();
	formidable_ts_post_reorder_storage();
}

// Au début du chargement, reordonnancer les colonnes
function formidable_ts_restore_reorder() {
	positions = $.tablesorter.storage(formidable_ts, 'tablesorter-reorder');
	// Et le remplir
	if (positions) {
		// Faire un clone de l'original, qui servira de modèle
		copy_ts = formidable_ts.clone();
		// Vider l'original
		$('tr', formidable_ts).empty();

		// Et le reremplir
		rows_copy = copy_ts.find('tr');
		rows_original = formidable_ts.find('tr');
		for (column of positions) {
			original = column['original'];
			row = 0;
			rows_original.each(function() {
					row_copy = rows_copy.eq(row);
					td_ou_th = row_copy.children()[original];
					if (td_ou_th) {
						$(this).append(td_ou_th.outerHTML);
					}
					row++;
				}
			);
		}
	}
}

reorder = 0
// Ajout des flèches au chargement
function formidable_ts_add_reorder_arrows() {
	$('.move-arrows').remove();
	formidable_ts_sticky = $('#'+formidable_ts.attr('id')+'-sticky');
	tables = [formidable_ts_sticky, formidable_ts];
	for (table of tables) {
		th = $('.tablesorter-ignoreRow th', table).not('.filtered');
		th.each(function (index) {
			$(this).prepend('<div class="move-arrows"></div>');
			if (index == 0) {
				$('.move-arrows', this).addClass('first');
				$('.move-arrows', this).prepend('<a class="right">&#x27A1;</a>');
			} else if (index != th.length -1) {
				$('.move-arrows', this).prepend('<a class="left">&#x2B05;</a> <a class="right">&#x27A1;</a>');
			} else {
				$('.move-arrows', this).prepend('<a class="left">&#x2B05;</a>');
			}
		});
	}
	$('.move-arrows a').click(function() {
		filter = [];
		reorder++;
		console.log('start reorder ' + reorder)
		var v1 = performance.now();
		th = $(this).parent().parent();
		tr = th.parent();
		index = th.index();
		if ($(this).hasClass('left')) {
			prev = th.prevAll(':not(.filtered)').first();
			index_inserting = prev.index();
			inserting = 'before';
		} else {
			next = th.nextAll(':not(.filtered)').first();
			index_inserting = next.index();
			inserting = 'after';
		}
		formidable_ts_sticky = $('#'+formidable_ts.attr('id')+'-sticky');
		tables = [formidable_ts_sticky, formidable_ts];
		for (table of tables) {
			$('tr', table).each(function(){
				cells = $(this).children();
				this_cell = cells.eq(index).clone(true);
				cells.eq(index).remove();
				if (inserting == 'after') {
					cells.eq(index_inserting).after(this_cell);
				} else {
					cells.eq(index_inserting).before(this_cell);
				}
			});
		}
		$('.tablesorter-filter', formidable_ts).each(function() {
			filter.push($(this).val());
		});
		var v2 = performance.now();
		console.log("reorder time  taken = "+(v2-v1)+"milliseconds");
		console.log('start post reorder ' + reorder)
		formidable_ts_post_reorder_set_filter(filter);
		formidable_ts_post_reorder_storage();
		formidable_ts_post_reorder_set_sorting();
		formidable_ts_post_reorder_refresh_view();
		var v3 = performance.now();
		console.log('end reorder ' + reorder)
		console.log("post reorder time  taken = "+(v3-v2)+"milliseconds");
		console.log("total time  taken = "+(v3-v1)+"milliseconds");
	});
}

/**
 * Réinitialisation de tout, aille, aille, aille
**/
function formidable_ts_restart() {
	$([
		'columnSelector',
		'columnSelector-auto',
		'filters',
		'reorder',
		'resizable',
		'savesort',
		'table-original-css-width',
		'table-resized-width'
	]).each(function(key, storage) {
			$.tablesorter.storage(formidable_ts, 'tablesorter-'+storage, null);
	});
	location.reload();
}
