<?php

class LoggingLevel
{
    const INFO = 'INFO';
    const WARN = 'WARN';
    const ERROR = 'ERROR';
    const FATAL = 'FATAL';
    const TRACE = 'TRACE';
    const DEBUG = 'DEBUG';
}

class CustomLogger
{
    private $logFilePath;
    private static $instance = null;

    // Private constructor to prevent direct creation of object
    private function __construct($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    // Method to get the single instance of the class
    public static function getInstance($logFilePath)
    {
        if (self::$instance === null) {
            self::$instance = new CustomLogger($logFilePath);
        }
        return self::$instance;
    }

    public function log($message, $level = LoggingLevel::INFO)
    {
        // Check if the function wp_get_current_user exists
        if (function_exists('wp_get_current_user')) {
            $current_user = wp_get_current_user();
            $userInfo = isset($current_user->ID) && $current_user->ID ? $current_user->ID : 'None';
        } else {
            $userInfo = 'None';
        }

        // Create log prefix with timestamp, level, and user info
        $logPrefix = date("Y-m-d H:i:s") . " [{$level}] ({$userInfo}) ";
        $logMessage = $logPrefix . $this->formatMessage($message);

        switch ($level) {
            case LoggingLevel::TRACE:
                $logMessage .= $this->getStackTrace();
                break;
            case LoggingLevel::ERROR:
            case LoggingLevel::FATAL:
                $logMessage .= $this->getErrorTrace();
                break;
            // Add additional cases for other log levels if needed
            default:
                // No additional information needed for other levels
                break;
        }

        $logMessage .= "\n";

        // Write log message to file
        if ($f = @fopen($this->logFilePath, 'a')) {
            fwrite($f, $logMessage);
            fclose($f);
        } else {
            error_log("Failed to open log file: {$this->logFilePath}");
            // Echo error for debugging purposes
            echo "Failed to open log file: {$this->logFilePath}<br>";
        }
    }

    private function getStackTrace()
    {
        ob_start();
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $trace = ob_get_clean();
        return "\n" . $trace;
    }

    private function getErrorTrace()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $traceString = '';

        foreach ($trace as $index => $frame) {
            $traceString .= "#{$index} ";
            if (isset($frame['file'])) {
                $traceString .= $frame['file'] . '(' . $frame['line'] . '): ';
            }
            if (isset($frame['class'])) {
                $traceString .= $frame['class'] . '->';
            }
            $traceString .= $frame['function'] . "()\n";
        }

        return $traceString;
    }

    private function formatMessage($message)
    {
        if (is_array($message) || is_object($message)) {
            return print_r($message, true);
        }
        return $message;
    }
}