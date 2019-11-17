$(function() {
	$(".tablesorter").tablesorter({
		widgets: ["zebra","stickyHeaders", "filter","print", "columnSelector"],
		widgetOptions: {
			columnSelector_container : $('#columnSelector'),
      print_columns: 's',
      print_rows: 'f',
			print_extraCSS: 'table{font-size:10pt}',
      filter_saveFilters : true
		}
	});
  $('.print').click(function() {
    $('.tablesorter').trigger('printTable');
  });
});
