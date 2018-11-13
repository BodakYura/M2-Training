<?php

namespace Training\CustomProductAttributes\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Reports\Model\ResourceModel\Product\Collection;

class TotalPageCountPlugin
{
    /**
     * @var Collection
     */
    private $productCollection;

    public function __construct(Collection $productCollection)
    {
        $this->productCollection = $productCollection;
    }

    public function beforeSave(Product $subject)
    {
        $productCollection = $this->productCollection->setProductAttributeSetId($subject->getAttributeSetId());
        $prodData = $productCollection->addViewsCount()->getData();

        if (count($prodData) > 0) {
            foreach ($prodData as $product) {
                if ($product['entity_id'] === $subject->getId()) {
                    $subject->setData('total_page_count', (int)$product['views']);
                }
            }
        }
    }
}