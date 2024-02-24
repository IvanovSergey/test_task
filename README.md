Docker images used for php and postgre
Validation done with symfony validator
Two small unit tests for api endpoints

Queries and json examples for testing
https://localhost/calculate-price
{
    "product": 1,
    "taxNumber": "DE123456789",
    "couponCode": "D15"
}

https://localhost/purchase
{
    "product": 1,
    "taxNumber": "IT12345678900",
    "couponCode": "D15",
    "paymentProcessor": "paypal"
}

