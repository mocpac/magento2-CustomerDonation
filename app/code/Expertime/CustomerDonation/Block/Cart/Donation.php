<?php

/**
 * @author Jo Ng <ngkwokwah@gmail.com>
 */

namespace Expertime\CustomerDonation\Block\Cart;

use Magento\Store\Model\ScopeInterface;

/**
 * Block on checkout/cart/index page to display a donation item, including below fields on frontend
 * - image of the product entered in the configuration
 * - title added from the configuration
 * - description added from the configuration
 * - "Amount" field to enter the amount of the donation
 * - "Donate" or "Cancel my donation" button
 */
class Donation extends \Magento\Checkout\Block\Cart
{
    /**
     * Config settings path to determine when Donation section will be visible
     */
    const XPATH_CONFIG_ACTIVE = 'expertime_donation/general/active';

    /**
     * SKU of donated product
     */
    const XPATH_CONFIG_PRODUCT_SKU = 'expertime_donation/general/sku';

    /**
     * Title of the donation
     */
    const XPATH_CONFIG_TITLE = 'expertime_donation/general/title';

    /**
     * Description (with WYSIWYG)
     */
    const XPATH_CONFIG_DESCRIPTION = 'expertime_donation/general/desc';

    /**
     * Default donation amount
     */
    const XPATH_CONFIG_AMOUNT = 'expertime_donation/general/amount';

    protected $productRepository;
    protected $imageHelper;

    /**
     * Donation constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $httpContext,
            $data
        );
    }

    public function isVisible()
    {
        return  (bool)$this->_scopeConfig->getValue(
            self::XPATH_CONFIG_ACTIVE,
            ScopeInterface::SCOPE_STORE
        ) && !empty($this->_scopeConfig->getValue(
            self::XPATH_CONFIG_PRODUCT_SKU,
            ScopeInterface::SCOPE_STORE
        ));
    }

    public function getProductSku()
    {
        return $this->_scopeConfig->getValue(
            self::XPATH_CONFIG_PRODUCT_SKU,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getProductImageUrl()
    {
        try {
            $product = $this->productRepository->get($this->getProductSku());
            $url = $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
            return $url;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    public function getTitle()
    {
        return $this->_scopeConfig->getValue(
            self::XPATH_CONFIG_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getDescription()
    {
        return $this->_scopeConfig->getValue(
            self::XPATH_CONFIG_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAmount()
    {
        return doubleval($this->_scopeConfig->getValue(
            self::XPATH_CONFIG_AMOUNT,
            ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     *  Check if the Donation product is already placed in cart
     */
    public function hasDonated()
    {
        foreach (parent::getItems() as $item) {
            if ($this->getProductSku() == $item->getSku()) {
                return true;
            }
        }

        return false;
    }
}
