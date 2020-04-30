$(function() {
	$(".tablesorter").tablesorter({
		widgets: ["zebra","stickyHeaders", "filter","print", "columnSelector", "output", "resizable", "reorder"],
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
			resizable_addLastColumn: true
		}
	}).bind('filterEnd', function(event, config){
		total = $(this).find('tbody tr').length;
		filtres = $(this).find('tbody tr.filtered').length;
		$('#total').text(total-filtres);
		}
	);
  $('.print').click(function() {
    $('.tablesorter').trigger('printTable');
  });
	type_export = 'csv'
	filename = 'nom';
	$('.output').click(function() {
		filename = $('table.tablesorter').data('identifiant');
		type_export = $(this).val();
		$('.tablesorter').trigger('outputTable');
		return false;
	});
	$('.reset').click(function() {
		$('.tablesorter').trigger('filterReset');
	});
});
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

$.tablesorter.defaults.textExtraction = function(node, table, cellIndex){
    return $(node).attr('data-sort-value') || $(node).text();
}

function call_formidable_tablesorter_export(config, data, url) {
	var form = $('<form></form>').attr('action', url_action_formidable_tablesorter_export).attr('method', 'post');
	form.append($("<input></input>").attr('type', 'hidden').attr('name', 'data').attr('value', data));
	form.append($("<input></input>").attr('type', 'hidden').attr('name', 'type_export').attr('value', type_export));
	form.append($("<input></input>").attr('type', 'hidden').attr('name', 'filename').attr('value', filename));
	form.appendTo('body').submit().remove();
	return false;
}
