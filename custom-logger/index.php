<?php
/*
   Plugin Name: Jon Daley Logger
   Description: Allow Custom Logging to Handle Errors Gracefully 
   Version: 1.0
   Author: Wafik Tawfik
   Author URI: http://wafik.world
   License: GPLv2 or later
   License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once(plugin_dir_path(__FILE__) . 'CustomLogger.php');

define('LOGGER_FILE_PATH', '/home/paleo/devfspitt1/wp-content/debug.log');

// Global variable to store the logger instance
global $custom_logger_instance;

// Install function to create log file if it doesn't exist
function install_custom_logger() {
    if (!file_exists(LOGGER_FILE_PATH)) {
        if ($f = @fopen(LOGGER_FILE_PATH, 'a')) {
            fwrite($f, "Log file created on " . date("Y-m-d H:i:s") . "\n");
            fclose($f);
        } else {
            error_log("Failed to create log file: " . LOGGER_FILE_PATH);
        }
    }
}

// Register activation hook to call the install function
register_activation_hook(__FILE__, 'install_custom_logger');

// Initialize the logger instance globally
$custom_logger_instance = CustomLogger::getInstance(LOGGER_FILE_PATH);

// Set the custom error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($custom_logger_instance) {
    $custom_logger_instance->log("Error: [$errno] $errstr - $errfile:$errline", LoggingLevel::ERROR);
    return true; // Return true to prevent default PHP error handler
});

// Set the custom exception handler
set_exception_handler(function ($exception) use ($custom_logger_instance) {
    $custom_logger_instance->log("Exception: " . $exception->getMessage(), LoggingLevel::FATAL);
});
