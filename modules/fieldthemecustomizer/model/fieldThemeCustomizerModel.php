<?php

class FieldThemeCustomizerModel {
    
    public static function findProductPos($id_category, $id_product)
    {
        $response = Db::getInstance()->getRow('
            SELECT position
			FROM '._DB_PREFIX_.'category_product
            WHERE id_category = ' . $id_category . '
            AND id_product = ' . $id_product
        );

        if ($response['position'] == null){
            return false;
        }

        return (int)$response['position'];
    }

    public static function findProductMaxPos($id_category)
    {
        $response = Db::getInstance()->getRow('
            SELECT MAX(position)
			FROM '._DB_PREFIX_.'category_product
            WHERE id_category = ' . $id_category
        );

        if ($response['MAX(position)'] == null){
            return false;
        }

        return (int)$response['MAX(position)'];
    }

    public static function getProductIdFromPos($id_category, $position)
    {
        $response = Db::getInstance()->getRow('
            SELECT id_product
			FROM '._DB_PREFIX_.'category_product
            WHERE id_category = ' . $id_category . '
            AND position = ' . $position
        );

        if ($response['id_product'] == null){
            return false;
        }

        return (int)$response['id_product'];
    }
}