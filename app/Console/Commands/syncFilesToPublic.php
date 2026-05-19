<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class syncFilesToPublic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-files-to-public';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! app()->environment(['local', 'testing'])) {
            // Prevent the script from timing out (0 means infinite runtime)
            ini_set('max_execution_time', '0');

            // Temporarily increase the memory allocation limit for file streaming
            ini_set('memory_limit', '512M');
            $this->syncImagesToPublic();
        }
    }

    private function syncImagesToPublic()
    {
        try {
            Log::info('Syncing Images');

            $source = '/home/planoo/repositories/planoo/public/uploads/';
            $destination = '/home/planoo/public_html/uploads';

            $this->recursiveCopy($source, $destination);
            $this->deleteExtraFiles($source, $destination);

            Log::info('Images Synced');
        } catch (\Exception $e) {
            Log::info('images syncing error', ['error' => $e->getMessage()]);
        }
    }

    private function recursiveCopy(string $src, string $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcPath = $src . DIRECTORY_SEPARATOR . $file;
            $dstPath = $dst . DIRECTORY_SEPARATOR . $file;

            if (is_dir($srcPath)) {
                $this->recursiveCopy($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }

        closedir($dir);
    }

    private function deleteExtraFiles(string $src, string $dst)
    {
        $dir = opendir($dst);

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcPath = $src . DIRECTORY_SEPARATOR . $file;
            $dstPath = $dst . DIRECTORY_SEPARATOR . $file;

            if (! file_exists($srcPath)) {
                if (is_dir($dstPath)) {
                    $this->deleteDirectory($dstPath);
                } else {
                    unlink($dstPath);
                }
            } elseif (is_dir($dstPath)) {
                $this->deleteExtraFiles($srcPath, $dstPath);
            }
        }

        closedir($dir);
    }

    private function deleteDirectory(string $dir)
    {
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    // private function syncImagesToPublic()
    // {
    //     try {
    //         if (function_exists('shell_exec')) {
    //             Log::info('Syncing Images');
    //             $command = 'rsync -a --delete --inplace --quiet '
    //                 . '/home/planoo/repositories/planoo/public/uploads/ '
    //                 . '/home/planoo/public_html/uploads';
    //             $guarded = "nice -n 10 ionice -c2 -n7 $command";
    //             $result = \exec($guarded);
    //             Log::info('Images Synced', ['result' => $result]);
    //         } else {
    //             Log::info('Shell is not enabled');
    //         }
    //     } catch (Exception $e) {
    //         Log::info('images syncing error', ['error' => $e->getMessage()]);
    //     }
    // }
}
