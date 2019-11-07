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
namespace Lof\Designer\Controller\Adminhtml\Designer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;

use Lof\Designer\Model\Designer as DesignerModel;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PageRepository  */
    protected $designerRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /** @var designerModel */
    protected $designerModel;

    /**
     * @param Context $context
     * @param PageRepository $designerRepository
     * @param JsonFactory $jsonFactory
     * @param Lof\Designer\Model\Designer $designerModel
     */
    public function __construct(
        Context $context,
        PageRepository $designerRepository,
        JsonFactory $jsonFactory,
        DesignerModel $designerModel
        ) {
        parent::__construct($context);
        $this->pageRepository = $designerRepository;
        $this->jsonFactory = $jsonFactory;
        $this->designerModel = $designerModel;
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

        foreach (array_keys($postItems) as $designerId) {
            /** @var \Lof\Designer\Model\Group $designer */
            $designer = $this->_objectManager->create('Lof\Designer\Model\Designer');
            $designerData = $postItems[$designerId];

            try {
                $designer->load($designerId);
                $designer->setData(array_merge($designer->getData(), $designerData));
                $designer->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithgroupId($designer, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithgroupId($designer, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPageId(
                    $page,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => 'abc',
            'error' => 'def'
            ]);
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $designer
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithgroupId($designer, $errorText)
    {
        return '[Page ID: ' . $designer->getId() . '] ' . $errorText;
    }
}