-- ============================================================
-- Ready for the Test? — Datos semilla
-- Copia exacta de los catálogos que ya existían en script.js
-- (sustanciasConocidas, reactivos, reacciones)
-- ============================================================

USE ready_for_the_test;

-- ---------- Sustancias conocidas (estación de identificación) ----------
INSERT INTO known_substances (name, description, state, transparency, conductivity, density, detail) VALUES
  ('Agua', 'Compuesto esencial para la vida; se usa como referencia de densidad y punto de congelación.', 'liquido', 'transparente', 'noconduce', 1.0, 'Pura no conduce electricidad porque no tiene iones disueltos; es el patrón con densidad 1 g/mL.'),
  ('Alcohol etílico', 'Líquido inflamable muy común en el laboratorio; menos denso que el agua.', 'liquido', 'transparente', 'noconduce', 0.79, 'Por su baja densidad flota sobre el agua; se evapora rápido y se usa como solvente.'),
  ('Agua salada', 'Agua con sales disueltas; por eso conduce la corriente eléctrica.', 'liquido', 'transparente', 'conduce', 1.03, 'Los iones de la sal permiten el paso de la corriente; su densidad es apenas mayor a la del agua.'),
  ('Aceite vegetal', 'Líquido graso que no se mezcla con el agua; es opaco y poco denso.', 'liquido', 'opaco', 'noconduce', 0.92, 'No conduce porque no tiene cargas libres; al ser menos denso que el agua, queda arriba en una mezcla.'),
  ('Hierro', 'Metal sólido y brillante; muy denso y buen conductor de la electricidad.', 'solido', 'opaco', 'conduce', 7.87, 'Sus electrones libres transportan la corriente; por su alta densidad se hunde en cualquier líquido común.'),
  ('Hielo', 'Agua en estado sólido; curiosamente menos denso que el agua líquida.', 'solido', 'transparente', 'noconduce', 0.92, 'Al congelarse las moléculas se ordenan dejando espacios, por eso flota en el agua.'),
  ('Aluminio', 'Metal ligero y buen conductor; muy usado por su resistencia y peso bajo.', 'solido', 'opaco', 'conduce', 2.7, 'Es uno de los metales más abundantes; conduce bien la corriente y es unas 3 veces menos denso que el hierro.'),
  ('Dióxido de carbono', 'Gas incoloro que exhalamos; se disuelve poco en agua.', 'gas', 'transparente', 'noconduce', 0.0018, 'Su densidad es muchísimo menor a la de los líquidos; no conduce porque sus moléculas son neutras.');

-- ---------- Reactivos de la mesa de mezclas ----------
INSERT INTO reagents (id, name, color_hex, icon, description) VALUES
  ('bicarbonato', 'Bicarbonato de sodio', '#F5F1E6', '🧂', 'Base débil que reacciona con ácidos liberando dióxido de carbono (efervescencia).'),
  ('vinagre', 'Vinagre', '#F4E7B0', '🧴', 'Ácido acético diluido; al combinarse con una base produce burbujeo por el CO2.'),
  ('peroxido', 'Agua oxigenada', '#EAF6FA', '💧', 'Peróxido de hidrógeno que se descompone rápido en presencia de un catalizador.'),
  ('yoduro', 'Yoduro de potasio', '#E4D6EC', '🟣', 'Sal que acelera la descomposición del agua oxigenada generando espuma abundante.'),
  ('sulfato_cobre', 'Sulfato de cobre', '#3E86C4', '🔷', 'Sal de color azul que con bases fuertes forma un precipitado de hidróxido de cobre.'),
  ('hidroxido_sodio', 'Hidróxido de sodio', '#EAF5EA', '🧪', 'Base fuerte que reacciona con sales de cobre y cambia el color de la fenolftaleína.'),
  ('fenolftaleina', 'Fenolftaleína', '#FBEFF5', '🩸', 'Indicador ácido-base: incolora en ácidos y rosa intenso en medios básicos.');

-- ---------- Recetas de reacciones descubribles ----------
INSERT INTO mixture_reactions (reagent_a_id, reagent_b_id, result_name, description, effect, result_color_hex) VALUES
  ('bicarbonato', 'vinagre',
    'Efervescencia ácido-base',
    'El bicarbonato reacciona con el ácido acético del vinagre y libera dióxido de carbono: burbujea con fuerza.',
    'burbujea', '#E7E2CC'),
  ('peroxido', 'yoduro',
    'Descomposición catalizada',
    'El yoduro de potasio acelera la descomposición del agua oxigenada: se libera oxígeno en forma de espuma abundante y se calienta.',
    'espuma', '#F0EFF2'),
  ('hidroxido_sodio', 'sulfato_cobre',
    'Precipitación de hidróxido de cobre',
    'Se forma un precipitado azul de hidróxido de cobre que se asienta poco a poco en el fondo del matraz.',
    'precipita', '#1E5FA8'),
  ('fenolftaleina', 'hidroxido_sodio',
    'Vire de indicador en medio básico',
    'En un medio básico, la fenolftaleína se torna de un rosa intenso.',
    'colorea', '#E85D9E'),
  ('fenolftaleina', 'vinagre',
    'Indicador en medio ácido',
    'En un medio ácido la fenolftaleína permanece incolora: no hay cambio visible.',
    'ninguno', '#F7F3E8');
