<?php

namespace App\Console\Commands;

use App\Exceptions\EmailExistsException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Throwable;

class CreateUserCommand extends Command
{
  protected $name = 'Create user';

  protected $signature = 'user:create';

  protected $description = 'Create new user';

  private UserRepository $userService;

  public function __construct(UserRepository $userService)
  {
    parent::__construct();
    $this->userService = $userService;
  }

  public function handle()
  {
    $userService = $this->userService;
    do {
      $email = $this->ask('User email');
      if (!$userService->validateEmail($email)) {
        $this->error('Please enter correct email.');
        $email = null;
        continue;
      }
      if ($userService->emailExists($email)) {
        $this->error('Email already in use, enter another.');
        $email = null;
        continue;
      }
    } while (!isset($email));

    $userName = $this->ask('User name');
    do {
      $password = trim($this->ask('Password'));
      if (!$userService->validatePassword($password)) {
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

    try {
      $user = $this->userService->create($userName, $email, $password);
    } catch (EmailExistsException $e) {
      $this->error('Email already exists, please run command again');
    } catch (Throwable $e) {
      $this->error($e->getMessage());
    }

    $this->line(sprintf('User with id "%d" created', $user->id));

    return Command::SUCCESS;
  }
}