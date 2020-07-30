{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file=$layout}
    {block name='index_wrapper'}
      <section id="content" class="page-home">
        {block name='page_content_top'}{/block}
            <div class="container">
				{hook h="blockPosition1"}
				<div class="container onecateproduct-blockcategories">
					<div class="row">
						<div class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
							{hook h="onecateproductslider"}
						</div>
						<div class="col-lg-8 col-md-7 col-sm-6 col-xs-12">
							{hook h="blockcategories"}
						</div>
					</div>
				</div>
                
                {hook h="tabproductsisotope"}
                {hook h="blockPosition3"}
				{hook h="tabcateslider"}
                {hook h="blockPosition4"}
                {if $page.page_name=='index' && isset($HOOK_SMARTBLOGHOMEPOST) && !empty($HOOK_SMARTBLOGHOMEPOST)} 
                {$HOOK_SMARTBLOGHOMEPOST nofilter}
                {/if}
                {block name='page_content'}
                {$HOOK_HOME nofilter}
            </div>
        {/block}
      </section>
    {/block}
