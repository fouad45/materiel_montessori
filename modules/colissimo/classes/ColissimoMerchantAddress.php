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
 * Class ColissimoMerchantAddress
 */
class ColissimoMerchantAddress
{
    /** @var array $types */
    private static $types = array('sender', 'return');

    /** @var string $type */
    public $type;

    /** @var string $companyName */
    public $companyName;

    /** @var string $lastName */
    public $lastName;

    /** @var string $firstName */
    public $firstName;

    /** @var string $line0 */
    public $line0;

    /** @var string $line1 */
    public $line1;

    /** @var string $line2 */
    public $line2;

    /** @var string $line3 */
    public $line3;

    /** @var string $countryCode */
    public $countryCode;

    /** @var string $city */
    public $city;

    /** @var string $zipCode */
    public $zipCode;

    /** @var string $phoneNumber */
    public $phoneNumber;

    /** @var string $email */
    public $email;

    /**
     * ColissimoMerchantAddress constructor.
     * @param string $type
     * @param array  $addressArray
     */
    public function __construct($type = 'sender', $addressArray = array())
    {
        if (!in_array($type, self::$types)) {
            $this->type = 'sender';
        } else {
            $this->type = $type;
        }
        if (!$addressArray) {
            $addressString = Configuration::get('COLISSIMO_'.Tools::strtoupper($this->type).'_ADDRESS');
            $addressArray = (array) json_decode($addressString, true);
        }
        $this->hydrate($addressArray);
    }

    /**
     * @param array $array
     */
    private function hydrate($array)
    {
        $this->companyName = isset($array[$this->type.'_company']) ? $array[$this->type.'_company'] : null;
        $this->lastName = isset($array[$this->type.'_lastname']) ? $array[$this->type.'_lastname'] : null;
        $this->firstName = isset($array[$this->type.'_firstname']) ? $array[$this->type.'_firstname'] : null;
        $this->line0 = isset($array[$this->type.'_address3']) ? $array[$this->type.'_address3'] : null;
        $this->line1 = isset($array[$this->type.'_address4']) ? $array[$this->type.'_address4'] : null;
        $this->line2 = isset($array[$this->type.'_address1']) ? $array[$this->type.'_address1'] : null;
        $this->line3 = isset($array[$this->type.'_address2']) ? $array[$this->type.'_address2'] : null;
        $this->countryCode = isset($array[$this->type.'_country']) ? $array[$this->type.'_country'] : null;
        $this->city = isset($array[$this->type.'_city']) ? $array[$this->type.'_city'] : null;
        $this->zipCode = isset($array[$this->type.'_zipcode']) ? $array[$this->type.'_zipcode'] : null;
        $this->phoneNumber = isset($array[$this->type.'_phone']) ? $array[$this->type.'_phone'] : null;
        $this->email = isset($array[$this->type.'_email']) ? $array[$this->type.'_email'] : null;
    }

    /**
     * @return ColissimoMerchantAddress
     */
    public static function getMerchantReturnAddress()
    {
        $useReturnAddress = Configuration::get('COLISSIMO_USE_RETURN_ADDRESS');
        if ($useReturnAddress) {
            return new self('return');
        } else {
            return new self('sender');
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            $this->type.'_company'   => $this->companyName,
            $this->type.'_lastname'  => $this->lastName,
            $this->type.'_firstname' => $this->firstName,
            $this->type.'_address1'  => $this->line2,
            $this->type.'_address2'  => $this->line3,
            $this->type.'_address3'  => $this->line0,
            $this->type.'_address4'  => $this->line1,
            $this->type.'_country'   => $this->countryCode,
            $this->type.'_city'      => $this->city,
            $this->type.'_zipcode'   => $this->zipCode,
            $this->type.'_phone'     => $this->phoneNumber,
            $this->type.'_email'     => $this->email,
        );
    }

    /**
     * @return false|string
     */
    public function toJSON()
    {
        $array = (array) $this->toArray();

        return json_encode($array);
    }
}
