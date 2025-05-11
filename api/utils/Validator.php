<?php
declare(strict_types=1);

class Validator {
    private $errors = [];
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * Validates that a field is required
     *
     * @param string $field Field name
     * @param string $message Custom error message
     * @return self
     */
    public function required(string $field, string $message = ''): self {
        if (!isset($this->data[$field]) || trim((string) $this->data[$field]) === '') {
            $this->errors[$field][] = $message ?: "The field {$field} is required.";
        }
        return $this;
    }

    /**
     * Validates that a field is a valid email
     *
     * @param string $field Field name
     * @param string $message Custom error message
     * @return self
     */
    public function email(string $field, string $message = ''): self {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = $message ?: "The field {$field} must be a valid email.";
        }
        return $this;
    }

    /**
     * Validates that a field has a minimum length
     *
     * @param string $field Field name
     * @param int $min Minimum length
     * @param string $message Custom error message
     * @return self
     */
    public function min(string $field, int $min, string $message = ''): self {
        if (isset($this->data[$field]) && strlen((string) $this->data[$field]) < $min) {
            $this->errors[$field][] = $message ?: "The field {$field} must have at least {$min} characters.";
        }
        return $this;
    }

    /**
     * Validates that a field has a maximum length
     *
     * @param string $field Field name
     * @param int $max Maximum length
     * @param string $message Custom error message
     * @return self
     */
    public function max(string $field, int $max, string $message = ''): self {
        if (isset($this->data[$field]) && strlen((string) $this->data[$field]) > $max) {
            $this->errors[$field][] = $message ?: "The field {$field} must have at most {$max} characters.";
        }
        return $this;
    }

    /**
     * Validates that a field is numeric
     *
     * @param string $field Field name
     * @param string $message Custom error message
     * @return self
     */
    public function numeric(string $field, string $message = ''): self {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field][] = $message ?: "The field {$field} must be numeric.";
        }
        return $this;
    }

    /**
     * Validates that a field is a valid date
     *
     * @param string $field Field name
     * @param string $format Date format (default: Y-m-d)
     * @param string $message Custom error message
     * @return self
     */
    public function date(string $field, string $format = 'Y-m-d', string $message = ''): self {
        if (isset($this->data[$field])) {
            $date = \DateTime::createFromFormat($format, $this->data[$field]);
            if (!$date || $date->format($format) !== $this->data[$field]) {
                $this->errors[$field][] = $message ?: "The field {$field} must be a valid date with format {$format}.";
            }
        }
        return $this;
    }

    /**
     * Validates that a field is in a list of allowed values
     *
     * @param string $field Field name
     * @param array $values Allowed values
     * @param string $message Custom error message
     * @return self
     */
    public function in(string $field, array $values, string $message = ''): self {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $this->errors[$field][] = $message ?: "The field {$field} must be one of the following values: " . implode(', ', $values) . ".";
        }
        return $this;
    }

    /**
     * Checks if validation passed
     *
     * @return bool
     */
    public function passes(): bool {
        return empty($this->errors);
    }

    /**
     * Checks if validation failed
     *
     * @return bool
     */
    public function fails(): bool {
        return !$this->passes();
    }

    /**
     * Gets validation errors
     *
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Gets validated data
     *
     * @return array
     */
    public function getValidData(): array {
        return $this->data;
    }
}
