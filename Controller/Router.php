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
namespace Lof\Designer\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;

class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * Designer Factory
     *
     * @var \Lof\Designer\Model\Designer $designerCollection
     */
    protected $_designerCollection;

    /**
     * Designer Factory
     *
     * @var \Lof\Designer\Model\Group $groupCollection
     */
    protected $_groupCollection;

    /**
     * Designer Helper
     */
    protected $_designerHelper;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ActionFactory          $actionFactory   
     * @param ResponseInterface      $response        
     * @param ManagerInterface       $eventManager    
     * @param \Lof\Designer\Model\Designer $designerCollection 
     * @param \Lof\Designer\Model\Group $groupCollection 
     * @param \Lof\Designer\Helper\Data $designerHelper     
     * @param StoreManagerInterface  $storeManager    
     */
    public function __construct(
    	ActionFactory $actionFactory,
    	ResponseInterface $response,
        ManagerInterface $eventManager,
        \Lof\Designer\Model\Designer $designerCollection,
        \Lof\Designer\Model\Group $groupCollection,
        \Lof\Designer\Helper\Data $designerHelper,
        StoreManagerInterface $storeManager
        )
    {
    	$this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->response = $response;
        $this->_designerHelper = $designerHelper;
        $this->_designerCollection = $designerCollection;
        $this->_groupCollection = $groupCollection;
        $this->storeManager = $storeManager;
    }
    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $_designerHelper = $this->_designerHelper;
        if (!$this->dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'lof_designer_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
                );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                    );
            }
            if (!$condition->getContinue()) {
                return null;
            }
            $route = $_designerHelper->getConfig('general_settings/route');
            $orgRouter = $route;
            $urlKeyArr = explode(".",$urlKey);
            $orig_url_suffix = "";
            if(count($urlKeyArr) > 1) {
                $urlKey = $urlKeyArr[0];
                $orig_url_suffix = $urlKeyArr[1];
            }
            $routeArr = explode(".",$route);
            if(count($routeArr) > 1) {
                $route = $routeArr[0];
            }
            if( $route !='' && $urlKey == $route )
            {
                if($origUrlKey == $orgRouter){
                    $request->setModuleName('lofdesigner')
                    ->setControllerName('index')
                    ->setActionName('index');
                    $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                    $this->dispatched = true;
                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                        );
                } else {
                    return null;
                }
            }
            $url_prefix = $_designerHelper->getConfig('general_settings/url_prefix');
            $url_suffix = $_designerHelper->getConfig('general_settings/url_suffix');
            $url_prefix = $url_prefix?$url_prefix:$route;
            $identifiers = explode('/',$urlKey);

            // SEARCH PAGE
            if(count($identifiers)==2 && $url_prefix == $identifiers[0] && $identifiers[1]=='search'){
                $request->setModuleName('lofdesigner')
                ->setControllerName('search')
                ->setActionName('result');
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }
            //Check Group Url
            if( (count($identifiers) == 2 && $identifiers[0] == $url_prefix) || (trim($url_prefix) == '' && count($identifiers) == 1)){
                $designerUrl = '';
                if( ($url_suffix && $url_suffix != $orig_url_suffix) || (!$url_suffix && $orig_url_suffix)){
                    return null;
                }
                if(trim($url_prefix) == '' && count($identifiers) == 1){
                    $designerUrl = str_replace($url_suffix, '', $identifiers[0]);
                }
                if(count($identifiers) == 2){
                    $designerUrl = str_replace($url_suffix, '', $identifiers[1]);
                }
                if ($designerUrl) {
                    $group = $this->_groupCollection->getCollection()
                    ->addFieldToFilter('status', array('eq' => 1))
                    ->addFieldToFilter('url_key', array('eq' => $designerUrl))
                    ->getFirstItem();

                    if($group && $group->getId()){
                        $request->setModuleName('lofdesigner')
                        ->setControllerName('group')
                        ->setActionName('view')
                        ->setParam('group_id', $group->getId());
                        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                        $request->setDispatched(true);
                        $this->dispatched = true;
                        return $this->actionFactory->create(
                            'Magento\Framework\App\Action\Forward',
                            ['request' => $request]
                            );
                    } else {
                        $designer = $this->_designerCollection->getCollection()
                                ->addFieldToFilter('status', array('eq' => 1))
                                ->addFieldToFilter('url_key', array('eq' => $designerUrl))
                                ->addStoreFilter([0,$this->storeManager->getStore()->getId()])
                                ->getFirstItem();

                        if($designer && $designer->getId()){
                            $request->setModuleName('lofdesigner')
                            ->setControllerName('designer')
                            ->setActionName('view')
                            ->setParam('designer_id', $designer->getId());
                            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                            $request->setDispatched(true);
                            $this->dispatched = true;
                            return $this->actionFactory->create(
                                'Magento\Framework\App\Action\Forward',
                                ['request' => $request]
                                );
                        }

                    }
                }
            }
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
            $request->setDispatched(true);
            $this->dispatched = true;
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
                );
        }
    }
}