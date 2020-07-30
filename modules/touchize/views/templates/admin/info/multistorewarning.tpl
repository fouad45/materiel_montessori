{*
 * 2019 Touchize Sweden AB.
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
{if isset($is_multishop_mode) && is_multishop_mode}
<div id="multistore-warning">
    <div class="panel">
        <div class="row">
            <p class="alert alert-warning">{l s='You cannot manage Touchize Swipe-2-Buy from a "All Shops" or a "Group Shop" context, select a shop you want to edit to continue.' mod='touchize'}</p>
        </div>
        <div class="row">
			{if isset($is_multishop) && $is_multishop && $shop_list && (isset($multishop_context) && $multishop_context & Shop::CONTEXT_GROUP || $multishop_context & Shop::CONTEXT_SHOP)}
				<ul id="header_shop">
					<li class="dropdown">
						{$shop_list nofilter}
					</li>
				</ul>
			{/if}
        </div>
    </div>
</div>
{/if}
