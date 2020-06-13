/** Appeler jquery table sorter
 * et lier les actions
**/
formidable_ts = '';
$(function() {
	formidable_ts = $(".tablesorter");
	formidable_ts.tablesorter({
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
      pager_ajaxObject: {
        type: 'GET', // default setting
        dataType: 'json'
      },
			resizable_addLastColumn: true
		}
	})
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





/**
 * Réinitialisation de tout, aille, aille, aille
**/
function formidable_ts_restart() {
	$([
		'columnSelector',
		'columnSelector-auto',
		'filters',
		'resizable',
		'savesort',
		'table-original-css-width',
		'table-resized-width'
	]).each(function(key, storage) {
			$.tablesorter.storage(formidable_ts, 'tablesorter-'+storage, null);
	});
	location.reload();
}
