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
 * Product helper.
 */

class TouchizeProductHelper extends TouchizeBaseHelper
{
    const NO_IMAGE_PLACEHOLDER = 'placeholder';
    /**
     * @var int
     **/
    protected $pageSize = 48;
    private $hasTouchizeListImageType;
    private $hasTouchizeProductImageType;
    private $highRes;
    private $orderBy;
    private $orderWay;
    private $manufacturer;
    private $isManufacturer;
    private $helper17;


    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
        $this->stockManagement = Configuration::get('PS_STOCK_MANAGEMENT');
        $this->highRes = (bool)Configuration::get('PS_HIGHT_DPI');
        $this->imgProd = (ImageType::typeAlreadyExists('touchize_product_img') > 0)
            ? 'touchize_product_img'
            : ImageType::getFormatedName('large');
        $this->imgList = (ImageType::typeAlreadyExists('touchize_list_img') > 0)
            ? 'touchize_list_img'
            : ImageType::getFormatedName('home');
        $this->productSort();
        $this->manufacturer = null;
        $this->isManufacturer = false;
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->helper17 = new Touchize17ProductListingProcessor();
        }
    }

    private function productSort()
    {
        $order_by_values  = array(
            0 => 'name',
            1 => 'price',
            2 => 'date_add',
            3 => 'date_upd',
            4 => 'position',
            5 => 'manufacturer_name',
            6 => 'quantity',
            7 => 'reference'
        );
        $order_way_values = array(0 => 'asc', 1 => 'desc');

        $this->orderBy  = Tools::strtolower(
            Tools::getValue('orderby', $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')])
        );
        $this->orderWay = Tools::strtolower(
            Tools::getValue('orderway', $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')])
        );

        if (!in_array($this->orderBy, $order_by_values)) {
            $this->orderBy = $order_by_values[0];
        }

        if (!in_array($this->orderWay, $order_way_values)) {
            $this->orderWay = $order_way_values[0];
        }
    }

    /**
     * [getIndexProductList description]
     *
     * @param  string $categoryId
     * @param  int    $index
     *
     * @return array
     */
    public function getIndexProductList($categoryId, $index)
    {
        # Check if category is a multiple category (comma separated)
        if (false !== strpos($categoryId, ',')) {
            $categoryIds = array_map('trim', explode(',', $categoryId));

            $list = array();
            foreach ($categoryIds as $catId) {
                $catList = $this->getProductList($catId, 24);
                $list = array_merge($list, $catList);
            }

            return array(
                'Count' => count($list),
                'Id' => $categoryId,
                'Index' => 0,
                'Products' => $list,
                'Title' => '',
            );
        }

        $totalItems = 0;
        $title = null;
        $description = null;

        if (Category::categoryExists($categoryId)) {
            $category = new Category(
                $categoryId,
                $this->context->language->id
            );

            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $totalItems = $this->helper17->getTotalItems($categoryId);
            } else {
                $totalItems = $category->getProducts(
                    $this->context->language->id,
                    null,
                    null,
                    null,
                    null,
                    true
                );
            }
            
            $title = $category->name;
            $description = $category->description;
        } elseif (false !== Tools::strpos($categoryId, 'manufacturer')) {
            $this->isManufacturer = true;
            $mid = str_replace('manufacturer', '', $categoryId);
            if (empty($mid)) {
                //all, use products all, but sort later on manufacturer name
                $title = $this->l('Manufacturers');
                $categoryId = Category::getRootCategory()->id;
                $this->orderBy = 'manufacturer_name';

                $category = new Category(
                    $categoryId,
                    $this->context->language->id
                );
                $totalItems = $category->getProducts(
                    $this->context->language->id,
                    null,
                    null,
                    null,
                    null,
                    true
                );
            } else {
                $this->manufacturer = new Manufacturer((int)$mid, $this->context->language->id);
                $title = $this->manufacturer->name;
                $description = Tools::nl2br(trim($this->manufacturer->description));
                $totalItems = $this->manufacturer->getProducts(
                    $this->manufacturer->id,
                    null,
                    null,
                    null,
                    $this->orderBy,
                    $this->orderWay,
                    true
                );
            }
        } else {
            switch ($categoryId) {
                case 'prices-drop':
                    $title = $this->l('Specials');
                    break;
                case 'best-sales':
                    $title = $this->l('Best sellers');
                    break;
                case 'new-products':
                    $title = $this->l('New arrivals');
                    break;
            }
        }

        # If we don't get index, assume page 1
        $page = ($index ? $index / $this->pageSize : 0) + 1;

        $list = $this->getProductList(
            $categoryId,
            $this->pageSize,
            null,
            false,
            $page
        );

        $index += sizeof($list);

        if ($index >= $totalItems) {
            # In case of new-products, best-sales
            # and prices-drop, don't use index
            $index = 0;
        }

        return array(
            'Count' => $totalItems,
            'Id' => $categoryId,
            'Index' => $index,
            'Products' => $list,
            //Add title and description only if first page
            'Title'    => ($page === 1) ? $title : null,
            'Description'    => ($page === 1) ? $description : null
        );
    }

    /**
     * Get list of products from category
     *
     * @param  int      $categoryId         Id of category
     * @param  int      $maxProducts
     * @param  null|int $excludeProductId
     * @param  bool     $getRandom
     * @param  int      $page
     *
     * @return array                        SLQ Productlist
     */
    public function getProductList(
        $categoryId,
        $maxProducts = 48,
        $excludeProductId = null,
        $getRandom = false,
        $page = 1
    ) {
        if ($this->manufacturer == null && Validate::isInt($categoryId)) {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $list = $this->helper17->getProducts($categoryId, $maxProducts, $page);
            } else {
                $category = new Category($categoryId);
                # $id_lang, $p, $n, $order_by = null, $order_way = null,
                # $get_total = false, $active = true, $random = false,
                # $random_number_products = 1,
                # $check_access = true, Context $context = null
                $list = $category->getProducts(
                    $this->context->language->id,
                    $page,
                    $maxProducts,
                    $this->orderBy,
                    $this->orderWay,
                    false,
                    true,
                    $getRandom,
                    $maxProducts
                ); # TODO: Add config for this...
            }
        } elseif ($this->manufacturer != null) {
            $list = $this->manufacturer->getProducts(
                $this->manufacturer->id,
                $this->context->language->id,
                $page,
                $maxProducts,
                $this->orderBy,
                $this->orderWay
            );
        } else {
            # Since startpage is 0 for these -categories-
            $temppage = ($page - 1 < 0)
                ? 0
                : $page - 1;

            switch ($categoryId) {
                case 'new-products':
                    $list = Product::getNewProducts(
                        $this->context->language->id,
                        $temppage,
                        $maxProducts
                    );
                    break;
                case 'best-sales':
                    $list = ProductSale::getBestSales(
                        $this->context->language->id,
                        $temppage,
                        $maxProducts
                    );
                    break;
                case 'prices-drop':
                    $list = Product::getPricesDrop(
                        $this->context->language->id,
                        $temppage,
                        $maxProducts
                    );
                    break;
                default:
                    break;
            }
        }

        if (empty($list)) {
            return array();
        }

        $products = array();

        foreach ($list as $product) {
            if ($excludeProductId &&
                $excludeProductId === (int)$product['id_product']
            ) {
                continue;
            }

            array_push(
                $products,
                $this->getProduct($product['id_product'], false)
            );
        }

        $listing_controller = $this->versionResolver->getListingProcessor() ;
        array_map(array($listing_controller, 'prepareProductForTemplate'), $list);

        return $products;
    }

    /**
     * Get single product
     *
     * @param  int      $productId        Id of product
     * @param  boolean  $details          include details
     *
     * @return object                     SLQ Product
     */
    public function getProduct($productId, $details = true)
    {
        $product = new Product(
            $productId,
            true,
            $this->context->language->id
        );

        return $this->mapSlqProduct($product, $details);
    }

    /**
     * Map product to SLQ model
     *
     * @param  object  $product PrestaShop product
     * @param  boolean $details include details
     *
     * @return object           SLQ product
     */
    protected function mapSlqProduct($product, $details = false)
    {
        $imgTypeName = $details
            ? $this->imgProd
            : $this->imgList;

        $images = $this->getImages(
            $product,
            $imgTypeName
        );
        $hasVariants = $this->checkForVariants($product);
        $slqProduct = array(
            'Id' => $product->id,
            'Title' => $product->name,
            'HasBundles' => false,
            'HasVariants' => $hasVariants,
            'SingleVariantId' => ($hasVariants ? null : $product->id),
            'Label' => $this->getLabel($product),
            'Tags' => $product->getTags($this->context->language->id),
            'Images' => $images,
            'InStock' => ($product->quantity > 0) || !$this->stockManagement,
            'StockQty' => $product->quantity,
            'Brands' => $product->manufacturer_name,
            'SKU' => $product->reference,
            'EAN13' => $product->ean13 ? $product->ean13 : $product->upc ? $product->upc : null,
            'CTA' => $this->l('Drag to cart'),
            'Url' => TouchizeControllerHelper::getRelativeURL(
                $this->context->link->getProductLink(
                    $product->id,
                    $product->link_rewrite,
                    $product->id_category_default
                )
            ),
            'Saleable' => (bool)$product->available_for_order,
        );
        if ($slqProduct['InStock']) {
            $slqProduct['StockTitle'] = $this->l('In stock');
        } else {
            $slqProduct['StockTitle'] = $this->l('Out of stock');
        }

        $slqProduct = array_merge(
            $slqProduct,
            $this->getPrices(
                $product,
                $product->getDefaultIdProductAttribute()
            )
        );

        if ($details) {
            $slqProduct = array_merge($slqProduct, array(
                'Relations' => $this->getRelatedProducts($product),
                'Description' => !empty($product->description)
                    ? $this->transformDescriptionWithImg($product->description, $product)
                    : null,
                'ShortDescription' => $product->description_short,
                'AttributeSet' => $this->getAttributeSet($product),
                'Variants' => $this->getVariants($product),
                'VariantsSelectionText' => sprintf(
                    $this->l('Select %s'),
                    $this->getAttributeNames($product)
                ),
                'VariantsText' => sprintf(
                    $this->l('Drag %s to cart'),
                    $this->getAttributeNames($product)
                ),
//                'VariantsText' => $this->l('Choose one of the variants below.'),
            ));
            $this->getReviews($slqProduct);
        }

        return $slqProduct;
    }

    /**
     * [getReviews description]
     *
     * @param  array $slqProduct
     */
    protected function getReviews(&$slqProduct)
    {
        if (Module::isInstalled('productcomments') &&
            Module::isEnabled('productcomments') &&
            file_exists(_PS_MODULE_DIR_ .'productcomments/ProductComment.php') &&
            file_exists(_PS_MODULE_DIR_ .'productcomments/ProductCommentCriterion.php')
        ) {
            require_once _PS_MODULE_DIR_
                .'productcomments/ProductComment.php';
            require_once _PS_MODULE_DIR_
                .'productcomments/ProductCommentCriterion.php';

            $reviews = ProductComment::getByProduct($slqProduct['Id']);
            $criterions = ProductCommentCriterion::getByProduct(
                $slqProduct['Id'],
                $this->context->language->id
            );

            $ratings = array();
            foreach ($criterions as $criterion) {
                $ratings[] = array(
                    'Label' => $criterion['name'],
                    'Name'  => 'criterion['
                                    .$criterion['id_product_comment_criterion']
                                .']',
                    'Range' => array(1, 2, 3, 4, 5),
                );
            }

            $slqReviews = array();
            foreach ($reviews as $review) {
                $slqReviews[] = array(
                    'Id' => $review['id_product_comment'],
                    'Content' => $review['content'],
                    'Name' => $review['customer_name'],
                    'Date' => $review['date_add']
                        ? Tools::displayDate($review['date_add'])
                        : '',
                    'Rating' => $review['grade'],
                    'MaxRating' => '5',
                    'Title' => $review['title'],
                    'UsefulTotal' => $review['total_advice'],
                    'Useful' => $review['total_useful'],
                );
            }

            # Add form input data
            if (!$this->context->customer->id &&
                !Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS')
            ) {
                $slqReviewForm = null;
            } else {
                $slqReviewForm = array(
                    'Title'   => $this->l('Write a review'),
                    'Ratings' => $ratings,
                    'Input'   => array(
                        array(
                            'Type' => 'hidden',
                            'Name' => 'id_product',
                            'Value' => $slqProduct['Id'],
                        ),
                        array(
                            'Type' => 'text',
                            'Label' => $this->l('Title'),
                            'Name' => 'title',
                            'Value' => '',
                            'Required' => true,
                        ),
                        array(
                            'Type' => 'textarea',
                            'Label' => $this->l('Comment'),
                            'Name' => 'content',
                            'Value' => '',
                            'Required' => true,
                        ),
                        array(
                            'Type' => 'text',
                            'Label' => $this->l('Your name'),
                            'Name' => 'customer_name',
                            'Value' => '',
                            'Required' => true,
                        ),
                        array(
                            'Type' => 'button',
                            'Label' => null,
                            'Name' => 'submit',
                            'Value' => $this->l('Submit'),
                            'Required' => false,
                        ),
                    ),
                );
            }

            $average = ProductComment::getAverageGrade($slqProduct['Id']);

            $slqProduct['ProductReviews'] = array(
                'Title' => $slqReviewForm ? $this->l('Place a review') : null,
                'Reviews' => $slqReviews,
                'Form' => $slqReviewForm,
                'Average' => round($average['grade']),
            );
        }
    }

    /**
     * Generate AttributeSet from features
     *
     * @param  object $product PrestaShop product
     *
     * @return object          SLQ AttributeSet
     */
    protected function getAttributeSet($product)
    {
        $features = $product->getFrontFeatures($this->context->language->id);

        $attributes = array();
        foreach ($features as $feature) {
            array_push($attributes, array(
                'Id' => $feature['id_feature'],
                'Name' => $feature['name'],
                'Value' => $feature['value'],
            ));
        }

        return array(
            'Name' => $this->l('Features'),
            'Attributes' => $attributes,
        );
    }

    /**
     * Get related products based on products category
     *
     * @param  object $product PrestaShop product
     *
     * @return array           SLQ relations
     */
    protected function getRelatedProducts($product)
    {
        $list = $this->getProductList(
            $product->id_category_default,
            12,
            $product->id,
            true
        );

        if (empty($list)) {
            return null;
        }

        $relations = array();
        array_push($relations, array(
            'Name' => $this->l('Related products'),
            'Products' => $list,
        ));

        return $relations;
    }

    /**
     * Get label from product
     *
     * @param  object $product PrestaShop product
     *
     * @return array           TZ Label
     */
    protected function getLabel($product)
    {
        if ($product->new) {
            return array(
                'Text' => $this->l('New'),
                'Color' => null,
                'Background' => null,
                'Class' => 'new',
            );
        } elseif ($product->on_sale) {
            return array(
                'Text' => $this->l('Sale'),
                'Color' => null,
                'Background' => null,
                'Class' => 'sale',
            );
        } else {
            return array();
        }
    }

    /**
     * Get prices for product or productvariant
     *
     * @param  object $product              PrestaShop product
     * @param  int    $idProductAttribute   id of variant
     *
     * @return object                       SLQ prices
     */
    protected function getPrices($product, $idProductAttribute)
    {
        if ($product->isDiscounted($product->id)) {
            $price = $product->getPriceWithoutReduct(
                false,
                $idProductAttribute
            );
            $reducedPrice = $product->getPrice(true, $idProductAttribute);

            if ($price === $reducedPrice) {
                # To prevent variant discounts from showing
                $reducedPrice = null;
            }
        } else {
            $price = $product->getPrice(true, $idProductAttribute);
            $reducedPrice = null;
        }

        if ($price == 0) {
            $price = null;
        }

        if ($reducedPrice == 0) {
            $reducedPrice = null;
        }

        $currency = $this->context->currency;
        $decimals = 2;
        if (is_array($currency)) {
            $decimals = (int)$currency['decimals'] * _PS_PRICE_DISPLAY_PRECISION_;
        } elseif (is_object($currency)) {
            $decimals = (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
        }

        $prices = array(
            'FPrice'           => Tools::displayPrice($price, $currency),
            'FDiscountedPrice' => Tools::displayPrice($reducedPrice, $currency),
            'Price'            => $price ? Tools::ps_round($price, $decimals) : $price,
            'DiscountedPrice'  => $reducedPrice ? Tools::ps_round($reducedPrice, $decimals) : $reducedPrice,
            'Currency'         => $this->context->currency->iso_code
        );

        /*
            # Nettotobak extra
            if ($prices['FPrice'] != null) {
              $prices['FPrice'] .= 'kr';
            }
            if ($prices['FDiscountedPrice'] != null) {
              $prices['FDiscountedPrice'] .= 'kr';
            }
        */

        return $prices;
    }

    /**
     * Map image to SLQ format
     *
     * @param  object $product PrestaShop Product
     * @param  object $image   PrestaShop Image
     * @param  string $size    Size definition ('home_default', 'large_default')
     *
     * @return array           SLQ Image
     */
    protected function mapImage($product, $image, $size)
    {
        //Disabled since if thumbnails not regenerated the link will be broken.
        //The rewrites in .htaccess use only the image-type.
        //So it has no effect to add '2x' to end of the filename as is done in default theme.
        //There is a need to run regenerate thumbnails.
        // if ($this->highRes) {
        //     $size = $size . '2x';
        // }

        # TODO: Find a better way to get correct element
        $linkRewrite = $product->link_rewrite;
        # TODO: Find a better way to get correct element
        $productName = $product->name;

        if ($image == self::NO_IMAGE_PLACEHOLDER) {
            return array(
                'Name' => $this->getDefaultPlaceholder(),
                'Alt'  => $productName,
            );
        }

        $link = new Link();
        $url = Tools::getShopProtocol() . $link->getImageLink(
            $linkRewrite,
            $image['id_image'],
            $size
        );

        return array(
            'Name' => $url,
            'Alt'  => array_key_exists('legend', $image) && !empty($image['legend'])
                ? $image['legend']
                : $productName
        );
    }

    /**
     * Get images for product
     *
     * @param  object $product
     *
     * @return array            Images array
     */
    protected function getImages($product, $size)
    {
        $images = array();
        $imageList = $product->getImages($this->context->language->id);

        //sort images array to set 'cover' image first
        $imageListSorted = array();
        foreach ($imageList as $key => $image) {
            $imageListSorted[$key] = $image['cover'] ?: 0;
        }
        array_multisort($imageListSorted, SORT_DESC, SORT_NUMERIC, $imageList);

        foreach ($imageList as $image) {
            array_push($images, $this->mapImage($product, $image, $size));
        }
        if (empty($images)) {
            array_push($images, $this->mapImage($product, self::NO_IMAGE_PLACEHOLDER, $size));
        }

        return $images;
    }

    /**
     * Get variants for product
     *
     * @param  object $product PrestaShop product
     *
     * @return array           SLQ ProductVariants
     */
    protected function getVariants($product)
    {
        $variants = array();
        $ids = $product->getAttributesGroups($this->context->language->id);
        $includePrices = $this->checkVariantPrices($product);

        //filtering duplicates from $ids
        $tmpIds = array();
        $filteredIds = array();
        if ($ids) {
            foreach ($ids as $id) {
                if (!in_array($id['id_product_attribute'], $tmpIds)) {
                    array_push($tmpIds, $id['id_product_attribute']);
                    array_push($filteredIds, $id);
                }
            }
        }
        if ($filteredIds) {
            foreach ($filteredIds as $variant) {
                $variantData = array(
                    'Id' => $variant['id_product_attribute'],
                    'ProductId' => $product->id,
                    'Images' => $this->getVariantImages(
                        $product,
                        $variant['id_product_attribute']
                    ),
                    'Attributes' => $this->getVariantAttributes(
                        $product,
                        $variant['id_product_attribute']
                    ),
                );
                if (array_key_exists('quantity', $variant) && Validate::isInt($variant['quantity'])) {
                    $variantData['InStock'] = ((int)$variant['quantity']) > 0 || !$this->stockManagement;
                    $variantData['StockQty'] = (int)$variant['quantity'];
                }

                if ($includePrices) {
                    $variantData = array_merge(
                        $variantData,
                        $this->getPrices(
                            $product,
                            $variant['id_product_attribute']
                        )
                    );
                }

                array_push($variants, $variantData);
            }
        }

        return $variants;
    }

    /**
     * [checkVariantPrices description]
     *
     * @param  object $product
     *
     * @return bool
     */
    protected function checkVariantPrices($product)
    {
        $variants = $product->getAttributesResume(
            $this->context->language->id
        );
        if ($variants) {
            foreach ($variants as $variant) {
                if ($product->getPrice(true) !== $product->getPrice(
                    true,
                    $variant['id_product_attribute']
                )) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get images for variant
     *
     * @param  object $product      PrestaShop product
     * @param  int    $attributeId  id of variant
     *
     * @return array                array of SLQ images
     */
    protected function getVariantImages($product, $attributeId)
    {
        $images = array();
        $image = $this->getCombinationImageById(
            $product,
            $attributeId,
            $this->context->language->id
        );

        if (!$image) {
            # If we don't have a specific images connected to the variant,
            # get cover-image from product
            $image = $product->getCover($product->id);
        }

        array_push($images, $this->mapImage(
            $product,
            $image,
            ImageType::getFormatedName('home')
        ));

        return $images;
    }

    /**
     * Split string of attributes into array
     *
     * @param  string $designation Attributes string
     *
     * @return array               Array of attributes
     */
    protected function getVariantAttributes($product, $idProductAttribute)
    {
        $attributes = $product->getAttributesParams(
            $product->id,
            $idProductAttribute
        );
        $attr = array();

        foreach ($attributes as $attribute) {
            $attr[] = $attribute['name'];
        }

        return $attr;
    }

    /**
     * Check if product has variants
     *
     * @param  object $product PrestaShop product
     *
     * @return bool
     */
    protected function checkForVariants($product)
    {
        $hasVariants = $product->getProductAttributesIds($product->id);
        if (!empty($hasVariants)) {
            # We have real variants, no need for further checking
            return true;
        }

        return false;
    }

    /**
     * Returns string of product attributes
     *
     * @param  object $product PrestaShop product
     *
     * @return string          Attributes
     */
    protected function getAttributeNames($product)
    {
        $groups = array();
        $attributes_groups = $product->getAttributesGroups(
            $this->context->language->id
        );

        if (is_array($attributes_groups) &&
            $attributes_groups
        ) {
            foreach ($attributes_groups as $row) {
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[
                        $row['id_attribute_group']
                    ] = $row['public_group_name'];
                }
            }
        }

        return implode(' & ', $groups);
    }
    
    /**
     * Returns parsed product description
     *
     * @param string $desc
     * @param object $product PrestaShop product
     * @return string
     */
    protected function transformDescriptionWithImg($desc, $product)
    {
        $reg = '/\[img\-([0-9]+)\-(left|right)\-([a-zA-Z0-9-_]+)\]/';
        while (preg_match($reg, $desc, $matches)) {
            $link_lmg = $this->context->link->getImageLink(
                $product->link_rewrite,
                $product->id.'-'.$matches[1],
                $matches[3]
            );
            $class = $matches[2] == 'left' ? 'class="imageFloatLeft"' : 'class="imageFloatRight"';
            $html_img = '<img src="'.$link_lmg.'" alt="" '.$class.'/>';
            $desc = str_replace($matches[0], $html_img, $desc);
        }
        return $desc;
    }
    
    /**
     * Safety to work with versions less than 1.6.0.12
     */
    private function getCombinationImageById($product, $id_product_attribute, $id_lang)
    {

        if (!Combination::isFeatureActive() || !$id_product_attribute) {
            return false;
        }

        if (is_callable('Product::getCombinationImageById')) {
            $image = $product->getCombinationImageById(
                $id_product_attribute,
                $this->context->language->id
            );

            return $image;
        }

        $result = Db::getInstance()->executeS('
            SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
            FROM `'._DB_PREFIX_.'product_attribute_image` pai
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (il.`id_image` = pai.`id_image`)
            LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = pai.`id_image`)
            WHERE pai.`id_product_attribute` = '.(int)$id_product_attribute.' 
            AND il.`id_lang` = '.(int)$id_lang.' ORDER by i.`position` LIMIT 1
            ');

        if (!$result) {
            return false;
        }

        return $result[0];
    }

    /**
     * @return string
     */
    public function getDefaultPlaceholder()
    {
        return _PS_BASE_URL_._PS_PROD_IMG_.$this->context->language->iso_code.'.jpg';
    }
}
