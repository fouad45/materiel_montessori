/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
(function($) {

if($('input[name="address2"]').length)
	$('input[name="address2"]').closest('.form-group').remove();

})(jQuery);