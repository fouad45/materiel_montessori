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
 * Taxonomy helper.
 */

class TouchizeTaxonomyHelper extends TouchizeBaseHelper
{
    /**
     * @var array
     */
    protected $allowedCategories;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
        $this->useTopMenu = true;
        $this->helperMenu = new TouchizeTopMenuHelper();
        $this->allowedCategories = $this->helperMenu->getAllowedItems(true);

        $top_menu_module = 'blocktopmenu';

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $top_menu_module = 'ps_shoppingcart';
        }

        if (Module::isInstalled($top_menu_module) &&
            Module::isEnabled($top_menu_module)
        ) {
            $this->useTopMenu = true;
        }
    }

    /**
     * [getTree description]
     *
     * @return array
     */
    public function getTree()
    {
        if ($this->useTopMenu) {
            return $this->getTreeTopMenu();
        } else {
            return $this->getTreeRegular();
        }
    }

    /**
     * [getTreeTopMenu description]
     *
     * @return array
     */
    public function getTreeTopMenu()
    {
        $configured_menu = $this->getTreeTopMenuRoots();
        $categories = $this->generateMenuTree($configured_menu);
        $categoryTree = $this->remapCategory($categories);

        if (!$categoryTree) {
            $categoryTree = array();
        }

        $tree = array(
            'Tree' => $categoryTree
        );

        return $tree;
    }

    public function generateMenuTree($configured_menu)
    {
        $menu = array();
        foreach ($configured_menu as $menu_item) {
            $id = $menu_item['id'];
            if (isset($this->allowedCategories[$id])) {
                $item = $this->allowedCategories[$id];
                if (isset($menu_item['children']) && $menu_item['children']) {
                    $item['children'] = $this->generateMenuTree($menu_item['children']);
                }
                $menu[] = $item;
            }
        }
        return $menu;
    }

    /**
     * [getTreeTopMenuRoots description]
     *
     * @return array
     */
    public function getTreeTopMenuRoots()
    {
        $menu_items = $this->helperMenu->getMenuItems();
        return $menu_items;
    }

    /**
     * Map category tree to SLQ format
     *
     * @return array $tree SLQ taxonomy tree
     */
    public function getTreeRegular()
    {
        $idRoot = Category::getRootCategory()->id;
        $categories = Category::getNestedCategories(
            $idRoot,
            $this->context->language->id,
            true
        );
        //If only root, use the children instead if any
        if ($categories && count($categories) == 1) {
            if ($root = reset($categories)) {
                $categories = $root['children'];
            }
        }
        $categoryTree = $this->remapCategory($categories);
        if (!$categoryTree) {
            $categoryTree = array();
        }
        $extraItems = $this->getExtraTaxonomies();
        $taxonomies = array_merge($categoryTree, $extraItems);
        $tree = array(
          'Tree' => $taxonomies
        );

        return $tree;
    }

    /**
     * Map category tree to SLQ format
     *
     * @param  array  $categoryNode PrestaShop nested categories
     *
     * @return null|array            SLQ taxonomy tree
     */
    protected function remapCategory($categoryNode)
    {
        if ($categoryNode) {
            $children = array();
            foreach ($categoryNode as $child) {
                if ($child['active']) {
                    if (isset($child['url'])) {
                        $url = $child['url'];
                    } else {
                        $url = TouchizeControllerHelper::getRelativeURL(
                            $this->context->link->getCategoryLink(
                                $child['id_category']
                            )
                        );
                    }

                    $newNode = array(
                        'Id' => $child['id_category'],
                        'ParentId' => $child['id_parent'],
                        'Description' => $child['description'],
                        'Name' => $child['name'],
                        'IsActive' => $child['active'],
                        'Position' => $child['position'],
                        'Level' => $child['level_depth'],
                        'Url' => $url,
                        'SubTaxa' => array(),
                    );

                    if (array_key_exists('children', $child)) {
                        $newNode['SubTaxa'] = $this->remapCategory(
                            $child['children']
                        );
                    }

                    $children[] = $newNode;
                }
            }

            return $children;
        }

        return null;
    }

    /**
     * Add extra nodes to taxonomy tree
     *
     * @param  object $taxonomies SLQ taxonomy tree
     *
     * @return object
     */
    public function getExtraTaxonomies()
    {
            $extra_taxonomies = array(
                array(
                    'Id' => 'prices-drop',
                    'ParentId' => 0,
                    'Name' => $this->l('Specials'),
                    'IsActive' => 1,
                    'Position' => 0,
                    'Level' => 0,
                    'Url' => TouchizeControllerHelper::getRelativeURL(
                        $this->context->link->getPageLink('prices-drop')
                    ),
                    'SubTaxa'  => array(),
                ),
                array(
                    'Id' => 'best-sales',
                    'ParentId' => 0,
                    'Name' => $this->l('Best sellers'),
                    'IsActive' => 1,
                    'Position' => 0,
                    'Level' => 0,
                    'Url' => TouchizeControllerHelper::getRelativeURL(
                        $this->context->link->getPageLink('best-sales')
                    ),
                    'SubTaxa'  => array(),
                ),
                array(
                    'Id' => 'new-products',
                    'ParentId' => 0,
                    'Name' => $this->l('New arrivals'),
                    'IsActive' => 1,
                    'Position' => 0,
                    'Level' => 0,
                    'Url' => TouchizeControllerHelper::getRelativeURL(
                        $this->context->link->getPageLink('new-products')
                    ),
                    'SubTaxa' => array(),
                )
            );


        return $extra_taxonomies;
    }
}
