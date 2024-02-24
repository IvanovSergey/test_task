<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Doctrine\Persistence\ManagerRegistry;
use App\Validator\ConstraintsTax;
use App\Validator\ConstraintsCoupon;
use App\Validator\ConstraintsPaymentProcessor;
use App\PaymentProcessor\PaypalPaymentProcessor;
use App\PaymentProcessor\StripePaymentProcessor;
use App\Entity\Product;

class ProductController extends AbstractController
{
    private $coupons = ['D15' => 15, 'D25' => 15, 'D50' => 15, 'P6' => 6, 'P15' => 15, 'P35' => 35, 'P50' => 50, 'P100' => 100],
            $taxes = ['DE' => 0.19, 'IT' => 0.22, 'FR' => 0.2, 'GR' => 0.24],
            $paymentProcessors = ['paypal', 'stripe'];    
    
    #[Route('/calculate-price', name: 'price')]
    public function actionPrice(Request $request, ManagerRegistry $doctrine): JsonResponse
    {        
        $validator = Validation::createValidator();
        $groups = new Assert\GroupSequence(['Default', 'custom']);

        $constraint = new Assert\Collection([
            'product' => new Assert\Length(['max' => 255]),
            'taxNumber' => new ConstraintsTax(),
            'couponCode' => new ConstraintsCoupon($this->coupons)
        ]);
        
        $postData = $request->request->all();
        $violations = $validator->validate($postData, $constraint, $groups);

        if ($violations->count() > 0) {
            $errors = [];
            foreach($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            
            $json = new JsonResponse(['Errors' => $errors]);
            $json->setStatusCode(400, 'Success');            
            return $json;
        }        
        
        $product = $doctrine->getRepository(Product::class)->find($postData['product']);
        
        $price = $this->applyTax($product->getPrice(), $postData['taxNumber']);
                
        
        $json = new JsonResponse(['Price' => !empty($postData['couponCode']) ? $this->applyCoupon($price, $postData['couponCode']) : $price]);
        $json->setStatusCode(200, 'Success');            
        return $json;
    }
    
    #[Route('/purchase', name: 'purchase')]
    public function actionPurchase(Request $request, ManagerRegistry $doctrine): JsonResponse
    {                
        $validator = Validation::createValidator();
        $groups = new Assert\GroupSequence(['Default', 'custom']);

        $constraint = new Assert\Collection([
            'product' => new Assert\Length(['max' => 255]),
            'taxNumber' => new ConstraintsTax(),
            'couponCode' => new ConstraintsCoupon($this->coupons),
            'paymentProcessor' => new ConstraintsPaymentProcessor($this->paymentProcessors)
        ]);
        
        $postData = $request->request->all();
        $violations = $validator->validate($request->request->all(), $constraint, $groups);

        if ($violations->count() > 0) {
            $errors = [];
            foreach($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            
            $json = new JsonResponse(['Errors' => $errors]);
            $json->setStatusCode(400, 'Success');            
            return $json;
        }        
        
        $product = $doctrine->getRepository(Product::class)->find($postData['product']);        
        $price = !empty($postData['couponCode']) ? $this->applyCoupon($this->applyTax($product->getPrice(), $postData['taxNumber']), $postData['couponCode']) : $this->applyTax($product->getPrice(), $postData['taxNumber']); 
        
        $paymentInfo = $this->getPaymentInfo($price, $postData['paymentProcessor']);
        
        $json = new JsonResponse(['Response' => $paymentInfo]);
        $json->setStatusCode(200, 'Success');            
        return $json;
    }
    
    public function applyTax($price, $taxNumber)
    {
        $country = substr($taxNumber, 0, 2);
        if(array_key_exists($country, $this->taxes)) {
            return $price + $price*$this->taxes[$country];
        }        
        return false;
    }
    
    public function applyCoupon($price, $couponCode)
    {
        $couponType = str_contains($couponCode, 'D') ? 'plain' : 'percentage';
        $couponValue = substr($couponCode, 1);
            
        return $couponType === 'plain' ? $price - $couponValue : $price - ($price*$couponValue/100);
    }
    
    public function getPaymentInfo($price, $paymentProcessor)
    {
        switch ($paymentProcessor) {
            case 'paypal':
                $class = new PaypalPaymentProcessor();
                return $class->pay($price);
            case 'stripe':
                $class = new StripePaymentProcessor();
                return $class->processPayment($price);
        }
    }
}
