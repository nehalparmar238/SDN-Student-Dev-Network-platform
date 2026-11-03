</main>
    <footer class="site-footer">
        <div class="container">
            <p>© SDN – Student Dev Network</p>
        </div>
    </footer>

    <?php if (isLoggedIn()) { ?>
        <!-- SDN AI Chatbot Floating Widget -->
        <div id="sdn-chatbot" class="chatbot-container">
            <button id="chatbot-toggle" class="chatbot-toggle-btn" type="button">💬 SDN Assistant</button>
            <div id="chatbot-window" class="chatbot-window hidden">
                <div class="chatbot-header">
                    <span>SDN AI Assistant</span>
                    <button id="chatbot-close" type="button">&times;</button>
                </div>
                <div id="chatbot-messages" class="chatbot-messages">
                    <div class="chat-bubble bot">Hello <?= e(currentUserName()) ?>! I'm your SDN community assistant. Ask me about projects, events, or how to join a team!</div>
                </div>
                <div class="chatbot-input-area">
                    <input type="text" id="chatbot-input" placeholder="Type a question..." maxlength="200">
                    <button id="chatbot-send" type="button">Send</button>
                </div>
            </div>
        </div>
    <?php } ?>

    <script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>