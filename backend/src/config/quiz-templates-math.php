<?php

declare(strict_types=1);

return [
    'aritmetica' => [
        'label' => 'Aritmética',
        'icon' => '🔢',
        'questions' => [
            ['q' => '¿Cuál es el resultado de 7 + 5 × 2?', 'options' => ['24', '17', '12', '19'], 'correct' => 1],
            ['q' => 'Simplifica la fracción 8/12.', 'options' => ['4/6', '2/3', '1/2', '3/4'], 'correct' => 1],
            ['q' => '¿Cuál es el 25% de 80?', 'options' => ['20', '25', '30', '40'], 'correct' => 0],
            ['q' => '¿Cuánto es 3⁴?', 'options' => ['12', '27', '64', '81'], 'correct' => 3],
            ['q' => 'Convierte 0.75 a porcentaje.', 'options' => ['7.5%', '75%', '750%', '0.75%'], 'correct' => 1],
            ['q' => '¿Cuál es el mínimo común múltiplo de 4 y 6?', 'options' => ['12', '24', '8', '6'], 'correct' => 0],
            ['q' => 'Tienes 120 caramelos y regalas la cuarta parte, ¿cuántos te quedan?', 'options' => ['30', '60', '90', '100'], 'correct' => 2],
            ['q' => '¿Cuánto es 2/3 + 1/6?', 'options' => ['4/6', '3/9', '5/6', '1'], 'correct' => 2],
            ['q' => 'Redondea 7.68 a la décima.', 'options' => ['7.6', '7.7', '8.0', '7.68'], 'correct' => 1],
            ['q' => '¿Cuál es el resultado de 15 − 3 × 4?', 'options' => ['48', '3', '12', '27'], 'correct' => 1],
        ],
    ],
    'algebra' => [
        'label' => 'Álgebra',
        'icon' => '🧮',
        'questions' => [
            ['q' => 'Resuelve: x + 5 = 12', 'options' => ['x = 5', 'x = 7', 'x = 17', 'x = 2'], 'correct' => 1],
            ['q' => 'Resuelve: 2x = 18', 'options' => ['x = 9', 'x = 16', 'x = 36', 'x = 6'], 'correct' => 0],
            ['q' => 'Resuelve: x − 7 = 10', 'options' => ['x = 3', 'x = 17', 'x = 7', 'x = 70'], 'correct' => 1],
            ['q' => 'Resuelve: 3(x − 2) = 9', 'options' => ['x = 5', 'x = 3', 'x = 7', 'x = 6'], 'correct' => 0],
            ['q' => '¿Cuál es el valor de 5² + 4?', 'options' => ['29', '45', '25', '14'], 'correct' => 0],
            ['q' => 'Si y = 2x + 1 y x = 3, ¿cuánto vale y?', 'options' => ['6', '7', '9', '23'], 'correct' => 1],
            ['q' => '¿Cuánto vale a⁰ cuando a ≠ 0?', 'options' => ['0', '1', 'a', '10'], 'correct' => 1],
            ['q' => 'Simplifica: 4x + 3x', 'options' => ['7x', '12x', 'x', '43x'], 'correct' => 0],
            ['q' => 'Resuelve: x/4 = 6', 'options' => ['x = 2', 'x = 10', 'x = 24', 'x = 1.5'], 'correct' => 2],
            ['q' => 'Factoriza: 6x + 9', 'options' => ['3(2x + 3)', '6(x + 3)', '3(2x + 9)', '9(6x)'], 'correct' => 0],
        ],
    ],
    'geometria' => [
        'label' => 'Geometría',
        'icon' => '📐',
        'questions' => [
            ['q' => '¿Cuál es el área de un cuadrado de lado 5?', 'options' => ['10', '20', '25', '50'], 'correct' => 2],
            ['q' => '¿Cuál es el perímetro de un rectángulo de 4 × 6?', 'options' => ['20', '24', '10', '48'], 'correct' => 0],
            ['q' => 'Área de un círculo de radio 3 (π ≈ 3.14).', 'options' => ['28.26', '18.84', '9.42', '33.14'], 'correct' => 0],
            ['q' => 'Hipotenusa de un triángulo rectángulo con catetos 3 y 4.', 'options' => ['7', '12', '5', '25'], 'correct' => 2],
            ['q' => 'Volumen de un cubo de arista 3.', 'options' => ['9', '27', '81', '6'], 'correct' => 1],
            ['q' => 'Área de un triángulo con base 8 y altura 5.', 'options' => ['40', '20', '13', '26'], 'correct' => 1],
            ['q' => 'Longitud de la circunferencia con radio 5 (π ≈ 3.14).', 'options' => ['31.4', '78.5', '15.7', '25'], 'correct' => 0],
            ['q' => '¿Cuánto suman los ángulos internos de un triángulo?', 'options' => ['90°', '180°', '270°', '360°'], 'correct' => 1],
            ['q' => 'Área de un rectángulo de 7 × 3.', 'options' => ['10', '20', '21', '24'], 'correct' => 2],
            ['q' => 'Volumen de un prisma rectangular de 2 × 3 × 4.', 'options' => ['9', '12', '24', '30'], 'correct' => 2],
        ],
    ],
];