<?php
/**
 * Plugin Name: UK Post Add JS
 * Description: 投稿、固定ページ、カスタム投稿の編集画面にJavaScript入力フィールドを追加し、フロントエンドで出力します。
 * Version: 1.0.0
 * Author: Y.U.
 */

// プラグインの直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

class UK_Add_JS {
    
    private $meta_key = 'uk_custom_js';
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // 管理画面でのみ実行
        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'add_custom_js_meta_box'));
            add_action('save_post', array($this, 'save_custom_js_meta_box'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        }
        
        // フロントエンドでの出力
        add_action('wp_footer', array($this, 'output_custom_js'), 999);
    }
    
    /**
     * 管理画面のスクリプトとスタイルを読み込み
     */
    public function enqueue_admin_scripts($hook) {
        // 投稿編集画面でのみ読み込み
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'uk-post-add-js-admin-style',
            plugin_dir_url(__FILE__) . 'admin-style.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'uk-post-add-js-admin-script',
            plugin_dir_url(__FILE__) . 'admin-script.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
    
    /**
     * カスタムフィールドのメタボックスを追加
     */
    public function add_custom_js_meta_box() {
        // 管理者権限のチェック
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $post_types = get_post_types(array('public' => true), 'names');
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'uk-custom-js-meta-box',
                'カスタムJavaScript',
                array($this, 'custom_js_meta_box_callback'),
                $post_type,
                'normal',
                'low'
            );
        }
    }
    
    /**
     * メタボックスのHTML出力
     */
    public function custom_js_meta_box_callback($post) {
        // 管理者権限のチェック
        if (!current_user_can('manage_options')) {
            echo '<p>このフィールドを編集するには管理者権限が必要です。</p>';
            return;
        }
        
        // nonce field for security
        wp_nonce_field('uk_custom_js_meta_box', 'uk_custom_js_meta_box_nonce');
        
        // 現在の値を取得
        $custom_js = get_post_meta($post->ID, $this->meta_key, true);
        
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="uk_custom_js">JavaScript コード</label></th>';
        echo '<td>';
        echo '<textarea id="uk_custom_js" name="uk_custom_js" rows="10" cols="50" style="width: 100%;" placeholder="ここにJavaScriptコードを入力してください（&lt;script&gt;タグも含めて入力）">' . esc_textarea($custom_js) . '</textarea>';
        echo '<p class="description">このページ/投稿でのみ実行されるJavaScriptコードを入力してください。&lt;script&gt;タグも含めて入力してください。外部CDNからの読み込みも可能です。</p>';
        echo '<p class="description uk-js-description"><strong>注意:</strong> 悪意のあるコードはサイトの動作に影響を与える可能性があります。信頼できるコードのみを入力してください。</p>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }
    
    /**
     * メタボックスの値を保存
     */
    public function save_custom_js_meta_box($post_id) {
        // nonceチェック
        if (!isset($_POST['uk_custom_js_meta_box_nonce']) || !wp_verify_nonce($_POST['uk_custom_js_meta_box_nonce'], 'uk_custom_js_meta_box')) {
            return;
        }
        
        // 権限チェック（管理者権限が必要）
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // 編集権限チェック
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // 自動保存の場合はスキップ
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // リビジョンの場合はスキップ
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        // 値を取得してサニタイズ
        $custom_js = isset($_POST['uk_custom_js']) ? stripslashes($_POST['uk_custom_js']) : '';
        
        // 基本的なサニタイゼーション（危険なタグを除去）
        $custom_js = wp_kses($custom_js, array());
        
        // メタ値を更新
        if (!empty($custom_js)) {
            update_post_meta($post_id, $this->meta_key, $custom_js);
        } else {
            delete_post_meta($post_id, $this->meta_key);
        }
    }
    
    /**
     * フロントエンドでカスタムJavaScriptを出力
     */
    public function output_custom_js() {
        // 管理画面では出力しない
        if (is_admin()) {
            return;
        }
        
        global $post;
        
        // 投稿/ページが存在しない場合は何もしない
        if (!$post || !is_object($post)) {
            return;
        }
        
        // カスタムJSを取得
        $custom_js = get_post_meta($post->ID, $this->meta_key, true);
        
        // カスタムJSが存在する場合のみ出力
        if (!empty($custom_js)) {
            echo "\n<!-- UK Add JS Plugin Custom JavaScript -->\n";
            echo $custom_js . "\n";
            echo "<!-- /UK Add JS Plugin Custom JavaScript -->\n";
        }
    }
}

// プラグインを初期化
new UK_Add_JS();
