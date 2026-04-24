# V7 AI Chatbot - Complete Documentation

## 📋 Table of Contents

1. [Introduction](#introduction)
2. [Features](#features)
3. [Installation](#installation)
4. [Getting API Keys](#getting-api-keys)
5. [Configuration Guide](#configuration-guide)
6. [Supported AI Providers](#supported-ai-providers)
7. [Models Reference](#models-reference)
8. [Customization Options](#customization-options)
9. [Content Sources](#content-sources)
10. [Troubleshooting](#troubleshooting)
11. [FAQ](#faq)
12. [Developer Hooks](#developer-hooks)
13. [Security](#security)
14. [Changelog](#changelog)

---

## Introduction

**V7 AI Chatbot** is a powerful, lightweight WordPress plugin that adds an AI-powered customer support chatbot to your website. The chatbot uses your site's content (pages, posts, products) to answer visitor questions intelligently.

### Key Highlights

- 🤖 AI-powered responses using latest LLM models
- 🔒 Answers ONLY about your website content
- 💰 Supports FREE API providers (Groq, Google Gemini)
- 🎨 Fully customizable appearance
- 📱 Mobile responsive design
- ⚡ Fast and lightweight

---

## Features

### Core Features

| Feature                 | Description                                  |
| ----------------------- | -------------------------------------------- |
| Multi-Provider Support  | Groq, Google Gemini, OpenAI                  |
| Dynamic Model Selection | Models filter based on selected provider     |
| Content Indexing        | Automatically indexes Pages, Posts, Products |
| Domain Restriction      | Only answers questions about your site       |
| Custom Greeting         | Personalized welcome message                 |
| Position Control        | Bottom-right or bottom-left placement        |
| Color Customization     | Primary, bubble, and text colors             |
| Auto-open               | Chatbot opens automatically on page load     |
| Responsive Design       | Works on all devices                         |

### Security Features

- WordPress Nonce verification
- Input sanitization
- Output escaping
- Capability checks
- No data storage (except settings)

---

## Installation

### Method 1: WordPress Admin

1. Go to **Plugins > Add New**
2. Click **Upload Plugin**
3. Select the `v7-ai-chatbot.zip` file
4. Click **Install Now**
5. Click **Activate Plugin**

### Method 2: FTP Upload

1. Extract `v7-ai-chatbot.zip`
2. Upload the `v7-ai-chatbot` folder to `/wp-content/plugins/`
3. Go to **Plugins** in WordPress admin
4. Find **V7 AI Chatbot** and click **Activate**

### Method 3: Manual Installation

```bash
cd /wp-content/plugins/
git clone [repository-url] v7-ai-chatbot
```

---

## Getting API Keys

### 🟢 Groq (FREE - Recommended)

**Cost:** FREE  
**Speed:** ⚡⚡⚡ Extremely Fast  
**Rate Limit:** Generous free tier

**Steps:**

1. Visit [console.groq.com](https://console.groq.com)
2. Sign up with Google/GitHub account
3. Go to **API Keys** section
4. Click **Create API Key**
5. Copy and save the key securely

### 🟢 Google Gemini (FREE Tier)

**Cost:** FREE (15 requests/minute)  
**Speed:** ⚡⚡ Fast  
**Rate Limit:** 15 RPM free

**Steps:**

1. Visit [makersuite.google.com](https://makersuite.google.com/app/apikey)
2. Sign in with Google account
3. Click **Create API Key**
4. Select or create a Google Cloud project
5. Copy the generated key

### 🔴 OpenAI (Paid)

**Cost:** Pay-per-use  
**Speed:** ⚡⚡ Fast  
**Quality:** High

**Steps:**

1. Visit [platform.openai.com](https://platform.openai.com/api-keys)
2. Create an account or sign in
3. Add payment method (required)
4. Go to **API Keys**
5. Click **Create new secret key**
6. Copy and save securely

---

## Configuration Guide

### Step 1: Access Settings

Navigate to **WordPress Admin > V7 AI Chatbot** (left sidebar)

### Step 2: General Settings

| Setting           | Description                   | Recommended                     |
| ----------------- | ----------------------------- | ------------------------------- |
| Enable Chatbot    | Show/hide chatbot on frontend | ✅ Enabled                      |
| Position          | Widget placement              | Bottom Right                    |
| Greeting Message  | First message shown           | "Hi! How can I help you today?" |
| Input Placeholder | Input field hint text         | "Type your message..."          |

### Step 3: AI Configuration

| Setting      | Description           | Recommended       |
| ------------ | --------------------- | ----------------- |
| API Provider | Select AI service     | Groq (FREE)       |
| API Key      | Your API key          | [Your Key]        |
| Model        | AI model to use       | Provider-specific |
| Max Tokens   | Response length limit | 500               |
| Temperature  | Creativity (0-2)      | 0.7               |

### Step 4: Content Sources

| Source   | Description                    |
| -------- | ------------------------------ |
| Pages    | Index all published pages      |
| Posts    | Index all published blog posts |
| Products | Index WooCommerce products     |

### Step 5: Appearance

| Setting       | Description              | Default |
| ------------- | ------------------------ | ------- |
| Primary Color | Header & send button     | #0073aa |
| Bubble Color  | Chat toggle button       | #0073aa |
| Text Color    | Text on colored elements | #ffffff |

### Step 6: Save & Test

1. Click **Save Changes**
2. Visit your website frontend
3. Test the chatbot with a question

---

## Supported AI Providers

### Comparison Table

| Provider   | Cost   | Speed  | Quality | Best For             |
| ---------- | ------ | ------ | ------- | -------------------- |
| **Groq**   | FREE   | ⚡⚡⚡ | High    | Testing & Production |
| **Gemini** | FREE\* | ⚡⚡   | High    | Low traffic sites    |
| **OpenAI** | Paid   | ⚡⚡   | Highest | Premium applications |

\*Free tier with limits

---

## Models Reference

### Groq Models (FREE)

| Model                          | Description                | Best For            |
| ------------------------------ | -------------------------- | ------------------- |
| `llama-3.3-70b-versatile`      | Latest Llama, very capable | General use ⭐      |
| `llama-3.1-8b-instant`         | Smaller, faster            | Quick responses     |
| `llama-3.2-90b-vision-preview` | Vision capable             | Image understanding |
| `mixtral-8x7b-32768`           | Long context (32K)         | Large content       |
| `gemma2-9b-it`                 | Google's Gemma             | Efficient           |

### Google Gemini Models (FREE Tier)

| Model                  | Description      | Best For             |
| ---------------------- | ---------------- | -------------------- |
| `gemini-1.5-flash`     | Fast & efficient | General use ⭐       |
| `gemini-1.5-flash-8b`  | Fastest variant  | High traffic         |
| `gemini-1.5-pro`       | Most capable     | Complex queries      |
| `gemini-2.0-flash-exp` | Experimental     | Testing new features |

### OpenAI Models (Paid)

| Model           | Cost | Description       | Best For         |
| --------------- | ---- | ----------------- | ---------------- |
| `gpt-3.5-turbo` | $    | Budget friendly   | Cost savings ⭐  |
| `gpt-4`         | $$$  | Most powerful     | Complex tasks    |
| `gpt-4-turbo`   | $$   | Fast + powerful   | Balance          |
| `gpt-4o`        | $$   | Latest multimodal | Latest features  |
| `gpt-4o-mini`   | $    | Cost effective    | Budget + quality |

---

## Customization Options

### Color Schemes

**Professional Blue:**

```
Primary: #0073aa
Bubble: #0073aa
Text: #ffffff
```

**Dark Theme:**

```
Primary: #1a1a2e
Bubble: #16213e
Text: #ffffff
```

**Green/Eco:**

```
Primary: #2d6a4f
Bubble: #40916c
Text: #ffffff
```

**Orange/Energetic:**

```
Primary: #e85d04
Bubble: #f48c06
Text: #ffffff
```

**Purple/Creative:**

```
Primary: #7209b7
Bubble: #560bad
Text: #ffffff
```

### Position Options

- **Bottom Right** - Standard placement (recommended)
- **Bottom Left** - Alternative placement

---

## Content Sources

### How Content Indexing Works

1. **Pages** - All published pages are indexed
   - Title + Content summary (50 words)
   - Up to 50 pages

2. **Posts** - All published blog posts
   - Title + Content summary (50 words)
   - Up to 50 posts

3. **Products** (WooCommerce)
   - Product name + Description (30 words)
   - Up to 50 products

### Content Sent to AI

```
Site: Your Site Name
URL: https://yoursite.com
Description: Your site tagline

IMPORTANT: Only answer questions about this website...

Pages:
- About Us: Summary of about page content...
- Contact: Summary of contact page...

Blog Posts:
- Post Title: Summary of post content...

Products:
- Product Name: Product description summary...
```

---

## Troubleshooting

### Common Issues

#### "API key not configured"

**Solution:**

1. Go to V7 AI Chatbot settings
2. Ensure API Provider is selected (not "Custom")
3. Paste your API key
4. Save changes

#### "API request failed"

**Causes & Solutions:**

- Invalid API key → Generate new key
- Model deprecated → Select different model
- Rate limit exceeded → Wait or upgrade plan
- Network error → Check server connectivity

#### Chatbot not showing

**Check:**

1. "Enable Chatbot" is checked
2. No JavaScript errors in browser console
3. Theme doesn't block footer scripts
4. Plugin is activated

#### Slow responses

**Solutions:**

- Use Groq (fastest provider)
- Reduce Max Tokens
- Use smaller/faster model
- Check server resources

#### Wrong answers

**Adjustments:**

- Lower Temperature (0.3-0.5)
- Ensure correct content sources enabled
- Check if pages/posts have content

---

## FAQ

### General Questions

**Q: Is the chatbot free to use?**  
A: The plugin is free. API costs depend on provider (Groq & Gemini have free tiers).

**Q: Will it answer anything users ask?**  
A: No, it only answers questions about YOUR website content.

**Q: Does it store conversations?**  
A: No, conversations are not stored. Only settings are saved.

**Q: Can I use multiple providers?**  
A: One at a time. You can switch anytime in settings.

### Technical Questions

**Q: Does it work with caching plugins?**  
A: Yes, AJAX requests bypass page cache.

**Q: Is it GDPR compliant?**  
A: Plugin doesn't store user data. Check your AI provider's policy.

**Q: Can I modify the chatbot appearance more?**  
A: Yes, use custom CSS in your theme.

**Q: Does it support multiple languages?**  
A: AI responds in the language of the question.

---

## Developer Hooks

### Filters

```php
// Modify site context before sending to AI
add_filter('v7_chatbot_context', function($context, $settings) {
    $context .= "\nCustom instructions here...";
    return $context;
}, 10, 2);

// Modify AI response before displaying
add_filter('v7_chatbot_response', function($response) {
    return $response;
});
```

### Actions

```php
// After chatbot query processed
add_action('v7_chatbot_after_query', function($message, $response) {
    // Log or process
}, 10, 2);
```

### Custom CSS

```css
/* Make chatbot window larger */
#v7-chatbot-window {
  width: 450px;
  height: 600px;
}

/* Custom header style */
#v7-chatbot-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Message bubbles */
.v7-bot-message .v7-message-content {
  background: #f0f0f0;
  border-radius: 18px;
}
```

---

## Security

### Data Handling

- **API Keys:** Stored encrypted in WordPress options
- **User Messages:** Sent to AI provider, not stored
- **Site Content:** Indexed on-demand, not cached
- **No Tracking:** Plugin doesn't track users

### Permissions

- Settings require `manage_options` capability
- Only admins can configure the plugin

### Best Practices

1. Keep API keys private
2. Regenerate keys periodically
3. Monitor API usage
4. Keep plugin updated

---

## Changelog

### Version 1.0.0

- Initial release
- Multi-provider support (Groq, Gemini, OpenAI)
- Dynamic model selection
- Customizable appearance
- Content indexing (Pages, Posts, Products)
- Responsive design
- Auto-open feature
- WordPress coding standards compliant

---

## Support

### Resources

- **Documentation:** This file
- **Plugin Page:** WordPress Admin > V7 AI Chatbot
- **API Docs:**
  - [Groq Documentation](https://console.groq.com/docs)
  - [Gemini Documentation](https://ai.google.dev/docs)
  - [OpenAI Documentation](https://platform.openai.com/docs)

### Reporting Issues

When reporting issues, include:

1. WordPress version
2. Plugin version
3. API provider & model
4. Error message (if any)
5. Browser console errors

---

## Credits

**Developer:** Vaibhaw Kumar Parashar  
**Website:** [vaibhawkumar.in](https://vaibhawkumar.in)  
**License:** GPL v2 or later

---

_Last Updated: February 2026_
