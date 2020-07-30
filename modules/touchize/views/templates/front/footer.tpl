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
 
    <script 
      type="text/javascript"
      src="{$scriptPath|escape:'htmlall':'UTF-8'}/js/slq.js"
    ></script>
    <script type="text/javascript">
      (function(w) {
          w.slqcore = new Slq.StoreFront({$touchfrontConfig|@json_encode|escape:'quotes':'UTF-8' nofilter}).start("#sq-base");
      })(window);
    </script>
    {* No escaping since admin user is allowed to enter pure HTML here. *}
    {if isset($body_html) && $body_html}
      {$body_html nofilter}
    {/if}
    {if !empty($offline_overlay)}
        <style>
            .offline_overlay{
                position:fixed;
                top:0;
                left:0;
                bottom:0;
                right:0;
                z-index: 9999;
                background: rgba(0, 0, 0, .8) no-repeat scroll center center;
            }
            .offline_overlay.content {
                padding: 200px 0;
                top:150px;
                left:50px;
                bottom:150px;
                right:50px;
                background: rgba(255,255,255,1);
                text-align: center;
            }
        </style>
        <div class="offline_overlay">
            <span class="offline_overlay content">
                {$tz_offline_content|escape:'html':'UTF-8'}
                <br>
                <a href="{$tz_pwa_start_url|escape:'html':'UTF-8'}">{$tz_pwa_reload_link_text|escape:'html':'UTF-8'}</a>
            </span>
        </div>
    {/if}
</body>
</html>
