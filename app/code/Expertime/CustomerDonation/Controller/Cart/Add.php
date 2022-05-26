<?php

/**
 * @author Jo Ng <ngkwokwah@gmail.com>
 */

namespace Expertime\CustomerDonation\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class to handle "Add product to cart" or "Remove product from cart" request
 */
class Add extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    const CART_INDEX_URL = 'checkout/cart/';

    /** @var \Magento\Checkout\Model\SessionFactory */
    private $checkoutSession;

    /** @var \Magento\Quote\Api\CartRepositoryInterface */
    private $cartRepository;

    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $productRepository;

    /** @var  \Magento\Framework\Data\Form\FormKey\Validator */
    private $formKeyValidator;

    /** @var \Expertime\CustomerDonation\Block\Cart\Donation */
    private $donation;

    /**
     * AddToCart constructor.
     * @param Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Expertime\CustomerDonation\Block\Cart\Donation $donation
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Expertime\CustomerDonation\Block\Cart\Donation $donation
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->donation = $donation;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect(self::CART_INDEX_URL);
        }

        try {
            $product = $this->productRepository->get($this->donation->getProductSku());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->_redirect(self::CART_INDEX_URL);
        }
        $qty = 1;
        $price = doubleval($this->getRequest()->getParam('donation-amt'));
        $isRemove = (bool)$this->getRequest()->getParam('remove');
        $session = $this->checkoutSession->create();
        $quote = $session->getQuote();
        if ($isRemove) {
            // Remove product from cart
            $item = $quote->getItemByProduct($product);
            $item->delete();
        } else {
            // Add product to cart
            $quote->addProduct($product, $qty);
            $item = $quote->getItemByProduct($product);
            if ($price > 0) {
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->getProduct()->setIsSuperMode(true);
            }
        }

        $this->cartRepository->save($quote);
        $session->replaceQuote($quote)->unsLastRealOrderId();

        return $this->_redirect(self::CART_INDEX_URL);
    }
}
