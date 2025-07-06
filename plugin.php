<?php
/*
Plugin Name: Plugin Status API
Plugin URI: https://github.com/graham55/yourls-plugin-status-api
Description: Adds API action to list enabled plugins and their status with filtering and health checks
Version: 1.1.0
Author: Graham McKoen (GPM)
Author URI: https://github.com/graham55
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Register the API action using the correct filter method
yourls_add_filter('api_action_plugin_status', 'gpm_pluginstatus_api_action');

function gpm_pluginstatus_api_action() {
    // Get all plugins from the plugins directory
    $all_plugins = yourls_get_plugins();
    $active_plugins = yourls_get_option('active_plugins', array());
    
    // Get filter parameter if provided
    $filter = isset($_REQUEST['filter']) ? strtolower($_REQUEST['filter']) : 'all';
    $include_health = isset($_REQUEST['health']) && $_REQUEST['health'] === 'true';
    
    $result = array();
    $plugin_health = array();
    
    foreach ($all_plugins as $plugin_file => $plugin_data) {
        $is_active = in_array($plugin_file, $active_plugins);
        
        $plugin_info = array(
            'plugin_file' => $plugin_file,
            'name' => isset($plugin_data['Plugin Name']) ? $plugin_data['Plugin Name'] : 'Unknown',
            'description' => isset($plugin_data['Description']) ? $plugin_data['Description'] : '',
            'version' => isset($plugin_data['Version']) ? $plugin_data['Version'] : '',
            'author' => isset($plugin_data['Author']) ? $plugin_data['Author'] : '',
            'plugin_uri' => isset($plugin_data['Plugin URI']) ? $plugin_data['Plugin URI'] : '',
            'author_uri' => isset($plugin_data['Author URI']) ? $plugin_data['Author URI'] : '',
            'active' => $is_active
        );
        
        // Add health check if requested
        if ($include_health) {
            $plugin_path = YOURLS_PLUGINDIR . '/' . $plugin_file;
            $plugin_info['health'] = array(
                'file_exists' => file_exists($plugin_path),
                'readable' => is_readable($plugin_path),
                'has_header' => !empty($plugin_data['Plugin Name']),
                'file_size' => file_exists($plugin_path) ? filesize($plugin_path) : 0
            );
        }
        
        $result[] = $plugin_info;
    }
    
    // Apply filtering
    if ($filter === 'active') {
        $result = array_filter($result, function($plugin) { return $plugin['active']; });
        $result = array_values($result); // Re-index array
    } elseif ($filter === 'inactive') {
        $result = array_filter($result, function($plugin) { return !$plugin['active']; });
        $result = array_values($result); // Re-index array
    }
    
    // Count plugins
    $active_count = count($active_plugins);
    $total_count = count(yourls_get_plugins());
    $filtered_count = count($result);
    
    // Prepare response data
    $response_data = array(
        'total_plugins' => $total_count,
        'active_plugins' => $active_count,
        'inactive_plugins' => $total_count - $active_count,
        'filtered_count' => $filtered_count,
        'filter_applied' => $filter,
        'plugins' => $result
    );
    
    // Add summary statistics
    if ($include_health) {
        $health_summary = gpm_pluginstatus_calculate_health_summary($result);
        $response_data['health_summary'] = $health_summary;
    }
    
    // Generate simple message
    $simple_message = $filtered_count . ' plugins';
    if ($filter !== 'all') {
        $simple_message .= ' (' . $filter . ')';
    }
    $simple_message .= ' found, ' . $active_count . ' total active';
    
    // Return data in YOURLS API format
    return array(
        'statusCode' => 200,
        'simple' => $simple_message,
        'message' => 'success',
        'plugin_status' => $response_data,
    );
}

// Helper function to calculate health summary
function gpm_pluginstatus_calculate_health_summary($plugins) {
    $healthy = 0;
    $total_size = 0;
    $issues = array();
    
    foreach ($plugins as $plugin) {
        if (isset($plugin['health'])) {
            $health = $plugin['health'];
            if ($health['file_exists'] && $health['readable'] && $health['has_header']) {
                $healthy++;
            } else {
                $plugin_issues = array();
                if (!$health['file_exists']) $plugin_issues[] = 'file_missing';
                if (!$health['readable']) $plugin_issues[] = 'not_readable';
                if (!$health['has_header']) $plugin_issues[] = 'invalid_header';
                
                $issues[] = array(
                    'plugin' => $plugin['name'],
                    'issues' => $plugin_issues
                );
            }
            $total_size += $health['file_size'];
        }
    }
    
    return array(
        'healthy_plugins' => $healthy,
        'total_checked' => count($plugins),
        'health_percentage' => count($plugins) > 0 ? round(($healthy / count($plugins)) * 100, 2) : 0,
        'total_size_bytes' => $total_size,
        'issues' => $issues
    );
}

// Add admin notice about the API endpoint
yourls_add_action('plugins_loaded', 'gpm_pluginstatus_admin_init');

function gpm_pluginstatus_admin_init() {
    if( yourls_is_admin() ) {
        yourls_add_action('admin_page_before_content', 'gpm_pluginstatus_admin_notice');
    }
}

function gpm_pluginstatus_admin_notice() {
    if( isset($_GET['page']) && $_GET['page'] == 'plugins' ) {
        echo '<div class="notice notice-info" style="margin: 10px 0; padding: 10px; background: #e7f3ff; border-left: 4px solid #2196F3;">';
        echo '<p><strong>Plugin Status API:</strong> Query plugin status via API using <code>action=plugin_status</code></p>';
        echo '<p><small>Supports parameters: <code>filter=active|inactive|all</code>, <code>health=true</code></small></p>';
        echo '</div>';
    }
}
?>
