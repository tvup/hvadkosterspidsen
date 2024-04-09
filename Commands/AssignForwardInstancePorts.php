<?php

namespace App\Console\Commands;

use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AssignForwardInstancePorts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-forward-instance-ports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Determines available ports for sail instance and updates .env file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $ports = [
            'APP_PORT' => $this->getNextAvailablePort(8080),
            'VITE_PORT' => $this->getNextAvailablePort(5173),
            'FORWARD_DB_PORT' => $this->getNextAvailablePort(3306),
            'FORWARD_REDIS_PORT' => $this->getNextAvailablePort(6379),
            'FORWARD_MAILPIT_PORT' => $this->getNextAvailablePort(1025),
            'FORWARD_MAILPIT_DASHBOARD_PORT' => $this->getNextAvailablePort(8025),
            'FORWARD_MEILISEARCH_PORT' => $this->getNextAvailablePort(7700)
        ];

        $this->info(json_encode($ports, JSON_PRETTY_PRINT));

        $fieldMap = [
            'APP_URL' => 'APP_PORT-VITE_PORT',
            'DB_PASSWORD' => 'FORWARD_DB_PORT',
            'REDIS_PORT' => 'FORWARD_REDIS_PORT',
            'MAIL_FROM_NAME' => 'FORWARD_MAILPIT_PORT-FORWARD_MAILPIT_DASHBOARD_PORT',
            'MEILISEARCH_NO_ANALYTICS' => 'FORWARD_MEILISEARCH_PORT',
        ];



        // Read the contents of the .env file
        $envFile = base_path('.env');
        $contents = File::get($envFile);

        // Split the contents into an array of lines
        $lines = explode(PHP_EOL, $contents);

        // Loop through the lines and update the values
        foreach ($lines as &$line) {
            // Skip empty lines and comments
            if (empty($line) || Str::startsWith($line, '#'))
            {
                continue;
            }

            // Split each line into key and value
            $parts = explode('=', $line, 2);
            $key = $parts[0];

            // Check if the key exists in the provided data
            if (isset($fieldMap[$key])) {
                $array = explode('-', $fieldMap[$key]);
                foreach($array as $entry) {
                    $line = $line . PHP_EOL . $entry . '=' . $ports[$entry];
                }
            }
        }

        // Combine the lines back into a string
        $updatedContents = implode(PHP_EOL, $lines);

        // Write the updated contents back to the .env file
        File::put($envFile, $updatedContents);

        return 0;
    }

    public function getNextAvailablePort(array|bool|int|string $portNumber): int
    {
        while (true) {
            try {
                $sock = socket_create_listen($portNumber);
                break;
            } catch (ErrorException $e) {
                //NOP
            }
            $portNumber++;
        }

        socket_getsockname($sock, $addr, $portNumber);
        socket_close($sock);

        return $portNumber;
    }



}
