<!-- Begin elevateZoom Header block -->
<script type="text/javascript">
	var zoom_type = '{$zoom_type}';
	var zoom_fade_in = {$zoom_fade_in};
    var zoom_fade_out = {$zoom_fade_out};
	var zoom_cursor_type = '{$zoom_cursor_type}';
	{if $language.is_rtl != 1}
	    var zoom_window_pos = {$zoom_window_pos};
	{elseif $zoom_window_pos == 1}
	    var zoom_window_pos = 11;
	{/if}
	var zoom_scroll = {$zoom_scroll};
	var zoom_easing = {$zoom_easing};
	var zoom_tint = {$zoom_tint};
	var zoom_tint_color = '{$zoom_tint_color}';
	var zoom_tint_opacity = {$zoom_tint_opacity};
    var zoom_lens_shape = '{$zoom_lens_shape}';
    var zoom_lens_size  = {$zoom_lens_size};
</script>
<script type="text/javascript">
{if $zoom_product==1}
	function applyElevateZoom(){
		var bigimage = $('.js-qv-product-cover').attr('src'); 
		$('.js-qv-product-cover').elevateZoom({
			zoomType: zoom_type,
			cursor: zoom_cursor_type,
			zoomWindowFadeIn: zoom_fade_in,
			zoomWindowFadeOut: zoom_fade_out,
			zoomWindowPosition: zoom_window_pos,
			scrollZoom: zoom_scroll,
			easing: zoom_easing,
			tint: zoom_tint,
			tintColour: zoom_tint_color,
			tintOpacity: zoom_tint_opacity,
			lensShape: zoom_lens_shape,
			lensSize: zoom_lens_size,
			zoomImage: bigimage{if $zoom_extra_params|strip!=''},
			{$zoom_extra_params nofilter} {/if}
	   });
	}
	$(document).ready(function(e) {
		if($(".zoomContainer").length){
		$(".zoomContainer").remove();	
		}
		applyElevateZoom();
		$(document).on('click','.input-color',function(e) {
            restartElevateZoom();
        });
		$(document).on('click','.js-qv-mask img.thumb',function(e) {
            restartElevateZoom();
        });
	});	
{/if}

	function restartElevateZoom(){
		$(".zoomContainer").remove();
		applyElevateZoom();
	}

{if $zoom_other==1}
	$(document).ready(function(){
		{$zoom_other_code}
	});
{/if}
</script>
<!-- End elevateZoom Header block -->
