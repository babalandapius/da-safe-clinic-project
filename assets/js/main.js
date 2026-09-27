document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (btn) {
        btn.onclick = function() {
            sidebar.classList.toggle('active');
            console.log("Sidebar toggled!");
        };
    }

    const modal = document.getElementById('dashboard-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalSubtitle = document.getElementById('modal-subtitle');
    const sections = document.querySelectorAll('.modal-section');
    const triggers = document.querySelectorAll('.modal-trigger');
    const closeButtons = document.querySelectorAll('.modal-close');

    function closeModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        sections.forEach(section => section.classList.add('hidden'));
    }

    function openModal(target) {
        if (!modal) return;
        modal.classList.remove('hidden');
        sections.forEach(section => section.classList.add('hidden'));

        const section = document.getElementById(`modal-section-${target}`);
        if (section) {
            section.classList.remove('hidden');
        }

        if (target === 'total-requests') {
            modalTitle.textContent = 'Total Requests';
            modalSubtitle.textContent = 'All recent lab requests and assigned team details.';
        } else if (target === 'pending-analysis') {
            modalTitle.textContent = 'Pending Analysis';
            modalSubtitle.textContent = 'Requests still awaiting completion.';
        } else if (target === 'results-ready') {
            modalTitle.textContent = 'Results Ready';
            modalSubtitle.textContent = 'Completed requests ready for review.';
        }
    }

    triggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            const target = trigger.getAttribute('data-modal-target');
            openModal(target);
        });
    });

    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });

    if (modal) {
        modal.addEventListener('click', event => {
            if (event.target === modal) closeModal();
        });
    }
});

function toggleDropdown() {
    const menu = document.getElementById("reportsMenu");
    const caret = document.getElementById("caret-icon");

    // Toggle the 'show' class for the menu
    menu.classList.toggle("show");

    // Toggle the rotation for the arrow icon
    caret.classList.toggle("arrow-rotate");
}