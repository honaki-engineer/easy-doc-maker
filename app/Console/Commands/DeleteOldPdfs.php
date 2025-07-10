<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DeleteOldPdfs extends Command
{
    protected $signature = 'pdf:cleanup'; // コマンド名
    protected $description = '1時間以上前に作られたPDFを削除します';

    public function handle()
    {
        $dir = storage_path('app/public/tmp');
        $deleted = 0;

        foreach(File::files($dir) as $file) {
            // ファイルの作成から1時間以上経ってたら削除
            if(now()->diffInMinutes(Carbon::createFromTimestamp(filemtime($file))) > 60) {
                File::delete($file);
                $deleted++;
            }
        }

        $this->info("🗑️ {$deleted}個の古いPDFを削除しました！");
    }
}
