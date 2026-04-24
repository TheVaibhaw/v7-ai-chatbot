=== V7 AI Chatbot ===
Contributors: thevaibhaw
Tags: chatbot, ai, customer support, ai chatbot, site assistant
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI-powered chatbot for WordPress. Provides intelligent customer support using your site content with Groq, Google Gemini, or OpenAI.

== Description ==

V7 AI Chatbot is an intelligent customer support solution that answers questions exclusively about your website content. It supports multiple AI providers — Groq (free), Google Gemini (free), and OpenAI (paid) — giving you the flexibility to choose what works best for your site.

= Key Features =

* AI-powered responses using Groq, Google Gemini, or OpenAI
* Site-specific answers only — won't respond to off-topic queries
* Indexes pages, posts, and WooCommerce products
* Fully customizable appearance
* Responsive mobile-friendly design
* Clean, optimized code
* Easy setup with admin panel
* Multiple color customization options
* Adjustable position (bottom-right or bottom-left)
* Customizable greeting and placeholder text
* Secure AJAX implementation with nonces

= Use Cases =

* Customer support automation
* Product information assistance
* Site navigation help
* Content discovery
* FAQ automation

= Requirements =

* An API key from one of the supported providers: Groq (free), Google Gemini (free), or OpenAI (paid)
* WordPress 5.0+
* PHP 7.4+

== Installation ==

1. Upload plugin files to `/wp-content/plugins/v7-ai-chatbot/`
2. Activate the plugin through Plugins menu
3. Go to V7 AI Chatbot settings
4. Choose your AI provider (Groq, Gemini, or OpenAI)
5. Enter your API key
6. Select content sources (pages, posts, products)
7. Enable the chatbot
8. Customize appearance to match your brand

== Frequently Asked Questions ==

= Which AI providers are supported? =

The plugin supports three providers: Groq (free), Google Gemini (free), and OpenAI (paid). You can choose your preferred provider from the settings page.

= Will it answer questions not related to my site? =

No, the chatbot is specifically configured to only answer questions about your website content. It will politely decline off-topic queries.

= What content can it access? =

The chatbot can access your published pages, posts, and WooCommerce products (if enabled in settings).

= Is it mobile responsive? =

Yes, the chatbot is fully responsive and works perfectly on all devices.

= Can I customize the appearance? =

Yes, you can customize colors, position, greeting message, and placeholder text from the admin panel.

= Does it work with WooCommerce? =

Yes, you can enable product indexing to help customers with product-related questions.

= Is my data secure? =

Yes, all communications are secured with WordPress nonces and sanitized inputs. Only your site content and user queries are sent to the selected AI provider.

== Screenshots ==

1. Admin settings panel
2. Chatbot widget on frontend
3. Chat conversation example
4. Customization options

== Changelog ==

= 1.0.0 =
* Initial release
* Groq, Google Gemini, and OpenAI integration
* Content indexing system
* Customizable appearance
* Responsive design
* Admin settings panel

== Upgrade Notice ==

= 1.0.0 =
Initial release of V7 AI Chatbot.

== External services ==

This plugin connects to third-party AI services to process user queries and generate chatbot responses. The user's message and your site content (pages, posts, and/or products as configured) are sent to the selected AI provider when a visitor uses the chatbot.

= Groq =

When Groq is selected as the AI provider, user messages and site content are sent to the Groq API for processing.

* Service URL: [https://groq.com](https://groq.com)
* API endpoint used: `https://api.groq.com/openai/v1/chat/completions`
* Terms of Use: [https://groq.com/terms-of-use/](https://groq.com/terms-of-use/)
* Privacy Policy: [https://groq.com/privacy-policy/](https://groq.com/privacy-policy/)

= Google Gemini =

When Google Gemini is selected as the AI provider, user messages and site content are sent to the Google Generative Language API for processing.

* Service URL: [https://ai.google.dev](https://ai.google.dev)
* API endpoint used: `https://generativelanguage.googleapis.com/v1beta/models/`
* Terms of Service: [https://ai.google.dev/gemini-api/terms](https://ai.google.dev/gemini-api/terms)
* Privacy Policy: [https://ai.google.dev/gemini-api/terms](https://ai.google.dev/gemini-api/terms)

= OpenAI =

When OpenAI is selected as the AI provider, user messages and site content are sent to the OpenAI API for processing.

* Service URL: [https://openai.com](https://openai.com)
* API endpoint used: `https://api.openai.com/v1/chat/completions`
* Terms of Use: [https://openai.com/policies/terms-of-use/](https://openai.com/policies/terms-of-use/)
* Privacy Policy: [https://openai.com/policies/privacy-policy/](https://openai.com/policies/privacy-policy/)

No user data is stored by this plugin beyond your API key in the WordPress database. The plugin does not track users or collect any personal data on its own.
