# CustomLogger - Lightweight PHP Logger

CustomLogger is a simple, lightweight logging utility for PHP applications. It provides a flexible way to log messages with different severity levels and includes features like stack trace logging for debugging purposes.

## Features

- Singleton design pattern for global access
- Multiple logging levels (INFO, WARN, ERROR, FATAL, TRACE, DEBUG)
- Stack trace logging for TRACE, ERROR, and FATAL levels
- WordPress integration (optional)
- File-based logging

## Files

### CustomLogger.php

This file contains the main `CustomLogger` class and the `LoggingLevel` class.

```php
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
    // ... (rest of the class implementation)
}
```

### index.php

This file sets up the WordPress plugin and initializes the CustomLogger.

```php
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

// ... (rest of the plugin setup)
```

## Usage

1. Include the `CustomLogger.php` file in your project.
2. Initialize the logger with a file path:

```php
$logger = CustomLogger::getInstance('/path/to/your/logfile.log');
```

3. Log messages using the `log` method:

```php
$logger->log("This is an info message"); // Default level is INFO
$logger->log("This is a warning", LoggingLevel::WARN);
$logger->log("This is an error", LoggingLevel::ERROR);
```

## Log Levels

- `INFO`: General information
- `WARN`: Warnings that don't prevent the application from functioning
- `ERROR`: Errors that may impact functionality
- `FATAL`: Critical errors that prevent the application from functioning
- `TRACE`: Detailed information for debugging, including stack trace
- `DEBUG`: Debugging information

## WordPress Integration

When used as a WordPress plugin, the logger automatically detects the current user (if available) and includes the user ID in log entries.

## Error Handling

The plugin sets up custom error and exception handlers to log PHP errors and exceptions automatically.

## Installation (as WordPress Plugin)

1. Copy the `CustomLogger.php` and `index.php` files to a new directory in your WordPress plugins folder.
2. Activate the plugin through the WordPress admin interface.
3. The logger will create a log file at the path specified by `LOGGER_FILE_PATH` in `index.php`.

## License

This project is licensed under the GPLv2 or later.
