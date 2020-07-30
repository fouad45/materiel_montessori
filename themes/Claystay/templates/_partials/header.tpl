{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{block name='header_nav'}
<nav class="header-nav">
	<div class="container">
      <div class="nav-inner">  
		<div class="hidden-md-down">
			<div class="left-nav">
				{hook h='displayNav1'}
			</div>	
			<div class="right-nav">
			{hook h='displayNav2'}
				
				{hook h='displayNav'}
			</div>
		</div>
		<div class="hidden-lg-up text-xs-center mobile">
			<div class="float-xs-left" id="menu-icon">
				<i class="material-icons menu-open">&#xE5D2;</i>
				<i class="material-icons menu-close">&#xE5CD;</i>			  
			</div>
			<div class="top-logo" id="_mobile_logo"></div>
			<div class="mobile-top-action">
				<div class="float-xs-right" id="_mobile_cart"></div>
				<div class="float-xs-right" id="_mobile_user_info"></div>
				<div class="float-xs-right" id="mobile_search">
					<div class="float-xs-right search_toggle" id="search_toggle">
						<i class="fa fa-search" aria-hidden="true"></i>
						<i class="fa fa-times" aria-hidden="true"></i>
					</div>
					<div class="float-xs-right search-container search-widget" id="_mobile_search_widget"></div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
      </div>
	</div>
</nav>
{/block}
{block name='header_top'}
	<div class="header-top">
		<div class="container">		
			{hook h='displayTopSearch'}
			<div class="header_logo hidden-md-down" id="_desktop_logo">
				<a href="{$urls.base_url}">
					<img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
				</a>
			</div>
			{hook h='displayTop'}
			<div id="mobile_top_menu_wrapper" class="row hidden-lg-up" style="display:none;">
				<div class="js-top-menu mobile" id="_mobile_vertical_top_menu"></div>
				<div class="js-top-menu mobile" id="_mobile_top_menu"></div>
				<div class="js-top-menu-bottom">				
					<div id="_mobile_language_selector"></div>
					<div id="_mobile_currency_selector"></div>
				</div>
			</div>
		</div>
	</div>	
{/block}
<div class="nav-bar">
	<div class="container">
		
			{hook h='displayNavFullWidth'}
	</div>
</div>
