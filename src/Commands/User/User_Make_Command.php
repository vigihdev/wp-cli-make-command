<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\User;

use Throwable;
use Vigihdev\WpCliModels\Entities\UserEntity;
use WP_CLI\Utils;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\Validators\UserValidator;

final class User_Make_Command extends Base_User_Command
{
    private ?string $username = null;
    private ?string $email = null;
    private ?string $password = null;
    private ?string $role = null;

    public function __construct()
    {
        parent::__construct(name: 'make:user');
    }

    /**
     * Create a new user
     *
     * ## OPTIONS
     *
     * <user-login>
     * : The login of the user to create.
     *
     * <user-email>
     * : The email address of the user to create.
     *
     * [--role=<role>]
     * : The role of the user to create. Default: default role. Possible values
     * include 'administrator', 'editor', 'author', 'contributor', 'subscriber'.
     * ---
     *
     * [--user_pass=<password>]
     * : The user password. Default: randomly generated.
     * ---
     *
     * [--user_registered=<yyyy-mm-dd-hh-ii-ss>]
     * : The date the user registered. Default: current date.
     * ---
     *
     * [--display_name=<name>]
     * : The display name.
     *
     * [--user_nicename=<nice_name>]
     * : A string that contains a URL-friendly name for the user. The default is the user's username.
     * ---
     *
     * [--user_url=<url>]
     * : A string containing the user's URL for the user's web site.
     * ---
     *
     * [--nickname=<nickname>]
     * : The user's nickname, defaults to the user's username.
     * ---
     *
     * [--first_name=<first_name>]
     * : The user's first name.
     *
     * [--last_name=<last_name>]
     * : The user's last name.
     *
     * [--description=<description>]
     * : A string containing content about the user.
     * ---
     *
     * [--dry-run]
     * : Run the command in dry-run mode
     * default: false
     * ---
     *
     * ## EXAMPLES
     *
     *     # Create a new user
     *     wp make:user demo admin@example.com --password=123456 --role=administrator
     *
     *     # Create a new user in dry-run mode
     *     wp make:user demo admin@example.com --password=123456 --role=administrator --dry-run
     *
     * @param array $args
     * @param array $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        $this->username = $args[0] ?? null;
        $this->email = $args[1] ?? null;
        $this->password = Utils\get_flag_value($assoc_args, 'password', 'auto');
        $this->role = Utils\get_flag_value($assoc_args, 'role', 'editor');
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        $io = new CliStyle();

        // Validate user input
        try {
            UserValidator::validateCreate(['username' => $this->username, 'email' => $this->email])
                ->mustHaveUniqueUsername()
                ->mustHaveUniqueEmail();

            if ($dryRun) {
                $this->processDryRun($io, $assoc_args);
                return;
            }
            // If validation passes, execute the command
            $this->process($io, $assoc_args);
        } catch (Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }

    private function processDryRun(CliStyle $io, array $assoc_args)
    {
        $dryRun = $io->renderDryRunPreset("New User");

        $assoc_args = array_filter($assoc_args, function ($key) {
            return !in_array($key, ['dry-run'], true);
        }, ARRAY_FILTER_USE_KEY);

        $userdata = array_merge([
            'username' => $this->username,
            'email' => $this->email,
            'user_pass' => '********',
        ], $assoc_args);

        $dryRun
            ->addDefinition($userdata)
            ->addInfo("1 User akan dibuat")
            ->render();
    }

    private function process(CliStyle $io, array $assoc_args)
    {
        $userdata = [
            'user_login' => $this->username,
            'user_email' => $this->email,
            'user_pass' => $this->password,
            'role' => $this->role,
        ];

        $userdata = array_merge($userdata, $assoc_args);
        $insert = UserEntity::insert($userdata);

        if (is_wp_error($insert)) {
            $io->renderBlock("Error insert user: " . $insert->get_error_message())->error();
            return;
        }
        $io->renderBlock("User created successfully with ID: $insert")->success();
    }
}
