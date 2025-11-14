// ===== SUPPORT PAGE FUNCTIONALITY =====

const support = {
    init: () => {
        support.initNavigation();
        support.initFAQ();
        support.initContactForm();
    },
    
    initNavigation: () => {
        const supportLinks = document.querySelectorAll('.support-link');
        const supportSections = document.querySelectorAll('.support-section');
        
        supportLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                
                // Update active link
                supportLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                
                // Show target section
                supportSections.forEach(section => {
                    section.classList.remove('active');
                    if (section.id === targetId) {
                        section.classList.add('active');
                    }
                });
                
                // Smooth scroll to section
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    },
    
    initFAQ: () => {
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                const answer = faqItem.querySelector('.faq-answer');
                const icon = question.querySelector('i');
                
                // Toggle active state
                faqItem.classList.toggle('active');
                
                if (faqItem.classList.contains('active')) {
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    answer.style.maxHeight = '0';
                    icon.style.transform = 'rotate(0deg)';
                }
            });
        });
    },
    
    initContactForm: () => {
        const form = document.querySelector('.support-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                support.handleFormSubmission(form);
            });
        }
    },
    
    handleFormSubmission: async (form) => {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        if (!data.name || !data.email || !data.subject || !data.message) {
            utils.showNotification('Please fill in all required fields', 'error');
            return;
        }
        try {
            const res = await fetch('support_submit.php', { method: 'POST', body: formData, credentials: 'same-origin' });
            const json = await res.json().catch(()=>({ ok:false, error:'Invalid response' }));
            if (res.status === 401) {
                // Not logged in: redirect to login and come back to contact section
                const back = 'support.php#contact';
                window.location.href = 'login.php?redirect=' + encodeURIComponent(back);
                return;
            }
            if (res.ok && json.ok) {
                utils.showNotification('Your ticket has been submitted. Ticket ID #' + json.ticket_id);
                form.reset();
            } else {
                utils.showNotification('Failed: ' + (json.error || res.statusText), 'error');
            }
        } catch (e) {
            utils.showNotification('Network error. Please try again later.', 'error');
        }
    }
};

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    support.init();
    console.log('Support page initialized successfully!');
});
