<div id="moda_popupnewsletter" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="{if $field_ppp.FIELD_WIDTH}max-width:{$field_ppp.FIELD_WIDTH}px;{/if}">
    <div class="modal-content">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<div class="fieldpopupnewsletter" style="{if $field_ppp.FIELD_WIDTH}width:{$field_ppp.FIELD_WIDTH}px;{/if}{if $field_ppp.FIELD_HEIGHT}height:{$field_ppp.FIELD_HEIGHT}px;{/if}{if $field_ppp.FIELD_BG == 1 && !empty($field_ppp.FIELD_BG_IMAGE)}background-image: url({$field_ppp.FIELD_BG_IMAGE});{/if}">
	{if $field_ppp.FIELD_NEWSLETTER == 1}
	<div id="newsletter_block_popup" class="block">
		<div class="block_content">
		{if isset($msg) && $msg}
			<p class="{if $nw_error}warning_inline{else}success_inline{/if}">{$msg}</p>
		{/if}
			<form method="post">
                            {if $field_ppp.FIELD_TITLE}<div class="popup_title">{$field_ppp.FIELD_TITLE|stripslashes nofilter}</div>{/if}
                            
                            {if isset($field_ppp.FIELD_COUNTDOWN_POPUP) && !empty($field_ppp.FIELD_COUNTDOWN_POPUP) && $field_ppp.FIELD_COUNTDOWN_POPUP != '0000-00-00 00:00:00'}
                                <span class="item-countdown">
                                    <span class="bg_tranp"></span>
                                    <span class="item-countdown-time" data-time="{$field_ppp.FIELD_COUNTDOWN_POPUP}"></span>
                                </span>
                            {/if}
                            <div class="send-response"></div>
                            <input class="inputNew" id="newsletter-input-popup" type="text" name="email" placeholder="{l s='Enter Email Address Here' mod='fieldpopupnewsletter'}"/>
                            <div class="send-reqest button_unique main_color_hover">{l s='Sent me a 50% coupon  now' mod='fieldpopupnewsletter'}</div>
							<div class="newsletter_block_popup-bottom">
								<input id="newsletter_popup_dont_show_again" type="checkbox">
								<label for="newsletter_popup_dont_show_again">{l s='Don\'t show popup again' mod='fieldpopupnewsletter'}</label>
							</div>
							
			</form>
		</div>
		{if $field_ppp.FIELD_TEXT}<div class="popup_text"><div class="social_footer">{$field_ppp.FIELD_TEXT|stripslashes nofilter}{hook h="blockPosition6"}</div></div>{/if}
		
                
	</div>
	{/if}
</div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
var field_width={$field_ppp.FIELD_WIDTH};
var field_height={$field_ppp.FIELD_HEIGHT};
var field_newsletter={$field_ppp.FIELD_NEWSLETTER};
var field_path='{$field_ppp.FIELD_PATH}';
</script>
