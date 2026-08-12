import './bootstrap';


const menuBtn = document.getElementById('mobile-menu-btn');
const closeBtn = document.getElementById('close-menu-btn');
const menu = document.getElementById('mobile-menu');
const overlay = document.getElementById('mobile-menu-overlay');

function toggleMenu() {
    const isClosed = menu.classList.contains('translate-x-full');
    if (isClosed) {
        menu.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        menu.classList.add('translate-x-full');
        overlay.classList.add('hidden');
    }
}

menuBtn.addEventListener('click', toggleMenu);
closeBtn.addEventListener('click', toggleMenu);
overlay.addEventListener('click', toggleMenu);

document.querySelectorAll('[data-hometaps]').forEach(button => {
	button.addEventListener('click', () => {
		const tabName = button.getAttribute('data-hometaps');
		showTab(tabName);
	});
});
function showTab(tab) {
	// Hide both contents
	document.getElementById('content-court').classList.add('hidden');
	document.getElementById('content-activity').classList.add('hidden');

	// Remove active styles
	document.getElementById('tab-court').classList.remove('tab-active','border-teal');
	document.getElementById('tab-activity').classList.remove('tab-active','border-teal');

	// Add inactive styles
	document.getElementById('tab-court').classList.add('tab-inactive');
	document.getElementById('tab-activity').classList.add('tab-inactive');

	// Show selected content + activate tab
	if(tab === 'court') {
	  document.getElementById('content-court').classList.remove('hidden');
	  document.getElementById('tab-court').classList.add('tab-active','border-teal');
	  document.getElementById('tab-court').classList.remove('tab-inactive');
	} else {
	  document.getElementById('content-activity').classList.remove('hidden');
	  document.getElementById('tab-activity').classList.add('tab-active','border-teal');
	  document.getElementById('tab-activity').classList.remove('tab-inactive');
	}
}


const observer = new IntersectionObserver(
	entries => {
	    entries.forEach(entry => {
	        if (entry.isIntersecting) {
	            document.querySelectorAll('a[data-section]').forEach(link =>link.classList.remove('text-teal') );
	            const activeLink = document.querySelector(`a[data-section="${entry.target.id}"]`);
	            if (activeLink) {
	                activeLink.classList.add('text-teal');
	            }
	        }
	    });
	},
	{ threshold: 0.5 }
);
document.querySelectorAll('section').forEach(section => observer.observe(section));
