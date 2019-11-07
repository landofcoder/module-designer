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
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Designer\Controller\Search;

use Magento\Framework\App\Action\Context;

/**
 * Display Hello on screen
 */
class Result extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Lof\Designer\Helper\Data
     */
    protected $_designerHelper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context
     * @param \Magento\Store\Model\StoreManager
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Lof\Designer\Helper\Data
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\Registry
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Designer\Helper\Data $designerHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry
        ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_designerHelper = $designerHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
        $this->_request = $context->getRequest();
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $designerHelper = $this->_designerHelper;
        $request = $this->getRequest();
        if(!$designerHelper->getConfig('general_settings/enable') || !trim($this->_request->getParam('s'))){
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('defaultnoroute');
        }
        return $page;
    }
}