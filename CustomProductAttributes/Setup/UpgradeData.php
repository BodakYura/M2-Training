<?php

namespace Training\CustomProductAttributes\Setup;

use Magento\Catalog\Model\Config;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\AttributeSetRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    private $attributeSetFactory;
    private $categorySetupFactory;
    private $attributeSetRepository;
    private $attributeManager;
    private $searchCriteriaBuilder;
    private $config;

    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory,
        AttributeSetRepository $attributeSetRepository,
        AttributeManagementInterface $attributeManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    )
    {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->attributeManager = $attributeManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $attributeSetBagId = null;
        $attributeSetBagNewId = null;
        $totalPageCountAttributeId = null;

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $attributeSet = $this->attributeSetFactory->create();

            try {
                $entityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);
            } catch (\Exception $ex) {
                return $ex->getMessage();
            }

            $searchCriteria = $this->searchCriteriaBuilder->addFilter('attribute_set_name', 'Bag')->create();
            $attributeSetBag = $this->attributeSetRepository->getList($searchCriteria);

            foreach ($attributeSetBag->getItems() as $value) {
                $attributeSetBagId = $value->getId();
            }

            $data = [
                'attribute_set_name' => 'BagNew',
                'entity_type_id' => $entityTypeId,
                'sort_order' => 200,
            ];

            $attributeSet->setData($data);

            try {
                $this->attributeSetRepository->save($attributeSet);
            } catch (\Exception $ex) {
                return $ex->getMessage();
            }

            if ($attributeSetBagId) {
                $attributeSet->initFromSkeleton($attributeSetBagId);

                try {
                    $attributeSetBagNewId = $this->attributeSetRepository->save($attributeSet)->getAttributeSetId();
                } catch (\Exception $ex) {
                    return $ex->getMessage();
                }
            }

            $groupId = $this->config->getAttributeGroupId($attributeSetBagNewId, 'General');

            try {
                $this->attributeManager->assign(Product::ENTITY, $attributeSetBagNewId, $groupId, 'total_page_count', 200);
            } catch (\Exception $ex) {
                return $ex->getMessage();
            }
        }

        $setup->endSetup();
    }
}