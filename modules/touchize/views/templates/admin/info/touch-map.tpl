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
var botabPath = "{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?botab=5";
var QRPath = "{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?preview=qrp5";
var previewDisplayPath = "{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?touchize=yes";
</script>
<div class="panel">
    <div class="panel-heading">
        <i class="icon-info"></i>
        {l s='Info' mod='touchize'}
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <p>{l s='Touchize Commerce works with banners in a way optimized for mobile viewing.' mod='touchize'}</p>
                <p>{l s='To add a new banner, simply go to the top right corner and press “Add new banner”.' mod='touchize'}</p>
            </div>
            <div class="col-md-3 text-center">
                <img src="{$img_dir|escape:'htmlall':'UTF-8'}banner.png" alt="link-menu" style="max-width: 60%;">
                <br>
                <p>{l s='Static banner' mod='touchize'}</p>
            </div>
            <div class="col-md-3 text-center">
                <img src="{$img_dir|escape:'htmlall':'UTF-8'}interactive-banner.png" alt="link-menu" style="max-width: 60%;">
                <br>
                <p>{l s='Interactive banner' mod='touchize'}</p>
            </div>



        </div>
        <br><br>

    </div>
</div>
