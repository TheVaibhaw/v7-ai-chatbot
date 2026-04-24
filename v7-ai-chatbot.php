<?php
/*
Plugin Name: V7 AI Chatbot
Plugin URI: https://github.com/TheVaibhaw/v7-ai-chatbot
Description: AI-powered chatbot for WordPress sites. Provides intelligent customer support using your site content.
Version: 1.0.0
Author: Vaibhaw Kumar Parashar
Author URI: https://vaibhawkumarparashar.in
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: v7-ai-chatbot
Requires at least: 5.0
Requires PHP: 7.4
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('V7_AI_CHATBOT_VERSION', '1.0.0');
define('V7_AI_CHATBOT_PATH', plugin_dir_path(__FILE__));
define('V7_AI_CHATBOT_URL', plugin_dir_url(__FILE__));

class V7_AI_Chatbot
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_footer', [$this, 'render_chatbot']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
        add_action('wp_ajax_v7_chatbot_query', [$this, 'handle_query']);
        add_action('wp_ajax_nopriv_v7_chatbot_query', [$this, 'handle_query']);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'settings_link']);
    }

    public function settings_link($links)
    {
        array_unshift($links, '<a href="' . admin_url('admin.php?page=v7-ai-chatbot') . '">' . esc_html__('Settings', 'v7-ai-chatbot') . '</a>');
        return $links;
    }

    public function add_menu()
    {
        add_menu_page('V7 AI Chatbot', 'V7 AI Chatbot', 'manage_options', 'v7-ai-chatbot', [$this, 'settings_page'], 'dashicons-format-chat', 30);
    }

    public function register_settings()
    {
        register_setting('v7_ai_chatbot', 'v7_ai_chatbot_settings', [$this, 'sanitize_settings']);
    }

    public function sanitize_settings($input)
    {
        $sanitized = [];
        $sanitized['enabled'] = !empty($input['enabled']) ? 1 : 0;
        $sanitized['api_key'] = isset($input['api_key']) ? sanitize_text_field($input['api_key']) : '';
        $sanitized['api_provider'] = isset($input['api_provider']) ? sanitize_text_field($input['api_provider']) : 'openai';
        $sanitized['model'] = isset($input['model']) ? sanitize_text_field($input['model']) : 'gpt-3.5-turbo';
        $sanitized['position'] = isset($input['position']) ? sanitize_text_field($input['position']) : 'bottom-right';
        $sanitized['primary_color'] = isset($input['primary_color']) ? sanitize_hex_color($input['primary_color']) : '#0073aa';
        $sanitized['bubble_color'] = isset($input['bubble_color']) ? sanitize_hex_color($input['bubble_color']) : '#0073aa';
        $sanitized['text_color'] = isset($input['text_color']) ? sanitize_hex_color($input['text_color']) : '#ffffff';
        $sanitized['greeting'] = isset($input['greeting']) ? sanitize_text_field($input['greeting']) : 'Hi! How can I help you today?';
        $sanitized['placeholder'] = isset($input['placeholder']) ? sanitize_text_field($input['placeholder']) : 'Type your message...';
        $sanitized['max_tokens'] = isset($input['max_tokens']) ? absint($input['max_tokens']) : 500;
        $sanitized['temperature'] = isset($input['temperature']) ? floatval($input['temperature']) : 0.7;
        $sanitized['include_pages'] = !empty($input['include_pages']) ? 1 : 0;
        $sanitized['include_posts'] = !empty($input['include_posts']) ? 1 : 0;
        $sanitized['include_products'] = !empty($input['include_products']) ? 1 : 0;
        return $sanitized;
    }

    public function settings_page()
    {
        if (!current_user_can('manage_options')) wp_die(esc_html__('Unauthorized', 'v7-ai-chatbot'));
        $settings = get_option('v7_ai_chatbot_settings', $this->get_defaults());
?>
        <div class="wrap v7-ai-chatbot-admin">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php settings_errors('v7_ai_chatbot_settings'); ?>
            <form method="post" action="options.php">
                <?php settings_fields('v7_ai_chatbot'); ?>
                <div class="v7-settings-container">
                    <div class="v7-settings-main">
                        <div class="v7-card">
                            <h2><?php esc_html_e('General Settings', 'v7-ai-chatbot'); ?></h2>
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e('Enable Chatbot', 'v7-ai-chatbot'); ?></th>
                                    <td>
                                        <label><input type="checkbox" name="v7_ai_chatbot_settings[enabled]" value="1" <?php checked($settings['enabled'], 1); ?>> <?php esc_html_e('Show chatbot on frontend', 'v7-ai-chatbot'); ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Position', 'v7-ai-chatbot'); ?></th>
                                    <td>
                                        <select name="v7_ai_chatbot_settings[position]">
                                            <option value="bottom-right" <?php selected($settings['position'], 'bottom-right'); ?>><?php esc_html_e('Bottom Right', 'v7-ai-chatbot'); ?></option>
                                            <option value="bottom-left" <?php selected($settings['position'], 'bottom-left'); ?>><?php esc_html_e('Bottom Left', 'v7-ai-chatbot'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Greeting Message', 'v7-ai-chatbot'); ?></th>
                                    <td><input type="text" name="v7_ai_chatbot_settings[greeting]" value="<?php echo esc_attr($settings['greeting']); ?>" class="regular-text"></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Input Placeholder', 'v7-ai-chatbot'); ?></th>
                                    <td><input type="text" name="v7_ai_chatbot_settings[placeholder]" value="<?php echo esc_attr($settings['placeholder']); ?>" class="regular-text"></td>
                                </tr>
                            </table>
                        </div>

                        <div class="v7-card">
                            <h2><?php esc_html_e('AI Configuration', 'v7-ai-chatbot'); ?></h2>
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e('API Provider', 'v7-ai-chatbot'); ?></th>
                                    <td>
                                        <select name="v7_ai_chatbot_settings[api_provider]" id="v7-api-provider">
                                            <option value="groq" <?php selected($settings['api_provider'], 'groq'); ?>>Groq (FREE)</option>
                                            <option value="gemini" <?php selected($settings['api_provider'], 'gemini'); ?>>Google Gemini (FREE)</option>
                                            <option value="openai" <?php selected($settings['api_provider'], 'openai'); ?>>OpenAI (Paid)</option>
                                        </select>
                                        <p class="description" id="v7-api-help"></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('API Key', 'v7-ai-chatbot'); ?></th>
                                    <td><input type="password" name="v7_ai_chatbot_settings[api_key]" value="<?php echo esc_attr($settings['api_key']); ?>" class="regular-text" autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Model', 'v7-ai-chatbot'); ?></th>
                                    <td>
                                        <select name="v7_ai_chatbot_settings[model]" id="v7-model" class="regular-text">
                                            <optgroup label="Groq Models" class="v7-models-groq">
                                                <option value="llama-3.3-70b-versatile" <?php selected($settings['model'], 'llama-3.3-70b-versatile'); ?>>Llama 3.3 70B Versatile (Recommended)</option>
                                                <option value="llama-3.1-8b-instant" <?php selected($settings['model'], 'llama-3.1-8b-instant'); ?>>Llama 3.1 8B Instant (Fast)</option>
                                                <option value="llama-3.2-90b-vision-preview" <?php selected($settings['model'], 'llama-3.2-90b-vision-preview'); ?>>Llama 3.2 90B Vision</option>
                                                <option value="mixtral-8x7b-32768" <?php selected($settings['model'], 'mixtral-8x7b-32768'); ?>>Mixtral 8x7B (32K context)</option>
                                                <option value="gemma2-9b-it" <?php selected($settings['model'], 'gemma2-9b-it'); ?>>Gemma 2 9B</option>
                                            </optgroup>
                                            <optgroup label="Google Gemini Models" class="v7-models-gemini">
                                                <option value="gemini-1.5-flash" <?php selected($settings['model'], 'gemini-1.5-flash'); ?>>Gemini 1.5 Flash (Fast)</option>
                                                <option value="gemini-1.5-flash-8b" <?php selected($settings['model'], 'gemini-1.5-flash-8b'); ?>>Gemini 1.5 Flash 8B (Fastest)</option>
                                                <option value="gemini-1.5-pro" <?php selected($settings['model'], 'gemini-1.5-pro'); ?>>Gemini 1.5 Pro (Best Quality)</option>
                                                <option value="gemini-2.0-flash-exp" <?php selected($settings['model'], 'gemini-2.0-flash-exp'); ?>>Gemini 2.0 Flash (Experimental)</option>
                                            </optgroup>
                                            <optgroup label="OpenAI Models" class="v7-models-openai">
                                                <option value="gpt-3.5-turbo" <?php selected($settings['model'], 'gpt-3.5-turbo'); ?>>GPT-3.5 Turbo (Budget)</option>
                                                <option value="gpt-4" <?php selected($settings['model'], 'gpt-4'); ?>>GPT-4 (Powerful)</option>
                                                <option value="gpt-4-turbo" <?php selected($settings['model'], 'gpt-4-turbo'); ?>>GPT-4 Turbo (Fast + Powerful)</option>
                                                <option value="gpt-4o" <?php selected($settings['model'], 'gpt-4o'); ?>>GPT-4o (Latest)</option>
                                                <option value="gpt-4o-mini" <?php selected($settings['model'], 'gpt-4o-mini'); ?>>GPT-4o Mini (Cost Effective)</option>
                                            </optgroup>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Max Tokens', 'v7-ai-chatbot'); ?></th>
                                    <td><input type="number" name="v7_ai_chatbot_settings[max_tokens]" value="<?php echo esc_attr($settings['max_tokens']); ?>" min="50" max="2000"></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Temperature', 'v7-ai-chatbot'); ?></th>
                                    <td>
                                        <input type="number" name="v7_ai_chatbot_settings[temperature]" value="<?php echo esc_attr($settings['temperature']); ?>" min="0" max="2" step="0.1">
                                        <p class="description"><?php esc_html_e('0-2 (Lower = more focused, Higher = more creative)', 'v7-ai-chatbot'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="v7-card">
                            <h2><?php esc_html_e('Content Sources', 'v7-ai-chatbot'); ?></h2>
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e('Include Content', 'v7-ai-chatbot'); ?></th>
                                    <td>
                                        <label><input type="checkbox" name="v7_ai_chatbot_settings[include_pages]" value="1" <?php checked($settings['include_pages'], 1); ?>> <?php esc_html_e('Pages', 'v7-ai-chatbot'); ?></label><br>
                                        <label><input type="checkbox" name="v7_ai_chatbot_settings[include_posts]" value="1" <?php checked($settings['include_posts'], 1); ?>> <?php esc_html_e('Posts', 'v7-ai-chatbot'); ?></label><br>
                                        <label><input type="checkbox" name="v7_ai_chatbot_settings[include_products]" value="1" <?php checked($settings['include_products'], 1); ?>> <?php esc_html_e('Products (WooCommerce)', 'v7-ai-chatbot'); ?></label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="v7-card">
                            <h2><?php esc_html_e('Appearance', 'v7-ai-chatbot'); ?></h2>
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e('Primary Color', 'v7-ai-chatbot'); ?></th>
                                    <td><input type="color" name="v7_ai_chatbot_settings[primary_color]" value="<?php echo esc_attr($settings['primary_color']); ?>"></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Chat Bubble Color', 'v7-ai-chatbot'); ?></th>
                                    <td><input type="color" name="v7_ai_chatbot_settings[bubble_color]" value="<?php echo esc_attr($settings['bubble_color']); ?>"></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Text Color', 'v7-ai-chatbot'); ?></th>
                                    <td><input type="color" name="v7_ai_chatbot_settings[text_color]" value="<?php echo esc_attr($settings['text_color']); ?>"></td>
                                </tr>
                            </table>
                        </div>

                        <?php submit_button(); ?>
                    </div>

                    <div class="v7-settings-sidebar">
                        <div class="v7-card">
                            <h3><?php esc_html_e('Quick Stats', 'v7-ai-chatbot'); ?></h3>
                            <ul class="v7-stats">
                                <li><strong><?php echo esc_html($this->get_pages_count()); ?></strong> <?php esc_html_e('Pages', 'v7-ai-chatbot'); ?></li>
                                <li><strong><?php echo esc_html($this->get_posts_count()); ?></strong> <?php esc_html_e('Posts', 'v7-ai-chatbot'); ?></li>
                                <li><strong><?php echo esc_html($this->get_products_count()); ?></strong> <?php esc_html_e('Products', 'v7-ai-chatbot'); ?></li>
                            </ul>
                        </div>

                        <div class="v7-card">
                            <h3><?php esc_html_e('Get Free API Keys', 'v7-ai-chatbot'); ?></h3>
                            <ul style="margin:0;padding-left:20px">
                                <li><strong>Groq (FREE):</strong><br><a href="https://console.groq.com/keys" target="_blank">console.groq.com/keys</a></li>
                                <li><strong>Gemini (FREE):</strong><br><a href="https://makersuite.google.com/app/apikey" target="_blank">makersuite.google.com</a></li>
                                <li><strong>OpenAI (Paid):</strong><br><a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com</a></li>
                            </ul>
                        </div>

                        <div class="v7-card">
                            <h3><?php esc_html_e('Instructions', 'v7-ai-chatbot'); ?></h3>
                            <ol>
                                <li><?php esc_html_e('Choose API provider', 'v7-ai-chatbot'); ?></li>
                                <li><?php esc_html_e('Get your free API key', 'v7-ai-chatbot'); ?></li>
                                <li><?php esc_html_e('Enter API key', 'v7-ai-chatbot'); ?></li>
                                <li><?php esc_html_e('Select content sources', 'v7-ai-chatbot'); ?></li>
                                <li><?php esc_html_e('Enable the chatbot', 'v7-ai-chatbot'); ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </form>
        </div>
<?php
    }

    private function get_defaults()
    {
        return [
            'enabled' => 0,
            'api_key' => '',
            'api_provider' => 'groq',
            'model' => 'llama-3.3-70b-versatile',
            'position' => 'bottom-right',
            'primary_color' => '#0073aa',
            'bubble_color' => '#0073aa',
            'text_color' => '#ffffff',
            'greeting' => 'Hi! How can I help you today?',
            'placeholder' => 'Type your message...',
            'max_tokens' => 500,
            'temperature' => 0.7,
            'include_pages' => 1,
            'include_posts' => 1,
            'include_products' => 0
        ];
    }

    private function get_pages_count()
    {
        return wp_count_posts('page')->publish;
    }

    private function get_posts_count()
    {
        return wp_count_posts('post')->publish;
    }

    private function get_products_count()
    {
        if (class_exists('WooCommerce')) {
            return wp_count_posts('product')->publish;
        }
        return 0;
    }

    public function enqueue_admin($hook)
    {
        if ('toplevel_page_v7-ai-chatbot' !== $hook) return;
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_add_inline_style('wp-admin', '.v7-ai-chatbot-admin .v7-settings-container{display:grid;grid-template-columns:1fr 300px;gap:20px}.v7-ai-chatbot-admin .v7-card{background:#fff;border:1px solid #ccd0d4;box-shadow:0 1px 1px rgba(0,0,0,.04);padding:20px;margin-bottom:20px}.v7-ai-chatbot-admin .v7-card h2,.v7-ai-chatbot-admin .v7-card h3{margin-top:0}.v7-ai-chatbot-admin .v7-stats{list-style:none;padding:0;margin:0}.v7-ai-chatbot-admin .v7-stats li{padding:10px 0;border-bottom:1px solid #f0f0f1}.v7-ai-chatbot-admin .v7-stats li:last-child{border:0}.v7-ai-chatbot-admin ol{padding-left:20px}#v7-model{min-width:300px}@media(max-width:782px){.v7-ai-chatbot-admin .v7-settings-container{grid-template-columns:1fr}}');
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($){
                function filterModels(){
                    var provider=$("#v7-api-provider").val();
                    $("#v7-model optgroup").hide();
                    $(".v7-models-"+provider).show();
                    var currentVal=$("#v7-model").val();
                    var visibleOptions=$(".v7-models-"+provider+" option");
                    var isCurrentVisible=false;
                    visibleOptions.each(function(){if($(this).val()===currentVal)isCurrentVisible=true;});
                    if(!isCurrentVisible&&visibleOptions.length>0)$("#v7-model").val(visibleOptions.first().val());
                }
                $("#v7-api-provider").on("change",filterModels);
                filterModels();
            });
        ');
    }

    public function enqueue_frontend()
    {
        $settings = get_option('v7_ai_chatbot_settings', $this->get_defaults());
        if (empty($settings['enabled'])) return;

        wp_enqueue_style('v7-ai-chatbot', V7_AI_CHATBOT_URL . 'assets/css/chatbot.css', [], '1.0.0');
        wp_enqueue_script('v7-ai-chatbot', V7_AI_CHATBOT_URL . 'assets/js/chatbot.js', ['jquery'], '1.0.0', true);

        wp_localize_script('v7-ai-chatbot', 'v7AiChatbot', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('v7_chatbot_nonce'),
            'settings' => array(
                'greeting' => $settings['greeting'],
                'placeholder' => $settings['placeholder'],
                'position' => $settings['position'],
                'primaryColor' => $settings['primary_color'],
                'bubbleColor' => $settings['bubble_color'],
                'textColor' => $settings['text_color']
            )
        ));

        $custom_css = ":root{--v7-primary:{$settings['primary_color']};--v7-bubble:{$settings['bubble_color']};--v7-text:{$settings['text_color']}}";
        wp_add_inline_style('v7-ai-chatbot', $custom_css);
    }

    public function render_chatbot()
    {
        $settings = get_option('v7_ai_chatbot_settings', $this->get_defaults());
        if (empty($settings['enabled'])) return;

        include V7_AI_CHATBOT_PATH . 'templates/chatbot.php';
    }

    public function handle_query()
    {
        check_ajax_referer('v7_chatbot_nonce', 'nonce');

        $message = isset($_POST['message']) ? sanitize_text_field(wp_unslash($_POST['message'])) : '';

        if (empty($message)) {
            wp_send_json_error(['message' => esc_html__('Please enter a message', 'v7-ai-chatbot')]);
        }

        $settings = get_option('v7_ai_chatbot_settings', $this->get_defaults());

        if (empty($settings['api_key'])) {
            wp_send_json_error(['message' => esc_html__('API key not configured', 'v7-ai-chatbot')]);
        }

        $context = $this->get_site_context($settings);
        $response = $this->query_ai($message, $context, $settings);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $response->get_error_message()]);
        }

        wp_send_json_success(['message' => $response]);
    }

    private function get_site_context($settings)
    {
        $context = "Site: " . get_bloginfo('name') . "\nURL: " . home_url() . "\nDescription: " . get_bloginfo('description') . "\n\n";
        $context .= "IMPORTANT: Only answer questions about this website and its content. If asked about anything else, politely decline and redirect to site-related topics.\n\n";
        $context .= "Available Content:\n";

        if (!empty($settings['include_pages'])) {
            $pages = get_posts(['post_type' => 'page', 'posts_per_page' => 50, 'post_status' => 'publish']);
            $context .= "Pages:\n";
            foreach ($pages as $page) {
                $context .= "- " . $page->post_title . ": " . wp_trim_words($page->post_content, 50) . "\n";
            }
        }

        if (!empty($settings['include_posts'])) {
            $posts = get_posts(['post_type' => 'post', 'posts_per_page' => 50, 'post_status' => 'publish']);
            $context .= "Blog Posts:\n";
            foreach ($posts as $post) {
                $context .= "- " . $post->post_title . ": " . wp_trim_words($post->post_content, 50) . "\n";
            }
        }

        if (!empty($settings['include_products']) && class_exists('WooCommerce')) {
            $products = get_posts(['post_type' => 'product', 'posts_per_page' => 50, 'post_status' => 'publish']);
            $context .= "Products:\n";
            foreach ($products as $product) {
                $context .= "- " . $product->post_title . ": " . wp_trim_words($product->post_content, 30) . "\n";
            }
        }

        return $context;
    }

    private function query_ai($message, $context, $settings)
    {
        switch ($settings['api_provider']) {
            case 'groq':
                return $this->query_groq($message, $context, $settings);
            case 'gemini':
                return $this->query_gemini($message, $context, $settings);
            case 'openai':
                return $this->query_openai($message, $context, $settings);
            default:
                return new WP_Error('invalid_provider', esc_html__('Invalid API provider', 'v7-ai-chatbot'));
        }
    }

    private function query_groq($message, $context, $settings)
    {
        $api_url = 'https://api.groq.com/openai/v1/chat/completions';
        $request_body = array(
            'model' => $settings['model'],
            'messages' => array(
                array('role' => 'system', 'content' => $context),
                array('role' => 'user', 'content' => $message)
            ),
            'max_tokens' => intval($settings['max_tokens']),
            'temperature' => floatval($settings['temperature'])
        );
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $settings['api_key'],
                'Content-Type' => 'application/json'
            ),
            'body' => wp_json_encode($request_body),
            'timeout' => 60
        ));
        if (is_wp_error($response)) return $response;
        $code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (200 !== $code) {
            $error_msg = isset($body['error']['message']) ? $body['error']['message'] : 'Groq API error (Code: ' . $code . ')';
            return new WP_Error('api_error', esc_html($error_msg));
        }
        if (!isset($body['choices'][0]['message']['content'])) return new WP_Error('invalid_response', esc_html__('Invalid response from Groq', 'v7-ai-chatbot'));
        return wp_kses_post($body['choices'][0]['message']['content']);
    }

    private function query_gemini($message, $context, $settings)
    {
        $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $settings['model'] . ':generateContent?key=' . $settings['api_key'];
        $body = array(
            'contents' => array(
                array('parts' => array(array('text' => $context . "\n\nUser: " . $message)))
            ),
            'generationConfig' => array(
                'maxOutputTokens' => intval($settings['max_tokens']),
                'temperature' => floatval($settings['temperature'])
            )
        );
        $response = wp_remote_post($api_url, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode($body),
            'timeout' => 30
        ));
        if (is_wp_error($response)) return $response;
        $code = wp_remote_retrieve_response_code($response);
        if (200 !== $code) return new WP_Error('api_error', esc_html__('Gemini API request failed', 'v7-ai-chatbot'));
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (!isset($body['candidates'][0]['content']['parts'][0]['text'])) return new WP_Error('invalid_response', esc_html__('Invalid response', 'v7-ai-chatbot'));
        return wp_kses_post($body['candidates'][0]['content']['parts'][0]['text']);
    }

    private function query_openai($message, $context, $settings)
    {
        $api_url = 'https://api.openai.com/v1/chat/completions';

        $body = array(
            'model' => $settings['model'],
            'messages' => array(
                array('role' => 'system', 'content' => $context),
                array('role' => 'user', 'content' => $message)
            ),
            'max_tokens' => intval($settings['max_tokens']),
            'temperature' => floatval($settings['temperature'])
        );

        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $settings['api_key'],
                'Content-Type' => 'application/json'
            ),
            'body' => wp_json_encode($body),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if (200 !== $response_code) {
            return new WP_Error('api_error', esc_html__('API request failed', 'v7-ai-chatbot'));
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($body['choices'][0]['message']['content'])) {
            return new WP_Error('invalid_response', esc_html__('Invalid API response', 'v7-ai-chatbot'));
        }

        return wp_kses_post($body['choices'][0]['message']['content']);
    }
}

function v7_ai_chatbot_activate()
{
    add_option('v7_ai_chatbot_redirect', 1);
}
register_activation_hook(__FILE__, 'v7_ai_chatbot_activate');

function v7_ai_chatbot_redirect()
{
    if (get_option('v7_ai_chatbot_redirect')) {
        delete_option('v7_ai_chatbot_redirect');
        $activate_multi = filter_input(INPUT_GET, 'activate-multi', FILTER_VALIDATE_BOOLEAN);
        if (!$activate_multi) {
            wp_safe_redirect(admin_url('admin.php?page=v7-ai-chatbot'));
            exit;
        }
    }
}
add_action('admin_init', 'v7_ai_chatbot_redirect');

V7_AI_Chatbot::get_instance();
