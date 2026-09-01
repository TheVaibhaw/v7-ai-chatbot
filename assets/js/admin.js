// V7 AI Chatbot Admin JavaScript

(function ($) {
	'use strict';

	const V7AIChatbotAdmin = {
		init: function () {
			this.setupTabs();
			this.setupColorPickers();
			this.setupTestConnection();
			this.setupExportData();
			this.setupProviderSelector();
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

		setupProviderSelector: function () {
			const self = this;
			$('#v7-ai-provider').on('change', function () {
				self.updateProviderInfo($(this).val());
				// Also update field visibility
				if (typeof V7AIProviderModels !== 'undefined') {
					V7AIProviderModels.updateAPIKeyFields($(this).val());
				}
			});

			// Initialize on page load
			const currentProvider = $('#v7-ai-provider').val();
			if (currentProvider) {
				self.updateProviderInfo(currentProvider);
				// Also update field visibility on load
				if (typeof V7AIProviderModels !== 'undefined') {
					V7AIProviderModels.updateAPIKeyFields(currentProvider);
				}
			}
		},

		updateProviderInfo: function (provider) {
			const providerData = V7AIProviderModels.providers[provider];
			if (!providerData) return;

			let infoHTML = `
				<div class="provider-info" style="margin-top: 10px; padding: 10px; background: #f0f7ff; border-radius: 4px; border-left: 3px solid #0073aa;">
					<p><strong>💡 Description:</strong> ${providerData.description}</p>
					<p><strong>Status:</strong> <span style="display: inline-block; padding: 3px 10px; background: #e8f1f7; border-radius: 3px; font-size: 12px; font-weight: bold;">${providerData.status.toUpperCase()}</span></p>
					<p><strong>💰 Pricing:</strong> ${providerData.pricing}</p>
					<p>
						<a href="${providerData.documentationUrl || providerData.website}" target="_blank" rel="noopener" class="button button-primary button-small">🔑 Get API Key</a>
						<a href="${providerData.website}" target="_blank" rel="noopener" class="button button-secondary button-small">🌐 Visit ${providerData.name}</a>
					</p>
				</div>
			`;

			let container = $('#v7-ai-provider').closest('tr').find('.provider-info');
			if (container.length) {
				container.replaceWith(infoHTML);
			} else {
				$('#v7-ai-provider').closest('td').append(infoHTML);
			}

			// Update model dropdown
			if (typeof V7AIProviderModels !== 'undefined') {
				V7AIProviderModels.updateModels(provider);
			}
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
