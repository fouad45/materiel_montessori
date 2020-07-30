<?php
/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

/**
 * Actionarea ObjectModel
 */

class TouchizeActionarea extends ObjectModel
{
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'touchize_actionarea',
        'primary' => 'id_touchize_actionarea',
        'multilang' => false,
        'fields' => array(
            'tx' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ),
            'ty' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ),
            'width' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ),
            'height' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'id_product_attribute' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'id_category' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'id_manufacturer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'search_term' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ),
            'id_touchize_touchmap' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
            ),
        ),
    );
    
    /**
     * @var string
     **/
    public $tx;

    /**
     * @var string
     **/
    public $ty;

    /**
     * @var string
     **/
    public $width;

    /**
     * @var string
     **/
    public $height;

    /**
     * @var int
     **/
    public $id_category;

    /**
     * @var int
     **/
    public $id_product_attribute;

    /**
     * @var int
     **/
    public $id_touchize_touchmap;

    /**
     * @var string
     **/
    public $search_term;

    /**
     * @var int
     **/
    public $id_product;

    /**
     * @var int
     **/
    public $id_manufacturer;

    /**
     * @var string
     **/
    public $date_add;

    /**
     * @var string
     **/
    public $date_upd;

    /**
     * Get action areas.
     *
     * @param  null|int $idTouchmap
     *
     * @return array
     */
    public static function getActionAreas($idTouchmap = null)
    {
        $unmappedAreas = self::getSqlActionAreas($idTouchmap);
        $areas = array();

        foreach ($unmappedAreas as $area) {
            if ($area['ProductId']) {
                $product = new Product(
                    (int)$area['ProductId'],
                    false,
                    Context::getContext()->language->id
                );
                $area['ProductName'] = $product->name;
                $area['Href'] = TouchizeControllerHelper::getRelativeURL(
                    Context::getContext()->link->getProductLink(
                        $product->id,
                        $product->link_rewrite,
                        $product->id_category_default
                    )
                );
                $area['Alt'] = $product->name;
            } else {
                $area['ProductId'] = null;
            }

            if ($area['TaxonId']) {
                $category = new Category(
                    (int)$area['TaxonId'],
                    Context::getContext()->language->id
                );
                $area['CategoryName'] = $category->name;
                $area['Href'] = TouchizeControllerHelper::getRelativeURL(
                    Context::getContext()->link->getCategoryLink(
                        (int)$area['TaxonId']
                    )
                );
                $area['Alt'] = $category->name;
            } else {
                $area['TaxonId'] = null;
            }

            if ($area['ManufacturerId']) {
                $manufacturer = new Manufacturer(
                    (int)$area['ManufacturerId'],
                    Context::getContext()->language->id
                );
                $area['TaxonId'] = 'manufacturer'.$area['ManufacturerId'];
                $area['CategoryName'] = $manufacturer->name;
                $area['Href'] = TouchizeControllerHelper::getRelativeURL(
                    Context::getContext()->link->getmanufacturerLink(
                        (int)$area['ManufacturerId'],
                        $manufacturer->link_rewrite
                    )
                );
                $area['Alt'] = $manufacturer->name;
            } else {
                $area['ManufacturerId'] = null;
            }

            if ($area['SearchTerm']) {
                $area['Href'] = TouchizeControllerHelper::getRelativeURL(
                    Context::getContext()->link->getPageLink('search')
                    .'?search_query='
                    .$area['SearchTerm']
                );
                $area['Alt'] = $area['SearchTerm'];
            }

            array_push($areas, $area);
        }

        return $areas;
    }

    /**
     * Get slq action areas.
     *
     * @param  null|int $idTouchmap
     *
     * @return array
     */
    public static function getSqlActionAreas($idTouchmap = null)
    {
        $sql = 'SELECT id_touchize_actionarea AS Id,
            id_touchize_touchmap AS CampaignId,
            tx AS Tx,
            ty AS Ty,
            width AS Width,
            height AS Height,
            search_term AS SearchTerm,
            id_product AS ProductId,
            id_category AS TaxonId,
            id_manufacturer AS ManufacturerId
            FROM '. _DB_PREFIX_
                .'touchize_actionarea
            WHERE id_touchize_touchmap = \''.pSQL((int)$idTouchmap).'\'';

        return Db::getInstance()->executeS($sql);
    }
}
