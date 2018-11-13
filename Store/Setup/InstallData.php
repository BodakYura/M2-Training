<?php

namespace Training\Store\Setup;

use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group;
use Magento\Store\Model\ResourceModel\Store;
use Magento\Store\Model\ResourceModel\Website;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Config\Model\ResourceModel\Config;

class InstallData implements InstallDataInterface
{
    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;

    /**
     * @var Website
     */
    private $websiteResourceModel;

    /**
     * @var StoreFactory
     */
    private $storeFactory;

    /**
     * @var GroupFactory
     */
    private $groupFactory;

    /**
     * @var Group
     */
    private $groupResourceModel;

    /**
     * @var Store
     */
    private $storeResourceModel;

    /**
     * @var Config
     */
    private $scopeConfig;

    public function __construct(
        WebsiteFactory $websiteFactory,
        Website $websiteResourceModel,
        Store $storeResourceModel,
        Group $groupResourceModel,
        StoreFactory $storeFactory,
        GroupFactory $groupFactory,
        Config $scopeConfig
    )
    {
        $this->websiteFactory = $websiteFactory;
        $this->websiteResourceModel = $websiteResourceModel;
        $this->storeFactory = $storeFactory;
        $this->groupFactory = $groupFactory;
        $this->groupResourceModel = $groupResourceModel;
        $this->storeResourceModel = $storeResourceModel;
        $this->scopeConfig = $scopeConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $website = $this->websiteFactory->create();
        $website->load('uawebsite');

        if (!$website->getId()) {
            $website->setCode('uawebsite');
            $website->setName('UaWebsite');

            $this->websiteResourceModel->save($website);
        }


        if ($website->getId()) {

            $this->scopeConfig->saveConfig(
                Custom::XML_PATH_GENERAL_LOCALE_CODE,
                'uk_UA',
                ScopeInterface::SCOPE_WEBSITES,
                $website->getId()
            );

            $this->scopeConfig->saveConfig(
                Custom::XML_PATH_GENERAL_LOCALE_TIMEZONE,
                'Europe/Kiev',
                ScopeInterface::SCOPE_WEBSITES,
                $website->getId()
            );

            $this->scopeConfig->saveConfig(
                Custom::XML_PATH_GENERAL_COUNTRY_DEFAULT,
                'UA',
                ScopeInterface::SCOPE_WEBSITES,
                $website->getId()
            );

            $group = $this->groupFactory->create();
            $group->setWebsiteId($website->getId());
            $group->setCode('uawebsitegroup');
            $group->setName('UaWebsiteGroup');

            $this->groupResourceModel->save($group);
        }

        $store = $this->storeFactory->create();
        $store->load('uastore');

        if (!$store->getId()) {
            $store->setCode('uastore');
            $store->setName('UaStore');
            $store->setWebsite($website);
            $store->setGroupId($group->getId());
            $store->setData('is_active', '1');

            $this->storeResourceModel->save($store);
        }

        $setup->endSetup();
    }

}