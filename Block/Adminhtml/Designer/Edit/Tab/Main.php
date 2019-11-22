<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Designer
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Designer\Block\Adminhtml\Designer\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Lof\Designer\Helper\Data
     */
    protected $_viewHelper;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_country;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Framework\Registry             $registry      
     * @param \Magento\Framework\Data\FormFactory     $formFactory   
     * @param \Magento\Store\Model\System\Store       $systemStore   
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     * @param \Lof\Designer\Helper\Data                  $viewHelper  
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param  \Magento\Directory\Model\RegionFactory $regionFactory  
     * @param array                                   $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\Designer\Helper\Data $viewHelper,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        array $data = []
    ) {
        $this->_viewHelper = $viewHelper;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_country = $country;
        $this->_regionFactory = $regionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm() {
    	/** @var $model \Lof\Designer\Model\Designer */
    	$model = $this->_coreRegistry->registry('lof_designer');
        $formData = array('country_id' => "");
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $countries = $this->_country->toOptionArray(false, 'US');
        $regionCollection = $this->_regionFactory->create()->getCollection()->addCountryFilter(
            $formData['country_id']
        );
        $regions = $regionCollection->toOptionArray();
    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Lof_Designer::designer_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}
    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('designer_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Designer Information')]);


    	if ($model->getId()) {
    		$fieldset->addField('designer_id', 'hidden', ['name' => 'designer_id']);
    	}

    	$fieldset->addField(
    		'name',
    		'text',
    		[
                'name'     => 'name',
                'label'    => __('Designer Name'),
                'title'    => __('Designer Name'),
                'required' => true,
                'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
    		'url_key',
    		'text',
    		[
                'name'     => 'url_key',
                'label'    => __('URL Key'),
                'title'    => __('URL Key'),
                'note'     => __('Empty to auto create url key'),
                'disabled' => $isElementDisabled
    		]
    		);

        $fieldset->addField(
            'group_id',
            'select',
            [
                'label'    => __('Designer Group'),
                'title'    => __('Designer Group'),
                'name'     => 'group_id',
                'required' => true,
                'options'  => $this->_viewHelper->getGroupList(),
                'disabled' => $isElementDisabled
            ]
        );

    	$fieldset->addField(
    		'image',
    		'image',
    		[
                'name'     => 'image',
                'label'    => __('Image'),
                'title'    => __('Image'),
                'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
    		'thumbnail',
    		'image',
    		[
                'name'     => 'thumbnail',
                'label'    => __('Thumbnail'),
                'title'    => __('Thumbnail'),
                'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
            'description',
            'editor',
            [
                'name'     => 'description',
                'style'    => 'height:200px;',
                'label'    => __('Description'),
                'title'    => __('Description'),
                'disabled' => $isElementDisabled,
                'config'   => $wysiwygConfig
            ]
        );

    	/**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        $fieldset->addField(
            'birthday_info',
            'text',
            [
                'name' => 'birthday_info', 
                'label' => __('Birthday Info'),
                'title'    => __('Birthday Info'),
                'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
    		'contact_name',
    		'text',
    		[
                'name'     => 'contact_name',
                'label'    => __('Contact Name'),
                'title'    => __('Contact Name'),
                'disabled' => $isElementDisabled
    		]
            );

        $fieldset->addField(
    		'company_name',
    		'text',
    		[
                'name'     => 'company_name',
                'label'    => __('Company Name'),
                'title'    => __('Company Name'),
                'disabled' => $isElementDisabled
    		]
            );
        
        $fieldset->addField(
    		'email',
    		'text',
    		[
                'name'     => 'email',
                'label'    => __('Email'),
                'title'    => __('Email'),
                'disabled' => $isElementDisabled
    		]
            );
        
        $fieldset->addField(
    		'telephone',
    		'text',
    		[
                'name'     => 'telephone',
                'label'    => __('Telephone'),
                'title'    => __('Telephone'),
                'disabled' => $isElementDisabled
    		]
            );

            $fieldset->addField(
                'street',
                'text',
                [
                    'name'     => 'street',
                    'label'    => __('Street'),
                    'title'    => __('Street'),
                    'disabled' => $isElementDisabled
                ]
                );
            
            $fieldset->addField(
                'website',
                'text',
                [
                    'name'     => 'website',
                    'label'    => __('Website'),
                    'title'    => __('Website'),
                    'disabled' => $isElementDisabled
                ]
                );
            $fieldset->addField(
                'country_id',
                'select',
                [
                    'name' => 'country_id',
                    'label' => __('Country'),
                    'title'    => __('Country'),
                    'values' => $countries,
                    'disabled' => $isElementDisabled
                ]
                );
                
            $fieldset->addField(
                'region_id',
                'select',
                [
                    'name' => 'region_id', 
                    'label' => __('State'),
                    'title'    => __('State'), 
                    'values' => $regions,
                    'disabled' => $isElementDisabled
                ]
                );
                
            $fieldset->addField(
                'region',
                'text',
                [
                    'name' => 'region', 
                    'label' => __('Region'),
                    'title'    => __('Region'),
                    'disabled' => $isElementDisabled
                ]
                );
                
            $fieldset->addField(
                'city',
                'text',
                [
                    'name' => 'city', 
                    'label' => __('City'),
                    'title'    => __('City'),
                    'disabled' => $isElementDisabled
                ]
                );
    
            $fieldset->addField(
                    'postcode',
                    'text',
                    [
                        'name' => 'postcode', 
                        'label' => __('Postcode'),
                        'title'    => __('Postcode'),
                        'disabled' => $isElementDisabled
                    ]
                    );
          $this->setChild(
                        'form_after',
                        $this->getLayout()->createBlock('Magento\Framework\View\Element\Template')->setTemplate('Lof_Designer::country/js.phtml')
                    );
                    
        $fieldset->addField(
    		'position',
    		'text',
    		[
	    		'name' => 'position',
	    		'label' => __('Position'),
	    		'title' => __('Position'),
	    		'disabled' => $isElementDisabled
    		]
    		);

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );


    	$form->setValues($model->getData());
    	$this->setForm($form);

    	return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Designer Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Designer Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}