$(function() {
	formidable_ts = $(".tablesorter");
});
$(function() {
	formidable_ts.tablesorter({
		widgets: ["zebra","stickyHeaders", "filter","print", "reorder", "columnSelector", "output", "resizable"],
		widgetOptions: {
			columnSelector_container : $('#columnSelector'),
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
				formidable_table_sorter_post_reorder();
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

/** Extraction de textes, notamment pour le tri **/
$.tablesorter.defaults.textExtraction = function(node, table, cellIndex){
    return $(node).attr('data-sort-value') || $(node).text();
}

/** Fonctions d'export **/
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


function formidable_table_sorter_post_reorder() {
	$('#columnSelector').empty();
}
