{*
* 2007-2019 PrestaShop
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
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<section class="form-fields">
  <div class="colissimo-mailbox-intro">
    <p>{l s='Enter the address of the parcel to be collected' mod='colissimo'}</p>
  </div>

  <div class="container-fluid">
    <form method="post" id="colissimo-mailbox-return-address">
      <input type="hidden" name="id_colissimo_label" value="{$id_colissimo_label}" />
      <div class="row form-group">
        <label class="form-control-label col-md-3 offset-md-1">{l s='Company' mod='colissimo'}</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="colissimo-company" value="{$address->company}">
        </div>
      </div>
      <div class="row form-group">
        <label class="form-control-label col-md-3 offset-md-1">{l s='Lastname' mod='colissimo'}</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="colissimo-lastname" value="{$address->lastname}">
        </div>
      </div>
      <div class="row form-group">
        <label class="form-control-label col-md-3 offset-md-1">{l s='Firstname' mod='colissimo'}</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="colissimo-firstname" value="{$address->firstname}">
        </div>
      </div>
      <div class="row form-group">
        <label class="form-control-label col-md-3 offset-md-1">{l s='Address 1' mod='colissimo'}</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="colissimo-address1" value="{$address->address1}">
        </div>
      </div>
      <div class="row form-group">
        <label class="form-control-label col-md-3 offset-md-1">{l s='Address 2' mod='colissimo'}</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="colissimo-address2" value="{$address->address2}">
        </div>
      </div>
      <div class="row form-group">
        <label class="form-control-label col-md-3 offset-md-1">{l s='Postcode' mod='colissimo'}</label>
        <div class="col-md-4">
          <input class="form-control" type="text" name="colissimo-postcode" value="{$address->postcode}">
        </div>
      </div>
      <div class="row form-group">
        <label class="form-control-label col-md-3 offset-md-1">{l s='City' mod='colissimo'}</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="colissimo-city" value="{$address->city}">
        </div>
      </div>
      <div class="row form-group">
        <span class="offset-md-4 col-md-8"><b>FRANCE</b></span>
      </div>


      <div class="row form-group">
        <div class="offset-md-4 col-md-6">
          <button class="btn btn-primary colissimo-submit-availability" type="submit" name="submitColissimoAvailability">
            <i class="material-icons icon-spinner-off">loop</i>
            {l s='Check availability' mod='colissimo'}
          </button>
        </div>
      </div>
    </form>
  </div>
</section>
