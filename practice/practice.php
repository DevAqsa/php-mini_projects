<!-- class is a blueprint -->

<?php

class Fruit {
  // variables are called Properties
  public $name;
  public $color;

  //functions are called Methods
  function set_name($name) {
    $this->name = $name;
  }
  function get_name() {
    return $this->name;
  }
}


$apple = new Fruit();
$banana = new Fruit();
$apple->set_name('Apple');
$banana->set_name('Banana');

echo $apple->get_name();
echo "<br>";
echo $banana->get_name();
?>

<!-- An object is an instance/copy of class -->
 <!-- class is a blueprint -->

<?php
class MathOperations{

   function sum($a, $b){
    echo $a+$b;
   }

   function multi($a, $b){
    echo $a*$b;
   }

   function sub($a, $b){
    echo $a-$b;
   }
};

$maths = new MathOperations();
echo $maths->sum(20,30);
echo "<br>";
echo $maths->multi(20,30);
echo "<br>";
echo $maths->sub(20,30);


?>

<!-- properties -->

<?php
class Properties{
//public , private , protected ==> acess modifiers

public $name = "aqsa";

function getName() {
   echo $this->name;
} 

function updateName(){
    $this-> name = "peter";
}
}

$p1 = new Properties();
// echo $p1->name; // public property can be accessed from outside the class
$p1->updateName();
echo "<br>";
$p1->getName(); // public method can be accessed from outside the class
echo "<br>";


?>




