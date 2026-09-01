// V7 AI Chatbot Admin JavaScript

(function ($) {
	'use strict';

	const V7AIChatbotAdmin = {
		init: function () {
			this.setupTabs();
			this.setupColorPickers();
			this.setupTestConnection();
			this.setupExportData();
		},

		setupTabs: function () {
			const self = this;
			$('.nav-tab').on('click', function (e) {
				e.preventDefault();
				const target = $(this).attr('href');

				// Hide all tabs
				$('.v7-ai-chatbot-tab-content').hide();
				$('.nav-tab').removeClass('nav-tab-active');

				// Show selected tab
				$(target).show();
				$(this).addClass('nav-tab-active');

				// Save preference
				localStorage.setItem('v7AIChatbotActiveTab', target);
			});

			// Restore last active tab
			const activeTab = localStorage.getItem('v7AIChatbotActiveTab') || '#general';
			$(activeTab).show();
			$('[href="' + activeTab + '"]').addClass('nav-tab-active');
		},

		setupColorPickers: function () {
			$('input[type="color"]').wpColorPicker({
				change: function () {
					this.setAttribute('data-changed', 'true');
				},
			});
		},

		setupTestConnection: function () {
			const self = this;
			$(document).on('click', '.v7-test-api-btn', function (e) {
				e.preventDefault();
				const button = $(this);
				const originalText = button.text();

				button.prop('disabled', true).html('<span class="v7-ai-chatbot-loader"></span> Testing...');

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'v7_ai_chatbot_settings',
						action_type: 'test_api',
						nonce: $('[name="_wpnonce"]').val(),
					},
					success: function (response) {
						if (response.success) {
							self.showNotice('success', response.message);
						} else {
							self.showNotice('error', response.message);
						}
					},
					error: function () {
						self.showNotice('error', 'Failed to test API connection');
					},
					complete: function () {
						button.prop('disabled', false).text(originalText);
					},
				});
			});
		},

		setupExportData: function () {
			const self = this;
			$(document).on('click', '.v7-export-data-btn', function (e) {
				e.preventDefault();
				const button = $(this);
				const originalText = button.text();

				button.prop('disabled', true).html('<span class="v7-ai-chatbot-loader"></span> Exporting...');

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'v7_ai_chatbot_settings',
						action_type: 'export_data',
						nonce: $('[name="_wpnonce"]').val(),
					},
					success: function (response) {
						// Trigger file download
						const dataStr = JSON.stringify(response, null, 2);
						const dataBlob = new Blob([dataStr], { type: 'application/json' });
						const url = URL.createObjectURL(dataBlob);
						const link = document.createElement('a');
						link.href = url;
						link.download = 'chatbot-data-' + new Date().toISOString().split('T')[0] + '.json';
						link.click();
						URL.revokeObjectURL(url);

						self.showNotice('success', 'Data exported successfully');
					},
					error: function () {
						self.showNotice('error', 'Failed to export data');
					},
					complete: function () {
						button.prop('disabled', false).text(originalText);
					},
				});
			});
		},

		showNotice: function (type, message) {
			const notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
			$('.wrap').prepend(notice);

			notice.find('button').on('click', function () {
				notice.fadeOut(function () {
					$(this).remove();
				});
			});

			setTimeout(function () {
				notice.fadeOut(function () {
					$(this).remove();
				});
			}, 5000);
		},

		copyToClipboard: function (text) {
			const temp = $('<textarea/>').appendTo('body').val(text).select();
			document.execCommand('copy');
			temp.remove();
		},
	};

	$(document).ready(function () {
		V7AIChatbotAdmin.init();

		// Handle form submission
		$('form').on('submit', function () {
			$('.v7-ai-chatbot-loader').remove();
		});
	});
})(jQuery);
