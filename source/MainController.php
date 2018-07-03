<?php 
include 'source/DriversController.php';
include 'source/CarsController.php';

class Main { 
    public $places,
    $drivers, 
    $cars,
    $ranges = array("30", "90", "360"),
    $output;
    
    function __construct($input) { 
        $input = json_decode($input, true);
        if(!isset($input['park']) || !isset($input['drivers']) || !isset($input['cars']))
            return false;
        $this->places = $input['park']['places'];
        $this->drivers = new Drivers($input['drivers']);
        $this->cars = new Cars($input["cars"]); 
        if(isset($input['params']) && isset($input['params']['range'])){
            array_push($this->ranges, $input['params']['range']);
        } 
        $this->ready_cars = array_map(function ($a1, $b1) { return $a1 + $b1; }, $this->drivers->drivers, $this->cars->cars); // усаживаем водителей за руль                
    } 
    
    function calculate(){
        foreach($this->ready_cars as $key=>$car){
            $this->ready_cars[$key] = $this->drivers->calculate($this->ready_cars[$key]);            
            $this->ready_cars[$key] = $this->cars->calculate($this->ready_cars[$key]);            
        }
        $this->output['places'] = $this->places;
        $this->output['total_cars'] = count($this->ready_cars);
        $this->output['bivaliy'] = $this->drivers->experiensed;
        $this->output['casual'] = $this->drivers->casual;
        $this->output['luda'] = $this->cars->luda;
        $this->output['homba'] = $this->cars->homba;
        $this->output['hendai'] = $this->cars->hendai;
        $this->output['ranges'] = $this->ranges;
        $this->output['sum_distance'] = array_sum(array_column($this->ready_cars, 'distance'));
        $this->output['sum_initial_distance'] = array_sum(array_column($this->ready_cars, 'km'));
        $this->output['sum_durability'] = array_sum(array_column($this->ready_cars, 'durability'));
        $this->output['sum_initial_durability'] = array_sum(array_column($this->ready_cars, 'initial_durability'));
        $this->output['suggested_places'] = round(($this->output['sum_durability'] + $this->output['sum_initial_durability'])/100);
        $this->output['suggested_places_without_initial'] = ceil(($this->output['sum_durability'])/100);
        $this->output['sum_gas'] = array_sum(array_column($this->ready_cars, 'gas'));
        
        foreach($this->ranges as $range){
            $this->calculateByRange($range);
        }
        return $this->output;
    }
    
    function calculateByRange($range){ 
        $this->output[$range . 'days']['sum_distance'] = round($this->output['sum_distance']*$range + $this->output['sum_initial_distance'],2);
        $this->output[$range . 'days']['sum_durability'] = round(($this->output['sum_durability']*$range + $this->output['sum_initial_durability'] + $this->cars->initial_break_value*$range)/100,2); // рассчитываем вероятность поломки с учетом выбранного промежутка времени, начальной прогнозируемойy поломки от уже пройденного расстояния и стандартного начального коэф. в 0.5% в день
        $this->output[$range . 'days']['sum_initial_durability'] = round($this->output['sum_initial_durability']/100,2);
        $this->output[$range . 'days']['all_repair_days'] = round($this->output[$range . 'days']['sum_durability']*3,2);
        $this->output[$range . 'days']['repair_days'] = round(($this->output[$range . 'days']['sum_durability'] - $this->output['sum_initial_durability']/100)*3,2);
        $this->output[$range . 'days']['sum_gas'] = round($this->output['sum_gas']*$range,2);        
    }
} 
 

