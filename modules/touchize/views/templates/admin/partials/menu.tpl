{*
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
*}

<ul class="clearfix touchsize-menu">
  {foreach from=$items item=element}
    <li class="{if $element.current}current {/if} {if $element.hide}hide {/if}">
      <a {if $element.license}class="tz-license"{/if} href="{$element.link|escape:'html':'UTF-8'}" title="{l s=$element.text mod='touchize'}" {if $element.blank}target="_blank"{/if}>{l s=$element.text mod='touchize'}</a>
    </li>
  {/foreach}
</ul>

<script type="text/javascript">
  $(document).ready(function () {
    var i = $('.touchsize-info').length > 0 ? 1 : 0;

    if ($('.touchsize-menu').parent().hasClass('form-wrapper')
    || $('.touchsize-menu').parents('#configuration_form').length > 0)
      $("#content .panel").eq(0).before($(".touchsize-menu").detach());
  });
</script>
<!-- Start of touchize Zendesk Widget script -->
<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=1a0ec4a5-1fcd-4941-88bc-cfe8fd0305a3"> </script>
<script type="text/javascript">
window.zESettings = {
  webWidget: {
    offset: {
      horizontal: '100px',
      vertical: '15px'
    }
  }
};
</script>
<!-- End of touchize Zendesk Widget script -->
