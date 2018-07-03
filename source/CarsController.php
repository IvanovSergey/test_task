<?php 

class Cars { 
    public $cars, 
    $luda_breaking_coef = 0.003, //коэффициент поломки марки Luda
    $default_breaking_coef = 0.01, //стандартный коэффициент поломки
    $homba_gas_coef = 0.7, //коэффициент расхода бензина марки Homba
    $initial_break_value = 0.005, //начальный коэф. поломки
    $luda =0,
    $homba =0,        
    $hendai =0;
            
    function __construct($cars) { 
        $this->cars = $cars;
    } 
    
    function calculate($item){        
        if($item['brand'] == 'luda'){
            $item['initial_durability'] = $item['km']*$this->luda_breaking_coef;
            $item['durability'] = $item['distance']*$this->luda_breaking_coef;
            $this->luda++;
        } else if($item['brand'] == 'homba'){  
            $item['initial_durability'] = $item['km']*$this->default_breaking_coef;
            $item['durability'] = $item['distance']*$this->default_breaking_coef;
            $item['gas'] = $item['gas']*$this->homba_gas_coef;
            $this->homba++;
        } else {
            $item['initial_durability'] = $item['km']*$this->default_breaking_coef;
            $item['durability'] = $item['distance']*$this->default_breaking_coef;
            $this->hendai++;
        }        
        return $item;
    }
} 
 

