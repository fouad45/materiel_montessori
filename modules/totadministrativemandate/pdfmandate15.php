<?php
/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class PDFMandate extends FPDF
{
    protected static $order = null;
    protected static $orderReturn = null;
    protected static $orderSlip = null;
    protected static $l_cache;
    protected static $delivery = null;
    protected static $priceDisplayMethod;

    protected $module;
    protected $NewPageGroup; // variable indicating whether a new group was requested
    protected $PageGroups; // variable containing the number of pages of the groups
    protected $CurrPageGroup; // variable containing the alias of the current page group

    protected $prodTaxDetails = array();
    protected $hasEcoTax = false;

    /** @var object Order currency object */
    protected static $currency = null;
    protected static $iso;

    /** @var array Special PDF params such encoding and font */
    protected static $pdfparams = array();
    protected static $fpdf_core_fonts = array(
        'courier',
        'helvetica',
        'helveticab',
        'helveticabi',
        'helveticai',
        'symbol',
        'times',
        'timesb',
        'timesbi',
        'timesi',
        'zapfdingbats',
    );

    /**
     * Constructor.
     */
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        $this->module = Module::getInstanceByName('totadministrativemandate');

        if (!isset(Context::getContext()->cookie) or !is_object(Context::getContext()->cookie)) {
            Context::getContext()->cookie->id_lang = (int) (Configuration::get('PS_LANG_DEFAULT'));
        }

        self::$iso = Tools::strtoupper(Language::getIsoById(Context::getContext()->cookie->id_lang));
        FPDF::FPDF($orientation, $unit, $format);
        $this->initPDFFonts();
    }

    // create a new page group; call this before calling AddPage()
    public function startPageGroup()
    {
        $this->NewPageGroup = true;
    }

    protected function initPDFFonts()
    {
        if (!$languages = Language::getLanguages()) {
            die(Tools::displayError());
        }

        $default_encoding = 'iso-8859-1';
        $default_font = 'helvetica';

        foreach ($languages as $language) {
            $isoCode = Tools::strtoupper($language['iso_code']);
            $conf = Configuration::getMultiple(
                array(
                    'PS_PDF_ENCODING_'.$isoCode,
                    'PS_PDF_FONT_'.$isoCode,
                )
            );

            $encoding = $default_encoding;

            if (array_key_exists('PS_PDF_ENCODING_'.$isoCode, $conf)) {
                if ($conf['PS_PDF_ENCODING_'.$isoCode]) {
                    $encoding = $conf['PS_PDF_ENCODING_'.$isoCode];
                }
            }

            $font = $default_font;

            if (array_key_exists('PS_PDF_FONT_'.$isoCode, $conf)) {
                if ($conf['PS_PDF_FONT_'.$isoCode]) {
                    $font = $conf['PS_PDF_FONT_'.$isoCode];
                }
            }

            self::$pdfparams[$isoCode] = array(
                'encoding' => $encoding,
                'font' => $font,
            );
        }

        if ($font = self::embedfont()) {
            $this->AddFont($font);
            $this->AddFont($font, 'B');
        }

        /* If the user is using a ISO code no present in the languages, use the first language available instead */
        if (!isset(self::$pdfparams[self::$iso]) && isset($languages[0]['iso_code'])) {
            self::$iso = Tools::strtoupper($languages[0]['iso_code']);
        }
    }

    public static function encoding()
    {
        $param = 'iso-8859-1';
        if (isset(self::$pdfparams[self::$iso]) && is_array($params = self::$pdfparams[self::$iso])) {
            if (array_key_exists('encoding', $params)) {
                $param = $params['encoding'];
            }
        }

        return $param;
    }

    public static function embedfont()
    {
        $param = false;
        if (isset(self::$pdfparams[self::$iso]) && is_array($params = self::$pdfparams[self::$iso])) {
            if (array_key_exists('font', $params) && !in_array($params['font'], self::$fpdf_core_fonts)) {
                $param = $params['font'];
            }
        }

        return $param;
    }

    public static function fontname()
    {
        $font = self::embedfont();
        if (in_array(self::$pdfparams[self::$iso]['font'], self::$fpdf_core_fonts)) {
            $font = self::$pdfparams[self::$iso]['font'];
        }

        return $font ? $font : 'Arial';
    }

    /**
     * Generate the header addresses for pdf File.
     */
    public function generateHeaderAddresses($order, $addressType, $patternRules, $width)
    {
        $maxY = 0;
        $this->setY($this->GetY());
        $this->SetFont(self::fontname(), 'B', 7);
        $this->SetFillColor(240, 240, 240);
        foreach (array_keys($addressType) as $type) {
            if ($type == 'invoice') {
                $message = $this->module->l('Address invoice', 'pdfmandate15');
                $border = 'RLT';
            } else {
                $message = $this->module->l('Address delivery', 'pdfmandate15');
                $border = 'LT';
            }

            $this->Cell($width, 6, $message, $border, 0, null, true);
        }
        $this->SetFont(self::fontname(), '', 7);
        $this->Ln(6);

        foreach ($addressType as $type => &$value) {
            $currentY = $this->getY();
            $attributeName = 'id_address_'.$type;
            $value['displayed'] = '';

            $address_object = new Address((int) $order->$attributeName);
            $value['addressObject'] = $address_object;
            $value['addressFields'] = AddressFormat::getOrderedAddressFields((int) $address_object->id_country);
            $value['addressFormatedValues'] = AddressFormat::getFormattedAddressFieldsValues(
                $address_object,
                $addressType[$type]['addressFields']
            );

            foreach ($value['addressFields'] as $line) {
                if (($patternsList = explode(' ', $line))) {
                    $temp = '';

                    foreach ($patternsList as $pattern) {
                        if (!in_array($pattern, $patternRules['avoid'])) {
                            if ($pattern == 'State:name'
                                && Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT')) == 'US'
                            ) {
                                $state_name = &$value['addressFormatedValues'][$pattern];
                                $state = new State((int) State::getIdByName($state_name));
                                if (Validate::isLoadedObject($state)) {
                                    $state_name = $state->iso_code;
                                } else {
                                    $state_name = Tools::strtoupper(Tools::substr($state_name, 0, 2));
                                }
                            }
                        }

                        if (isset($value['addressFormatedValues'][$pattern]) &&
                            !empty($value['addressFormatedValues'][$pattern])) {
                            $address_formated = $value['addressFormatedValues'][$pattern];
                            $temp .= Tools::iconv('utf-8', self::encoding(), $address_formated).' ';
                        }
                    }

                    $tmp = trim($temp);
                    $value['displayed'] .= (!empty($tmp)) ? $tmp."\n" : '';
                }
            }

            if ($type == 'invoice') {
                $border = 'RLB';
            } else {
                $border = 'LB';
            }

            $this->MultiCell($width, 6, $value['displayed'], $border, 'L', 0);

            if ($this->GetY() > $maxY) {
                $maxY = $this->GetY();
            }

            $this->SetY($currentY);
            $this->SetX($width + 10);
        }

        $this->SetY($maxY);

        if ($maxY) {
            $this->Ln(5);
        }

        return $addressType;
    }

    /**
     * Product table with price, quantities...
     */
    public function prodTab($delivery = false)
    {
        if (!$delivery) {
            $w = array(100, 15, 30, 15, 30);
        } else {
            $w = array(120, 30, 10);
        }

        self::prodTabHeader($delivery);
        if (!self::$orderSlip) {
            if (isset(self::$order->products) and sizeof(self::$order->products)) {
                $products = self::$order->products;
            } else {
                $products = self::$order->getProducts();
            }
        } else {
            $products = self::$orderSlip->getOrdersSlipProducts(self::$orderSlip->id, self::$order);
        }

        $customizedDatas = Product::getAllCustomizedDatas((int) (self::$order->id_cart));
        Product::addCustomizationPrice($products, $customizedDatas);

        $counter = 0;
        $lines = 25;
        $lineSize = 0;
        $line = 0;

        foreach ($products as $product) {
            $quantity = (int) $product['product_quantity'] - (int) $product['product_quantity_refunded'];

            if (!$this->hasEcoTax && (float) $product['ecotax'] != 0.0) {
                $this->hasEcoTax = true;
            }

            $hasProductTaxRate = isset($this->prodTaxDetails[ (string) $product['tax_rate'] ]);
            $this->prodTaxDetails[ (string) $product['tax_rate'] ] = array(
                'total_tax_excl' => $product['total_price_tax_excl'] +
                    ($hasProductTaxRate ? $this->prodTaxDetails[ (string) $product['tax_rate'] ]['total_tax_excl'] : 0),
                'total_tax' => ($product['total_price_tax_excl'] * ($product['tax_rate'] / 100)) +
                    ($hasProductTaxRate ? $this->prodTaxDetails[ (string) $product['tax_rate'] ]['total_tax'] : 0),
                'ecotax' => ($product['ecotax'] * $product['product_quantity']) +
                    ($hasProductTaxRate ? $this->prodTaxDetails[ (string) $product['tax_rate'] ]['ecotax'] : 0),
                'total_tax_incl' => $product['total_price_tax_incl'] +
                    ($hasProductTaxRate ? $this->prodTaxDetails[ (string) $product['tax_rate'] ]['total_tax_incl'] : 0),
            );

            if (!$delivery || ($quantity > 0)) {
                if ($counter >= $lines) {
                    $this->AddPage();
                    $this->Ln();
                    self::prodTabHeader($delivery);
                    $lineSize = 0;
                    $counter = 0;
                    $lines = 40;
                    ++$line;
                }
                $counter = $counter + ($lineSize / 5);

                $i = -1;

                // Unit vars
                $unit_without_tax = $product['product_price'] + $product['ecotax'];
                $unit_with_tax = $product['product_price_wt'];
                if (self::$priceDisplayMethod == PS_TAX_EXC) {
                    $unit_price = &$unit_without_tax;
                } else {
                    $unit_price = &$unit_with_tax;
                }

                $productQuantity = $delivery ? $quantity : (int) ($product['product_quantity']);

                if ($productQuantity <= 0) {
                    continue;
                }

                // Total prices
                $total_with_tax = $unit_with_tax * $productQuantity;
                $total_without_tax = $unit_without_tax * $productQuantity;
                // Spec
                if (self::$priceDisplayMethod == PS_TAX_EXC) {
                    $final_price = &$total_without_tax;
                } else {
                    $final_price = &$total_with_tax;
                }

                // End Spec

                $id_product = (int) $product['product_id'];
                $ipa = (int) $product['product_attribute_id'];

                if (isset($customizedDatas[$id_product][$ipa])) {
                    $custoLabelTmp = '';

                    foreach ($customizedDatas[$id_product][$ipa] as $customizedData) {
                        $customizationGroup = $customizedData['datas'];
                        $nb_images = 0;

                        if (array_key_exists(_CUSTOMIZE_FILE_, $customizationGroup)) {
                            $nb_images = sizeof($customizationGroup[_CUSTOMIZE_FILE_]);
                        }

                        if (array_key_exists(_CUSTOMIZE_TEXTFIELD_, $customizationGroup)) {
                            foreach ($customizationGroup[_CUSTOMIZE_TEXTFIELD_] as $customization) {
                                if (!empty($customization['name'])) {
                                    $custoLabelTmp .= '- '.$customization['name'].': '.$customization['value']."\n";
                                }
                            }
                        }

                        if ($nb_images > 0) {
                            $custoLabelTmp .= '- '.$nb_images.' ';
                            $custoLabelTmp .= $this->displayMessageUTF8($this->module->l('image(s)', 'pdfmandate15'))."\n";
                        }

                        $custoLabelTmp .= "---\n";
                    }

                    $custoLabel = rtrim($custoLabelTmp, "---\n");

                    if ($delivery) {
                        $this->SetX(25);
                    }

                    $before = $this->GetY();

                    $full_name = Tools::iconv('utf-8', self::encoding(), $product['product_name']);
                    $full_name .= ' - '.$this->displayMessageUTF8($this->module->l('Customized', 'pdfmandate15'))." \n";
                    $full_name .= Tools::iconv('utf-8', self::encoding(), $custoLabel);

                    $this->MultiCell(
                        $w[++$i],
                        5,
                        $full_name,
                        'LB'
                    );

                    $lineSize = $this->GetY() - $before;

                    $this->SetXY($this->GetX() + $w[0] + ($delivery ? 15 : 0), $this->GetY() - $lineSize);
                    $this->Cell($w[++$i], $lineSize, $product['product_reference'], 'B');

                    if (!$delivery) {
                        $amount = self::$orderSlip ? '-' : '';
                        $amount .= self::convertSign(Tools::displayPrice($unit_price, self::$currency, true));

                        $this->Cell(
                            $w[++$i],
                            $lineSize,
                            $amount,
                            'B',
                            0,
                            'R'
                        );
                    }

                    $this->Cell(
                        $w[++$i],
                        $lineSize,
                        (int) ($product['customizationQuantityTotal']),
                        ($delivery ? 'R' : '').'B',
                        0,
                        'C'
                    );

                    if (!$delivery) {
                        $price_custom = $unit_price * (int) $product['customizationQuantityTotal'];

                        $amount = self::$orderSlip ? '-' : '';
                        $amount .= self::convertSign(Tools::displayPrice($price_custom, self::$currency, true));

                        $this->Cell(
                            $w[++$i],
                            $lineSize,
                            $amount,
                            'RB',
                            0,
                            'R'
                        );
                    }
                    $this->Ln();
                    $i = -1;
                    $total_with_tax = $unit_with_tax * $quantity;
                    $total_without_tax = $unit_without_tax * $quantity;
                }

                if ($delivery) {
                    $this->SetX(25);
                }

                if ($quantity) {
                    $before = $this->GetY();

                    $product_name = Tools::iconv('utf-8', self::encoding(), $product['product_name']);

                    $this->MultiCell($w[++$i], 5, $product_name, 'LB');

                    $lineSize = $this->GetY() - $before;
                    $this->SetXY($this->GetX() + $w[0] + ($delivery ? 15 : 0), $this->GetY() - $lineSize);

                    $product_reference = '--';

                    if ($product['product_reference']) {
                        $product_reference = Tools::iconv('utf-8', self::encoding(), $product['product_reference']);
                    }

                    $this->Cell($w[++$i], $lineSize, $product_reference, 'B');

                    if (!$delivery) {
                        $amount = self::$orderSlip ? '-' : '';
                        $amount .= self::convertSign(Tools::displayPrice($unit_price, self::$currency, true));

                        $this->Cell(
                            $w[++$i],
                            $lineSize,
                            $amount,
                            'B',
                            0,
                            'R'
                        );
                    }
                    $this->Cell($w[++$i], $lineSize, $quantity, ($delivery ? 'R' : '').'B', 0, 'C');
                    if (!$delivery) {
                        $amount = self::$orderSlip ? '-' : '';
                        $amount .= self::convertSign(Tools::displayPrice($final_price, self::$currency, true));

                        $this->Cell(
                            $w[++$i],
                            $lineSize,
                            $amount,
                            'RB',
                            0,
                            'R'
                        );
                    }
                    $this->Ln();
                }
            }
        }

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $getDiscounts = self::$order->getCartRules();
        } else {
            $getDiscounts = self::$order->getDiscounts();
        }

        if (!sizeof($getDiscounts) && !$delivery) {
            $this->Cell(array_sum($w), 0, '');
        }
    }

    public function prodTabHeader($delivery = false)
    {
        if (!$delivery) {
            $header = array(
                array($this->displayMessageUTF8($this->module->l('Description', 'pdfmandate15')), 'L'),
                array($this->displayMessageUTF8($this->module->l('Reference', 'pdfmandate15')), 'L'),
                array($this->displayMessageUTF8($this->module->l('U. price', 'pdfmandate15')), 'R'),
                array($this->displayMessageUTF8($this->module->l('Qty', 'pdfmandate15')), 'C'),
                array($this->displayMessageUTF8($this->module->l('Total', 'pdfmandate15')), 'R'),
            );
            $w = array(100, 15, 30, 15, 30);
        } else {
            $header = array(
                array($this->displayMessageUTF8($this->module->l('Description', 'pdfmandate15')), 'L'),
                array($this->displayMessageUTF8($this->module->l('Reference', 'pdfmandate15')), 'L'),
                array($this->displayMessageUTF8($this->module->l('Qty', 'pdfmandate15')), 'C'),
            );
            $w = array(120, 30, 10);
        }
        $this->SetFont(self::fontname(), 'B', 8);
        $this->SetFillColor(240, 240, 240);
        if ($delivery) {
            $this->SetX(25);
        }

        for ($i = 0; $i < sizeof($header); ++$i) {
            $this->Cell(
                $w[$i],
                5,
                $header[$i][0],
                ($i == 0 ? 'L' : ($i == (sizeof($header) - 1) ? 'R' : '')).'T',
                0,
                $header[$i][1],
                1
            );
        }
        $this->Ln();
        $this->SetFont(self::fontname(), '', 8);
    }

    protected static function convertSign($s)
    {
        $arr = array();
        $arr['before'] = array('€', '£', '¥');
        $arr['after'] = array(chr(128), chr(163), chr(165));

        return str_replace($arr['before'], $arr['after'], $s);
    }

    /**
     * Discount table with value, quantities...
     */
    public function discTab()
    {
        $w = array(90, 25, 15, 10, 25, 25);
        $this->SetFont(self::fontname(), 'B', 7);

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $discounts = self::$order->getCartRules();
        } else {
            $discounts = self::$order->getDiscounts();
        }

        foreach ($discounts as $discount) {
            $this->Cell(
                $w[0],
                6,
                $this->displayMessageUTF8($this->module->l('Discount:', 'pdfmandate15')).' '.$discount['name'],
                'B'
            );
            $this->Cell(
                $w[1],
                6,
                '',
                'B'
            );
            $this->Cell(
                $w[2],
                6,
                '',
                'B'
            );
            $this->Cell(
                $w[3],
                6,
                '',
                'B',
                0,
                'R'
            );
            $this->Cell(
                $w[4],
                6,
                '1',
                'B',
                0,
                'C'
            );

            $discount_valueTmp = $discount['value'];
            $tax_rate_discount = 0;
            if (self::$priceDisplayMethod == PS_TAX_EXC) {
                $tax_rate_discount = self::$order->getTaxesAverageUsed();
            }

            $discount_value = $discount_valueTmp / (1 + $tax_rate_discount / 100);

            $amount = !self::$orderSlip && $discount['value'] != 0.00 ? '-' : '';
            $amount .= self::convertSign(Tools::displayPrice($discount_value, self::$currency, true));

            $this->Cell(
                $w[5],
                6,
                $amount,
                'B',
                0,
                'R'
            );
            $this->Ln();
        }

        if (sizeof($discounts)) {
            $this->Cell(array_sum($w), 0, '');
        }
    }

    /**
     * Tax table.
     */
    public function taxTab()
    {
        $taxable_address = new Address((int) self::$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        if (Tools::strtoupper(Country::getIsoById((int) $taxable_address->id_country)) == 'CA') {
            return;
        }

        $vat_number_management = Configuration::get('VATNUMBER_MANAGEMENT');
        $vat_number_country = Configuration::get('VATNUMBER_COUNTRY');

        if ($vat_number_management
            && !empty($taxable_address->vat_number)
            && $taxable_address->id_country != $vat_number_country
        ) {
            $this->Ln();
            $msg = $this->module->l('Exempt of VAT according section 259B of the General Tax Code.', 'pdfmandate15');
            $this->Cell(
                30,
                0,
                $this->displayMessageUTF8($msg),
                0,
                0,
                'L'
            );

            return;
        }

        if (self::$order->total_paid == '0.00'
            || (
                !(int) Configuration::get('PS_TAX')
                && self::$order->total_products == self::$order->total_products_wt
            )
        ) {
            return;
        }

        $carrier_tax_rate = (float) self::$order->carrier_tax_rate;
        if (self::$order->total_products == self::$order->total_products_wt
            && (!$carrier_tax_rate || $carrier_tax_rate == '0.00')
            && (!self::$order->total_wrapping || self::$order->total_wrapping == '0.00')
        ) {
            return;
        }

        // Displaying header tax
        if ($this->hasEcoTax) {
            $header = array(
                $this->displayMessageUTF8($this->module->l('Tax detail', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Tax', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Pre-Tax Total', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Total Tax', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Ecotax (Tax Incl.)', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Total with Tax', 'pdfmandate15')),
            );
            $w = array(60, 20, 40, 20, 30, 20);
        } else {
            $header = array(
                $this->displayMessageUTF8($this->module->l('Tax detail', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Tax', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Pre-Tax Total', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Total Tax', 'pdfmandate15')),
                $this->displayMessageUTF8($this->module->l('Total with Tax', 'pdfmandate15')),
            );
            $w = array(60, 30, 40, 30, 30);
        }
        $this->SetFont(self::fontname(), 'B', 8);
        for ($i = 0; $i < sizeof($header); ++$i) {
            $this->Cell($w[$i], 5, $header[$i], 0, 0, 'R');
        }

        $this->Ln();
        $this->SetFont(self::fontname(), '', 7);

        $nb_tax = 0;

        $order_slip = self::$orderSlip ? '-' : '';

        // Display product tax
        foreach ($this->prodTaxDetails as $rate => $data) {
            if ($rate != '0.00') {
                ++$nb_tax;
                $before = $this->GetY();
                $lineSize = $this->GetY() - $before;
                $this->SetXY($this->GetX(), $this->GetY() - $lineSize + 3);
                $this->Cell(
                    $w[0],
                    $lineSize,
                    $this->displayMessageUTF8($this->module->l('Products', 'pdfmandate15')),
                    0,
                    0,
                    'R'
                );
                $this->Cell(
                    $w[1],
                    $lineSize,
                    number_format($rate, 3, ',', ' ').' %',
                    0,
                    0,
                    'R'
                );

                $ecotax = $data['ecotax'];
                $ecotax_display = Tools::displayPrice($ecotax, self::$currency, true);
                $tax = Tools::displayPrice($data['total_tax'], self::$currency, true);

                $price_without_ecotax = Tools::displayPrice($data['total_tax_excl'], self::$currency, true);

                $this->Cell(
                    $w[2],
                    $lineSize,
                    $order_slip.self::convertSign($price_without_ecotax),
                    0,
                    0,
                    'R'
                );

                $this->Cell($w[3], $lineSize, $order_slip.self::convertSign($tax), 0, 0, 'R');

                if ($this->hasEcoTax) {
                    $this->Cell($w[4], $lineSize, $order_slip.self::convertSign($ecotax_display), 0, 0, 'R');
                }

                $this->Cell(
                    $w[$this->hasEcoTax ? 5 : 4],
                    $lineSize,
                    $order_slip.self::convertSign(Tools::displayPrice($data['total_tax_incl'], self::$currency, true)),
                    0,
                    0,
                    'R'
                );
                $this->Ln();
            }
        }

        // Display carrier tax
        if ($carrier_tax_rate
            && $carrier_tax_rate != '0.00'
            && (
                (self::$order->total_shipping != '0.00' && !self::$orderSlip)
                || (self::$orderSlip && self::$orderSlip->shipping_cost)
            )
        ) {
            ++$nb_tax;
            $before = $this->GetY();
            $lineSize = $this->GetY() - $before;

            $shipping_cost_tax_excl = self::$order->total_shipping_tax_excl;
            $total_shipping = self::$order->total_shipping;

            $shipping_amount_tax = $total_shipping - $shipping_cost_tax_excl;

            $this->SetXY($this->GetX(), $this->GetY() - $lineSize + 3);
            $this->Cell(
                $w[0],
                $lineSize,
                $this->displayMessageUTF8($this->module->l('Carrier', 'pdfmandate15')),
                0,
                0,
                'R'
            );
            $this->Cell(
                $w[1],
                $lineSize,
                number_format($carrier_tax_rate, 3, ',', ' ').' %',
                0,
                0,
                'R'
            );
            $this->Cell(
                $w[2],
                $lineSize,
                $order_slip.self::convertSign(Tools::displayPrice($shipping_cost_tax_excl, self::$currency, true)),
                0,
                0,
                'R'
            );
            $this->Cell(
                $w[3],
                $lineSize,
                $order_slip.self::convertSign(Tools::displayPrice($shipping_amount_tax, self::$currency, true)),
                0,
                0,
                'R'
            );
            if ($this->hasEcoTax) {
                $this->Cell($w[4], $lineSize, (self::$orderSlip ? '-' : '').'', 0, 0, 'R');
            }

            $this->Cell(
                $w[$this->hasEcoTax ? 5 : 4],
                $lineSize,
                $order_slip.self::convertSign(Tools::displayPrice($total_shipping, self::$currency, true)),
                0,
                0,
                'R'
            );
            $this->Ln();
        }

        // Display wrapping tax
        if (self::$order->total_wrapping && self::$order->total_wrapping != '0.00') {
            // $tax = new Tax((int) (Configuration::get('PS_GIFT_WRAPPING_TAX')));
            $tax = new Tax((int) (Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP')));

            $taxRate = $tax->rate;
            $gift_price = Configuration::get('PS_GIFT_WRAPPING_PRICE');
            $totalTaxe = ($taxRate * $gift_price) / 100;

            $wrapping_price = self::$order->total_wrapping;

            ++$nb_tax;
            $before = $this->GetY();
            $lineSize = $this->GetY() - $before;
            $this->SetXY($this->GetX(), $this->GetY() - $lineSize + 3);
            $this->Cell(
                $w[0],
                $lineSize,
                $this->displayMessageUTF8($this->module->l('Gift-wrapping', 'pdfmandate15')),
                0,
                0,
                'R'
            );
            $this->Cell(
                $w[1],
                $lineSize,
                number_format($taxRate, 3, ',', ' ').' %',
                0,
                0,
                'R'
            );
            $this->Cell(
                $w[2],
                $lineSize,
                $order_slip.self::convertSign(Tools::displayPrice($gift_price, self::$currency, true)),
                0,
                0,
                'R'
            );
            $this->Cell(
                $w[3],
                $lineSize,
                $order_slip.self::convertSign(Tools::displayPrice($totalTaxe, self::$currency, true)),
                0,
                0,
                'R'
            );

            if ($this->hasEcoTax) {
                $this->Cell($w[4], $lineSize, (self::$orderSlip ? '-' : '').'', 0, 0, 'R');
            }

            $this->Cell(
                $w[$this->hasEcoTax ? 5 : 4],
                $lineSize,
                $order_slip.self::convertSign(Tools::displayPrice($wrapping_price, self::$currency, true)),
                0,
                0,
                'R'
            );
        }

        if (!$nb_tax) {
            $this->Cell(
                190,
                10,
                $this->displayMessageUTF8($this->module->l('No tax', 'pdfmandate15')),
                0,
                0,
                'C'
            );
        }
    }

    public function _beginpage($orientation, $arg2)
    {
        parent::_beginpage($orientation, $arg2);
        if ($this->NewPageGroup) {
            // start a new group
            $n = sizeof($this->PageGroups) + 1;
            $alias = "{nb$n}";
            $this->PageGroups[$alias] = 1;
            $this->CurrPageGroup = $alias;
            $this->NewPageGroup = false;
        } elseif ($this->CurrPageGroup) {
            ++$this->PageGroups[$this->CurrPageGroup];
        }
    }

    public function _putpages()
    {
        $nb = $this->page;
        if (!empty($this->PageGroups)) {
            // do page number replacement
            foreach ($this->PageGroups as $k => $v) {
                for ($n = 1; $n <= $nb; ++$n) {
                    $this->pages[$n] = str_replace($k, $v, $this->pages[$n]);
                }
            }
        }
        parent::_putpages();
    }

    private function builMerchantFooterDetail($conf)
    {
        $footerText = null;

        // If the country is USA
        if (Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT')) == 'US') {
            $completeAddressShop = $this->_getCompleteUSAddressFormat($conf);

            $footerText = $this->displayMessageUTF8($this->module->l('Headquarters:', 'pdfmandate15'))."\n";
            $footerText .= $conf['PS_SHOP_NAME_UPPER']."\n";

            if (isset($conf['PS_SHOP_ADDR1']) && !empty($conf['PS_SHOP_ADDR1'])) {
                $footerText .= $conf['PS_SHOP_ADDR1']."\n";
            }

            if (isset($conf['PS_SHOP_ADDR2']) && !empty($conf['PS_SHOP_ADDR2'])) {
                $footerText .= $conf['PS_SHOP_ADDR2']."\n";
            }

            if (!empty($completeAddressShop)) {
                $footerText .= $completeAddressShop."\n";
            }

            if (isset($conf['PS_SHOP_COUNTRY']) && !empty($conf['PS_SHOP_COUNTRY'])) {
                $footerText .= $conf['PS_SHOP_COUNTRY']."\n";
            }

            if (isset($conf['PS_SHOP_PHONE']) && !empty($conf['PS_SHOP_PHONE'])) {
                $footerText .= $this->displayMessageUTF8($this->module->l('PHONE:', 'pdfmandate15')).' '.$conf['PS_SHOP_PHONE']."\n";
            }
        } else {
            $footerText .= $conf['PS_SHOP_NAME_UPPER'].' ';

            if (isset($conf['PS_SHOP_ADDR1']) && !empty($conf['PS_SHOP_ADDR1'])) {
                $footerText .= $this->displayMessageUTF8($this->module->l('Headquarters:', 'pdfmandate15')).' ';
                $footerText .= $conf['PS_SHOP_ADDR1'].' ';
            }

            if (isset($conf['PS_SHOP_ADDR2']) && !empty($conf['PS_SHOP_ADDR2'])) {
                $footerText .= $conf['PS_SHOP_ADDR2'].' ';
            }

            if (isset($conf['PS_SHOP_STATE']) && !empty($conf['PS_SHOP_STATE'])) {
                $footerText .= ', '.$conf['PS_SHOP_STATE'].' ';
            }

            $footerText .= $conf['PS_SHOP_COUNTRY'].' ';

            if (isset($conf['PS_SHOP_DETAILS']) && !empty($conf['PS_SHOP_DETAILS'])) {
                $footerText .= $this->displayMessageUTF8($this->module->l('Details:', 'pdfmandate15')).' ';
                $footerText .= $conf['PS_SHOP_DETAILS'].' - ';
            }

            if (isset($conf['PS_SHOP_PHONE']) && !empty($conf['PS_SHOP_PHONE'])) {
                $footerText .= $this->displayMessageUTF8($this->module->l('PHONE:', 'pdfmandate15')).' '.$conf['PS_SHOP_PHONE']."\n";
            }
        }

        return $footerText;
    }

    // current page in the group
    public function groupPageNo()
    {
        return $this->PageGroups[$this->CurrPageGroup];
    }

    private function isPngFile($file)
    {
        $pngReferenceHeader = array(137, 80, 78, 71, 13, 10, 26, 10);
        $fp = fopen($file, 'r');
        if ($fp) {
            for ($n = 0; $n < 8; ++$n) {
                if (ord(fread($fp, 1)) !== $pngReferenceHeader[$n]) {
                    fclose($fp);

                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    // alias of the current page group -- will be replaced by the total number of pages in this group
    public function pageGroupAlias()
    {
        return $this->CurrPageGroup;
    }

    /**
     * Invoice header.
     */
    public function header()
    {
        $conf = Configuration::getMultiple(
            array(
                'PS_SHOP_NAME',
                'PS_SHOP_ADDR1',
                'PS_SHOP_CODE',
                'PS_SHOP_CITY',
                'PS_SHOP_COUNTRY',
                'PS_SHOP_STATE',
            )
        );
        if (isset($conf['PS_SHOP_NAME'])) {
            $conf['PS_SHOP_NAME'] = Tools::iconv('utf-8', self::encoding(), $conf['PS_SHOP_NAME']);
        } else {
            $conf['PS_SHOP_NAME'] = 'Your company';
        }

        if (isset($conf['PS_SHOP_ADDR1'])) {
            $conf['PS_SHOP_ADDR1'] = Tools::iconv('utf-8', self::encoding(), $conf['PS_SHOP_ADDR1']);
        } else {
            $conf['PS_SHOP_ADDR1'] = 'Your company';
        }

        if (isset($conf['PS_SHOP_CODE'])) {
            $conf['PS_SHOP_CODE'] = Tools::iconv('utf-8', self::encoding(), $conf['PS_SHOP_CODE']);
        } else {
            $conf['PS_SHOP_CODE'] = 'Postcode';
        }

        if (isset($conf['PS_SHOP_CITY'])) {
            $conf['PS_SHOP_CITY'] = Tools::iconv('utf-8', self::encoding(), $conf['PS_SHOP_CITY']);
        } else {
            $conf['PS_SHOP_CITY'] = 'City';
        }

        if (isset($conf['PS_SHOP_COUNTRY'])) {
            $conf['PS_SHOP_COUNTRY'] = Tools::iconv('utf-8', self::encoding(), $conf['PS_SHOP_COUNTRY']);
        } else {
            $conf['PS_SHOP_COUNTRY'] = 'Country';
        }

        if (isset($conf['PS_SHOP_STATE'])) {
            $conf['PS_SHOP_STATE'] = Tools::iconv('utf-8', self::encoding(), $conf['PS_SHOP_STATE']);
        } else {
            $conf['PS_SHOP_STATE'] = '';
        }

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $this->Image($this->getLogo(), 10, 8, 0, 15, 'JPG');
        } else {
            if (file_exists(_PS_IMG_DIR_.'/logo_invoice.jpg')) {
                if ($this->isPngFile(_PS_IMG_DIR_.'/logo_invoice.jpg')) {
                    $this->Image(_PS_IMG_DIR_.'/logo_invoice.jpg', 10, 8, 0, 15, 'PNG');
                } else {
                    $this->Image(_PS_IMG_DIR_.'/logo_invoice.jpg', 10, 8, 0, 15);
                }
            } elseif (file_exists(_PS_IMG_DIR_.'/logo.jpg')) {
                if ($this->isPngFile(_PS_IMG_DIR_.'/logo.jpg')) {
                    $this->Image(_PS_IMG_DIR_.'/logo.jpg', 10, 8, 0, 15, 'PNG');
                } else {
                    $this->Image(_PS_IMG_DIR_.'/logo.jpg', 10, 8, 0, 15);
                }
            }
        }
        $this->SetFont(self::fontname(), 'B', 15);
        $this->Cell(65);
        // Title
        $this->SetFillColor(240, 240, 240);
        $before = $this->GetY();
        $this->MultiCell(60, 10, $this->module->l('Order slip', 'pdfmandate15'), 0, 'C', true);
        // Date
        $this->SetFont(self::fontname(), 'B', 12);
        $lineSize = $this->GetY() - $before;
        $this->SetXY($this->GetX() + 70 + 60, $this->GetY() - $lineSize);
        $this->MultiCell(60, 10, $this->displayMessageUTF8(strftime('%d/%m/%Y')), 0, 'R', false);
        // Message
        $this->Ln(1);
        $this->SetFont(self::fontname(), 'B', 9);
        $this->MultiCell(
            190,
            5,
            $this->displayMessageUTF8($this->module->l('(only for metropolitan France and DOM / TOM)', 'pdfmandate15')),
            0,
            'C',
            false
        );
    }

    /**
     * Returns the invoice logo.
     */
    protected function getLogo()
    {
        $logo = null;

        $logo_invoice = Configuration::get('PS_LOGO_INVOICE', null, null, (int) self::$order->id_shop);
        $logo = Configuration::get('PS_LOGO', null, null, (int) self::$order->id_shop);

        if ($logo_invoice != false && file_exists(_PS_IMG_DIR_.$logo_invoice)) {
            $logo = _PS_IMG_DIR_.$logo_invoice;
        } elseif ($logo != false && file_exists(_PS_IMG_DIR_.$logo)) {
            $logo = _PS_IMG_DIR_.$logo;
        } else if (file_exists(_PS_IMG_DIR_.'logo_invoice.jpg')) {
            $logo = _PS_IMG_DIR_.'logo_invoice.jpg';
        } else if (file_exists(_PS_IMG_DIR_.'logo.jpg')) {
            $logo = _PS_IMG_DIR_.'logo.jpg';
        }
        return $logo;
    }

    /**
     * Create a pdf mandate.
     *
     * @return file .png
     */
    public function mandatePDF()
    {
        $mode = 'D';
        $slip = false;
        $delivery = false;
        $reference = Tools::getValue('id_order');

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $SQL = 'SELECT `id_order` FROM `'._DB_PREFIX_."orders` WHERE `reference` = '".$reference."' ";
            $id_order = Db::getInstance()->getValue($SQL);
        } else {
            $id_order = Tools::getValue('id_order');
        }
        $order = new Order($id_order);

        if (!Validate::isLoadedObject($order)
            || (defined('_PS_ADMIN_DIR_') && !Context::getContext()->cookie->id_employee)
            || (
                isset(Context::getContext()->cookie->id_customer)
                && Context::getContext()->cookie->id_customer != $order->id_customer
            )
        ) {
            die('Invalid order or invalid order state');
        }

        self::$order = clone $order;

        self::$orderSlip = $slip;
        self::$delivery = $delivery;
        self::$iso = Tools::strtoupper(Language::getIsoById((int) (self::$order->id_lang)));
        if ((self::$priceDisplayMethod = self::$order->getTaxCalculationMethod()) === false) {
            die($this->displayMessageUTF8($this->module->l('No price display method defined for the customer group', 'pdfmandate15')));
        }

        $this->SetAutoPageBreak(true, 35);
        $this->startPageGroup();

        self::$currency = Currency::getCurrencyInstance((int) (self::$order->id_currency));

        $this->AliasNbPages();
        $this->AddPage();

        $width = 100;
        $this->SetX(10);
        $this->SetY(30);
        $this->SetFont(self::fontname(), 'B', 8);
        $this->Cell(195, 5, '> '.$this->displayMessageUTF8($this->module->l('Complete and send to:', 'pdfmandate15')), 0);
        $tmp = null;
        if ($name = Configuration::get('TOTADMINISTRATIVEMANDATE_CO_NAME')) {
            $tmp .= $name;
        } else {
            $tmp .= Configuration::get('blockcontactinfos_company');
        }
        $tmp .= ' - ';
        if ($addr = Configuration::get('TOTADMINISTRATIVEMANDATE_CO_ADDR')) {
            $tmp .= $addr;
        } else {
            $tmp .= Configuration::get('blockcontactinfos_address');
        }

        $this->Ln(5);
        $this->Cell(0, 5, $this->displayMessageUTF8($tmp), 0);
        $this->Ln(5);
        // Line Tel / Fax / Email
        $this->SetFont(self::fontname(), '', 8);
        $tmp2 = null;
        $phone = Configuration::get('TOTADMINISTRATIVEMANDATE_PHONE');
        if ($phone != false) {
            $tmp2 = $this->module->l('Tel:', 'pdfmandate15').' '.$phone;
        }
        $fax = Configuration::get('TOTADMINISTRATIVEMANDATE_FAX');
        if ($fax != false) {
            $tmp2 .= (($tmp2 != null) ? ' / ' : '').$this->module->l('Fax:', 'pdfmandate15').' '.$fax;
        }
        $mail = Configuration::get('TOTADMINISTRATIVEMANDATE_MAIL');
        if ($mail != false) {
            $tmp2 .= (($tmp2 != null) ? ' / ' : '').$this->module->l('Mail:', 'pdfmandate15').' '.$mail;
        }
        $this->Cell(0, 5, $this->displayMessageUTF8($tmp2), 0);
        // \Line Tel / Fax / Email
        $this->Ln(5);
        // First content
        $this->SetFont(self::fontname(), 'B', 7);
        $this->Cell(
            0,
            5,
            $this->displayMessageUTF8($this->module->l('Stamp of the institution + date:', 'pdfmandate15')),
            'LTR'
        );
        $this->Ln(5);
        $this->Cell(0, 15, '', 'LBR');
        $this->Ln(15);
        // \First content
        // Second content
        $this->SetFont(self::fontname(), '', 7);
        $this->Cell(0, 5, $this->displayMessageUTF8($this->module->l('Name and title of authorized person:', 'pdfmandate15')), 'LTR');
        $this->Ln(5);

        $email_text = '..........................................................................................';
        $email_text .= '@..........................................................................................';

        $this->Cell(0, 5, $this->displayMessageUTF8($this->module->l('E-mail address:', 'pdfmandate15')).$email_text, 'LR');
        $this->Ln(5);
        $this->Cell(0, 5, $this->displayMessageUTF8($this->module->l('Tel:', 'pdfmandate15')), 'LBR');
        $this->Ln(5);
        // Second content

        $addressTypes = array(
            'delivery' => array(),
            'invoice' => array(),
        );

        $patternRules = array(
            'optional' => array(
                'address2',
                'company', ),
            'avoid' => array(
                'State:iso_code', ), );

        $addressType = $this->generateHeaderAddresses(self::$order, $addressTypes, $patternRules, 95);

        if (Configuration::get('VATNUMBER_MANAGEMENT')
            && !empty($addressType['invoice']['addressObject']->vat_number)
        ) {
            $vat_delivery = '';
            if ($addressType['invoice']['addressObject']->id != $addressType['delivery']['addressObject']->id) {
                $vat_delivery = $addressType['delivery']['addressObject']->vat_number;
            }

            $this->Cell(
                $width,
                10,
                Tools::iconv('utf-8', self::encoding(), $vat_delivery),
                0,
                'L'
            );
            $this->Cell(
                $width,
                10,
                Tools::iconv('utf-8', self::encoding(), $addressType['invoice']['addressObject']->vat_number),
                0,
                'L'
            );
            $this->Ln(5);
        }

        if ($addressType['invoice']['addressObject']->dni != null) {
            $dni = Tools::iconv('utf-8', self::encoding(), $addressType['invoice']['addressObject']->dni);
            $this->Cell(
                $width,
                10,
                $this->displayMessageUTF8($this->module->l('Tax ID number:', 'pdfmandate15')).' '.$dni,
                0,
                'L'
            );
        }

        $this->Ln(5);
        // Reference
        $this->SetFont(self::fontname(), 'B', 10);

        $reference = (version_compare(_PS_VERSION_, '1.5', '>') ? self::$order->reference : self::$order->id);
        $this->Cell(
            $width,
            10,
            $this->displayMessageUTF8($this->module->l('Order reference:', 'pdfmandate15')).' '.$reference
        );
        $this->Ln(10);

        $this->prodTab('');

        /* Exit if delivery */
        if (!self::$delivery) {
            if (!self::$orderSlip) {
                $this->discTab();
            }

            if (self::$orderSlip) {
                $order_slip_prefix = array('-', '');
            } else {
                $order_slip_prefix = array('', '-');
            }

            // $shipping_cost_tax_excl = Tools::ps_round((float) self::$order->total_shipping_tax_excl, 2);
            // $gift_cost_tax_excl = Tools::ps_round((float) self::$order->total_wrapping_tax_excl, 2);

            $total_discounts = self::$order->total_discounts_tax_excl;

            // $tax = self::$order->getTaxesAverageUsed();

            // $shipping_gift_tax_excl = $shipping_cost_tax_excl + $gift_cost_tax_excl;

            $tax_without_tax = Tools::displayPrice(self::$order->total_products, self::$currency, true);
            $tax_tax = Tools::displayPrice(self::$order->total_products_wt, self::$currency, true);

            /*
             * Display price summation
             */
            if (Configuration::get('PS_TAX') || $order->total_products_wt != $order->total_products) {
                $this->Ln(5);
                $this->SetFont(self::fontname(), 'B', 8);
                $width = 165;

                $tax_excl = $this->displayMessageUTF8($this->module->l('Total products (tax excl.)', 'pdfmandate15'));
                $tax_incl = $this->displayMessageUTF8($this->module->l('Total products (tax incl.)', 'pdfmandate15'));

                // Price tax excl.
                $this->Cell($width, 0, $tax_excl.' : ', 0, 0, 'R');
                $this->Cell(0, 0, $order_slip_prefix[0].self::convertSign($tax_without_tax), 0, 0, 'R');
                $this->Ln(4);
                // Price tax incl.
                $this->Cell($width, 0, $tax_incl.' : ', 0, 0, 'R');
                $this->Cell(0, 0, $order_slip_prefix[0].self::convertSign($tax_tax), 0, 0, 'R');
                $this->Ln(4);
            } else {
                $this->Ln(5);
                $this->SetFont(self::fontname(), 'B', 8);
                $width = 165;
                $this->Cell(
                    $width,
                    0,
                    $this->displayMessageUTF8($this->module->l('Total products ', 'pdfmandate15')).' : ',
                    0,
                    0,
                    'R'
                );
                $this->Cell(
                    0,
                    0,
                    $order_slip_prefix[0].self::convertSign($tax_without_tax),
                    0,
                    0,
                    'R'
                );
                $this->Ln(4);
            }

            if (!self::$orderSlip && $total_discounts != '0.00') {
                $this->Cell(
                    $width,
                    0,
                    $this->displayMessageUTF8($this->module->l('Total discounts (tax incl.)', 'pdfmandate15')).' : ',
                    0,
                    0,
                    'R'
                );
                $total_discounts_display = Tools::displayPrice($total_discounts, self::$currency, true);
                $this->Cell(
                    0,
                    0,
                    $order_slip_prefix[1].self::convertSign($total_discounts_display),
                    0,
                    0,
                    'R'
                );
                $this->Ln(4);
            }

            if (isset(self::$order->total_wrapping) && ((float) (self::$order->total_wrapping) > 0)) {
                $this->Cell(
                    $width,
                    0,
                    $this->displayMessageUTF8($this->module->l('Total gift-wrapping', 'pdfmandate15')).' : ',
                    0,
                    0,
                    'R'
                );

                if (self::$priceDisplayMethod == PS_TAX_EXC) {
                    $amount = self::$order->total_wrapping_tax_excl;
                    $this->Cell(
                        0,
                        0,
                        $order_slip_prefix[0].self::convertSign(Tools::displayPrice($amount, self::$currency, true)),
                        0,
                        0,
                        'R'
                    );
                } else {
                    $amount = self::$order->total_wrapping;
                    $this->Cell(
                        0,
                        0,
                        $order_slip_prefix[0].self::convertSign(Tools::displayPrice($amount, self::$currency, true)),
                        0,
                        0,
                        'R'
                    );
                }

                $this->Ln(4);
            }

            if (self::$order->total_shipping != '0.00' &&
                (
                    !self::$orderSlip
                    || (self::$orderSlip && self::$orderSlip->shipping_cost)
                )
            ) {
                if (self::$priceDisplayMethod == PS_TAX_EXC) {
                    $this->Cell(
                        $width,
                        0,
                        $this->displayMessageUTF8($this->module->l('Total shipping (tax excl.)', 'pdfmandate15')).' : ',
                        0,
                        0,
                        'R'
                    );
                    $amount = Tools::ps_round(self::$order->total_shipping_tax_excl, 2);
                    $this->Cell(
                        0,
                        0,
                        $order_slip_prefix[0].self::convertSign(Tools::displayPrice($amount, self::$currency, true)),
                        0,
                        0,
                        'R'
                    );
                } else {
                    $this->Cell(
                        $width,
                        0,
                        $this->displayMessageUTF8($this->module->l('Total shipping (tax incl.)', 'pdfmandate15')).' : ',
                        0,
                        0,
                        'R'
                    );
                    $amount = Tools::displayPrice(self::$order->total_shipping, self::$currency, true);
                    $this->Cell(
                        0,
                        0,
                        $order_slip_prefix[0].self::convertSign($amount),
                        0,
                        0,
                        'R'
                    );
                }
                $this->Ln(4);
            }

            $tax_price_with_tax = self::$order->total_paid_tax_incl;
            $tax_price_without_tax = self::$order->total_paid_tax_excl;

            if (Configuration::get('PS_TAX') or $order->total_products_wt != $order->total_products) {
                $tax_incl = $this->displayMessageUTF8($this->module->l(' (tax incl.)', 'pdfmandate15'));
                $tax_excl = $this->displayMessageUTF8($this->module->l(' (tax excl.)', 'pdfmandate15'));
                $tax_display = self::$priceDisplayMethod == PS_TAX_EXC ? $tax_incl : $tax_excl;

                $tax_price = self::$priceDisplayMethod == PS_TAX_EXC ? $tax_price_with_tax : $tax_price_without_tax;

                $no_tax_price = self::$priceDisplayMethod == PS_TAX_EXC ? $tax_price_without_tax : $tax_price_with_tax;

                $this->Cell(
                    $width,
                    0,
                    $this->displayMessageUTF8($this->module->l('Total', 'pdfmandate15')).' '.$tax_display.' : ',
                    0,
                    0,
                    'R'
                );

                $this->Cell(
                    0,
                    0,
                    $order_slip_prefix[0].self::convertSign(Tools::displayPrice($tax_price, self::$currency, true)),
                    0,
                    0,
                    'R'
                );
                $this->Ln(4);
                $this->Cell(
                    $width,
                    0,
                    $this->displayMessageUTF8($this->module->l('Total', 'pdfmandate15')).' '.$tax_incl.' : ',
                    0,
                    0,
                    'R'
                );
                $this->Cell(
                    0,
                    0,
                    $order_slip_prefix[0].self::convertSign(Tools::displayPrice($no_tax_price, self::$currency, true)),
                    0,
                    0,
                    'R'
                );
                $this->Ln(4);
            } else {
                $this->Cell(
                    $width,
                    0,
                    $this->displayMessageUTF8($this->module->l('Total', 'pdfmandate15')).' : ',
                    0,
                    0,
                    'R'
                );

                $tax_price_without_tax_display = Tools::displayPrice($tax_price_without_tax, self::$currency, true);

                $this->Cell(
                    0,
                    0,
                    $order_slip_prefix[0].self::convertSign($tax_price_without_tax_display),
                    0,
                    0,
                    'R'
                );
                $this->Ln(4);
            }

            $this->taxTab();
        }

        $this->Ln(8);

        $this->SetFont(self::fontname(), '', 9);
        $text = $this->displayMessageUTF8(Configuration::get('TOTADMINISTRATIVEMANDATE_OWNER'));
        $text .= "\n\r".$this->displayMessageUTF8(Configuration::get('TOTADMINISTRATIVEMANDATE_DETAILS'));
        $text .= "\n\r".$this->displayMessageUTF8(Configuration::get('TOTADMINISTRATIVEMANDATE_ADDRESS'));

        $this->Ln(12);
        if ($this->nbLines(0, $text) > 7) {
            $this->AddPage();
            $this->Ln();
        }
        $this->Cell(0, 6, $this->displayMessageUTF8($this->module->l('RIB', 'pdfmandate15')), 1, 2, 'C', 1);

        $this->MultiCell(0, 6, html_entity_decode($text), 1);
        $this->Ln(5);
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            Hook::exec('PDFInvoice', array('pdf' => $this, 'id_order' => $id_order));
        } else {
            Hook::PDFInvoice($this, self::$order->id);
        }
        if (ob_get_contents()) {
            ob_clean();
            ob_end_clean();
        }
        return $this->Output(Tools::getValue('id_order').'.pdf', $mode);
    }

    private function nbLines($w, $txt)
    {
        //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }

        $wmax = ($w - 2 * $this->rMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = Tools::strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            --$nb;
        }

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                ++$i;
                $sep = -1;
                $j = $i;
                $l = 0;
                ++$nl;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }

            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        ++$i;
                    }
                } else {
                    $i = $sep + 1;
                }

                $sep = -1;
                $j = $i;
                $l = 0;
                ++$nl;
            } else {
                ++$i;
            }
        }

        return $nl;
    }

    /**
     * Invoice footer.
     */
    public function footer()
    {
        $arrayConf = array(
            'PS_SHOP_NAME',
            'PS_SHOP_ADDR1',
            'PS_SHOP_ADDR2',
            'PS_SHOP_CODE',
            'PS_SHOP_CITY',
            'PS_SHOP_COUNTRY',
            'PS_SHOP_COUNTRY_ID',
            'PS_SHOP_DETAILS',
            'PS_SHOP_PHONE',
            'PS_SHOP_STATE', );

        $conf = Configuration::getMultiple($arrayConf);
        $conf['PS_SHOP_NAME_UPPER'] = Tools::strtoupper($conf['PS_SHOP_NAME']);
        $y_delta = array_key_exists('PS_SHOP_DETAILS', $conf) ? substr_count($conf['PS_SHOP_DETAILS'], "\n") : 0;

        foreach ($conf as $key => $value) {
            $conf[$key] = Tools::iconv('utf-8', self::encoding(), $value);
        }

        foreach ($arrayConf as $key) {
            if (!isset($conf[$key])) {
                $conf[$key] = '';
            }
        }

        $merchantDetailFooter = $this->builMerchantFooterDetail($conf);
        $totalLineDetailFooter = count(explode("\n", $merchantDetailFooter));

        // A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm).
        // This is a very common unit in typography; font sizes are expressed in that unit.
        // 8 point = 2.8mm and the cell height = 4mm
        $this->SetY(-(21.0 + (4 * $totalLineDetailFooter)) - ($y_delta * 7.0));
        $this->SetFont(self::fontname(), '', 7);
        $this->Cell(
            190,
            5,
            ' '."\n".Tools::iconv('utf-8', self::encoding(), 'P. ').$this->groupPageNo().' / '.$this->pageGroupAlias(),
            'T',
            1,
            'R'
        );

        if ($msg = Configuration::get('PS_INVOICE_FREE_TEXT', Context::getContext()->cookie->id_lang)) {
            $this->Cell(
                0,
                10,
                $this->displayMessageUTF8($msg),
                0,
                0,
                'C',
                0
            );
            $this->Ln(4);
        }

        $this->Ln(4);
        $this->Ln(9);

        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont(self::fontname(), '', 8);

        $this->MultiCell(0.0, 4.0, $merchantDetailFooter, 0, 'C', 1);
    }

    private function displayMessageUTF8($msg)
    {
        return html_entity_decode(
            utf8_decode($msg)
        );
    }
}
