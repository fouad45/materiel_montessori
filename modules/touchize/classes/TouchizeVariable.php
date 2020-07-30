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
 * Variables ObjectModel
 */

class TouchizeVariable extends ObjectModel
{
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'touchize_variables',
        'primary' => 'id_variable',
        'multilang' => false,
        'fields' => array(
            'id_shop' => array(
                'type' => self::TYPE_NOTHING,
                'validate' => 'isUnsignedId',
                'required' => false,
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'required' => false,
            ),
            'description' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'required' => false,
            ),
            'value' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'required' => false,
            ),
            'is_color' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
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
    public $name;

    /**
     * @var string
     **/
    public $description;

    /**
     * @var string
     **/
    public $value;

    /**
     * @var bool
     **/
    public $is_color;

    /**
     * @var string
     **/
    public $date_add;

    /**
     * @var string
     **/
    public $date_upd;

    /**
     * Overrides update
     *
     * @see ObjectModel::update
     *
     * @param  bool $autodate
     * @param  bool $nullValues
     *
     * @return bool
     */
    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues)) {
            $this->compileLESS();

            return true;
        }

        return false;
    }

    /**
     * Overrides update
     *
     * @see ObjectModel::update
     *
     * @param  bool $null_values
     *
     * @return bool
     */
    public function update($nullValues = false)
    {
        if (parent::update($nullValues)) {
            $this->compileLESS();

            return true;
        }

        return false;
    }

    /**
     * Overrides delete
     *
     * @see ObjectModel::delete
     *
     * @return bool
     */
    public function delete()
    {
        if (parent::delete()) {
            $this->compileLESS();

            return true;
        }

        return false;
    }

    /**
     * Generating of .css file
     */
    protected function compileLESS()
    {
        $link = new Link();

        if (Configuration::get('PS_LOGO_MOBILE')) {
            $logo = $link->getMediaLink(
                _PS_IMG_.Configuration::get('PS_LOGO_MOBILE')
                .'?'.Configuration::get('PS_IMG_UPDATE_TIME')
            );
        } else {
            $logo = $link->getMediaLink(
                _PS_IMG_.Configuration::get('PS_LOGO')
            );
        }

        $less = new lessc;
        $variables = $this->getAllVariables();
        $inputVars = array(
            'brand-logo-background' => '"//'.$logo.'"',
        );

        foreach ($variables as $value) {
            $inputVars[$value['name']] = $value['value'];
        }

        $less->setVariables($inputVars);
        $file = _PS_MODULE_DIR_.'touchize/less/index.less';
        $less->compileFile(
            $file,
            _PS_MODULE_DIR_.'touchize/views/css/override' . TouchizeBaseHelper::getCSSFileAddition() . '.css'
        );
    }

    /**
     * Get all styling variables.
     *
     * @return array
     */
    protected function getAllVariables()
    {
        $sql = 'SELECT name, value
                FROM '._DB_PREFIX_.'touchize_variables';

        return Db::getInstance()->executeS($sql);
    }
    
    /**
     * Insert or Update multishop variable for styling
     *
     * @param string $table
     * @param array $data
     * @param string $where
     * @return boolean
     */
    public static function insertOrUpdateOnMultishop($table, $data, $where)
    {
        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);
        
        $check = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.pSQL($table).'` WHERE ' . $where .
            TouchizeBaseHelper::sqlRestriction($id_shop_group, $id_shop)
        );
        if ($check) {
            return Db::getInstance()->update(
                $table,
                $data,
                $where
            );
        } else {
            $check = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.pSQL($table).'` WHERE ' . $where);
            if ($check) {
                $global = $check[0];
                $check_for_multishop_id = Db::getInstance()->executeS(
                    'SELECT * FROM `'._DB_PREFIX_.pSQL($table).'`
                      WHERE `name` = \'' . pSQL($global['name']) . '\' 
                        AND `template` = \'' . pSQL($global['template']) . '\' ' .
                    TouchizeBaseHelper::sqlRestriction($id_shop_group, $id_shop)
                );
                if ($check_for_multishop_id) {
                    $where = 'id_variable = ' . (int)$check_for_multishop_id[0]['id_variable'];
                    return Db::getInstance()->update(
                        $table,
                        $data,
                        $where
                    );
                } else {
                    $now = date('Y-m-d H:i:s');
                    return Db::getInstance()->insert($table, array(
                        'id_shop_group' => $id_shop_group ? (int)$id_shop_group : null,
                        'id_shop'       => $id_shop ? (int)$id_shop : null,
                        'name'          => pSQL($global['name']),
                        'description'   => pSQL($global['description']),
                        'value'         => pSQL($data['value']),
                        'is_color'      => pSQL($global['is_color']),
                        'template'      => pSQL($global['template']),
                        'date_add'      => $now,
                        'date_upd'      => $now,
                    ), true);
                }
            }
        }
    }
}
