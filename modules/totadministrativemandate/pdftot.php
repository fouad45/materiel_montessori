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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!class_exists('Tools')) {
    include_once dirname(__FILE__).'/../../config/config.inc.php';
}

$cookie = Context::getContext()->cookie;
if ($cookie === null) {
    if (defined(_PS_ADMIN_DIR_) === false) {
        $cookie = new Cookie('ps');
    } else {
        $cookie = new Cookie('psAdmin');
    }
}
if (Tools::getValue('id_order')) {
    /* Header can't be included, so cookie must be created here */
    if (!isset($cookie->id_employee) && !isset($cookie->id_customer)) {
        $link = new Link();
        Tools::redirect($link->getPageLink('authentication.php'), null);
    }

    if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
        if (!class_exists('TCPDF')) {
            include_once _PS_TCPDF_PATH_.'tcpdf.php';
        }

        include_once 'pdfmandate16.php';
    } else {
        if (!class_exists('FPDF')) {
            include_once _PS_FPDF_PATH_.'fpdf.php';
        }

        include_once 'pdfmandate15.php';
    }

    $pdf = new PDFMandate();
    $pdf->mandatePDF();

    die();
} else {
    class PDFTot extends AdminTab
    {
        /**
         * Link to admin.
         *
         * @var string
         */
        private $url;

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->view              = 1;
            $this->url               = '?tab=PDFTot&token='.Tools::getValue('token');
            $this->colorOnBackground = true;
            parent::__construct();
        }
    }
}
