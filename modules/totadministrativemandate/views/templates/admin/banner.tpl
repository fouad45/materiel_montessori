{*
* @author 202 ecommerce <contact@202-ecommerce.com>
* @copyright  Copyright (c) 202 ecommerce 2014
* @license    Commercial license
*}


<div id="tot_banner_container">
    <table class="banner">
        <tr>
            <td class="module_informations">
                <img src="{$module_dir|escape:'htmlall':'UTF-8'}/logo.png">
                <br /><span class="white"><span>{$module.displayName|escape:'htmlall':'UTF-8'}</span></span>
                <p>{$module.description|escape:'htmlall':'UTF-8'}</p>
            </td>

            <td class="links_container">

                <a href="https://addons.prestashop.com/contact-form.php?id_product=27342" target="_blank" title="{l s='Contact us' mod='totadministrativemandate'}">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/banner/question-mark.png" />
                </a>

                <a href="https://addons.prestashop.com/en/ratings.php" target="_blank" title="{l s='Rate our module' mod='totadministrativemandate'}">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/banner/star.png" />
                </a>

                <a href="https://addons.prestashop.com/en/27_202-ecommerce" target="_blank">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/banner/logo-202-v2.png" id="logo202" />
                </a>

            </td>
        </tr>
    </table>
</div>
