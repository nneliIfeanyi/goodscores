const nextBtn = document.querySelector('#next-btn');
const prevBtn = document.querySelector('#prev-btn');

let slideIndex = 1;
showSlides(slideIndex);

// Next/Prev controls
function plusSlides(n) { showSlides(slideIndex += n); }

// Question Controls
function currentSlide(n) { showSlides(slideIndex = n); }

// Disable button 
function disableBtn(n) {
	n.disabled = true;
	n.classList.add('disabled')
}
// Enable button 
function enableBtn(n) {
	n.disabled = false;
	n.classList.remove('disabled')
}

function showSlides(n) {
	let slides = document.getElementsByClassName('question');

	// Disable previous button if it's beginning of questions & enable if not the first question
	n === 1 ? disableBtn(prevBtn) : enableBtn(prevBtn);
	// Disable next button if it's end of questions & enable if not end
	n === slides.length ? disableBtn(nextBtn) : enableBtn(nextBtn);
	for (let i = 0; i < slides.length; i++) {
		slides[i].style.display = 'none';
		// Indicate number of questions answered and the one remaining
		const indicate = `${slideIndex}/${slides.length}`;
		document.querySelector('.indicator').textContent = indicate;
	}
	slides[slideIndex - 1].style.display = 'block';
}
