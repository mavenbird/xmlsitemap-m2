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

namespace Mavenbird\XmlSitemap\Plugin\Mavenbird\Blog\Model\ResourceModel\Post;

use Mavenbird\XmlSitemap\Model\Config;

class Collection
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Before load
     *
     * @param [type] $subject
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return void
     */
    public function beforeLoad($subject, $printQuery = false, $logQuery = false)
    {
        if ($this->config->isEnabled()) {
            $backTrace = \Magento\Framework\Debug::backtrace(true, true, false);

            if (false !== strpos($backTrace, \Magento\Sitemap\Model\Sitemap::class)) {
                $subject->addFieldToFilter('mf_exclude_xml_sitemap', ['neq' => 1]);
            }
        }
    }
}
