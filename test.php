<?php

enum Test : int {
    case A = 1;
    case B = 2;
    case C = 3;
}

class TestClass {
    public Test $test;
}

$refClass = new ReflectionClass(TestClass::class);
$refProperty = $refClass->getProperty('test');

$refClass = new ReflectionClass(Test::class);

var_dump($refProperty->getType());
