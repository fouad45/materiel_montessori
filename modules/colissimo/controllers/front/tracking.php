<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ColissimoTrackingModuleFrontController
 *
 * Ajax processes:
 *  - showTracking
 *
 */
class ColissimoTrackingModuleFrontController extends ModuleFrontController
{
    /** @var Colissimo $module */
    public $module;

    /** @var Order $order */
    public $order;

    /** @var ColissimoOrder $colissimoOrder */
    public $colissimoOrder;

    /** @var array $languages */
    public $languages = array(
        'fr' => 'fr_FR',
        'de' => 'de_DE',
        'en' => 'en_GB',
        'es' => 'es-ES',
        'it' => 'it_IT',
        'nl' => 'nl_NL',
    );

    /** @var array $days */
    public $days = array(
        1 => array('fr' => 'lundi', 'en' => 'monday,', 'de' => 'Montag,', 'nl' => 'maandag', 'it' => 'lunedi', 'es' => 'lunes,'),
        2 => array('fr' => 'mardi', 'en' => 'tuesday,', 'de' => 'Dienstag,', 'nl' => 'dinsdag', 'it' => 'martedì', 'es' => 'martes,'),
        3 => array('fr' => 'mercredi', 'en' => 'wednesday,', 'de' => 'Mittwoch,', 'nl' => 'woensdag', 'it' => 'mercoledì', 'es' => 'miércoles,'),
        4 => array('fr' => 'jeudi', 'en' => 'thursday,', 'de' => 'Donnerstag,', 'nl' => 'donderdag', 'it' => 'giovedi', 'es' => 'jueves,'),
        5 => array('fr' => 'vendredi', 'en' => 'friday,', 'de' => 'Freitag,', 'nl' => 'vrijdag', 'it' => 'venerdì', 'es' => 'viernes,'),
        6 => array('fr' => 'samedi', 'en' => 'saturday,', 'de' => 'Samstag,', 'nl' => 'zaterdag', 'it' => 'sabato', 'es' => 'sábado,'),
        7 => array('fr' => 'dimanche', 'en' => 'sunday,', 'de' => 'Sonntag,', 'nl' => 'zondag', 'it' => 'domenica', 'es' => 'domingo,'),
    );

    /** @var array $months */
    public $months = array(
        1 => array('fr' => 'janvier', 'en' => 'january', 'de' => 'januar', 'nl' => 'januari', 'it' => 'gennaio', 'es' => 'de enero'),
        2 => array('fr' => 'février', 'en' => 'february', 'de' => 'februar', 'nl' => 'februari', 'it' => 'febbraio', 'es' => 'de febrero'),
        3 => array('fr' => 'mars', 'en' => 'march', 'de' => 'märz', 'nl' => 'maart', 'it' => 'marzo', 'es' => 'de marzo'),
        4 => array('fr' => 'avril', 'en' => 'april', 'de' => 'april', 'nl' => 'april', 'it' => 'aprile', 'es' => 'de abril'),
        5 => array('fr' => 'mai', 'en' => 'may', 'de' => 'mai', 'nl' => 'mei', 'it' => 'maggio', 'es' => 'de mayo'),
        6 => array('fr' => 'juin', 'en' => 'june', 'de' => 'juni', 'nl' => 'juni', 'it' => 'giugno', 'es' => 'de junio'),
        7 => array('fr' => 'juillet', 'en' => 'july', 'de' => 'juli', 'nl' => 'juli', 'it' => 'luglio', 'es' => 'de julio'),
        8 => array('fr' => 'août', 'en' => 'august', 'de' => 'august', 'nl' => 'augustus', 'it' => 'agosto', 'es' => 'de agosto'),
        9 => array('fr' => 'septembre', 'en' => 'september', 'de' => 'september', 'nl' => 'september', 'it' => 'settembre', 'es' => 'de septiembre'),
        10 => array('fr' => 'octobre', 'en' => 'october', 'de' => 'oktober', 'nl' => 'oktober', 'it' => 'ottobre', 'es' => 'de octubre'),
        11 => array('fr' => 'novembre', 'en' => 'november', 'de' => 'november', 'nl' => 'november', 'it' => 'novembre', 'es' => 'de noviembre'),
        12 => array('fr' => 'décembre', 'en' => 'december', 'de' => 'dezember', 'nl' => 'december', 'it' => 'dicembre', 'es' => 'de diciembre'),
    );

    /**
     * @return array
     */
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['meta']['robots'] = 'noindex';
        $page['meta']['title'] = $this->module->l('Colissimo shipment tracking #').$this->order->reference;

        return $page;
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function checkAccess()
    {
        $reference = Tools::getValue('order_reference');
        $hash = Tools::getValue('hash');
        /** @var PrestaShopCollection $orders */
        $orders = Order::getByReference($reference);
        if (!$orders->count()) {
            $this->redirect_after = $this->context->link->getPageLink('my-account');
            $this->redirect();
        }
        /** @var Order $order */
        $order = $orders->getFirst();
        if (md5($reference.$order->secure_key) !== $hash) {
            $this->redirect_after = $this->context->link->getPageLink('my-account');
            $this->redirect();
        }
        $idColissimoOrder = ColissimoOrder::exists($order->id);
        if (!$idColissimoOrder) {
            $this->redirect_after = $this->context->link->getPageLink('my-account');
            $this->redirect();
        }
        $this->order = $order;
        $this->colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);

        return parent::checkAccess();
    }

    /**
     * @return bool|void
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->module->registerJs(
            'colissimo-module-front-tracking',
            'front.tracking.js',
            array('position' => 'bottom', 'priority' => 150)
        );
        $this->module->registerCSS('module-colissimo-sprites-flag', 'flag.sprites.css');
        $this->module->registerCSS('module-colissimo-front', 'colissimo.front.css');
    }

    /**
     * @param string $template
     * @param array  $params
     * @param null   $locale
     * @throws PrestaShopException
     */
    public function setTemplate($template, $params = array(), $locale = null)
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            parent::setTemplate($template, $params, $locale);
        } else {
            parent::setTemplate('module:colissimo/views/templates/front/'.$template, $params, $locale);
        }
    }

    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();
        $hash = md5($this->order->reference.$this->order->secure_key);
        $labels = $this->colissimoOrder->getShipments($this->context->language->id);
        $this->context->smarty->assign(
            array(
                'colissimo_img_path' => $this->module->getPathUri().'views/img/',
                'order_reference'    => $this->order->reference,
                'order_hash'         => $hash,
                'no_labels'          => (!$labels || empty($labels)) ? 1 : 0,
            )
        );
        $this->setTemplate($this->module->psFolder.'/tracking.tpl');
    }

    /**
     * @param array $events
     * @return array
     * @throws PrestaShopException
     */
//    public function getEvents($events)
//    {
//        $return = array();
//        foreach ($events as $event) {
//            $return[] = array_merge(
//                array('dateDisplay' => Tools::displayDate(date('Y-m-d H:i:s', $event['date'] / 1000))),
//                $event
//            );
//        }
//
//        return $return;
//    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function displayAjaxShowTracking()
    {
        $language = new Language((int) $this->context->language->id);
        $iso = $language->iso_code;
        $locale = isset($this->languages[$iso]) ? $this->languages[$iso] : $this->languages['en'];
        $html = array();
        $labels = $this->colissimoOrder->getShipments($language->id);
        $order = new Order((int) $this->colissimoOrder->id_order);
        $this->module->logger->setChannel('Timeline');
        $this->module->logger->info(
            'Start tracking for order '.$this->order->id.' ('.$this->order->reference.')'
        );
        foreach ($labels as $label) {
            $trackingRequest = new ColissimoTrackingTimelineRequest(ColissimoTools::getCredentials($order->id_shop));
            $trackingRequest->setParcelNumber($label['shipping_number'])
                            ->setLang(str_replace('-', '_', $locale))
                            ->setIp($_SERVER['REMOTE_ADDR'])
                            ->buildRequest();
            $this->module->logger->info(
                'Request',
                array('json' => json_decode($trackingRequest->getRequest(true), true))
            );
            $client = new ColissimoClient();
            $client->setRequest($trackingRequest);
            try {
                /** @var ColissimoTrackingTimelineResponse $trackingResponse */
                $trackingResponse = $client->request();
            } catch (Exception $e) {
                $this->module->logger->error('Exception thrown: '.$e->getMessage());
                continue;
            }
            if ($trackingResponse->status[0]['code']) {
                $this->module->logger->error('Error found', $trackingResponse->status[0]['message']);
                continue;
            }
            $this->module->logger->info(
                'Response reveived for label '.$label['shipping_number'],
                array('response' => $trackingResponse)
            );
            if ($trackingResponse->parcelDetails['statusDelivery']) {
                $this->module->updateTrackingByTypology(
                    ColissimoTrackingCode::TYPO_DELIVERED,
                    $this->order,
                    $label['id_label']
                );
            } elseif (is_array($trackingResponse->events) && count($trackingResponse->events) > 1) {
                $this->module->updateTrackingByTypology(
                    ColissimoTrackingCode::TYPO_SHIPPED,
                    $this->order,
                    $label['id_label']
                );
            }

            foreach ($trackingResponse->events as &$event) {
                if ($event['date'] !== null) {
                    try {
                        $dateTime = new DateTime($event['date']);
                    } catch (Exception $e) {
                        $event['dateDisplay'] = '';
                        $event['dateDisplayShort'] = '';
                        continue;
                    }
                    $day = $this->days[$dateTime->format('N')];
                    $day = isset($day[$iso]) ? $day[$iso] : $day['en'];
                    $month = $this->months[$dateTime->format('n')];
                    $month = isset($month[$iso]) ? $month[$iso] : $month['en'];
                    $event['dateDisplay'] = $day.' '.$dateTime->format('j').' '.$month;
                    $event['dateDisplayShort'] = Tools::displayDate($dateTime->format('Y-m-d H:i:s'));
                } else {
                    $event['dateDisplay'] = '';
                    $event['dateDisplayShort'] = '';
                }
            }

            foreach ($trackingResponse->timeline as $key => &$step) {
                if ($step['date'] !== null) {
                    try {
                        $dateTime = new DateTime($step['date']);
                    } catch (Exception $e) {
                        $step['dateDisplay'] = '';
                        $step['dateDisplayShort'] = '';
                        continue;
                    }
                    $day = $this->days[$dateTime->format('N')];
                    $day = isset($day[$iso]) ? $day[$iso] : $day['en'];
                    $month = $this->months[$dateTime->format('n')];
                    $month = isset($month[$iso]) ? $month[$iso] : $month['en'];
                    $step['dateDisplay'] = $day.' '.$dateTime->format('j').' '.$month;
                    $step['dateDisplayShort'] = Tools::displayDate($dateTime->format('Y-m-d H:i:s'));
                } else {
                    $step['dateDisplay'] = '';
                    $step['dateDisplayShort'] = '';
                }
                if ($step['countryCodeISO']) {
                    $idCountry = Country::getByIso($step['countryCodeISO']);
                    $idLang = $this->context->language->id;
                    $step['countryName'] = $idCountry ? Country::getNameById($idLang, $idCountry) : '';
                } else {
                    $step['countryName'] = '';
                }
                if ($step['status'] == 'STEP_STATUS_INACTIVE') {
                    $step['statusClass'] = 'inactive';
                } elseif ($step['status'] == 'STEP_STATUS_ACTIVE') {
                    if (!isset($trackingResponse->timeline[$key + 1])) {
                        $trackingResponse->parcelDetails['currentStep'] = $step;
                        $step['statusClass'] = 'active current';
                    } else {
                        if ($trackingResponse->timeline[$key + 1]['status'] == 'STEP_STATUS_ACTIVE') {
                            $step['statusClass'] = 'active';
                        } else {
                            $step['statusClass'] = 'active current';
                            $trackingResponse->parcelDetails['currentStep'] = $step;
                        }
                    }
                }
            }

            $shipment = array(
                'messages'       => $trackingResponse->messages,
                'user_messages'  => $trackingResponse->userMessages,
                'steps_timeline' => $trackingResponse->timeline,
                'steps_details'  => $trackingResponse->events,
                'parcel_details' => $trackingResponse->parcelDetails,
            );
            $this->context->smarty->assign(array('shipment' => $shipment));
            $tpl = $this->module->getTemplatePath($this->module->psFolder.'/_partials/colissimo-shipments.tpl');
            $html[] = $this->context->smarty->fetch($tpl);
        }
        $this->ajaxDie(json_encode(array('error' => count($html) ? 0 : 1, 'html_result' => implode('<hr>', $html))));
    }
}
