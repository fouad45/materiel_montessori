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
 * Contains code for auth util class.
 */

namespace Boxtal\BoxtalConnectPrestashop\Util;

use Boxtal\BoxtalPhp\ApiClient;
use Boxtal\BoxtalPhp\RestClient;

/**
 * Auth util class.
 *
 * Helper to manage API auth.
 */
class AuthUtil
{
    /**
     * API request validation.
     *
     * @param string $body encrypted body
     *
     * @return mixed
     */
    public static function authenticate($body)
    {
        return null === self::decryptBody($body) ? ApiUtil::sendApiResponse(401) : true;
    }

    /**
     * API request validation with access key check.
     *
     * @param string $body encrypted body
     *
     * @return mixed
     */
    public static function authenticateAccessKey($body)
    {
        $decryptedBody = self::decryptBody($body);
        if (null === $decryptedBody) {
            ApiUtil::sendApiResponse(401);
        }

        if (is_object($decryptedBody) && property_exists($decryptedBody, 'accessKey')
            && self::getAccessKey(ShopUtil::$shopGroupId, ShopUtil::$shopId) === $decryptedBody->accessKey) {
            return true;
        }

        ApiUtil::sendApiResponse(403);
    }

    /**
     * Is plugin paired.
     *
     * @param int $shopGroupId shop group id
     * @param int $shopId shop id
     *
     * @return bool
     */
    public static function isPluginPaired($shopGroupId, $shopId)
    {
        return null !== self::getAccessKey($shopGroupId, $shopId) && null !== self::getSecretKey($shopGroupId, $shopId);
    }

    /**
     * Can use plugin.
     *
     * @return bool
     */
    public static function canUsePlugin()
    {
        if (null === ShopUtil::$shopGroupId && null === ShopUtil::$shopId) {
            return false;
        }

        return false !== self::isPluginPaired(ShopUtil::$shopGroupId, ShopUtil::$shopId)
            && null === ConfigurationUtil::get('BX_PAIRING_UPDATE');
    }

    /**
     * Pair plugin.
     *
     * @param string $accessKey API access key
     * @param string $secretKey API secret key
     *
     * @void
     */
    public static function pairPlugin($accessKey, $secretKey)
    {
        ConfigurationUtil::set('BX_ACCESS_KEY', $accessKey);
        ConfigurationUtil::set('BX_SECRET_KEY', $secretKey);
    }

    /**
     * Start pairing update (puts plugin on hold).
     *
     * @param string $callbackUrl callback url
     *
     * @void
     */
    public static function startPairingUpdate($callbackUrl)
    {
        ConfigurationUtil::set('BX_PAIRING_UPDATE', $callbackUrl);
    }

    /**
     * End pairing update (release plugin).
     *
     * @void
     */
    public static function endPairingUpdate()
    {
        ConfigurationUtil::delete('BX_PAIRING_UPDATE', ShopUtil::$shopGroupId, ShopUtil::$shopId);
    }

    /**
     * Request body decryption.
     *
     * @param string $jsonBody encrypted body
     *
     * @return mixed
     */
    public static function decryptBody($jsonBody)
    {
        $body = json_decode($jsonBody);

        if (null === $body || !is_object($body) || !property_exists($body, 'encryptedKey')
            || !property_exists($body, 'encryptedData')) {
            return null;
        }

        $key = self::decryptPublicKey($body->encryptedKey);

        if (null === $key) {
            return null;
        }

        $data = self::encryptRc4(base64_decode($body->encryptedData), $key);

        return json_decode($data);
    }

    /**
     * Request body decryption.
     *
     * @param mixed $body encrypted body
     *
     * @return mixed
     */
    public static function encryptBody($body)
    {
        $key = self::getRandomKey();
        if (null === $key) {
            return null;
        }

        return json_encode(
            array(
                'encryptedKey' => MiscUtil::base64OrNull(self::encryptPublicKey($key)),
                'encryptedData' => MiscUtil::base64OrNull(
                    self::encryptRc4((is_array($body) ? json_encode($body) : $body), $key)
                ),
            )
        );
    }

    /**
     * Get random encryption key.
     *
     * @return string
     */
    public static function getRandomKey()
    {
        //phpcs:ignore
        $randomKey = openssl_random_pseudo_bytes(200);
        if (false === $randomKey) {
            return null;
        }

        return bin2hex($randomKey);
    }

    /**
     * Encrypt with public key.
     *
     * @param string $str string to encrypt
     *
     * @return array bytes array
     */
    public static function encryptPublicKey($str)
    {
        // phpcs:ignore
        $publicKey = \Tools::file_get_contents(realpath(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR
            . 'resource' . DIRECTORY_SEPARATOR . 'publickey');
        $encrypted = '';
        if (openssl_public_encrypt($str, $encrypted, $publicKey)) {
            return $encrypted;
        }

        return null;
    }

    /**
     * Decrypt with public key.
     *
     * @param string $str string to encrypt
     *
     * @return mixed
     */
    public static function decryptPublicKey($str)
    {
        // phpcs:ignore
        $publicKey = \Tools::file_get_contents(realpath(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR
            . 'resource' . DIRECTORY_SEPARATOR . 'publickey');
        $decrypted = '';
        if (openssl_public_decrypt(base64_decode($str), $decrypted, $publicKey)) {
            return json_decode($decrypted);
        }

        return null;
    }

    /**
     * RC4 symmetric cipher encryption/decryption
     *
     * @param string $str string to be encrypted/decrypted
     * @param array $key secret key for encryption/decryption
     *
     * @return array bytes array
     */
    public static function encryptRc4($str, $key)
    {
        $s = array();
        for ($i = 0; $i < 256; ++$i) {
            $s[$i] = $i;
        }
        $j = 0;
        for ($i = 0; $i < 256; ++$i) {
            $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
        }
        $i = 0;
        $j = 0;
        $res = '';
        $length = strlen($str);
        for ($y = 0; $y < $length; ++$y) {
            //phpcs:ignore
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
            $res .= $str[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
        }

        return $res;
    }

    /**
     * Get access key.
     *
     * @param int $shopGroupId shop group id
     * @param int $shopId shop id
     *
     * @return string
     */
    public static function getAccessKey($shopGroupId, $shopId)
    {
        return ConfigurationUtil::get('BX_ACCESS_KEY', $shopGroupId, $shopId);
    }

    /**
     * Get secret key.
     *
     * @param int $shopGroupId shop group id
     * @param int $shopId shop id
     *
     * @return string
     */
    public static function getSecretKey($shopGroupId, $shopId)
    {
        return ConfigurationUtil::get('BX_SECRET_KEY', $shopGroupId, $shopId);
    }

    /**
     * Get maps token.
     *
     * @return string
     */
    public static function getMapsToken()
    {
        $lib = new ApiClient(
            self::getAccessKey(ShopUtil::$shopGroupId, ShopUtil::$shopId),
            self::getSecretKey(ShopUtil::$shopGroupId, ShopUtil::$shopId)
        );
        //phpcs:ignore
        $response = $lib->restClient->request(RestClient::$POST, ConfigurationUtil::get('BX_MAP_TOKEN_URL'));

        if (!$response->isError() && property_exists($response->response, 'accessToken')) {
            return $response->response->accessToken;
        }

        return null;
    }
}
