<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class JwtGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:secret {--show : Display the keys instead of modifying files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the JWT secret and refresh keys to the .env';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $keys = $this->getRandomKeys();

        if ($this->option('show')){
            $this->showKeys($keys);
            return;
        }

        $path = base_path('.env');
        if (file_exists($path)){
            $this->putKeys($path, $keys);
            exit;
        }

        $this->line("<comment>Can't find .env file</comment>");
        $this->showKeys($keys);
    }

    /**
     * Generate a random keys for JSON Web Token.
     *
     * @return array
     */
    private function getRandomKeys()
    {
        return ([
            'secret' => Str::random(60),
            'refresh' => Str::random(60)
        ]);
    }

    /**
     * Show JWT_SECRET_KEY and JWT_REFRESH_KEY keys.
     *
     * @param array $keys
     *
     * @return void
     */
    private function showKeys(array $keys)
    {
        $this->line('<comment>JWT_SECRET_KEY: '.$keys['secret'].'</comment>');
        $this->line('<comment>JWT_REFRESH_KEY: '.$keys['refresh'].'</comment>');
        return;
    }

    /**
     * Put JWT_SECRET_KEY and JWT_REFRESH_KEY keys in to .env file.
     *
     * @param String $path
     * @param array $keys
     *
     * @return void
     */
    private function putKeys(String $path, array $keys)
    {
        $secret = Str::contains(file_get_contents($path), 'JWT_SECRET_KEY');
        $refresh = Str::contains(file_get_contents($path), 'JWT_REFRESH_KEY');

        if (!$secret){
            file_put_contents(
                $path,
                PHP_EOL . 'JWT_SECRET_KEY=' . $keys['secret'] . PHP_EOL,
                FILE_APPEND
            );
            $this->line("<comment>JWT_SECRET_KEY was created.</comment>");
        }

        if (!$refresh){
            file_put_contents(
                $path,
                'JWT_REFRESH_KEY=' . $keys['refresh'] . PHP_EOL,
                FILE_APPEND
            );
            $this->line("<comment>JWT_REFRESH_KEY was created.</comment>");
        }

        if ($secret && $refresh){
            file_put_contents($path, str_replace(
                'JWT_SECRET_KEY=' . $this->laravel['config']['jwt.secret'],
                'JWT_SECRET_KEY=' . $keys['secret'], file_get_contents($path))
            );
            file_put_contents($path, str_replace(
                    'JWT_REFRESH_KEY=' . $this->laravel['config']['jwt.refresh'],
                    'JWT_REFRESH_KEY=' . $keys['refresh'], file_get_contents($path))
            );
            $this->line("<comment>JWT_SECRET_KEY and JWT_REFRESH_KEY was updated.</comment>");
        }
    }
}
