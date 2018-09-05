<?php
include 'source/MainController.php';

if(isset($_REQUEST['ajax'])){
    $main = new Main();
    $output = $main->calculate($_REQUEST['int'], $_REQUEST['diff_start'], $_REQUEST['diff_end']);
    echo $output;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Расчет прогнозируемых результатов эмуляции онлайн-тестирования.</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body style="font-size: 12px;">
<div class="container">
  <div class="jumbotron">
    <h3>Расчет прогнозируемых результатов эмуляции онлайн-тестирования.</h3><br />  
    <form id="form_data" style="padding-top:15px;width:800px;" method="POST" action="/">
    <div class="row">
        <div class="col-sm-4">
            <label for="comment">Введите интеллект испытуемого:</label>
            <input style="width:70px;" type="text" value="<?php echo isset($_REQUEST['int']) ? $_REQUEST['int'] : '';?>" class="form-control" id="int" name="int">  
        </div>  
        <div class="col-sm-8">
            <label for="comment">Введите диапазон сложности вопросов:</label>
            <input style="width:70px;" type="text" value="<?php echo isset($_REQUEST['diff_start']) ? $_REQUEST['diff_start'] : '';?>" class="form-control" id="diff_start" placeholder="От" name="diff_start"><br />
            <input style="width:70px;" type="text" value="<?php echo isset($_REQUEST['diff_end']) ? $_REQUEST['diff_end'] : '';?>" class="form-control" id="diff_end" placeholder="До" name="diff_end">
        </div>
    </div> 
    <button id="ajax_sub" type="submit" class="btn btn-default">Submit</button>
  </form>
  </div>
  <div id="content"></div>
</div>
<script>
    $('#ajax_sub').click(function(){
        $.ajax({
            method: "POST",
            url: "/",
            data: { int: $('#int').val(), diff_start: $('#diff_start').val(), diff_end: $('#diff_end').val(), ajax: true }
            })
            .done(function( msg ) { console.log(msg);
                $('#content').html(msg);
            });
        return false;
    });
</script>    
</body>
</html>