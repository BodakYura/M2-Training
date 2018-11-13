<?php

namespace Training\CustomProductAttributes\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\{InstallDataInterface, ModuleContextInterface, ModuleDataSetupInterface};
use Magento\Catalog\Api\Data\ProductAttributeInterface;

/**
 * Class InstallData
 * @package Training\CustomProductAttributes\Setup
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }


    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $attributeCode = 'total_page_count';

        $entityType = ProductAttributeInterface::ENTITY_TYPE_CODE;

        $eavSetup->addAttribute($entityType, $attributeCode, [
            'label' => 'Total page count',
            'type' => 'int',
            'input' => 'text',
            'required' => true,
            'visible' => true,
            'visible_on_front' => true,
            'group' => 'General',
            'system' => true,
            'sort_order' => 100,
            'user_defined' => false,
            'frontend_class' => 'validate-number'
        ]);


        $setup->endSetup();
    }

}