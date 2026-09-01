(function ($) {
	'use strict';

	const V7AiChatbot = {
		conversationId: null,
		isLoading: false,

		init: function () {
			this.button = $('#v7-ai-chatbot-button');
			this.window = $('#v7-ai-chatbot-window');
			this.closeBtn = $('#v7-ai-chatbot-close');
			this.input = $('#v7-ai-chatbot-input');
			this.sendBtn = $('#v7-ai-chatbot-send');
			this.messages = $('#v7-ai-chatbot-messages');
			this.conversationId = this.getOrCreateConversationId();
			this.bindEvents();
			this.openWindow();
			this.applySettings();
		},

		getOrCreateConversationId: function () {
			let convId = sessionStorage.getItem('v7_chatbot_conversation_id');
			if (!convId) {
				convId = 'conv_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
				sessionStorage.setItem('v7_chatbot_conversation_id', convId);
			}
			return convId;
		},

		bindEvents: function () {
			this.button.on('click', () => this.toggleWindow());
			this.closeBtn.on('click', () => this.closeWindow());
			this.sendBtn.on('click', () => this.sendMessage());
			this.input.on('keypress', (e) => {
				if (e.which === 13 && !e.shiftKey) {
					e.preventDefault();
					this.sendMessage();
				}
			});
			$(window).on('beforeunload', () => {
				this.saveConversation();
			});
		},

		applySettings: function () {
			const settings = v7AiChatbotParams.settings;
			const container = $('#v7-ai-chatbot-container');

			if (container.length) {
				container.removeClass('bottom-right bottom-left top-right top-left').addClass(settings.position);
			}
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
			if (this.isLoading) {
				return;
			}

			const message = this.input.val().trim();
			if (!message) {
				return;
			}

			this.input.val('');
			this.addMessage(message, 'user');
			this.input.prop('disabled', true);
			this.sendBtn.prop('disabled', true);
			this.isLoading = true;
			this.showTyping();

			$.ajax({
				url: v7AiChatbotParams.ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'v7_ai_chatbot_query',
					message: message,
					conversation_id: this.conversationId,
					nonce: v7AiChatbotParams.nonce
				},
				timeout: 60000,
				success: (response) => {
					this.hideTyping();
					if (response.success) {
						this.addMessage(response.data.message, 'bot');
						if (response.data.conversation_id) {
							this.conversationId = response.data.conversation_id;
							sessionStorage.setItem('v7_chatbot_conversation_id', this.conversationId);
						}
					} else {
						const errorMsg = response.data?.message || 'Sorry, something went wrong. Please try again later.';
						this.addMessage(errorMsg, 'error');
					}
				},
				error: (jqXHR, textStatus, errorThrown) => {
					this.hideTyping();
					let errorMsg = 'Sorry, I am unable to respond right now. Please try again later.';

					if (textStatus === 'timeout') {
						errorMsg = 'Request timed out. Please try again with a shorter message.';
					} else if (jqXHR.status === 429) {
						errorMsg = 'Too many requests. Please wait a moment before sending another message.';
					} else if (jqXHR.status === 403) {
						errorMsg = 'You do not have permission to use the chatbot.';
					}

					this.addMessage(errorMsg, 'error');
				},
				complete: () => {
					this.input.prop('disabled', false);
					this.sendBtn.prop('disabled', false);
					this.isLoading = false;
					this.input.focus();
				}
			});
		},

		addMessage: function (text, type) {
			let messageClass = 'v7-ai-chatbot-' + type + '-message';
			if (type === 'error') {
				messageClass = 'v7-ai-chatbot-error-message';
			}

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
		},

		saveConversation: function () {
			if (this.conversationId) {
				sessionStorage.setItem('v7_chatbot_last_conversation', this.conversationId);
			}
		}
	};

	$(document).ready(() => {
		V7AiChatbot.init();
	});
})(jQuery);
