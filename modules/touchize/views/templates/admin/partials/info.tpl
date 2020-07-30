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

 <div class="panel touchsize-info">
  <div class="panel-heading">
    <i class="icon-cogs"></i>
      {l s='Info' mod='touchize'}
  </div>
</div>

<script>
  $(document).ready(function () {
    if ($('.touchsize-info').parent().hasClass('form-wrapper')
    || $('.touchsize-info').parents('#configuration_form').length > 0)
      $("#content .panel").eq(1).before($(".touchsize-info").detach());
  });
</script>