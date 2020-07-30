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
 * Class ColissimoTools
 */
class ColissimoTools
{
    const DEST_FR = 'FRANCE';
    const DEST_OM = 'OM';
    const DEST_EU = 'EUROPE';
    const DEST_WORLD = 'WORLDWIDE';
    const DEST_INTRA_OM = 'INTRA_DOM';
    const FIRSTNAME_LEN = 29;
    const LASTNAME_LEN = 35;
    const COMPANY_LEN = 35;
    const ADDRESS_LINE_LEN = 35;
    const ZIPCODE = 5;
    const CITY_LEN = 35;
    const ISO_GUADELOUPE = 'GP';
    const ISO_MARTINIQUE = 'MQ';

    /** @var array $isoOutreMer Outre-Mer zone 1, return shipment available */
    public static $isoOutreMer = array('GP', 'MQ', 'GF', 'RE', 'YT', 'PM', 'MF', 'BL');

    /** @var array $isoOutreMerZone2 Outre-Mer zone 2, return shipment NOT available */
    public static $isoOutreMerZone2 = array('NC', 'PF', 'WF', 'TF');

    /** @var array $isoOutreMerZone1Special Outre-Mer zone 1, special rules with Inter-Dom & FR shipments */
    public static $isoOutreMerZone1Special = array('GP', 'MQ');

    //@formatter:off
    /** @var array $isoOutreMerReturnSpecial Outre-Mer special : we can make return from these ISO to only these same ISO + 3 FR ISO */
    public static $isoOutreMerReturnSpecial = array('GP', 'MF', 'BL', 'MQ');

    /** @var array $isoFR French ISO codes that include Andorra & Monaco */
    public static $isoFR = array('FR', 'AD', 'MC');

    /** @var array $isoEUCountries (30 ISO) */
    public static $isoEUCountries = array('AT', 'BE', 'BG', 'CH', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'GR', 'FI', 'HR', 'HU', 'IE', 'IT', 'IS', 'LT', 'LU', 'LV', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'GB');

    /** @var array $isoEUCountriesWithCN23 Countries that require CN23 documents (4 ISO) */
    public static $isoEUCountriesWithCN23 = array('CH', 'HR', 'IS', 'NO');

    /** @var array $isoEUCountries (15 ISO) -- NOT USED IN THE CODE, JUST IN CASE ALL DESTINATIONS WILL BE AVAILABLE LATER */
    public static $isoEUCountriesZone1Zone3FromPricing = array('DE', 'BE', 'LU', 'NL', 'DK', 'EE', 'HU', 'LT', 'LV', 'PL', 'CZ', 'SK', 'SI', 'SE', 'CH');

    /** @var array $isoEUCountries EU Countries that allow delivery in mailbox (2 ISO) */
    public static $isoEUCountriesZone1Zone3 = array('BE', 'CH');

    /** @var array $isoReturnAvailable (24 ISO) */
    public static $isoReturnAvailable = array('DE', 'BE', 'LU', 'NL', 'AT', 'ES', 'IE', 'IT', 'PT', 'GB', 'EE', 'HU', 'LT', 'PL', 'CZ', 'SK', 'SI', 'CH', 'HR', 'FI', 'GR', 'MT', 'RO', 'AU');
    //@formatter:on

    /** @var array $isoSender Countries the merchants can ship their orders from */
    public static $isoSender = array('FR', 'MC');

    /** @var array $cn23Exceptions Countries that have exceptions (e.g. Canaries Island etc...) */
    public static $cn23Exceptions = array('ES', 'IT', 'DE', 'CH', 'GB', 'GR');

    /**
     * @param Context $context
     * @param string  $localPath
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public static function getPrestUI(Context $context, $localPath)
    {
        $context->controller->addJS($localPath.'views/js/riot+compiler.min.js');
        $context->smarty->assign('ps_version', _PS_VERSION_);
        $prestUI = $context->smarty->fetch($localPath.'views/templates/admin/prestui/ps-alert.tpl');
        $prestUI .= $context->smarty->fetch($localPath.'views/templates/admin/prestui/ps-form.tpl');
        $prestUI .= $context->smarty->fetch($localPath.'views/templates/admin/prestui/ps-panel.tpl');
        $prestUI .= $context->smarty->fetch($localPath.'views/templates/admin/prestui/ps-table.tpl');
        $prestUI .= $context->smarty->fetch($localPath.'views/templates/admin/prestui/ps-tabs.tpl');
        $prestUI .= $context->smarty->fetch($localPath.'views/templates/admin/prestui/ps-tags.tpl');

        return $prestUI;
    }

    /**
     * @param array $values
     * @return array
     */
    public static function getValueMultiple($values)
    {
        $return = array();
        foreach ($values as $value) {
            $return[$value] = Tools::getValue($value);
        }

        return $return;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function getMultipleGlobal($array)
    {
        $return = array();
        foreach ($array as $item) {
            $return[$item] = Configuration::getGlobalValue($item);
        }

        return $return;
    }

    /**
     * @param string $passwd
     * @return string
     */
    public static function hash($passwd)
    {
        return md5(_COOKIE_KEY_.$passwd);
    }

    /**
     * @return string
     */
    public static function getLogFilename()
    {
        $hash = self::hash(_PS_MODULE_DIR_);
        $file = date('Ym_').$hash.'.log';

        return $file;
    }

    /**
     * @return string
     */
    public static function getCurrentLogFilePath()
    {
        $file = dirname(__FILE__).'/../logs/'.self::getLogFilename();

        return $file;
    }

    /**
     * @param string $moduleName
     * @return string
     */
    public static function getCurrentLogFileUrl($moduleName)
    {
        $link = new ColissimoLink();
        $file = $link->getBaseLink().'modules/'.$moduleName.'/logs/'.self::getLogFilename();

        return $file;
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public static function copyLogo($source, $destination)
    {
        $iconExists = file_exists($destination) && md5_file($source) === md5_file($destination);
        if (!$iconExists) {
            Tools::copy($source, $destination);
        }
    }

    /**
     * @return array|bool
     */
    public static function getColissimoServicesSource()
    {
        $file = dirname(__FILE__).'/../install/colissimo_services.csv';
        if (($fd = @fopen($file, 'r')) == false) {
            return false;
        }
        $csv = array();
        $headers = array_slice(fgetcsv($fd), 0, -1);
        while (($data = fgetcsv($fd)) !== false) {
            $csv[$data[6]][] = array_combine($headers, array_slice($data, 0, -1));
        }

        return $csv;
    }

    /**
     * @return array|bool
     */
    public static function getColissimoTrackingCodesSource()
    {
        $file = dirname(__FILE__).'/../install/colissimo_tracking_codes.csv';
        if (($fd = @fopen($file, 'r')) == false) {
            return false;
        }
        $csv = array();
        $headers = fgetcsv($fd);
        while (($data = fgetcsv($fd)) !== false) {
            $csv[] = array_combine($headers, $data);
        }

        return $csv;
    }

    /**
     * @param mixed    $key
     * @param mixed    $values
     * @param bool     $html
     * @param null|int $idShopGroup
     * @param null|int $idShop
     * @return bool
     */
    public static function updateValueIfNotExists($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        if (!Configuration::hasKey($key, null, $idShopGroup, $idShop)) {
            return Configuration::updateValue($key, $values, $html, $idShopGroup, $idShop);
        }

        return true;
    }

    /**
     * @param int $idState
     * @return false|null|string
     */
    public static function getIsoStateById($idState)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('iso_code')
                ->from('state')
                ->where('id_state = '.(int) $idState);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
                 ->getValue($dbQuery);
    }

    /**
     * @param Address $address
     * @return bool
     */
    public static function validateDeliveryAddress(Address $address)
    {
        if (Tools::strlen($address->firstname) > self::FIRSTNAME_LEN) {
            return false;
        }
        if (Tools::strlen($address->lastname) > self::LASTNAME_LEN) {
            return false;
        }
        if (Tools::strlen($address->company) > self::COMPANY_LEN) {
            return false;
        }
        if (Tools::strlen($address->address1) > self::ADDRESS_LINE_LEN || !Validate::isAddress($address->city)) {
            return false;
        }
        if (Tools::strlen($address->address2) > self::ADDRESS_LINE_LEN || !Validate::isAddress($address->city)) {
            return false;
        }
        if (!Validate::isPostCode($address->postcode)) {
            return false;
        }
        if (Tools::strlen($address->city) > self::CITY_LEN || !Validate::isCityName($address->city)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $hsCode
     * @return bool
     */
    public static function isValidHsCode($hsCode)
    {
        if (!ctype_digit($hsCode) || !in_array((int) Tools::strlen($hsCode), array(6, 8, 10))) {
            return false;
        }

        return true;
    }

    /**
     * @param string $iso
     * @param string $postalCode
     * @return bool
     */
    public static function needCN23Exceptions($iso, $postalCode)
    {
        switch ($iso) {
            case 'ES':
                $province = Tools::substr($postalCode, 0, 2);
                if (in_array($province, array('35', '38', '51', '52'))) {
                    return true;
                }

                break;
            case 'IT':
                if (in_array($postalCode, array('23030', '22060'))) {
                    return true;
                }

                break;
            case 'DE':
                if (in_array($postalCode, array('27498', '78266'))) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'CH':
                if ($postalCode == '8238') {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'GB':
                $postalZone = Tools::substr($postalCode, 0, 2);
                if (in_array($postalZone, array('JE', 'IM', 'GY'))) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'GR':
                //@formatter:off
                if (in_array($postalCode, array('63075', '60386', '63086', '63087', '630 75', '603 86', '630 86', '630 87'))) {
                    return true;
                } else {
                    return false;
                }
                //@formatter:on
                break;
            default:
                return false;

                break;
        }

        return false;
    }

    /**
     * @param string $isoFrom
     * @param string $isoTo
     * @param string $postalCode
     * @return bool
     */
    public static function needCN23($isoFrom, $isoTo, $postalCode)
    {
        if (in_array($isoTo, self::$cn23Exceptions)) {
            if (self::needCN23Exceptions($isoTo, $postalCode)) {
                return true;
            }
        }
        $isoEUCountries = self::$isoEUCountries;
        if (Configuration::get('COLISSIMO_ENABLE_BREXIT')) {
            $isoEUCountries = array_diff($isoEUCountries, array('GB'));
        }
        if (in_array($isoFrom, self::$isoSender)) {
            if (in_array($isoTo, self::$isoSender)) {
                return false;
            } elseif (in_array($isoTo, $isoEUCountries) && !in_array($isoTo, self::$isoEUCountriesWithCN23)) {
                return false;
            } else {
                return true;
            }
        } elseif (in_array($isoFrom, self::$isoOutreMer)) {
            if (in_array($isoFrom, self::$isoOutreMerZone1Special) && in_array($isoTo, self::$isoOutreMerZone1Special)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * @param string $isoFrom
     * @param string $isoTo
     * @param string $postalCode
     * @return bool
     */
    public static function needReturnCN23($isoFrom, $isoTo, $postalCode)
    {
        return self::needCN23($isoTo, $isoFrom, $postalCode);
    }

    /**
     * @param string $isoTo
     * @return string
     */
    public static function getDestinationTypeByIsoCountry($isoTo)
    {
        $accountType = json_decode(Configuration::get('COLISSIMO_ACCOUNT_TYPE'), true);
        $senderAddr = new ColissimoMerchantAddress('sender');
        $isoFrom = $senderAddr->countryCode;
        if (in_array($isoFrom, self::$isoOutreMerZone1Special)) {
            if (in_array($isoTo, self::$isoOutreMerZone1Special)) {
                return self::DEST_INTRA_OM;
            } elseif (in_array($isoTo, array_merge(self::$isoFR, self::$isoOutreMer, self::$isoOutreMerZone2))) {
                return self::DEST_OM;
            } else {
                return self::DEST_WORLD;
            }
        } elseif (in_array($isoFrom, self::$isoOutreMer)) {
            if ($isoTo == $isoFrom) {
                return self::DEST_INTRA_OM;
            } elseif (in_array($isoTo, array_merge(self::$isoFR, self::$isoOutreMer, self::$isoOutreMerZone2))) {
                return self::DEST_OM;
            } else {
                return self::DEST_WORLD;
            }
        } elseif (in_array($isoFrom, self::$isoFR)) {
            $isoEUCountries = self::$isoEUCountries;
            if (Configuration::get('COLISSIMO_ENABLE_BREXIT')) {
                $isoEUCountries = array_diff($isoEUCountries, array('GB'));
            }
            if (in_array($isoTo, ColissimoTools::$isoFR)) {
                return self::DEST_FR;
            } elseif (in_array($isoTo, $isoEUCountries) && isset($accountType['EUROPE'])) {
                return self::DEST_EU;
            } elseif (in_array($isoTo, self::$isoOutreMer) || in_array($isoTo, self::$isoOutreMerZone2)) {
                return self::DEST_OM;
            } else {
                return self::DEST_WORLD;
            }
        }

        return self::DEST_WORLD;
    }

    /**
     * @param int $idCountryTo
     * @return string
     */
    public static function getDestinationTypeByIdCountry($idCountryTo)
    {
        $isoTo = Country::getIsoById((int) $idCountryTo);

        return self::getDestinationTypeByIsoCountry($isoTo);
    }

    /**
     * @param int $idColissimoOrder
     * @return int
     */
    public static function getDeliveryAddressByIdColissimoOrder($idColissimoOrder)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('a.id_address')
                ->from('address', 'a')
                ->leftJoin('orders', 'o', 'o.id_address_delivery = a.id_address')
                ->leftJoin('colissimo_order', 'co', 'co.id_order = o.id_order')
                ->where('co.id_colissimo_order = '.(int) $idColissimoOrder);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }

    /**
     * @param int|null $idShop
     * @return array
     */
    public static function getCredentials($idShop = null)
    {
        if (null !== $idShop) {
            $contractNumbers = Configuration::getMultiShopValues('COLISSIMO_ACCOUNT_LOGIN');
            $passwords = Configuration::getMultiShopValues('COLISSIMO_ACCOUNT_PASSWORD');
            $credentials = array(
                'contract_number' => $contractNumbers[(int) $idShop],
                'password'        => $passwords[(int) $idShop],
            );
        } else {
            $credentials = array(
                'contract_number' => Configuration::get('COLISSIMO_ACCOUNT_LOGIN'),
                'password'        => Configuration::get('COLISSIMO_ACCOUNT_PASSWORD'),
            );
        }

        return $credentials;
    }

    /**
     * @param array $lines
     * @throws Exception
     */
    public static function downloadColishipExport($lines)
    {
        $filename = 'coliship_'.date('YmdHis').'.csv';
        $destination = sys_get_temp_dir();
        if ($destination && Tools::substr($destination, -1) != DIRECTORY_SEPARATOR) {
            $destination .= DIRECTORY_SEPARATOR;
        }
        $safeDestination = realpath($destination).DIRECTORY_SEPARATOR.$filename;
        if (($fd = @fopen($safeDestination, 'w+')) === false) {
            throw new Exception(
                sprintf(
                    'Cannot open CSV file in tmp directory (%s).',
                    Tools::safeOutput(realpath($destination).DIRECTORY_SEPARATOR.$filename)
                )
            );
        }
        // INet compatibility
        fprintf($fd, chr(0xEF).chr(0xBB).chr(0xBF));
        $eol = "\r\n";
        foreach ($lines as $line) {
            fputcsv($fd, array_map('utf8_decode', $line), ';');
            if (0 === fseek($fd, -1, SEEK_CUR)) {
                fwrite($fd, $eol);
            }
        }
        fclose($fd);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($safeDestination).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        readfile($safeDestination);
        exit;
    }

    /**
     * @param array  $files
     * @param string $filename
     * @throws Exception
     */
    public static function downloadDocuments($files, $filename)
    {
        $destination = sys_get_temp_dir();
        $zip = new ZipArchive();
        if ($destination && Tools::substr($destination, -1) != DIRECTORY_SEPARATOR) {
            $destination .= DIRECTORY_SEPARATOR;
        }
        $safeTmpDir = realpath($destination);
        $destination .= $filename;
        if ($zip->open($destination, ZIPARCHIVE::CREATE) !== true) {
            throw new Exception('Cannot open zip file.');
        }
        foreach ($files as $obj) {
            /** @var ColissimoLabel|ColissimoDepositSlip $obj */
            try {
                $path = $obj->getFilePath();
            } catch (Exception $e) {
                continue;
            }
            $basename = basename($path);
            $zip->addFile($path, $basename);
        }
        $zip->close();
        $realpath = realpath($destination);
        if ($realpath === false) {
            throw new Exception('The path of the zip file is incorrect.');
        }
        if (Tools::substr($realpath, 0, Tools::strlen($safeTmpDir)) != $safeTmpDir) {
            throw new Exception('Possible directory traversal attempt.');
        }
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($destination);
        unlink($destination);
        exit;
    }

    /**
     * @param array  $files
     * @param string $filename
     * @return bool
     */
    public static function downloadCN23Documents($files, $filename)
    {
        $destination = sys_get_temp_dir();
        $zip = new ZipArchive();
        if ($destination && Tools::substr($destination, -1) != DIRECTORY_SEPARATOR) {
            $destination .= DIRECTORY_SEPARATOR;
        }
        $safeTmpDir = realpath($destination);
        $destination .= $filename;
        if ($zip->open($destination, ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        foreach ($files as $obj) {
            /** @var ColissimoLabel $obj */
            try {
                $path = $obj->getCN23Path();
            } catch (Exception $e) {
                continue;
            }
            $basename = basename($path);
            $zip->addFile($path, $basename);
        }
        $zip->close();
        $realpath = realpath($destination);
        if ($realpath === false) {
            return false;
        }
        if (Tools::substr($realpath, 0, Tools::strlen($safeTmpDir)) != $safeTmpDir) {
            return false;
        }
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($destination);
        unlink($destination);

        return true;
    }

    /**
     * @param array  $files
     * @param string $filename
     * @return bool
     */
    public static function downloadAllDocuments($files, $filename)
    {
        $destination = sys_get_temp_dir();
        $zip = new ZipArchive();
        if ($destination && Tools::substr($destination, -1) != DIRECTORY_SEPARATOR) {
            $destination .= DIRECTORY_SEPARATOR;
        }
        $safeTmpDir = realpath($destination);
        $destination .= $filename;
        if ($zip->open($destination, ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        if (isset($files['label'])) {
            foreach ($files['label'] as $obj) {
                /** @var ColissimoLabel $obj */
                try {
                    $path = $obj->getFilePath();
                } catch (Exception $e) {
                    continue;
                }
                $basename = basename($path);
                $zip->addFile($path, $basename);
            }
        }
        if (isset($files['cn23'])) {
            foreach ($files['cn23'] as $obj) {
                /** @var ColissimoLabel $obj */
                try {
                    $path = $obj->getCN23Path();
                } catch (Exception $e) {
                    continue;
                }
                $basename = basename($path);
                $zip->addFile($path, $basename);
            }
        }
        $zip->close();
        $realpath = realpath($destination);
        if ($realpath === false) {
            return false;
        }
        if (Tools::substr($realpath, 0, Tools::strlen($safeTmpDir)) != $safeTmpDir) {
            return false;
        }
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($destination);
        unlink($destination);

        return true;
    }

    /**
     * @param string $isoFrom
     * @return bool|string
     */
    public static function getReturnDestinationTypeByIsoCountry($isoFrom)
    {
        $isoReturnAvailable = self::$isoReturnAvailable;
        /** @var ColissimoMerchantAddress $returnAddress */
        $returnAddress = ColissimoMerchantAddress::getMerchantReturnAddress();
        $isoTo = $returnAddress->countryCode;
        if (in_array($isoTo, ColissimoTools::$isoOutreMer)) {
            $isoOutreMerZone1Special = ColissimoTools::$isoOutreMerZone1Special;
            if ($isoTo == $isoFrom ||
                (in_array($isoFrom, $isoOutreMerZone1Special) && in_array($isoTo, $isoOutreMerZone1Special))
            ) {
                return self::DEST_FR;
            }
            if (in_array($isoFrom, ColissimoTools::$isoOutreMerReturnSpecial)) {
                if (in_array($isoTo, ColissimoTools::$isoOutreMerReturnSpecial)) {
                    return false;
                }
            }
            if (in_array($isoFrom, array_merge(ColissimoTools::$isoOutreMer, ColissimoTools::$isoFR))) {
                return self::DEST_OM;
            }

            return false;
        } elseif (in_array($isoTo, ColissimoTools::$isoFR)) {
            if (in_array($isoFrom, ColissimoTools::$isoOutreMer)) {
                return self::DEST_OM;
            } elseif (in_array($isoFrom, ColissimoTools::$isoFR)) {
                return self::DEST_FR;
            } elseif (in_array($isoFrom, $isoReturnAvailable)) {
                return self::DEST_WORLD;
            }
        } else {
            return false;
        }

        return false;
    }

    /**
     * @param int $idCountryFrom
     * @return bool|string
     */
    public static function getReturnDestinationTypeByIdCountry($idCountryFrom)
    {
        $isoFrom = Country::getIsoById((int) $idCountryFrom);

        return self::getReturnDestinationTypeByIsoCountry($isoFrom);
    }

    /**
     * @param Order $order
     * @return float|int
     */
    public static function getOrderTotalWeightInKg($order)
    {
        $totalWeightReal = $order->getTotalWeight();
        $totalWeight = 0;
        $products = $order->getProducts();
        foreach ($products as $product) {
            if ((float) $product['product_weight']) {
                $totalWeight += ($product['product_weight'] * $product['product_quantity']);
            } else {
                $totalWeight += ($product['product_quantity'] * 0.05);
            }
        }

        return $totalWeightReal ? self::weightInKG($totalWeight) : $totalWeight;
    }

    /**
     * @param float $weight
     * @return float|int
     */
    public static function weightInKG($weight)
    {
        $conversion = array(
            'g'   => 0.001,
            'gr'  => 0.001,
            'kg'  => 1,
            'kgs' => 1,
            'lb'  => 0.453592,
            'lbs' => 0.453592,
            'oz'  => 0.0283495,
        );
        $weightUnit = Tools::strtolower(Configuration::get('PS_WEIGHT_UNIT'));

        return isset($conversion[$weightUnit]) ? $weight * $conversion[$weightUnit] : $weight;
    }

    /**
     * @param float    $price
     * @param Currency $currencyFrom
     * @return float
     * @throws Exception
     */
    public static function convertInEUR($price, $currencyFrom)
    {
        if ($currencyFrom->iso_code == 'EUR') {
            return $price;
        }
        $idCurrencyEUR = Currency::getIdByIsoCode('EUR');
        if (!$idCurrencyEUR) {
            throw new Exception('EUR Currency is not installed.');
        }
        $defaultCurrencyPrice = Tools::convertPrice($price, $currencyFrom->id, false);
        $priceInEUR = Tools::convertPrice($defaultCurrencyPrice, $idCurrencyEUR);

        return Tools::ps_round($priceInEUR, 2);
    }

    /**
     * @param Order  $order
     * @param string $subject
     * @param string $link
     * @return bool|int
     */
    public static function sendHandlingShipmentMail(Order $order, $subject, $link)
    {
        $customer = new Customer((int) $order->id_customer);
        $templateVars = array(
            '{followup_colissimo}' => $link,
            '{firstname}'          => $customer->firstname,
            '{lastname}'           => $customer->lastname,
            '{id_order}'           => $order->id,
            '{order_name}'         => $order->getUniqReference(),
        );

        return @Mail::Send(
            (int) $order->id_lang,
            'colissimo_handling_shipment',
            $subject,
            $templateVars,
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            null,
            null,
            dirname(__FILE__).'/../mails/',
            false,
            (int) $order->id_shop
        );
    }

    /**
     * @param ColissimoLabel $colissimoReturnLabel
     * @param string         $subject
     * @return bool|int
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    public static function sendReturnLabelMail($colissimoReturnLabel, $subject)
    {
        $colissimoOrder = new ColissimoOrder((int) $colissimoReturnLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        $customer = new Customer((int) $order->id_customer);
        $file = $colissimoReturnLabel->getFilePath();
        $fileAttachment = array(
            'content' => Tools::file_get_contents($file),
            'name'    => basename($file),
            'mime'    => 'application/pdf',
        );

        $templateVars = array(
            '{firstname}' => $customer->firstname,
            '{lastname}'  => $customer->lastname,
            '{reference}' => $order->reference,
        );

        return @Mail::Send(
            (int) $order->id_lang,
            'colissimo_label_return',
            sprintf($subject, $order->reference),
            $templateVars,
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            $fileAttachment,
            null,
            dirname(__FILE__).'/../mails/',
            false,
            (int) $order->id_shop
        );
    }

    /**
     * @param int    $existingCarrierId
     * @param array  $correspondance
     * @param string $key
     * @throws PrestaShopException
     */
    public static function migrateCarrierData($existingCarrierId, $correspondance, $key)
    {
        $oldCarrier = new Carrier((int) $existingCarrierId);
        $oldCarrierIdReference = $oldCarrier->id_reference;
        $colissimoCarrier = new ColissimoCarrier((int) Configuration::get($correspondance[$key]));

        Configuration::updateGlobalValue('COLISSIMO_MIGRATION_'.$correspondance[$key], $colissimoCarrier->id_reference);

        $carriersDeleted = (array) json_decode(
            Configuration::getGlobalValue('COLISSIMO_MIGRATION_CARRIERS_DELETED'),
            true
        );
        $carriersDeleted[] = $oldCarrier->id;
        Configuration::updateGlobalValue('COLISSIMO_MIGRATION_CARRIERS_DELETED', json_encode($carriersDeleted));
        Db::getInstance()
          ->update(
              'carrier',
              array('deleted' => 1),
              'id_carrier = '.(int) $oldCarrier->id
          );

        // Carrier prices & delivery ranges

        $colissimoCarrier->deleteDeliveryPrice('range_weight');
        $rangeTable = $oldCarrier->getRangeTable();
        if ($rangeTable == 'range_weight') {
            $otherRangeTable = 'range_price';
        } else {
            $otherRangeTable = 'range_weight';
        }
        $ranges = Carrier::getDeliveryPriceByRanges($rangeTable, $oldCarrier->id);
        $createdRanges = array();
        foreach ($ranges as $range) {
            $id = 'id_'.$rangeTable;
            $otherId = 'id_'.$otherRangeTable;
            if (!isset($createdRanges[$range[$id]])) {
                $rangeObject = $rangeTable == 'range_weight' ? new RangeWeight((int) $range[$id]) :
                    new RangePrice((int) $range[$id]);
                $newRangeObject = $oldCarrier->getRangeObject();
                $newRangeObject->id_carrier = (int) $colissimoCarrier->id;
                $newRangeObject->delimiter1 = (float) $rangeObject->delimiter1;
                $newRangeObject->delimiter2 = (float) $rangeObject->delimiter2;
                $newRangeObject->save();
                $createdRanges[$range[$id]] = (int) $newRangeObject->id;
            }

            Db::getInstance()
              ->insert(
                  'delivery',
                  array(
                      'id_shop'       => '',
                      'id_shop_group' => '',
                      $id             => (int) $createdRanges[$range[$id]],
                      $otherId        => 0,
                      'id_carrier'    => (int) $colissimoCarrier->id,
                      'id_zone'       => (int) $range['id_zone'],
                      'price'         => (float) $range['price'],
                  ),
                  true
              );
        }

        // Carrier zones

        $carrierZoneIdsQuery = new DbQuery();
        $carrierZoneIdsQuery->select('*')
                            ->from('carrier_zone')
                            ->where('id_carrier = '.(int) $oldCarrier->id);
        $carrierZoneIds = Db::getInstance()
                            ->executeS($carrierZoneIdsQuery);
        $colissimoCarrier->setZones($carrierZoneIds);

        // Carrier taxes

        $carrierTaxRulesGroupQuery = new DbQuery();
        $carrierTaxRulesGroupQuery->select($colissimoCarrier->id.' AS id_carrier, id_tax_rules_group, id_shop')
                                  ->from('carrier_tax_rules_group_shop')
                                  ->where('id_carrier = '.(int) $oldCarrier->id);
        Db::getInstance()
          ->insert(
              'carrier_tax_rules_group_shop',
              Db::getInstance()
                ->executeS($carrierTaxRulesGroupQuery)
          );

        // Carrier groups

        $carrierGroups = $oldCarrier->getGroups();
        $colissimoCarrier->setGroups($carrierGroups);
        $colissimoCarrier->is_free = (bool) $oldCarrier->is_free;
        $colissimoCarrier->shipping_handling = (bool) $oldCarrier->shipping_handling;
        $colissimoCarrier->shipping_method = (int) $oldCarrier->shipping_method;
        $colissimoCarrier->max_width = (int) $oldCarrier->max_width;
        $colissimoCarrier->max_height = (int) $oldCarrier->max_height;
        $colissimoCarrier->max_depth = (int) $oldCarrier->max_depth;
        $colissimoCarrier->max_weight = (float) $oldCarrier->max_weight;
        $colissimoCarrier->range_behavior = (int) $oldCarrier->range_behavior;
        $colissimoCarrier->save();

        $carriersDeleted = (array) json_decode(
            Configuration::getGlobalValue('COLISSIMO_MIGRATION_CARRIERS_RENAMED'),
            true
        );
        $carriersDeleted[] = $oldCarrierIdReference;
        Configuration::updateGlobalValue('COLISSIMO_MIGRATION_CARRIERS_RENAMED', json_encode($carriersDeleted));
    }

    /**
     * @param int $idCart
     * @return OrderCore|Order|null
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getOrderByCartId($idCart)
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            return Order::getByCartId((int) $idCart);
        } else {
            $idOrder = Order::getOrderByCartId((int) $idCart);

            return new Order((int) $idOrder);
        }
    }

    /**
     * @param string $directory
     * @return array
     */
    public static function getDocumentsDirDetails($directory)
    {
        $dirs = array('cn23', 'deposit_slip', 'labels');
        $count = 0;
        $totalSize = 0;
        foreach ($dirs as $dir) {
            $dirArray = scandir($directory.$dir);
            foreach ($dirArray as $filename) {
                if ($filename != ".." && $filename != "." && $filename != 'index.php' && $filename != '.htaccess') {
                    if (is_file($directory.$dir.'/'.$filename)) {
                        $totalSize += filesize($directory.$dir.'/'.$filename);
                        $count += 1;
                    }
                }
            }
        }

        return array('total_size' => $totalSize, 'count' => $count);
    }

    /**
     * @param int|mixed $bytes
     * @return string
     */
    public static function formatDirectorySize($bytes)
    {
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;
        $tb = $gb * 1024;
        if (($bytes >= 0) && ($bytes < $kb)) {
            return $bytes.' B';
        } elseif (($bytes >= $kb) && ($bytes < $mb)) {
            return ceil($bytes / $kb).' KB';
        } elseif (($bytes >= $mb) && ($bytes < $gb)) {
            return ceil($bytes / $mb).' MB';
        } elseif (($bytes >= $gb) && ($bytes < $tb)) {
            return ceil($bytes / $gb).' GB';
        } elseif ($bytes >= $tb) {
            return ceil($bytes / $tb).' TB';
        } else {
            return $bytes.' B';
        }
    }

    /**
     * @param int $orderId
     * @return bool|false|null|string
     */
    public static function getCPassIdByOrderId($orderId)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('cpass_id');
        $dbQuery->from('colissimopass_orders');
        $dbQuery->where('id_order = '.(int) $orderId);
        try {
            $CPassId = Db::getInstance(_PS_USE_SQL_SLAVE_)
                         ->getValue($dbQuery);
        } catch (Exception $e) {
            return false;
        }

        return $CPassId;
    }

    /**
     * @param ColissimoPickupPoint $pickupPoint
     * @param Order                $order
     * @param string               $phone
     * @return int
     * @throws PrestaShopException
     */
    public static function createAddressFromPickupPoint($pickupPoint, $order, $phone)
    {
        $customer = new Customer((int) $order->id_customer);
        $idCountry = Country::getByIso($pickupPoint->iso_country);
        $pickupPointAddress = new Address();
        $pickupPointAddress->id_customer = (int) $order->id_customer;
        $pickupPointAddress->alias = 'COLISSIMO POINT PICKUP '.(int) $pickupPoint->id;
        $pickupPointAddress->lastname = pSQL($customer->lastname);
        $pickupPointAddress->firstname = pSQL($customer->firstname);
        $pickupPointAddress->company = pSQL($pickupPoint->company_name);
        $pickupPointAddress->address1 = pSQL($pickupPoint->address1);
        $pickupPointAddress->address2 = pSQL($pickupPoint->address2);
        $pickupPointAddress->other = pSQL($pickupPoint->colissimo_id);
        $pickupPointAddress->postcode = pSQL($pickupPoint->zipcode);
        $pickupPointAddress->city = pSQL($pickupPoint->city);
        $pickupPointAddress->id_country = (int) $idCountry;
        $mobile = ColissimoCartPickupPoint::getMobilePhoneByCartId((int) $order->id_cart);
        $pickupPointAddress->phone_mobile = pSQL($mobile);
        $pickupPointAddress->phone = pSQL($phone);
        $pickupPointAddress->deleted = 1;
        $pickupPointAddress->save();

        return $pickupPointAddress->id;
    }
}
