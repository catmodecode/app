<?php

namespace App\Console\Commands;

use App\Contracts\UserRepositoryContract;
use App\Exceptions\User\{
    NotPhoneException,
    EmailExistsException,
    PhoneExistsException,
};
use App\Models\User;
use Illuminate\Console\Command;
use Throwable;

class CreateUserCommand extends Command
{
    protected $name = 'Create user';

    protected $signature = 'user:create';

    protected $description = 'Create new user';

    public function __construct(protected UserRepositoryContract $userRepository)
    {
        parent::__construct();
    }

    public function handle()
    {
        $userRepository = $this->userRepository;
        do {
            $email = $this->ask('User email');
            if (!$userRepository->validateEmail($email)) {
                $this->error('Please enter correct email.');
                $email = null;
                continue;
            }
            if ($userRepository->emailExists($email)) {
                $this->error('Email already in use, enter another.');
                $email = null;
                continue;
            }
        } while (!isset($email));

        $userName = $this->ask('User name');
        do {
            $password = trim($this->ask('Password'));
            if (!$userRepository->validatePassword($password)) {
                $this->error('Passwords too weak, please try other');
                $password = null;
                continue;
            }
            if ($password !== trim($this->ask('Confirm password'))) {
                $this->error('Passwords doesnt match. Reenter.');
                $password = null;
                continue;
            }
        } while (!isset($password));

        do {
            $phone = $this->ask('User phone(use +xxxxxxxxxxx format)');
            if (!$userRepository->validatePhone($phone)) {
                $this->error('Please enter correct phone.');
                $phone = null;
                continue;
            }
            if ($userRepository->phoneExists($phone)) {
                $this->error('Phone already in use, enter another.');
                $phone = null;
                continue;
            }
        } while (!isset($phone));

        try {
            $user = $this->userRepository->create($userName, $email, $password, $phone);
        } catch (EmailExistsException) {
            $this->error('Email already exists, please run command again');
        } catch (PhoneExistsException) {
            $this->error('Phone already exists, please run command again');
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }

        $this->line(sprintf('User with id "%d" created', $user->id));

        return Command::SUCCESS;
    }
}
