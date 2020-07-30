<?php
$sql = array();
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'field_staticblock` (
            `id_fieldstaticblock` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `identify` varchar(128) NOT NULL,
            `hook_position` varchar(128) NOT NULL,
            `name_module` varchar(128) NOT NULL,
            `hook_module` varchar(128) NOT NULL,
            `fieldorder` int(10) unsigned NOT NULL,
            `insert_module` int(10) unsigned NOT NULL,
            `active` int(10) unsigned NOT NULL,
            `showhook` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id_fieldstaticblock`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
		
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'field_staticblock_lang` (
            `id_fieldstaticblock` int(11) unsigned NOT NULL,
            `id_lang` int(11) unsigned NOT NULL,
			`title` varchar(128) NOT NULL,
            `description` longtext,
            PRIMARY KEY (`id_fieldstaticblock`,`id_lang`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
				

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'field_staticblock_shop` (
            `id_fieldstaticblock` int(11) unsigned NOT NULL,
            `id_shop` int(11) unsigned NOT NULL,
            PRIMARY KEY (`id_fieldstaticblock`,`id_shop`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$doc = new DOMDocument();
$file = _PS_MODULE_DIR_. '/fieldstaticblocks/sql/staticblock.xml';
$doc->load( $file);

$blocks = $doc->getElementsByTagName("block");
foreach( $blocks as $block )
{	
	$ids = $block->getElementsByTagName( "id_fieldstaticblock" );
	$id = $ids->item(0)->nodeValue;
	$identifys = $block->getElementsByTagName( "identify" );
	$identify = $identifys->item(0)->nodeValue;
	$hook_positions = $block->getElementsByTagName( "hook_position" );
	$hook_position = $hook_positions->item(0)->nodeValue;
	$name_modules = $block->getElementsByTagName( "name_module" );
	$name_module = $name_modules->item(0)->nodeValue;
	$hook_modules = $block->getElementsByTagName( "hook_module" );
	$hook_module = $hook_modules->item(0)->nodeValue;
	$fieldorders = $block->getElementsByTagName( "fieldorder" );
	$fieldorder = $fieldorders->item(0)->nodeValue;
	$fieldorders = $block->getElementsByTagName( "fieldorder" );
	$fieldorder = $fieldorders->item(0)->nodeValue;
	$insert_modules = $block->getElementsByTagName( "insert_module" );
	$insert_module = $insert_modules->item(0)->nodeValue;
	$actives = $block->getElementsByTagName( "active" );
	$active = $actives->item(0)->nodeValue;
	$showhooks = $block->getElementsByTagName( "showhook" );
	$showhook = $showhooks->item(0)->nodeValue;
	$sql[] = "INSERT INTO `"._DB_PREFIX_."field_staticblock` (`id_fieldstaticblock`, `identify`, `hook_position`, `name_module`, `hook_module`,`fieldorder`, `insert_module`, `active`, `showhook`) 
	values('".$id."','".$identify."','".$hook_position."','".$name_module."','".$hook_module."','".$fieldorder."','".$insert_module."','".$active."','".$showhook."')";
}
$languages = Language::getLanguages(true, $this->context->shop->id);
$blocklangs = $doc->getElementsByTagName("block_lang");

foreach( $languages as $language ){
        $dem = 0;
        foreach( $blocklangs as $blocklang )
        {	
                    $isolangs = $blocklang->getElementsByTagName( "iso_lang" );
                    $isolang = $isolangs->item(0)->nodeValue;
                    if($isolang == $language['iso_code']){
                        $ids = $blocklang->getElementsByTagName( "id_fieldstaticblock" );
                        $id = $ids->item(0)->nodeValue;
                        $idlang = $language['id_lang'];
                        $titles = $blocklang->getElementsByTagName( "title" );
                        $title = $titles->item(0)->nodeValue;
                        $descriptions = $blocklang->getElementsByTagName( "description" );
                        $description = $descriptions->item(0)->nodeValue;
                        $description =  str_replace('/kids__17/home1/',__PS_BASE_URI__, $description);
                        $sql[] = "INSERT INTO `"._DB_PREFIX_."field_staticblock_lang` (`id_fieldstaticblock`, `id_lang`, `title`, `description`) 
                                          VALUES('".$id."','".$idlang."','".$title."','".$description."')";
                        $dem = 1;
                    }
        }
        if($dem == 0){
            foreach( $blocklangs as $blocklang ){	
                    $isolangs = $blocklang->getElementsByTagName( "iso_lang" );
                    $isolang = $isolangs->item(0)->nodeValue;
                    if($isolang == 'en'){
                        $ids = $blocklang->getElementsByTagName( "id_fieldstaticblock" );
                        $id = $ids->item(0)->nodeValue;
                        $idlang = $language['id_lang'];
                        $titles = $blocklang->getElementsByTagName( "title" );
                        $title = $titles->item(0)->nodeValue;
                        $descriptions = $blocklang->getElementsByTagName( "description" );
                        $description = $descriptions->item(0)->nodeValue;
                        $description =  str_replace('/kids__17/home1/',__PS_BASE_URI__, $description);
                        $sql[] = "INSERT INTO `"._DB_PREFIX_."field_staticblock_lang` (`id_fieldstaticblock`, `id_lang`, `title`, `description`) 
                                          VALUES('".$id."','".$idlang."','".$title."','".$description."')";
                    }
            }
        }
}

$blockshops = $doc->getElementsByTagName("block_shop");
foreach( $blockshops as $blockshop )
{	
	$ids = $blockshop->getElementsByTagName( "id_fieldstaticblock" );
	$id = $ids->item(0)->nodeValue;
        $id_shops = $blockshop->getElementsByTagName( "id_shop" );
	$id_shop = $id_shops->item(0)->nodeValue;
        $sql[] = "insert into `"._DB_PREFIX_."field_staticblock_shop`(`id_fieldstaticblock`, `id_shop`) 
            VALUES('".$id."','".$id_shop."')";
}