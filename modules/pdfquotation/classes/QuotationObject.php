<?php
/**
* Class QuotationObject
*
* @author    Empty
* @copyright 2007-2016 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class QuotationObject extends ObjectModel 
{
	/** @var integer */
	public $id_quotation;
		
	/** @var string */
	public $ref_quotation;
	
	/** @var integer */
    public $id_cart;

    /** @var integer */
    public $id_customer;

    /** @var string */
	public $first_name;
    
    /** @var string */
	public $last_name;
    
    /** @var string */
	public $email;
    
    /** @var string */
	public $phone;
    
    /** @var integer */
	public $contacted;

    /** @var integer */
    public $date_add;

    /** @var integer */
    public $deleted;
	
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'quotation',
        'primary' => 'id_quotation',
        'multilang' => FALSE,
        'fields' => array(
            'ref_quotation' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => FALSE, 'lang' => FALSE),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => TRUE, 'lang' => FALSE),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => FALSE, 'lang' => FALSE),
            'first_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => TRUE, 'lang' => FALSE),
            'last_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => TRUE, 'lang' => FALSE),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => TRUE, 'lang' => FALSE),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => FALSE, 'lang' => FALSE),
            'contacted' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => TRUE, 'lang' => FALSE),
            'deleted' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => TRUE, 'lang' => FALSE),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => TRUE, 'lang' => FALSE),
        ),
    );
    
    public static function loadByIdQuotation($id_quotation){
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'quotation` q
            WHERE q.`id_quotation` = '.(int)$id_quotation
        );

        return new QuotationObject($result['id_quotation']);
    }

    public function getByIdCustomer($idCustomer){
        $results = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'quotation` q
            WHERE q.`id_customer` = '.(int)$idCustomer.' '.
            'AND q.deleted=1'
        );

        foreach($results as &$result) {
            $cartObj = new Cart($result['id_cart']);
            $result['total'] = $cartObj->getOrderTotal();
            $result['products'] = $cartObj->getProducts();
        }

        return $results;
    }

    public static function loadByIdAndCustomer($idQuotation, $idCustomer) {
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'quotation` quotation
            WHERE quotation.`id_quotation` = '.(int)$idQuotation.' '.
            'AND quotation.`id_customer` = '.(int)$idCustomer
        );

        if(!empty($result['id_quotation'])) {
            return new QuotationObject($result['id_quotation']);
        }
        else {
            return null;
        }
    }
}

