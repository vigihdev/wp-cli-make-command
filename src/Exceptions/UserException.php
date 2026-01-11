<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

final class UserException extends WpCliMakeException
{
    public static function notAllowUserRole(string $role): static
    {
        return new self(
            message: sprintf('User role "%s" is not allow.', $role),
            code: 400,
            context: ['role' => $role],
            solutions: [
                'Check the user role in the code.',
                'Add the user role to the allow list.',
            ]
        );
    }

    public static function userNotFound(string $identifier): static
    {
        return new self(
            message: sprintf('User with identifier "%s" not found.', $identifier),
            code: 404,
            context: ['user_identifier' => $identifier],
            solutions: [
                'Verify the user ID, username, or email is correct.',
                'Check if the user exists in the WordPress installation.',
                'Ensure you have proper permissions to access this user.',
            ]
        );
    }

    public static function invalidUserData(array $data, string $reason): static
    {
        return new self(
            message: sprintf('Invalid user data provided: %s', $reason),
            code: 400,
            context: [
                'user_data' => $data,
                'reason' => $reason
            ],
            solutions: [
                'Verify all required user fields are properly filled.',
                'Check that the data format is valid for WordPress users.',
                'Ensure email addresses are properly formatted.',
            ]
        );
    }

    public static function duplicateUserEmail(string $email): static
    {
        return new self(
            message: sprintf('A user with email address "%s" already exists.', $email),
            code: 409,
            context: ['email' => $email],
            solutions: [
                'Use a different email address for the new user.',
                'Find and update the existing user instead.',
                'Check if the email was mistyped or already in use.',
            ]
        );
    }

    public static function duplicateUsername(string $username): static
    {
        return new self(
            message: sprintf('A user with username "%s" already exists.', $username),
            code: 409,
            context: ['username' => $username],
            solutions: [
                'Use a different username for the new user.',
                'Find and update the existing user instead.',
                'Check if the username was mistyped or already in use.',
            ]
        );
    }

    public static function duplicateEmail(string $email): static
    {
        return new self(
            message: sprintf('A user with email address "%s" already exists.', $email),
            code: 409,
            context: ['email' => $email],
            solutions: [
                'Use a different email address for the new user.',
                'Find and update the existing user instead.',
                'Check if the email was mistyped or already in use.',
            ]
        );
    }


    public static function unauthorizedUserOperation(string $operation): static
    {
        return new self(
            message: sprintf('Current user is not authorized to perform "%s" operation.', $operation),
            code: 403,
            context: ['operation' => $operation],
            solutions: [
                'Ensure the current user has the required capabilities for this operation.',
                'Log in as an administrator or user with appropriate permissions.',
                'Check that the required user roles are assigned.',
            ]
        );
    }

    public static function failedToCreateUser(string $reason): static
    {
        return new self(
            message: sprintf('Failed to create user: %s', $reason),
            code: 500,
            context: ['reason' => $reason],
            solutions: [
                'Check the WordPress error logs for more details.',
                'Verify that all required user data is provided correctly.',
                'Ensure the site allows user registrations if applicable.',
            ]
        );
    }

    public static function failedToUpdateUser(string $userId, string $reason): static
    {
        return new self(
            message: sprintf('Failed to update user %s: %s', $userId, $reason),
            code: 500,
            context: [
                'user_id' => $userId,
                'reason' => $reason
            ],
            solutions: [
                'Check the WordPress error logs for more details.',
                'Verify that the user exists and the data is properly formatted.',
                'Ensure the current user has permission to update this account.',
            ]
        );
    }

    public static function invalidPassword(string $reason): static
    {
        return new self(
            message: sprintf('Invalid password provided: %s', $reason),
            code: 400,
            context: ['reason' => $reason],
            solutions: [
                'Ensure the password meets the minimum security requirements.',
                'Check that the password policy is followed.',
                'Verify the password length and complexity rules.',
            ]
        );
    }

    public static function invalidEmail(string $email): static
    {
        return new self(
            message: sprintf('Invalid email address provided: %s', $email),
            code: 400,
            context: ['email' => $email],
            solutions: [
                'Verify that the email address is properly formatted.',
                'Check for any typos or errors in the email address.',
                'Ensure the email domain is valid and exists.',
            ]
        );
    }

    public static function invalidUsername(string $username): static
    {
        return new self(
            message: sprintf('Invalid username provided: %s', $username),
            code: 400,
            context: ['username' => $username],
            solutions: [
                'Ensure the username only contains letters, numbers, and underscores.',
                'Check that the username does not start with a number.',
                'Verify the username length is within the allowed range.',
            ]
        );
    }
}
