import { api, getUser, storageUrl } from './api.js';

const escapeHtml = (value) => String(value ?? '')
  .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;');

const getSettings = (paper, user) => {
  const settings = { ...(user?.pdf_settings || {}), ...(paper.paper_settings || {}) };
  if (user?.school_id && user.school?.paper_settings) Object.assign(settings, user.school.paper_settings);
  return settings;
};

async function getHeader(user) {
  if (user?.school_id) {
    return { school_name: user.school?.name || 'Examination Centre', extra_line: user.school?.address || '', logo_path: user.school?.logo || '' };
  }
  let header = null;
  try { header = JSON.parse(localStorage.getItem('gs_exam_header') || 'null'); } catch (_) { localStorage.removeItem('gs_exam_header'); }
  if (navigator.onLine && user?.id) {
    try {
      const response = await api('/headers');
      header = response.data?.[0] || header;
      if (header) localStorage.setItem('gs_exam_header', JSON.stringify(header));
    } catch (_) {}
  }
  return header || { school_name: 'Examination Centre', extra_line: '', logo_path: '' };
}

function renderQuestions(paper, settings) {
  const sections = settings.sections?.length ? settings.sections : [{ title: 'Questions', instructions: '', question_ids: (paper.questions || []).map((q) => q.id || q.offline_id) }];
  const questions = new Map((paper.questions || []).map((q) => [String(q.id ?? q.offline_id), q]));
  const layout = new Map((settings.question_layout || []).map((item) => [String(item.question_id), item]));
  let number = 0;
  return sections.map((section) => {
    const items = (section.question_ids || []).map((id) => questions.get(String(id))).filter(Boolean).map((question) => {
      number += 1;
      const label = layout.get(String(question.id ?? question.offline_id))?.label || String(number);
      const options = question.type === 'mcq' ? `<div class="options">${(question.options || []).map((option) => `<div><strong>${escapeHtml(option.key)}.</strong> ${escapeHtml(option.text)}</div>`).join('')}</div>` : '';
      const images = (question.images || (question._pending_image ? [{ data_url: question._pending_image, original_name: 'Question image' }] : [])).map((image) => `<figure class="question-image"><img style="max-width:400px;width:100%;height:auto;max-height:300px;margin:0 auto;object-fit:contain" src="${escapeHtml(image.data_url || storageUrl(image.file_path))}" alt="${escapeHtml(image.original_name || 'Question image')}"></figure>`).join('');
      const marks = settings.show_marks === false ? '' : `<span class="marks">[${escapeHtml(question.marks || 1)} mark${Number(question.marks) === 1 ? '' : 's'}]</span>`;
      return `<article style="margin-bottom:8px"><div><strong>${escapeHtml(label)}.</strong> ${question.body || ''}${marks}</div>${images}${options}</article>`;
    }).join('');
    return `<section><h2>${escapeHtml(section.title || 'Questions')}</h2><p class="instructions">${escapeHtml(section.instructions || '')}</p><div class="question-grid" style="display:block;columns:2;column-gap:24px">${items}</div></section>`;
  }).join('');
}

export async function renderPaperHtml(paper) {
  const user = getUser() || {};
  const settings = getSettings(paper, user);
  const header = await getHeader(user);
  const logo = settings.show_logo !== false && header.logo_path ? `<img class="header-logo" src="${escapeHtml(storageUrl(header.logo_path))}" alt="Logo">` : '';
  const session = paper.current_session || user.current_session || 'Current session';
  const paperSize = ['A4', 'LETTER', 'LEGAL'].includes(String(settings.paper_size || 'A4').toUpperCase()) ? String(settings.paper_size || 'A4').toUpperCase() : 'A4';
  const orientation = settings.orientation === 'landscape' ? 'landscape' : 'portrait';
  const fontSize = Math.max(8, Math.min(18, Number(settings.pdf_font_size || user.pdf_font_size || 11)));
  const margins = `${Number(settings.margin_top ?? 10)}mm ${Number(settings.margin_right ?? 8)}mm ${Number(settings.margin_bottom ?? 10)}mm ${Number(settings.margin_left ?? 8)}mm`;
  const body = renderQuestions(paper, settings);
  return `<!doctype html><html><head><meta charset="utf-8"><title>${escapeHtml(paper.title || 'Exam Paper')}</title><style>@page{size:${paperSize} ${orientation};margin:${margins}}body{font-family:Arial,sans-serif;color:#111;font-size:${fontSize}pt}.exam-header{border-bottom:2px solid #0d9488;padding-bottom:8px;margin-bottom:8px;min-height:${Number(settings.header_height ?? 80)}px}.header-table,.info-table{width:100%;border-collapse:collapse}.header-table td{vertical-align:middle;text-align:center}.header-logo{display:block;max-width:90px;max-height:55px;margin:auto}.school-name{font-size:16pt;font-weight:bold;color:#0f766e}.extra-line{font-size:9pt;color:#444;margin-top:3px}.info-table{font-size:9pt;margin:4px 0 12px}.info-table td{padding:3px 8px 3px 0;width:33%}h2{text-align:center;border-bottom:1px solid #999;padding-bottom:5px;font-size:13pt}.instructions{font-style:italic;text-align:center}.question-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 24px}article{margin:0 0 16px;page-break-inside:avoid}.question-image{margin:8px auto;text-align:center}.question-image img{display:block;max-width:100%;max-height:220px;margin:0 auto;object-fit:contain}.marks{float:right;font-size:10pt;color:#555}.options{margin:6px 0 0 24px;line-height:1.5}.footer{position:fixed;bottom:0;left:0;right:0;text-align:center;font-size:8pt;color:#777;border-top:1px solid #ddd;padding-top:4px}@media print{.question-grid{break-inside:auto}}</style></head><body><header class="exam-header"><table class="header-table"><tr><td style="width:20%">${logo}</td><td style="width:60%">${settings.show_school_name === false ? '' : `<div class="school-name">${escapeHtml(header.school_name)}</div>`}<div class="extra-line">${escapeHtml(header.extra_line || '')}</div></td><td style="width:20%"></td></tr></table></header><table class="info-table"><tr><td><strong>Term:</strong> ${escapeHtml(paper.term_name || '')}</td><td><strong>Session:</strong> ${escapeHtml(session)}</td><td><strong>Time allowed:</strong> ${escapeHtml(settings.time_allowed || '________')}</td></tr><tr><td><strong>Subject:</strong> ${escapeHtml(paper.subject_name || '')}</td><td><strong>Class:</strong> ${escapeHtml(paper.class_name || '')}</td><td><strong>Total marks:</strong> ${escapeHtml(paper.total_marks || '')}</td></tr></table>${body}<footer class="footer">${escapeHtml(settings.footer_text ?? 'End of Paper')}</footer></body></html>`;
}

export async function downloadPaperHtml(paper, format = 'html') {
  const html = await renderPaperHtml(paper);
  const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `${(paper.title || 'exam-paper').replace(/[^a-z0-9]+/gi, '-').toLowerCase()}.${format === 'docx' ? 'doc' : 'html'}`;
  link.click();
  setTimeout(() => URL.revokeObjectURL(url), 1000);
}

export async function printPaper(paper) {
  const popup = window.open('', '_blank');
  if (!popup) throw new Error('Allow popups to print the paper');
  popup.document.write((await renderPaperHtml(paper)).replace('</body>', '<script>window.onload=function(){window.print()}<\/script></body>'));
  popup.document.close();
}
