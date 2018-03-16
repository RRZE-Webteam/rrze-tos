<?php

namespace RRZE\Wcag;

function getSalt() {
    return 'Nj,izn_}*)JWzJ-d029=|R&4+VBA. d>:Soy2u+,R+,Tm-}htt/PoL7COrJ|hIX;';
}

function randomNumbers() {

  $min_number = 1;
  $max_number = 9;

  $random_number1 = mt_rand($min_number, $max_number);
  $random_number2 = mt_rand($min_number, $max_number);

  return array(
    'number1' => $random_number1, 
    'number2' => $random_number2
  );
  
}

function numbers(array $random) {

  $arr = array(
    'eins'  => 1,
    'zwei'  => 2,
    'drei'  => 3,
    'vier'  => 4,
    'fünf'  => 5,
    'sechs' => 6,
    'sieben'=> 7,
    'acht'  => 8,
    'neun'  => 9
  );

  $number2 = $random['number2'];

  $literal = array_search($number2, $arr);

  return $literal;

}

function getOperator() {

  $operator = array(
    '+' => 'plus',
    '*' => 'mal'
  );

  $rand_operator = array_rand($operator, 1);

  $single_operator = $operator[$rand_operator[0]];

  return $single_operator;

}

function calculate(array $random, $operator) {

  switch ($operator) {
    case 'plus':
      $output = $random['number1'] + $random['number2'];
      break;
    case 'mal':
      $output = $random['number1'] * $random['number2'];
      break;
  }

  return $output;

  }

function getCaptchaString(array $random, $lit, $operator) {

  $string = $random['number1'] . ' ' . $operator  . ' ' . $lit;

  return $string;

}  

function getCaptcha() {

    $random = randomNumbers();
    $lit = numbers($random);
    $op = getOperator();
    $task_string = getCaptchaString($random, $lit, $op);
    $solution = calculate($random, $op);
    
    return array(
        'task_string'   => $task_string,
        'task_solution' => $solution,
        'task_encrypted'=> md5($solution)
    );

}