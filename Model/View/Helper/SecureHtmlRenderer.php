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

namespace Mavenbird\XmlSitemap\Model\View\Helper;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Mavenbird\XmlSitemap\Api\SecureHtmlRendererInterface;

class SecureHtmlRenderer implements SecureHtmlRendererInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ProductMetadataInterface $productMetadata
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ObjectManagerInterface $objectManager
    ) {
        $this->productMetadata = $productMetadata;
        $this->objectManager = $objectManager;
    }

    /**
     * Render tag
     *
     * @param string $tagName
     * @param array $attributes
     * @param string|null $content
     * @param boolean $textContent
     * @return void
     */
    public function renderTag(
        string $tagName,
        array $attributes,
        ?string $content = null,
        bool $textContent = true
    ) {
        $version = $this->productMetadata->getVersion();
        if (version_compare($version, '2.4.0', ">=")) {
            return $this->objectManager->get(\Magento\Framework\View\Helper\SecureHtmlRenderer::class)->renderTag($tagName, $attributes, $content, $textContent);
        } else {
            $attrs = [];
            if ($attributes) {
                foreach ($attributes as $key => $value) {
                    $attrs[] = $key . '="' . $value . '"';
                }
            }
            return '<' . $tagName . ' ' . implode(' ', $attrs) . '>' . $content . '</' . $tagName . '>';
        }
    }
}
