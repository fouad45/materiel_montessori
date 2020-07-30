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
 * @author    Boxtal <api@boxtal.com>
 * @copyright 2007-2019 PrestaShop SA / 2018-2019 Boxtal
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Contains code for the shop rest controller.
 */
use Boxtal\BoxtalConnectPrestashop\Util\ShopUtil;
use Boxtal\BoxtalPhp\RestClient;
use Boxtal\BoxtalConnectPrestashop\Controllers\Misc\NoticeController;
use Boxtal\BoxtalConnectPrestashop\Util\ApiUtil;
use Boxtal\BoxtalConnectPrestashop\Util\AuthUtil;
use Boxtal\BoxtalConnectPrestashop\Util\ConfigurationUtil;

/**
 * Shop class.
 *
 * Opens API endpoint to pair.
 */
class BoxtalConnectShopModuleFrontController extends ModuleFrontController
{
    /**
     * Processes request.
     *
     * @void
     */
    public function postProcess()
    {
        $entityBody = Tools::file_get_contents('php://input');

        $route = Tools::getValue('route'); // Get route

        if ('pair' === $route) {
            AuthUtil::authenticate($entityBody);
            $body = AuthUtil::decryptBody($entityBody);

            if (isset($_SERVER['REQUEST_METHOD'])) {
                switch ($_SERVER['REQUEST_METHOD']) {
                    case RestClient::$POST:
                        $this->pairingHandler($body);
                        break;

                    default:
                        break;
                }
            }
        } elseif ('update-configuration' === $route) {
            AuthUtil::authenticateAccessKey($entityBody);
            $body = AuthUtil::decryptBody($entityBody);

            if (isset($_SERVER['REQUEST_METHOD'])) {
                switch ($_SERVER['REQUEST_METHOD']) {
                    case RestClient::$POST:
                        $this->updateConfigurationHandler($body);
                        break;

                    default:
                        break;
                }
            }
        } elseif ('delete-configuration' === $route) {
            AuthUtil::authenticateAccessKey($entityBody);

            if (isset($_SERVER['REQUEST_METHOD'])) {
                switch ($_SERVER['REQUEST_METHOD']) {
                    case RestClient::$POST:
                        $this->deleteConfigurationHandler();
                        break;

                    default:
                        break;
                }
            }
        }
        ApiUtil::sendApiResponse(400);
    }

    /**
     * Endpoint callback.
     *
     * @param array $body request body
     *
     * @void
     */
    public function pairingHandler($body)
    {
        if (null === $body) {
            ApiUtil::sendApiResponse(400);
        }

        $accessKey = null;
        $secretKey = null;
        $callbackUrl = null;
        if (is_object($body) && property_exists($body, 'accessKey')
            && property_exists($body, 'secretKey')) {
            $accessKey = $body->accessKey;
            $secretKey = $body->secretKey;

            if (property_exists($body, 'pairCallbackUrl')) {
                $callbackUrl = $body->pairCallbackUrl;
            }
        }

        if (null !== $accessKey && null !== $secretKey) {
            if (!AuthUtil::isPluginPaired(ShopUtil::$shopGroupId, ShopUtil::$shopId)) { // initial pairing.
                AuthUtil::pairPlugin($accessKey, $secretKey);
                NoticeController::removeNotice(
                    NoticeController::$setupWizard,
                    ShopUtil::$shopGroupId,
                    ShopUtil::$shopId
                );
                NoticeController::addNotice(
                    NoticeController::$pairing,
                    ShopUtil::$shopGroupId,
                    ShopUtil::$shopId,
                    array('result' => 1)
                );
                ApiUtil::sendApiResponse(200);
            } else { // pairing update.
                if (null !== $callbackUrl) {
                    AuthUtil::pairPlugin($accessKey, $secretKey);
                    NoticeController::removeNotice(
                        NoticeController::$pairing,
                        ShopUtil::$shopGroupId,
                        ShopUtil::$shopId
                    );
                    AuthUtil::startPairingUpdate($callbackUrl);
                    NoticeController::addNotice(
                        NoticeController::$pairingUpdate,
                        ShopUtil::$shopGroupId,
                        ShopUtil::$shopId
                    );
                    ApiUtil::sendApiResponse(200);
                } else {
                    ApiUtil::sendApiResponse(403);
                }
            }
        } else {
            NoticeController::addNotice(
                NoticeController::$pairing,
                ShopUtil::$shopGroupId,
                ShopUtil::$shopId,
                array('result' => 0)
            );
            ApiUtil::sendApiResponse(400);
        }
    }

    /**
     * Endpoint callback.
     *
     * @void
     */
    public function deleteConfigurationHandler()
    {
        ConfigurationUtil::deleteConfiguration();
        ApiUtil::sendApiResponse(200);
    }

    /**
     * Endpoint callback.
     *
     * @param object $body request body
     *
     * @void
     */
    public function updateConfigurationHandler($body)
    {
        if (ConfigurationUtil::parseConfiguration($body)) {
            ApiUtil::sendApiResponse(200);
        }

        ApiUtil::sendApiResponse(400);
    }
}
