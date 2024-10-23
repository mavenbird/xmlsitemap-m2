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

namespace Mavenbird\XmlSitemap\Model;

use Mavenbird\XmlSitemap\Api\HyvaThemeDetectionInterface;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

class HyvaThemeDetection implements HyvaThemeDetectionInterface
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var array
     */
    private $result;

    /**
     * @param ModuleManager $moduleManager
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ThemeProviderInterface $themeProvider
     */
    public function __construct(
        ModuleManager $moduleManager,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider
    ) {
        $this->moduleManager = $moduleManager;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
    }

    /**
     * Execute
     *
     * @param [type] $storeId
     * @return boolean
     */
    public function execute($storeId = null): bool
    {
        $key = 'store_' . $storeId;
        if (isset($this->result[$key])) {
            return $this->result[$key];
        }

        $hyvaThemeEnabled = $this->moduleManager->isEnabled('Hyva_Theme');

        if ($hyvaThemeEnabled) {

            if (null === $storeId) {
                $storeId = $this->storeManager->getStore()->getId();
            }

            if (!$storeId) {
                $stores = $this->storeManager->getStores();
                foreach ($stores as $store) {
                    if ($this->isHyvaThemeInUse($store->getId())) {
                        $this->result[$key] = true;
                        return $this->result[$key];
                    }
                }
            } else {
                $this->result[$key] = $this->isHyvaThemeInUse($storeId);
                return $this->result[$key];
            }
        }
        $this->result[$key] = false;
        return $this->result[$key];
    }

    /**
     * Hyva theme in use
     *
     * @param [type] $storeId
     * @return boolean
     */
    private function isHyvaThemeInUse($storeId)
    {
        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($themeId) {
            try {
                $theme = $this->themeProvider->getThemeById($themeId);
            } catch (\Exception $e) {
                $theme = false;
            }

            while ($theme) {
                $themePath = $theme->getThemePath();
                if (false !== stripos($themePath, 'h' . 'y' . 'v' . 'a')) {
                    return true;
                }

                $theme = $theme->getParentTheme();
            }
        }
        return false;
    }
}
