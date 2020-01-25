$(function() {
	$(".tablesorter").tablesorter({
		widgets: ["zebra","stickyHeaders", "filter","print", "columnSelector", "output", "resizable"],
		widgetOptions: {
			columnSelector_container : $('#columnSelector'),
      print_columns: 's',
      print_rows: 'f',
			print_extraCSS: 'table{font-size:10pt}',
      filter_saveFilters : true,
			output_saveFileName : 'export.csv',
			output_encoding      : 'data:application/octet-stream;charset=utf8,',
			output_delivery: 'download',
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
	$('.output').click(function() {
		$('.tablesorter').trigger('outputTable');
		return false;
	});
});
