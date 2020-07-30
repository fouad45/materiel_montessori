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
 * Class ColissimoDepositSlip
 */
class ColissimoDepositSlip extends ObjectModel
{
    /** @var int $id_colissimo_deposit_slip */
    public $id_colissimo_deposit_slip;

    /** @var string $filename */
    public $filename;

    /** @var int $number Deposit slip number */
    public $number;

    /** @var int $nb_parcel Number of parcels included in the deposit slip */
    public $nb_parcel;

    /** @var bool $file_deleted Flag to indicate if the file is deleted */
    public $file_deleted;

    /** @var string $date_add */
    public $date_add;

    /** @var array $definition */
    public static $definition = array(
        'table'   => 'colissimo_deposit_slip',
        'primary' => 'id_colissimo_deposit_slip',
        'fields'  => array(
            'filename'     => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 45),
            'number'       => array('type' => self::TYPE_INT, 'required' => true, 'size' => 10),
            'nb_parcel'    => array('type' => self::TYPE_INT, 'required' => true, 'size' => 11),
            'file_deleted' => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
                'default'  => 0,
            ),
            'date_add'     => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * @return bool
     * @throws Exception
     */
    public function download()
    {
        $file = $this->getFilePath();
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            readfile($file);
            exit;
        } else {
            return false;
        }
    }

    /**
     * @param string $content Base64 encoded string
     * @return bool|int
     * @throws Exception
     */
    public function writeDepositSlip($content)
    {
        $depositSlipPath = $this->getFilePath(false);

        return file_put_contents($depositSlipPath, $content);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteFile()
    {
        $depositSlipPath = $this->getFilePath();
        if (file_exists($depositSlipPath)) {
            return unlink($depositSlipPath);
        }

        return false;
    }

    /**
     * @param bool $fileExistsCheck
     * @return bool|string
     * @throws Exception
     */
    public function getFilePath($fileExistsCheck = true)
    {
        if (Tools::substr($this->filename, -4) !== '.pdf') {
            throw new Exception('Deposit slip filename has an incorrect extension.');
        }
        $basename = Tools::substr($this->filename, 0, -4);
        if (strpos($basename, '.') === false) {
            if (!ctype_digit($basename)) {
                throw new Exception('Deposit slip filename has an incorrect format.');
            }
        } elseif (!preg_match('/^[0-9]{8}\.[0-9]{14}$/', $basename)) {
            throw new Exception('Deposit slip filename has an incorrect format.');
        }
        $safePath = realpath(_PS_MODULE_DIR_.'colissimo/documents/deposit_slip/');
        if ($safePath === false) {
            throw new Exception('Invalid pathname.');
        }
        if (!$fileExistsCheck) {
            return $safePath.DIRECTORY_SEPARATOR.$this->filename;
        }
        $path = dirname(__FILE__).'/../documents/deposit_slip/'.$this->filename;
        $realpath = realpath($path);
        if ($realpath === false) {
            throw new Exception('The path of deposit slip file is incorrect.');
        }
        if (Tools::substr($realpath, 0, Tools::strlen($safePath)) != $safePath) {
            throw new Exception('Possible directory traversal attempt.');
        }

        return $realpath;
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getLabelIds()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_label')
                ->from('colissimo_label')
                ->where('id_colissimo_deposit_slip = '.(int) $this->id);

        return (array) Db::getInstance(_PS_USE_SQL_SLAVE_)
                         ->executeS($dbQuery);
    }
}
