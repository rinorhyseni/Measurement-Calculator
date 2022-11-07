<?php

namespace MeasurementCalculator\Service;

use Shopware\Bundle\StoreFrontBundle\Service\PriceCalculationServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

class MeasurementCalculatorService implements PriceCalculationServiceInterface
{
    /**
     * @var PriceCalculationServiceInterface
     */
    private $decoratedService;

    /**
     * @param PriceCalculationServiceInterface $service
     */
    public function __construct(PriceCalculationServiceInterface $service)
    {
        $this->decoratedService = $service;
    }

    /**
     * @param Struct\ListProduct $product
     * @param Struct\ProductContextInterface $context
     */
    public function calculateProduct(Struct\ListProduct $product, Struct\ProductContextInterface $context) {

        // Get the customergroup of the currently logged in user
        $customerGroup = $context->getCurrentCustomerGroup();

        // Calculate some random price between 90 and 99
        $randomPrice = rand(900, 999);
        $randomPrice = 100;

        // Delete all prices except the first one, cause we don´t have price rules on
        // a self calculated price. Then caluclate the correct price and set it.
        $priceRules = array();

        $unit = new Struct\Product\Unit();
        $priceRule = new Struct\Product\PriceRule();
        $priceRule->setPrice($randomPrice);             // @TODO Set newly calculated NET price here
        $priceRule->setPseudoPrice($randomPrice+100);   // @TODO Set original price here
        $priceRule->setCustomerGroup($customerGroup);
        $priceRule->setFrom(0);
        $priceRule->setTo(null);
        $priceRule->setUnit($unit);

        // Assign manipulated pricerules to product
        $priceRules[] = $priceRule;
        $product->setPriceRules($priceRules);
        $product->setCheapestPriceRule(clone $priceRules[0]);

        // Call the decorated main service to calculate product prices
        $this->decoratedService->calculateProduct($product,$context);

        //add state to the product which can be used to check if the prices are already calculated.
        $product->addState(Struct\ListProduct::STATE_PRICE_CALCULATED);

    }

}