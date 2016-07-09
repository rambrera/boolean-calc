<?php
$input = $_POST['input'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
</head>
<body>
<div class="container">
<div class="row col-md-8 col-xs-12">
    
<h1 class="hidden-xs">Калькулятор булевых функций</h1>
<h4 class="visible-xs">Калькулятор булевых функций</h4>
<form method="post">
    <div class="btn-toolbar hidden-xs">
        <div class="btn-group">
            <a class="op btn btn-default" role="button" title="Конъюнкция">&and;</a>
            <a class="op btn btn-default" role="button" title="Дизнъюнкция">&or;</a>
            <a class="op btn btn-default" role="button" title="Инверсия">&not;</a>
        </div>
        <div class="btn-group">
            <a class="op btn btn-default" role="button" title="Импликация">&rarr;</a>
            <a class="op btn btn-default" role="button" title="Эквиваленция">&hArr;</a>
            <a class="op btn btn-default" role="button" title="Сложение по модулю 2">&oplus;</a>
        </div>
        <div class="btn-group">
            <a class="op btn btn-default" role="button" title="Штрих Шеффера">|</a>
            <a class="op btn btn-default" role="button" title="Стрелка Пирса">&darr;</a>
        </div>
    </div>
    <div class="btn-toolbal visible-xs row">
        <div class="btn-group">
        <a class="op small btn btn-default" role="button" title="Конъюнкция">&and;</a>
        <a class="op small btn btn-default" role="button" title="Дизнъюнкция">&or;</a>
        <a class="op small btn btn-default" role="button" title="Инверсия">&not;</a>
        <a class="op small btn btn-default" role="button" title="Импликация">&rarr;</a>
        <a class="op small btn btn-default" role="button" title="Эквиваленция">&hArr;</a>
        <a class="op small btn btn-default" role="button" title="Сложение по модулю 2">&oplus;</a>
        <a class="op small btn btn-default" role="button" title="Штрих Шеффера">|</a>
        <a class="op small btn btn-default" role="button" title="Стрелка Пирса">&darr;</a>
        </div>
    </div>
    <div class="input-group">
        <input type="text" class="form-control" id="input" name="input" onkeyup="CaretPos = getCaretPos(this)" onclick="CaretPos = getCaretPos(this)" value="<?=$input?>"/>
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit">Вычислить</button>
        </span>        
    </div>
</form>
<br>
<style>
td,th{
    text-align: center;
}

th{
    font-family: "Times New Roman", Times, serif;
    font-style: italic;
    padding-left:10px;
    padding-right:10px;
    line-height: 1.5;
}
.op{
    width: 40px;
}
.btn-toolbar{
    margin-bottom: 15px;
    margin-top: 15px;
}

.btn-toolbal.visible-xs.row {
    padding-bottom: 15px;
    padding-top: 15px;
    padding-left: 15px;
/*    text-align: center;*/
}

.op.small {
    font-size: 14px;
    width: 35px;
}
</style>
<script src="/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script>
    var CaretPos = 0;
    function setSelectionRange(input, selectionStart, selectionEnd) {
      if (input.setSelectionRange) {
        input.focus();
        input.setSelectionRange(selectionStart, selectionEnd);
      } else if (input.createTextRange) {
        var range = input.createTextRange();
        range.collapse(true);
        range.moveEnd('character', selectionEnd);
        range.moveStart('character', selectionStart);
        range.select();
      }
    }

    function getCaretPos(input) {
        if(input.createTextRange){
            var range = document.selection.createRange.duplicate();
            range.moveStart('character', -input.value.length);
            return range.text.length;
        }else{
            return input.selectionStart;
        }
    }
    var input = $('#input');
    $('.op').bind('click', function(){
        input.val(input.val().slice(0,CaretPos) +$(this).text()+ input.val().slice(CaretPos));
        input.focus();
        CaretPos++;
        setSelectionRange(input[0],CaretPos,CaretPos);
    });
</script>
<?php
/*Описание основных токенов*/
$TOKEN['1'] = new Token('1');
$TOKEN['0'] = new Token('0');
$TOKEN['('] = new Token('(');
$TOKEN[')'] = new Token(')');
/*Описание операторов*/
$TOKEN["¬"] = new Operator("¬", 8, function($a){return !$a;},1,false);    //Инверсия
$TOKEN["|"] = new Operator("|", 7, function($a,$b){return !($a && $b);}); //Штрих Шеффера
$TOKEN["↓"] = new Operator("↓", 6, function($a,$b){return !($a || $b);}); //Стрелка Пирса
$TOKEN["∧"] = new Operator("∧", 5, function($a,$b){return $a && $b;});    //Конъюнкция
$TOKEN["∨"] = new Operator("∨", 4, function($a,$b){return $a || $b;});    //Дизнъюнкция
$TOKEN["⊕"] = new Operator("⊕", 3, function($a,$b){return ($a xor $b);});   //Сложение по модулю 2
$TOKEN["→"] = new Operator("→", 2, function($a,$b){return !$a || $b;});   //Импликация
$TOKEN["⇔"] = new Operator("⇔", 1, function($a,$b){return $a == $b;});    //Эквиваленция
try{
if(empty($input)) throw new Exception();
/*Разделим токены пробелами*/
foreach($TOKEN as $t){
    //Если токен 0 или 1 пропускаем их, так-как они могут находиться в токенах переменных
    if($TOKEN['0'] == $t || $TOKEN['1'] == $t) continue;
    $string = $t->token;
    $input = str_replace($string, " $string ", $input);
}
/*Преобразуем набор строк в токены*/
$string = explode(' ', $input);
$list = array();
for($i = 0; $i < count($string); $i++){
    if($string[$i] == ''){
        array_splice($string,$i--,1);
    }else{
        if(isset($TOKEN[$string[$i]])) $list[] = $TOKEN[$string[$i]];
        else{
            if(!preg_match('/^[A-Za-z][A-Za-z0-9]*$/',$string[$i])) throw new Exception();
            $list[] = new Token($string[$i]);
        }
    }
}
/*Алгоритм сортировочная станция*/
$expression = array();
$stack = new SplStack();
foreach($list as $token){
    if($token == $TOKEN['(']){
        $stack->push($token);
        continue;
    }
    if($token == $TOKEN[')']){
        while($stack->top() != $TOKEN['(']){
            $expression[] = $stack->pop();
        }
        $stack->pop();
        continue;
    }
    if($token instanceof Operator){
        while($stack->count() > 0 && $stack->top() instanceof Operator &&
            (($token->associative == 0 && ($token->priority <= $stack->top()->priority)) ||
            ($token->associative == 1 && ($token->priority < $stack->top()->priority)))){
             $expression[] = $stack->pop();
        }
        $stack->push($token);
        continue;
    }
    if ($token instanceof Func){
        $stack->push($token);
        continue;
    }
    $expression[] = $token->token;
}
while($stack->count() != 0){
    $expression[] = $stack->pop();
}
/*Поиск токенов переменных*/
$vars = array();
foreach($expression as $current){
    if(is_numeric($current)) continue;
    if(!($current instanceof $token)) $vars[$current] = 0;
}
ksort($vars);
if(count($vars) > 0){
    /*Заполнение таблицы*/
    $var_count = count($vars);
    $rows = pow(2,count($vars));
    $i = 0;
    $t_expression = $expression;
    $stack = new SplStack();
    foreach($t_expression as $token){
        if(!($token instanceof Token)){
            $stack->push($token);
        }else{
            $args = array();
            $args[] = $stack->pop();
            if($token->binary == true){
                $args[] = $stack->pop();
                $action = $args[1].$token->token.$args[0];
            }else{
                $action = $token->token.$args[0];
            }
            $stack->push("A<sub>$i</sub>");
            $table[0][$var_count+$i] = sprintf('A<sub>%d</sub><br>%s',$i++,$action);
        }
    }
    if($stack->count() > 1) throw new Exception();
    $cols = $var_count + $i;
    $i = 0;
    foreach($vars as $key => $value){
        $table[0][$i++] = $key;
    }
    for($i = 1; $i < $rows+1; $i++){
        $str = sprintf("%'0{$var_count}b",$i-1);
        $j = 0;
        foreach($vars as $key => $value){
            $vars[$key] = $str[$j++];
            $table[$i][$j-1] = $vars[$key];
        }
        $t_expression = $expression;
        for($j = 0; $j < count($t_expression); $j++){
            foreach($vars as $var=>$value){
                if($t_expression[$j] == $var) $t_expression[$j] = $value;
            }
        }
        //Вычисление
        $stack = new SplStack();
        $t = 0;
        if(count($t_expression) == 0) return 0;
        foreach($t_expression as $token){
            
            if(!($token instanceof Token)){
                $stack->push($token);
            }else{
                $args = array();
                $args[] = $stack->pop();
                if($token->binary == true){
                    $args[] = $stack->pop();
                    $args = array_reverse($args);
                }
                if($token == $TOKEN['(']) throw new Exception();
                $result = call_user_func_array(array($token->action,'__invoke'),$args);
                $table[$i][$var_count + $t++] = $result?"1":"0";
                $stack->push($result);
            }
        }
    }
    /*Вывод таблицы*/
    echo '<table class="table-bordered table-striped">';
    for($y = 0; $y < $rows+1; $y++){
        if($y == 0) echo '<thead>';
        echo '<tr>';
        if($y == 1) echo '<tbody>';
        for($x = 0; $x < $cols; $x++){
            if($y == 0) echo "<th>{$table[$y][$x]}</th>";
            else echo "<td>{$table[$y][$x]}</td>";
        }
        echo '</tr>';
        if($y == 0) echo '</thead>';
        if($y == $rows) echo '</tbody>';
    }
    echo '</table><br>';
    /*Построение СДНФ*/
    echo 'СДНФ: ';
    $f = false;
    for($i = 1; $i < $rows+1; $i++){
        if($table[$i][$cols-1] == 1){
             if($f) echo '∨';
            else $f = true;
            echo "(";
            for($j = 0; $j < $var_count; $j++){
               // 
               if($table[$i][$j] == 0) echo '¬';
               echo $table[0][$j];
               if($j != $var_count-1) echo '∧';
            }
            echo ')';
        }
    }
    echo '<br>';
    /*Построение СКНФ*/
    echo 'СКНФ: ';
    $f = false;
    for($i = 1; $i < $rows+1; $i++){
        if($table[$i][$cols-1] =='0'){
            if($f) echo '∧';
            else $f = true;
            echo "(";
            for($j = 0; $j < $var_count; $j++){
               // 
               if($table[$i][$j] == '1') echo '¬';
               echo $table[0][$j];
               if($j != $var_count-1) echo '∨';
            }
            echo ')';
        }
    }
    echo '<br>';
    /*Построение полинома Жегалкина(методом треугольника)*/
    echo 'Полином Жегалкина: ';
    for($i = 1; $i < $rows+1; $i++){
        $pascal[0][] = (boolean)$table[$i][$cols-1];
    }
    for($i = 1; $i < count($pascal[0]); $i++){
        for($j = 0; $j < count($pascal[$i-1])-1; $j++){
            //Разрабы php горите в аду суки! Какого хуя у xor приоритет ниже чем у присвоения?
            $pascal[$i][$j] = ($pascal[$i-1][$j] xor $pascal[$i-1][$j+1]);
        }
    }
    for($i = 1; $i < $rows+1; $i++){
        if($pascal[$i-1][0]==1){
            if($no_first) echo '⊕';
            $no_first = true;
            if($i != 1){
                $no_first_con = false;
                for($j = 0; $j < $var_count; $j++){
                    if($table[$i][$j] == '1'){
                        if($no_first_con) echo '∧';
                        $no_first_con = true;
                        echo $table[0][$j];
                    }
                }
            }else echo '1';
        }
    }
}else{
    //Вычисление
    $stack = new SplStack();
    $t = 0;
    if(count($expression) == 0) return 0;
    foreach($expression as $token){
        if(!($token instanceof Token)){
            $stack->push($token);
        }else{
            $args = array();
            $args[] = $stack->pop();
            if($token->binary == true){
                $args[] = $stack->pop();
                $args = array_reverse($args);
            }
            if($token == $TOKEN['(']) throw new Exception();
            $result = call_user_func_array(array($token->action,'__invoke'),$args);
            $stack->push($result);
        }
    }
    if($stack->count() > 1) throw new Exception();
    echo "Ответ: ".($stack->pop()?'1':'0');
}
}catch(Exception $e){
if(!empty($input)){?>
<div class="alert alert-danger">
    <strong>Ошибка!</strong> Некорректное выражение
</div>
<?php
}    
}

class Token{
    public $token;
    function __construct($token){
        $this->token = $token;
    }
}

class Operator extends Token{
    public $associative;
    public $binary;
    public $priority;
    public $action;
    function __construct($token, $priority, $action, $associative = 0, $binary = true){
        $this->token = $token;
        $this->action = $action;
        $this->priority = $priority;
        $this->associative = $associative;
        $this->binary = $binary;
    }
}
?>
</div>
</div>
<div style="padding: 50px;" align="center">Ардесов Вячеслав, Салихов Дмитрий<p>© <a href="http://rambrera.com/?from=calc">Rambrera Studio</a> 2008-2016</div>
</body>
</html>