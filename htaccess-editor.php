<?php
/*
Plugin Name: .htaccess Editor
Plugin URI: https://wpxss.com/
Description: Edit the .htaccess file and restore default.
Version: 1.0
Author: Stefan Pejcic
Author URI: https://pejcic.rs/
*/

function htaccess_editor_menu() {
    add_management_page('.htaccess Editor', '.htaccess Editor', 'manage_options', 'htaccess-editor', 'htaccess_editor_page');
}
add_action('admin_menu', 'htaccess_editor_menu');

function htaccess_editor_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    $htaccess_path = ABSPATH . '.htaccess';
    if (isset($_POST['submit'])) {
        $content = wp_kses_post($_POST['content']);
        $result = file_put_contents($htaccess_path, $content);
        if ($result) {
            add_settings_error('htaccess-editor', 'htaccess-editor', 'The .htaccess file has been updated.', 'updated');
        } else {
            add_settings_error('htaccess-editor', 'htaccess-editor', 'There was an error updating the .htaccess file.', 'error');
        }
    } elseif (isset($_POST['default'])) {
        $default_url = 'https://gist.githubusercontent.com/BFTrick/3706672/raw/be744502cf3921f761cbef11878af6f4a2024c3d/.htaccess';
        $default_content = file_get_contents($default_url);
        if ($default_content) {
            $result = file_put_contents($htaccess_path, $default_content);
            if ($result) {
                add_settings_error('htaccess-editor', 'htaccess-editor', 'The .htaccess file has been restored to default.', 'updated');
            } else {
                add_settings_error('htaccess-editor', 'htaccess-editor', 'There was an error restoring the .htaccess file to default.', 'error');
            }
        } else {
            add_settings_error('htaccess-editor', 'htaccess-editor', 'Error downloading default .htaccess file.', 'error');
        }
    }
    $content = file_get_contents($htaccess_path);
    if (!$content) {
        $content = 'Error reading .htaccess file.';
    }
    ?>
    <div class="wrap">
        <h1>.htaccess Editor</h1>
        <?php settings_errors(); ?>
        <form method="post" action="">
            <textarea name="content" rows="20" cols="80"><?php echo htmlspecialchars($content); ?></textarea>
            <br>
            <input type="submit" name="submit" value="Save Changes" class="button button-primary">
            <input type="submit" name="default" value="Restore Default" class="button">
        </form>
    </div>
    
    <?php
}

