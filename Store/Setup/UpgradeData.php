<?php

namespace Training\Store\Setup;


use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product;

class UpgradeData implements UpgradeDataInterface
{

    private $productFactory;
    private $storeFactory;
    private $websiteFactory;
    private $productRepository;
    private $productResourceModel;
    private $state;

    public function __construct(
        ProductFactory $productFactory,
        StoreFactory $storeFactory,
        WebsiteFactory $websiteFactory,
        Product $productResourceModel,
        ProductRepository $productRepository,
        State $state)
    {
        $this->productFactory = $productFactory;
        $this->storeFactory = $storeFactory;
        $this->websiteFactory = $websiteFactory;
        $this->productRepository = $productRepository;
        $this->productResourceModel = $productResourceModel;
        $this->state = $state;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            $products = [
                [
                    'name' => 'Vans Wm Realm Flying V Ba Black and White',
                    'sku' => '53324742',
                    'price' => 1699,
                    'total_page_count' => 5,
                    'uastore' => [
                        'name' => 'Vans Wm Realm Flying V Ba Чорно-білий',
                        'price' => 1799
                    ]
                ],
                [
                    'name' => 'ZiBi Ultimo BonAir Black',
                    'sku' => '11984988',
                    'price' => 1499,
                    'total_page_count' => 5,
                    'uastore' => [
                        'name' => 'ZiBi Ultimo BonAir Чорний',
                        'price' => 1599
                    ]
                ],
                [
                    'name' => 'Bobby compact anti-theft backpack blue',
                    'sku' => '28812913',
                    'price' => 1233,
                    'total_page_count' => 5,
                    'uastore' => [
                        'name' => 'Bobby compact anti-theft backpack cиній',
                        'price' => 1266
                    ]
                ]
            ];

            try {
                $this->state->setAreaCode(Area::AREA_ADMINHTML);
            } catch (LocalizedException $ex) {
                return $ex->getMessage();
            }

            $base = $this->websiteFactory->create();
            $base->load('base');

            $uawebsite = $this->websiteFactory->create();
            $uawebsite->load('uawebsite');

            $uastore = $this->storeFactory->create();
            $uastore->load('uastore');

            foreach ($products as $product) {
                $newProduct = $this->productFactory->create();
                $newProduct->setAttributeSetId(15);
                $newProduct->setName($product['name']);
                $newProduct->setSku($product['sku']);
                $newProduct->setPrice($product['uastore']['price']);
                $newProduct->setData('total_page_count', $product['total_page_count']);
                $newProduct->setWebsiteIds([$base->getId(), $uawebsite->getId()]);

                try {
                    $result = $this->productRepository->save($newProduct);
                } catch (\Exception $ex) {
                    return $ex->getMessage();
                }

                try {
                    $editProduct = $this->productFactory->create();
                    $this->productResourceModel->load($editProduct, $result->getId());
                    $editProduct->setStoreId($uastore->getId());
                    $editProduct->setName($product['uastore']['name']);
                    $editProduct->setPrice($product['uastore']['price']);

                    try {
                        $this->productResourceModel->saveAttribute($editProduct, 'price');
                        $this->productResourceModel->saveAttribute($editProduct, 'name');
                    } catch (\Exception $ex) {
                        return $ex->getMessage();
                    }

                } catch (NoSuchEntityException $ex) {
                    return $ex->getMessage();
                }

            }
        }

        $setup->endSetup();
    }
}