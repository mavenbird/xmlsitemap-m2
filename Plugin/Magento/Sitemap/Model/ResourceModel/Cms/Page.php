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

namespace Mavenbird\XmlSitemap\Plugin\Magento\Sitemap\Model\ResourceModel\Cms;

use Magento\Sitemap\Model\ResourceModel\Cms\Page as Subject;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Mavenbird\XmlSitemap\Model\Config;

class Page
{
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepositoryInterface;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param PageRepositoryInterface $pageRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $config
     */
    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->pageRepositoryInterface = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
    }

    /**
     * After getCollection
     *
     * @param Subject $subject
     * @param array $result
     * @return array
     */
    public function afterGetCollection(Subject $subject, array $result): array
    {
        if ($result && $this->config->isEnabled()) {

            $searchCriteria = $this->searchCriteriaBuilder->addFilter('mf_exclude_xml_sitemap', 1, 'eq')->create();
            $excludedPages = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();

            foreach ($result as $key => $page) {
                $key = (int)$key;
                if (isset($excludedPages[$key])) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }
}
