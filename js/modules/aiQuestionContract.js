export const MAX_QUESTIONS_PER_REQUEST = 10;
export const DAILY_REQUEST_LIMIT = 10;

export function normalizeDiagramRequest(value) {
  if (!value || typeof value !== 'object') return null;
  const description = String(value.description || '').trim();
  if (!description) return null;
  const labels = Array.isArray(value.labels)
    ? value.labels.map((label) => String(label || '').trim()).filter(Boolean).slice(0, 12)
    : [];
  const svg = String(value.svg || '').trim();
  const safeSvg = /^<svg\b[\s\S]*<\/svg>$/i.test(svg)
    ? svg
      .replace(/<\/?(?:script|foreignObject|iframe|object|embed|style|metadata)\b[^>]*>/gi, '')
      .replace(/\s(?:on[a-z]+|href|xlink:href)\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+)/gi, '')
      .replace(/javascript\s*:/gi, '')
    : '';
  return { type: 'precise_diagram', description, labels, svg: safeSvg, image_prompt: '' };
}

export function normalizeDiagramSpec(value) {
  if (!value || typeof value !== 'object') return null;
  const type = String(value.type || '').trim();
  const description = String(value.description || '').trim();
  if (!['precise_diagram', 'illustration'].includes(type) || !description) return null;
  const labels = Array.isArray(value.labels)
    ? value.labels.map((label) => String(label || '').trim()).filter(Boolean).slice(0, 12)
    : [];
  const svg = String(value.svg || '').trim();
  const safeSvg = /^<svg\b[\s\S]*<\/svg>$/i.test(svg)
    ? svg
      .replace(/<\/?(?:script|foreignObject|iframe|object|embed|style|metadata)\b[^>]*>/gi, '')
      .replace(/\s(?:on[a-z]+|href|xlink:href)\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+)/gi, '')
      .replace(/javascript\s*:/gi, '')
    : '';
  return {
    type,
    description,
    labels,
    svg: type === 'precise_diagram' ? safeSvg : '',
    image_prompt: type === 'illustration' ? String(value.image_prompt || description).trim() : '',
  };
}

export function isValidQuestionCount(value) {
  return Number.isInteger(Number(value))
    && Number(value) >= 1
    && Number(value) <= MAX_QUESTIONS_PER_REQUEST;
}
