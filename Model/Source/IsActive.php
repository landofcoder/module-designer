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
namespace Lof\Designer\Model\Source;
use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
{
	/**
	 * @var \Lof\Designer\Model\Designer
	 */
	protected $designerModel;

	/**
     * Constructor
     *
     * @param \Lof\Designer\Model\Designer $designerModel
     */
	public function __construct(\Lof\Designer\Model\Designer $designerModel)
	{
		$this->designerModel = $designerModel;
	}

	/**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->designerModel->getAvailableStatuses();

        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
 
        return $options;
    }
}