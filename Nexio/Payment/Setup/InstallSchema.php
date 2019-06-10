<?php
namespace Nexio\Payment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup(); 
        
        $data[] = ['status' => 'nexio_paid', 'label' => 'Paid in Nexio'];
        $data[] = ['status' => 'nexio_auth', 'label' => 'Authorized by Nexio'];
        
        $setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);

        $setup->getConnection()->insertArray(
        $setup->getTable('sales_order_status_state'),
        ['status', 'state', 'is_default','visible_on_front'],
        [
            ['nexio_auth','processing', '0', '1'], 
            ['nexio_paid', 'processing', '0', '1']
        ]
        );

        $setup->endSetup();
    }
}
