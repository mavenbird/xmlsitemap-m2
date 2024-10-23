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

namespace Mavenbird\XmlSitemap\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ProductMetadataInterface;

class Section
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public const MODULE = 'mfmodule';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public const ENABLED = 'enabled';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public const KEY = 'key';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public const TYPE = 'mftype';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Mavenbird\XmlSitemap\Model\GetModuleVersion
     */
    private $getModuleVersion;

    /**
     * @var \Mavenbird\XmlSitemap\Model\HyvaThemeDetection
     */

    private $hyvaThemeDetection;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $key;

    /**
     * @var ProductMetadataInterface
     */
    protected $metadata;

   /**
    * Constructor
    *
    * @param ScopeConfigInterface $scopeConfig
    * @param ProductMetadataInterface $metadata
    * @param GetModuleVersion $getModuleVersion
    * @param HyvaThemeDetection $hyvaThemeDetection
    * @param [type] $name
    * @param [type] $key
    */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductMetadataInterface $metadata,
        GetModuleVersion $getModuleVersion,
        HyvaThemeDetection $hyvaThemeDetection,
        $name = null,
        $key = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->metadata = $metadata;
        $this->getModuleVersion = $getModuleVersion;
        $this->hyvaThemeDetection = $hyvaThemeDetection;
        $this->name = $name;
        $this->key = $key;
    }

    /**
     * Is enable
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) $this->getConfig(self::ENABLED);
    }

    /**
     * Get module
     *
     * @param boolean $e
     * @return void
     */
    public function getModule($e = false)
    {
        $fs = $e ? [self::MODULE] : [self::MODULE . 'e', self::MODULE . 'p', self::MODULE];
        foreach ($fs as $f) {
            $module = (string)$this->getConfig($f);
            if ($module) {
                break;
            }
        }
        $url = $this->scopeConfig->getValue(
            'web/unsecure/base' . '_' . 'url',
            ScopeInterface::SCOPE_STORE,
            0
        );

        if (\Mavenbird\XmlSitemap\Model\UrlChecker::showUrl($url)) {
            if ($module
                && (!$this->getConfig(self::TYPE)
                    || $this->getConfig(self::TYPE) && $this->metadata->getEdition() != 'C' . 'omm' . 'un' . 'ity'
                )
            ) {
                return $module;
            }

            if ($module == ('B' . 'l' . 'o' . 'g')
                && version_compare($this->getModuleVersion->execute('Ma' . 'ge' . 'fa' . 'n_' . $module), '2.' . '11' . '.4', '>=')
                && $this->hyvaThemeDetection->execute()
            ) {
                return $module;
            }
        }
        return false;
    }

    /**
     * Get key
     *
     * @return void
     */
    public function getKey()
    {
        if (null !== $this->key) {
            return $this->key;
        } else {
            return $this->getConfig(self::KEY);
        }
    }

    /**
     * Get name
     *
     * @return void
     */
    public function getName()
    {
        return (string) $this->name;
    }

    /**
     * Validate
     *
     * @param [type] $data
     * @return void
     */
    public function validate($data)
    {
        if (isset($data[$this->getModule()])) {
            return !empty($data[$this->getModule()]);
        }

        $k = $this->getKey();

        foreach ([$this->getModule(), $this->getModule(true)] as $id) {
            foreach (['', 'Plus', 'Extra'] as $e) {
                if ($result = $this->validateIDK($id . $e, $k)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validate key
     *
     * @param [type] $id
     * @param [type] $k
     * @return void
     */
    private function validateIDK($id, $k)
    {
        $l = substr($id, 1, 1);
        $d = (string) strlen($id);

        return (strlen($k) >= '3' . '2')
            && (strpos($k, $l, 5) == 5)
            && (strpos($k, $d, 19) == 19);
    }

    /**
     * Get config value
     *
     * @param [type] $field
     * @return void
     */
    private function getConfig($field)
    {
        $g = 'general';
        return $this->scopeConfig->getValue(
            implode('/', [$this->name, $g, $field]),
            ScopeInterface::SCOPE_STORE,
            0
        );
    }
}
