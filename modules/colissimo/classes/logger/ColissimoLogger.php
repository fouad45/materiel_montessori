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
 * Class ColissimoLogger
 */
class ColissimoLogger
{
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';

    /** @var AbstractColissimoHandler $handler */
    protected $handler;

    /** @var string $channel */
    protected $channel;

    /** @var string $version */
    protected $version;

    /**
     * DhlLogger constructor.
     * @param AbstractColissimoHandler $handler
     * @param string                   $version
     */
    public function __construct($handler, $version)
    {
        $this->handler = $handler;
        $this->channel = 'Unknown';
        $this->version = str_replace('.', '_', $version);
    }

    /**
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $this->channel = 'Colissimo_'.$this->version.'_'.$channel;
    }

    /**
     * @param string $message
     * @param array  $details
     */
    public function info($message, $details = array())
    {
        $this->handler->log(self::LEVEL_INFO, $message, $this->channel, $details);
    }

    /**
     * @param string $message
     * @param string $xmlString
     */
    public function infoXml($message, $xmlString)
    {
        $this->handler->logXml(self::LEVEL_INFO, $message, $this->channel, $xmlString);
    }

    /**
     * @param string $message
     * @param array  $details
     */
    public function warning($message, $details = array())
    {
        $this->handler->log(self::LEVEL_WARNING, $message, $this->channel, $details);
    }

    /**
     * @param string $message
     * @param array  $details
     */
    public function error($message, $details = array())
    {
        $this->handler->log(self::LEVEL_ERROR, $message, $this->channel, $details);
    }
}
