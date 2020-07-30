<?php
/**
 * @author JMango360 Operations BV
 * @copyright 2019 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Jmango360SettingsController extends AdminControllerCore
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';
        $this->tabAccess['view'] = 1;
        $this->tabAccess['edit'] = 1;
        $this->tabAccess['add'] = 1;
        $this->tabAccess['delete'] = 1;

        parent::__construct();

        $this->fields_options = array(
            'general' => array(
                'title' => $this->l('General'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'JM360_SHOW_ORDERED_FROM' => array(
                        'title' => $this->l('Show Ordered from in order list'),
                        'hint' => $this->l('Show Ordered from in order list'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    )
                ),
                'submit' => array('title' => $this->l('Save'))
            )
        );
    }
}
