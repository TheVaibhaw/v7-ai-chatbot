<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="v7-ai-chatbot-container" class="<?php echo esc_attr($settings['position']); ?>">
    <div id="v7-ai-chatbot-button" style="display:none">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="currentColor" />
            <path d="M7 9H17V11H7V9ZM7 12H14V14H7V12Z" fill="currentColor" />
        </svg>
    </div>
    <div id="v7-ai-chatbot-window">
        <div id="v7-ai-chatbot-header">
            <h3><?php echo esc_html(get_bloginfo('name')); ?></h3>
            <button id="v7-ai-chatbot-close">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>
        <div id="v7-ai-chatbot-messages">
            <div class="v7-ai-chatbot-message v7-ai-chatbot-bot-message">
                <div class="v7-ai-chatbot-message-content"><?php echo esc_html($settings['greeting']); ?></div>
            </div>
        </div>
        <div id="v7-ai-chatbot-input-container">
            <input type="text" id="v7-ai-chatbot-input" placeholder="<?php echo esc_attr($settings['placeholder']); ?>">
            <button id="v7-ai-chatbot-send">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 10L18 2L10 18L8 11L2 10Z" fill="currentColor" />
                </svg>
            </button>
        </div>

    </div>
</div>