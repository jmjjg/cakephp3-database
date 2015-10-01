# Database

## Description

CakePHP 3 plugin that provides classes for adding default validation rules from the database table schema and cleaning data being saved.

Caching is enabled by default but can be disabled in the configuration or at run-time.

Tested with CakePHP 3.1.0-RC1 only.

## Main classes

AutovalidateBehavior reads various informations from the database table schema, such as column type, the fact that the column can be NULL, foreign key constraints, unique constraints and automatically adds those validation rules to the default ones.

FormattableBehavior cleans up data before validation or saving using configurable static class methods. The columns on which the formatters are applied can be defined based on column type and field name regular expression.

## Setup

Assuming the plugin is installed under plugins/Database, add the following to config/bootstrap.php:

    Plugin::load('Database', ['autoload' => true, 'bootstrap' => true]);

## Usage

The following code should be added to your table classes, inside the initialize() method.

The two behaviors are independant and can be loaded in any order.

Note that NULL and boolean TRUE and are equivalent as configuration values.

    public function initialize(array $config)
    {
        // ...
            $this->addBehavior('DatabaseAutovalidate',
                [
                    'className' => 'Database.Autovalidate',
                    // Default values
                    // 1°) Accepted validator names, as a string or an array of strings, NULL for any
                    'accepted' => null,
                    // 2°) Cache validation rules and their error messages ?
                    'cache' => null,
                    // 3°) Domain to use for error messages
                    'domain' => 'database'
                ]
            );

            $this->addBehavior('DatabaseFormattable',
                [
                    'className' => 'Database.Formattable',
                    // Default values
                    // 1°) Cache formatters and the field list they apply to ?
                    'cache' => null,
                    // 2°) List of formatter functions or static methods as keys, fields they apply to as values
                        // a°) A NOT key is allowed to negate the condition
                        // b°) Boolean true means all fields, false means the formatter is not used
                        // c°) Strings or array of strings are allowed
                            // * regular expressions (delimited by "/") are used to filter field names
                            // * other strings are used to filter field types
                    'formatters' => [
                        // Extract the part after the last "_" character
                        '\\Database\\Utility\\Formatter::suffix' => '/_id$/',
                        // Trim the value
                        '\\Database\\Utility\\Formatter::trim' => [ 'NOT' => 'binary'],
                        // Transform empty string to a NULL value
                        '\\Database\\Utility\\Formatter::null' => true,
                        // Tries to parse an integer value using the current intl.default_locale value
                        '\\Database\\Utility\\Formatter::integer' => ['integer', 'biginteger'],
                        // Tries to parse a decimal value using the current intl.default_locale value
                        '\\Database\\Utility\\Formatter::decimal' => ['decimal', 'float', 'numeric']
                    ]
                ]
            );
        // ...
    }