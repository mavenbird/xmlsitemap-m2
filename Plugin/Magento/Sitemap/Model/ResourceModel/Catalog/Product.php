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

namespace Mavenbird\XmlSitemap\Plugin\Magento\Sitemap\Model\ResourceModel\Catalog;

use Magento\Sitemap\Model\ResourceModel\Catalog\Product as Subject;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Mavenbird\XmlSitemap\Model\Config;

class Product
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Config $config
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        Config $config
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config = $config;
    }

    /**
     * After get collection
     *
     * @param Subject $subject
     * @param array $result
     * @return array
     */
    public function afterGetCollection(Subject $subject, array $result): array
    {

        if ($result && $this->config->isEnabled()) {
            $productCollection = $this->productCollectionFactory->create()
                ->addFieldToFilter('mf_exclude_xml_sitemap', ['eq' => 1]);

            if ($productCollection) {
                $excludedIds = array_flip($productCollection->getAllIds());

                foreach ($result as $key => $item) {
                    if (isset($excludedIds[(int)$item->getId()])) {
                        unset($result[(int)$key]);
                    }
                }
            }
        }

        return $result;
    }
}
