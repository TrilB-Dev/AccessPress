document.addEventListener('DOMContentLoaded', () => {
	const root = document;
	root.querySelectorAll('[data-accesspress-count]').forEach((element) => {
		element.classList.add('accesspress-count-ready');
	});
});



