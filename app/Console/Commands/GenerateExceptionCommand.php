<?php

namespace App\Console\Commands;

use App\Exceptions\EmailExistsException;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

class GenerateExceptionCommand extends Command
{
    protected $name = 'Make exception';

    protected $signature = 'make:exception {ClassName}';

    protected $description = 'Make exception class';

    protected $template = <<<EOT
<?php

namespace App\Exceptions{NAMESPASE_RELATIVE};

use Exception;

class CLASS_NAME extends Exception
{
}

EOT;

    public function handle()
    {
        $ds = DIRECTORY_SEPARATOR;
        $className = $this->argument('ClassName');
        preg_match('/^(.+)[\/](.+)$/', $className, $matches);
        $file = $className;
        $path = '';
        if (count($matches) === 3) {
            $file = $matches[2];
            $path = $matches[1];
            mkdir(app()->path(). $ds .'Exceptions' . $ds . $path, recursive:true);
        }
        
        $fileName = app()->path(). $ds .'Exceptions' . $ds . ($path === '' ? '' : $path . $ds).$file.'.php';
        if (!file_exists($fileName)) {
            file_put_contents($fileName, str_replace(
                '{NAMESPASE_RELATIVE}',
                $path === '' ? '' : '\\' . str_replace('/', '\\', $path),
                str_replace('CLASS_NAME', $file, $this->template))
            );
        }
    }
}
