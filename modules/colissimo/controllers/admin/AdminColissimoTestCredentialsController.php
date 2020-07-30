<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class AdminColissimoTestCredentialsController
 *
 * Ajax processes:
 *  - testWidgetCredentials
 *  - testWSCredentials
 *
 */
class AdminColissimoTestCredentialsController extends ModuleAdminController
{
    /** @var Colissimo $module */
    public $module;

    /**
     * AdminColissimoTestCredentialsController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        parent::__construct();
        $this->module->logger->setChannel('TestCredentials');
    }

    /**
     *
     */
    public function ajaxProcessTestWidgetCredentials()
    {
        $credentials = array(
            'contract_number' => Tools::getValue('COLISSIMO_ACCOUNT_LOGIN'),
            'password'        => Tools::getValue('COLISSIMO_ACCOUNT_PASSWORD'),
            'force_endpoint'  => Tools::getValue('COLISSIMO_WIDGET_ENDPOINT'),
        );
        $this->module->logger->info('Test widget', $credentials);
        $request = new ColissimoWidgetAuthenticationRequest($credentials);
        $client = new ColissimoClient();
        $client->setRequest($request);
        //@formatter:off
        $returnError = array(
            'errors'  => true,
            'message' => $this->module->l('Widget could not be reached at the moment. Please verify the url or try again later', 'AdminColissimoTestCredentialsController'),
        );
        $returnSuccess = array(
            'errors'  => false,
            'message' => $this->module->l('Webservice connection is working.', 'AdminColissimoTestCredentialsController'),
        );
        //@formatter:on
        try {
            /** @var ColissimoWidgetAuthenticationResponse $response */
            $response = $client->request();
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $this->ajaxDie(json_encode($returnError));
        }
        if ($response->token) {
            $this->ajaxDie(json_encode($returnSuccess));
        } else {
            $this->ajaxDie(json_encode($returnError));
        }
    }

    /**
     *
     */
    public function ajaxProcessTestWSCredentials()
    {
        $credentials = array(
            'contract_number' => Tools::getValue('COLISSIMO_ACCOUNT_LOGIN'),
            'password'        => Tools::getValue('COLISSIMO_ACCOUNT_PASSWORD'),
        );
        $output = array(
            'x'                  => 0,
            'y'                  => 0,
            'outputPrintingType' => 'PDF_A4_300dpi',
        );
        $senderAddr = array(
            'address' => array(
                'companyName' => 'Test Company',
                'line2'       => '353 Avenue Jean Jaurès',
                'countryCode' => 'FR',
                'city'        => 'Lyon',
                'zipCode'     => '69007',
            ),
        );
        $addresseAddr = array(
            'address' => array(
                'lastName'    => 'Test lastname',
                'firstName'   => 'Test firstname',
                'line2'       => '111 Boulevard Brune',
                'countryCode' => 'FR',
                'city'        => 'Paris',
                'zipCode'     => '75014',
            ),
        );
        $shipmentOptions = array(
            'weight' => 1,
        );
        $shipmentServices = array(
            'productCode' => 'DOM',
            "depositDate" => date('Y-m-d'),
        );
        $this->module->logger->info('Test WS', $credentials);
        $testWS = new ColissimoCheckGenerateLabelRequest($credentials);
        $testWS->setOutput($output)
               ->setSenderAddress($senderAddr)
               ->setAddresseeAddress($addresseAddr)
               ->setShipmentOptions($shipmentOptions)
               ->setShipmentServices($shipmentServices)
               ->buildRequest();

        $client = new ColissimoClient();
        $client->setRequest($testWS);
        $returnError = array(
            'errors'  => true,
            'message' => $this->module->l('Please verify your credentials.', 'AdminColissimoTestCredentialsController'),
        );
        //@formatter:off
        $returnSuccess = array(
            'errors'  => false,
            'message' => $this->module->l('Your credentials have been verified successfully.', 'AdminColissimoTestCredentialsController'),
        );
        //@formatter:on
        try {
            /** @var ColissimoCheckGenerateLabelResponse $response */
            $response = $client->request();
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $this->ajaxDie(json_encode($returnError));
        }
        if ($response->messages[0]['id'] != 0) {
            $this->ajaxDie(json_encode($returnError));
        }
        $this->ajaxDie(json_encode($returnSuccess));
    }
}
