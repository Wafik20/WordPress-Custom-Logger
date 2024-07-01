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

// Include the CustomLogger class
require_once(plugin_dir_path(__FILE__) . 'CustomLogger.php');

// Define the path for the log file
define('LOGGER_FILE_PATH', '/home/paleo/devfspitt1/wp-content/debug.log');

// Global variable to store the logger instance
global $custom_logger_instance;

/**
 * Install function to create the log file if it doesn't exist
 */
function install_custom_logger() {
    if (!file_exists(LOGGER_FILE_PATH)) {
        // Attempt to open the log file for writing
        if ($f = @fopen(LOGGER_FILE_PATH, 'a')) {
            // Write initial log entry with the creation timestamp
            fwrite($f, "Log file created on " . date("Y-m-d H:i:s") . "\n");
            fclose($f);
        } else {
            // Log to the default error log if the file creation fails
            error_log("Failed to create log file: " . LOGGER_FILE_PATH);
        }
    }
}

// Register the activation hook to call the install function
register_activation_hook(__FILE__, 'install_custom_logger');

// Initialize the logger instance globally
$custom_logger_instance = CustomLogger::getInstance(LOGGER_FILE_PATH);

// Start session for error tracking
session_start();

/**
 * Custom error handler function
 *
 * @param int $errno Error number
 * @param string $errstr Error message
 * @param string $errfile File where the error occurred
 * @param int $errline Line number where the error occurred
 * @return bool Always returns true to prevent default PHP error handler
 */
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($custom_logger_instance) {
    // Generate a unique key for each error to prevent repeated logging
    $errorKey = md5("error_" . $errno . "_" . $errstr);

    // Log the error if it hasn't been logged in this session
    if (!isset($_SESSION['logged_errors'][$errorKey])) {
        // Log the error using the custom logger
        $custom_logger_instance->log("Error: [$errno] $errstr - $errfile:$errline", LoggingLevel::ERROR);
        // Mark the error as logged in the session
        $_SESSION['logged_errors'][$errorKey] = true;
    }

    // Return true to prevent the default PHP error handler from being called
    return true;
});

/**
 * Custom exception handler function
 *
 * @param Exception $exception The exception that was thrown
 */
set_exception_handler(function ($exception) use ($custom_logger_instance) {
    // Log the exception message using the custom logger
    $custom_logger_instance->log("Exception: " . $exception->getMessage(), LoggingLevel::FATAL);
});
