(function ($) {
	'use strict';

	const V7AiChatbot = {
		init: function () {
			this.button = $('#v7-ai-chatbot-button');
			this.window = $('#v7-ai-chatbot-window');
			this.closeBtn = $('#v7-ai-chatbot-close');
			this.input = $('#v7-ai-chatbot-input');
			this.sendBtn = $('#v7-ai-chatbot-send');
			this.messages = $('#v7-ai-chatbot-messages');
			this.bindEvents();
			this.openWindow();
		},

		bindEvents: function () {
			this.button.on('click', () => this.toggleWindow());
			this.closeBtn.on('click', () => this.closeWindow());
			this.sendBtn.on('click', () => this.sendMessage());
			this.input.on('keypress', (e) => {
				if (e.which === 13) {
					this.sendMessage();
				}
			});
		},

		toggleWindow: function () {
			if (this.window.is(':visible')) {
				this.closeWindow();
			} else {
				this.openWindow();
			}
		},

		openWindow: function () {
			this.window.show();
			this.button.hide();
			this.input.focus();
			this.scrollToBottom();
		},

		closeWindow: function () {
			this.window.hide();
			this.button.show();
		},

		sendMessage: function () {
			const message = this.input.val().trim();
			if (!message) {
				return;
			}

			this.input.val('');
			this.addMessage(message, 'user');
			this.input.prop('disabled', true);
			this.sendBtn.prop('disabled', true);
			this.showTyping();

			$.ajax({
				url: v7AiChatbotParams.ajaxUrl,
				type: 'POST',
				data: {
					action: 'v7_ai_chatbot_query',
					message: message,
					nonce: v7AiChatbotParams.nonce
				},
				success: (response) => {
					this.hideTyping();
					if (response.success) {
						this.addMessage(response.data.message, 'bot');
					} else {
						this.addMessage(response.data.message || 'Sorry, something went wrong.', 'bot');
					}
				},
				error: () => {
					this.hideTyping();
					this.addMessage('Sorry, I am unable to respond right now. Please try again later.', 'bot');
				},
				complete: () => {
					this.input.prop('disabled', false);
					this.sendBtn.prop('disabled', false);
					this.input.focus();
				}
			});
		},

		addMessage: function (text, type) {
			const messageClass = type === 'user' ? 'v7-ai-chatbot-user-message' : 'v7-ai-chatbot-bot-message';
			const messageHtml = `<div class="v7-ai-chatbot-message ${messageClass}"><div class="v7-ai-chatbot-message-content">${this.escapeHtml(text)}</div></div>`;
			this.messages.append(messageHtml);
			this.scrollToBottom();
		},

		showTyping: function () {
			const typingHtml = '<div class="v7-ai-chatbot-message v7-ai-chatbot-bot-message v7-ai-chatbot-typing-indicator"><div class="v7-ai-chatbot-typing"><span></span><span></span><span></span></div></div>';
			this.messages.append(typingHtml);
			this.scrollToBottom();
		},

		hideTyping: function () {
			this.messages.find('.v7-ai-chatbot-typing-indicator').remove();
		},

		scrollToBottom: function () {
			if (this.messages.length) {
				this.messages.scrollTop(this.messages[0].scrollHeight);
			}
		},

		escapeHtml: function (text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}
	};

	$(document).ready(() => {
		V7AiChatbot.init();
	});
})(jQuery);
