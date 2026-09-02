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
			this.setupLoadModels();
		},

		// Replaces the bundled model list with the models the saved API key
		// can actually use, so a provider retiring a model ID can't leave the
		// chatbot pointing at something that no longer exists.
		setupLoadModels: function () {
			const self = this;
			$(document).on('click', '.v7-load-models-btn', function (e) {
				e.preventDefault();
				const button = $(this);
				const originalText = button.text();
				const provider = $('#v7-ai-provider').val();

				if (!provider) {
					self.showNotice('error', 'Please select an AI provider first.');
					return;
				}

				button.prop('disabled', true).html('<span class="v7-ai-chatbot-loader"></span> Loading...');

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'v7_ai_chatbot_settings',
						action_type: 'fetch_models',
						provider: provider,
						nonce: (typeof v7AiChatbotAdminParams !== 'undefined' ? v7AiChatbotAdminParams.nonce : ''),
					},
					success: function (response) {
						if (!response || !response.success || !response.data || !response.data.models) {
							const msg = (response && response.data && response.data.message) || 'Could not load models.';
							self.showNotice('error', msg);
							return;
						}

						const select = $('#v7-ai-model');
						const previous = select.val();
						select.empty();

						response.data.models.forEach(function (id) {
							$('<option>').attr('value', id).text(id).appendTo(select);
						});

						// Keep the existing choice if the provider still offers it.
						if (previous && response.data.models.indexOf(previous) !== -1) {
							select.val(previous);
						}

						self.showNotice('success', response.data.message + ' Pick one and click Save Changes.');
					},
					error: function () {
						self.showNotice('error', 'Failed to load models from the provider.');
					},
					complete: function () {
						button.prop('disabled', false).text(originalText);
					},
				});
			});
		},

		setupTabs: function () {
			const self = this;
			$('.nav-tab').on('click', function (e) {
				e.preventDefault();
				const target = $(this).attr('href');
				$('.v7-ai-chatbot-tab-content').hide();
				$('.nav-tab').removeClass('nav-tab-active');
				$(target).show();
				$(this).addClass('nav-tab-active');
				localStorage.setItem('v7AIChatbotActiveTab', target);
			});

			const activeTab = localStorage.getItem('v7AIChatbotActiveTab') || '#general';
			if ($(activeTab).length) {
				$('.v7-ai-chatbot-tab-content').hide();
				$('.nav-tab').removeClass('nav-tab-active');
				$(activeTab).show();
				$('[href="' + activeTab + '"]').addClass('nav-tab-active');
			}
		},

		setupProviderSelector: function () {
			const self = this;
			// Changing provider resets to that provider's default model...
			$('#v7-ai-provider').on('change', function () {
				self.updateProviderInfo($(this).val(), false);
				if (typeof V7AIProviderModels !== 'undefined') {
					V7AIProviderModels.updateAPIKeyFields($(this).val());
				}
				self.markSavedKeyField($(this).val());
			});

			// ...but simply loading the page must keep the saved model.
			const currentProvider = $('#v7-ai-provider').val();
			if (currentProvider) {
				self.updateProviderInfo(currentProvider, true);
				if (typeof V7AIProviderModels !== 'undefined') {
					V7AIProviderModels.updateAPIKeyFields(currentProvider);
				}
				self.markSavedKeyField(currentProvider);
			}
		},

		markSavedKeyField: function (provider) {
			const savedProviders = (typeof v7AiChatbotAdminParams !== 'undefined' && v7AiChatbotAdminParams.savedKeyProviders) || [];
			const row = $('[data-api-key-field="' + provider + '"]');
			if (!row.length) return;

			row.find('.v7-key-saved-hint').remove();

			if (savedProviders.indexOf(provider) !== -1) {
				row.find('input[type="password"]').after(
					'<p class="v7-key-saved-hint description" style="color:#0a7c2f;">✓ ' +
					'A key is already saved for this provider. Leave this field blank to keep it, or enter a new key to replace it.' +
					'</p>'
				);
			}
		},

		updateProviderInfo: function (provider, preserveModelSelection) {
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

			if (typeof V7AIProviderModels !== 'undefined') {
				V7AIProviderModels.updateModels(provider, !!preserveModelSelection);
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
						nonce: (typeof v7AiChatbotAdminParams !== 'undefined' ? v7AiChatbotAdminParams.nonce : ''),
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
						nonce: (typeof v7AiChatbotAdminParams !== 'undefined' ? v7AiChatbotAdminParams.nonce : ''),
					},
					success: function (response) {
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

		$('form').on('submit', function (e) {
			$('.v7-ai-chatbot-loader').remove();

			const form = this;
			const invalidField = form.querySelector(':invalid');
			if (invalidField) {
				const tabContent = $(invalidField).closest('.v7-ai-chatbot-tab-content');
				if (tabContent.length && !tabContent.is(':visible')) {
					$('.v7-ai-chatbot-tab-content').hide();
					$('.nav-tab').removeClass('nav-tab-active');
					tabContent.show();
					$('[href="#' + tabContent.attr('id') + '"]').addClass('nav-tab-active');
				}
			}
		});
	});
})(jQuery);
