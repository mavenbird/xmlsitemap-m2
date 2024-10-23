<?php
/**
 * Mavenbird Technologies Private Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mavenbird.com/Mavenbird-Module-License.txt
 *
 * =================================================================
 *
 * @category   Mavenbird
 * @package    Mavenbird_XmlSitemap
 * @author     Mavenbird Team
 * @copyright  Copyright (c) 2018-2024 Mavenbird Technologies Private Limited ( http://mavenbird.com )
 * @license    http://mavenbird.com/Mavenbird-Module-License.txt
 */

declare(strict_types=1);

namespace Mavenbird\XmlSitemap\Setup\Patch\Data;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class PageToExclude implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var PageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PageCollectionFactory $pageCollectionFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PageCollectionFactory   $pageCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * Apply
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $pageCollection = $this->pageCollectionFactory->create();
        $connection = $pageCollection->getConnection();
        $connection->update(
            $pageCollection->getTable('cms_page'),
            ['mf_exclude_xml_sitemap' => 1],
            ['identifier IN (?)' => ['no-route', 'home', 'enable-cookies']]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Revert
     *
     * @return void
     */
    public function revert()
    {
    }

    /**
     * Get Aliases
     *
     * @return void
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return void
     */
    public static function getDependencies()
    {
        return [];
    }
}
