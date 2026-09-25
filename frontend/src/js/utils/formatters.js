const STATE_LABELS = {
  solido: 'Sólido',
  liquido: 'Líquido',
  gas: 'Gas',
};

const TYPE_LABELS = {
  densidad: 'Densidad',
  temperatura: 'Temperatura',
  identificacion: 'Identificación',
  masa_volumen: 'Masa/Volumen',
  mezcla: 'Mezcla',
  basica: 'Básica',
  cientifica: 'Científica',
};

const EFFECT_LABELS = {
  burbujea: 'Burbujea',
  espuma: 'Espuma',
  precipita: 'Precipita',
  colorea: 'Colorea',
  ninguno: 'Sin efecto',
};

const TRANSPARENCY_LABELS = {
  transparente: 'Transparente',
  opaco: 'Opaco',
};

const CONDUCTIVITY_LABELS = {
  conduce: 'Conduce',
  noconduce: 'No conduce',
};

const MONTHS = [
  '01', '02', '03', '04', '05', '06',
  '07', '08', '09', '10', '11', '12',
];

export function formatDate(value) {
  if (!value) {
    return '-';
  }
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }
  const day = String(date.getDate()).padStart(2, '0');
  const hour = String(date.getHours()).padStart(2, '0');
  const minute = String(date.getMinutes()).padStart(2, '0');

  return `${day}/${MONTHS[date.getMonth()]}/${date.getFullYear()} ${hour}:${minute}`;
}

export function labelFor(value) {
  const labels = [
    STATE_LABELS,
    TYPE_LABELS,
    EFFECT_LABELS,
    TRANSPARENCY_LABELS,
    CONDUCTIVITY_LABELS,
  ];
  for (const group of labels) {
    if (value in group) {
      return group[value];
    }
  }
  return value;
}

export function formatState(value) {
  return labelFor(value);
}

export function formatType(value) {
  return labelFor(value);
}

export function formatEffect(value) {
  return labelFor(value);
}