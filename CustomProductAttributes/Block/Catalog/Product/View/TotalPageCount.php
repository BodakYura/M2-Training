<?php

namespace Training\CustomProductAttributes\Block\Catalog\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

class TotalPageCount extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(Context $context, Registry $registry, array $data = [])
    {
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getCurrentProduct(): Product
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return int
     */
    public function getTotalPageCount(): int
    {
        $product = $this->getCurrentProduct();

        return $product->getData('total_page_count');
    }
}