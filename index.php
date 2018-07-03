<?php
include 'source/MainController.php';

if(isset($_REQUEST['json_input']))
    $json = $_REQUEST['json_input'];
else
    $json = '{"park": {"places":3},"params": {"range":720},"drivers":[{"type":"default"},{"type":"pro"},{"type":"default"},{"type":"pro"},{"type":"default"},{"type":"pro"},{"type":"default"},{"type":"pro"},{"type":"default"}],"cars":[{"km":13951, "brand":"Homba"},{"km":15005, "brand":"luda"},{"km":9005, "brand":"homba"},{"km":16005, "brand":"luda"},{"km":12005, "brand":"Hendai"},{"km":15005, "brand":"luda"},{"km":9005, "brand":"homba"},{"km":16005, "brand":"luda"},{"km":12005, "brand":"Hendai"}]}';
$main = new Main($json); 

$output = $main->calculate();
//var_dump($output);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Расчет прогнозируемых параметров для таксопарка</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <div class="jumbotron">
    <h3>Расчет прогнозируемых параметров для таксопарка.</h3><br />
        Кол-вом паркомест : <?php echo $output['places']; ?> <br />
        Кол-вом машин : <?php echo $output['total_cars']; ?> <br />
        Опытных водителей : <?php echo $output['bivaliy']; ?> <br />
        Обычных водителей : <?php echo $output['casual']; ?> <br />
        Luda : <?php echo $output['luda']; ?> <br />
        Homba : <?php echo $output['homba']; ?> <br />
        hendai : <?php echo $output['hendai']; ?> 
    
  </div>
  <div class="row">
    <div class="col-sm-4">
        
    </div>      
    <div class="col-sm-2">
        <?php echo $output['ranges'][0]; ?> дней
    </div>
    <div class="col-sm-2">
        <?php echo $output['ranges'][1]; ?> дней
    </div>
    <div class="col-sm-2">
        <?php echo $output['ranges'][2]; ?> дней
    </div>
    <?php if(isset($output['ranges'][3])){ ?>
    <div class="col-sm-2">
        <?php echo $output['ranges'][3]; ?> дней
    </div>
    <?php } ?>
  </div>
  <div style="padding-top:15px;" class="row">
    <div class="col-sm-4">
        Прогнозируемое кол-во поломок машин(с учетом начального пробега машин)
    </div>  
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][0] . 'days']['sum_durability']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][1] . 'days']['sum_durability']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][2] . 'days']['sum_durability']; ?>
    </div>
    <?php if(isset($output['ranges'][3])){ ?>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][3] . 'days']['sum_durability']; ?>
    </div>
    <?php } ?>
  </div>
  <div style="padding-top:15px;" class="row">
    <div class="col-sm-4">
        Прогнозируемое кол-во поломок машин(без учета начального пробега машин)
    </div>  
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][0] . 'days']['sum_durability'] - $output[$output['ranges'][0] . 'days']['sum_initial_durability']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][1] . 'days']['sum_durability'] - $output[$output['ranges'][1] . 'days']['sum_initial_durability']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][2] . 'days']['sum_durability'] - $output[$output['ranges'][2] . 'days']['sum_initial_durability']; ?>
    </div>
    <?php if(isset($output['ranges'][3])){ ?>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][3] . 'days']['sum_durability'] - $output[$output['ranges'][3] . 'days']['sum_initial_durability']; ?>
    </div>
    <?php } ?>
  </div>
  <div style="padding-top:15px;" class="row">
    <div class="col-sm-4">
        Общий километраж(суммарный начальный пробег всех машин - <?php echo $output['sum_initial_distance']; ?>)
    </div>  
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][0] . 'days']['sum_distance'] - $output['sum_initial_distance']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][1] . 'days']['sum_distance'] - $output['sum_initial_distance']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][2] . 'days']['sum_distance'] - $output['sum_initial_distance']; ?>
    </div>
    <?php if(isset($output['ranges'][3])){ ?>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][3] . 'days']['sum_distance'] - $output['sum_initial_distance']; ?>
    </div>
    <?php } ?>
  </div>
  <div style="padding-top:15px;" class="row">
    <div class="col-sm-4">
        Прогнозируемое кол-во дней на ремонте(с учетом начального пробега машин)
    </div>  
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][0] . 'days']['all_repair_days']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][1] . 'days']['all_repair_days']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][2] . 'days']['all_repair_days']; ?>
    </div>
    <?php if(isset($output['ranges'][3])){ ?>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][3] . 'days']['all_repair_days']; ?>
    </div>
    <?php } ?>
  </div>
  <div style="padding-top:15px;" class="row">
    <div class="col-sm-4">
        Прогнозируемое кол-во дней на ремонте(без учета начального пробега машин)
    </div>  
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][0] . 'days']['repair_days']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][1] . 'days']['repair_days']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][2] . 'days']['repair_days']; ?>
    </div>
    <?php if(isset($output['ranges'][3])){ ?>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][3] . 'days']['repair_days']; ?>
    </div>
    <?php } ?>
  </div> 
  <div style="padding-top:15px;" class="row">
    <div class="col-sm-4">
        Затраты бензина
    </div>  
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][0] . 'days']['sum_gas']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][1] . 'days']['sum_gas']; ?>
    </div>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][2] . 'days']['sum_gas']; ?>
    </div>
    <?php if(isset($output['ranges'][3])){ ?>
    <div class="col-sm-2">
        <?php echo $output[$output['ranges'][3] . 'days']['sum_gas']; ?>
    </div>
    <?php } ?>
  </div>
  <div style="padding-top:15px;" class="row">
    <div class="col-sm-4">
        Рекомендуемое кол-во паркомест для указанного кол-ва машин
    </div>  
    <div style="text-align: center;" class="col-sm-8">
        <?php echo 'C учетом начального пробега машин: ' . $output['suggested_places']; ?><br />
        <?php echo 'Без учета начального пробега машин: ' . $output['suggested_places_without_initial']; ?>
    </div>
  </div>
  <form style="padding-top:15px;" method="POST" action="/">
    <div class="form-group">
      <label for="comment">Форма ввода json данных:</label>
      <textarea class="form-control" name="json_input" rows="5" id="comment"><?php if(isset($_REQUEST['json_input'])) echo $_REQUEST['json_input']; else echo '{"park": {"places":3},"params": {"range":720},"drivers":[{"type":"default"},{"type":"pro"},{"type":"default"},{"type":"pro"},{"type":"default"},{"type":"pro"},{"type":"default"},{"type":"pro"},{"type":"default"}],"cars":[{"km":13951, "brand":"Homba"},{"km":15005, "brand":"luda"},{"km":9005, "brand":"homba"},{"km":16005, "brand":"luda"},{"km":12005, "brand":"Hendai"},{"km":15005, "brand":"luda"},{"km":9005, "brand":"homba"},{"km":16005, "brand":"luda"},{"km":12005, "brand":"Hendai"}]}'; ?></textarea>
    </div> 
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
</div>

</body>
</html>