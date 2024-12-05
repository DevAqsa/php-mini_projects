<?php

echo "aqsa mumtaz <br/>";


$name = "aqsa";
$Name = "working with dedication";

echo $Name;

?>

<!-- php with html  -->
<?php
echo "<h1 style='color:purple'>Php with html</h1>";
?>

<?php
$name="learning php";
echo "<h1 style='color:blue'>I'm $name</h1>"
?>

<!-- html with php -->

<h2 style="color:red">
    <?php
    echo "I'm Practicing and $name";
    ?>
</h2>


<!-- constant -->
<?php
const data = 'aqsa learning php';
echo strlen(data);

echo data;
// for scope
define("GREETING", "Welcome!");
echo GREETING;
?>
<!-- --------------------------------------------------------------------------------------------------------- -->


<!-- Data types in PHP -->
 <?php
//  $name = "Peter";
//  echo var_dump($name);

//  $num = 1000;
//  echo var_dump($num);

// $numFloat = 1.10;
// echo var_dump($numFloat);

// $bool = true;
// echo var_dump($bool);

// $arr = ['aqsa', 'sana', 'iqra', 10];
// echo var_dump($arr);


// $empty = null;
// echo var_dump($empty);
 ?>


<!-- operators in PHP  -->

<?php
$a = 10;
$b = 40;
$c = "70";
$d= 6;
// PHP Arithmetic Operators
//  echo $a + $b;
//  echo $a - $b;
//  echo $a * $b;
//  echo $a / $b;
//  echo $a % $b;
//  echo $a ** $b;


// PHP Assignment Operators
//   $a += $b;
//   echo $a;
//    $a -= $b;
//    echo $a;
//    $a *= $b;
//    echo $a;
//    $a /= $b;
//    echo $a;
//    $a %= $b;
//    echo $a;
//    $a **= $b;
//    echo $a;

//    PHP Comparison Operators
// echo $a == $b;
// echo $a != $b;
// echo $a === $b;
// echo $a !== $b;

// var_dump($a==$c);
// var_dump($a===$c);
// var_dump($a!=$c);
var_dump($a<>$c);
?>

<?php
// PHP Increment / Decrement Operators
$r = 10;

// echo "<br>";
// echo "<br>";
// echo "<br>";

// echo $r++;
echo "<br>";
echo "<br>";
// echo $r;

echo --$r;

echo "<br>";
echo "<br>";
echo $r;
?>

<!-- PHP String Operators -->

<?php
$name ="Joe";

$age = 14;
echo "<br>";
echo "<br>";
// echo "my name is joe".$name;
// echo "my name is joe  $name";

$str= "Hello How are you Doing";
echo "<br>";
echo "<br>";
$str1= "My name is joe";
echo $str1.=$str;

?>


<!-- ---------------------------------------------------------------------- -->

<!-- conditional statements -->

