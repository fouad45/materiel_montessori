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
 * Class ColissimoLabel
 */
class ColissimoLabel extends ObjectModel
{
    const FRANCE_RETURN_PREFIX = '8R';
    const INTER_RETURN_PREFIX = '7R';

    /** @var int $id_colissimo_label */
    public $id_colissimo_label;

    /** @var int $id_colissimo_order */
    public $id_colissimo_order;

    /** @var int $id_colissimo_deposit_slip */
    public $id_colissimo_deposit_slip;

    /** @var string $shipping_number */
    public $shipping_number;

    /** @var string $label_format Format of the label (PDF/ZPL...) */
    public $label_format;

    /** @var int $return_label Flag to indicate if the label is a return label */
    public $return_label;

    /** @var bool $cn23 Flag to indicate if the label has a CN23 file associated */
    public $cn23;

    /** @var bool $coliship Flag to indicate if the label comes from Coliship */
    public $coliship;

    /** @var bool $migration Flag to indicate if the label has been migrated from other module */
    public $migration;

    /** @var int $insurance NULL = no indication, 0 = shipment not insured, 1 = shipment insured */
    public $insurance;

    /** @var bool $file_deleted Flag to indicate if the file is deleted */
    public $file_deleted;

    /** @var string $date_add */
    public $date_add;

    /** @var array $validFormats Valid file extensions for labels */
    private $validFormats = array('pdf', 'dpl', 'zpl');

    /** @var array $definition */
    public static $definition = array(
        'table'   => 'colissimo_label',
        'primary' => 'id_colissimo_label',
        'fields'  => array(
            'id_colissimo_order'        => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true,
            ),
            'id_colissimo_deposit_slip' => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => false,
            ),
            'shipping_number'           => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 45),
            'label_format'              => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 3),
            'return_label'              => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true,
            ),
            'cn23'                      => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'coliship'                  => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
                'default'  => 0,
            ),
            'migration'                 => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
                'default'  => 0,
            ),
            'insurance'                 => array(
                'type'     => self::TYPE_NOTHING,
                'validate' => 'isUnsignedId',
                'required' => false,
                'default'  => null,
            ),
            'file_deleted'              => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
                'default'  => 0,
            ),
            'date_add'                  => array(
                'type'      => self::TYPE_DATE,
                'validate'  => 'isDate',
                'copy_post' => false,
            ),
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
     * @return bool
     * @throws Exception
     */
    public function view()
    {
        $file = $this->getFilePath();
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: inline; filename="'.basename($file).'"');
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
    public function writeLabel($content)
    {
        $labelPath = $this->getFilePath(false);

        return file_put_contents($labelPath, $content);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteFile()
    {
        $labelPath = $this->getFilePath();
        if (file_exists($labelPath)) {
            return unlink($labelPath);
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
        if ((!$this->return_label && !in_array($this->label_format, $this->validFormats)) ||
            ($this->return_label && $this->label_format != 'pdf') ||
            !ctype_alnum($this->shipping_number)
        ) {
            throw new Exception('Label filename has an incorrect extension or format.');
        }
        $safePath = realpath(_PS_MODULE_DIR_.'colissimo/documents/labels/');
        $returnLabel = $this->return_label ? 'RET-' : '';
        $fileExtension = $this->return_label ? 'pdf' : $this->label_format;
        $filename = sprintf('%s%d-%s.%s', $returnLabel, (int) $this->id, $this->shipping_number, $fileExtension);
        if ($safePath === false) {
            throw new Exception('Invalid pathname.');
        }
        if (!$fileExistsCheck) {
            return $safePath.DIRECTORY_SEPARATOR.$filename;
        }
        $path = dirname(__FILE__).'/../documents/labels/'.$filename;
        $realpath = realpath($path);
        if ($realpath === false) {
            throw new Exception('The path of label file is incorrect. '.$path);
        }
        if (Tools::substr($realpath, 0, Tools::strlen($safePath)) != $safePath) {
            throw new Exception('Possible directory traversal attempt.');
        }

        return $realpath;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function downloadCN23()
    {
        $file = $this->getCN23Path();
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
     * @return bool
     * @throws Exception
     */
    public function viewCN23()
    {
        $file = $this->getCN23Path();
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: inline; filename="'.basename($file).'"');
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
     * @return bool
     * @throws Exception
     */
    public function deleteCN23()
    {
        $file = $this->getCN23Path();
        if (file_exists($file)) {
            return unlink($file);
        } else {
            return false;
        }
    }

    /**
     * @param string $content Base64 encoded string
     * @return bool|int
     * @throws Exception
     */
    public function writeCN23File($content)
    {
        $cn23Path = $this->getCN23Path(false);

        return file_put_contents($cn23Path, $content);
    }

    /**
     * @param bool $fileExistsCheck
     * @return string
     * @throws Exception
     */
    public function getCN23Path($fileExistsCheck = true)
    {
        if (!ctype_alnum($this->shipping_number)) {
            throw new Exception('CN23 filename has an incorrect format.');
        }
        $safePath = realpath(_PS_MODULE_DIR_.'colissimo/documents/cn23/');
        $filename = sprintf('%d-CN23-%s.pdf', (int) $this->id, $this->shipping_number);
        if ($safePath === false) {
            throw new Exception('Invalid pathname.');
        }
        if (!$fileExistsCheck) {
            return $safePath.DIRECTORY_SEPARATOR.$filename;
        }
        $path = dirname(__FILE__).'/../documents/cn23/'.$filename;
        $realpath = realpath($path);
        if ($realpath === false) {
            throw new Exception('The path of CN23 file is incorrect.');
        }
        if (Tools::substr($realpath, 0, Tools::strlen($safePath)) != $safePath) {
            throw new Exception('Possible directory traversal attempt.');
        }

        return $realpath;
    }

    /**
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function getLastTrackingDetailsKnown()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('*')
                ->from('colissimo_shipment_tracking')
                ->where('id_colissimo_label = '.(int) $this->id);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
                 ->getRow($dbQuery);
    }

    /**
     * @param string $shippingNumber
     * @return int
     */
    public static function getLabelIdByShippingNumber($shippingNumber)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_label')
                ->from('colissimo_label')
                ->where('shipping_number = "'.pSQL($shippingNumber).'"');

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }

    /**
     * @return bool
     */
    public function hasMailboxPickup()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_label')
                ->from('colissimo_mailbox_return')
                ->where('id_colissimo_label = '.(int) $this->id);

        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)
                        ->getValue($dbQuery);
    }

    /**
     * @return array|bool|null|object
     */
    public function getMailboxPickupDetails()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('*')
                ->from('colissimo_mailbox_return')
                ->where('id_colissimo_label = '.(int) $this->id);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
                 ->getRow($dbQuery);
    }

    /**
     * @return int
     */
    public function getReturnLabelId()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_label')
                ->from('colissimo_label')
                ->where('return_label = '.(int) $this->id);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }

    /**
     * Determine if a label is downloadable. We cannot download label that are:
     *  - deleted physically on the server
     *  - created from Coliship
     *  - created from another Colissimo module and migrated
     *
     * @return bool
     */
    public function isDownloadable()
    {
        if ($this->file_deleted) {
            return false;
        }
        if ($this->coliship) {
            return false;
        }
        if ($this->migration) {
            return false;
        }

        return true;
    }

    /**
     * Determine if a label is deletable. We cannot delete label that are:
     *  - linked to a deposit slip
     *
     * @return bool
     */
    public function isDeletable()
    {
        if ($this->id_colissimo_deposit_slip) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isFranceReturnLabel()
    {
        if (!$this->shipping_number) {
            return false;
        }

        return (Tools::substr($this->shipping_number, 0, 2) == self::FRANCE_RETURN_PREFIX) ? true : false;
    }

    /**
     * @return string
     * @throws PrestaShopDatabaseException
     */
    public function getNextShippingNumber()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('shipping_number')
                ->from('colissimo_label')
                ->where('id_colissimo_order = '.(int) $this->id_colissimo_order)
                ->where('shipping_number != "'.pSQL($this->shipping_number).'"')
                ->where('return_label = 0');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)
                    ->executeS($dbQuery);
        if (!$result) {
            return '';
        } else {
            return $result[0]['shipping_number'];
        }
    }
}
