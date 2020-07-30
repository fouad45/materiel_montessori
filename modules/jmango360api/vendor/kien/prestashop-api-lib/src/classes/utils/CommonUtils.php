<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CommonUtils
{
    /**
     * Check version => 1.7
     */
    public static function isV17()
    {
        return version_compare(_PS_VERSION_, '1.7', '>=');
    }

    /**
     * Clean html: strip tags, fix relative link and image source...
     *
     * @param string $html
     * @param array $strip_tags
     * @param bool $fix_link
     * @return string
     */
    public static function cleanHtml($html, $strip_tags = array(), $fix_link = true)
    {
        if (!$html) {
            return $html;
        }

        try {
            $doc = new DOMDocument();

            // Set error level to ignore some warnings
            $internalErrors = libxml_use_internal_errors(true);

            if (function_exists('mb_convert_encoding')) {
                $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            } elseif (function_exists('iconv')) {
                $doc->loadHTML(iconv('utf-8//TRANSLIT//IGNORE', 'HTML-ENTITIES', $html));
            } else {
                $doc->loadHTML($html);
            }

            // Restore error level
            libxml_use_internal_errors($internalErrors);

            $xpath = new DOMXPath($doc);

            if (!is_array($strip_tags)) {
                $strip_tags = array($strip_tags);
            }

            foreach ($strip_tags as $tag) {
                $whatsappElms = $xpath->query('//' . $tag);
                foreach ($whatsappElms as $whatsappElm) {
                    $whatsappElm->parentNode->removeChild($whatsappElm);
                }
            }

            $baseUrl = Configuration::get('PS_SSL_ENABLED') ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_;

            $links = $doc->getElementsByTagName('a');
            foreach ($links as $link) {
                //Extract and show the "href" attribute.
                $href = $link->getAttribute('href');
                if (strpos($href, 'http') === false) {
                    $href = $baseUrl . $href;
                    $link->setAttribute('href', $href);
                }
            }

            $images = $doc->getElementsByTagName('img');
            foreach ($images as $image) {
                $src = $image->getAttribute('src');
                if (strpos($src, 'http') === false) {
                    if (strpos($src, '//') === 0) {
                        if (Configuration::get('PS_SSL_ENABLED')) {
                            $src = 'https:' . $src;
                        } else {
                            $src = 'http:' . $src;
                        }
                    } else {
                        $src = $baseUrl . $src;
                    }
                    $image->setAttribute('src', $src);
                }
            }

            return str_replace(array('<body>', '</body>'), array('', ''), $doc->saveHTML($doc->getElementsByTagName('body')->item(0)));
        } catch (Exception $e) {
            return $html;
        }
    }

    /**
     * Parse payment data from html
     *
     * @param string $html
     * @param string|null $module_name
     * @return array
     * @throws
     */
    public static function parsePaymentFromHtml($html, $module_name = null)
    {
        if (!$html) {
            return array();
        }

        $doc = new DOMDocument();

        // Set error level to ignore some warnings
        $internalErrors = libxml_use_internal_errors(true);

        if (function_exists('mb_convert_encoding')) {
            $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        } elseif (function_exists('iconv')) {
            $doc->loadHTML(iconv('utf-8//TRANSLIT//IGNORE', 'HTML-ENTITIES', $html));
        } else {
            $doc->loadHTML($html);
        }

        // Restore error level
        libxml_use_internal_errors($internalErrors);

        $xpath = new DOMXPath($doc);

        $output = array();

        if ($module_name === 'cmcicpaiement') {
            $paymentElms = $xpath->query('//div[contains(@class,"payment_module")]');
        } else {
            $paymentElms = $xpath->query('//p[contains(@class,"payment_module")]');
        }
        foreach ($paymentElms as $index => $paymentElm) {
            $linkElms = $xpath->query('descendant::a', $paymentElm);
            foreach ($linkElms as $linkElm) {
                foreach ($linkElm->attributes as $attribute) {
                    if ($attribute->name == 'title') {
                        $output[$index]['title'] = trim($attribute->value);
                    } elseif ($attribute->name == 'href') {
                        $output[$index]['url'] = trim($attribute->value);
                    }
                }
                $output[$index]['description'] = trim($linkElm->nodeValue);
            }
            $imgElms = $xpath->query('descendant::img', $paymentElm);
            foreach ($imgElms as $imgElm) {
                foreach ($imgElm->attributes as $attribute) {
                    if ($attribute->name == 'src') {
                        $output[$index]['logo'] = trim($attribute->value);
                    } elseif ($attribute->name == 'title') {
                        if (empty($output[$index]['title'])) {
                            $output[$index]['title'] = trim($attribute->value);
                        }
                    } elseif ($attribute->name == 'alt') {
                        if (empty($output[$index]['title'])) {
                            $output[$index]['title'] = trim($attribute->value);
                        }
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Return a valid URL
     *
     * @param $url
     * @return string
     */
    public static function cleanUrl($url)
    {
        if (!$url) return $url;

        $baseUrl = Configuration::get('PS_SSL_ENABLED') ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_;

        if (strpos($url, 'http') === false) {
            if (strpos($url, '//') === 0) {
                if (Configuration::get('PS_SSL_ENABLED')) {
                    $url = 'https:' . $url;
                } else {
                    $url = 'http:' . $url;
                }
            } else {
                $url = $baseUrl . $url;
            }
        }

        return $url;
    }
}
