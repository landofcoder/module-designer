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
namespace Lof\Designer\Controller\Adminhtml\Group;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Designer::group_edit');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Lof_Designer::designer')
            ->addBreadcrumb(__('Designer'), __('Designer'))
            ->addBreadcrumb(__('Manage Designers'), __('Manage Designers'));
        return $resultPage;
    }

    /**
     * Edit Designer Page
     */
    public function execute()
    {
    	// 1. Get ID and create model
    	$id = $this->getRequest()->getParam('group_id');
    	$model = $this->_objectManager->create('Lof\Designer\Model\Group');

    	// 2. Initial checking
    	if($id){
    		$model->load($id);
    		if(!$model->getId()){
    			$this->messageManager->addError(__('This designer no longer exits. '));
    			/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
    			$resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
    		}
    	}

    	// 3. Set entered data if was error when we do save
    	$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('lof_designer', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Designer') : __('New Group'),
            $id ? __('Edit Designer') : __('New Group')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Designers'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getname() : __('New Group'));

        return $resultPage;
    }
}