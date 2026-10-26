const allowedTags = new Set(['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ol', 'ul', 'li']);

export function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function renderExpression(value) {
  let expression = String(value ?? '').trim();
  expression = expression.replace(/\\frac\s*\{([^{}]*)\}\s*\{([^{}]*)\}/g, '<span class="math-frac"><span>$1</span><span>$2</span></span>');
  expression = expression.replace(/\\sqrt\s*\[([^\]]+)\]\s*\{([^{}]*)\}/g, '<span class="math-root"><sup>$1</sup>√<span>$2</span></span>');
  expression = expression.replace(/\\sqrt\s*\{([^{}]*)\}/g, '<span class="math-root">√<span>$1</span></span>');
  expression = expression.replace(/\\(log|ln|sin|cos|tan|lim)_\{([^{}]+)\}/g, '<span class="math-op">$1</span><sub>$2</sub>');
  expression = expression.replace(/\\(log|ln|sin|cos|tan|lim)_([A-Za-z0-9]+)/g, '<span class="math-op">$1</span><sub>$2</sub>');
  expression = expression.replace(/\\(log|ln|sin|cos|tan|lim)\b/g, '<span class="math-op">$1</span>');
  expression = expression.replace(/([A-Za-z0-9)]+)_\{([^{}]+)\}/g, '$1<sub>$2</sub>');
  expression = expression.replace(/([A-Za-z0-9)]+)_([0-9]+)/g, '$1<sub>$2</sub>');
  expression = expression.replace(/([A-Za-z0-9)]+)\^\{([^{}]+)\}/g, '$1<sup>$2</sup>');
  expression = expression.replace(/([A-Za-z0-9)]+)\^([A-Za-z0-9+−-]+)/g, '$1<sup>$2</sup>');
  return expression.replace(/\\([{}()[\]])/g, '$1');
}

function renderMathText(value) {
  const source = String(value ?? '');
  const pieces = [];
  let cursor = 0;
  const delimiter = /\\\(([^]*?)\\\)|\\\[([^]*?)\\\]/g;
  let match;
  const renderPlain = (plain) => {
    let text = escapeHtml(plain);
    const symbols = {
      '\\alpha': 'α', '\\beta': 'β', '\\gamma': 'γ', '\\delta': 'δ',
      '\\theta': 'θ', '\\lambda': 'λ', '\\mu': 'μ', '\\pi': 'π',
      '\\rho': 'ρ', '\\sigma': 'σ', '\\phi': 'φ', '\\omega': 'ω',
      '\\Delta': 'Δ', '\\Lambda': 'Λ', '\\Omega': 'Ω',
      '\\times': '×', '\\cdot': '·', '\\pm': '±', '\\div': '÷',
      '\\leq': '≤', '\\le': '≤', '\\geq': '≥', '\\ge': '≥',
      '\\neq': '≠', '\\infty': '∞', '\\rightarrow': '→', '\\to': '→',
      '\\leftarrow': '←', '\\leftrightarrow': '↔',
      '\\rightleftharpoons': '⇌', '\\rightleftarrows': '⇄',
      '\\prime': '′',
    };
    Object.entries(symbols).forEach(([command, symbol]) => { text = text.split(command).join(symbol); });
    text = text.replace(/\\vec\{([^{}]+)\}/g, '$1⃗');
    text = text.replace(/\\frac\s*\{([^{}]*)\}\s*\{([^{}]*)\}/g, (_, numerator, denominator) => `<span class="math-frac"><span>${escapeHtml(numerator)}</span><span>${escapeHtml(denominator)}</span></span>`);
    text = text.replace(/\\sqrt\s*\[([^\]]+)\]\s*\{([^{}]*)\}/g, (_, index, radicand) => `<span class="math-root"><sup>${escapeHtml(index)}</sup>√<span>${escapeHtml(radicand)}</span></span>`);
    text = text.replace(/\\sqrt\s*\{([^{}]*)\}/g, (_, radicand) => `<span class="math-root">√<span>${escapeHtml(radicand)}</span></span>`);
    text = text.replace(/\\(log|ln|sin|cos|tan|lim)_\{([^{}]+)\}/g, '<span class="math-op">$1</span><sub>$2</sub>').replace(/\\(log|ln|sin|cos|tan|lim)_([A-Za-z0-9]+)/g, '<span class="math-op">$1</span><sub>$2</sub>');
    text = text.replace(/\\(log|ln|sin|cos|tan|lim)\b/g, '<span class="math-op">$1</span>');
    text = text.replace(/([A-Za-z]+)_\{([^{}]+)\}\^\{([^{}]+)\}/g, '$1<sub>$2</sub><sup>$3</sup>');
    text = text.replace(/([A-Za-z]+)_([0-9]+)\^\{([^{}]+)\}/g, '$1<sub>$2</sub><sup>$3</sup>');
    text = text.replace(/([A-Za-z0-9)]+)_\{([^{}]+)\}/g, '$1<sub>$2</sub>').replace(/([A-Za-z0-9)]+)_([0-9]+)/g, '$1<sub>$2</sub>');
    text = text.replace(/([A-Za-z0-9)]+)\^\{([^{}]+)\}/g, '$1<sup>$2</sup>').replace(/([A-Za-z0-9)]+)\^([A-Za-z0-9+−-]+)/g, '$1<sup>$2</sup>');
    return text;
  };
  while ((match = delimiter.exec(source))) {
    pieces.push(renderPlain(source.slice(cursor, match.index)));
    pieces.push(`<span class="math-expression${match[2] !== undefined ? ' math-display' : ''}">${renderPlain(match[1] ?? match[2])}</span>`);
    cursor = match.index + match[0].length;
  }
  pieces.push(renderPlain(source.slice(cursor)));
  return pieces.join('');
}

export function mathHtml(value) {
  const source = String(value ?? '');
  if (!source.includes('<')) return renderMathText(source);
  const template = document.createElement('template');
  template.innerHTML = source;
  const walk = (node) => {
    [...node.childNodes].forEach((child) => {
      if (child.nodeType === Node.TEXT_NODE) {
        const span = document.createElement('span');
        span.innerHTML = renderMathText(child.nodeValue || '');
        child.replaceWith(...span.childNodes);
      } else if (child.nodeType === Node.ELEMENT_NODE) {
        if (!allowedTags.has(child.tagName.toLowerCase())) child.replaceWith(document.createTextNode(child.textContent || ''));
        else walk(child);
      }
    });
  };
  walk(template.content);
  return template.innerHTML;
}

export function renderMathInto(root) {
  if (!root) return;
  root.innerHTML = mathHtml(root.innerHTML);
}