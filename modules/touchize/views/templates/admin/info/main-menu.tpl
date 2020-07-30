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
<script type="text/javascript">
var botabPath = "{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?botab=3";
var QRPath = "{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?preview=qrp3";
var previewDisplayPath = "{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?touchize=yes";
</script>
<div class="panel">
    <div class="panel-heading">
        <i class="icon-info"></i>
        {l s='Info' mod='touchize'}
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                {l s='Touchize Commerce handles menus in a different way to fit mobile devices better.' mod='touchize'}<br><br>
                <strong>{l s='Link Menu' mod='touchize'}</strong><br>
                {l s='The Link menu – content not directly involved in the customers shopping process, such as contact us, language switching etc.' mod='touchize'}<br>
                {l s='The link menu (☰) is the menu shown to the top right, also known as “Hamburgermenu”.' mod='touchize'}<br><br>
                {l s='You can add a new item to the Link menu by pressing the “Add new” button top right.' mod='touchize'}<br>
                {l s='After you have added a link menu item, you can change the order by dragging them up and down (click and drag on the + sign).' mod='touchize'}
            </div>
            <div class="col-md-4 text-center">
                <img src="{$img_dir|escape:'htmlall':'UTF-8'}link-menu.png" alt="link-menu" style="max-width: 80%;">
            </div>
        </div>
    </div>
</div>
