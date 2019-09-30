<?php
/*
delete from setup_module where  module ='module_name';
sudo bin/magento setup:upgrade
sudo bin/magento setup:di:compile
sudo bin/magento cache:clean
*/
namespace Revolve\Mage25\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Customer\Setup\CustomerSetupFactory;


class InstallSchema implements InstallSchemaInterface
{
	private $customerSetupFactory;
	
	
	public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
      
    }
	
    /**
    https://webkul.com/blog/magento2-write-custom-mysql-query/
    http://www.mage-world.com/blog/create-a-module-with-custom-database-table-in-magento-2.html
    **/
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	  
	   $installer = $setup;
        $installer->startSetup();
        
        
        if (! $installer->tableExists('mage25_areas')) {
	   
	    		$mage25_areas = $installer->getConnection()->newTable(
                $installer->getTable('mage25_areas')
			 )
			 ->addColumn(
                    'arID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Area ID'
                )
                ->addColumn(
                    'cID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Collection Name'
                )
                 ->addColumn(
                    'arHandle',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Area Handle'
                )->setComment('Mage25 Area Table');
            $installer->getConnection()->createTable($mage25_areas);
            
           }
           
          if (! $installer->tableExists('mage25_area_blocks')) {
	          $mage25_area_blocks = $installer->getConnection()->newTable(
                $installer->getTable('mage25_area_blocks')
			 )
			 ->addColumn(
                    'bID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Block ID'
                )
                ->addColumn(
                    'btID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Block Type ID'
                )
                ->addColumn(
                    'arID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Area ID'
                )
                 ->addColumn(
                    'fileName',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'File Name'
                )
                 ->addColumn(
                    'trash',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    [],
                    'Trash'
                )
                  ->addColumn(
                    'date_deleted',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'date Deleted'
                )
                 ->addColumn(
                    'sort_order',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Sort Order'
                )->setComment('Mage25 Area Table');
            $installer->getConnection()->createTable($mage25_area_blocks);
	     }
	     
	     
	      if (! $installer->tableExists('mage25_block_types')) {
	          $mage25_block_types = $installer->getConnection()->newTable(
                $installer->getTable('mage25_block_types')
			 )
			  ->addColumn(
                    'btID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Block Type ID'
                )
                 ->addColumn(
                    'btHandle',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Block Type handle'
                )
                 ->addColumn(
                    'btName',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Block Type Name'
                )
                ->addColumn(
                    'btDescription',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                    null,
                    ['nullable => false'],
                    'Block Type Description'
                )
                 ->addColumn(
                    'btInterfaceWidth',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Block Popup Width'
                )
                 ->addColumn(
                    'btInterfaceHeight',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Block Popup Height'
                )
                ->addColumn(
                    'pkgID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Package ID'
                )
                 ->addColumn(
                    'btActive',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    [],
                    'Package ID'
                )
                 ->addColumn(
                    'btTable',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Block Type Table'
                )
			->setComment('Mage25 Block Types');
            $installer->getConnection()->createTable($mage25_block_types);
	     }
	     
	     
           if (! $installer->tableExists('mage25_files')) {
	          $mage25_files = $installer->getConnection()->newTable(
                $installer->getTable('mage25_files')
			 )
			  ->addColumn(
                    'fID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'File ID'
                )
                  ->addColumn(
                    'fName',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'File Name'
                )
                 ->addColumn(
                    'fDescription',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                    null,
                    ['nullable => false'],
                    'File Description'
                )
                  ->addColumn(
                    'dateAdded',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Date Added'
                )
                ->addColumn(
                    'uID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'User ID'
                )
                 ->addColumn(
                    'fPath',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                    null,
                    ['nullable => false'],
                    'File Path'
                )
            
            ->setComment('Mage25 Files');
            $installer->getConnection()->createTable($mage25_files);
	     }
            
              if (! $installer->tableExists('mage25_file_sets')) {
	          $mage25_file_sets = $installer->getConnection()->newTable(
                $installer->getTable('mage25_file_sets')
			 )
			  ->addColumn(
                    'fsID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'File Set ID'
                )   
                  ->addColumn(
                    'fsName',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'File Set Name'
                )
     
			 ->setComment('Mage25 Filesets');
            $installer->getConnection()->createTable($mage25_file_sets);
	     }
	     
	     
	      if (! $installer->tableExists('mage25_file_set_files')) {
	          $mage25_file_set_files = $installer->getConnection()->newTable(
                $installer->getTable('mage25_file_set_files')
			 )
			   ->addColumn(
                    'fsID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'File Set ID'
                )
                 ->addColumn(
                    'fID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'File ID'
                )
                
                 ->addColumn(
                    'sort_order',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Sort Order'
                )->setComment('Mage25 Fileset Files');
			 $installer->getConnection()->createTable($mage25_file_set_files);
	     }
	     
	     //have to define mage25_block_type_templates table Structure too
	     
	     
	      if (! $installer->tableExists('mage25_vendor_templates')) {
	          $mage25_vendor_templates = $installer->getConnection()->newTable(
                $installer->getTable('mage25_vendor_templates')
			 )
			   ->addColumn(
                    'btID',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Block type ID'
                )->addColumn(
                    'filename',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Template file name'
                )->setComment('Mage25 Vendor Templates');
			 
			 $installer->getConnection()->createTable($mage25_vendor_templates);
	     }
        
        
       $installer->endSetup();
    }
    
    

}
