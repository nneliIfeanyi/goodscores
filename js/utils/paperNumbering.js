export function getSectionQuestionLabels(sections, questionsById, questionLayout = []) {
  const layoutById = new Map(questionLayout.map((item) => [String(item.question_id), item]));
  const labels = new Map();
  let previousMainNumber = 0;

  sections.forEach((section) => {
    let mainNumber = section.numbering_mode === 'continue' ? previousMainNumber : 0;
    let currentMain = 0;
    let currentPart = 0;
    let partNumber = 0;
    let subPartNumber = 0;
    const questionIds = (section.question_ids || []).map(String);

    questionIds.forEach((questionId, index) => {
      const question = questionsById.get(questionId);
      if (!question) return;

      const layout = question.type === 'theory' ? layoutById.get(questionId) || {} : {};
      const level = Math.max(0, Math.min(2, Number(layout.level) || 0));
      let label;
      if (level === 0) {
        mainNumber += 1;
        currentMain = mainNumber;
        const nextQuestion = questionsById.get(questionIds[index + 1]);
        const nextLayout = nextQuestion?.type === 'theory'
          ? layoutById.get(questionIds[index + 1]) || {}
          : {};
        const hasParts = Number(nextLayout.level) > 0;
        partNumber = hasParts ? 1 : 0;
        currentPart = hasParts ? 1 : 0;
        subPartNumber = 0;
        label = hasParts ? `${currentMain}(a)` : String(currentMain);
      } else if (level === 1) {
        if (!currentMain) currentMain = ++mainNumber;
        partNumber += 1;
        currentPart = partNumber;
        subPartNumber = 0;
        label = `${currentMain}(${String.fromCharCode(96 + currentPart)})`;
      } else {
        if (!currentMain) currentMain = ++mainNumber;
        if (!currentPart) currentPart = ++partNumber;
        subPartNumber += 1;
        const roman = ['i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix', 'x'][subPartNumber - 1] || String(subPartNumber);
        label = `${currentMain}(${String.fromCharCode(96 + currentPart)})(${roman})`;
      }

      labels.set(questionId, label);
    });

    previousMainNumber = mainNumber;
  });

  return labels;
}
