<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\WhatsAppCommand;

class MakeWhatsAppCommand extends Command
{
    protected $signature = 'make:wa-command {name} {--description=} {--nagari_id=1} {--footer=1}';
    protected $description = 'Create WhatsApp Command Handler and insert to DB';

    public function handle()
    {
        $name = Str::kebab($this->argument('name')); // misal izinPegawai
        $className = Str::studly($name) . 'Handler'; // IzinPegawaiHandler
        $filePath = app_path("Handlers/{$className}.php");

        if (File::exists($filePath)) {
            $this->error("Handler {$className} sudah ada!");
            return;
        }

        // 1️⃣ Buat file handler
        $stub = <<<PHP
<?php

namespace App\Handlers;

use App\Contracts\WhatsAppCommandHandler;

class {$className} implements WhatsAppCommandHandler
{
    public function handle(\$user, \$chat, \$data)
    {
        // TODO: implement logic for {$name} command
        return [
            'success' => true,
            'message' => 'Handler {$name} executed!',
            'data' => null
        ];
    }
}
PHP;

        File::put($filePath, $stub);
        $this->info("Handler {$className} berhasil dibuat di app/Handlers/");

        // 2️⃣ Masukkan ke database
        $commandText = Str::snake($name); // misal: izin_pegawai
        WhatsAppCommand::updateOrCreate(
            [
                'command' => $commandText,
                'nagari_id' => $this->option('nagari_id')
            ],
            [
                'handler_class' => "App\\Handlers\\{$className}",
                'description' => $this->option('description') ?? "Handler untuk command {$commandText}",
                'footer_whats_app_id' => $this->option('footer') ?? 1, // ✅ kasih default supaya tidak null
                'is_active' => true, // ✅ kasih default supaya tidak null
            ]
        );

        $this->info("Command '{$commandText}' berhasil disimpan ke database!");
    }
}
