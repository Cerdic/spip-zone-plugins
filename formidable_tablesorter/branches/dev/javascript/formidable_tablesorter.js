/** Appeler jquery table sorter
 * et lier les actions
**/
formidable_ts = '';
$(function() {
	formidable_ts = $(".tablesorter");
	formidable_ts.tablesorter({
		widgets: ["zebra","stickyHeaders", "filter","print", "reorder", "columnSelector", "output", "resizable", "savesort"],
		widgetOptions: {
			columnSelector_container: $('#columnSelector'),
			columnSelector_mediaquery: false,
      print_columns: 's',
      print_rows: 'f',
			print_extraCSS: 'table{font-size:10pt}',
			filter_filterLabel: filter_filterLabel,
			filter_placeholder: {search:filter_placeholder},
      filter_saveFilters : true,
			output_separator: 'array',
			output_delivery: 'download',
			output_saveFileName: 'toto.xlsx',
			output_callback: function(config, data, url) {
				return call_formidable_tablesorter_export(config, data, url);
			},
			reorder_complete : function () {
				formidable_ts_post_reorder();
			},
			resizable_addLastColumn: true
		}
	}).bind('filterEnd', function(event, config) {
		total = $(this).find('tbody tr').length;
		filtres = $(this).find('tbody tr.filtered').length;
		$('#total').text(total-filtres);
		}
	);
  $('.print').click(function() {
    formidable_ts.trigger('printTable');
  });
	$('.output').click(function() {
		filename = $('table.tablesorter').data('identifiant');
		type_export = $(this).val();
		formidable_ts.trigger('outputTable');
		return false;
	});
	$('.reset').click(function() {
		formidable_ts.trigger('filterReset');
	});
	formidable_ts_init_reorder();
	formidable_ts_add_check_all_button();
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
			$('#columnSelector input[data-column]').prop('checked',true);
			formidable_ts.trigger('refreshColumnSelector');
		} else {
			input.attr('class');
			span.text(checkAll);
			$('#columnSelector input[data-column]').prop('checked',false);
			formidable_ts.trigger('refreshColumnSelector');
		}
	});
}

/** Extraction de textes, notamment pour le tri **/
$.tablesorter.defaults.textExtraction = function(node, table, cellIndex){
    return $(node).attr('data-sort-value') || $(node).text();
}

/** Fonctions d'export tableur **/
function call_formidable_tablesorter_export(config, data, url) {
	var form = $('<form></form>').attr('action', url_action_formidable_tablesorter_export).attr('method', 'post');
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

/** Reordonnnancement des colonnes **/

// Après le réordonnancement, reinitialiser le column selecteur + sauver les infos sur l'état
function formidable_ts_post_reorder() {
	$('#columnSelector').empty();
	headers = formidable_ts.find('.tablesorter-headerRow th');
	positions = [];
	headers.each(function () {
		positions.push({
			'original' : $(this).attr('data-column-original-position'),
			'final' : $(this).attr('data-column')
			});// Tableau position final  => position original
		}
	);
	$.tablesorter.storage(formidable_ts, 'tablesorter-reorder', positions, {});
}

// Au début du chargement, reordonnancer les colonnes
function formidable_ts_init_reorder() {
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
					$(this).append(td_ou_th.outerHTML);
					row++;
				}
			);
		}
		// Reinitialiser tout
		formidable_ts.trigger('resetToLoadState');
	}
}


