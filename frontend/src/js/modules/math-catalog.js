export const MATH_TOPICS = [
  {
    key: 'ma_aritmetica',
    title: 'Aritmética',
    icon: '🔢',
    badge: 'Operaciones y fracciones',
    items: [
      {
        name: 'Jerarquía de operaciones',
        formula: '7 + 5 × 2 = 17',
        note: 'Primero se resuelven las multiplicaciones y divisiones, después sumas y restas.',
      },
      {
        name: 'Simplificar fracciones',
        formula: '8/12 = 2/3',
        note: 'Divide numerador y denominador entre el máximo común divisor.',
      },
      {
        name: 'Porcentaje de un número',
        formula: '25% de 80 = 20',
        note: 'Multiplica el número por el porcentaje y divide entre 100.',
      },
      {
        name: 'Mínimo común múltiplo',
        formula: 'mcm(4, 6) = 12',
        note: 'Es el menor múltiplo común a ambos números.',
      },
    ],
  },
  {
    key: 'ma_algebra',
    title: 'Álgebra',
    icon: '🧮',
    badge: 'Ecuaciones y potencias',
    items: [
      {
        name: 'Ecuación de primer grado',
        formula: 'x + 5 = 12 → x = 7',
        note: 'Despeja la incógnita: lo que suma pasa restando y lo que multiplica pasa dividiendo.',
      },
      {
        name: 'Producto entre potencias',
        formula: 'x³ · x² = x⁵',
        note: 'Para multiplicar potencias de la misma base se suman los exponentes.',
      },
      {
        name: 'Potencia con exponente 0',
        formula: 'a⁰ = 1 (a ≠ 0)',
        note: 'Cualquier base distinta de cero elevada a la cero da uno.',
      },
      {
        name: 'Factor común',
        formula: '6x + 9 = 3(2x + 3)',
        note: 'Extrae el factor que se repite en todos los términos.',
      },
    ],
  },
  {
    key: 'ma_geometria',
    title: 'Geometría',
    icon: '📐',
    badge: 'Áreas, perímetros y volúmenes',
    items: [
      {
        name: 'Área de un cuadrado',
        formula: 'A = lado² → 5² = 25',
        note: 'El área es el lado multiplicado por sí mismo.',
      },
      {
        name: 'Perímetro de un rectángulo',
        formula: 'P = 2(ancho + alto)',
        note: 'Suma dos veces la base y dos veces la altura.',
      },
      {
        name: 'Teorema de Pitágoras',
        formula: 'c² = a² + b² → 3² + 4² = 5²',
        note: 'Válido solo en triángulos rectángulos; c es la hipotenusa.',
      },
      {
        name: 'Área de un círculo',
        formula: 'A = π · r²',
        note: 'Multiplica π por el radio elevado al cuadrado.',
      },
    ],
  },
  {
    key: 'ma_proporcionalidad',
    title: 'Proporcionalidad y medidas',
    icon: '📏',
    badge: 'Reglas de tres y unidades',
    items: [
      {
        name: 'Regla de tres directa',
        formula: 'si 4 → 100, 1 → 25',
        note: 'Relaciona dos magnitudes que crecen o disminuyen juntas.',
      },
      {
        name: 'Densidad de un material',
        formula: 'ρ = masa ÷ volumen',
        note: 'Relaciona la masa con el espacio que ocupa un objeto.',
      },
      {
        name: 'Conversión de unidades',
        formula: '1 kg = 1000 g · 1 L = 1000 mL',
        note: 'Usa los factores de conversión para cambiar de unidad sin perder el valor.',
      },
      {
        name: 'Velocidad media',
        formula: 'v = distancia ÷ tiempo',
        note: 'Divide la distancia recorrida entre el tiempo empleado.',
      },
    ],
  },
];

export function mathTopicBadges() {
  return MATH_TOPICS.map((topic) => topic.badge);
}