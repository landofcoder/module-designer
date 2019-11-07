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
namespace Lof\Designer\Controller\Designer;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Lof\Designer\Model\Layer\Resolver;

class View extends \Magento\Framework\App\Action\Action
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
     * @var \Lof\Designer\Model\Designer
     */
    protected $_designerModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Lof\Designer\Helper\Data
     */
    protected $_designerHelper;

    /**
     * @param Context                                             $context              [description]
     * @param \Magento\Store\Model\StoreManager                   $storeManager         [description]
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    [description]
     * @param \Lof\Designer\Model\Designer                              $designerModel           [description]
     * @param \Magento\Framework\Registry                         $coreRegistry         [description]
     * @param Resolver                                            $layerResolver        [description]
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory [description]
     * @param \Lof\Designer\Helper\Data                              $designerHelper          [description]
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Designer\Model\Designer $designerModel,
        \Magento\Framework\Registry $coreRegistry,
        Resolver $layerResolver,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Lof\Designer\Helper\Data $designerHelper
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_designerModel = $designerModel;
        $this->layerResolver = $layerResolver;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_designerHelper = $designerHelper;
    }

    public function _initDesigner()
    {
        $designerId = (int)$this->getRequest()->getParam('designer_id', false);
        if (!$designerId) {
            return false;
        }
        try{
            $designer = $this->_designerModel->load($designerId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_designer', $designer);
        return $designer;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->_designerHelper->getConfig('general_settings/enable')){
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $designer = $this->_initDesigner();
        if ($designer) {
            $this->layerResolver->create('designer');
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $page = $this->resultPageFactory->create();
            // apply custom layout (page) template once the blocks are generated
            if ($designer->getPageLayout()) {
                $page->getConfig()->setPageLayout($designer->getPageLayout());
            }
            $page->addHandle(['type' => 'VES_BRAND_'.$designer->getId()]);
            if (($layoutUpdate = $designer->getLayoutUpdateXml()) && trim($layoutUpdate)!='') {
                $page->addUpdate($layoutUpdate);
            }

            /*$collectionSize = $designer->getProductCollection()->getSize();
            if($collectionSize){
                $page->addHandle(['type' => 'lofdesigner_designer_layered']);
            }*/
            $page->getConfig()->addBodyClass('page-products')
            ->addBodyClass('designer-' . $designer->getUrlKey());
            return $page;
        }elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}