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

<section>
  {if $shipment.user_messages}
    <ul class="timeline-user-messages">
      {foreach $shipment.user_messages as $data}
        <li class="timeline-user-message">
          <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon-warning.png"/>
          <div class="user-message-txt">{$data.message nofilter}</div>
        </li>
      {/foreach}
    </ul>
  {/if}
  <div class="timeline-container">
    <div class="timeline-header">
      <div class="timeline-shipment-number-mobile">
        <a href="https://www.laposte.fr/outils/suivre-vos-envois?code={$shipment.parcel_details.parcelNumber|escape:'html':'utf-8'}"
           target="_blank">
          <span>N°{$shipment.parcel_details.parcelNumber|escape:'html':'utf-8'}</span>
        </a>
      </div>
      <div class="timeline-logo">
        <img src="{$colissimo_img_path|escape:'html':'utf-8'}Colissimo_Logo_H.png"/>
      </div>
      <div class="timeline-shipment-number">
        <a href="https://www.laposte.fr/outils/suivre-vos-envois?code={$shipment.parcel_details.parcelNumber|escape:'html':'utf-8'}"
           target="_blank">
          <span>N°{$shipment.parcel_details.parcelNumber|escape:'html':'utf-8'}</span>
        </a>
      </div>
      {if $shipment.parcel_details.customerLogoURL}
        <div class="timeline-merchant-info">
          <span> {l s='- Sent by' mod='colissimo'}</span>
          <img src="{$shipment.parcel_details.customerLogoURL|escape:'html':'utf-8'}"/>
        </div>
      {/if}
      {if $shipment.parcel_details.deliveryLabel}
        <div class="timeline-delivery-type-mobile">
          {$shipment.parcel_details.deliveryLabel|escape:'html':'utf-8'}
          {$shipment.parcel_details.optionDeliveryLabel|escape:'html':'utf-8'}
        </div>
      {/if}
    </div>

    {if $shipment.steps_timeline[1]['status'] != 'STEP_STATUS_ACTIVE'}
      <div class="timeline-step-0">
        <div class="timeline-step-0-icon">
          <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon-box-timeline.png"/>
        </div>
        <div class="timeline-step-0-text">{$shipment.steps_timeline[0]['labelShort']|escape:'html':'utf-8'}</div>
      </div>
    {else}
      {if $shipment.parcel_details.deliveryLabel}
        <div class="timeline-delivery-type">
          <div class="delivery-type-img">
            <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon-box-timeline.png"/>
          </div>
          <div class="delivery-type-text">
            <span>{l s='Type of delivery' mod='colissimo'}</span>
            {$shipment.parcel_details.deliveryLabel|escape:'html':'utf-8'}
            {$shipment.parcel_details.optionDeliveryLabel|escape:'html':'utf-8'}
          </div>
        </div>
      {/if}
      <div class="timeline-dates">
        <div class="timeline-first-step-text">{if $shipment.steps_timeline[1].labelShort}{$shipment.steps_timeline[1].labelShort|escape:'html':'utf-8'}{/if}</div>
        <div class="timeline-last-step-text">{if $shipment.steps_timeline[5].labelShort}{$shipment.steps_timeline[5].labelShort|escape:'html':'utf-8'}{/if}</div>
        <div class="timeline-first-step-date">{if $shipment.steps_timeline[1].date}{$shipment.steps_timeline[1].dateDisplayShort|escape:'html':'utf-8'}{/if}</div>
        <div class="timeline-last-step-date">{if $shipment.steps_timeline[5].date}{$shipment.steps_timeline[5].dateDisplayShort|escape:'html':'utf-8'}{/if}</div>
      </div>
      <div class="timeline-steps">
        <div class="timeline-dots">
          {foreach $shipment.steps_timeline as $key => $step}
            {if $step.stepId}
              <div class="timeline-dot {$step.statusClass|escape:'html':'utf-8'}">
                <div class="round"></div>
                <div class="mobile-round">
                  <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon-box-timeline.png"/>
                </div>
                {if isset($shipment.steps_timeline[$key + 1]) && $shipment.steps_timeline[$key + 1]['status'] != 'STEP_STATUS_INACTIVE'}
                  <div class="line"></div>
                {/if}
              </div>
            {/if}
          {/foreach}
        </div>
        <div class="timeline-mobile-informations">
          <div class="text-short">{$shipment.parcel_details.currentStep.labelShort|escape:'html':'utf-8'}</div>
          <div class="step-date">{$shipment.parcel_details.currentStep.dateDisplay|escape:'html':'utf-8'}</div>
          {if $shipment.parcel_details.currentStep.countryName}
            <div class="step-country">
              <span class="flag flag-{$shipment.parcel_details.currentStep.countryCodeISO|escape:'html':'utf-8'|lower}"></span>
              {$shipment.parcel_details.currentStep.countryName|escape:'html':'utf-8'}
            </div>
          {/if}
          {if $shipment.parcel_details.service.deliveryChoice}
            <p class="reschedule-delivery">
              <a target="_blank"
                 href="http://www.laposte.fr/particulier/modification-livraison?code={$shipment.parcel_details.parcelNumber|escape:'html':'utf-8'}">
                {l s='Reschedule my delivery' mod='colissimo'}
              </a>
            </p>
          {/if}
          {if $shipment.parcel_details.currentStep.stepId == 4 && $shipment.parcel_details.removalPoint && $shipment.parcel_details.removalPoint.countryCodeISO == 'FR'}
            <p class="removal-point">
              <a target="_blank"
                 href="http://www.laposte.fr/particulier/outils/trouver-un-bureau-de-poste/bureau-detail/{$shipment.parcel_details.removalPoint.siteName|replace:'/':''|escape:'html':'utf-8'}/{$shipment.parcel_details.removalPoint.siteCode|escape:'html':'utf-8'}">
                {$shipment.parcel_details.removalPoint.siteName|escape:'html':'utf-8'}
              </a>
              <br>
              <span>{l s='You will need ID to collect your shipment.' mod='colissimo'}</span>
            </p>
          {/if}
        </div>
        <ul class="timeline-steps-details-simple">
          {foreach $shipment.steps_timeline as $key => $step}
            {if $step.stepId}
              <li>
                <p class="text-short">{$step.labelShort|escape:'html':'utf-8'}</p>
                {if $step.countryName}
                  <p class="step-country">
                    <span class="flag flag-{$step.countryCodeISO|escape:'html':'utf-8'|lower}"></span>
                    {$step.countryName|escape:'html':'utf-8'}
                  </p>
                {/if}
                <p class="step-date">{$step.dateDisplay|escape:'html':'utf-8'}</p>
                <p class="text-long">{$step.labelLong|escape:'html':'utf-8'}</p>
                {if $step.stepId == $shipment.parcel_details.currentStep.stepId && $shipment.parcel_details.service.deliveryChoice}
                  <p class="reschedule-delivery">
                    <a target="_blank"
                       href="http://www.laposte.fr/particulier/modification-livraison?code={$shipment.parcel_details.parcelNumber|escape:'html':'utf-8'}">
                      {l s='Reschedule my delivery' mod='colissimo'}
                    </a>
                  </p>
                {/if}
                {if $step.stepId == 4 && $shipment.parcel_details.removalPoint && $shipment.parcel_details.removalPoint.countryCodeISO == 'FR'}
                  <p class="removal-point-name">{$shipment.parcel_details.removalPoint.siteName|escape:'html':'utf-8'}</p>
                  <p class="removal-point">
                    <a target="_blank"
                       href="http://www.laposte.fr/particulier/outils/trouver-un-bureau-de-poste/bureau-detail/{$shipment.parcel_details.removalPoint.siteName|replace:'/':''|escape:'html':'utf-8'}/{$shipment.parcel_details.removalPoint.siteCode|escape:'html':'utf-8'}">
                      {l s='More information' mod='colissimo'}
                    </a>
                    <br>
                    <span>{l s='You will need ID to collect your shipment.' mod='colissimo'}</span>
                  </p>
                {/if}
                <p></p>
              </li>
            {/if}
          {/foreach}
        </ul>
      </div>
      <div class="timeline-steps-details-full">
        <img src="https://www.laposte.fr/_ui/eboutique/images/suivi/sep.png" style="max-width: 100%"/>
        <div class="details-header">
          <div class="details-title">
            <h2>{l s='See all the steps' mod='colissimo'}</h2>
          </div>
          <div class="details-accordion">
            <img class="js-details-accordion" src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon-chevron.png"/>
          </div>
        </div>
        <div class="timeline-steps-details-table">
          <table>
            <thead>
            <tr>
              <th>{l s='Dates' mod='colissimo'}</th>
              <th>&nbsp</th>
              <th>{l s='Steps' mod='colissimo'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $shipment.steps_details as $step}
              <tr>
                <td class="timeline-details-date">
                  {$step.dateDisplay|escape:'html':'utf-8'}
                </td>
                <td class="timeline-details-sep"></td>
                <td class="timeline-details-label">
                  {$step.labelLong|escape:'html':'utf-8'}
                </td>
              </tr>
            {/foreach}
            </tbody>
          </table>
          <div class="table-mobile">
            {foreach $shipment.steps_details as $step}
              <div class="timeline-details-date">
                {$step.dateDisplayShort|escape:'html':'utf-8'}
              </div>
              <div class="timeline-details-label">
                {$step.labelLong|escape:'html':'utf-8'}
              </div>
            {/foreach}
          </div>
        </div>
      </div>
    {/if}
  </div>
</section>
