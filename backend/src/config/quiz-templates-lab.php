<?php

declare(strict_types=1);

return [
    'sustancias' => [
        'label' => 'Sustancias y propiedades',
        'icon' => '🧪',
        'questions' => [
            ['q' => '¿Cuál de estas sustancias conduce la corriente eléctrica?', 'options' => ['Agua', 'Aceite vegetal', 'Agua salada', 'Alcohol'], 'correct' => 2],
            ['q' => '¿Qué estado físico tiene el hierro a temperatura ambiente?', 'options' => ['Sólido', 'Líquido', 'Gas', 'Plasma'], 'correct' => 0],
            ['q' => '¿Cuál es la densidad aproximada del agua?', 'options' => ['0.5 g/mL', '1.0 g/mL', '2.5 g/mL', '7.8 g/mL'], 'correct' => 1],
            ['q' => 'Si una sustancia es opaca, significa que...', 'options' => ['Deja pasar la luz', 'No deja pasar la luz', 'Refleja toda la luz', 'Emite luz'], 'correct' => 1],
            ['q' => 'El alcohol etílico es...', 'options' => ['Sólido opaco', 'Líquido transparente', 'Gas conductor', 'Líquido que conduce'], 'correct' => 1],
            ['q' => '¿Cuál tiene mayor densidad: hierro o hielo?', 'options' => ['El hielo', 'El hierro', 'Ambos iguales', 'Ninguno'], 'correct' => 1],
            ['q' => 'La conductividad de una sustancia indica si...', 'options' => ['Conduce el calor', 'Conduce la electricidad', 'Es transparente', 'Flota en el agua'], 'correct' => 1],
            ['q' => 'El dióxido de carbono a temperatura ambiente es...', 'options' => ['Sólido', 'Líquido', 'Gas', 'Plasma'], 'correct' => 2],
            ['q' => '¿Qué estados de la materia maneja el catálogo?', 'options' => ['Sólido y líquido', 'Sólido, líquido y gas', 'Gas y plasma', 'Solo líquido'], 'correct' => 1],
            ['q' => 'El aceite vegetal es un líquido...', 'options' => ['Transparente', 'Opaco', 'Conductor', 'Muy denso'], 'correct' => 1],
        ],
    ],
    'mezclas' => [
        'label' => 'Mezclas y reacciones',
        'icon' => '⚗️',
        'questions' => [
            ['q' => '¿Qué ocurre al mezclar bicarbonato de sodio con vinagre?', 'options' => ['Sin efecto', 'Burbujea con fuerza', 'Precipita azul', 'Se torna rosa'], 'correct' => 1],
            ['q' => 'El peróxido de hidrógeno con yoduro de potasio produce...', 'options' => ['Espuma abundante', 'Precipitado', 'Cambio a rosa', 'Nada visible'], 'correct' => 0],
            ['q' => 'Hidróxido de sodio con sulfato de cobre forman un precipitado...', 'options' => ['Blanco', 'Azul', 'Rosado', 'Amarillo'], 'correct' => 1],
            ['q' => 'La fenolftaleína en medio básico se torna...', 'options' => ['Incolora', 'Rosa intenso', 'Azul', 'Naranja'], 'correct' => 1],
            ['q' => 'La fenolftaleína en medio ácido...', 'options' => ['Se torna rosa', 'Permanecer incolora', 'Precipita', 'Burbujea'], 'correct' => 1],
            ['q' => 'Para descubrir una reacción nueva necesitas...', 'options' => ['Un solo reactivo', 'Dos reactivos', 'Tres sustancias', 'Calentar la mezcla'], 'correct' => 1],
            ['q' => '¿Qué reactivo tiene color azul intenso?', 'options' => ['Bicarbonato', 'Sulfato de cobre', 'Vinagre', 'Yoduro'], 'correct' => 1],
            ['q' => 'Descubrir una reacción por primera vez otorga...', 'options' => ['0 puntos', 'Puntos bonus extra', 'Pierdes puntos', 'Nada especial'], 'correct' => 1],
            ['q' => 'Mezclar vinagre con fenolftaleína produce...', 'options' => ['Espuma', 'Sin cambio visible', 'Precipitado azul', 'Luz'], 'correct' => 1],
            ['q' => 'El yoduro de potasio en la mesa aparece de color...', 'options' => ['Verde', 'Violeta', 'Azul', 'Ámbar'], 'correct' => 1],
        ],
    ],
    'densidad' => [
        'label' => 'Masa, volumen y densidad',
        'icon' => '⚖️',
        'questions' => [
            ['q' => 'La fórmula de la densidad es...', 'options' => ['masa × volumen', 'masa ÷ volumen', 'volumen ÷ masa', 'masa + volumen'], 'correct' => 1],
            ['q' => 'Una muestra tiene 20 g y 10 mL. Su densidad es...', 'options' => ['0.5 g/mL', '2 g/mL', '10 g/mL', '200 g/mL'], 'correct' => 1],
            ['q' => 'El agua salada tiene densidad aproximada de...', 'options' => ['0.79 g/mL', '1.03 g/mL', '7.87 g/mL', '2.7 g/mL'], 'correct' => 1],
            ['q' => 'Si la masa es 40 g y el volumen 20 mL, la densidad es...', 'options' => ['0.5 g/mL', '2 g/mL', '4 g/mL', '20 g/mL'], 'correct' => 1],
            ['q' => 'El aceite vegetal tiene densidad de 0.92 g/mL. Si lo pones en agua (1 g/mL)...', 'options' => ['Se hunde', 'Flota', 'Se disuelve', 'Explota'], 'correct' => 1],
            ['q' => 'Para registrar una muestra necesitas...', 'options' => ['Solo nombre', 'Nombre, masa y volumen', 'Temperatura y presión', 'Solo masa'], 'correct' => 1],
            ['q' => 'El aluminio tiene densidad de 2.7 g/mL y el hierro 7.87 g/mL. El más denso es...', 'options' => ['El aluminio', 'El hierro', 'Son iguales', 'Depende de la masa'], 'correct' => 1],
            ['q' => 'Si un objeto tiene alta densidad, sus partículas están...', 'options' => ['Muy separadas', 'Muy juntas', 'En reposo', 'En el vacío'], 'correct' => 1],
            ['q' => 'Un sólido con 100 g y 40 mL de volumen tiene densidad de...', 'options' => ['2.5 g/mL', '4 g/mL', '0.4 g/mL', '60 g/mL'], 'correct' => 0],
            ['q' => 'Al calcular la densidad de una muestra desconocida puedes...', 'options' => ['Identificarla con el catálogo', 'Convertirla en gas', 'Hacerla conducir', 'Aumentar su masa'], 'correct' => 0],
        ],
    ],
];