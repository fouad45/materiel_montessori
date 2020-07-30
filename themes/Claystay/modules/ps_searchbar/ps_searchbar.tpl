{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- Block search module TOP -->
<div id="_desktop_search_widget"  data-search-controller-url="{$search_controller_url}">
<!--<span class="search_button"></span>-->
<div id="search-toggle" class="searchtoggle"><i class="fa fa-search" aria-hidden="true"></i><i class="material-icons close">&#xE5CD;</i> </div>
<div class="search-container search-widget">   
<form method="get" action="{$search_controller_url}">
<input type="hidden" name="controller" value="search">
<input type="text" name="s" value="{$search_string}" placeholder="{l s='Search' d='Shop.Theme.Catalog'}">
<button type="submit">	
<span class="pst-search-icon"></span>
<i class="fa fa-search" aria-hidden="true"></i>
</button>
</form>
</div>
</div>
<!-- /Block search module TOP -->
