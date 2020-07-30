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
 * Touchmap helper.
 */

class TouchizeTouchmapHelper extends TouchizeBaseHelper
{
    /**
     * Get touchmaps.
     *
     * @param  string $categoryId
     *
     * @return array
     */
    public function getTouchmaps($categoryId)
    {
        $mobile = $this->context->mobile_detect->isMobile();
        $tablet = $this->context->mobile_detect->isTablet();

        if ($tablet) {
            $mobile = false;
        }

        # Check if category is a multiple category (comma separated)
        if (false !== strpos($categoryId, ',')) {
            $categoryIds = array_map('trim', explode(',', $categoryId));

            $touchMaps = array();
            foreach ($categoryIds as $catId) {
                $tmList = TouchizeTouchmap::getTouchmaps(
                    $catId,
                    true,
                    $mobile,
                    $tablet
                );
                $touchMaps = array_merge($touchMaps, $tmList);
            }
        } else {
            # Single category
            $touchMaps = TouchizeTouchmap::getTouchmaps(
                $categoryId,
                true,
                $mobile,
                $tablet
            );
        }

        $response = array();
        foreach ($touchMaps as $touchmap) {
            array_push($response, $this->mapTouchmap($touchmap));
        }

        return $response;
    }

    /**
     * [mapTouchmap description]
     *
     * @param  array $touchmap
     *
     * @return array
     */
    protected function mapTouchmap($touchmap)
    {
        $aspectRatio = '0%';
        if (0 != $touchmap['width']) {
            $aspectRatio = $touchmap['height'] / $touchmap['width'] * 100 . '%';
        }

        return array(
            'Id' => $touchmap['id_touchize_touchmap'],
            'Visible' => $touchmap['active'],
            'UseInSlider' => (bool)$touchmap['inslider'],
            'ImageUrl' => $this->context->link->getMediaLink(
                _PS_IMG_.'touchmaps/'.$touchmap['id_touchize_touchmap'].'.jpg'
            ),
            'Title' => $touchmap['name'],
            'AspectRatio' => $aspectRatio,
            'RunOnce' => (bool)$touchmap['runonce'],
            'Map' => TouchizeActionarea::getActionAreas(
                $touchmap['id_touchize_touchmap']
            ),
        );
    }
}
