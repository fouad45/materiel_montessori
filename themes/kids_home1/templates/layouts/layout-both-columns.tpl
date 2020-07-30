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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
    {if isset($FIELD_customCSS) && $FIELD_customCSS}
<!-- Start Custom CSS -->
    <style>{$FIELD_customCSS nofilter}</style>
<!-- End Custom CSS -->
{/if}
<script type="text/javascript">
	var LANG_RTL={$language.is_rtl};
	var langIso='{$language.language_code}';
	var baseUri='{$urls.base_url}';
{if (isset($FIELD_enableCountdownTimer) && $FIELD_enableCountdownTimer)}
	var FIELD_enableCountdownTimer=true;
{/if}
{if (isset($FIELD_stickyMenu) && $FIELD_stickyMenu)}
	var FIELD_stickyMenu=true;
{/if}
{if (isset($FIELD_stickySearch) && $FIELD_stickySearch)}
	var FIELD_stickySearch=true;
{/if}
{if (isset($FIELD_stickyCart) && $FIELD_stickyCart)}
	var FIELD_stickyCart=true;
{/if}
{if (isset($FIELD_mainLayout) && $FIELD_mainLayout)}
	var FIELD_mainLayout='{$FIELD_mainLayout}';
{/if}
	var countdownDay='{l s="Day"}';
	var countdownDays='{l s="Days"}';
	var countdownHour='{l s="Hour"}';
	var countdownHours='{l s="Hours"}';
	var countdownMinute='{l s="Min"}';
	var countdownMinutes='{l s="Mins"}';
	var countdownSecond='{l s="Sec"}';
	var countdownSeconds='{l s="Secs"}';
 </script>
  </head>
  <body id="{$page.page_name}" class="{$page.body_classes|classnames} {if isset($FIELD_mainLayout)}{$FIELD_mainLayout}{/if}">
{if isset($FIELD_showPanelTool) && $FIELD_showPanelTool}
	{include file="modules/fieldthemecustomizer/views/templates/front/colortool.tpl"}
{/if}
    {hook h='displayAfterBodyOpeningTag'}

    <main>
      {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
      {/block}
      <header id="header">
        <nav class="header-nav">
            <div class="container">
				<div class="contact-link">
					<i class="fa fa-phone"></i>
					<span class="contact-link-text">07 72 07 66 68</span>
				</div>
				<div class="contact-link-ft">
					<i class="fa fa-envelope"></i>
					<a href="mailto:{$contact_infos.email}">contact@materiel-montessori.fr</a>
				</div>
				<div class="nav1-nav2">
					{hook h='displayNav1'}
					
					{*<div class="nav2">
					<div class="mobile_links">
						<div class="click-nav2">
							<i class="fa fa-cog"></i>
							<span>{l s='Setting'}</span>
						</div>
						<div class="content-nav2">
							
								{hook h='displayNav2'}
							
						</div>
					</div>
					</div>*}
				</div>
            </div>
        </nav>
        <div class="header-top">
            <div class="container">
				<div class="logo_header">
					<a href="{$urls.base_url}">
						<img class="img-responsive logo" src="{$shop.logo}" alt="{$shop.name}">
					</a>
				</div>
				<div id="sticky_top">
					{hook h='displayTop'}
				</div>
            <!-- MEGAMENU -->
				<div id="header_menu" class="visible-lg visible-md">
					<div class="container">
						<div class="row">
							{hook h='displayHeaderMenu'}
						</div>
					</div>
				</div>
				
            </div>
        </div>
		
      </header>
		
        <div id="header_mobile_menu" class="navbar-inactive visible-sm visible-xs">
            <div class="container">
                <div class="fieldmm-nav col-sm-12 col-xs-12">
                    <span class="brand">{l s='Menu list'}</span>
                    <span id="fieldmm-button"><i class="fa fa-reorder"></i></span>
                    {hook h='displayHeaderMenu' fieldmegamenumobile=true}
                </div>
            </div>
        </div>
		<div class="header-top-ft">
			<div class="container">
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
						{hook h="vmegamenu"}
					</div>
				</div>
			</div>
		</div>
 <!--END MEGAMENU -->
 {if $page.page_name!='index'}
  
  {/if}
<!-- SLIDER SHOW -->
{if $page.page_name=='index'}
<div id="field_slideshow">{hook h='fieldSlideShow'}</div>
{/if}
<!--END SLIDER SHOW -->
      {block name='notifications'}
        {include file='_partials/notifications.tpl'}
      {/block}
      <section id="wrapper" class="active_grid">
      <h2 style="display:none !important">.</h2>
       {block name='index_wrapper'}
	   
	   {block name='breadcrumb'}
             {include file='_partials/breadcrumb.tpl'}
          {/block}
        <div class="container">
          {block name='banner_categories'}
    		
          {/block}
          
        {if (isset($layout) && $layout!='layouts/layout-full-width.tpl') || (isset($layout_category) && $layout_category!='layouts/layout-full-width.tpl')  || (isset($layout_details) && $layout_details!='layouts/layout-full-width.tpl') }
		<div class="row">
        {/if}
          {block name="left_column"}
            <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
            {if $page.page_name=='index'}
            
            {else}
                {hook h="displayLeftColumn"}
				{hook h="blockPosition5"}
            {/if}
            </div>
          {/block}

          {block name="content_wrapper"}
            <div id="content-wrapper" class="left-column right-column col-sm-4 col-md-6">
              {block name="content"}
                <p>Hello world! This is HTML5 Boilerplate.</p>
              {/block}
            </div>
          {/block}

          {block name="right_column"}
            <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
            {if $page.page_name=='index'}
            
            {else}
                {hook h="displayRightColumn"}
				{hook h="blockPosition5"}
            {/if}
            </div>
          {/block}
      {if (isset($layout) && $layout!='layouts/layout-full-width.tpl') || (isset($layout_category) && $layout_category!='layouts/layout-full-width.tpl')  || (isset($layout_details) && $layout_details!='layouts/layout-full-width.tpl') }
		  </div>
        {/if}
        
        {block name='product_footer_container'}
        {/block}
        {block name='mapsforcontact'}
        {/block}
        </div>
       {/block} 
      </section>

      <footer id="footer">
        {if isset($HOOK_FIELDBRANDSLIDER) && !empty($HOOK_FIELDBRANDSLIDER)}
            <div class ="Brands-block-slider">
            <div class="container">
                {$HOOK_FIELDBRANDSLIDER nofilter}
            </div>
            </div>
			
			<div class="container">
				<div class="footer-newsletter-social">
					{hook h='blockFooter3'}
					{hook h='blockFooter4'}
				</div>
				{hook h="blockPosition2"}
			</div>
			
        {/if}
        <div class="footer-container">
            
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        {hook h='blockFooter1'}
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                {hook h='blockFooter2'}
            </div>
        </div>
        <div id="back-top"><a href="javascript:void(0)" class="mypresta_scrollup hidden-phone"><i class="fa fa-chevron-up"></i></a></div>
        {if isset($FIELD_customJS) && $FIELD_customJS}
        <!-- Start Custom JS -->
            <script type="text/javascript">{$FIELD_customJS nofilter}</script>
        <!-- End Custom JS -->
        {/if}
      </footer>

    </main>
    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}
    {hook h='displayBeforeBodyClosingTag'}
  </body>

</html>
