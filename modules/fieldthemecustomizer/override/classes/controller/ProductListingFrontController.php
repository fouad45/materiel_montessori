<?php
abstract class ProductListingFrontController extends ProductListingFrontControllerCore
{
    protected function prepareProductArrayForAjaxReturn(array $products)
    {
        $allowed_properties = array('id_product','images', 'price', 'reference', 'active', 'description_short', 'link',
            'link_rewrite', 'name', 'manufacturer_name', 'position', 'url', 'canonical_url', 'add_to_cart_url',
            'has_discount', 'discount_type', 'discount_percentage', 'discount_percentage_absolute', 'discount_amount',
            'price_amount', 'regular_price_amount', 'regular_price', 'discount_to_display', 'labels', 'main_variants',
            'unit_price', 'tax_name', 'rate'
        );
        foreach ($products as $product_key => $product) {
            foreach ($product as $product_property => $data) {
                if (!in_array($product_property, $allowed_properties)) {
                    unset($products[$product_key][$product_property]);
                }
            }
        }
        return $products;
    }
}
