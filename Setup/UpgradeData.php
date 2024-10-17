<?php

namespace LimeSoda\Cashpresso\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;

class UpgradeData implements UpgradeDataInterface
{
    private WriterInterface $configWriter;

    private ReinitableConfigInterface $reinitConfig;

    /**
     * Constructor
     *
     * @param WriterInterface $configWriter
     * @param ReinitableConfigInterface $reinitConfig
     */
    public function __construct(
        WriterInterface $configWriter,
        ReinitableConfigInterface $reinitConfig
    ) {
        $this->configWriter = $configWriter;
        $this->reinitConfig = $reinitConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.17', '<')) {
            $configPaths = [
                'payment/cashpresso/account',
                'payment/cashpresso/account_source'
            ];

            foreach ($configPaths as $configPath) {
                $this->configWriter->delete($configPath);
            }

            $this->reinitConfig->reinit();
        }

        $setup->endSetup();
    }
}