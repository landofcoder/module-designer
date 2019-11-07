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

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;

use Lof\Designer\Model\Group as GroupModel;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PageRepository  */
    protected $groupRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /** @var groupModel */
    protected $groupModel;

    /**
     * @param Context        $context         
     * @param PageRepository $groupRepository 
     * @param JsonFactory    $jsonFactory     
     * @param GroupModel     $groupModel      
     */
    public function __construct(
        Context $context,
        PageRepository $groupRepository,
        JsonFactory $jsonFactory,
        GroupModel $groupModel
        ) {
        parent::__construct($context);
        $this->pageRepository = $groupRepository;
        $this->jsonFactory = $jsonFactory;
        $this->groupModel = $groupModel;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
                ]);
        }

        foreach (array_keys($postItems) as $groupId) {
            /** @var \Lof\Designer\Model\Group $group */
            $group = $this->_objectManager->create('Lof\Designer\Model\Group');
            $groupData = $postItems[$groupId];

            try {
                $group->load($groupId);
                $group->setData(array_merge($group->getData(), $groupData));
                $group->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithgroupId($group, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithgroupId($group, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithgroupId(
                    $group,
                    __('URL key already exists.')
                    );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
            ]);
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $group
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithgroupId($group, $errorText)
    {
        return '[Page ID: ' . $group->getId() . '] ' . $errorText;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Designer::group_save');
    }
}