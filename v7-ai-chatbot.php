<?php
/*
Plugin Name: V7 AI Chatbot
Plugin URI: https://github.com/TheVaibhaw/v7-ai-chatbot
Description: AI-powered chatbot for WordPress sites. Provides intelligent customer support using your site content.
Version: 1.0.0
Author: Vaibhaw Kumar Parashar
Author URI: https://vaibhawkumar.in
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: v7-ai-chatbot
Requires at least: 5.0
Tested up to: 7.0
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
        add_action('wp_ajax_v7_ai_chatbot_query', [$this, 'handle_query']);
        add_action('wp_ajax_nopriv_v7_ai_chatbot_query', [$this, 'handle_query']);
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
        register_setting('v7_ai_chatbot_group', 'v7_ai_chatbot_settings', [$this, 'sanitize_settings']);
    }

    public function sanitize_settings($input)
    {
        $sanitized = [];
        $sanitized['enabled'] = !empty($input['enabled']) ? 1 : 0;
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
                <?php settings_fields('v7_ai_chatbot_group'); ?>
                <div class="v7-ai-chatbot-settings-container">
                    <div class="v7-ai-chatbot-settings-main">
                        <div class="v7-ai-chatbot-card">
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

                        <div class="v7-ai-chatbot-card">
                            <h2><?php esc_html_e('Generation Settings', 'v7-ai-chatbot'); ?></h2>
                            <table class="form-table">
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
                            <?php if (!function_exists('wp_ai_client_prompt') || !wp_supports_ai()): ?>
                                <div style="background:#fff8f0;border:1px solid #ffb900;padding:12px;margin-top:15px;border-radius:4px;">
                                    <p><strong><?php esc_html_e('⚠️ AI Features Not Available', 'v7-ai-chatbot'); ?></strong></p>
                                    <p><?php esc_html_e('This plugin requires WordPress 7.0+ with an AI provider configured. Please ask your site administrator to set up an AI provider in WordPress settings.', 'v7-ai-chatbot'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="v7-ai-chatbot-card">
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

                        <div class="v7-ai-chatbot-card">
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

                    <div class="v7-ai-chatbot-settings-sidebar">
                        <div class="v7-ai-chatbot-card">
                            <h3><?php esc_html_e('Quick Stats', 'v7-ai-chatbot'); ?></h3>
                            <ul class="v7-ai-chatbot-stats">
                                <li><strong><?php echo esc_html($this->get_pages_count()); ?></strong> <?php esc_html_e('Pages', 'v7-ai-chatbot'); ?></li>
                                <li><strong><?php echo esc_html($this->get_posts_count()); ?></strong> <?php esc_html_e('Posts', 'v7-ai-chatbot'); ?></li>
                                <li><strong><?php echo esc_html($this->get_products_count()); ?></strong> <?php esc_html_e('Products', 'v7-ai-chatbot'); ?></li>
                            </ul>
                        </div>

                        <div class="v7-ai-chatbot-card">
                            <h3><?php esc_html_e('Setup Instructions', 'v7-ai-chatbot'); ?></h3>
                            <ol>
                                <li><?php esc_html_e('Configure an AI provider in WordPress settings', 'v7-ai-chatbot'); ?></li>
                                <li><?php esc_html_e('Select content sources below', 'v7-ai-chatbot'); ?></li>
                                <li><?php esc_html_e('Adjust generation settings as needed', 'v7-ai-chatbot'); ?></li>
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
        wp_add_inline_style('wp-admin', '.v7-ai-chatbot-admin .v7-ai-chatbot-settings-container{display:grid;grid-template-columns:1fr 300px;gap:20px}.v7-ai-chatbot-admin .v7-ai-chatbot-card{background:#fff;border:1px solid #ccd0d4;box-shadow:0 1px 1px rgba(0,0,0,.04);padding:20px;margin-bottom:20px}.v7-ai-chatbot-admin .v7-ai-chatbot-card h2,.v7-ai-chatbot-admin .v7-ai-chatbot-card h3{margin-top:0}.v7-ai-chatbot-admin .v7-ai-chatbot-stats{list-style:none;padding:0;margin:0}.v7-ai-chatbot-admin .v7-ai-chatbot-stats li{padding:10px 0;border-bottom:1px solid #f0f0f1}.v7-ai-chatbot-admin .v7-ai-chatbot-stats li:last-child{border:0}.v7-ai-chatbot-admin ol{padding-left:20px}@media(max-width:782px){.v7-ai-chatbot-admin .v7-ai-chatbot-settings-container{grid-template-columns:1fr}}');
    }

    public function enqueue_frontend()
    {
        $settings = get_option('v7_ai_chatbot_settings', $this->get_defaults());
        if (empty($settings['enabled'])) return;

        wp_enqueue_style('v7-ai-chatbot', V7_AI_CHATBOT_URL . 'assets/css/chatbot.css', [], '1.0.0');
        wp_enqueue_script('v7-ai-chatbot', V7_AI_CHATBOT_URL . 'assets/js/chatbot.js', ['jquery'], '1.0.0', true);

        wp_localize_script('v7-ai-chatbot', 'v7AiChatbotParams', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('v7_ai_chatbot_nonce'),
            'settings' => array(
                'greeting' => $settings['greeting'],
                'placeholder' => $settings['placeholder'],
                'position' => $settings['position'],
                'primaryColor' => $settings['primary_color'],
                'bubbleColor' => $settings['bubble_color'],
                'textColor' => $settings['text_color']
            )
        ));

        $custom_css = ":root{--v7-ai-chatbot-primary:{$settings['primary_color']};--v7-ai-chatbot-bubble:{$settings['bubble_color']};--v7-ai-chatbot-text:{$settings['text_color']}}";
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
        check_ajax_referer('v7_ai_chatbot_nonce', 'nonce');

        $message = isset($_POST['message']) ? sanitize_text_field(wp_unslash($_POST['message'])) : '';

        if (empty($message)) {
            wp_send_json_error(['message' => esc_html__('Please enter a message', 'v7-ai-chatbot')]);
        }

        $settings = get_option('v7_ai_chatbot_settings', $this->get_defaults());

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
        if (!function_exists('wp_ai_client_prompt') || !wp_supports_ai()) {
            return new WP_Error('ai_unavailable', esc_html__('AI features are not available. Please ask your site administrator to configure an AI provider in WordPress.', 'v7-ai-chatbot'));
        }

        $result = wp_ai_client_prompt($message)
            ->using_system_instruction($context)
            ->using_max_tokens(intval($settings['max_tokens']))
            ->using_temperature(floatval($settings['temperature']))
            ->generate_text();

        if (is_wp_error($result)) {
            return $result;
        }

        return wp_kses_post($result);
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
