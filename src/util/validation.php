<?php

/**
 * Check if a value is not empty
 */
function isRequired($value)
{
    return !empty(trim($value));
}

/**
 * Check if a value meets the minimum length requirement
 */
function minLength($value, $min)
{
    return strlen(trim($value)) >= $min;
}

/**
 * Check if a value does not exceed the maximum length
 */
function maxLength($value, $max)
{
    return strlen(trim($value)) <= $max;
}

/**
 * Check if an email address is valid
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if two values match (for password confirmation)
 */
function matches($value1, $value2)
{
    return $value1 === $value2;
}

/**
 * Check if a value is a valid integer
 */
function isValidInt($value)
{
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

/**
 * Sanitize a value to prevent XSS attacks
 */
function sanitize($value)
{
    return htmlspecialchars(strip_tags(trim($value)));
}

/**
 * Check if a password meets minimum security requirements
 * At least 8 characters, one letter and one number
 */
function isValidPassword($password)
{
    return strlen($password) >= 8 &&
        preg_match('/[A-Za-z]/', $password) &&
        preg_match('/[0-9]/', $password);
}

/**
 * Collect and return all form errors as an array
 */
function validateRegistration($name, $email, $password, $confirm_password)
{
    $errors = [];

    if (!isRequired($name)) {
        $errors[] = 'Full name is required.';
    } elseif (!minLength($name, 2)) {
        $errors[] = 'Full name must be at least 2 characters.';
    } elseif (!maxLength($name, 100)) {
        $errors[] = 'Full name must not exceed 100 characters.';
    }

    if (!isRequired($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (!maxLength($email, 150)) {
        $errors[] = 'Email address must not exceed 150 characters.';
    }

    if (!isRequired($password)) {
        $errors[] = 'Password is required.';
    } elseif (!isValidPassword($password)) {
        $errors[] = 'Password must be at least 8 characters and contain at least one letter and one number.';
    }

    if (!isRequired($confirm_password)) {
        $errors[] = 'Please confirm your password.';
    } elseif (!matches($password, $confirm_password)) {
        $errors[] = 'Passwords do not match.';
    }

    return $errors;
}

/**
 * Validate complaint form inputs
 */
function validateComplaint($category_id, $title, $description)
{
    $errors = [];

    if (!isRequired($category_id) || !isValidInt($category_id)) {
        $errors[] = 'Please select a valid category.';
    }

    if (!isRequired($title)) {
        $errors[] = 'Complaint title is required.';
    } elseif (!minLength($title, 5)) {
        $errors[] = 'Title must be at least 5 characters.';
    } elseif (!maxLength($title, 200)) {
        $errors[] = 'Title must not exceed 200 characters.';
    }

    if (!isRequired($description)) {
        $errors[] = 'Complaint description is required.';
    } elseif (!minLength($description, 10)) {
        $errors[] = 'Description must be at least 10 characters.';
    }

    return $errors;
}

/**
 * Validate login form inputs
 */
function validateLogin($email, $password)
{
    $errors = [];

    if (!isRequired($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (!isRequired($password)) {
        $errors[] = 'Password is required.';
    }

    return $errors;
}

/**
 * Display errors as an alert
 */
function displayErrors($errors)
{
    if (!empty($errors)) {
        echo '<div class="alert alert-error"><ul style="margin: 0; padding-left: 20px;">';
        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul></div>';
    }
}
