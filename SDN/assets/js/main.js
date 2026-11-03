document.addEventListener('DOMContentLoaded', function () {
    var menuButton = document.querySelector('.menu-toggle');
    var navLinks = document.querySelector('#nav-links');

    if (menuButton && navLinks) {
        menuButton.addEventListener('click', function () {
            var isExpanded = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            navLinks.classList.toggle('is-open', !isExpanded);
        });
    }
});

document.addEventListener('click', function (event) {
    if (!(event.target instanceof Element)) {
        return;
    }

    var confirmElement = event.target.closest('[data-confirm]');

    if (confirmElement && confirmElement.tagName !== 'FORM') {
        var message = confirmElement.getAttribute('data-confirm');

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    }
});

document.addEventListener('submit', function (event) {
    var form = event.target;

    if (form instanceof HTMLFormElement && form.hasAttribute('data-confirm')) {
        var message = form.getAttribute('data-confirm');

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    }
});
// --- SDN AI Chatbot Local Assistant Logic ---
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('chatbot-toggle');
    var chatWindow = document.getElementById('chatbot-window');
    var closeBtn = document.getElementById('chatbot-close');
    var sendBtn = document.getElementById('chatbot-send');
    var inputField = document.getElementById('chatbot-input');
    var messagesContainer = document.getElementById('chatbot-messages');

    if (!toggleBtn || !chatWindow) return;

    toggleBtn.addEventListener('click', function () {
        chatWindow.classList.toggle('hidden');
    });

    closeBtn.addEventListener('click', function () {
        chatWindow.classList.add('hidden');
    });

    function appendMessage(text, sender) {
        var bubble = document.createElement('div');
        bubble.className = 'chat-bubble ' + sender;
        bubble.textContent = text;
        messagesContainer.appendChild(bubble);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function getBotResponse(userQuery) {
        var q = userQuery.toLowerCase();
        if (q.includes('project') || q.includes('create project')) {
            return "You can browse projects from the Projects tab or create your own by clicking 'Create a project' in your dashboard or My Projects page!";
        } else if (q.includes('event') || q.includes('workshop') || q.includes('hackathon')) {
            return "Check out the Events page to see upcoming campus workshops and hackathons, and click 'Register' to secure your spot.";
        } else if (q.includes('apply') || q.includes('team')) {
            return "To join a project, open any open project's details page and submit an application message to the project owner.";
        } else if (q.includes('admin') || q.includes('role')) {
            return "Administrators can manage users, delete inappropriate projects or events, and oversee categories in the Admin Panel.";
        } else {
            return "I'm your SDN assistant! You can ask me about projects, joining teams, campus events, or member skills.";
        }
    }

    function handleSend() {
        var text = inputField.value.trim();
        if (!text) return;
        appendMessage(text, 'user');
        inputField.value = '';

        setTimeout(function () {
            var reply = getBotResponse(text);
            appendMessage(reply, 'bot');
        }, 400);
    }

    if (sendBtn && inputField) {
        sendBtn.addEventListener('click', handleSend);
        inputField.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                handleSend();
            }
        });
    }
});