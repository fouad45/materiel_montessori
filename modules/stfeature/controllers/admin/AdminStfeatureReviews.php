<?php

/**
 * 2007-2017 Splashythemes
 *
 * NOTICE OF LICENSE
 *
 * St feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
 *
 * DISCLAIMER
 *
 *  @Module Name: ST Feature
 *  @author    splashythemes <splashythemes@gmail.com>
 *  @copyright 2007-2017 splashythemes
 *  @license   http://splashythemes.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'stfeature/classes/ProductReviewCriterion.php');
require_once(_PS_MODULE_DIR_.'stfeature/classes/ProductReview.php');

class AdminStfeatureReviewsController extends ModuleAdminController
{
	// protected $mess_id;
	
    public function __construct()
    {		
        parent::__construct();		
        $this->bootstrap = true;
        $this->table = 'stfeature_product_review_criterion';      
        $this->identifier = 'id_product_review_criterion';
        $this->className = 'StProductReviewCriterion';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
        $this->fields_list = array(
            'id_product_review_criterion' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
				'class' => 'fixed-width-sm'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
            ),
            'type_name' => array(
                'title' => $this->l('Type'),
                'type' => 'text',
				'search' => false,
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
				'class' => 'fixed-width-sm'
            ),
        );
		
		$this->_defaultOrderBy = 'id_product_review_criterion';
		// $this->_select .= ' cl.title ';
		$this->_select .= ' CASE 
								WHEN `id_product_review_criterion_type` = 1 THEN "'.$this->module->l('Valid for the entire catalog').'" 
								WHEN `id_product_review_criterion_type` = 2 THEN "'.$this->module->l('Restricted to some categories').'" 
								WHEN `id_product_review_criterion_type` = 3 THEN "'.$this->module->l('Restricted to some products').'"															
							  END as `type_name` ';
        // $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'leoblogcat c ON a.id_leoblogcat = c.id_leoblogcat
								  // LEFT JOIN '._DB_PREFIX_.'leoblogcat_lang cl ON cl.id_leoblogcat=c.id_leoblogcat AND cl.id_lang=b.id_lang 
			    // ';
		// echo '<pre>';
		// print_r($this->_list);die();
    }
	
	// public function setMedia()
    // {
        // parent::setMedia();
       
       
        // $this->context->controller->addJS(__PS_BASE_URI__.'modules/leofeature/js/back.js');
    // }

    public function renderForm()
    {
		// if (!$this->loadObject(true)) {
            if (Validate::isLoadedObject($this->object)) {
                $this->display = 'edit';
				// $this->mess_id = '4';
				// print_r($this->mess_id);die();
            } else {
                $this->display = 'add';
				// $this->mess_id = '3';
				// print_r($this->mess_id);die();
            }
        // }
		
		$types = StProductReviewCriterion::getTypes();
        $query = array();
        foreach ($types as $key => $value) {
            $query[] = array(
                    'id' => $key,
                    'label' => $value,
                );
        }
		
		if (Tools::getValue('id_product_review_criterion'))
		{
			$id_criterion = Tools::getValue('id_product_review_criterion');
		}
		else
		{
			$id_criterion = 0;
		}
		
		$criterion = new StProductReviewCriterion((int) $id_criterion);
        $selected_categories = $criterion->getCategories();

        $product_table_values = Product::getSimpleProducts($this->context->language->id);
		
        $selected_products = $criterion->getProducts();
		
        foreach ($product_table_values as $key => $product) {
            if (false !== array_search($product['id_product'], $selected_products)) {
                $product_table_values[$key]['selected'] = 1;
            }
        }
		
		// echo '<pre>';
		// print_r($product_table_values);die();

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $field_category_tree = array(
				'type' => 'categories_select',
				'name' => 'categoryBox',
				'label' => $this->l('Criterion will be restricted to the following categories'),
				'category_tree' => $this->initCategoriesAssociation(null, $id_criterion),
			);
        } else {
            $field_category_tree = array(
				'type' => 'categories',
				'label' => $this->l('Criterion will be restricted to the following categories'),
				'name' => 'categoryBox',
				'desc' => $this->l('Mark the boxes of categories to which this criterion applies.'),
				'tree' => array(
					'use_search' => false,
					'id' => 'categoryBox',
					'use_checkbox' => true,
					'selected_categories' => $selected_categories,
				),
				//retro compat 1.5 for category tree
				'values' => array(
					'trads' => array(
						'Root' => Category::getTopCategory(),
						'selected' => $this->l('Selected'),
						'Collapse All' => $this->l('Collapse All'),
						'Expand All' => $this->l('Expand All'),
						'Check All' => $this->l('Check All'),
						'Uncheck All' => $this->l('Uncheck All'),
					),
					'selected_cat' => $selected_categories,
					'input_name' => 'categoryBox[]',
					'use_radio' => false,
					'use_search' => false,
					'disabled_categories' => array(),
					'top_category' => Category::getTopCategory(),
					'use_context' => true,
				),
			);
        }
		
		$this->fields_form = array(
                'legend' => array(
                    'title' => $this->l('Add new criterion'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_product_review_criterion',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Criterion name'),
                        'name' => 'name',
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'id_product_review_criterion_type',
                        'label' => $this->l('Application scope of the criterion'),
                        'options' => array(
                                        'query' => $query,
                                        'id' => 'id',
                                        'name' => 'label',
                                    ),
                    ),
                    $field_category_tree,
                    array(
                        'type' => 'products',
                        'label' => $this->l('The criterion will be restricted to the following products'),
                        'name' => 'ids_product',
                        'values' => $product_table_values,
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled'),
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled'),
							),
						),
                    ),
                ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitEditCriterion',
			),
			'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'submitEditCriterionAndStay',
                    'type' => 'submit',
                    'title' => $this->l('Save and stay'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save-and-stay'
                )
            )
            
        );
        return parent::renderForm();
    }

    public function renderList()
    {
        $this->toolbar_title = $this->l('Review Criteria');
        // $this->toolbar_btn['new'] = array(
            
			// 'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            // 'desc' => $this->l('Add new')
        // );
		
		// echo '<pre>';
		// print_r($this->_defaultOrderBy);die();
		// print_r($this->_list);die();
		$return = null;
		$return .= $this->renderModerateLists();
		$return .= parent::renderList();
		$return .= $this->renderReviewsList();
        return $return;
		
    }
	
	// public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    // {		
	
        // $criterions = StProductReviewCriterion::getCriterions($this->context->language->id, false, false);
		
		// $this->_list = $criterions;
		// $this->_listTotal = count($criterions);
		
		
    // }

    public function postProcess()
    {	
		// print_r($this->display);die();
		
        if (Tools::isSubmit('submitEditCriterion') || Tools::isSubmit('submitEditCriterionAndStay')) {
			
            parent::validateRules();
			
			// print_r('aaa');die();
            if (count($this->errors)) {
				$this->display = 'edit';
                return false;				
            }
			// print_r('aaa');die();
			if ((int) Tools::getValue('id_product_review_criterion'))
			{
				$mess_id = '4';
			}
			else
			{
				$mess_id = '3';
			}
			
			$criterion = new StProductReviewCriterion((int) Tools::getValue('id_product_review_criterion'));
            $criterion->id_product_review_criterion_type = Tools::getValue('id_product_review_criterion_type');
            $criterion->active = Tools::getValue('active');

            $languages = Language::getLanguages();
            $name = array();
            foreach ($languages as $key => $value) {
                $name[$value['id_lang']] = Tools::getValue('name_'.$value['id_lang']);
            }
            $criterion->name = $name;

            $criterion->save();

            // Clear before reinserting data
            $criterion->deleteCategories();
            $criterion->deleteProducts();
            if ($criterion->id_product_review_criterion_type == 2) {
                if ($categories = Tools::getValue('categoryBox')) {
                    if (count($categories)) {
                        foreach ($categories as $id_category) {
                            $criterion->addCategory((int) $id_category);
                        }
                    }
                }
            } elseif ($criterion->id_product_review_criterion_type == 3) {
                if ($products = Tools::getValue('ids_product')) {
                    if (count($products)) {
                        foreach ($products as $product) {
                            $criterion->addProduct((int) $product);
                        }
                    }
                }
            }
            if ($criterion->save()) {			
                if (Tools::isSubmit('submitEditCriterionAndStay')) {			
					# validate module			
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$criterion->id.'&conf='.$mess_id.'&update'.$this->table.'&token='.$this->token;
				} else {
					# validate module
					$this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
				}
            } else {
                return false;
            }
		           
        }
		elseif (Tools::isSubmit('deletestfeature_product_review') && Tools::getValue('id_product_review')) 
		{
            $id_product_review = (int) Tools::getValue('id_product_review');
            $review = new StProductReview($id_product_review);
			$review->delete();
			// var_dump($review->delete());die();            
			$this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token;                           	
		}
		elseif (Tools::isSubmit('approveReview') && Tools::getValue('id_product_review'))
		{	
			$id_product_review = (int) Tools::getValue('id_product_review');
            $review = new StProductReview($id_product_review);
            $review->validate();
			$this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;  
        }
		elseif (Tools::isSubmit('noabuseReview') && Tools::getValue('id_product_review'))
		{
			$id_product_review = (int) Tools::getValue('id_product_review');
            StProductReview::deleteReports($id_product_review); 
			$this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;  			
		}
		else
		{
			// print_r('test');die();
			// echo '<pre>';
			// print_r($_GET);die();
			// echo '<pre>';
			// var_dump(parent::postProcess());die();
			parent::postProcess(true);
			// return parent::postProcess();
		}
		$this->module->_clearcache('st_list_product_review.tpl');
    }

    public function initCategoriesAssociation($id_root = null, $id_criterion = 0)
    {
        if (is_null($id_root)) {
            $id_root = Configuration::get('PS_ROOT_CATEGORY');
        }
        $id_shop = (int) Tools::getValue('id_shop');
        $shop = new Shop($id_shop);
        if ($id_criterion == 0) {
            $selected_cat = array();
        } else {
            $pdc_object = new StProductReviewCriterion($id_criterion);
            $selected_cat = $pdc_object->getCategories();
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop')) {
            $root_category = new Category($shop->id_category);
        } else {
            $root_category = new Category($id_root);
        }
        $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name[$this->context->language->id]);

        $helper = new Helper();

        return $helper->renderCategoryTree($root_category, $selected_cat, 'categoryBox', false, true);
    }
	
	//DONGND:: render list reviews have not approved and reported
	public function renderModerateLists()
    {      
        $return = null;

        if (Configuration::get('STFEATURE_PRODUCT_REVIEWS_MODERATE')) {
            $reviews = StProductReview::getByValidate(0, false);
			
			if (count($reviews) > 0)
			{	
				$fields_list = $this->getStandardFieldList();

				if (version_compare(_PS_VERSION_, '1.6', '<')) {
					$return .= $this->l('Reviews waiting for approval');
					$actions = array('enable', 'delete');
				} else {
					$actions = array('approve', 'delete');
				}

				$helper = new HelperList();
				$helper->shopLinkType = '';
				$helper->simple_header = true;
				$helper->actions = $actions;
				$helper->show_toolbar = false;
				$helper->module = $this->module;
				$helper->listTotal = count($reviews);
				$helper->identifier = 'id_product_review';
				$helper->title = $this->l('Reviews waiting for approval');
				$helper->table = 'stfeature_product_review';
				$helper->token = $this->token;
				$helper->currentIndex = self::$currentIndex;
				$helper->no_link = true;
				//$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

				$return .= $helper->generateList($reviews, $fields_list);
			}
        }

        $reviews = StProductReview::getReportedReviews();
       
		if (count($reviews) > 0)
		{
			$fields_list = $this->getStandardFieldList();
			if (version_compare(_PS_VERSION_, '1.6', '<')) {
				$return .= $this->l('Reported Reviews');
				$actions = array('enable', 'delete');
			} else {
				$actions = array('delete', 'noabuse');
			}

			$helper = new HelperList();
			$helper->shopLinkType = '';
			$helper->simple_header = true;
			$helper->actions = $actions;
			$helper->show_toolbar = false;
			$helper->module = $this->module;
			$helper->listTotal = count($reviews);
			$helper->identifier = 'id_product_review';
			$helper->title = $this->l('Reported Reviews');
			$helper->table = 'stfeature_product_review';
			$helper->token = $this->token;
			$helper->currentIndex = self::$currentIndex;
			$helper->no_link = true;
			//$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

			$return .= $helper->generateList($reviews, $fields_list);
		}
        return $return;
    }
	
	//DONGND:: render list reviews have approved
	public function renderReviewsList()
    {
       
        $reviews = StProductReview::getByValidate(1, false);
        $moderate = Configuration::get('STFEATURE_PRODUCT_REVIEWS_MODERATE');
        if (empty($moderate)) {
            $reviews = array_merge($reviews, StProductReview::getByValidate(0, false));
        }

        $fields_list = $this->getStandardFieldList();

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = array('delete');
        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->listTotal = count($reviews);
        $helper->identifier = 'id_product_review';
        $helper->title = $this->l('Approved Reviews');
        $helper->table = 'stfeature_product_review';
        $helper->token = $this->token;
		$helper->currentIndex = self::$currentIndex;
		$helper->no_link = true;
        //$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

        return $helper->generateList($reviews, $fields_list);
    }
	
	public function displayApproveLink($token, $id, $name = null)
    {
		$template = array();
		
		$this->context->smarty->assign(array(
            'href' => $this->context->link->getAdminLink('AdminStfeatureReviews').'&approveReview&id_product_review='.$id,
            'action' => $this->l('Approve'),
		));
		return $this->context->smarty->fetch(_PS_MODULE_DIR_.'stfeature/views/templates/admin/stfeature_reviews/list_action_approve.tpl');
		
		/* $template = $this->createTemplate('list_action_approve.tpl');
		
        $template->assign(array(
            'href' => $this->context->link->getAdminLink('AdminStfeatureReviews').'&approveReview&id_product_review='.$id,
            'action' => $this->l('Approve'),
        ));

        return $template->fetch(); */
		
    }

    public function displayNoabuseLink($token, $id, $name = null)
    {
		$template = array();
		// $template = $this->createTemplate('list_action_noabuse.tpl');
		
		$this->context->smarty->assign(array(
            'href' => $this->context->link->getAdminLink('AdminStfeatureReviews').'&noabuseReview&id_product_review='.$id,
            'action' => $this->l('Approve'),
		));
       /* $template->assign(array(
            'href' => $this->context->link->getAdminLink('AdminStfeatureReviews').'&noabuseReview&id_product_review='.$id,
            'action' => $this->l('Not abusive'),
        )); */
		return $this->context->smarty->fetch(_PS_MODULE_DIR_.'stfeature/views/templates/admin/stfeature_reviews/list_action_noabuse.tpl');
        //return $template->fetch();
    }
	
	public function getStandardFieldList()
    {
        return array(
            'id_product_review' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
            ),
            'title' => array(
                'title' => $this->l('Review title'),
                'type' => 'text',
            ),
            'content' => array(
                'title' => $this->l('Review'),
                'type' => 'text',
            ),
            'grade' => array(
                'title' => $this->l('Rating'),
                'type' => 'text',
                'suffix' => '/5',
            ),
            'customer_name' => array(
                'title' => $this->l('Author'),
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Product'),
                'type' => 'text',
            ),
            'date_add' => array(
                'title' => $this->l('Time of publication'),
                'type' => 'date',
            ),
        );
    }
}
