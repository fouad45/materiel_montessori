$(document).ready(function () {
	var $searchWidget = $('#search_widget');
	var $searchBox    = $('#search_query_top');
	var searchURL     = search_url;
	$.widget('prestashop.psBlockSearchAutocomplete', $.ui.autocomplete, {
		delay: 0,
		_renderItem: function (ul, product) {
			return $("<li>")
					.append($("<a>")
					.append($("<span class='left-search-ajax'>").html('<img src="'+ product.images[0].bySize.small_default.url +'">'))
					.append($("<span class='right-search-ajax'>").html('<span class="search-name-ajax">'+product.name+'</span><span class="price-search-ajax">'+(product.regular_price!=product.price ? '<span class="price-regular-ajax">'+product.regular_price+'</span>' : '' )+'<span class="price-ajax">'+product.price+'</span></span>'))
				).appendTo(ul)
			;
		}
	});
	$searchBox.psBlockSearchAutocomplete({
		delay: 0,
		source: function (query, response) {
			$.get(searchURL,{
				s: query.term,
				category_filter:$("#category_filter").val(),
				resultsPerPage:20
			}, null, 'json')
			.then(function (resp) {
				response(resp.products);
			})
			.fail(response);
		},
		select: function (event, ui) {
			var url = ui.item.url;
			window.location.href = url;
		},
	});
});
