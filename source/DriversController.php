<?php 

class Drivers { 
    public $drivers, 
    $drive_range = 70, //километраж в день
    $experiensed_driver_coef = 0.3, //коэффициент увеличения расстояния бывалых
    $gas_consumption = 0.1, //коэффициент расхода бензина на км
    $experiensed_gas_consumption = 0.8, //коэффициент экономии бензина бывалыми
    $experiensed = 0,
    $casual =0;
    
    
    function __construct($drivers) { 
        $this->drivers = $drivers;
    } 
    
    function calculate($item){
        if($item['type'] == 'default'){
            $item['distance'] = $this->drive_range;
            $item['gas'] = $item['distance']*$this->gas_consumption;
            $this->casual++;
        } else {
            $item['distance'] = $this->drive_range + $this->drive_range*$this->experiensed_driver_coef;
            $item['gas'] = ($item['distance']*$this->gas_consumption)*$this->experiensed_gas_consumption;
            $this->experiensed++;
        }
        return $item;
    }
} 
 

