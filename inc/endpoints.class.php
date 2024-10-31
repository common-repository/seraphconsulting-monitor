<?php

class seraphMonitorEndpoints
{

    public function __construct()
    {

        add_filter( 'plugin_action_links_seraphconsulting-monitor/seraph-monitor.php', 'seraph_monitor_settings_link' );
        function seraph_monitor_settings_link( $links ) {
            $url = esc_url( add_query_arg(
                'page',
                'seraph-monitor',
                get_admin_url() . 'admin.php'
            ) );
            // Create the link.
            $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
            array_push(
                $links,
                $settings_link
            );
            return $links;
        }

        function register_plugin_settings() {
            register_setting( 'seraph_monitor_options_group', 'seraph_monitor_api_key' );
            register_setting( 'seraph_monitor_options_group', 'seraph_monitor_backups_dir' );
        }

        function seraph_monitor_register_options_page() {
            add_options_page('SeraphConsulting Monitor options', 'SC Monitor', 'manage_options', 'seraph-monitor', 'seraph_monitor_options_page');
            add_action( 'admin_init', 'register_plugin_settings' );

        }
        add_action('admin_menu', 'seraph_monitor_register_options_page');

        function seraph_monitor_options_page()
        {
            $key = md5(microtime().rand());

            ?>
            <div>
                <?php screen_icon(); ?>
                <h2>SeraphConsulting Monitor options</h2>
                <form method="post" action="options.php">
                    <?php settings_fields( 'seraph_monitor_options_group' ); ?>
                    <?php do_settings_sections( 'seraph_monitor_options_group' ); ?>

                    <table>
                        <tr valign="top">
                            <th scope="row"><label for="seraph_monitor_api_key">API Key</label></th>
                            <td><input type="text" id="seraph_monitor_api_key" name="seraph_monitor_api_key" value="<?php echo get_option('seraph_monitor_api_key'); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <td colspan="2">
                                <small>Please enter API key to secure access to your website data. Or copy/paste this key: <?php echo $key ?></small>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="seraph_monitor_backups_dir">Backups Dir</label></th>
                            <td><input type="text" id="seraph_monitor_backups_dir" name="seraph_monitor_backups_dir" value="<?php echo get_option('seraph_monitor_backups_dir'); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <td colspan="2">
                                <small>Please enter where your backups directory located.</small>
                            </td>
                        </tr>

                    </table>
                    <?php  submit_button(); ?>
                </form>
            </div>
            <?php
        }

        add_action('rest_api_init', function () {

            register_rest_route('seraph-monitor/v1', '/info/', [
                'methods' => 'GET',
                'callback' => [$this, 'getResponse'],
            ]);

            register_rest_route('seraph-monitor/v1', '/info/(?P<apiKey>\d+)', [
                'methods' => 'GET',
                'callback' => [$this, 'getResponse'],
                'args' => [
                    'apiKey'
                ]
            ]);
        });

    }

    public function getResponse($request)
    {
        $urlApiKey = $request['apiKey']; // value or NULL
        $settingsApiKey = get_option('seraph_monitor_api_key');
        if($settingsApiKey !== '' && !is_null($settingsApiKey)){
            if(is_null($urlApiKey) || $settingsApiKey !== $urlApiKey) return 'Wrong API key';
        }
        return ['themes' => $this->getThemes(), 'plugins' => $this->getPlugins(), 'wpinfo' => $this->getWpInfo()];
    }

    public function getThemes()
    {
        if (!function_exists('wp_get_themes')) {
            require_once ABSPATH . 'wp-admin/includes/theme.php';
        }

        $current_theme = wp_get_theme();
        $currentThemeName = $current_theme->get('Name');

        $themes = [];

        $all_themes = wp_get_themes();

        foreach ($all_themes as $theme) {

            $themes[$theme->stylesheet] = [
                'Name' => $theme->get('Name'),
                'ThemeURI' => $theme->get('ThemeURI'),
                'Description' => $theme->get('Description'),
                'Author' => $theme->get('Author'),
                'AuthorURI' => $theme->get('AuthorURI'),
                'Version' => $theme->get('Version'),
                'Template' => $theme->get('Template'),
                'Status' => $theme->get('Status'),
                'Tags' => $theme->get('Tags'),
                'TextDomain' => $theme->get('TextDomain'),
                'DomainPath' => $theme->get('DomainPath'),
                'Active' => $currentThemeName === $theme->get('Name')
            ];
        }

        return $themes;
    }

    public function getPlugins()
    {

        if (!function_exists('get_plugin_updates')) {
            require_once ABSPATH . 'wp-admin/includes/update.php';
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $list_updates = get_plugin_updates();

        $all_plugins = get_plugins();

        $active_plugins = get_option('active_plugins');

        foreach ($all_plugins as $pluginName => $pluginInfo) {
            $all_plugins[$pluginName]['Active'] = in_array($pluginName, $active_plugins);
            $all_plugins[$pluginName]['Key'] = $pluginName;
            unset($all_plugins[$pluginName]['Description']);
            unset($all_plugins[$pluginName]['AuthorURI']);
            unset($all_plugins[$pluginName]['AuthorName']);
            unset($all_plugins[$pluginName]['Author']);

            if (array_key_exists($pluginName, $list_updates)) {
                $all_plugins[$pluginName]['Update'] = $list_updates[$pluginName]->update;
            }

        }
        return $all_plugins;

    }

    public function getWpInfo()
    {
        global $wp_version;

        if (!function_exists('request_filesystem_credentials')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';

        }

        if (!function_exists('get_core_updates')) {
            require_once ABSPATH . 'wp-admin/includes/update.php';
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $update = get_preferred_from_update_core();

        $storedBackupsDir = get_option('seraph_monitor_backups_dir');

        $path    = '/var/www/backups/'.basename(ABSPATH);

        if($storedBackupsDir){
            $path = $storedBackupsDir;
        }
        $files = scandir($path);

        $backups = [];
        if($files){
            $backups = array_diff(scandir($path), ['.', '..']);
        }

        return [
            'current' => $wp_version,
            'latest' => $update->current,
            'update' => $update,
            'mysql' => $update->mysql_version,
            'php' => $update->php_version,
            'need_update' => $update->current !== $wp_version,
            'backups_list' => $backups,
            'free_space' => disk_free_space(__DIR__) / 1024 / 1024 / 1024 // GB
        ];

    }

}
