<?php/** * @author Jmango360 * @copyright 2017 JMango360 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0) */class  IconModelResponse{    public $src = "assets/icons/icon-jmango360-pwa-192x192.png";    public $sizes = "192x192";    public $type = "image/png";    public function __construct($size, $type, $src)    {        $this->sizes = $size;        $this->type = "image/png";        $this->src = $src;    }}