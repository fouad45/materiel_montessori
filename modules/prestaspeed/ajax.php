<?php
/**
 * 2007-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * No redistribute in other sites, or copy.
 *
 * @author    RSI
 * @copyright 2007-2018 RSI
 * @license   http://localhost!
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include_once('prestaspeed.php');
include_once('smusher.php');
/*            $cusi2 = Configuration::get('PRESTASPEED_CUSI2');
            define('BASEPATH', _PS_ROOT_DIR_.'/'.str_replace('../', '', $type)); // TODO: CAMBIAR ESTO POR TU PATH ORIGINAL
            //define('BASEURL', 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.$cusi); // TODO: Y ESTO POR TU URL
            define('MIN_TIME', 1); // uTime, modificado hace mínimo una fase lunar (aprox. 29 dias)
            define('ORIGINAL_POSTFIX', '_orig');
            $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'smush (`id_smush` int(2) NOT NULL, `url` varchar(255) NOT NULL, `smushed` TINYINT(1) NOT NULL,`saved` varchar(255) NULL, PRIMARY KEY(`url`)) ENGINE=MyISAM default CHARSET=utf8';
            Db::getInstance()
              ->Execute($query);
            $smusher = new Smush();
			$smusher->it(BASEPATH);
			return;
			
			*/
// check that the file exists
//ob_start();
$msg = '';

$path = str_replace(
    "'",
    "",
    Tools::getValue('path')
);
$curl = curl_init('http://api.resmush.it/ws.php?img=');
// set default options
curl_setopt_array(
    $curl,
    array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true
    )
);
$regexp = '/\.(jpg|jpeg|png)$/i';
$quiet = Tools::getValue('quiet');
$pretend = false;
$recursive = Tools::getValue('recursive');
$type = null;
if (!file_exists($path)) {
    $msg .= 'Invalid file path: '.$path;
    //$msg .= Tools::jsonEncode($msg);
} elseif (preg_match(
    $regexp,
    $path
)) {
    curl_setopt(
        $curl,
        CURLOPT_POSTFIELDS,
        array(
            'files' => class_exists(
                'CurlFile',
                false
            ) ? new CURLFile($path) : "@{$path}"
        )
    );
    if (!$quiet && $type == null) {
        $msg .= "  smushing ".$path." :";
    }
    // call the server app
    $response = curl_exec($curl);
    // if no response from the server
    if ($response === false) {
        if (!$quiet && $type == null) {
            $msg .= "  error: the server has gone\n".PHP_EOL;
            //$msg .= Tools::jsonEncode($msg);
        }
        $msg .= '5';
    } else {
        // decode the json response
        $data = Tools::jsonDecode($response);
        $query = 'INSERT IGNORE INTO '._DB_PREFIX_.'smush (`id_smush`, `url`, `smushed`, `saved`) VALUES (\'\', \''.pSQL(
            (string)$path
        ).'\', \'0\', \'0\')';
        if (!Db::getInstance()
               ->Execute($query)
        ) {
        }

        //URL of the optimized picture
        // if there is some error
        if (!empty($data->error)) {
            $s = str_replace(
                _PS_ROOT_DIR_,
                'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__,
                $path
            );
            $o = Tools::jsonDecode(file_get_contents('http://api.resmush.it/ws.php?img='.$s));
            if (isset($o->error)) {
                if (!$quiet && $type == null) {
                    $msg .= "  error: ".Tools::strtolower($data->error)."\n";
                    //$msg .= Tools::jsonEncode($msg);
                    // echo Tools::jsonEncode($msg);
                }
            } else {
                if ($type == null) {
                    $msg .= str_pad(
                        "  ".$o->src_size." -> ".$o->dest_size,
                        26,
                        " "
                    )." = ".round($o->dest_size * 100 / $o->src_size)."%\n<br/>";
                }
                $total = $o->src_size - $o->dest_size;

                $upd = 'UPDATE '._DB_PREFIX_.'smush SET `smushed` = \'1\', `saved` = \''.pSQL(
                    (float)$total
                ).'\' WHERE  '._DB_PREFIX_.'smush.url =  \''.pSQL((string)$path).'\'';
                Db::getInstance()
                  ->Execute($upd);
            }
            // if it's a gif image it is converted to a png file
            if ($pretend) {
                $msg .= '11';
                //echo Tools::jsonEncode($msg);
            }
            $ds = $o->dest;
            $content = Tools::file_get_contents($ds);
            file_put_contents(
                $path.'-tmp',
                $content
            );
            if (filesize($path.'-tmp') > 0) {
                if (!file_exists($path.'-old')) {
                    rename(
                        $path,
                        $path.'-old'
                    );
                }
                rename(
                    $path.'-tmp',
                    $path
                );
                //$msg .= Tools::jsonEncode($msg);
                //echo Tools::jsonEncode($msg);
            } else {
                // unlink($path.'-tmp');
                //$msg .= Tools::jsonEncode($msg);
                //exit;
                //echo Tools::jsonEncode($msg);
            }
        } elseif ($data->src_size < $data->dest_size) {
            if (!$quiet && $type != null) {
                $msg .= "  error: got larger\n<br/>".PHP_EOL;
                //$msg .= Tools::jsonEncode($msg);
                // echo Tools::jsonEncode($msg);
            }
        } elseif ($data->dest_size < 20) {
            if (!$quiet) {
                if ($type == null) {
                    $msg .= "  error: empty file downloaded\n<br/>".PHP_EOL;
                    //$msg .= Tools::jsonEncode($msg);
                    //echo Tools::jsonEncode($msg);
                }
                $upd = 'UPDATE '._DB_PREFIX_.'smush SET `smushed` = \'1\', `saved` = \'-2\' WHERE  '._DB_PREFIX_.'smush.url =  \''.pSQL(
                    (string)$path
                ).'\'';
                Db::getInstance()
                  ->Execute($upd);
            }
        } elseif ($data->src_size == $data->dest_size) {
            if (!$quiet) {
                if ($type == null) {
                    $msg .= "  cannot be optimized further\n<br/>".PHP_EOL;
                    //$msg .= Tools::jsonEncode($msg);
                    //echo $msg;
                }
                $upd = 'UPDATE '._DB_PREFIX_.'smush SET `smushed` = \'1\', `saved` = \'-1\' WHERE  '._DB_PREFIX_.'smush.url =  \''.pSQL(
                    (string)$path
                ).'\'';
                Db::getInstance()
                  ->Execute($upd);
            }
        } else {
            if (!$quiet) {
                if ($type == null) {
                    $msg .= str_pad(
                        "  ".$data->src_size." -> ".$data->dest_size,
                        26,
                        " "
                    )." = ".round($data->dest_size * 100 / $data->src_size)."%\n<br/>";
                }
                $total = $data->src_size - $data->dest_size;

                $upd = 'UPDATE '._DB_PREFIX_.'smush SET `smushed` = \'1\', `saved` = \''.pSQL(
                    (float)$total
                ).'\' WHERE  '._DB_PREFIX_.'smush.url =  \''.pSQL((string)$path).'\'';
                Db::getInstance()
                  ->Execute($upd);
            }
            // if it's a gif image it is converted to a png file

            if ($pretend) {
                echo Tools::jsonEncode($msg);
            }
            $ds = $data->dest;
            $content = Tools::file_get_contents($ds);
            file_put_contents(
                $path.'-tmp',
                $content
            );
            if (filesize($path.'-tmp') > 0) {
                if (!file_exists($path.'-old')) {
                    rename(
                        $path,
                        $path.'-old'
                    );
                }
                rename(
                    $path.'-tmp',
                    $path
                );
                //$msg .= Tools::jsonEncode($msg);
                //exit;
                //echo $msg;
            } else {
                // unlink($path.'-tmp');
                //$msg .= Tools::jsonEncode($msg);
                //$msg .= 'd';
                //exit;
                //echo $msg;
            }
        }
    }
} elseif (!$quiet && $type == null) {
    $msg .= "  error: invalid file ".$path."\n".PHP_EOL;
    //$msg .= Tools::jsonEncode($msg);
    //exit;
    //echo $msg;
}
//ob_flush();
//ob_end_flush();

echo Tools::jsonEncode($msg);
exit();
