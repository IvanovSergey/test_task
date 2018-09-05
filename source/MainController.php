<?php 

class Main { 
    public  $output,
            $output_questions,
            $db,
            $picked_questions,
            $skipped_questions,
            $answered_questions = 0,
            $total_questions;
    
    function __construct() { 
        $this->db = mysqli_connect("127.0.0.1", "root", "", "test");
    }
    
    function calculate($int, $diff_start, $diff_end, $ajax = false){
        $questions = $this->getQuestions($diff_start, $diff_end);
        
        foreach($this->picked_questions as $question){            
             if($question->difficulty == 100){
                $prob = 0;
            } else if($question->difficulty >= $int){ 
                $prob = ($question->difficulty*$int)/10000; 
                if($int == 100 && $question->difficulty == 100)
                    $prob = 0; //100 сложность на 100 интеллект
                else if($int == 100)
                    $prob = 1; //100 интеллект на любую сложность
            } else{ 
                $prob = ($int*$question->difficulty)/10000; var_dump($prob);
                if($int == 100)
                    $prob = 1;
            }
            if($question->difficulty == 0 && $int != 0){
                $prob = 1;
            } else if($int == 0){
                $prob = 0;
            }
            
            $question->result = $this->checkWithProbability($prob);
            
            if($question->result)
                $this->answered_questions++;
            
            $this->output_questions[] = $question;            
        }        
        
        $testing_data = $this->saveTesting($diff_start, $diff_end, $int);
        
        $this->output = '<div class="row"><div class="col-sm-8"><table class="table">
                <thead>
                    <tr>
                        <th scope="col">Порядковый номер вопроса</th>
                        <th scope="col">ID вопроса по БД</th>
                        <th scope="col">Количество тестов, в которых этот вопрос ранее встречался</th>
                        <th scope="col">Сложность вопроса (от 0 до 100)</th>
                        <th scope="col">Был ли дан правильный ответ</th>
                    </tr>
                </thead>
            <tbody>';
        
        foreach($this->output_questions as $key=>$question){
            $question->result = $question->result ? "Да" : "Нет";
            $num = $key +1;
            $this->output .= '<tr>
                <th scope="row">' . $num . '</th>
                <td>' . $question->id . '</td>
                <td>' . $question->times_used . '</td>
                <td>' . $question->difficulty . '</td>
                <td>' . $question->result . '</td>
              </tr>';
        }
        
        $this->output .= '</tbody></table></div>';
        
        $this->output .= '<div class="col-sm-4"><table class="table">
                <thead>
                    <tr>
                        <th scope="col">Порядковый номер тестирования</th>
                        <th scope="col">Интеллект тестируемого</th>
                        <th scope="col">Диапазон сложности вопросов (от до)</th>
                        <th scope="col">Результат тестирования (X из 40)</th>
                    </tr>
                </thead>
            <tbody>';
        
        $this->output .= '<tr>
                <th scope="row">' . $testing_data->id . '</th>
                <td>' . $testing_data->intelligence . '</td>
                <td>' . $testing_data->range_from . ' до ' . $testing_data->range_to . '</td>
                <td>' . $this->answered_questions . " вопросов из " . $this->total_questions . '</td>
              </tr>';
        
        $this->output .= '</tbody></table></div></div>';
        
        $this->output .= '<div class="jumbotron">
                            <h3>Тестируемый ответил правильно на ' . $this->answered_questions . " вопросов из " . $this->total_questions . '.</h3><br />    
                        </div>';
        
        return $this->output;
    }
    
    function getQuestions($diff_start, $diff_end){ 
        $sql = 'SELECT MAX(times_used) AS top_used FROM questions'
                . ' WHERE difficulty >= ' . $diff_start . ' AND difficulty <= ' . $diff_end . ';';
        $result = $this->db->query($sql)->fetch_object();
        
        if(!empty($result->top_used))
            $minimum_prob = 1/$result->top_used; // вероятность что будет выбран самый популярный вопрос
        else 
            $minimum_prob = 0;
        
        $sql = 'SELECT * FROM questions'
                . ' WHERE difficulty >= ' . $diff_start . ' AND difficulty <= ' . $diff_end . ' ORDER BY times_used;';
        
        $result = $this->db->query($sql); 
        while ($row = $result->fetch_object()){ 
            if($result->num_rows > 40){ // Вычисляем вероятность только если вопросов больше 40
                $prob = 1 - $minimum_prob*$row->times_used; // Вычисляем вероятность с которой вопрос будет выбран относительно вероятности выбора самого популярного вопроса
                $row->picked = $this->checkWithProbability($prob);          
                if($row->picked){
                    $this->picked_questions[] = $row;
                } else {
                    $this->skipped_questions[] = $row;
                }
                if(isset($this->picked_questions) && count($this->picked_questions) == 40){
                    break;
                }                
            } else {
                $this->picked_questions[] = $row;
                $less_than_40 = true;
            }
        }  
        if(isset($this->picked_questions) && count($this->picked_questions) < 40 && !isset($less_than_40)){ //в случае если после вычисления вероятностей кол-во вопросов меньше 40 то дополняем массив отброшенными вопросами с наименьшей популярностью
            usort($this->skipped_questions, array($this, "cmp"));            
            $this->picked_questions = array_merge($this->picked_questions, array_slice($this->skipped_questions, 0, 40 - count($this->picked_questions)));                        
        }        
        $this->total_questions = count($this->picked_questions);
        
        $this->incrementTimesUsed();        
    }
    
    function saveTesting($diff_start, $diff_end, $int){
        $sql = "INSERT INTO testing (intelligence, range_from, range_to, result) VALUES (" . $int . ", " . $diff_start . ", " . $diff_end . ", " . $this->answered_questions . ")";                        
        $this->db->query($sql);
        $sql = "SELECT * FROM testing WHERE `id` =" . $this->db->insert_id;
        return $this->db->query($sql)->fetch_object();
    }
    
    function incrementTimesUsed(){ 
        $sql = "INSERT INTO questions (id, times_used) VALUES ";        
        foreach($this->picked_questions as $key=>$question){ 
            $question->times_used++;
            if($key + 1 < count($this->picked_questions))
                $sql .= "(" . $question->id . ", " . intval($question->times_used) . "),";
            else 
                $sql .= "(" . $question->id . ", " . intval($question->times_used) . ")";
        }
        $sql .= " ON DUPLICATE KEY UPDATE times_used=VALUES(times_used)";
        
        $this->db->query($sql);
    }
    
    function cmp($a, $b)
    {
        return strcmp($a->times_used, $b->times_used);
    }
    
    function checkWithProbability($probability=0.1, $length=10000)
    {
       $test = mt_rand(1, $length);
       return $test<=$probability*$length;
    }
} 
 

