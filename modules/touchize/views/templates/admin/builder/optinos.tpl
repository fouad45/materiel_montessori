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

{foreach from=$allowed_items item=category}
	<option value="{$category.id_category|escape:'htmlall':'UTF-8'}" >{"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$category.level_depth} {$category.name|escape:'htmlall':'UTF-8'}</option>
	{if isset($category.children) && $category.children}
		{include file="{$template_dir}builder/optinos.tpl" allowed_items=$category.children }
	{/if}
{/foreach}