<?php

    // Init
    $sql = array();

    // Create Table in Database
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldslideshow` (
                      `id_fieldslideshow` int(10) NOT NULL AUTO_INCREMENT,
                      `porder` int NOT NULL,
					  `active` int NOT NULL,
                       `images` varchar(250) NOT NULL,
					  PRIMARY KEY (`id_fieldslideshow`)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
					
	$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldslideshow_lang` (
						`id_fieldslideshow` int(11) unsigned NOT NULL,
						`id_lang` int(11) unsigned NOT NULL,
						`title1` varchar(250) NOT NULL,
						`title2` varchar(250) NOT NULL,
						`btntext` varchar(250) NOT NULL,
						`link` varchar(250) NOT NULL DEFAULT "#",
						`description` longtext NOT NULL,
						`image` longtext NOT NULL,
                        PRIMARY KEY (`id_fieldslideshow`,`id_lang`)
                    ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';									

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldslideshow_shop` (
                        `id_fieldslideshow` int(11) unsigned NOT NULL,
                        `id_shop` int(11) unsigned NOT NULL,
                        PRIMARY KEY (`id_fieldslideshow`,`id_shop`)
                    ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
					
    $doc = new DOMDocument();
    $file = _PS_MODULE_DIR_ . '/fieldslideshow/sql/slideshow.xml';
    $doc->load($file);
    
    $blocks = $doc->getElementsByTagName("slideshow");
    
    foreach ($blocks as $block) {
        $ids = $block->getElementsByTagName("id_fieldslideshow");
        $id = $ids->item(0)->nodeValue;
        $actives = $block->getElementsByTagName("active");
        $active = $actives->item(0)->nodeValue;
        $porders = $block->getElementsByTagName("porder");
        $porder = $porders->item(0)->nodeValue;
        $images = $block->getElementsByTagName("images");
        $image = $images->item(0)->nodeValue;
        $sql[] = "insert into `" . _DB_PREFIX_ . "fieldslideshow` (`id_fieldslideshow`,`porder`,`active`,`images`) 
				  values('".$id."','".$porder."','".$active."','".$image."');";
    }
	
	
	$blocklangs = $doc->getElementsByTagName("slideshow_lang");
    foreach ($blocklangs as $block) {
        $ids = $block->getElementsByTagName("id_fieldslideshow");
        $id = $ids->item(0)->nodeValue;
        $titles1 = $block->getElementsByTagName("title1");
        $title1 = $titles1->item(0)->nodeValue;
        $titles2 = $block->getElementsByTagName("title2");
        $title2 = $titles2->item(0)->nodeValue;
        $btntexts = $block->getElementsByTagName("btntext");
        $btntext = $btntexts->item(0)->nodeValue;
        $links = $block->getElementsByTagName("link");
        $link = $links->item(0)->nodeValue;
        $descriptions = $block->getElementsByTagName("description");
        $description = $descriptions->item(0)->nodeValue;
		$id_langs = $block->getElementsByTagName('id_lang');
		$id_lang = $id_langs->item(0)->nodeValue;
        $sql[] = "insert into `" . _DB_PREFIX_ . "fieldslideshow_lang` (`id_fieldslideshow`,`id_lang`, `title1`, `title2`, `btntext`, `link`, `description`) 
           values('".$id."','".$id_lang."','".$title1."','".$title2."','".$btntext."','".$link."','".$description."');";
    }

    $blockshops = $doc->getElementsByTagName("slideshow_shop");
    foreach ($blockshops as $blockshop) {
        $ids = $blockshop->getElementsByTagName("id_fieldslideshow");
        $id = $ids->item(0)->nodeValue;
        $id_shops = $blockshop->getElementsByTagName("id_shop");
        $id_shop = $id_shops->item(0)->nodeValue;
        //echo $id.'-'.$id_shop;
        $sql[] = "insert into `" . _DB_PREFIX_ . "fieldslideshow_shop`(`id_fieldslideshow`, `id_shop`) 
                VALUES('" . $id . "','" . $id_shop . "')";
    }