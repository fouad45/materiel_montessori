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

<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "Organization",
    "name": "{$shop_name|escape:'html':'UTF-8'}",
    "url": "{$link->getPageLink('index', true)|escape:'html':'UTF-8'}",
    "logo": {
        "@type": "ImageObject",
        "url": "{$logo_url|escape:'html':'UTF-8'}"
    },
    "sameAs": [{if isset($seo_same_as)}{foreach from=$seo_same_as item=site name=seos}"{$site|escape:'html':'UTF-8'}"{if $smarty.foreach.seos.last}{else},{/if}{/foreach}{/if}]
}
</script>
<script   type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "WebSite",
    "url": "{$link->getPageLink('index', true)|escape:'html':'UTF-8'}",
    "image": {
        "@type": "ImageObject",
        "url": "{$logo_url|escape:'html':'UTF-8'}"
    },
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{'--search_term_string--'|str_replace:'{search_term_string}':$link->getPageLink('search',true,null,['search_query'=>'--search_term_string--'])|escape:'html':'UTF-8'}",
        "query-input": "required name=search_term_string"
    }
}
</script>


