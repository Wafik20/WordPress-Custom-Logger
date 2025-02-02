<?php

/**
 * Class LoggingLevel
 *
 * Defines constants for different logging levels.
 */
class LoggingLevel
{
    const INFO = 'INFO';
    const WARN = 'WARN';
    const ERROR = 'ERROR';
    const FATAL = 'FATAL';
    const TRACE = 'TRACE';
    const DEBUG = 'DEBUG';
}

/**
 * Class CustomLogger
 *
 * Provides custom logging functionality with different logging levels.
 */
class CustomLogger
{
    private $logFilePath;
    private static $instance = null;

    /**
     * Private constructor to prevent direct creation of object
     *
     * @param string $logFilePath The file path where logs will be written.
     */
    private function __construct($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    /**
     * Method to get the single instance of the class
     *
     * @param string $logFilePath The file path where logs will be written.
     * @return CustomLogger The single instance of CustomLogger.
     */
    public static function getInstance($logFilePath)
    {
        if (self::$instance === null) {
            self::$instance = new CustomLogger($logFilePath);
        }
        return self::$instance;
    }

    /**
     * Logs a message with a specific logging level
     *
     * @param string $message The message to log.
     * @param string $level The logging level of the message. Default is INFO.
     */
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
        $logPrefix = date("Y-m-d H:i:s") . " [{$level}] ({$userInfo})";
        $logMessage = $logPrefix . $this->formatMessage($message);

        switch ($level) {
            case LoggingLevel::TRACE:
                $logMessage .= "\n";
                $logMessage .= $this->getStackTrace();
                break;
            case LoggingLevel::ERROR:
            case LoggingLevel::FATAL:
                $logMessage .= "\n";
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

    /**
     * Retrieves the stack trace
     *
     * @return string The stack trace as a string.
     */
    private function getStackTrace()
    {
        ob_start();
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $trace = ob_get_clean();
        return "\n" . $trace;
    }

    /**
     * Retrieves the error trace
     *
     * @return string The error trace as a string.
     */
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

    /**
     * Formats the message for logging
     *
     * @param mixed $message The message to format.
     * @return string The formatted message.
     */
    private function formatMessage($message)
    {
        if (is_array($message) || is_object($message)) {
            return print_r($message, true);
        }
        return $message;
    }
}
