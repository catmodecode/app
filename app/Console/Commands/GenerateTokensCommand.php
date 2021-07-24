<?php

namespace App\Console\Commands;

use App\Exceptions\EmailExistsException;
use App\Models\User;
use Illuminate\Console\Command;
use Throwable;

class GenerateTokensCommand extends Command
{
  protected $name = 'Generate app rsa keys';

  protected $signature = 'tokens:generate';

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
    $path = storage_path() . '/tokens';
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }
    file_put_contents($path . '/jwtRS256.key', $pem);
    file_put_contents($path . '/jwtRS256.key.pub', $publicKeyPem);
    $this->line('Keys successfully generated');
    return Command::SUCCESS;
  }
}
