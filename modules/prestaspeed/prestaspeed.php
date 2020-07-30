<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * No redistribute in other sites, or copy.
 *
 * @author    RSI
 * @copyright 2007-2016 RSI
 * @license   http://localhost
 */

/*ini_set(
    'memory_limit',
    '256M'
);*/
if (!defined('_PS_VERSION_')) {
    exit;
}

//$path = _PS_MODULE_DIR_.'prestaspeed';
//include('minifier.php');
//require_once $path . '/minify/src/CSS.php';
//require_once $path . '/minify/src/JS.php';
//require_once $path . '/minify/src/Exception.php';
//require_once $path . '/path-converter/src/Converter.php';
define('SMUSHIT_USER_AGENT', 'PRESTASHOP reSMUSHIT');
define('SMUSHIT_WINDOW', 5);
define('SMUSHIT_URL', 'http://api.resmush.it/ws.php');
//use MatthiasMullie\Minify;

if (ini_get('safe_mode')) {
    /* safe mode */
    /*is on */
} else {
    @set_time_limit(0);
}

//require __DIR__.DS.'includes'.DS.'phpfastcache.php';

class PrestaSpeed extends Module
{
    private $output = '';
    private $_images;
    private $savelog;
    private $fast_cache;
    private $imageTypes = array();
    private $_postErrors = array();
    private $prestacache_config;
    protected $smushit_url;
    public function __construct()
    {
        include_once('smush2.php');
        $this->module_key = '9ed5d291ca4bbc39a25aba178a90363a';
        $this->name = 'prestaspeed';

        if (_PS_VERSION_ < '1.4.0.0') {
            $this->tab = 'Tools';
        }
        if (_PS_VERSION_ > '1.4.0.0') {
            $this->tab = 'administration';
            $this->author = 'RSI';
            $this->need_instance = 0;
        }        if (_PS_VERSION_ > '1.6.0.0') {
            $this->bootstrap = true;
        }

        $this->version = '4.3.1';
        parent::__construct();
        foreach (ImageType::getImagesTypes('products') as $type) {
            $this->imageTypes[] = $type;
        }
        $this->displayName = $this->l('Presta Speed');
        $this->description = $this->l('Optimize database speed and Prestashop performance - RSI');
        //$this->prestacache_config = $this->getPSCacheConfig();
        //$this->fast_cache = $this->getFastCache();
        if (_PS_VERSION_ < '1.5') {
            require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
        }
    }
    public function __autoload($className)
    {
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = Tools::substr($className, 0, $lastNsPos);
            $className = Tools::substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    // $fileName .= $className . '.php'; //sometimes you need a custom structure
    //require_once "library/class.php"; //or include a class manually
        require $fileName;
    }
    public function install()
    {
        $ch = curl_init();
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
        curl_setopt($ch, CURLOPT_URL, 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__);
        $s = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__;
        $o = json_decode(Tools::file_get_contents('https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url='.$s.'&strategy=desktop&key=AIzaSyD-3OKXz9-SFlzGqXYhPDRBOh56bQrqNBM&screenshot=true'));
        Configuration::updateValue('PRESTASPEED_LOADINI', isset($o->ruleGroups->SPEED->score) ? $o->ruleGroups->SPEED->score : '');
        if (!Configuration::updateValue(
            'PRESTASPEED_CO',
            1
        ) || !parent::install() || !$this->registerHook('header') || !$this->registerHook('home') || !$this->registerHook(
            'watermark'
        ) || !$this->registerHook('footer')
        ) {
            return false;
        }
        if (_PS_VERSION_ > '1.7.0.0') {
            if (!$this->registerHook(
                'displayBeforeBodyClosingTag'
            )
            ) {
                return false;
            }
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_GU',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_PA',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_OVALID',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_YOUTUBE',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_DI',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_PNF',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CA',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CUSI',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CUSI2',
            'img/'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_FUNC',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CAV',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_DA',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CAC',
            Configuration::get('PS_SMARTY_CACHE')
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_TE',
            Configuration::get('PS_CSS_THEME_CACHE')
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_GZ',
            Configuration::get('PS_HTACCESS_CACHE_CONTROL')
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_JA',
            Configuration::get('PS_JS_THEME_CACHE')
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_FR',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_TO',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_FR2',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_TO2',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_ORD',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_HTA',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_COMPRESS',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_BATCHT',
            '30'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_SPEED',
            '1'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_BO',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CSSM',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_SMUSH',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_PRELOAD',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_SMUSH2',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CLEANI',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_TOTFIL',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CUSMESDATE',
            '2015-01-01 00:00:00'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_TOTSMUSH',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_TOTCOMP',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_TOTCOMPF',
            '0'
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_DBS',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_IDB',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_TYPE',
            ''
        )
        ) {
            return false;
        }
        if (_PS_VERSION_ < "1.7.0.0") {
            if (!Configuration::updateValue(
                'PRESTASPEED_ITEM_SELECTOR',
                '.product_list > li'
            )
            ) {
                return false;
            }
            if (!Configuration::updateValue(
                'PRESTASPEED_CONTENT_SELECTOR',
                '.product_list'
            )
            ) {
                return false;
            }
            if (!Configuration::updateValue(
                'PRESTASPEED_NAV_SELECTOR',
                '.content_sortPagiBar'
            )
            ) {
                return false;
            }
            if (!Configuration::updateValue(
                'PRESTASPEED_NEXT_SELECTOR',
                '.pagination_next > a'
            )
            ) {
                return false;
            }
        } else {
            if (!Configuration::updateValue(
                'PRESTASPEED_ITEM_SELECTOR',
                '#products .product-miniature'
            )
            ) {
                return false;
            }
            if (!Configuration::updateValue(
                'PRESTASPEED_CONTENT_SELECTOR',
                '#products .products'
            )
            ) {
                return false;
            }
            if (!Configuration::updateValue(
                'PRESTASPEED_NAV_SELECTOR',
                '.pagination .page-list'
            )
            ) {
                return false;
            }
            if (!Configuration::updateValue(
                'PRESTASPEED_NEXT_SELECTOR',
                '.pagination .next'
            )
            ) {
                return false;
            }
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_INFI',
            0
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_PRELOADER',
            0
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_OPTIMIZE_TIMER',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_FACEBOOK',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_ACCOUNT_ID',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_OPTIMIZE_ID',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_USERID_ENABLED',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_CONFIGURATION_OK',
            ''
        )
        ) {
            return false;
        }
        if (!Configuration::updateValue(
            'PRESTASPEED_BEFO',
            '2008-01-01'
        )
        ) {
            return false;
        }
        if (_PS_VERSION_ > '1.5.0.0' || _PS_VERSION_ < '1.6.0.0') {
            $this->registerHook('displayAdminHomeQuickLinks') == false;
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            $this->registerHook('displayBackOfficeHeader') == false;
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            $this->registerHook('displayBackOfficeFooter') == false;
        }
        if (_PS_VERSION_ < '1.5.0.0') {
            $this->registerHook('backOfficeHome') == false;
        }
        if (_PS_VERSION_ > '1.6.0.0') {
            $this->registerHook('dashboardZoneOne') == false;
            $this->registerHook('actionAdminControllerSetMedia') == false;
        }
        $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NOT NULL, `smushed` TINYINT(1) NOT NULL,`saved` float(10) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
        Db::getInstance()
          ->Execute($query);
        $sql = 'SELECT table_schema \''._DB_NAME_.'\', SUM( data_length + index_length) / 1024 / 1024 \'db_size_in_mb\' FROM information_schema.TABLES WHERE table_schema=\''._DB_NAME_.'\' GROUP BY table_schema ;';
        Db::getInstance()
          ->Execute($sql);
        $data = $sql;
        if (is_array($data)) {
            Configuration::updateValue(
                'PRESTASPEED_IDB',
                @$data[0]['db_size_in_mb']
            );
        } else {
            Configuration::updateValue(
                'PRESTASPEED_IDB',
                @$data['db_size_in_mb']
            );
        }
        $this->sendmail();
        return true;
    }

    public function clearTmpDir()
    {
        $i = 0;
        foreach (scandir(_PS_TMP_IMG_DIR_) as $d) {
            if (preg_match(
                '/(.*)\.jpg$/',
                $d
            )) {
                $i++;
                unlink(_PS_TMP_IMG_DIR_.$d);
            }
        }
        Configuration::updateValue(
            'PRESTASPEED_TMPI',
            $i
        );
    }

    public function hookHeader()
    {
        $speed = Configuration::get('PRESTASPEED_SPEED');
        $instaclick = '';
        if (Configuration::get('PS_CSS_THEME_CACHE') == 1 && Configuration::get('PS_JS_THEME_CACHE') == 1 && _PS_VERSION_ > "1.7.0.0" && Configuration::get('PRESTASPEED_PRELOAD') == 1) {
            $this->context->controller->addJS(($this->_path).'views/js/instantclick.min.js');
            $instaclick = 1;
        } else {
            $instaclick = 0;
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], "Google")) {
        } else {
            $this->page_name = Dispatcher::getInstance()->getController();
            if ($this->page_name == 'index') {
                $this->_assignMedia();
            }
        }
        if (Configuration::get('PRESTASPEED_INFI') != null && Configuration::get(
            'PRESTASPEED_INFI'
        ) != 0) {
            if (_PS_VERSION_ > '1.4.0.0' && _PS_VERSION_ < '1.5.0.0') {
                Tools::addJS(__PS_BASE_URI__.'modules/prestaspeed/views/js/jquery.infinitescroll.min.js');
                Tools::addCSS(__PS_BASE_URI__.'modules/prestaspeed/views/css/jquery.infinitescroll.css');
            }

            if (_PS_VERSION_ > '1.5.0.0') {
                $this->context->controller->addJS(($this->_path).'views/js/jquery.infinitescroll.min.js');
                $this->context->controller->addCSS(($this->_path).'views/css/jquery.infinitescroll.css');
            }
        }
        if (Configuration::get('PRESTASPEED_YOUTUBE') != null && Configuration::get(
            'PRESTASPEED_YOUTUBE'
        ) != 0) {
            if (_PS_VERSION_ > '1.4.0.0' && _PS_VERSION_ < '1.5.0.0') {
                Tools::addJS(__PS_BASE_URI__.'modules/prestaspeed/views/js/youtube.js');
                Tools::addCSS(__PS_BASE_URI__.'modules/prestaspeed/views/css/youtube.css');
            }

            if (_PS_VERSION_ > '1.5.0.0') {
                $this->context->controller->addJS(($this->_path).'views/js/youtube.js');
                $this->context->controller->addCSS(($this->_path).'views/css/youtube.css');
            }
        }
        if ($speed == 1) {
            ini_set(
                'zlib.output_compression_level',
                9
            );
            if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
                ob_start('ob_gzhandler');
            }
        }
        /*$time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $start = $time;
        Configuration::updateValue(
            'PRESTASPEED_LOADS',
            $start
        );*/
    }
    public function hookdisplayBeforeBodyClosingTag($params)
    {
        $instaclick = '';
        if (Configuration::get('PS_CSS_THEME_CACHE') == 1 && Configuration::get('PS_JS_THEME_CACHE') == 1 && _PS_VERSION_ > "1.7.0.0" && Configuration::get('PRESTASPEED_PRELOAD') == 1) {
            $this->context->controller->addJS(($this->_path).'views/js/instantclick.min.js');
            $instaclick = 1;
        } else {
            $instaclick = 0;
        }
        if (_PS_VERSION_ > "1.7.0.0") {
            $this->smarty->assign(array('instaclick' => $instaclick));
            return $this->display(
                __FILE__,
                'views/templates/front/prestaspeed-header.tpl'
            );
        }
    }

    public function currentUrl()
    {
        $s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
        $protocol = 'http'.$s;
        $port = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':'.$_SERVER['SERVER_PORT']);
        return $protocol.'://'.$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
    }

    public function hookDisplayBackOfficeFooter()
    {
        /*
		$this->context->smarty->assign(array(
		'psversion'      => _PS_VERSION_,
		));
		return $this->display(__FILE__, 'views/templates/front/insta.tpl');*/
    }

    public function hookDisplayBackOfficeHeader()
    {
        $speed = Configuration::get('PRESTASPEED_SPEED');
        /*
				if (_PS_VERSION_ > '1.4.0.0' && _PS_VERSION_ < '1.5.0.0')

				Tools::addJS(__PS_BASE_URI__.'modules/prestaspeed/js/instantclick.min.js');

			if (_PS_VERSION_ > '1.5.0.0')

				$this->context->controller->addJS(($this->_path).'js/instantclick.min.js');
	*/
    }

    public function hookFooter()
    {
        $options = array(
            'loading' => array(
                'msgText' => '<div class="infscr-loading"><p>'.$this->l('Loading').'</p></div>',
                'finishedMsg' => '<div class="infscr-loading"><p>'.$this->l('No more products').'</p></div>',
                'img' => $this->_path.'views/img/ajax-loader-mini.gif',
                'speed' => 'slow',
            ),
            'nextSelector' => Configuration::get('PRESTASPEED_NEXT_SELECTOR'),
            'navSelector' => Configuration::get('PRESTASPEED_NAV_SELECTOR'),
            'itemSelector' => Configuration::get('PRESTASPEED_ITEM_SELECTOR'),
            'contentSelector' => Configuration::get('PRESTASPEED_CONTENT_SELECTOR'),
            'debug' => false,
            //'animate'      => true,
            //'buffer' => 24,
            'behavior' => '',
            'callback' => ''
        );
        $options = Tools::jsonEncode($options);
        $this->context->smarty->assign(
            array(
                'infi' => Configuration::get('PRESTASPEED_INFI'),
                'pagesnb' => Configuration::get('PS_PRODUCTS_PER_PAGE'),
                'speedfacebook' => Configuration::get('PRESTASPEED_SPEEDFACEBOOK'),
                'psversion' => _PS_VERSION_,
            )
        );
        if (strpos($_SERVER['HTTP_USER_AGENT'], "Google")) {
        } else {
            if (!$this->isCached('blockfacebook.tpl', $this->getCacheId())) {
                $facebookurl = Configuration::get('PRESTASPEED_FACEBOOK');
                if (!strstr($facebookurl, 'facebook.com')) {
                    $facebookurl = 'https://www.facebook.com/' . $facebookurl;
                }
                $this->context->smarty->assign('facebookurl', $facebookurl);
            }
        }
        $this->smarty->assign(array('options' => $options));
        if (_PS_VERSION_ < "1.7.0.0") {
            return $this->display(
                __FILE__,
                'views/templates/front/insta.tpl'
            );
        } else {
            return $this->display(
                __FILE__,
                'views/templates/front/insta17.tpl'
            );
        }
    }
    public function minIfin($wtm)
    {
        /*include_once(_PS_MODULE_DIR_.'prestaspeed/classes/minifier.php');
        $files = $this->rglob('{*.'.$wtm.'}', '../themes/'._THEME_NAME_, GLOB_BRACE);
        $mini = new Minifier();
        foreach ($files as $file) {
            set_time_limit(60);
            $sourcePath = $file;
            if ($wtm == 'css') {
                if (file_exists($file.'unmin')) {
                } else {
                    copy($file, $file.'unmin');
                    if (strpos($file, 'min') === false) {
                        $css = Tools::file_get_contents('http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.str_replace('../', '', $file));
                        $minifiedcss = $mini->getMinified('http://cssminifier.com/raw', $css);
                        $handler = fopen($file, 'w');
                        if ($minifiedcss != null) {
                            fwrite($handler, $minifiedcss);
                            fclose($handler);
                        }
                    }
                }
            } elseif ($wtm == 'js') {
                if (file_exists($file.'unmin')) {
                } else {
                    if (strpos($file, 'min') === false) {
                        copy($file, $file.'unmin');
                        $js = Tools::file_get_contents('http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.str_replace('../', '', $file));
                        $minifiedcss = $mini->getMinified('http://javascript-minifier.com/raw', $js);
                        $handler = fopen($file, 'w');
                        if ($minifiedcss != null) {
                            fwrite($handler, $minifiedcss);
                            fclose($handler);
                        }
                    }
                }
            }
        }*/
    }
    public function isDomainAvailible($domain)
    {
        $curlInit = curl_init($domain);
        curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curlInit, CURLOPT_HEADER, true);
        curl_setopt($curlInit, CURLOPT_NOBODY, true);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlInit);
        curl_close($curlInit);
        if ($response) {
            return true;
        }
        return false;
    }
    public function rglob(
        $pattern = '*',
        $path = '',
        $flags = 0
    ) {
        $files = array();
        $paths = glob(
            $path.'*',
            GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT
        );
        $files = glob(
            $path.$pattern,
            $flags
        );
        foreach ($paths as $path) {
            @$files = array_merge(
                $files,
                $this->rglob($pattern, $path, $flags)
            );
        }
        return $files;
    }

    /*one image*/
    public function smushcustom($cusi)
    {
        if (!is_callable('curl_init')) {
            $output = $this->displayError($this->l('cURL not loaded'));
        }
        $this->registerHook('watermark');
        /***************************************************************************/
        include_once('smusher.php');
        $cusi = Configuration::get('PRESTASPEED_CUSI');
        //define('BASEPATH', _PS_ROOT_DIR_.'/'.str_replace('../', '', $type)); // TODO: CAMBIAR ESTO POR TU PATH ORIGINAL
        define('BASEURL', _PS_ROOT_DIR_.'/'.str_replace('../', '', $cusi)); // TODO: Y ESTO POR TU URL
        define('MIN_TIME', 1); // uTime, modificado hace mínimo una fase lunar (aprox. 29 dias)
        define('ORIGINAL_POSTFIX', '_orig');
        $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NOT NULL, `smushed` TINYINT(1) NOT NULL,`saved` varchar(255) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
        Db::getInstance()
          ->Execute($query);
        $smusher = new Smush();
        $smusher->it(BASEURL);
        $bytes = 'SELECT sum(saved) as total FROM '._DB_PREFIX_.'smush WHERE saved > 0;';
        $bytt = Db::getInstance()
                  ->executeS($bytes);
        Configuration::updateValue(
            'PRESTASPEED_TOTCOMP',
            ($bytt[0]['total'] * 1) / 1024
        );
        $fcount  = $this->rglob('{*.jpg,*.png,*.gif}', BASEURL, GLOB_BRACE);
        $filecount = count($fcount);
        Configuration::updateValue(
            'PRESTASPEED_TOTSMUSH',
            1
        );
        Configuration::updateValue(
            'PRESTASPEED_TOTFIL',
            $filecount
        );
    }

    /*optimize folder*/
    public function smushcustom2(
        $cusi2,
        $newv
    ) {
        $start_time = 0;
        $max_execution_time = 3337200;
        require_once _PS_MODULE_DIR_.$this->name.'/smushit.inc.php';
        $smush = new SmushIt();
//return $files;
// convertimos la salida del comando en un array trabajable.
// init variables stats
        $totalCompress = 0;
        $sumCompress = 0;
        $bytesAhorro = 0;
        $i = 0;
        foreach ($newv as $file) {
            set_time_limit(-0);
            $i++;
            $filena = str_replace(
                'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__,
                '',
                $file['url']
            );
            $query2 = 'SELECT * FROM '._DB_PREFIX_.'smush WHERE `url` = \''.pSQL($filena).'\' AND `smushed` = 0';
            $quer = Db::getInstance()
                      ->executeS($query2);
            if ($quer != null) {
//var_dump($file['url'].$file['filename']);
                //
                // y de momento no nos interesa por los cambios de
                // código que implica.
                if ($file['extension'] != '.gif') {
                    // comprovar que no existe ya optimizado de hace poco...
                    //  if ((time() - (filemtime(_PS_ROOT_DIR_.'/'.$_GET['url'].$file['basename']))) > 0) {
                    $compressRes = $smush->compress($file['url']);
                    $a = 0;
                    if (isset($compressRes->error) || $compressRes->dest_size == -1) {
                        $upd = 'UPDATE '._DB_PREFIX_.'smush SET `smushed` = \'-1\' WHERE  '._DB_PREFIX_.'smush.url =  \''.pSQL($filena).'\'';
                        Db::getInstance()
                          ->Execute($upd);
                    }
                    if ($compressRes->src_size) {
                        if ($compressRes->src_size > $compressRes->dest_size && $compressRes->dest != null && $compressRes->dest_size != -1) {
                            // Realizamos copia del original
                            // pero solo si no existe el original ya.
                            if (!file_exists($file['pathsmush'])) {
                                //echo 'Realizamos copia del original ' . PHP_EOL . 'cp ' . $compressRes->dest . ' ' . $file['pathsmush'] . PHP_EOL;
                                //var_dump($file['path'].$file['basename']);
                                $nfil = _PS_ROOT_DIR_.'/'.$cusi2.$file['basename'];
                                $nfiltmp = _PS_ROOT_DIR_.'/'.$cusi2.'tmp_'.$file['filename'];
                                copy($compressRes->dest, $nfil);
                                /*use curl*/
                                set_time_limit(0);
                                $fp = fopen(
                                    $nfiltmp,
                                    'w+'
                                );//This is the file where we save the    information
                                $ch = curl_init(str_replace(" ", "%20", $compressRes->dest));//Here is the file we are downloading, replace spaces with %20
                                curl_setopt(
                                    $ch,
                                    CURLOPT_TIMEOUT,
                                    200
                                );
                                curl_setopt(
                                    $ch,
                                    CURLOPT_FILE,
                                    $fp
                                ); // write curl response to file
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                curl_exec($ch); // get curl response
                                curl_close($ch);
                                fclose($fp);
                                if (file_exists($nfiltmp)) {
                                    if (filesize($nfiltmp != 0) || filesize($nfiltmp != null)) {
                                        copy($nfiltmp, $nfil);
                                    }
                                }
                                //unlink($nfiltmp);

                                $total = $compressRes->src_size - $compressRes->dest_size;
                                $upd = 'UPDATE '._DB_PREFIX_.'smush SET `smushed` = \'1\', `saved` = \''.pSQL((float)$total).'\' WHERE  '._DB_PREFIX_.'smush.url =  \''.pSQL($filena).'\'';
                                Db::getInstance()
                                  ->Execute($upd);
                                //exec('cp ' . $file['path'] . ' ' . $file['pathsmush']);
                            }
                            if ($compressRes->src_size <= $compressRes->dest_size) {
                                $upd = 'UPDATE '._DB_PREFIX_.'smush SET `smushed` = \'2\', `saved` = \'0\' WHERE  '._DB_PREFIX_.'smush.url =  \''.pSQL($filena).'\'';
                                Db::getInstance()
                                  ->Execute($upd);
                            }
                            // Bajamos el fichero "bien" comprimido
                            // echo 'downloading: ' . $file['path'] . PHP_EOL . 'from: ' . $compressRes->dest . ' (' . $compressRes->percent . '%)' . PHP_EOL . PHP_EOL ;
                            //  exec('wget --output-document=' . $file['path'] . ' ' . $compressRes->dest);
                            // calulamos estadístcas.
                            $bytesAhorro += $compressRes->src_size - $compressRes->dest_size;
                            $sumCompress += $compressRes->percent;
                            $totalCompress++;
                        }
                    } else {
                        //   var_dump($compressRes). PHP_EOL;
                        $a++;
                    }
                }
                // echo '<br/>'.'Bytes total ahorrados = '.($bytesAhorro).'<br/>';
            }
            //Configuration::updateValue('PRESTASPEED_TOTSMUSH', $i);
        }
        if ($totalCompress == 0) {
            $totalCompress = 1;
        } // Evita un divided by 0.
    }


    /**/
    public function smushall(
        $type,
        $output,
        $cusi
    ) {
        if (!is_callable('curl_init')) {
            $output = $this->displayError($this->l('cURL not loaded'));
        }
        $this->registerHook('watermark');
        /***************************************************************************/
        include_once('smusher.php');
        if (Configuration::get('PRESTASPEED_CUSI') == null) {
            $cusi2 = Configuration::get('PRESTASPEED_CUSI2');
            define('BASEPATH', _PS_ROOT_DIR_.'/'.str_replace('../', '', $type)); // TODO: CAMBIAR ESTO POR TU PATH ORIGINAL
            //define('BASEURL', 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.$cusi); // TODO: Y ESTO POR TU URL
            define('MIN_TIME', 1); // uTime, modificado hace mínimo una fase lunar (aprox. 29 dias)
            define('ORIGINAL_POSTFIX', '_orig');
            $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NOT NULL, `smushed` TINYINT(1) NOT NULL,`saved` varchar(255) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
            Db::getInstance()
              ->Execute($query);
            $smusher = new Smush();
            $smusher->it(BASEPATH);
            $bytes = 'SELECT sum(saved) as total FROM '._DB_PREFIX_.'smush WHERE saved > 0;';
            $bytt = Db::getInstance()
                      ->executeS($bytes);
            Configuration::updateValue(
                'PRESTASPEED_TOTCOMP',
                ($bytt[0]['total'] * 1) / 1024
            );
            $fcount  = $this->rglob('{*.jpg,*.png,*.gif}', BASEPATH, GLOB_BRACE);
            $filecount = count($fcount);
            Configuration::updateValue(
                'PRESTASPEED_TOTSMUSH',
                $filecount
            );
            Configuration::updateValue(
                'PRESTASPEED_TOTFIL',
                $filecount
            );
        } else {
            $cusi = Configuration::get('PRESTASPEED_CUSI');
            define('BASEPATH', _PS_ROOT_DIR_.'/'.str_replace('../', '', $type)); // TODO: CAMBIAR ESTO POR TU PATH ORIGINAL
            define('BASEURL', 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.str_replace('../', '', $cusi)); // TODO: Y ESTO POR TU URL
            define('MIN_TIME', 1); // uTime, modificado hace mínimo una fase lunar (aprox. 29 dias)
            define('ORIGINAL_POSTFIX', '_orig');
            $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NOT NULL, `smushed` TINYINT(1) NOT NULL,`saved` varchar(255) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
            Db::getInstance()
              ->Execute($query);
            $smusher = new Smush();
            $smusher->it(BASEURL);
            $bytes = 'SELECT sum(saved) as total FROM '._DB_PREFIX_.'smush WHERE saved > 0;';
            $bytt = Db::getInstance()
                      ->executeS($bytes);
            Configuration::updateValue(
                'PRESTASPEED_TOTCOMP',
                ($bytt[0]['total'] * 1) / 1024
            );
            $fcount  = $this->rglob('{*.jpg,*.png,*.gif}', BASEURL, GLOB_BRACE);
            $filecount = count($fcount);
            Configuration::updateValue(
                'PRESTASPEED_TOTSMUSH',
                $filecount
            );
            Configuration::updateValue(
                'PRESTASPEED_TOTFIL',
                $filecount
            );
        }
    }


    public function postProcess()
    {
        $errors = '';
        $output = '';
        if (Tools::isSubmit('submitDelete4')) {
            if ($logf = Tools::getValue('logf')) {
                Configuration::updateValue('PRESTASPEED_LOGF', $logf);
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_LOGF');
            }
        }
        if (Tools::isSubmit('submitDelete3')) {
            if ($cusi2 = Tools::getValue('cusi2')) {
                Configuration::updateValue('PRESTASPEED_CUSI2', $cusi2);
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CUSI2');
            }
            /*
				if ($cusi2 != null)
			{
define('BASEPATH', _PS_ROOT_DIR_.'/'.$cusi2); // TODO: CAMBIAR ESTO POR TU PATH ORIGINAL
define('BASEURL', 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.$cusi2); // TODO: Y ESTO POR TU URL
define('MIN_TIME', 1); // uTime, modificado hace mínimo una fase lunar (aprox. 29 dias)
define('ORIGINAL_POSTFIX', '_orig');
$files  = $this->rglob('{*.jpg,*.png,*.gif}', BASEPATH, GLOB_BRACE);
Configuration::updateValue('PRESTASPEED_TOTFIL', count($files));
$query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NULL, `smushed` TINYINT(1) NOT NULL,`saved` varchar(255) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
Db::getInstance()->Execute($query);
$newv = array();
foreach ($files as $file) {
    $newv[] = pathinfo($file);
}
foreach ($newv as $k => $v) {


    //var_dump($newv)	;

    $newv[$k]['path'] = $newv[$k]['dirname'].'/';
    $newv[$k]['filename'] = $newv[$k]['filename'];
    $newv[$k]['extension'] = substr(
        $newv[$k]['path'],
        strrpos(
            $newv[$k]['path'],
            '.'
        )
    );
    $newv[$k]['pathsmush'] = substr(
            $newv[$k]['path'],
            0,
            strrpos(
                $newv[$k]['path'],
                '.'
            )
        ).ORIGINAL_POSTFIX.$newv[$k]['extension'];
    $newv[$k]['url'] = BASEURL.str_replace(
            BASEPATH,
            '',
            $newv[$k]['path']
        ).$newv[$k]['basename'];
    //  $newv[$k]['mimetype'] = $v['extension'];
}
$i = 0;
foreach ($newv as $file) {
	$i++;
$query = 'INSERT IGNORE INTO '._DB_PREFIX_.'smush (`id_smush`, `url`, `smushed`, `saved`) VALUES (\''.pSQL((int)$i).'\', \''.pSQL($cusi2.$file['basename']).'\', \'0\', \'0\')';

			if (!Db::getInstance()->Execute($query));
}
				$this->smushcustom2($cusi2,$newv);
				$output .= $this->displayConfirmation($this->l('Total proceced images:').Configuration::get('PRESTASPEED_TOTSMUSH').'<br/>');
				$bytes = 'SELECT sum(saved) as total FROM '._DB_PREFIX_.'smush';
				$bytt  = Db::getInstance()->executeS($bytes);
				Configuration::updateValue('PRESTASPEED_TOTCOMP', ($bytt[0]['total'] * 1) / 1024);
				$output .= $this->displayConfirmation($this->l('Total KB saved:').round(Configuration::get('PRESTASPEED_TOTCOMP'), 2).'<br/>');
			}*/
        }
        if (Tools::isSubmit('submitDelete2')) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'smush` CHANGE `saved` `saved` FLOAT(11) NULL DEFAULT NULL;');
            if ($smush = Tools::getValue('smush')) {
                Configuration::updateValue(
                    'PRESTASPEED_SMUSH',
                    $smush
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_SMUSH');
            }
            if ($cleani = Tools::getValue('cleani')) {
                Configuration::updateValue(
                    'PRESTASPEED_CLEANI',
                    $cleani
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CLEANI');
            }
            if ($cusi = Tools::getValue('cusi')) {
                Configuration::updateValue(
                    'PRESTASPEED_CUSI',
                    $cusi
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CUSI');
            }
            if ($smush2 = Tools::getValue('smush2')) {
                Configuration::updateValue(
                    'PRESTASPEED_SMUSH2',
                    $smush2
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_SMUSH2');
            }
            if ($batcht = Tools::getValue('batcht')) {
                Configuration::updateValue(
                    'PRESTASPEED_BATCHT',
                    $batcht
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_BATCHT');
            }

            if ($type = Tools::getValue('type')) {
                Configuration::updateValue(
                    'PRESTASPEED_TYPE',
                    $type
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_TYPE');
            }
            if ($stats = Tools::getValue('stats')) {
                Configuration::updateValue(
                    'PRESTASPEED_STATS',
                    $stats
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_STATS');
            }
            if ($stats == 1) {
                Db::getInstance()
                  ->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'smush` ;');
                Configuration::updateValue(
                    'PRESTASPEED_TOTCOMP',
                    0
                );
                Configuration::updateValue(
                    'PRESTASPEED_TOTCOMPF',
                    0
                );
                $output .= $this->displayConfirmation($this->l('Image optimization stats deleted').'<br/>');
            }
            if ($smush == 1) {
                $output .= $this->displayConfirmation($this->l('New product image optimization enabled').'<br/>');
            } else {
                $output .= $this->displayConfirmation($this->l('New product image optimization disabled').'<br/>');
            }
            if ($smush2 == 1 && $cusi == null) {
                //include('ajax.php');
                /*$this->smushall(
                   $type,
                   $output,
                   $cusi
                );*/
            }
            if ($smush2 == 1 && $cusi == null) {
                /*$output .= $this->displayConfirmation(
                    $this->l('Total proceced images:').Configuration::get('PRESTASPEED_TOTSMUSH').'<br/>'
                );
                $output .= $this->displayConfirmation(
                    $this->l('Total images:').Configuration::get('PRESTASPEED_TOTFIL').'<br/>'
                );*/
                $bytes = 'SELECT sum(saved) as total FROM '._DB_PREFIX_.'smush WHERE saved > 0;';
                $bytt = Db::getInstance()
                          ->executeS($bytes);
                Configuration::updateValue(
                    'PRESTASPEED_TOTCOMP',
                    ($bytt[0]['total'] * 1) / 1024
                );
                $output .= $this->displayConfirmation(
                    $this->l('Total KB saved:').round(
                        Configuration::get('PRESTASPEED_TOTCOMP'),
                        2
                    ).'<br/>'
                );
            }
            if ($cusi != null) {
                //$this->smushcustom($cusi);
                /*$output .= $this->displayConfirmation(
                    $this->l('Total proceced images:').Configuration::get('PRESTASPEED_TOTSMUSH').'<br/>'
                );*/
                $bytes = 'SELECT sum(saved) as total FROM '._DB_PREFIX_.'smush WHERE saved > 0;';
                $bytt = Db::getInstance()
                          ->executeS($bytes);
                Configuration::updateValue(
                    'PRESTASPEED_TOTCOMP',
                    ($bytt[0]['total'] * 1) / 1024
                );
                $output .= $this->displayConfirmation(
                    $this->l('Total KB saved:').round(
                        Configuration::get('PRESTASPEED_TOTCOMP'),
                        2
                    ).'<br/>'
                );
            }
            if ($cleani == 1) {
                $this->clearTmpDir();
                $output .= $this->displayConfirmation(
                    $this->l('TMP images deleted:').Configuration::get('PRESTASPEED_TMPI').'<br/>'
                );
            }
            Configuration::updateValue(
                'PRESTASPEED_TMPI',
                0
            );
            $this->sendmail();
            return $output;
        }
        if (Tools::isSubmit('submitDelete')) {
            /*delete all*/
            $output .= '<div class="spinner">
  <div class="rect1"></div>
  <div class="rect2"></div>
  <div class="rect3"></div>
  <div class="rect4"></div>
  <div class="rect5"></div>
</div>';
            $total = '';
            $total2 = '';
            $total3 = '';
            $total4 = '';
            $total5 = '';
            $total6 = '';
            $total7 = '';
            $total8 = '';
            $total9 = '';
            $total10 = '';
            $total11 = '';
            $total4c = '';
            $tmp = '';
            $totalpnf = null;
            $befo = Configuration::get('PRESTASPEED_BEFO');
            if ($befo == null) {
                $befo = date("Y-m-d H:i:s");
            }
            if ($conn = Tools::getValue('conn')) {
                Configuration::updateValue(
                    'PRESTASPEED_CO',
                    $conn
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CO');
            }
            if ($gues = Tools::getValue('gues')) {
                Configuration::updateValue(
                    'PRESTASPEED_GU',
                    $gues
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_GU');
            }
            if ($cusmes = Tools::getValue('cusmes')) {
                Configuration::updateValue(
                    'PRESTASPEED_CUSMES',
                    $cusmes
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CUSMES');
            }
            if ($cusmesdate = Tools::getValue('cusmesdate')) {
                Configuration::updateValue(
                    'PRESTASPEED_CUSMESDATE',
                    $cusmesdate
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CUSMESDATE');
            }
            if ($ovalid = Tools::getValue('ovalid')) {
                Configuration::updateValue(
                    'PRESTASPEED_OVALID',
                    $ovalid
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_OVALID');
            }
            if ($pag = Tools::getValue('pag')) {
                Configuration::updateValue(
                    'PRESTASPEED_PA',
                    $pag
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_PA');
            }
            if ($dis = Tools::getValue('dis')) {
                Configuration::updateValue(
                    'PRESTASPEED_DI',
                    $dis
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_DI');
            }
            if ($pnf = Tools::getValue('pnf')) {
                Configuration::updateValue(
                    'PRESTASPEED_PNF',
                    $pnf
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_PNF');
            }
            if ($car = Tools::getValue('car')) {
                Configuration::updateValue(
                    'PRESTASPEED_CA',
                    $car
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CA');
            }
            if ($befo = Tools::getValue('befo')) {
                Configuration::updateValue(
                    'PRESTASPEED_BEFO',
                    $befo
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_BEFO');
            }
            if ($cav = Tools::getValue('cav')) {
                Configuration::updateValue(
                    'PRESTASPEED_CAV',
                    $cav
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CAV');
            }

            $cav = Configuration::get('PRESTASPEED_CAV');

            if ($sp_from = Tools::getValue('sp_from')) {
                Configuration::updateValue(
                    'PRESTASPEED_FR',
                    $sp_from
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_FR');
            }
            if ($sp_to = Tools::getValue('sp_to')) {
                Configuration::updateValue(
                    'PRESTASPEED_TO',
                    $sp_to
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_TO');
            }
            if ($func = Tools::getValue('func')) {
                Configuration::updateValue(
                    'PRESTASPEED_FUNC',
                    $func
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_FUNC');
            }

            if ($ord = Tools::getValue('ord')) {
                Configuration::updateValue(
                    'PRESTASPEED_ORD',
                    $ord
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_ORD');
            }
            if ($sp_from2 = Tools::getValue('sp_from2')) {
                Configuration::updateValue(
                    'PRESTASPEED_FR2',
                    $sp_from2
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_FR2');
            }
            if ($sp_to2 = Tools::getValue('sp_to2')) {
                Configuration::updateValue(
                    'PRESTASPEED_TO2',
                    $sp_to2
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_TO2');
            }

            if ($dat = Tools::getValue('dat')) {
                Configuration::updateValue(
                    'PRESTASPEED_DA',
                    $dat
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_DA');
            }
            if ($ord == 1 && $sp_from2 != null && $sp_to2 != null) {
                $ord = Db::getInstance(_PS_USE_SQL_SLAVE_)
                         ->executeS(
                             'SELECT * FROM '._DB_PREFIX_.'orders WHERE date_add > \''.pSQL(
                                 $sp_from2
                             ).'\' AND date_add < \''.pSQL($sp_to2).'\' AND valid = '.(int)$ovalid.';'
                         );
                $h = 0;
                foreach ($ord as $order) {
                    $this->deleteorderbyid($ord[$h]['id_order']);
                    //var_dump($ord[$h]['id_order']);
                    $h++;
                }
                $output .= $this->displayConfirmation($this->l('Total orders deleted').': '.$h.'<br/>');
            }
            Configuration::updateValue('PS_USE_HTMLPURIFIER', 0);
            if ($conn == 1) {
                $sorgudc = Db::getInstance(_PS_USE_SQL_SLAVE_)
                             ->executeS(
                                 'SELECT * FROM `'._DB_PREFIX_.'connections` WHERE `date_add` < \''.pSQL($befo).'\'
         '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').' LIMIT 9000;'
                             );
                if ($sorgudc === false) {
                    $tmp = '';
                } else {
                    $veridc = $sorgudc;
                    @$total = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                ->NumRows($sorgudc);
                }
                $sorgudc2 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                              ->executeS('SELECT * FROM `'._DB_PREFIX_.'connections_page` LIMIT 9000;');
                if ($sorgudc2 === false) {
                    $tmp = '';
                } else {
                    $veridc2 = $sorgudc2;
                    @$total2 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                 ->NumRows($sorgudc2);
                }
                /*pnf*/
                if ($pnf == 1 && Module::isInstalled('pagesnotfound')) {
                    $sorgudcpnf = Db::getInstance(_PS_USE_SQL_SLAVE_)
                             ->executeS(
                                 'SELECT * FROM `'._DB_PREFIX_.'pagenotfound` WHERE `date_add` < \''.pSQL($befo).'\'
         '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').' LIMIT 9000;'
                             );
                    if ($sorgudcpnf === false) {
                        $tmp = '';
                    } else {
                        $veridcpnf = $sorgudcpnf;
                        @$totalpnf = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                ->NumRows($sorgudcpnf);
                    }
                }
                /*end pnf*/
                $sorgudc3 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                              ->executeS(
                                  'SELECT * FROM `'._DB_PREFIX_.'connections_source` WHERE `date_add` < \''.pSQL(
                                      $befo
                                  ).'\' LIMIT 9000;'
                              );
                if ($sorgudc3 === false) {
                    $tmp = '';
                } else {
                    $veridc3 = $sorgudc3;
                    @$total3 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                 ->NumRows($sorgudc3);
                }
            }
            if ($gues == 1) {
                $sorgudc7 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                              ->executeS(
                                  'SELECT g.*, c.*  FROM `'._DB_PREFIX_.'guest` g LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest WHERE c.`date_add` < \''.pSQL(
                                      $befo
                                  ).'\' LIMIT 9000;'
                              );
                if ($sorgudc7 === false) {
                    $tmp = '';
                    $total7 = '';
                } else {
                    $veridc7 = $sorgudc7;
                    $total7 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                ->NumRows($sorgudc7);
                }
            }
            if ($cusmes == 1) {
                $sorgudc7mes = Db::getInstance(_PS_USE_SQL_SLAVE_)
                              ->executeS(
                                  'SELECT *  FROM `'._DB_PREFIX_.'customer_thread` WHERE `date_add` < \''.pSQL(
                                      $cusmesdate
                                  ).'\' LIMIT 9000;'
                              );
                if ($sorgudc7mes === false) {
                    $tmp = '';
                    $total7mes = '';
                } else {
                    $veridc7mes = $sorgudc7mes;
                    $total7mes = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                ->NumRows($sorgudc7mes);
                }
            }
            if ($pag == 1) {
                $sorgudc8 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                              ->executeS(
                                  'SELECT * FROM `'._DB_PREFIX_.'page_viewed`
         '.((_PS_VERSION_ > '1.5.0.0') ? ' WHERE `id_shop` = '.(int)$this->context->shop->id : '').' LIMIT 9000;'
                              );
                if ($sorgudc8 === false) {
                    $tmp = '';
                } else {
                    $veridc8 = $sorgudc8;
                    $total8 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                ->NumRows($sorgudc8);
                }
            }
            /*cart*/
            if ($car == 1) {
                if ($sp_from == 0 && $sp_to == 0) {
                    $sorgudc4 = Db::getInstance()
                                  ->executeS(
                                      'SELECT * FROM `'._DB_PREFIX_.'cart` WHERE `id_customer` = 0'.($cav == 1 ? ' OR `id_customer` != 0' : '').';'
                                  );
                    if ($sorgudc4 === false) {
                        $tmp = '';
                    } else {
                        $veridc4 = $sorgudc4;
                        $total4 = Db::getInstance()
                                    ->NumRows($sorgudc4);
                    }
                }
                if ($sp_from != 0 && $sp_to != 0) {
                    $sorgudc4 = Db::getInstance()
                                  ->executeS(
                                      'SELECT * FROM `'._DB_PREFIX_.'cart`
                        WHERE `id_customer` = 0'.($cav == 1 ? ' OR `id_customer` != 0' : '').'
                        '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').'
                        AND `date_upd` BETWEEN \''.pSQL($sp_from).'\' AND \''.pSQL($sp_to).'\';'
                                  );

                    if ($sorgudc4 === false) {
                        $tmp = '';
                    } else {
                        $veridc4 = $sorgudc4;
                        $total4 = Db::getInstance()
                                    ->NumRows($sorgudc4);
                    }
                }
                /**/
                if ($sp_from == 0 && $sp_to == 0) {
                    $sorgudc4c = Db::getInstance()
                                   ->executeS(
                                       'SELECT * FROM `'._DB_PREFIX_.'cart` WHERE `id_customer` = 0'.($cav == 1 ? ' OR `id_customer` != 0' : '').';'
                                   );
                    if ($sorgudc4c === false) {
                        $tmp = '';
                    } else {
                        $veridc4c = $sorgudc4c;
                        $total4c = Db::getInstance()
                                     ->NumRows($sorgudc4c);
                    }
                }
                if ($sp_from != 0 && $sp_to != 0) {
                    $sorgudc4c = Db::getInstance()
                                   ->executeS(
                                       'SELECT * FROM `'._DB_PREFIX_.'cart`
        WHERE `id_customer` = 0'.($cav == 1 ? ' OR `id_customer` != 0' : '').'
        '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').'
        AND `date_upd` BETWEEN \''.pSQL($sp_from).'\' AND \''.pSQL($sp_to).'\';'
                                   );
                    if ($sorgudc4c === false) {
                        $tmp = '';
                    } else {
                        $veridc4c = $sorgudc4c;
                        $total4c = Db::getInstance()
                                     ->NumRows($sorgudc4c);
                    }
                }
                /**/
            }
            if ($dis == 1) {
                /*ps > 1.5*/
                if (_PS_VERSION_ < '1.5.0.0') {
                    $sorgudc6 = Db::getInstance()
                                  ->executeS('SELECT * FROM `'._DB_PREFIX_.'discount`;');
                    if ($sorgudc6 === false) {
                        $tmp = '';
                    } else {
                        $veridc6 = $sorgudc6;
                        @$total6 = Db::getInstance()
                                     ->NumRows($sorgudc6);
                    }
                    $current_date = date('Y-m-d H:i:s');
                    $sorgudc5 = Db::getInstance()
                                  ->executeS(
                                      'SELECT * FROM `'._DB_PREFIX_.'discount` WHERE `date_to` < \''.pSQL(
                                          $current_date
                                      ).'\';'
                                  );
                    if ($sorgudc5 === false) {
                        $tmp = '';
                    } else {
                        $veridc5 = $sorgudc5;
                        $total5 = Db::getInstance()
                                    ->NumRows($sorgudc5);
                    }
                }
                /*end*/
                if (_PS_VERSION_ > '1.5.0.0') {
                    $current_date = date('Y-m-d H:i:s');
                    $sorgudc9 = Db::getInstance()
                                  ->executeS(
                                      'SELECT * FROM `'._DB_PREFIX_.'cart_rule` WHERE `date_to` < \''.pSQL(
                                          $current_date
                                      ).'\' AND `date_to` != \'0000-00-00 00:00:00\';'
                                  );
                    if ($sorgudc9 === false) {
                        $tmp = '';
                    } else {
                        $veridc9 = $sorgudc9;
                        $total9 = Db::getInstance()
                                    ->NumRows($sorgudc9);
                    }
                    /**/
                    $current_date = date('Y-m-d H:i:s');
                    $sorgudc10 = Db::getInstance()
                                   ->executeS(
                                       'SELECT * FROM `'._DB_PREFIX_.'specific_price_rule` WHERE `to` < \''.pSQL(
                                           $current_date
                                       ).'\' AND `to` != \'0000-00-00 00:00:00\';'
                                   );
                    if ($sorgudc10 === false) {
                        $tmp = '';
                    } else {
                        $veridc10 = $sorgudc10;
                        $total10 = Db::getInstance()
                                     ->NumRows($sorgudc10);
                    }
                    $current_date = date('Y-m-d H:i:s');
                    $sorgudc11 = Db::getInstance()
                                   ->executeS(
                                       'SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE `to` < \''.pSQL(
                                           $current_date
                                       ).'\' AND `to` != \'0000-00-00 00:00:00\';'
                                   );
                    if ($sorgudc11 === false) {
                        $tmp = '';
                    } else {
                        $veridc11 = $sorgudc11;
                        $total11 = Db::getInstance()
                                     ->NumRows($sorgudc11);
                    }
                }
            }

            if ($conn == 1) {
                if ($total != null) {
                    foreach ($sorgudc as $veridc) {
                        $idconn = $veridc['id_connections'];
                        //var_dump($idconn);
                        if ($conn == 1) {
                            $this->deleteconn($idconn);
                        }
                    } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
                }
                if ($total2 != null) {
                    foreach ($sorgudc2 as $veridc2) {
                        $idconn2 = $veridc2['id_connections'];
                        if ($conn == 1) {
                            $this->deleteconn($idconn2);
                        }
                    } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
                }
                if ($total3 != null) {
                    foreach ($sorgudc3 as $veridc3) {
                        $idconn3 = $veridc3['id_connections'];
                        if ($conn == 1) {
                            $this->deleteconn($idconn3);
                        }
                    } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
                }
                if ($befo == date("Y-m-d H:i:s")) {
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'connections`');
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'connections_source`');
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'connections_page`');
                }
            }
            $modsperf = @Db::getInstance()->ExecuteS('SHOW TABLES LIKE \''._DB_PREFIX_.'modules_perfs\'');
            if ($modsperf) {
                @Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'modules_perfs`;');
            }
            if ($gues == 1) {
                Db::getInstance()
                  ->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'guest`;');
            }
            if ($pnf == 1) {
                if ($totalpnf != null) {
                    foreach ($sorgudcpnf as $veridcpnf) {
                        $idconnpnf = $veridcpnf['id_pagenotfound'];
                        if ($pnf == 1) {
                            $this->deletepnf($idconnpnf);
                        }
                    } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
                }
                if ($befo == date("Y-m-d H:i:s")) {
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'pagenotfound`');
                }
            }
            if ($cusmes == 1) {
                if ($total7mes  != null) {
                    foreach ($veridc7mes as $veridcmes) {
                        $idmes = $veridcmes['id_customer_thread'];
                        if ($cusmes == 1) {
                            $this->deletemes($idmes);
                        }
                        Db::getInstance()
                        ->Execute('DELETE FROM `'._DB_PREFIX_.'customer_thread` WHERE `id_customer_thread` = '.pSql($idmes).';');
                    }
                }
                /*if ($befo == date("Y-m-d H:i:s")) {
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer_messages`');
                }*/
            }
            if ($pag == 1) {
                Db::getInstance()
                  ->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'page_viewed`;');
            }
            if (_PS_VERSION_ > '1.5.0.0') {
                if ($total11 != null) {
                    foreach ($sorgudc11 as $veridc11) {
                        $idc11 = $veridc11['id_specific_price'];
                        if ($dis == 1) {
                            $this->delete11($idc11);
                        }
                    } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
                }
                if ($total10 != null) {
                    foreach ($sorgudc10 as $veridc10) {
                        $idc10 = $veridc10['id_specific_price_rule'];
                        if ($dis == 1) {
                            $this->delete10($idc10);
                        }
                    } //while ($veridc10 = mysql_fetch_assoc($sorgudc10));
                }
                /**/
                if (_PS_VERSION_ > '1.5.0.0') {
                    if ($total9 != null) {
                        foreach ($sorgudc9 as $veridc9) {
                            $idc9 = $veridc9['id_cart_rule'];
                            if ($dis == 1) {
                                $this->delete9($idc9);
                            }
                        } //while ($veridc9 = mysql_fetch_assoc($sorgudc9));
                    }
                }
            }
            /**/
            if ($total5 != null) {
                foreach ($sorgudc5 as $veridc5) {
                    $idc5 = $veridc5['id_discount'];
                    if ($dis == 1) {
                        $this->delete5($idc5);
                    }
                } //while ($veridc5 = mysql_fetch_assoc($sorgudc5));
            }
            if ($total4 != null) {
                foreach ($sorgudc4 as $veridc4) {
                    if ($car == 1) {
                        $idc = $veridc4['id_cart'];
                    }
                    $this->delete($idc);
                } //while ($veridc4 = mysql_fetch_assoc($sorgudc4));
            }
            if ($total4c != null) {
                foreach ($sorgudc4c as $veridc4c) {
                    $idcc = $veridc4c['id_cart'];
                    if ($car == 1) {
                        $this->deletec($idcc);
                    }
                } //while ($veridc4c = mysql_fetch_assoc($sorgudc4c));
            }
            if ($conn == 1 && $total != null) {
                if ($total == '9000') {
                    $output .= $this->displayConfirmation(
                        $this->l(
                            'Some rows still in  connection table, run the process again until get no results'
                        ).': '.$total.'<br/>'
                    );
                } else {
                    $output .= $this->displayConfirmation(
                        $this->l('Rows before optimization on connection table (now is zero)').': '.$total.'<br/>'
                    );
                }
            }
            if ($conn == 1 && $total2 != null) {
                if ($total2 == '9000') {
                    $output .= $this->displayConfirmation(
                        $this->l(
                            'Some rows still in  connection_page table, run the process again until get no results'
                        ).': '.$total2.'<br/>'
                    );
                } else {
                    $output .= $this->displayConfirmation(
                        $this->l('Rows before optimization on connection_page table (now is zero)').': '.$total2.'<br/>'
                    );
                }
            }
            if ($conn == 1 && $total3 != null) {
                if ($total3 == '9000') {
                    $output .= $this->displayConfirmation(
                        $this->l(
                            'Some rows still in  connection_page_source table, run the process again until get no results'
                        ).': '.$total3.'<br/>'
                    );
                } else {
                    $output .= $this->displayConfirmation(
                        $this->l(
                            'Rows before optimization on connection_page_source table (now is zero)'
                        ).': '.$total3.'<br/>'
                    );
                }
            }
            if ($pnf == 1 && $totalpnf != null) {
                if ($totalpnf == '9000') {
                    $output .= $this->displayConfirmation(
                        $this->l(
                            'Some rows still in  psgenotfound table, run the process again until get no results'
                        ).': '.$totalpnf.'<br/>'
                    );
                } else {
                    $output .= $this->displayConfirmation(
                        $this->l(
                            'Rows before optimization on psgenotfound table (now is zero)'
                        ).': '.$totalpnf.'<br/>'
                    );
                }
            }
            if ($gues == 1 && $total7 != null) {
                $output .= $this->displayConfirmation(
                    $this->l('Rows before optimization on guest table (now is zero)').': '.$total7.'<br/>'
                );
            }
            if ($cusmes == 1 && $total7mes != null) {
                $output .= $this->displayConfirmation(
                    $this->l('Rows before optimization on customer messages table (now is zero)').': '.$total7mes.'<br/>'
                );
            }

            if ($pag == 1 && $total8 != null) {
                $output .= $this->displayConfirmation(
                    $this->l('Rows before optimization on page_viewed table (now is zero)').': '.$total8.'<br/>'
                );
            }
            if ($car == 1 && $total4 != null) {
                $output .= $this->displayConfirmation(
                    $this->l(
                        'Rows before optimization on cart table (only abandoned carts for guests or users)'
                    ).': '.$total4.'<br/>'
                );
            }
            if ($dis == 1 && $total6 != null || $dis == 1 && $total9 != null || $dis == 1 && $total10 != null || $dis == 1 && $total11 != null) {
                $tottot = $total6 + $total9 + $total11 + $total10;
                $output .= $this->displayConfirmation(
                    $this->l(
                        'Rows before optimization on vouchers/discounts/product discounts tables'
                    ).': '.$tottot.'<br/>'
                );
            }
            if ($func == 1) {
                if (_PS_VERSION_ > '1.5.0.0') {
                    $this->checkAndFix();
                    $output .= $this->displayConfirmation($this->l('DB integrity fixed'));
                }
            }
            if ($dat == 1) {
                $alltables = Db::getInstance()
                               ->executeS('SHOW TABLES FROM `'._DB_NAME_.'`');
                $v = '';
                foreach ($alltables as $tablename) {
                    $tb = current($tablename);
                    Db::getInstance()
                      ->Execute('OPTIMIZE TABLE `'.$tb.'`');
                    Db::getInstance()
                      ->Execute('REPAIR TABLE `'.$tb.'`');
                    $v .= $tb.'</br>';
                }
                $output .= $this->displayConfirmation($v);
// Alert that operation was successful
                $output .= $this->displayConfirmation($this->l('Above tables successfully optimized.').'<br/>');
            }
            $output .= '
<style type="text/css">
.spinner {
display:none
}
</style>';
            // Reset the module properties
            //    $this->initialize();
            $this->_clearCache('prestaspeed-dash.tpl');
            $sql = Db::getInstance()
                     ->executeS(
                         "SELECT table_schema '"._DB_NAME_."', SUM( data_length + index_length) / 1024 / 1024 'db_size_in_mb' FROM information_schema.TABLES WHERE table_schema='"._DB_NAME_."' GROUP BY table_schema ;"
                     );
            $data = $sql;
            if (is_array($data)) {
                Configuration::updateValue(
                    'PRESTASPEED_DBS',
                    $data[0]['db_size_in_mb']
                );
            } else {
                Configuration::updateValue(
                    'PRESTASPEED_DBS',
                    $data['db_size_in_mb']
                );
            }
            $this->sendmail();
            //if (!$errors)
            return $output;
            //Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&conf=6');
            //echo $this->displayError($errors);
        }
        if (Tools::isSubmit('submitDeleteO')) {
            if ($iscontent = Tools::getValue('iscontent')) {
                Configuration::updateValue(
                    'PRESTASPEED_CONTENT_SELECTOR',
                    $iscontent
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CONTENT_SELECTOR');
            }
            if ($cssm = Tools::getValue('cssm')) {
                Configuration::updateValue(
                    'PRESTASPEED_CSSM',
                    $cssm
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CSSM');
            }
            if ($jsm = Tools::getValue('jsm')) {
                Configuration::updateValue(
                    'PRESTASPEED_JSM',
                    $jsm
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_JSM');
            }
            if ($isnav = Tools::getValue('isnav')) {
                Configuration::updateValue(
                    'PRESTASPEED_NAV_SELECTOR',
                    $isnav
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_NAV_SELECTOR');
            }
            if ($preload = Tools::getValue('preload')) {
                Configuration::updateValue(
                    'PRESTASPEED_PRELOAD',
                    $preload
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_PRELOAD');
            }
            if ($isnext = Tools::getValue('isnext')) {
                Configuration::updateValue(
                    'PRESTASPEED_NEXT_SELECTOR',
                    $isnext
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_NEXT_SELECTOR');
            }
            if ($isitem = Tools::getValue('isitem')) {
                Configuration::updateValue(
                    'PRESTASPEED_ITEM_SELECTOR',
                    $isitem
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_ITEM_SELECTOR');
            }

            if ($infi = Tools::getValue('infi')) {
                Configuration::updateValue(
                    'PRESTASPEED_INFI',
                    $infi
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_INFI');
            }
            if ($speed = Tools::getValue('speed')) {
                Configuration::updateValue(
                    'PRESTASPEED_SPEED',
                    $speed
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_SPEED');
            }

            if ($cac = Tools::getValue('cac')) {
                Configuration::updateValue(
                    'PRESTASPEED_CAC',
                    $cac
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_CAC');
            }

            if ($tem = Tools::getValue('tem')) {
                Configuration::updateValue(
                    'PRESTASPEED_TE',
                    $tem
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_TE');
            }
            if ($gzip = Tools::getValue('gzip')) {
                Configuration::updateValue(
                    'PRESTASPEED_GZ',
                    $gzip
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_GZ');
            }
            if ($java = Tools::getValue('java')) {
                Configuration::updateValue(
                    'PRESTASPEED_JA',
                    $java
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_JA');
            }
            if ($hta = Tools::getValue('hta')) {
                Configuration::updateValue(
                    'PRESTASPEED_HTA',
                    $hta
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_HTA');
            }
            if ($bo = Tools::getValue('bo')) {
                Configuration::updateValue(
                    'PRESTASPEED_BO',
                    $bo
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_BO');
            }
            if ($youtube = Tools::getValue('youtube')) {
                Configuration::updateValue(
                    'PRESTASPEED_YOUTUBE',
                    $youtube
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_YOUTUBE');
            }
            if ($facebook = Tools::getValue('facebook')) {
                Configuration::updateValue(
                    'PRESTASPEED_FACEBOOK',
                    $facebook
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_FACEBOOK');
            }
            if ($speedfacebook = Tools::getValue('speedfacebook')) {
                Configuration::updateValue(
                    'PRESTASPEED_SPEEDFACEBOOK',
                    $speedfacebook
                );
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('PRESTASPEED_SPEEDFACEBOOK');
            }
            /*if ($compress = Tools::getValue('compress'))
			Configuration::updateValue('PRESTASPEED_COMPRESS', $compress);
		elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP)
			Configuration::deleteFromContext('PRESTASPEED_COMPRESS');*/


//        if ($compress == 1)
//{
//$mask = '../themes/'._THEME_NAME_.'/cache/*.css';
//$cssFiles = glob($mask);
//$buffer = "";
//foreach ($cssFiles as $cssFile) {
//$buffer = file_get_contents($cssFile);
//$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
//$buffer = str_replace(': ', ':', $buffer);
//    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
//$my_file = $cssFile;
//$fh = fopen($my_file, 'w');
//fwrite($fh, $buffer);
//fclose($fh);
//}
///*js*/
//$mask2 = '../themes/'._THEME_NAME_.'/cache/*.js';
//$jsFiles = glob($mask2);
//$buffer = "";
//foreach ($jsFiles as $jsFile) {
//$buffer = file_get_contents($jsFile);
//$buffer = str_replace(': ', ':', $buffer);
//$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
//$my_file = $jsFile;
//$fh = fopen($my_file, 'w');
//fwrite($fh, $buffer);
//fclose($fh);
//}
//}
            if ($jsm == 1) {
                if ($this->isDomainAvailible('http://javascript-minifier.com')) {
                    $this->minIfin('js');
                    $output .= $this->displayConfirmation($this->l('All js minified'));
                } else {
                    $output .= $this->displayConfirmation($this->l('JS Service down'));
                }
            }
            if ($cssm == 1) {
                if ($this->isDomainAvailible('http://cssminifier.com')) {
                    $this->minIfin('css');
                    $output .= $this->displayConfirmation($this->l('All css minified'));
                } else {
                        $output .= $this->displayConfirmation($this->l('CSS Service down'));
                }
            }
            if ($bo == 1 && _PS_VERSION_ > '1.5.0.0') {
                $mask = '../config/xml/*.xml';
                $fil = glob($mask);
                foreach ($fil as $fi) {
                    @chmod(
                        $fi,
                        0777
                    );
                }
                $mask2 = '../config/xml/themes/*.xml';
                $fil2 = glob($mask2);
                foreach ($fil2 as $fi2) {
                    @chmod(
                        $fi2,
                        0777
                    );
                }
                //exec('find ../config/xml -type f -exec chmod 0777 {} +');
                //exec('find ../config/xml/.htaccess -type f -exec chmod 0644 {} +');
                //exec('find ../config/xml/themes -type f -exec chmod 0777 {} +');
                //exec('find ../config/xml/themes/.htaccess -type f -exec chmod 0644 {} +');
                if (file_exists('../.htaccess')) {
                    copy(
                        '../modules/prestaspeed/htaccess.txt',
                        '.htaccess'
                    );
                }
            }
            if ($bo == 0 && _PS_VERSION_ > '1.5.0.0') {
                $mask = '../config/xml/*.xml';
                $fil = glob($mask);
                foreach ($fil as $fi) {
                    @chmod(
                        $fi,
                        0644
                    );
                }
                $mask2 = '../config/xml/themes/*.xml';
                $fil2 = glob($mask2);
                foreach ($fil2 as $fi2) {
                    @chmod(
                        $fi2,
                        0644
                    );
                }
                //exec('find ../config/xml -type f -exec chmod 0644 {} +');
                //exec('find ../config/xml/themes -type f -exec chmod 0644 {} +');
                if (file_exists('.htaccess')) {
                    unlink('.htaccess');
                }
            }
            //db
            //rewrite htaccess
            if ($hta == 1) {
                $this->removeHtaccessSection();
                if (file_exists('../.htaccess') && is_writeable('../.htaccess')) {
                    if (file_exists('../.htaccessps')) {
                        //copy('../.htaccess', '../.htaccessps');
                        //copy('../.htaccess', '../.htaccessps');
                    } else {
                        copy(
                            '../.htaccess',
                            '../.htaccessps'
                        );
                    }
                    $file = Tools::file_get_contents('../.htaccess');
                    if (!preg_match(
                        '/Prestaspeed addon start/',
                        $file
                    )
                    ) {
                        $my_file = '../.htaccess';
                        $fh = fopen(
                            $my_file,
                            'a'
                        );
                        $string_data = '
#Prestaspeed addon start
<IfModule mod_mime.c>
    AddType text/css .css
    AddType application/x-javascript .js
    AddType text/x-component .htc
    AddType text/html .html .htm
    AddType text/richtext .rtf .rtx
    AddType image/svg+xml .svg .svgz
    AddType text/plain .txt
    AddType text/xsd .xsd
    AddType text/xsl .xsl
    AddType text/xml .xml
    AddType video/asf .asf .asx .wax .wmv .wmx
    AddType video/avi .avi
    AddType image/bmp .bmp
    AddType application/java .class
    AddType video/divx .divx
    AddType application/msword .doc .docx
    AddType application/vnd.ms-fontobject .eot
    AddType application/x-msdownload .exe
    AddType image/gif .gif
    AddType application/x-gzip .gz .gzip
    AddType image/x-icon .ico
    AddType image/jpeg .jpg .jpeg .jpe
    AddType application/vnd.ms-access .mdb
    AddType audio/midi .mid .midi
    AddType video/quicktime .mov .qt
    AddType audio/mpeg .mp3 .m4a
    AddType video/mp4 .mp4 .m4v
    AddType video/mpeg .mpeg .mpg .mpe
    AddType application/vnd.ms-project .mpp
    AddType application/x-font-otf .otf
    AddType application/vnd.oasis.opendocument.database .odb
    AddType application/vnd.oasis.opendocument.chart .odc
    AddType application/vnd.oasis.opendocument.formula .odf
    AddType application/vnd.oasis.opendocument.graphics .odg
    AddType application/vnd.oasis.opendocument.presentation .odp
    AddType application/vnd.oasis.opendocument.spreadsheet .ods
    AddType application/vnd.oasis.opendocument.text .odt
    AddType audio/ogg .ogg
    AddType application/pdf .pdf
    AddType image/png .png
    AddType application/vnd.ms-powerpoint .pot .pps .ppt .pptx
    AddType audio/x-realaudio .ra .ram
    AddType application/x-shockwave-flash .swf
    AddType application/x-tar .tar
    AddType image/tiff .tif .tiff
    AddType application/x-font-ttf .ttf .ttc
    AddType audio/wav .wav
    AddType audio/wma .wma
    AddType application/vnd.ms-write .wri
    AddType application/vnd.ms-excel .xla .xls .xlsx .xlt .xlw
    AddType application/zip .zip
</IfModule>

<IfModule mod_expires.c>
# Default directive

    ExpiresActive On
    ExpiresByType text/css A31536000
	ExpiresByType text/html "access plus 0 minutes"
    ExpiresByType application/x-javascript A31536000
    ExpiresByType text/x-component A31536000
    ExpiresByType text/richtext A3600
    ExpiresByType image/svg+xml A3600
    ExpiresByType text/plain A3600
    ExpiresByType text/xsd A3600
    ExpiresByType text/xsl A3600
    ExpiresByType video/asf A31536000
    ExpiresByType video/avi A31536000
    ExpiresByType image/bmp A31536000
    ExpiresByType application/java A31536000
    ExpiresByType video/divx A31536000
    ExpiresByType application/msword A31536000
    ExpiresByType application/vnd.ms-fontobject A31536000
    ExpiresByType application/x-msdownload A31536000
    ExpiresByType image/gif A31536000
	ExpiresByType image/jpg "access plus 1 month"
	ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType application/x-gzip A31536000
    ExpiresByType image/x-icon A31536000
    ExpiresByType application/vnd.ms-access A31536000
    ExpiresByType audio/midi A31536000
    ExpiresByType video/quicktime A31536000
    ExpiresByType audio/mpeg A31536000
    ExpiresByType video/mp4 A31536000
    ExpiresByType video/mpeg A31536000
    ExpiresByType application/vnd.ms-project A31536000
    ExpiresByType application/x-font-otf A31536000
    ExpiresByType application/vnd.oasis.opendocument.database A31536000
    ExpiresByType application/vnd.oasis.opendocument.chart A31536000
    ExpiresByType application/vnd.oasis.opendocument.formula A31536000
    ExpiresByType application/vnd.oasis.opendocument.graphics A31536000
    ExpiresByType application/vnd.oasis.opendocument.presentation A31536000
    ExpiresByType application/vnd.oasis.opendocument.spreadsheet A31536000
    ExpiresByType application/vnd.oasis.opendocument.text A31536000
    ExpiresByType audio/ogg A31536000
    ExpiresByType application/pdf A31536000
    ExpiresByType image/png A31536000
    ExpiresByType application/vnd.ms-powerpoint A31536000
    ExpiresByType audio/x-realaudio A31536000
    ExpiresByType image/svg+xml A31536000
    ExpiresByType application/x-shockwave-flash A31536000
    ExpiresByType application/x-tar A31536000
    ExpiresByType image/tiff A31536000
    ExpiresByType application/x-font-ttf A31536000
    ExpiresByType audio/wav A31536000
    ExpiresByType audio/wma A31536000
    ExpiresByType application/vnd.ms-write A31536000
    ExpiresByType application/vnd.ms-excel A31536000
    ExpiresByType application/zip A31536000
</IfModule>

<FilesMatch "\.(css|js)(\.gz)?$">
RewriteEngine On
RewriteCond %{HTTP:Accept-encoding} gzip
RewriteCond %{REQUEST_FILENAME}\.gz -s
RewriteRule ^(.*)\.(css|js) $1\.$2\.gz [QSA]
RewriteRule \.css\.gz$ - [T=text/css,E=no-gzip:1,E=FORCE_GZIP]
RewriteRule \.js\.gz$ - [T=text/javascript,E=no-gzip:1,E=FORCE_GZIP]
Header set Content-Encoding gzip env=FORCE_GZIP
</FilesMatch>

<FilesMatch "\.(ico|jpg|jpeg|png|gif|js|css|swf)$">
Header unset ETag
FileETag None
</FilesMatch>
<IfModule mod_deflate.c>
    #The following line is enough for .js and .css
    AddOutputFilter DEFLATE js css

    #The following line also enables compression by file content type, for the following list of Content-Type:s
    AddOutputFilterByType DEFLATE text/html text/plain text/xml application/xml

    #The following lines are to avoid bugs with some browsers
    BrowserMatch ^Mozilla/4 gzip-only-text/html
    BrowserMatch ^Mozilla/4\.0[678] no-gzip
    BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
    <IfModule mod_setenvif.c>
        BrowserMatch ^Mozilla/4 gzip-only-text/html
        BrowserMatch ^Mozilla/4\.0[678] no-gzip
        BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
        BrowserMatch \bMSI[E] !no-gzip !gzip-only-text/html
    </IfModule>
    <IfModule mod_headers.c>
        Header append Vary User-Agent env=!dont-vary
    </IfModule>
    <IfModule mod_filter.c>
        AddOutputFilterByType DEFLATE text/css application/x-javascript text/x-component text/html text/richtext image/svg+xml text/plain text/xsd text/xsl text/xml image/x-icon
    </IfModule>
</IfModule>
<IfModule mod_headers.c>
  <FilesMatch "\.(js|css|xml|gz)$">
    Header append Vary: Accept-Encoding
  </FilesMatch>
</IfModule>
<FilesMatch "\.(css|js|htc|CSS|JS|HTC)$">
    <IfModule mod_headers.c>
        Header set Pragma "public"
        Header append Cache-Control "public, must-revalidate, proxy-revalidate"
    </IfModule>
    FileETag MTime Size
    <IfModule mod_headers.c>
         Header set X-Powered-By "prestaspeed"
    </IfModule>
</FilesMatch>
<FilesMatch "\.(html|htm|rtf|rtx|svg|svgz|txt|xsd|xsl|xml|HTML|HTM|RTF|RTX|SVG|SVGZ|TXT|XSD|XSL|XML)$">
    <IfModule mod_headers.c>
        Header set Pragma "public"
        Header append Cache-Control "public, must-revalidate, proxy-revalidate"
    </IfModule>
    FileETag MTime Size
    <IfModule mod_headers.c>
         Header set X-Powered-By "prestaspeed"
    </IfModule>
</FilesMatch>
<FilesMatch "\.(asf|asx|wax|wmv|wmx|avi|bmp|class|divx|doc|docx|eot|exe|gif|gz|gzip|ico|jpg|jpeg|jpe|mdb|mid|midi|mov|qt|mp3|m4a|mp4|m4v|mpeg|mpg|mpe|mpp|otf|odb|odc|odf|odg|odp|ods|odt|ogg|pdf|png|pot|pps|ppt|pptx|ra|ram|svg|svgz|swf|tar|tif|tiff|ttf|ttc|wav|wma|wri|xla|xls|xlsx|xlt|xlw|zip|ASF|ASX|WAX|WMV|WMX|AVI|BMP|CLASS|DIVX|DOC|DOCX|EOT|EXE|GIF|GZ|GZIP|ICO|JPG|JPEG|JPE|MDB|MID|MIDI|MOV|QT|MP3|M4A|MP4|M4V|MPEG|MPG|MPE|MPP|OTF|ODB|ODC|ODF|ODG|ODP|ODS|ODT|OGG|PDF|PNG|POT|PPS|PPT|PPTX|RA|RAM|SVG|SVGZ|SWF|TAR|TIF|TIFF|TTF|TTC|WAV|WMA|WRI|XLA|XLS|XLSX|XLT|XLW|ZIP)$">
    <IfModule mod_headers.c>
        Header set Pragma "public"
        Header append Cache-Control "public, must-revalidate, proxy-revalidate"
    </IfModule>
    FileETag MTime Size
    <IfModule mod_headers.c>
         Header set X-Powered-By "prestaspeed"
    </IfModule>
</FilesMatch>
<IfModule mod_setenvif.c>
 <IfModule mod_headers.c>
    # mod_headers, y u no match by Content-Type?!
    <FilesMatch ".(gif|png|jpe?g|svg|svgz|ico|webp)$">
      SetEnvIf Origin ":" IS_CORS
      Header set Access-Control-Allow-Origin "*" env=IS_CORS
    </FilesMatch>
 </IfModule>
</IfModule>

<IfModule mod_headers.c>
  <FilesMatch ".(ttf|ttc|otf|eot|woff|font.css)$">
    Header set Access-Control-Allow-Origin "*"
  </FilesMatch>
</IfModule>
<IfModule mod_headers.c>
Header set Connection keep-alive
</IfModule>

<IfModule mod_deflate.c>
# compress text, html, javascript, css, xml:
AddOutputFilterByType DEFLATE text/plain
AddOutputFilterByType DEFLATE text/html
AddOutputFilterByType DEFLATE text/xml
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE application/xml
AddOutputFilterByType DEFLATE application/xhtml+xml
AddOutputFilterByType DEFLATE application/rss+xml
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-javascript
# Common Fonts
AddOutputFilterByType DEFLATE image/svg+xml
AddOutputFilterByType DEFLATE application/x-font-ttf
AddOutputFilterByType DEFLATE application/font-woff
AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
AddOutputFilterByType DEFLATE application/x-font-otf
</IfModule>
<ifModule mod_gzip.c>
mod_gzip_on Yes
mod_gzip_dechunk Yes
mod_gzip_item_include file .(html?|txt|css|js|php|pl)$
mod_gzip_item_include handler ^cgi-script$
mod_gzip_item_include mime ^text/.*
mod_gzip_item_include mime ^application/x-javascript.*
mod_gzip_item_exclude mime ^image/.*
mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
</ifModule>
#Prestaspeed addon end';
                        fwrite(
                            $fh,
                            $string_data
                        );
                        fclose($fh);
                    }
                    $output .= $this->displayConfirmation($this->l('file .htaccess optimized').'<br/>');
                } else {
                    $output .= $this->displayError($this->l('file .htaccess no exists (or check permissons)').'<br/>');
                }
            }
            if ($hta == 0) {
                $this->removeHtaccessSection();
                $output .= $this->displayConfirmation($this->l('file .htaccess optimization disabled').'<br/>');
            }
            /*smarty*/
            if ($tem == 1) {
                Configuration::updateValue(
                    'PS_SMARTY_CACHE',
                    1
                );
                if (_PS_VERSION_ > '1.5.0.0') {
                    Configuration::updateValue(
                        'PS_SMARTY_FORCE_COMPILE',
                        0
                    );
                } else {
                    Configuration::updateValue(
                        'PS_SMARTY_FORCE_COMPILE',
                        0
                    );
                }
                Configuration::updateValue(
                    'PS_CSS_THEME_CACHE',
                    1
                );
                Configuration::updateValue(
                    'PS_JS_THEME_CACHE',
                    1
                );
                Configuration::updateValue(
                    'PS_HTML_THEME_COMPRESSION',
                    1
                );
                $folder = '../themes/'._THEME_NAME_.'/cache/';
                if (false !== ($path = file_exists($folder))) {
                    $output .= $this->displayConfirmation($this->l('Cache theme folder exists').'');
                } else {
                    mkdir($folder);
                    $output .= $this->displayConfirmation($this->l('Cache theme folder created').'');
                }
// Continue do stuff
            }
            if ($gzip == 1) {
                if (_PS_VERSION_ > '1.5.0.0') {
                    Configuration::updateValue(
                        'PS_HTACCESS_CACHE_CONTROL',
                        1
                    );
                }
            } else {
                if (_PS_VERSION_ > '1.5.0.0') {
                    Configuration::updateValue(
                        'PS_HTACCESS_CACHE_CONTROL',
                        0
                    );
                }
            }
            /*java*/
            if ($gzip == 1) {
                if (_PS_VERSION_ > '1.6.0.0') {
                    Configuration::updateValue(
                        'PS_JS_DEFER',
                        1
                    );
                }
                if (_PS_VERSION_ > '1.4.0.0') {
                    Configuration::updateValue(
                        'PS_JS_HTML_THEME_COMPRESSION',
                        1
                    );
                }
            } else {
                if (_PS_VERSION_ > '1.6.0.0') {
                    Configuration::updateValue(
                        'PS_JS_DEFER',
                        0
                    );
                }
                if (_PS_VERSION_ > '1.4.0.0') {
                    Configuration::updateValue(
                        'PS_JS_HTML_THEME_COMPRESSION',
                        0
                    );
                }
            }
            /**/
            if ($tem == 0) {
                Configuration::updateValue(
                    'PS_SMARTY_CACHE',
                    0
                );
                if (_PS_VERSION_ > '1.5.0.0') {
                    Configuration::updateValue(
                        'PS_SMARTY_FORCE_COMPILE',
                        1
                    );
                } else {
                    Configuration::updateValue(
                        'PS_SMARTY_FORCE_COMPILE',
                        0
                    );
                }
                Configuration::updateValue(
                    'PS_CSS_THEME_CACHE',
                    0
                );
                Configuration::updateValue(
                    'PS_JS_THEME_CACHE',
                    0
                );
                Configuration::updateValue(
                    'PS_HTML_THEME_COMPRESSION',
                    0
                );
            }
            if ($cac == 1) {
                if (_PS_VERSION_ < '1.4.0.0') {
                    $mask = '../tools/smarty/compile/*tpl.php';
                    array_map(
                        'unlink',
                        glob($mask)
                    );
                }
                if (_PS_VERSION_ > '1.4.5.0' && _PS_VERSION_ < '1.5.0.0') {
                    Tools::clearCache($this->context->smarty);
                }
                if (_PS_VERSION_ > '1.5.0.0') {
                    if (_PS_VERSION_ > '1.5.5.0') {
                        Tools::clearSmartyCache();
                    }
                    Tools::clearCache($this->context->smarty);
                    if (_PS_VERSION_ > '1.6.0.0') {
                        Media::clearCache();
                    }
                }
                if (_PS_VERSION_ > '1.6.0.11') {
                    Db::getInstance()
                      ->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'smarty_cache`;');
                }
            }

            if ($cac == 1) {
                $output .= $this->displayConfirmation($this->l('Cache cleared').'<br/>');
            }
            if ($tem == 1) {
                $output .= $this->displayConfirmation($this->l('Template optimized').'<br/>');
            }
            if ($java == 1) {
                $output .= $this->displayConfirmation($this->l('Javascript optimized').'<br/>');
            }
            if ($gzip == 1) {
                $output .= $this->displayConfirmation($this->l('Gzip enabled').'<br/>');
            }
            if ($preload == 1) {
                $output .= $this->displayConfirmation($this->l('Preload CSS/JS enabled').'<br/>');
            }
            $server = _DB_SERVER_;
            $user = _DB_USER_;
            $pwd = _DB_PASSWD_;
            $db_name = _DB_NAME_;
            return $output;
        }
    }

    public function getConfigFieldsValues()
    {
        $fields_values = array(
            'sp_to2' => Tools::getValue(
                'sp_to2',
                Configuration::get('PRESTASPEED_TO2')
            ),
            'befo' => Tools::getValue(
                'befo',
                Configuration::get('PRESTASPEED_BEFO')
            ),
            'sp_from2' => Tools::getValue(
                'sp_from2',
                Configuration::get('PRESTASPEED_FR2')
            ),
            'sp_to' => Tools::getValue(
                'sp_to',
                Configuration::get('PRESTASPEED_TO')
            ),
            'java' => Tools::getValue(
                'java',
                Configuration::get('PRESTASPEED_JA')
            ),
            'ord' => Tools::getValue(
                'ord',
                Configuration::get('PRESTASPEED_ORD')
            ),
            'sp_from' => Tools::getValue(
                'sp_from',
                Configuration::get('PRESTASPEED_FR')
            ),
            'gzip' => Tools::getValue(
                'gzip',
                Configuration::get('PRESTASPEED_GZ')
            ),
            //'compress'    => Tools::getValue('compress', Configuration::get('PRESTASPEED_COMPRESS')),
            'tem' => Tools::getValue(
                'tem',
                Configuration::get('PRESTASPEED_TE')
            ),
            'speed' => Tools::getValue(
                'speed',
                Configuration::get('PRESTASPEED_SPEED')
            ),
            'func' => Tools::getValue(
                'func',
                Configuration::get('PRESTASPEED_FUNC')
            ),
            'cac' => Tools::getValue(
                'cac',
                Configuration::get('PRESTASPEED_CAC')
            ),
            'cav' => Tools::getValue(
                'cav',
                Configuration::get('PRESTASPEED_CAV')
            ),
            'car' => Tools::getValue(
                'car',
                Configuration::get('PRESTASPEED_CA')
            ),
            'dat' => Tools::getValue(
                'dat',
                Configuration::get('PRESTASPEED_DA')
            ),
            'dis' => Tools::getValue(
                'dis',
                Configuration::get('PRESTASPEED_DI')
            ),
            'pag' => Tools::getValue(
                'pag',
                Configuration::get('PRESTASPEED_PA')
            ),
            'batcht' => Tools::getValue(
                'batcht',
                Configuration::get('PRESTASPEED_BATCHT')
            ),
            'gues' => Tools::getValue(
                'gues',
                Configuration::get('PRESTASPEED_GU')
            ),
            'pnf' => Tools::getValue(
                'pnf',
                Configuration::get('PRESTASPEED_PNF')
            ),
            'conn' => Tools::getValue(
                'conn',
                Configuration::get('PRESTASPEED_CO')
            ),
            'hta' => Tools::getValue(
                'hta',
                Configuration::get('PRESTASPEED_HTA')
            ),
            'bo' => Tools::getValue(
                'bo',
                Configuration::get('PRESTASPEED_BO')
            ),
            'smush' => Tools::getValue(
                'smush',
                Configuration::get('PRESTASPEED_SMUSH')
            ),
            'cleani' => Tools::getValue(
                'cleani',
                Configuration::get('PRESTASPEED_CLEANI')
            ),
            'smush2' => Tools::getValue(
                'smush2',
                Configuration::get('PRESTASPEED_SMUSH2')
            ),
            'type' => Tools::getValue(
                'type',
                Configuration::get('PRESTASPEED_TYPE')
            ),
            'stats' => Tools::getValue(
                'stats',
                Configuration::get('PRESTASPEED_STATS')
            ),
            'cusi' => Tools::getValue(
                'cusi',
                Configuration::get('PRESTASPEED_CUSI')
            ),
            'cusi' => Tools::getValue(
                'cusi',
                Configuration::get('PRESTASPEED_CUSI')
            ),
            'cusi' => Tools::getValue(
                'cusi',
                Configuration::get('PRESTASPEED_CUSI')
            ),
            'cusi' => Tools::getValue(
                'cusi',
                Configuration::get('PRESTASPEED_CUSI')
            ),
            'cssm' => Tools::getValue(
                'cssm',
                Configuration::get('PRESTASPEED_CSSM')
            ),
            'jsm' => Tools::getValue(
                'jsm',
                Configuration::get('PRESTASPEED_JSM')
            ),
            'cusi' => Tools::getValue(
                'cusi',
                Configuration::get('PRESTASPEED_CUSI')
            ),
            'infi' => Tools::getValue(
                'infi',
                Configuration::get('PRESTASPEED_INFI')
            ),
            'isitem' => Tools::getValue(
                'isitem',
                Configuration::get('PRESTASPEED_ITEM_SELECTOR')
            ),
            'iscontent' => Tools::getValue(
                'iscontent',
                Configuration::get('PRESTASPEED_CONTENT_SELECTOR')
            ),
            'isnav' => Tools::getValue(
                'isnav',
                Configuration::get('PRESTASPEED_NAV_SELECTOR')
            ),
            'isnext' => Tools::getValue(
                'isnext',
                Configuration::get('PRESTASPEED_NEXT_SELECTOR')
            ),
            'ovalid' => Tools::getValue(
                'ovalid',
                Configuration::get('PRESTASPEED_OVALID')
            ),
            'cusmes' => Tools::getValue(
                'cusmes',
                Configuration::get('PRESTASPEED_CUSMES')
            ),
            'cusmesdate' => Tools::getValue(
                'cusmesdate',
                Configuration::get('PRESTASPEED_CUSMESDATE')
            ),
            'youtube' => Tools::getValue(
                'youtube',
                Configuration::get('PRESTASPEED_YOUTUBE')
            ),
            'facebook' => Tools::getValue(
                'facebook',
                Configuration::get('PRESTASPEED_FACEBOOK')
            ),
            'speedfacebook' => Tools::getValue(
                'speedfacebook',
                Configuration::get('PRESTASPEED_SPEEDFACEBOOK')
            ),
            'preload' => Tools::getValue(
                'preload',
                Configuration::get('PRESTASPEED_PRELOAD')
            ),
        );
        return $fields_values;
    }

    public function getConfigFieldsValues2()
    {
        $fields_values2 = array(

            'cusi2' => Tools::getValue(
                'cusi2',
                Configuration::get('PRESTASPEED_CUSI2')
            ),
        );

        return $fields_values2;
    }
    public function getConfigFieldsValues3()
    {
        $fields_values = array(
            'logf' => Tools::getValue(
                'logf',
                Configuration::get('PRESTASPEED_LOGF')
            ),
        );

        return $fields_values;
    }
    public function renderFormO()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Site optimization'),
                    'icon' => 'icon-cogs'
                ),
                'description' => $this->l('If you get a timeout when minify CSS/JS, just Press F5 to continue, or compress first CSS and later JS.If you have errors when minify CSS / JS files, use this URL to restore the original files: ')._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-minify.php?token='.Tools::substr(Tools::encrypt('prestaspeed/minify'), 0, 10).'&id_shop='.$this->context->shop->id.'<br/>'.$this->l('If you get a white screen in front office when optimize .htaccess, use this cron to restore it: ')._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-hta.php?token='.Tools::substr(Tools::encrypt('prestaspeed/hta'), 0, 10).'&id_shop='.$this->context->shop->id,

                'input' => array(
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clear cache:'),
                        'name' => 'cac',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Clear all smarty cache files'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Optimize template:'),
                        'name' => 'tem',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Enable the best settings to speed up the template loading'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    /*array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Minify all CSS:'),
                        'name' => 'cssm',
                        'is_bool' => true,
                        'desc' => $this->l('Compress all css files in themes and modules folder(this create a backup of all files with the extension .cssunmin)'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),*/
                    /*array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Minify all JS:'),
                        'name' => 'jsm',
                        'is_bool' => true,
                        'desc' => $this->l('Compress all javasctript files in themes and modules folder(this create a backup of all files with the extension .jsunmin)'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),*/
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Optimize javascript:'),
                        'name' => 'java',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Enable best javascript settings to load JS files more efficient'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Preload CSS/JS:'),
                        'name' => 'preload',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Preload the CSS/JS before you click in a link, optimize template and javascript must be enabled to use this option. Disable this option if you template use a loading feature (only for PS  1.7)'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Enable Gzip:'),
                        'name' => 'gzip',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Activate the gzip compression for faster execution. This option maybe not work in all servers (only PS > 1.5)'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    /*array(
						'type'    => 'switch',
						'label'   => $this->l('Compression of css'),
						'name'    => 'compress',
						'is_bool' => true,
						'desc'    => $this->l('Compress all css cache files of your theme'),
						'values'  => array(
							array(
								'id'    => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id'    => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),*/
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Youtube optimization:'),
                        'name' => 'youtube',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'This option optimizes the youtube embed videos in your site and save almost 1MB. You must replace your embed youtube videos with <iframe width="560" height="315" src="https://www.youtube.com/embed/mCJCbvoPaaA?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>  to <div class="youtube-player" data-id="mCJCbvoPaaA"></div>  the data-id must be the video ID'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Facebook followbox optimization:'),
                        'name' => 'facebook',
                        'desc' => $this->l(
                            'Enter your Facebook fanpage URL to optimize the Facebook block (you must disable the prestashop facebook block). The module can be in home or columns'
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Enable block Facebook on footer?'),
                        'name' => 'speedfacebook',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Show the Facebook block on footer'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Htaccess optimization:'),
                        'name' => 'hta',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Enable the apache htaccess optimization. This option only works if you server have a .htaccess file created. If you enable this option and the site dont work (some servers dont support all optimizations), simply delete your .htaccess file in your root directory and rename the .htaccessps to .htaccess to restore your original file'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Back office optimization'),
                        'name' => 'bo',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Try to optimize the back office performance with a htaccess file in the admin folder (delete thsi file if you get an error). The expiration of the cache is for 1 day, if you modify admin css/js files or images, you can turn off this option to see the changes'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Enable infinite scroll'),
                        'name' => 'infi',
                        'class' => 't',
                        'desc' => $this->l(
                            'Configure in Preferences -> Products the total products to show to 12. If you use Layered navigation block, please check the readme to make it work.'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Infinite scroll next selector'),
                        'name' => 'isnext',
                        'desc' => $this->l('The css next page selector (usually: #pagination_next > a)'),

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Infinite scroll item selector'),
                        'name' => 'isitem',
                        'desc' => $this->l('The css item selector (usually: .product_list > li)'),

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Infinite scroll content selector'),
                        'name' => 'iscontent',
                        'desc' => $this->l('The css content selector (usually: .product_list)'),

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Infinite scroll nav selector'),
                        'name' => 'isnav',
                        'desc' => $this->l('The css nav selector (usually: .bottom-pagination-content, .top-pagination-content)'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDeleteO';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function renderForm()
    {
        $idbs = '';
        $dbs = Configuration::get('PRESTASPEED_DBS');
        $idbs = Configuration::get('PRESTASPEED_IBS');
        $sql = Db::getInstance()
                 ->executeS(
                     'SELECT table_schema \''._DB_NAME_.'\', SUM( data_length + index_length) / 1024 / 1024 \'db_size_in_mb\' FROM information_schema.TABLES WHERE table_schema=\''._DB_NAME_.'\' GROUP BY table_schema ;'
                 );
        $data = $sql;
        if (is_array($data)) {
            Configuration::updateValue(
                'PRESTASPEED_DBS',
                $data[0]['db_size_in_mb']
            );
        } else {
            Configuration::updateValue(
                'PRESTASPEED_DBS',
                $data['db_size_in_mb']
            );
        }
        $dbs = Configuration::get('PRESTASPEED_DBS');
        if (Configuration::get('PRESTASPEED_IBS') == '' || Configuration::get('PRESTASPEED_IBS') == null) {
            if (is_array($data)) {
                Configuration::updateValue(
                    'PRESTASPEED_IBS',
                    $data[0]['db_size_in_mb']
                );
            } else {
                Configuration::updateValue(
                    'PRESTASPEED_IBS',
                    $data['db_size_in_mb']
                );
            }
        } else {
            $idbs = Configuration::get('PRESTASPEED_IBS');
        }
        if ($idbs == 0 or $idbs == null) {
            $idbs = $dbs;
        }
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Database optimization'),
                    'icon' => 'icon-cogs'
                ),
                'description' => $this->l('Database Size: ').round($dbs, 2).'MB'.' - '.$this->l('Initial database size before module install: ').round($idbs, 2).'MB',
                'input' => array(
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean connections:'),
                        'name' => 'conn',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Delete all fields on connection tables. These tables store information about how long a user keeps on the site, where user come from, etc'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean Page not found:'),
                        'name' => 'pnf',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l(
                            'Delete all fields on page not found table. This tables store information about 404 error pages'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'date',
                        'size' => 32,
                        'label' => $this->l('Delete connection data Before'),
                        'name' => 'befo',
                        'desc' => $this->l(
                            'Set a date to delete all connections and pagenotfound data before that date. If you get a white screen, start from old dates, and increase to new ones in each optimization.'
                        ),

                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean guests data:'),
                        'name' => 'gues',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Delete all fields on guest table. This table have data from visitors, like OS, browser, etc'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean customer messages:'),
                        'name' => 'cusmes',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l(
                            'Clean all customer messages before the selected date'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'date',
                        'size' => 32,
                        'label' => $this->l('Delete customer messages before'),
                        'name' => 'cusmesdate',
                        'desc' => $this->l(
                            'Set a date to delete all customer messages  data before that date. If you get a white screen, start from old dates, and increase to new ones in each optimization. Leave empty to delete all'
                        ),

                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean viewed page:'),
                        'name' => 'pag',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l(
                            'Delete all fields on page table. This table store the viewed pages from users.'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean expired discounts:'),
                        'name' => 'dis',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'This option cleans all the data on expired vouchers, discounts, and product discounts tables'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean abandoned carts:'),
                        'name' => 'car',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Delete all abandoned carts (non registered users). The users with products in cart, need to redo the orders.'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Delete abandoned carts from users too?'),
                        'name' => 'cav',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Delete all abandoned carts for register users in selected date.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'date',
                        'size' => 32,
                        'label' => $this->l('From'),
                        'name' => 'sp_from',
                    ),
                    array(
                        'type' => 'date',
                        'size' => 32,
                        'label' => $this->l('To'),
                        'name' => 'sp_to',

                    ),
                    /*delete valid carts*/
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Delete orders:'),
                        'name' => 'ord',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Delete validated carts. This action cant be undone and you lost the orders between the date selected. This option is great to delete really old orders.'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Delete only valid orders?'),
                        'name' => 'ovalid',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Set enabled to delete valid (payment accepted, delivered, etc) or disable for invalid orders (calceled, etc)'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Valid orders')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Invalid orders')
                            )
                        ),
                    ),
                    array(
                        'type' => 'date',
                        'size' => 32,
                        'label' => $this->l('From'),
                        'name' => 'sp_from2',
                    ),
                    array(
                        'type' => 'date',
                        'size' => 32,
                        'label' => $this->l('To'),
                        'name' => 'sp_to2',

                    ),
                    /*end deletion*/
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Optimize database:'),
                        'name' => 'dat',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Optimize all database tables and repair errors.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Check database integrity:'),
                        'name' => 'func',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Improve queries and solve problems'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDelete';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }
    public function renderForm2()
    {
        $datatot = Configuration::get('PRESTASPEED_TOTCOMP') + Configuration::get('PRESTASPEED_TOTCOMPF');
        $options = array(
            array(
                'id_option' => '../img/p/',
                'name' => $this->l('Product images'),
            ),
            array(
                'id_option' => '../img/c/',
                'name' => $this->l('Category images'),
            ),
            array(
                'id_option' => '../img/m/',
                'name' => $this->l('Manufacturer images'),
            ),
            array(
                'id_option' => '../img/admin/',
                'name' => $this->l('Admin images'),
            ),
            array(
                'id_option' => '../img/cms/',
                'name' => $this->l('CMS images'),
            ),
            array(
                'id_option' => '../img/l/',
                'name' => $this->l('Language images'),
            ),
            array(
                'id_option' => '../img/su/',
                'name' => $this->l('Supplier images'),
            ),
            array(
                'id_option' => '../img/scenes/',
                'name' => $this->l('Scenes images'),
            ),
            array(
                'id_option' => '../img/st/',
                'name' => $this->l('Stores images'),
            ),
            array(
                'id_option' => '../themes/',
                // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Template images'),
                // The value of the text content of the  <option> tag.
            ),
            array(
                'id_option' => '../modules/',
                // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Modules images'),
                // The value of the text content of the  <option> tag.
            ),
            array(
                'id_option' => '../upload/',
                // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Upload folder images'),
                // The value of the text content of the  <option> tag.
            ),
        );
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Optimize images'),
                    'icon' => 'icon-image'
                ),
                'description' => $this->l('In this section, select the options to perform the image optimization (image type, clean temp images, etc.) and after save, run the optimization in the above window. The image optimization can take several minutes, just wait until process finish (you will see the results in the window).').'<br/><br/>'.$this->l('Total KB optimized in images: ').round($datatot, 2).'<br/><br/><br/>'.$this->l('If the optimization process fail and you get empty images, you can restore the images with this url:')._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-restore.php?token='.Tools::substr(Tools::encrypt('prestaspeed/restore'), 0, 10).'&id_shop='.$this->context->shop->id.'<br/><br/>'.$this->l('PrestaSpeed generates a backup of every image optimized with the -old extension.').'<br/>'.$this->l('Use this cron url to delete backup images in the img folder, or click on the link to execute now: ')._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-remove.php?token='.Tools::substr(Tools::encrypt('prestaspeed/remove'), 0, 10).'&id_shop='.$this->context->shop->id.'<br/><br/>',
                'input' => array(
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean image stats:'),
                        'name' => 'stats',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'This option clean all the stats for the images in the database. If you compress the images again, you need to wait more time to process all again. Clean all if you only have regenerated your images.'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Optimize new images:'),
                        'name' => 'smush',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'This option optimize new images for products when you upload it, but your uploads take more time. Watermark module must be disabled. This option dont work always, but you can regenerate the images of products'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Optimize all images:'),
                        'name' => 'smush2',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'If you enable this option, the module compress all selected images with Smushit service. This can take time depending of your amount of images. All proceced files must be the same as total files. If you dont get the same number, repeat the process until you get it.'
                        ),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Type of images'),
                        'name' => 'type',
                        'desc' => $this->l(
                            'Select the type of images to optimize. The modules/themes option optimize all themes and modules images to get a better performance'
                        ),
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Total images to optimize in a batch'),
                        'name' => 'batcht',
                        'desc' => $this->l(
                            'Prestaspeed try to process x amount of files at same time  intil process all files. You can try with less quantity is your server is slow.)'
                        ),

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Optimize only one image'),
                        'name' => 'cusi',
                        'desc' => $this->l(
                            'Set the url of the image to optimize (like http://www.site.com/img/test.jpg, set the url to ../img/test.jpg), leave blank if you want to optimize by type of image '
                        ),

                    ),
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Clean all temp images:'),
                        'name' => 'cleani',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Delete all images on TMP img folder to save space in the server hard disk'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),


                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => $this->l('Save'),
                )


            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) : 0;
        $this->fields_form2 = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDelete2';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    /*new image system*/
    public function renderForm3()
    {
        $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NOT NULL, `smushed` TINYINT(1) NOT NULL,`saved` varchar(255) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
        Db::getInstance()
          ->Execute($query);
        $bytes = 'SELECT sum(saved) as total FROM '._DB_PREFIX_.'smush WHERE saved > 0';
        $bytt = Db::getInstance()
                  ->executeS($bytes);
        Configuration::updateValue(
            'PRESTASPEED_TOTCOMP',
            ($bytt[0]['total'] * 1) / 1024
        );
        $datatot = ($bytt[0]['total']) * 1 / 1024;
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Optimize images with cron'),
                    'icon' => 'icon-image'
                ),
                'description' => $this->l('Total KB optimized in images: ').round($datatot, 2).'<br/>'.$this->l('Use this cron url to process images in the selected folder in a cron job, or click on the link to execute now: ')._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-img.php?token='.Tools::substr(Tools::encrypt('prestaspeed/img'), 0, 10).'&id_shop='.$this->context->shop->id.'<br/><br/>'.$this->l('PrestaSpeed generates a backup of every image optimized with the -old extension.').'<br/>'.$this->l('Use this cron url to delete backup images in the img folder, or click on the link to execute now: ')._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-remove.php?token='.Tools::substr(Tools::encrypt('prestaspeed/remove'), 0, 10).'&id_shop='.$this->context->shop->id.'<br/><br/>'.$this->l('If the optimization process fail and you get empty images, you can restore the images with this url:')._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-restore.php?token='.Tools::substr(Tools::encrypt('prestaspeed/restore'), 0, 10).'&id_shop='.$this->context->shop->id,
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Optimize a entire folder'),
                        'name' => 'cusi2',
                        'desc' => $this->l('Set a path to the images like img/p/ or modules/'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) : 0;
        $this->fields_form2 = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDelete3';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues2(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }
    public function renderForm4()
    {
        @$bytes = disk_free_space(".");
        $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
        $base = 1024;
        $class = min((int)log($bytes, $base), count($si_prefix) - 1);
        $free = sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class] . '<br />';
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Free disk Space / Log files'),
                    'icon' => 'icon-trash'
                ),
                'description' => $this->l('Total of Free disk Space: ').$free.'<br/>'.$this->l('Log files can increase the size with the time. If you have a lot of errors in the site, the log files can be 1GB of size or more. With this tool you can check the file sizes and delete. We recomend to solve the issues openning the log files and contact a developer').'<br/>'.$this->l('Use this cron URLto see the logs files or delete if the delete option is enabled: ')._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-log.php?token='.Tools::substr(Tools::encrypt('prestaspeed/log'), 0, 10).'&id_shop='.$this->context->shop->id,
                    'input' => array(
                    array(
                        'type' =>(_PS_VERSION_ > '1.6.0.0' ? 'switch' : 'radio'),
                        'label' => $this->l('Delete all log files:'),
                        'name' => 'logf',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Delete all log files when open the cron URL'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                 ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) : 0;
        $this->fields_form2 = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDelete4';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues3(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }
    /**/
    private function _displayInfo()
    {
        $this->context->smarty->assign(
            array(
                'prestaspeed_cron' => _PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-cron.php?token='.Tools::substr(Tools::encrypt('prestaspeed/cron'), 0, 10).'&id_shop='.$this->context->shop->id,
            )
        );
        return $this->display(
            __FILE__,
            'views/templates/hook/infos.tpl'
        );
    }
    private function _displayInfo3()
    {
        $this->context->smarty->assign(
            array(
                'psversion' => _PS_VERSION_,
            )
        );
        return $this->display(
            __FILE__,
            'views/templates/hook/infos3.tpl'
        );
    }
    private function _displayInfo2()
    {
        $ch = curl_init();
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
        curl_setopt($ch, CURLOPT_URL, 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__);
        $s = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__;
        $o = json_decode(Tools::file_get_contents('https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url='.$s.'&strategy=desktop&key=AIzaSyD-3OKXz9-SFlzGqXYhPDRBOh56bQrqNBM&screenshot=true'));
/*$data = ($o->screenshot->data);
$im = imagecreatefromstring( $data );
ob_start();
imagepng($im);
$png = ob_get_clean();
$uri = "data:image/png;base64," . base64_encode($png);*/
//echo "<img src=" . $uri /* URI goes here */ . "alt=\"the image\" />";
        Configuration::updateValue('PRESTASPEED_LOAD', (isset($o->ruleGroups->SPEED->score) ? $o->ruleGroups->SPEED->score : ''));
        $resources = (isset($o->pageStats->numberResources) ? $o->pageStats->numberResources : '');
        $response = (isset($o->formattedResults->ruleResults->MainResourceServerResponseTime->urlBlocks[0]->header->args[0]->value) ? $o->formattedResults->ruleResults->MainResourceServerResponseTime->urlBlocks[0]->header->args[0]->value : '');
        $responseimpact = (isset($o->formattedResults->ruleResults->MainResourceServerResponseTime->ruleImpact) ? $o->formattedResults->ruleResults->MainResourceServerResponseTime->ruleImpact : '');


        //var_dump($response = $o->formattedResults->ruleResults->MainResourceServerResponseTime->urlBlocks[0]->header->args[0]->value);

//var_dump( $o->ruleGroups->SPEED);
//var_dump( $o->pageStats->numberResources);
//var_dump( $o); //URL of the optimized picture
//URL of the optimized picture
        $loadtime = Configuration::get('PRESTASPEED_LOAD');
        $percent = '';
        if ($loadtime > 0 && $loadtime <= 10) {
            $percent = '10';
        }
        if ($loadtime > 11 && $loadtime <= 20) {
            $percent = '20';
        }
        if ($loadtime > 21 && $loadtime <= 30) {
            $percent = '0';
        }
        if ($loadtime > 31 && $loadtime <= 40) {
            $percent = '40';
        }
        if ($loadtime > 41 && $loadtime <= 50) {
            $percent = '50';
        }
        if ($loadtime > 51 && $loadtime <= 60) {
            $percent = '60';
        }
        if ($loadtime > 61 && $loadtime <= 70) {
            $percent = '70';
        }
        if ($loadtime > 71 && $loadtime <= 80) {
            $percent = '80';
        }
        if ($loadtime > 81 && $loadtime <= 90) {
            $percent = '90';
        }
        if ($loadtime > 91 && $loadtime <= 100) {
            $percent = '100';
        }
        $this->context->smarty->assign(
            array(
                'loadtime' => $loadtime,
                'resources' => $resources,
                'responseimpact' => $responseimpact,
                'response' => $response,
                'percent' => $percent,
            )
        );
        return $this->display(
            __FILE__,
            'views/templates/hook/infos2.tpl'
        );
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (get_class($this->context->controller) == 'AdminDashboardController') {
            $this->context->controller->addCSS(
                $this->_path.'views/css/style.css',
                'all'
            );
        }
        if ((Tools::getValue('controller') == 'AdminModules') && (Tools::getValue('configure') == $this->name)) {
            $this->context->controller->addJS(($this->_path).'views/js/ajax.js');
        }
    }

    public function hookDashboardZoneOne($params)
    {
        $total = '';
        $total2 = '';
        $total3 = '';
        $total4 = '';
        $total5 = '';
        $total6 = '';
        $total7 = '';
        $total8 = '';
        $total9 = '';
        $total10 = '';
        $total11 = '';
        $total4c = '';
        $tottot = '';
        $tmp = '';
        $dbs = Configuration::get('PRESTASPEED_DBS');
        $befo = Configuration::get('PRESTASPEED_BEFO');
        if ($befo == null) {
            $befo =date("Y-m-d H:i:s");
        }
        $sql = Db::getInstance()
                 ->executeS(
                     'SELECT table_schema \''._DB_NAME_.'\', SUM( data_length + index_length) / 1024 / 1024 \'db_size_in_mb\' FROM information_schema.TABLES WHERE table_schema=\''._DB_NAME_.'\' GROUP BY table_schema ;'
                 );
        $data = $sql;
        if (is_array($data)) {
            $valda = $data[0]['db_size_in_mb'];
        } else {
            $valda = $data['db_size_in_mb'];
        }
        $sorgudc = Db::getInstance(_PS_USE_SQL_SLAVE_)
                     ->executeS(
                         'SELECT * FROM `'._DB_PREFIX_.'connections` WHERE `date_add` < \''.pSQL($befo).'\'
         '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').' LIMIT 6000;'
                     );
        if ($sorgudc === false) {
            $tmp = '';
        } else {
            $veridc = $sorgudc;
            @$total = Db::getInstance(_PS_USE_SQL_SLAVE_)
                        ->NumRows($sorgudc);
        }
        $sorgudc2 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                      ->executeS('SELECT * FROM `'._DB_PREFIX_.'connections_page` LIMIT 6000;');
        if ($sorgudc2 === false) {
            $tmp = '';
        } else {
            $veridc2 = $sorgudc2;
            @$total2 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                         ->NumRows($sorgudc2);
        }
        $sorgudc3 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                      ->executeS(
                          'SELECT * FROM `'._DB_PREFIX_.'connections_source` WHERE `date_add` < \''.pSQL(
                              $befo
                          ).'\' LIMIT 6000;'
                      );
        if ($sorgudc3 === false) {
            $tmp = '';
        } else {
            $veridc3 = $sorgudc3;
            @$total3 = Db::getInstance()
                         ->NumRows($sorgudc3);
        }
        $sorgudc7 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                      ->executeS(
                          'SELECT g.*, c.*  FROM `'._DB_PREFIX_.'guest` g LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest WHERE c.`date_add` < \''.pSQL(
                              $befo
                          ).'\' LIMIT 6000;'
                      );
        if ($sorgudc7 === false) {
            $tmp = '';
        } else {
            $veridc7 = $sorgudc7;
            $total7 = Db::getInstance()
                        ->NumRows($veridc7);
        }

        if (Module::isInstalled('pagesnotfound')) {
            $sorgudcpnf = Db::getInstance(_PS_USE_SQL_SLAVE_)
                          ->executeS(
                              'SELECT *  FROM `'._DB_PREFIX_.'pagenotfound` WHERE `date_add` < \''.pSQL(
                                  $befo
                              ).'\' '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').' LIMIT 6000'
                          );
            if ($sorgudcpnf === false) {
                $tmp = '';
            } else {
                $veridcpnf = $sorgudcpnf;
                $totalpnf = Db::getInstance()
                        ->NumRows($sorgudcpnf);
            }
        } else {
            $totalpnf = 0;
        }
        $sorgudc8 = Db::getInstance()
                      ->executeS(
                          'SELECT * FROM `'._DB_PREFIX_.'page_viewed`
         '.((_PS_VERSION_ > '1.5.0.0') ? ' WHERE `id_shop` = '.(int)$this->context->shop->id : '').' LIMIT 6000;'
                      );
        if ($sorgudc8 === false) {
            $tmp = '';
        } else {
            $veridc8 = $sorgudc8;
            $total8 = Db::getInstance()
                        ->NumRows($sorgudc8);
        }
        /*cart*/
        $sorgudc4 = Db::getInstance()
                      ->executeS('SELECT * FROM `'._DB_PREFIX_.'cart` WHERE `id_customer` = 0 LIMIT 6000;');
        if ($sorgudc4 === false) {
            $tmp = '';
        } else {
            $veridc4 = $sorgudc4;
            $total4 = Db::getInstance()
                        ->NumRows($sorgudc4);
        }
        $sorgudc4c = Db::getInstance()
                       ->executeS('SELECT * FROM `'._DB_PREFIX_.'cart` WHERE `id_customer` = 0 LIMIT 6000;');
        if ($sorgudc4c === false) {
            $tmp = '';
        } else {
            $veridc4c = $sorgudc4c;
            $total4c = Db::getInstance()
                         ->NumRows($sorgudc4c);
        }
        $sorgudc4mes = Db::getInstance()
                       ->executeS('SELECT * FROM `'._DB_PREFIX_.'customer_thread` LIMIT 6000;');
        if ($sorgudc4mes === false) {
            $tmp = '';
        } else {
            $veridc4mes = $sorgudc4mes;
            $total4mes = Db::getInstance()
                         ->NumRows($sorgudc4mes);
        }
        if (_PS_VERSION_ < '1.5.0.0') {
            $sorgudc6 = Db::getInstance()
                          ->executeS('SELECT * FROM `'._DB_PREFIX_.'discount`;');
            if ($sorgudc6 === false) {
                $tmp = '';
            } else {
                $veridc6 = $sorgudc6;
                $total6 = Db::getInstance()
                            ->NumRows($sorgudc6);
            }
            $current_date = date('Y-m-d H:i:s');
            $sorgudc5 = Db::getInstance()
                          ->executeS(
                              'SELECT * FROM `'._DB_PREFIX_.'discount` WHERE `date_to` < \''.pSQL($current_date).'\';'
                          );
            if ($sorgudc5 === false) {
                $tmp = '';
            } else {
                $veridc5 = $sorgudc5;
                $total5 = Db::getInstance()
                            ->NumRows($sorgudc5);
            }
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            $current_date = date('Y-m-d H:i:s');
            $sorgudc9 = Db::getInstance()
                          ->executeS(
                              'SELECT * FROM `'._DB_PREFIX_.'cart_rule` WHERE `date_to` < \''.pSQL(
                                  $current_date
                              ).'\' AND `date_to` != \'0000-00-00 00:00:00\';'
                          );
            if ($sorgudc9 === false) {
                $tmp = '';
            } else {
                $veridc9 = $sorgudc9;
                $total9 = Db::getInstance()
                            ->NumRows($sorgudc9);
            }

            $current_date = date('Y-m-d H:i:s');
            $sorgudc10 = Db::getInstance()
                           ->executeS(
                               'SELECT * FROM `'._DB_PREFIX_.'specific_price_rule` WHERE `to` < \''.pSQL(
                                   $current_date
                               ).'\' AND `to` != \'0000-00-00 00:00:00\';'
                           );
            if ($sorgudc10 === false) {
                $tmp = '';
            } else {
                $veridc10 = $sorgudc10;
                $total10 = Db::getInstance()
                             ->NumRows($sorgudc10);
            }
            $current_date = date('Y-m-d H:i:s');
            $sorgudc11 = Db::getInstance()
                           ->executeS(
                               'SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE `to` < \''.pSQL(
                                   $current_date
                               ).'\' AND `to` != \'0000-00-00 00:00:00\';'
                           );
            if ($sorgudc11 === false) {
                $tmp = '';
            } else {
                $veridc11 = $sorgudc11;
                $total11 = Db::getInstance()
                             ->NumRows($sorgudc11);
            }
        }
        $totc = (float)$total + (float)$total2 + (float)$total3;
        $tottot = (float)$total11 + (float)$total9 + (float)$total10 + (float)$total5;
        $linkpmo = Context::getContext()->link->getAdminLink('AdminModules', true).'&configure=prestaspeed&module_name=prestaspeed&tab_module=administration';
        $this->context->smarty->assign(
            array(
                'psversion' => _PS_VERSION_,
                'conn' => ($totc > 18000 ? $totc.'+' : $totc),
                'totsav' => round(
                    Configuration::get('PRESTASPEED_TOTCOMP'),
                    2
                ),
                'guest' => $total7,
                'pnf' => $totalpnf,
                'pages' => $total8,
                'cartsa' => $total4,
                'disc' => $tottot,
                'mes' => $total4mes,
                'linkpmo' => $linkpmo,
                'data' => round($dbs, 2).' MB',
            )
        );
        return $this->display(
            __FILE__,
            'views/templates/front/prestaspeed-dash.tpl'
        );
    }

    public function hookdisplayAdminHomeQuickLinks($params)
    {
        $linkpmo = Context::getContext()->link->getAdminLink('AdminModules', true).'&configure=prestaspeed&module_name=prestaspeed&tab_module=administration';
        $this->context->smarty->assign(
            'linkpmo',
            $linkpmo
        );
        return $this->display(
            __FILE__,
            'views/templates/front/prestaspeed.tpl'
        );
    }

    public function backOfficeHome($params)
    {
        $linkpmo = Context::getContext()->link->getAdminLink('AdminModules', true).'&configure=prestaspeed&module_name=prestaspeed&tab_module=administration';
        $this->context->smarty->assign('linkpmo', $linkpmo);
        return $this->display(
            __FILE__,
            'views/templates/front/prestaspeed.tpl'
        );
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }


    public function getContent()
    {
        /*delete all*/
        if (_PS_VERSION_ < '1.5.0.0') {
        } else {
            return $this->postProcess().$this->_displayInfo2().$this->renderForm().$this->renderFormO(
            ).$this->renderForm2().$this->_displayInfo3().$this->renderForm3().$this->renderForm4().$this->_displayInfo();
        }
    }
    public function remove()
    {
        $files = $this->globRecursive('../../img/*.*-old', 0); // get all file names
        foreach ($files as $file) {
            //var_dump( $file).'<br/>';
            unlink($file); // delete file
            echo 'Backup file deleted:'.basename($file).'</br>';
        }
        echo "All img backup files deleted";
    }
    public function restore()
    {
        $files = $this->globRecursive('../../img/*.*-old', 0); // get all file names
        foreach ($files as $file) {
            copy($file, str_replace('-old', '', $file));
            echo 'Backup file restored:'.basename($file).'</br>';
        }
        $files = $this->globRecursive('../../themes/*.*-old', 0); // get all file names
        foreach ($files as $file) {
            copy($file, str_replace('-old', '', $file));
            echo 'Backup file restored:'.basename($file).'</br>';
        }
        $files = $this->globRecursive('../../modules/*.*-old', 0); // get all file names
        foreach ($files as $file) {
            copy($file, str_replace('-old', '', $file));
            echo 'Backup file restored:'.basename($file).'</br>';
        }
        echo "All img files restored";
    }
    public function log()
    {
        $files = $this->globRecursive('../error_log', 0);
        foreach ($files as $file) {
            echo 'Log File in modules folder: '.$file.' - File Size:'.filesize($file).' bytes </br>';
            if (Configuration::get('PRESTASPEED_LOGF') == 1) {
                unlink($file); // delete file
                echo 'Log file deleted</br>';
            }
        }
        $files2 = glob('../../error_log', 0); // get all file names
        foreach ($files2 as $file) {
            echo 'Log File in root folder: '.$file.' - File Size:'.filesize($file).' bytes </br>';
            if (Configuration::get('PRESTASPEED_LOGF') == 1) {
                unlink($file); // delete file
                echo 'Log file deleted</br>';
            }
        }
        if (Configuration::get('PRESTASPEED_LOGF') == 1) {
        }
        $files3 = $this->globRecursive('../classes/error_log', 0); // get all file names
        foreach ($files3 as $file) {
            echo 'Log File in classes folder: '.$file.' - File Size:'.filesize($file).' bytes </br>';
            if (Configuration::get('PRESTASPEED_LOGF') == 1) {
                unlink($file); // delete file
                echo 'Log file deleted</br>';
            }
        }
        if (Configuration::get('PRESTASPEED_LOGF') == 1) {
        }
        $files4 = $this->globRecursive('../classes/error_log', 0); // get all file names
        foreach ($files4 as $file) {
            echo 'Log File in controllers folder: '.$file.' - File Size:'.filesize($file).' bytes </br>';
            if (Configuration::get('PRESTASPEED_LOGF') == 1) {
                unlink($file); // delete file
                echo 'Log file deleted</br>';
            }
        }
        if (Configuration::get('PRESTASPEED_LOGF') == 1) {
            echo '<br/>All log files deleted';
        }
    }
    // Does not support flag GLOB_BRACE
    public function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->globRecursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
    /*cron img*/
    public function img()
    {
        include_once('smusher.php');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
            $sec = "https";
        } else {
            $sec = "http";
        }
        if (Configuration::get('PRESTASPEED_CUSI2') != null) {
            echo '
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
			<iframe id="ifra" width="100%"></iframe>  
	<script type="text/javascript">
		$("#ifra").attr("src", "'.$sec.'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/prestaspeed/ajax2.php?type='.Configuration::get('PRESTASPEED_CUSI2').'")
		</script>';
        /*
            $cusi2 = Configuration::get('PRESTASPEED_CUSI2');
            define('BASEPATH', _PS_ROOT_DIR_.'/'.$cusi2); // TODO: CAMBIAR ESTO POR TU PATH ORIGINAL
            define('BASEURL', 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.$cusi2); // TODO: Y ESTO POR TU URL
            define('MIN_TIME', 1); // uTime, modificado hace mínimo una fase lunar (aprox. 29 dias)
            define('ORIGINAL_POSTFIX', '_orig');
            $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NOT NULL, `smushed` TINYINT(1) NOT NULL,`saved` varchar(255) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
            Db::getInstance()
              ->Execute($query);
            $smusher = new Smush();
            $smusher->it(BASEPATH, $cusi2);
            $bytes = 'SELECT sum(saved) as total FROM '._DB_PREFIX_.'smush WHERE saved > 0;';
            $bytt = Db::getInstance()
                      ->executeS($bytes);
            Configuration::updateValue(
                'PRESTASPEED_TOTCOMP',
                ($bytt[0]['total'] * 1) / 1024
            );
        }
        if ($this->img) {
            die();
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            Tools::redirect(
                'location: ./index.php?tab=AdminModules&configure=prestaspeed&token='.Tools::getAdminTokenLite(
                    'AdminModules'
                ).'&tab_module='.$this->tab.'&module_name=prestaspeed&validation'
            );
        } else {
            Tools::redirect($_SERVER['HTTP_REFERER'].'');
        }*/
        }
        die();
    }

    /*end*/
    /*cron purposes*/
    public function minify()
    {
        $files = $this->rglob('{*.cssunmin}', '../../themes/'._THEME_NAME_, GLOB_BRACE);
        $files5 = $this->rglob('{*.jsunmin}', '../../themes/'._THEME_NAME_, GLOB_BRACE);
        $files3 = $this->rglob('{*.jsunmin}', '../../modules/', GLOB_BRACE);
        $files6 = $this->rglob('{*.cssunmin}', '../../modules/', GLOB_BRACE);
        $files4 = $this->rglob('{*.jsunmin}', '../../js/', GLOB_BRACE);
        $files2 = array_merge($files, $files4, $files5, $files6, $files3);
        foreach ($files2 as $file) {
                $sourcePath = $file;
                copy($file, str_replace('unmin', '', $file));
                @unlink($file);
                echo 'File '.$file.' restored<br/>';
        }
        echo 'End <br/>';
    }
    public function cron()
    {
        /*delete all*/
        $total = '';
        $total2 = '';
        $total3 = '';
        $total4 = '';
        $total5 = '';
        $total6 = '';
        $total7 = '';
        $total8 = '';
        $total9 = '';
        $total10 = '';
        $total11 = '';
        $total4c = '';
        $conn = Configuration::get('PRESTASPEED_CO');
        $func = Configuration::get('PRESTASPEED_FUNC');
        $totalpnf = null;
        $gues = Configuration::get('PRESTASPEED_GU');
        $cusmes = Configuration::get('PRESTASPEED_CUSMES');
        $cusmesdate = Configuration::get('PRESTASPEED_CUSMESDATE');
        $pag = Configuration::get('PRESTASPEED_PA');
        $dis = Configuration::get('PRESTASPEED_DI');
        $car = Configuration::get('PRESTASPEED_CA');
        $pnf = Configuration::get('PRESTASPEED_PNF');
        $dat = Configuration::get('PRESTASPEED_DA');
        $cav = Configuration::get('PRESTASPEED_CAV');
        $cac = Configuration::get('PRESTASPEED_CAC');
        $tem = Configuration::get('PRESTASPEED_TE');
        $java = Configuration::get('PRESTASPEED_JA');
        $gzip = Configuration::get('PRESTASPEED_GZ');
        $sp_from = Configuration::get('PRESTASPEED_FR');
        $befo = Configuration::get('PRESTASPEED_BEFO');
        $sp_to = Configuration::get('PRESTASPEED_TO');
        $cleani = Configuration::get('PRESTASPEED_CLEANI');
        if ($cleani == 1) {
            $this->clearTmpDir();
        }
        $tmp = '';
        if ($befo == null) {
            $befo = date("Y-m-d H:i:s");
        }
        if ($cusmesdate == null) {
            $cusmesdate = date("Y-m-d H:i:s");
        }
//    $this->backup_tables(_DB_SERVER_,_DB_USER_,_DB_PASSWD_,_DB_NAME_);
        /*smarty*/
        $files = $this->globRecursive('../error_log', 0); // get all file names
        foreach ($files as $file) {
            echo 'Log File in modules folder: '.$file.' - File Size:'.filesize($file).' bytes </br>';
            if (Configuration::get('PRESTASPEED_LOGF') == 1) {
                unlink($file); // delete file
                echo 'Log file deleted</br>';
            }
        }
        $files2 = glob('../../error_log', 0); // get all file names
        foreach ($files2 as $file) {
            echo 'Log File in root folder: '.$file.' - File Size:'.filesize($file).' bytes </br>';
            if (Configuration::get('PRESTASPEED_LOGF') == 1) {
                unlink($file); // delete file
                echo 'Log file deleted</br>';
            }
        }
        if (Configuration::get('PRESTASPEED_LOGF') == 1) {
        }
        $files3 = $this->globRecursive('../classes/error_log', 0); // get all file names
        foreach ($files3 as $file) {
            echo 'Log File in classes folder: '.$file.' - File Size:'.filesize($file).' bytes </br>';
            if (Configuration::get('PRESTASPEED_LOGF') == 1) {
                unlink($file); // delete file
                echo 'Log file deleted</br>';
            }
        }
        if (Configuration::get('PRESTASPEED_LOGF') == 1) {
        }
        $files4 = $this->globRecursive('../classes/error_log', 0); // get all file names
        foreach ($files4 as $file) {
            echo 'Log File in controllers folder: '.$file.' - File Size:'.filesize($file).' bytes </br>';
            if (Configuration::get('PRESTASPEED_LOGF') == 1) {
                unlink($file); // delete file
                echo 'Log file deleted</br>';
            }
        }
        if (Configuration::get('PRESTASPEED_LOGF') == 1) {
            echo '<br/>All log files deleted';
        }
        if ($conn == 1) {
            $sorgudc = Db::getInstance(_PS_USE_SQL_SLAVE_)
                         ->executeS(
                             'SELECT * FROM `'._DB_PREFIX_.'connections` WHERE `date_add` < \''.pSQL($befo).'\'
         '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').';'
                         );
            if ($sorgudc === false) {
                $tmp = '';
            } else {
                $veridc = $sorgudc;
                $total = Db::getInstance(_PS_USE_SQL_SLAVE_)
                           ->NumRows($sorgudc);
            }
            $sorgudc2 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                          ->executeS('SELECT * FROM `'._DB_PREFIX_.'connections_page`;');
            if ($sorgudc2 === false) {
                $tmp = '';
            } else {
                $veridc2 = $sorgudc2;
                $total2 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                            ->NumRows($sorgudc2);
            }

            $sorgudc3 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                          ->executeS(
                              'SELECT * FROM `'._DB_PREFIX_.'connections_source` WHERE `date_add` < \''.pSQL(
                                  $befo
                              ).'\';'
                          );
            if ($sorgudc3 === false) {
                $tmp = '';
            } else {
                $veridc3 = $sorgudc3;
                $total3 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                            ->NumRows($sorgudc3);
            }
        }
        if ($gues == 1) {
            $sorgudc7 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                          ->executeS(
                              'SELECT g.*, c.*  FROM `'._DB_PREFIX_.'guest` g LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest WHERE c.`date_add` < \''.pSQL(
                                  $befo
                              ).'\' LIMIT 10000;'
                          );
            if ($sorgudc7 === false) {
                $tmp = '';
            } else {
                $veridc7 = $sorgudc7;
                $total7 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                            ->NumRows($sorgudc7);
            }
        }
        if ($cusmes == 1) {
            $sorgudc7mes = Db::getInstance(_PS_USE_SQL_SLAVE_)
                          ->executeS(
                              'SELECT *  FROM `'._DB_PREFIX_.'customer_thread` WHERE `date_add` < \''.pSQL(
                                  $cusmesdate
                              ).'\' LIMIT 9000;'
                          );
            if ($sorgudc7mes === false) {
                $tmp = '';
                $total7mes = '';
            } else {
                $veridc7mes = $sorgudc7mes;
                $total7mes = Db::getInstance(_PS_USE_SQL_SLAVE_)
                            ->NumRows($sorgudc7mes);
            }
        }
        if ($pnf == 1 && Module::isInstalled('pagesnotfound')) {
            $sorgudcpnf = Db::getInstance(_PS_USE_SQL_SLAVE_)
                          ->executeS(
                              'SELECT * FROM `'._DB_PREFIX_.'pagenotfound`  WHERE `date_add` < \''.pSQL(
                                  $befo
                              ).'\' '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').';'
                          );
            if ($sorgudcpnf === false) {
                $tmp = '';
            } else {
                $veridcpnf = $sorgudcpnf;
                $totalpnf = Db::getInstance(_PS_USE_SQL_SLAVE_)
                            ->NumRows($sorgudcpnf);
            }
        }
        if ($pag == 1) {
            $sorgudc8 = Db::getInstance(_PS_USE_SQL_SLAVE_)
                          ->executeS(
                              'SELECT * FROM `'._DB_PREFIX_.'page_viewed`
         '.((_PS_VERSION_ > '1.5.0.0') ? ' WHERE `id_shop` = '.$this->context->shop->id : '').';'
                          );
            if ($sorgudc8 === false) {
                $tmp = '';
            } else {
                $veridc8 = $sorgudc8;
                $total8 = Db::getInstance()
                            ->NumRows($sorgudc8);
            }
        }
        /*cart*/
        if ($car == 1) {
            if ($sp_from == 0 && $sp_to == 0) {
                $sorgudc4 = Db::getInstance()
                              ->executeS(
                                  'SELECT * FROM `'._DB_PREFIX_.'cart` WHERE `id_customer` = 0'.($cav == 1 ? ' OR `id_customer` != 0' : '').';'
                              );
                if ($sorgudc4 === false) {
                    $tmp = '';
                } else {
                    $veridc4 = $sorgudc4;
                    $total4 = Db::getInstance()
                                ->NumRows($sorgudc4);
                }
            }
            if ($sp_from != 0 && $sp_to != 0) {
                $sorgudc4 = Db::getInstance()
                              ->executeS(
                                  'SELECT * FROM `'._DB_PREFIX_.'cart`
                        WHERE `id_customer` = 0'.($cav == 1 ? ' OR `id_customer` != 0' : '').'
                        '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').'
                        AND `date_upd` BETWEEN \''.pSQL($sp_from).'\' AND \''.pSQL($sp_to).'\';'
                              );
                if ($sorgudc4 === false) {
                    $tmp = '';
                } else {
                    $veridc4 = $sorgudc4;
                    $total4 = Db::getInstance()
                                ->NumRows($sorgudc4);
                }
            }
            if ($sp_from == 0 && $sp_to == 0) {
                $sorgudc4c = Db::getInstance()
                               ->executeS(
                                   'SELECT * FROM `'._DB_PREFIX_.'cart` WHERE `id_customer` = 0'.($cav == 1 ? ' OR `id_customer` != 0' : '').';'
                               );
                if ($sorgudc4c === false) {
                    $tmp = '';
                } else {
                    $veridc4c = $sorgudc4c;
                    $total4c = Db::getInstance()
                                 ->NumRows($sorgudc4c);
                }
            }
            if ($sp_from != 0 && $sp_to != 0) {
                $sorgudc4c = Db::getInstance()
                               ->executeS(
                                   'SELECT * FROM `'._DB_PREFIX_.'cart`
        WHERE `id_customer` = 0'.($cav == 1 ? ' OR `id_customer` != 0' : '').'
        '.((_PS_VERSION_ > '1.5.0.0') ? ' AND `id_shop` = '.(int)$this->context->shop->id : '').'
        AND `date_upd` BETWEEN \''.pSQL($sp_from).'\' AND \''.pSQL($sp_to).'\';'
                               );
                if ($sorgudc4c === false) {
                    $tmp = '';
                } else {
                    $veridc4c = $sorgudc4c;
                    $total4c = Db::getInstance()
                                 ->NumRows($sorgudc4c);
                }
            }
        }
        if ($func == 1) {
            if (_PS_VERSION_ > '1.5.0.0') {
                $this->checkAndFix();
            }
        }
        if ($dis == 1) {
            if (_PS_VERSION_ < '1.5.0.0') {
                $sorgudc6 = Db::getInstance()
                              ->executeS('SELECT * FROM `'._DB_PREFIX_.'discount`;');
                if ($sorgudc6 === false) {
                    $tmp = '';
                } else {
                    $veridc6 = $sorgudc6;
                    $total6 = Db::getInstance()
                                ->NumRows($sorgudc6);
                }
                $current_date = date('Y-m-d H:i:s');
                $sorgudc5 = Db::getInstance()
                              ->executeS(
                                  'SELECT * FROM `'._DB_PREFIX_.'discount` WHERE `date_to` < \''.pSQL(
                                      $current_date
                                  ).'\';'
                              );
                if ($sorgudc5 === false) {
                    $tmp = '';
                } else {
                    $veridc5 = $sorgudc5;
                    $total5 = Db::getInstance()
                                ->NumRows($sorgudc5);
                }
            }
            if (_PS_VERSION_ > '1.5.0.0') {
                $current_date = date('Y-m-d H:i:s');
                $sorgudc9 = Db::getInstance()
                              ->executeS(
                                  'SELECT * FROM `'._DB_PREFIX_.'cart_rule` WHERE `date_to` < \''.pSQL(
                                      $current_date
                                  ).'\' AND `date_to` != \'0000-00-00 00:00:00\';'
                              );
                if ($sorgudc9 === false) {
                    $tmp = '';
                } else {
                    $veridc9 = $sorgudc9;
                    $total9 = Db::getInstance()
                                ->NumRows($sorgudc9);
                }
                /**/
                $current_date = date('Y-m-d H:i:s');
                $sorgudc10 = Db::getInstance()
                               ->executeS(
                                   'SELECT * FROM `'._DB_PREFIX_.'specific_price_rule` WHERE `to` < \''.pSQL(
                                       $current_date
                                   ).'\' AND `to` != \'0000-00-00 00:00:00\';'
                               );
                if ($sorgudc10 === false) {
                    $tmp = '';
                } else {
                    $veridc10 = $sorgudc10;
                    $total10 = Db::getInstance()
                                 ->NumRows($sorgudc10);
                }
                $current_date = date('Y-m-d H:i:s');
                $sorgudc11 = Db::getInstance()
                               ->executeS(
                                   'SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE `to` < \''.pSQL(
                                       $current_date
                                   ).'\' AND `to` != \'0000-00-00 00:00:00\';'
                               );
                if ($sorgudc11 === false) {
                    $tmp = '';
                } else {
                    $veridc11 = $sorgudc11;
                    $total11 = Db::getInstance()
                                 ->NumRows($sorgudc11);
                }
            }
        }
        if ($conn == 1) {
            if ($total != null) {
                foreach ($sorgudc as $veridc) {
                    $idconn = $veridc['id_connections'];
                    if ($conn == 1) {
                        $this->deleteconn($idconn);
                    }
                } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
            }
            if ($total2 != null) {
                foreach ($sorgudc2 as $veridc2) {
                    $idconn2 = $veridc2['id_connections'];
                    if ($conn == 1) {
                        $this->deleteconn($idconn2);
                    }
                } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
            }
            if ($total3 != null) {
                foreach ($sorgudc3 as $veridc3) {
                    $idconn3 = $veridc3['id_connections'];
                    if ($conn == 1) {
                        $this->deleteconn($idconn3);
                    }
                } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
            }
            if ($befo == date("Y-m-d H:i:s")) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'connections`');
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'connections_source`');
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'connections_page`');
            }
        }
        if ($pnf == 1) {
            if ($totalpnf != null) {
                foreach ($sorgudcpnf as $veridcpnf) {
                    $idconn = $veridc['id_pagenotfound'];
                    if ($pnf == 1) {
                        $this->deleteconn($idconn);
                    }
                } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
            }
            if ($befo == date("Y-m-d H:i:s")) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'pagenotfound`');
            }
        }
        if ($cusmes == 1) {
            if ($total7mes  != null) {
                foreach ($veridc7mes as $veridcmes) {
                    $idmes = $veridcmes['id_customer_thread'];
                    if ($cusmes == 1) {
                        $this->deletemes($idmes);
                    }
                    Db::getInstance()
                    ->Execute('DELETE FROM `'._DB_PREFIX_.'customer_thread` WHERE `id_customer_thread` = '.pSql($idmes).';');
                }
            }
            /*if ($befo == date("Y-m-d H:i:s")) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer_messages`');
            }*/
        }
        $modsperf = @Db::getInstance()->ExecuteS('SHOW TABLES LIKE \''._DB_PREFIX_.'modules_perfs\'');
        if ($modsperf) {
            @Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'modules_perfs`;');
        }
        if ($gues == 1) {
            Db::getInstance()
              ->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'guest`;');
        }
        if ($pag == 1) {
            Db::getInstance()
              ->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'page_viewed`;');
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            if ($total11 != null) {
                foreach ($sorgudc11 as $veridc11) {
                    $idc11 = $veridc11['id_specific_price'];
                    if ($dis == 1) {
                        $this->delete11($idc11);
                    }
                } //while ($veridc11 = mysql_fetch_assoc($sorgudc11));
            }
            if ($total10 != null) {
                foreach ($sorgudc10 as $veridc10) {
                    $idc10 = $veridc10['id_specific_price_rule'];
                    if ($dis == 1) {
                        $this->delete10($idc10);
                    }
                } //while ($veridc10 = mysql_fetch_assoc($sorgudc10));
            }
            /**/
            if (_PS_VERSION_ > '1.5.0.0') {
                if ($total9 != null) {
                    foreach ($sorgudc9 as $veridc9) {
                        $idc9 = $veridc9['id_cart_rule'];
                        if ($dis == 1) {
                            $this->delete9($idc9);
                        }
                    } //while ($veridc9 = mysql_fetch_assoc($sorgudc9));
                }
            }
        }
        /**/
        if ($total5 != null) {
            foreach ($sorgudc5 as $veridc5) {
                $idc5 = $veridc5['id_discount'];
                if ($dis == 1) {
                    $this->delete5($idc5);
                }
            } //while ($veridc5 = mysql_fetch_assoc($sorgudc5));
        }
        if ($total4 != null) {
            foreach ($sorgudc4 as $veridc4) {
                if ($car == 1) {
                    $idc = $veridc4['id_cart'];
                }
                $this->delete($idc);
            } //while ($veridc4 = mysql_fetch_assoc($sorgudc4));
        }
        if ($total4c != null) {
            foreach ($sorgudc4c as $veridc4c) {
                $idcc = $veridc4c['id_cart'];
                if ($car == 1) {
                    $this->deletec($idcc);
                }
            } //while ($veridc4c = mysql_fetch_assoc($sorgudc4c));
        }
        $server = _DB_SERVER_;
        $user = _DB_USER_;
        $pwd = _DB_PASSWD_;
        $db_name = _DB_NAME_;
        if ($dat == 1) {
            $alltables = Db::getInstance()
                           ->executeS('SHOW TABLES FROM `'._DB_NAME_.'`');
            $v = '';
            foreach ($alltables as $tablename) {
                $tb = current($tablename);
                //var_dump($table_lang);
                Db::getInstance()
                  ->Execute('OPTIMIZE TABLE `'.$tb.'`');
                Db::getInstance()
                  ->Execute('REPAIR TABLE `'.$tb.'`');
                $v .= $tb.'</br>';
            }
// Alert that operation was successful
        }
        $sql = Db::getInstance()
                 ->Execute(
                     "SELECT table_schema '"._DB_NAME_."', SUM( data_length + index_length) / 1024 / 1024 'db_size_in_mb' FROM information_schema.TABLES WHERE table_schema='"._DB_NAME_."' GROUP BY table_schema ;"
                 );
        $data = $sql;
        if (is_array($data)) {
            Configuration::updateValue(
                'PRESTASPEED_DBS',
                $data[0]['db_size_in_mb']
            );
        } else {
            Configuration::updateValue(
                'PRESTASPEED_DBS',
                $data['db_size_in_mb']
            );
        }
        if ($this->cron) {
            die();
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            Tools::redirect(
                'location: ./index.php?tab=AdminModules&configure=prestaspeed&token='.Tools::getAdminTokenLite(
                    'AdminModules'
                ).'&tab_module='.$this->tab.'&module_name=prestaspeed&validation'
            );
        } else {
            Tools::redirect($_SERVER['HTTP_REFERER'].'');
        }
        die();
    }

    /*end cron*/
    public function delete11($idc11)
    {
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_specific_price` = '.(int)$idc11.';');
    }

    public function delete10($idc10)
    {
        Db::getInstance()
          ->Execute(
              'DELETE FROM `'._DB_PREFIX_.'specific_price_rule` WHERE `id_specific_price_rule` = '.(int)$idc10.';'
          );
        Db::getInstance()
          ->Execute(
              'DELETE FROM `'._DB_PREFIX_.'specific_price_rule_condition_group` WHERE `id_specific_price_rule` = '.(int)$idc10.';'
          );
    }
    public function deletepnf($idcconn)
    {
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'pagenotfound` WHERE `id_pagenotfound` = '.(int)$idcconn.';');
    }
    public function deletemes($idcconn)
    {
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'customer_message` WHERE `id_customer_thread` = '.(int)$idcconn.';');
    }
    public function deleteconn($idcconn)
    {
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'connections` WHERE `id_connections` = '.(int)$idcconn.';');
        Db::getInstance()
          ->Execute(
              'DELETE FROM `'._DB_PREFIX_.'connections_page` WHERE `id_connections` = '.(int)$idcconn.';'
          );
        Db::getInstance()
          ->Execute(
              'DELETE FROM `'._DB_PREFIX_.'connections_source` WHERE `id_connections` = '.(int)$idcconn.';'
          );
    }

    public function delete9($idc9)
    {
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule` WHERE `id_cart_rule` = '.(int)$idc9.';');
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$idc9.';');
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$idc9.';');
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$idc9.';');
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_lang` WHERE `id_cart_rule` = '.(int)$idc9.';');
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE `id_cart_rule` = '.(int)$idc9.';');
    }

    public function delete5($idc5)
    {
        if (_PS_VERSION_ < '1.5.0.0') {
            Db::getInstance()
              ->Execute('DELETE FROM `'._DB_PREFIX_.'discount_category` WHERE `id_discount` = '.(int)$idc5.';');
            Db::getInstance()
              ->Execute('DELETE FROM `'._DB_PREFIX_.'discount_lang` WHERE `id_discount` = '.(int)$idc5.';');
            Db::getInstance()
              ->Execute('DELETE FROM `'._DB_PREFIX_.'discount` WHERE `id_discount` = '.(int)$idc5.';');
        }
    }

    public function deletec($idcc)
    {
        if ($this->orderExists($idcc)) {
            return false;
        }
        if (_PS_VERSION_ < '1.5.0.0') {
            Db::getInstance()
              ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_discount` WHERE `id_cart` = '.(int)$idcc.';');
        }
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$idcc.';');
    }

    public function delete($idc)
    {
        if ($this->orderExists($idc)) {
            return false;
        }

        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart` WHERE `id_cart` = '.(int)$idc.';');
        // --------------  must NOT delete a cart which is associated with an order!
        // --------------  unlink uploaded files associated with any customized product in the cart (similar to deletePictureToProduct() method )
        $uploadedFiles = Db::getInstance()
                           ->executeS(
                               '
        SELECT cd.`value`
        FROM `'._DB_PREFIX_.'customized_data` cd
        INNER JOIN `'._DB_PREFIX_.'customization` c ON (cd.`id_customization`= c.`id_customization`)
        WHERE cd.`type`= 0 AND c.`id_cart`='.(int)$idc.';'
                           );
        foreach ($uploadedFiles as $mustUnlink) {
            unlink(_PS_UPLOAD_DIR_.$mustUnlink['value'].'_small');
            unlink(_PS_UPLOAD_DIR_.$mustUnlink['value']);
        }
        Db::getInstance()
          ->Execute(
              'DELETE FROM `'._DB_PREFIX_.'customized_data` WHERE `id_customization` IN (
         SELECT `id_customization` FROM `'._DB_PREFIX_.'customization` WHERE `id_cart`='.(int)$idc.');'
          );
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE `id_cart`='.(int)$idc.';');
        Db::getInstance()
          ->Execute(
              'DELETE FROM `'._DB_PREFIX_.'message_readed` WHERE `id_message` IN (
         SELECT `id_message` FROM `'._DB_PREFIX_.'message` WHERE `id_cart`='.(int)$idc.');'
          );
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'message` WHERE `id_cart`='.(int)$idc.';');
        if (_PS_VERSION_ < '1.5.0.0') {
            Db::getInstance()
              ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_discount` WHERE `id_cart` = '.(int)$idc.';');
        }
        Db::getInstance()
          ->Execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$idc.';');
        //    ignore    TABLES  fianet_fraud  and prestafraud_carts
        // ------- FINALLY  (no multilingual aspect, so avoid calling parent -- just delete it from here)
    }

    public function tableExist($table)
    {
        $con = '';
        $sql = 'show tables like \''.$table.'\'';
        $res = $con->query($sql);
        return ($res->num_rows > 0);
    }

    public function dbSize()
    {
        $sql = Db::getInstance()
                 ->Execute(
                     'SELECT table_schema \''._DB_NAME_.'\', SUM( data_length + index_length) / 1024 / 1024 \'db_size_in_mb\' FROM information_schema.TABLES WHERE table_schema=\''._DB_NAME_.'\' GROUP BY table_schema ;'
                 );
        $data = $sql;
        $data[0]['db_size_in_mb'];
    }

    public function orderExists($idc)
    {
        return (bool)Db::getInstance()
                       ->getValue('SELECT `id_cart` FROM `'._DB_PREFIX_.'orders` WHERE `id_cart` = '.(int)$idc.';');
    }

    public function psversion()
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(
            '.',
            $version
        );
        return $exp[1];
    }

    /*delete orders*/
    public function deleteorderbyid(
        $id,
        $return = 0
    ) {
        $psversion = $this->psversion();

        if ($psversion == 5 || $psversion == 6) {
            @$thisorder = Db::getInstance(_PS_USE_SQL_SLAVE_)
                           ->executeS('SELECT id_cart FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)$id).';';
            if (isset($thisorder[0])) {
                //deleting order_return
                $q = 'DELETE a,b FROM '._DB_PREFIX_.'order_return AS a LEFT JOIN '._DB_PREFIX_.'order_return_detail AS b ON a.id_order_return = b.id_order_return WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                //deleting order_slip
                $q = 'DELETE a,b FROM '._DB_PREFIX_.'order_slip AS a LEFT JOIN '._DB_PREFIX_.'order_slip_detail AS b ON a.id_order_slip = b.id_order_slip WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                @$q = 'DELETE FROM '._DB_PREFIX_.'cart_product WHERE id_cart="'.(int)$thisorder[0]['id_cart'].'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'order_history WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'order_detail WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'orders WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
            }
        }

        if ($psversion == 3) {
            $thisorder = Db::getInstance()
                           ->executeS('SELECT id_cart FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)$id).';';
            if (isset($thisorder[0])) {
                $q = 'DELETE a,b FROM '._DB_PREFIX_.'order_return AS a LEFT JOIN '._DB_PREFIX_.'order_return_detail AS b ON a.id_order_return = b.id_order_return WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE a,b FROM '._DB_PREFIX_.'order_slip AS a LEFT JOIN '._DB_PREFIX_.'order_slip_detail AS b ON a.id_order_slip = b.id_order_slip WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'cart_discount WHERE id_cart="'.(int)$thisorder[0]['id_cart'].'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'cart_product WHERE id_cart="'.(int)$thisorder[0]['id_cart'].'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'order_history WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'order_discount WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }

                $q = 'DELETE FROM '._DB_PREFIX_.'order_detail WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'orders WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
            }
        }

        if ($psversion == 4) {
            $thisorder = Db::getInstance(_PS_USE_SQL_SLAVE_)
                           ->executeS('SELECT id_cart FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)$id).';';
            if (isset($thisorder[0])) {
                $q = 'DELETE a,b FROM '._DB_PREFIX_.'order_return AS a LEFT JOIN '._DB_PREFIX_.'order_return_detail AS b ON a.id_order_return = b.id_order_return WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE a,b FROM '._DB_PREFIX_.'order_slip AS a LEFT JOIN '._DB_PREFIX_.'order_slip_detail AS b ON a.id_order_slip = b.id_order_slip WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'cart_discount WHERE id_cart="'.(int)$thisorder['id_cart'].'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }

                $q = 'DELETE FROM '._DB_PREFIX_.'cart_product WHERE id_cart="'.(int)$thisorder['id_cart'].'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'order_history WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'order_discount WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }

                $q = 'DELETE FROM '._DB_PREFIX_.'order_detail WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
                $q = 'DELETE FROM '._DB_PREFIX_.'orders WHERE id_order="'.(int)$id.'";';
                if (!Db::getInstance()
                       ->Execute($q)
                ) {
                    $this->errorlog[] = $this->l('ERROR');
                }
            }
        }
    }

    /* Retrocompatibility image*/
    public function hookwatermark($params)
    {
        $this->hookActionWatermark($params);
    }
    public function htmlpath($relative_path)
    {
        $realpath=realpath($relative_path);
        $htmlpath=str_replace($_SERVER['DOCUMENT_ROOT'], '', $realpath);
        return $htmlpath;
    }
    public function hookActionWatermark($params)
    {
        include_once('smushit.inc.php');
        $images_list = array();
        $image = new Image($params['id_image']);
        $image->id_product = $params['id_product'];
        $file = _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.jpg';
        array_push($images_list, $file);
        $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NOT NULL, `smushed` TINYINT(1) NOT NULL,`saved` varchar(255) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
        Db::getInstance()
            ->Execute($query);
        //go through file formats and resize them
        $a = 0;
        foreach ($this->imageTypes as $imageType) {
            $newFile = _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'-'.Tools::stripslashes($imageType['name']).'.jpg';
            $smusher = new Smush22();
            $smusher->it($newFile);
        }
    }

    public function savetolog($data)
    {
        file_put_contents(dirname(__FILE__).'/log.txt', $data, FILE_APPEND);
    }

    public function removeHtaccessSection()
    {
        $key1 = '#Prestaspeed addon start';
        $key2 = '#Prestaspeed addon end';
        $path = _PS_ROOT_DIR_.'/.htaccess';
        if (file_exists($path) && is_writable($path)) {
            $s = Tools::file_get_contents($path);
            $p1 = strpos($s, $key1);
            $p2 = strpos($s, $key2, $p1);
            if ($p1 === false || $p2 === false) {
                return false;
            }
            $s = Tools::substr($s, 0, $p1).Tools::substr($s, $p2 + Tools::strlen($key2));
            file_put_contents($path, $s);
        }

        return true;
    }

    public function getExistingImgPath()
    {
        if (!$this->id) {
            return false;
        }
        if (!$this->existing_path) {
            if (Configuration::get('PS_LEGACY_IMAGES') && file_exists(_PS_PROD_IMG_DIR_.$this->id_product.'-'.$this->id.'.'.$this->image_format)) {
                $this->existing_path = $this->id_product.'-'.$this->id;
            } else {
                $this->existing_path = $this->getImgPath();
            }
        }
        return $this->existing_path;
    }

    public static function getImgFolderStatic($id_image)
    {
        if (!is_numeric($id_image)) {
            return false;
        }
        $folders = str_split((string)$id_image);
        return implode(
            '/',
            $folders
        ).'/';
    }

    public function getImgPath()
    {
        if (!$this->id) {
            return false;
        }
        $path = $this->getImgFolder().$this->id;
        return $path;
    }

    public function getImgFolder()
    {
        if (!$this->id) {
            return false;
        }
        if (!$this->folder) {
            $this->folder = self::getImgFolderStatic($this->id);
        }
        return $this->folder;
    }

    public static function checkAndFix()
    {
        $db = Db::getInstance();
        $logs = array();

        // Remove doubles in the configuration
        $filtered_configuration = array();
        $result = $db->executeS('SELECT * FROM '._DB_PREFIX_.'configuration');
        foreach ($result as $row) {
            if (_PS_VERSION_ > '1.5.0.0') {
                $key = $row['id_shop_group'].'-|-'.$row['id_shop'].'-|-'.$row['name'];
            } else {
                $key = $row['id_shop_group'].'-|-'.$row['name'];
            }
            if (in_array(
                $key,
                $filtered_configuration
            )) {
                $query = 'DELETE FROM '._DB_PREFIX_.'configuration WHERE id_configuration = '.(int)$row['id_configuration'].';';
                $db->Execute($query);
                $logs[$query] = 1;
            } else {
                $filtered_configuration[] = $key;
            }
        }
        unset($filtered_configuration);

        // Remove inexisting or monolanguage configuration value from configuration_lang
        $query = 'DELETE FROM `'._DB_PREFIX_.'configuration_lang`
		WHERE `id_configuration` NOT IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration`)
		OR `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE name IS NULL OR name = "");';
        if ($db->Execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }
        // Simple Cascade Delete
        if (_PS_VERSION_ < '1.7') {
            $queries = array(
            // 0 => DELETE FROM __table__, 1 => WHERE __id__ NOT IN, 2 => NOT IN __table__, 3 => __id__ used in the "NOT IN" table, 4 => module_name
            array('access', 'id_profile', 'profile', 'id_profile'),
            array('access', 'id_tab', 'tab', 'id_tab'),
            array('accessory', 'id_product_1', 'product', 'id_product'),
            array('accessory', 'id_product_2', 'product', 'id_product'),
            array('address_format', 'id_country', 'country', 'id_country'),
            array('attribute', 'id_attribute_group', 'attribute_group', 'id_attribute_group'),
            array('carrier_group', 'id_carrier', 'carrier', 'id_carrier'),
            array('carrier_group', 'id_group', 'group', 'id_group'),
            array('carrier_zone', 'id_carrier', 'carrier', 'id_carrier'),
            array('carrier_zone', 'id_zone', 'zone', 'id_zone'),
            array('cart_cart_rule', 'id_cart', 'cart', 'id_cart'),
            array('cart_product', 'id_cart', 'cart', 'id_cart'),
            array('cart_rule_carrier', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_carrier', 'id_carrier', 'carrier', 'id_carrier'),
            array('cart_rule_combination', 'id_cart_rule_1', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_combination', 'id_cart_rule_2', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_country', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_country', 'id_country', 'country', 'id_country'),
            array('cart_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_group', 'id_group', 'group', 'id_group'),
            array('cart_rule_product_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array(
                'cart_rule_product_rule',
                'id_product_rule_group',
                'cart_rule_product_rule_group',
                'id_product_rule_group'
            ),
            array(
                'cart_rule_product_rule_value',
                'id_product_rule',
                'cart_rule_product_rule',
                'id_product_rule'
            ),
            array('category_group', 'id_category', 'category', 'id_category'),
            array('category_group', 'id_group', 'group', 'id_group'),
            array('category_product', 'id_category', 'category', 'id_category'),
            array('category_product', 'id_product', 'product', 'id_product'),
            array('cms', 'id_cms_category', 'cms_category', 'id_cms_category'),
            array('cms_block', 'id_cms_category', 'cms_category', 'id_cms_category', 'blockcms'),
            array('cms_block_page', 'id_cms', 'cms', 'id_cms', 'blockcms'),
            array('cms_block_page', 'id_cms_block', 'cms_block', 'id_cms_block', 'blockcms'),
            array('compare', 'id_customer', 'customer', 'id_customer'),
            array('compare_product', 'id_compare', 'compare', 'id_compare'),
            array('compare_product', 'id_product', 'product', 'id_product'),
            array('connections', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('connections', 'id_shop', 'shop', 'id_shop'),
            array('connections_page', 'id_connections', 'connections', 'id_connections'),
            array('connections_page', 'id_page', 'page', 'id_page'),
            array('connections_source', 'id_connections', 'connections', 'id_connections'),
            array('customer', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('customer', 'id_shop', 'shop', 'id_shop'),
            array('customer_group', 'id_group', 'group', 'id_group'),
            array('customer_group', 'id_customer', 'customer', 'id_customer'),
            array(
                'customer_message',
                'id_customer_thread',
                'customer_thread',
                'id_customer_thread'
            ),
            array('customer_thread', 'id_shop', 'shop', 'id_shop'),
            array('customization', 'id_cart', 'cart', 'id_cart'),
            array('customization_field', 'id_product', 'product', 'id_product'),
            array('customized_data', 'id_customization', 'customization', 'id_customization'),
            array('delivery', 'id_shop', 'shop', 'id_shop'),
            array('delivery', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('delivery', 'id_carrier', 'carrier', 'id_carrier'),
            array('delivery', 'id_zone', 'zone', 'id_zone'),
            array('editorial', 'id_shop', 'shop', 'id_shop', 'editorial'),
            array('favorite_product', 'id_product', 'product', 'id_product', 'favoriteproducts'),
            array('favorite_product', 'id_customer', 'customer', 'id_customer', 'favoriteproducts'),
            array('favorite_product', 'id_shop', 'shop', 'id_shop', 'favoriteproducts'),
            array('feature_product', 'id_feature', 'feature', 'id_feature'),
            array('feature_product', 'id_product', 'product', 'id_product'),
            array('feature_value', 'id_feature', 'feature', 'id_feature'),
            array('group_reduction', 'id_group', 'group', 'id_group'),
            array('group_reduction', 'id_category', 'category', 'id_category'),
            array('homeslider', 'id_shop', 'shop', 'id_shop', 'homeslider'),
            array(
                'homeslider',
                'id_homeslider_slides',
                'homeslider_slides',
                'id_homeslider_slides',
                'homeslider'
            ),
            array('hook_module', 'id_hook', 'hook', 'id_hook'),
            array('hook_module', 'id_module', 'module', 'id_module'),
            array('hook_module_exceptions', 'id_hook', 'hook', 'id_hook'),
            array('hook_module_exceptions', 'id_module', 'module', 'id_module'),
            array('hook_module_exceptions', 'id_shop', 'shop', 'id_shop'),
            array('image', 'id_product', 'product', 'id_product'),
            array('message', 'id_cart', 'cart', 'id_cart'),
            array('message_readed', 'id_message', 'message', 'id_message'),
            array('message_readed', 'id_employee', 'employee', 'id_employee'),
            array('orders', 'id_shop', 'shop', 'id_shop'),
            array('orders', 'id_shop_group', 'group_shop', 'id_shop_group'),
            array('order_carrier', 'id_order', 'orders', 'id_order'),
            array('order_cart_rule', 'id_order', 'orders', 'id_order'),
            array('order_detail', 'id_order', 'orders', 'id_order'),
            array('order_detail_tax', 'id_order_detail', 'order_detail', 'id_order_detail'),
            array('order_history', 'id_order', 'orders', 'id_order'),
            array('order_invoice', 'id_order', 'orders', 'id_order'),
            array('order_invoice_payment', 'id_order', 'orders', 'id_order'),
            array('order_invoice_tax', 'id_order_invoice', 'order_invoice', 'id_order_invoice'),
            array('order_return', 'id_order', 'orders', 'id_order'),
            array('order_return_detail', 'id_order_return', 'order_return', 'id_order_return'),
            array('order_slip', 'id_order', 'orders', 'id_order'),
            array('order_slip_detail', 'id_order_slip', 'order_slip', 'id_order_slip'),
            array('pack', 'id_product_pack', 'product', 'id_product'),
            array('pack', 'id_product_item', 'product', 'id_product'),
            array('page', 'id_page_type', 'page_type', 'id_page_type'),
            array('page_viewed', 'id_shop', 'shop', 'id_shop'),
            array('page_viewed', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('page_viewed', 'id_date_range', 'date_range', 'id_date_range'),
            array('product_attachment', 'id_attachment', 'attachment', 'id_attachment'),
            array('product_attachment', 'id_product', 'product', 'id_product'),
            array('product_attribute', 'id_product', 'product', 'id_product'),
            array(
                'product_attribute_combination',
                'id_product_attribute',
                'product_attribute',
                'id_product_attribute'
            ),
            array('product_attribute_combination', 'id_attribute', 'attribute', 'id_attribute'),
            array('product_attribute_image', 'id_image', 'image', 'id_image'),
            array(
                'product_attribute_image',
                'id_product_attribute',
                'product_attribute',
                'id_product_attribute'
            ),
            array('product_carrier', 'id_product', 'product', 'id_product'),
            array('product_carrier', 'id_shop', 'shop', 'id_shop'),
            array('product_carrier', 'id_carrier_reference', 'carrier', 'id_reference'),
            array('product_country_tax', 'id_product', 'product', 'id_product'),
            array('product_country_tax', 'id_country', 'country', 'id_country'),
            array('product_country_tax', 'id_tax', 'tax', 'id_tax'),
            array('product_download', 'id_product', 'product', 'id_product'),
            array('product_group_reduction_cache', 'id_product', 'product', 'id_product'),
            array('product_group_reduction_cache', 'id_group', 'group', 'id_group'),
            array('product_sale', 'id_product', 'product', 'id_product'),
            array('product_supplier', 'id_product', 'product', 'id_product'),
            array('product_supplier', 'id_supplier', 'supplier', 'id_supplier'),
            array('product_tag', 'id_product', 'product', 'id_product'),
            array('product_tag', 'id_tag', 'tag', 'id_tag'),
            array('range_price', 'id_carrier', 'carrier', 'id_carrier'),
            array('range_weight', 'id_carrier', 'carrier', 'id_carrier'),
            array('referrer_cache', 'id_referrer', 'referrer', 'id_referrer'),
            array(
                'referrer_cache',
                'id_connections_source',
                'connections_source',
                'id_connections_source'
            ),
            array('scene_category', 'id_scene', 'scene', 'id_scene'),
            array('scene_category', 'id_category', 'category', 'id_category'),
            array('scene_products', 'id_scene', 'scene', 'id_scene'),
            array('scene_products', 'id_product', 'product', 'id_product'),
            array('search_index', 'id_product', 'product', 'id_product'),
            array('search_word', 'id_lang', 'lang', 'id_lang'),
            array('search_word', 'id_shop', 'shop', 'id_shop'),
            array('shop_url', 'id_shop', 'shop', 'id_shop'),
            array('specific_price_priority', 'id_product', 'product', 'id_product'),
            array('stock', 'id_warehouse', 'warehouse', 'id_warehouse'),
            array('stock', 'id_product', 'product', 'id_product'),
            array('stock_available', 'id_product', 'product', 'id_product'),
            array('stock_mvt', 'id_stock', 'stock', 'id_stock'),
            array('tab_module_preference', 'id_employee', 'employee', 'id_employee'),
            array('tab_module_preference', 'id_tab', 'tab', 'id_tab'),
            array('tax_rule', 'id_country', 'country', 'id_country'),
            array('theme_specific', 'id_theme', 'theme', 'id_theme'),
            array('theme_specific', 'id_shop', 'shop', 'id_shop'),
            array('warehouse_carrier', 'id_warehouse', 'warehouse', 'id_warehouse'),
            array('warehouse_carrier', 'id_carrier', 'carrier', 'id_carrier'),
            array('warehouse_product_location', 'id_product', 'product', 'id_product'),
            array('warehouse_product_location', 'id_warehouse', 'warehouse', 'id_warehouse'),
            );
        } else {
            $queries = array(
            // 0 => DELETE FROM __table__, 1 => WHERE __id__ NOT IN, 2 => NOT IN __table__, 3 => __id__ used in the "NOT IN" table, 4 => module_name
            array('access', 'id_profile', 'profile', 'id_profile'),
            //array('access', 'id_tab', 'tab', 'id_tab'),
            array('accessory', 'id_product_1', 'product', 'id_product'),
            array('accessory', 'id_product_2', 'product', 'id_product'),
            array('address_format', 'id_country', 'country', 'id_country'),
            array('attribute', 'id_attribute_group', 'attribute_group', 'id_attribute_group'),
            array('carrier_group', 'id_carrier', 'carrier', 'id_carrier'),
            array('carrier_group', 'id_group', 'group', 'id_group'),
            array('carrier_zone', 'id_carrier', 'carrier', 'id_carrier'),
            array('carrier_zone', 'id_zone', 'zone', 'id_zone'),
            array('cart_cart_rule', 'id_cart', 'cart', 'id_cart'),
            array('cart_product', 'id_cart', 'cart', 'id_cart'),
            array('cart_rule_carrier', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_carrier', 'id_carrier', 'carrier', 'id_carrier'),
            array('cart_rule_combination', 'id_cart_rule_1', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_combination', 'id_cart_rule_2', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_country', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_country', 'id_country', 'country', 'id_country'),
            array('cart_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_group', 'id_group', 'group', 'id_group'),
            array('cart_rule_product_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array(
                'cart_rule_product_rule',
                'id_product_rule_group',
                'cart_rule_product_rule_group',
                'id_product_rule_group'
            ),
            array(
                'cart_rule_product_rule_value',
                'id_product_rule',
                'cart_rule_product_rule',
                'id_product_rule'
            ),
            array('category_group', 'id_category', 'category', 'id_category'),
            array('category_group', 'id_group', 'group', 'id_group'),
            array('category_product', 'id_category', 'category', 'id_category'),
            array('category_product', 'id_product', 'product', 'id_product'),
            array('cms', 'id_cms_category', 'cms_category', 'id_cms_category'),
            array('cms_block', 'id_cms_category', 'cms_category', 'id_cms_category', 'blockcms'),
            array('cms_block_page', 'id_cms', 'cms', 'id_cms', 'blockcms'),
            array('cms_block_page', 'id_cms_block', 'cms_block', 'id_cms_block', 'blockcms'),
            //array('compare', 'id_customer', 'customer', 'id_customer'),
            //array('compare_product', 'id_compare', 'compare', 'id_compare'),
            //array('compare_product', 'id_product', 'product', 'id_product'),
            array('connections', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('connections', 'id_shop', 'shop', 'id_shop'),
            array('connections_page', 'id_connections', 'connections', 'id_connections'),
            array('connections_page', 'id_page', 'page', 'id_page'),
            array('connections_source', 'id_connections', 'connections', 'id_connections'),
            array('customer', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('customer', 'id_shop', 'shop', 'id_shop'),
            array('customer_group', 'id_group', 'group', 'id_group'),
            array('customer_group', 'id_customer', 'customer', 'id_customer'),
            array(
                'customer_message',
                'id_customer_thread',
                'customer_thread',
                'id_customer_thread'
            ),
            array('customer_thread', 'id_shop', 'shop', 'id_shop'),
            array('customization', 'id_cart', 'cart', 'id_cart'),
            array('customization_field', 'id_product', 'product', 'id_product'),
            array('customized_data', 'id_customization', 'customization', 'id_customization'),
            array('delivery', 'id_shop', 'shop', 'id_shop'),
            array('delivery', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('delivery', 'id_carrier', 'carrier', 'id_carrier'),
            array('delivery', 'id_zone', 'zone', 'id_zone'),
            array('editorial', 'id_shop', 'shop', 'id_shop', 'editorial'),
            array('favorite_product', 'id_product', 'product', 'id_product', 'favoriteproducts'),
            array('favorite_product', 'id_customer', 'customer', 'id_customer', 'favoriteproducts'),
            array('favorite_product', 'id_shop', 'shop', 'id_shop', 'favoriteproducts'),
            array('feature_product', 'id_feature', 'feature', 'id_feature'),
            array('feature_product', 'id_product', 'product', 'id_product'),
            array('feature_value', 'id_feature', 'feature', 'id_feature'),
            array('group_reduction', 'id_group', 'group', 'id_group'),
            array('group_reduction', 'id_category', 'category', 'id_category'),
            array('homeslider', 'id_shop', 'shop', 'id_shop', 'homeslider'),
            array(
                'homeslider',
                'id_homeslider_slides',
                'homeslider_slides',
                'id_homeslider_slides',
                'homeslider'
            ),
            array('hook_module', 'id_hook', 'hook', 'id_hook'),
            array('hook_module', 'id_module', 'module', 'id_module'),
            array('hook_module_exceptions', 'id_hook', 'hook', 'id_hook'),
            array('hook_module_exceptions', 'id_module', 'module', 'id_module'),
            array('hook_module_exceptions', 'id_shop', 'shop', 'id_shop'),
            array('image', 'id_product', 'product', 'id_product'),
            array('message', 'id_cart', 'cart', 'id_cart'),
            array('message_readed', 'id_message', 'message', 'id_message'),
            array('message_readed', 'id_employee', 'employee', 'id_employee'),
            array('orders', 'id_shop', 'shop', 'id_shop'),
            array('orders', 'id_shop_group', 'group_shop', 'id_shop_group'),
            array('order_carrier', 'id_order', 'orders', 'id_order'),
            array('order_cart_rule', 'id_order', 'orders', 'id_order'),
            array('order_detail', 'id_order', 'orders', 'id_order'),
            array('order_detail_tax', 'id_order_detail', 'order_detail', 'id_order_detail'),
            array('order_history', 'id_order', 'orders', 'id_order'),
            array('order_invoice', 'id_order', 'orders', 'id_order'),
            array('order_invoice_payment', 'id_order', 'orders', 'id_order'),
            array('order_invoice_tax', 'id_order_invoice', 'order_invoice', 'id_order_invoice'),
            array('order_return', 'id_order', 'orders', 'id_order'),
            array('order_return_detail', 'id_order_return', 'order_return', 'id_order_return'),
            array('order_slip', 'id_order', 'orders', 'id_order'),
            array('order_slip_detail', 'id_order_slip', 'order_slip', 'id_order_slip'),
            array('pack', 'id_product_pack', 'product', 'id_product'),
            array('pack', 'id_product_item', 'product', 'id_product'),
            array('page', 'id_page_type', 'page_type', 'id_page_type'),
            array('page_viewed', 'id_shop', 'shop', 'id_shop'),
            array('page_viewed', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('page_viewed', 'id_date_range', 'date_range', 'id_date_range'),
            array('product_attachment', 'id_attachment', 'attachment', 'id_attachment'),
            array('product_attachment', 'id_product', 'product', 'id_product'),
            array('product_attribute', 'id_product', 'product', 'id_product'),
            array(
                'product_attribute_combination',
                'id_product_attribute',
                'product_attribute',
                'id_product_attribute'
            ),
            array('product_attribute_combination', 'id_attribute', 'attribute', 'id_attribute'),
            array('product_attribute_image', 'id_image', 'image', 'id_image'),
            array(
                'product_attribute_image',
                'id_product_attribute',
                'product_attribute',
                'id_product_attribute'
            ),
            array('product_carrier', 'id_product', 'product', 'id_product'),
            array('product_carrier', 'id_shop', 'shop', 'id_shop'),
            array('product_carrier', 'id_carrier_reference', 'carrier', 'id_reference'),
            array('product_country_tax', 'id_product', 'product', 'id_product'),
            array('product_country_tax', 'id_country', 'country', 'id_country'),
            array('product_country_tax', 'id_tax', 'tax', 'id_tax'),
            array('product_download', 'id_product', 'product', 'id_product'),
            array('product_group_reduction_cache', 'id_product', 'product', 'id_product'),
            array('product_group_reduction_cache', 'id_group', 'group', 'id_group'),
            array('product_sale', 'id_product', 'product', 'id_product'),
            array('product_supplier', 'id_product', 'product', 'id_product'),
            array('product_supplier', 'id_supplier', 'supplier', 'id_supplier'),
            array('product_tag', 'id_product', 'product', 'id_product'),
            array('product_tag', 'id_tag', 'tag', 'id_tag'),
            array('range_price', 'id_carrier', 'carrier', 'id_carrier'),
            array('range_weight', 'id_carrier', 'carrier', 'id_carrier'),
            array('referrer_cache', 'id_referrer', 'referrer', 'id_referrer'),
            array(
                'referrer_cache',
                'id_connections_source',
                'connections_source',
                'id_connections_source'
            ),
            //array('scene_category', 'id_scene', 'scene', 'id_scene'),
            //array('scene_category', 'id_category', 'category', 'id_category'),
            //array('scene_products', 'id_scene', 'scene', 'id_scene'),
            //array('scene_products', 'id_product', 'product', 'id_product'),
            array('search_index', 'id_product', 'product', 'id_product'),
            array('search_word', 'id_lang', 'lang', 'id_lang'),
            array('search_word', 'id_shop', 'shop', 'id_shop'),
            array('shop_url', 'id_shop', 'shop', 'id_shop'),
            array('specific_price_priority', 'id_product', 'product', 'id_product'),
            array('stock', 'id_warehouse', 'warehouse', 'id_warehouse'),
            array('stock', 'id_product', 'product', 'id_product'),
            array('stock_available', 'id_product', 'product', 'id_product'),
            array('stock_mvt', 'id_stock', 'stock', 'id_stock'),
            array('tab_module_preference', 'id_employee', 'employee', 'id_employee'),
            array('tab_module_preference', 'id_tab', 'tab', 'id_tab'),
            array('tax_rule', 'id_country', 'country', 'id_country'),
            //array('theme_specific', 'id_theme', 'theme', 'id_theme'),
            //array('theme_specific', 'id_shop', 'shop', 'id_shop'),
            array('warehouse_carrier', 'id_warehouse', 'warehouse', 'id_warehouse'),
            array('warehouse_carrier', 'id_carrier', 'carrier', 'id_carrier'),
            array('warehouse_product_location', 'id_product', 'product', 'id_product'),
            array('warehouse_product_location', 'id_warehouse', 'warehouse', 'id_warehouse'),
            );
        }
        $queries = self::bulle($queries);
        foreach ($queries as $query_array) {
            // If this is a module and the module is not installed, we continue
            if (isset($query_array[4]) && !Module::isInstalled($query_array[4])) {
                continue;
            }
            $query = 'DELETE FROM `'._DB_PREFIX_.$query_array[0].'` WHERE `'.$query_array[1].'` NOT IN (SELECT `'.$query_array[3].'` FROM `'._DB_PREFIX_.$query_array[2].'`);';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }
        // _lang table cleaning
        $tables = Db::getInstance()
                    ->executeS('SHOW TABLES LIKE "'.preg_replace('/([%_])/', '\\$1', _DB_PREFIX_).'%_\\_lang"');
        foreach ($tables as $table) {
            $table_lang = current($table);
            $table = str_replace('_lang', '', $table_lang);
            $id_table = 'id_'.preg_replace('/^'._DB_PREFIX_.'/', '', $table);

            $query = 'DELETE FROM `'.bqSQL($table_lang).'` WHERE `'.bqSQL($id_table).'` NOT IN (SELECT `'.bqSQL($id_table).'` FROM `'.bqSQL($table).'`);';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
            $query = 'DELETE FROM `'.bqSQL($table_lang).'` WHERE `id_lang` NOT IN (SELECT `id_lang` FROM `'._DB_PREFIX_.'lang`);';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }

        // _shop table cleaning
        $tables = Db::getInstance()
                    ->executeS('SHOW TABLES LIKE "'.preg_replace('/([%_])/', '\\$1', _DB_PREFIX_).'%_\\_shop"');
        foreach ($tables as $table) {
            $table_shop = current($table);
            $table = str_replace('_shop', '', $table_shop);
            $id_table = 'id_'.preg_replace('/^'._DB_PREFIX_.'/', '', $table);

            if (in_array(
                $table_shop,
                array(_DB_PREFIX_.'carrier_tax_rules_group_shop')
            )) {
                continue;
            }

            $query = 'DELETE FROM `'.bqSQL($table_shop).'` WHERE `'.bqSQL($id_table).'` NOT IN (SELECT `'.bqSQL($id_table).'` FROM `'.bqSQL($table).'`);';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
            $query = 'DELETE FROM `'.bqSQL($table_shop).'` WHERE `id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`);';
            if ($db->Execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }

        // stock_available
        $query = 'DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE `id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`) AND `id_shop_group` NOT IN (SELECT `id_shop_group` FROM `'._DB_PREFIX_.'shop_group`);';
        if ($db->Execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }
        Category::regenerateEntireNtree();
        return $logs;
    }

    public function hta()
    {
        if (file_exists('../../.htaccessps')) {
            @rename('../../.htaccess', '../../.htacces-not-work');
            @rename('../../.htaccessps', '../../.htaccess');
        }
    }
    public function sendmail()
    {
        $admin_email = "".Configuration::get('PS_SHOP_EMAIL')."";
        $email = "".Configuration::get('PS_SHOP_EMAIL')."";
        /*$subject =  $this->l('PrestaSpeed Important Information');
        $headers = "From: " . strip_tags(Configuration::get('PS_SHOP_EMAIL')) . "\r\n";
        $headers .= "Reply-To: ". strip_tags(Configuration::get('PS_SHOP_EMAIL')) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";*/
        $comment = '
		<h2>'.$this->l('Keep this email safe. You have all the links to restore data with the PrestaSpeed module').'</h2>
		<p>'.$this->l('Thanks for choosing PrestaSpeed. This module help to improve the speed of your PrestaShop store. However, because the module have a lot of features, and PrestaShop have a lot of modules, version, and host configurations, sometimes you can get an error with some functions. Here are all the links to restore some features if PrestaSpeed fails to optimize the site in some point').'</p>
		<h2>Images</h2>
			<p>'.$this->l('1-If you optimize images, and get images with ? sign, you always can restore the images with the backup created by PrestaSpeed. Just navigate with a FTP client, and find the file with the -old extension (is the original file) and rename it to the original name. If you want to restore all images, just use this cron URL:').'<br/>'._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-restore.php?token='.Tools::substr(Tools::encrypt('prestaspeed/restore'), 0, 10).'&id_shop='.$this->context->shop->id.'</p>
			<p>'.$this->l('2-If you want to optimize all images (the IMG folder of PrestaShop), just run this URL in a cron task in your host:').'<br/>'._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-img.php?token='.Tools::substr(Tools::encrypt('prestaspeed/img'), 0, 10).'&id_shop='.$this->context->shop->id.'</p>
			<p>'.$this->l('3-After optimize all images and you already check that are ok, you can delete the backup images running this URL:').'<br/>'._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-remove.php?token='.Tools::substr(Tools::encrypt('prestaspeed/remove'), 0, 10).'&id_shop='.$this->context->shop->id.'</p>	
		<h2>CSS/JS</h2>
			<p>'.$this->l('1-If you use the option to minify CSS/JS and the front office/back office dont work well, run this cron to restore the original files:').'<br/>'._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-minify.php?token='.Tools::substr(Tools::encrypt('prestaspeed/minify'), 0, 10).'&id_shop='.$this->context->shop->id.'</p>
	    <h2>.htaccess</h2>
	<p>'.$this->l('1-The htaccess optimization have a lot of features, and sometimes if the host not support one of these features, you get a white screen. Use this URL to restore the original .htaccess:').'<br/>'._PS_BASE_URL_._MODULE_DIR_.'prestaspeed/prestaspeed-hta.php?token='.Tools::substr(Tools::encrypt('prestaspeed/hta'), 0, 10).'&id_shop='.$this->context->shop->id.'</p>	
			<h2>Other settings</h2>
			<p>'.$this->l('You can optimize the database enable the MySQL query cache, before that you need to set few variables in mysql configuration file (usually is my.cnf or my.ini) or contact your host support to set the values for you').'</p>

<p>'.$this->l('1st, set query_cache_type to 1. (There are 3 possible settings: 0 (disable / off), 1 (enable / on) and 2 (on demand)').'</p>
query-cache-type = 1
<p>'.$this->l('2nd, set query_cache_size to your expected size. Recommended: 20MB/40MB').'</p>
query-cache-size = 20M
			<br/>
			<h5>'.$this->l('If you have any other issue, just send a support ticket to us').'</h5>
        ';
        $template_vars = array(
            '{comment}' => $comment,
            );
        $dir_mail = false;
        if (file_exists(dirname(__FILE__).'/mails/en/prestaspeed.txt') && file_exists(dirname(__FILE__).'/mails/en/prestaspeed.html')) {
            $dir_mail = dirname(__FILE__).'/mails/';
        }
        if ($dir_mail) {
            Mail::Send(
                $this->context->language->id,
                'prestaspeed',
                'PrestaSpeed important Information',
                $template_vars,
                $admin_email,
                null,
                $admin_email,
                Configuration::get('PS_SHOP_NAME'),
                null,
                null,
                $dir_mail,
                null,
                $this->context->shop->id
            );
        }
        //mail($admin_email, "$subject", $comment, $headers);
    }
    protected static function bulle($array)
    {
        $sorted = false;
        $size = count($array);
        while (!$sorted) {
            $sorted = true;
            for ($i = 0; $i < $size - 1; ++$i) {
                for ($j = $i + 1; $j < $size; ++$j) {
                    if ($array[$i][2] == $array[$j][0]) {
                        $tmp = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $tmp;
                        $sorted = false;
                    }
                }
            }
        }
        return $array;
    }
    public function hookDisplayLeftColumn()
    {
        if ($this->page_name !== 'index') {
            $this->_assignMedia();
        }

        return $this->hookDisplayHome();
    }
    protected function _assignMedia()
    {
        if (Configuration::get('PRESTASPEED_FACEBOOK') != null) {
            $this->context->controller->addCss(($this->_path) . 'views/css/blockfacebook.css');
            $this->context->controller->addJS(($this->_path) . 'views/js/blockfacebook.js');
        }
    }
    public function hookDisplayHome()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], "Google")) {
        } else {
            if (!$this->isCached('blockfacebook.tpl', $this->getCacheId())) {
                $facebookurl = Configuration::get('PRESTASPEED_FACEBOOK');
                $this->context->smarty->assign('facebookurl', $facebookurl);
            }
            if ($facebookurl != null or $facebookurl != 0) {
                return $this->display(__FILE__, 'views/templates/front/blockfacebook.tpl', $this->getCacheId());
            }
        }
    }
    public function hookDisplayRightColumn()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], "Google")) {
        } else {
            if ($this->page_name !== 'index') {
                $this->_assignMedia();
            }

            return $this->hookDisplayHome();
        }
    }
}
