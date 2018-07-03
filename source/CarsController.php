<?php 

class Cars { 
    public $cars; 
    public $luda_breaking_coef = 0.003; //коэффициент поломки марки Luda
    public $default_breaking_coef = 0.01; //стандартный коэффициент поломки
    public $homba_gas_coef = 0.7; //коэффициент расхода бензина марки Homba
    public $initial_break_value = 0.005; //начальный коэф. поломки
    
    function __construct($cars) { 
        $this->cars = $cars;
    } 
    
    function calculate($item){        
        if($item['brand'] == 'luda'){
            $item['initial_durability'] = $item['km']*$this->luda_breaking_coef;
            $item['durability'] = $item['distance']*$this->luda_breaking_coef;
        } else if($item['brand'] == 'homba'){  
            $item['initial_durability'] = $item['km']*$this->default_breaking_coef;
            $item['durability'] = $item['distance']*$this->default_breaking_coef;
            $item['gas'] = $item['gas']*$this->homba_gas_coef;
        } else {
            $item['initial_durability'] = $item['km']*$this->default_breaking_coef;
            $item['durability'] = $item['distance']*$this->default_breaking_coef;
        }        
        return $item;
    }
} 
 

