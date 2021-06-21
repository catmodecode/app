<?php

namespace App\Console\Commands;

use App\Exceptions\EmailExistsException;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Console\Command;
use Throwable;

class GenerateKeysCommand extends Command
{
  protected $name = 'Generate app rsa keys';

  protected $signature = 'keys:generate';

  protected $description = 'Generate rs256 keys for jwt auth';

  public function handle()
  {

    $rsaKey = openssl_pkey_new(array(
      "digest_alg" => OPENSSL_ALGO_SHA256,
      'private_key_bits' => 4096,
      'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ));
    $privateKey = openssl_pkey_get_private($rsaKey);
    openssl_pkey_export($privateKey, $pem);
    $publicKeyPem = openssl_pkey_get_details($privateKey)['key'];
    file_put_contents(storage_path() . '/tokens/jwtRS256.key', $pem);
    file_put_contents(storage_path() . '/tokens/jwtRS256.key.pub', $publicKeyPem);
    $this->line('Keys successfully generated');
    return Command::SUCCESS;
  }
}
