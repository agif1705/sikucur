<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class TranscodeVideo implements ShouldQueue
{
 use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

 public string $path;
 public string $disk;

 public function __construct(string $path, string $disk = 'public')
 {
  $this->path = $path;
  $this->disk = $disk;
 }

 public function handle(): void
 {
  $disk = Storage::disk($this->disk);
  $inputPath = $disk->path($this->path);

  $statusKey = 'transcode-status/' . str_replace(["/", "\\"], '__', $this->path) . '.json';
  // write processing status
  try {
   Storage::disk('local')->put($statusKey, json_encode(['status' => 'processing', 'started_at' => now()->toDateTimeString(), 'path' => $this->path]));
  } catch (\Throwable $e) {
   Log::warning('TranscodeVideo: failed to write initial status', ['error' => $e->getMessage()]);
  }

  if (! file_exists($inputPath)) {
   Log::error('TranscodeVideo: input file not found', ['path' => $inputPath]);
   return;
  }

  $tmpOutput = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'transcoded-' . uniqid() . '.mp4';

  $cmd = 'ffmpeg -y -i ' . escapeshellarg($inputPath)
   . ' -c:v libx264 -preset veryfast -crf 28 -maxrate 1M -bufsize 2M'
   . ' -vf "scale=min(1280\,iw):-2" -c:a aac -b:a 128k '
   . escapeshellarg($tmpOutput);

  try {
   $process = Process::fromShellCommandline($cmd);
   $process->setTimeout(3600);
   $process->run();

   if (! $process->isSuccessful()) {
    Log::error('TranscodeVideo: ffmpeg failed', ['cmd' => $cmd, 'output' => $process->getOutput(), 'error' => $process->getErrorOutput()]);
    if (file_exists($tmpOutput)) {
     @unlink($tmpOutput);
    }
    Storage::disk('local')->put($statusKey, json_encode(['status' => 'failed', 'error' => $process->getErrorOutput(), 'path' => $this->path]));
    return;
   }

   // Replace original file with transcoded file
   // Try rename first, fallback to copy
   if (@rename($tmpOutput, $inputPath) === false) {
    $disk->put($this->path, file_get_contents($tmpOutput));
    @unlink($tmpOutput);
   }

   // worker jalan sbg root -> samakan owner ke www-data biar web server bisa baca (hindari 403)
   @chown($inputPath, 'www-data');
   @chgrp($inputPath, 'www-data');
   @chmod($inputPath, 0644);

   Log::info('TranscodeVideo: success', ['path' => $this->path]);
   try {
    $size = filesize($inputPath) ?: null;
    Storage::disk('local')->put($statusKey, json_encode(['status' => 'done', 'finished_at' => now()->toDateTimeString(), 'path' => $this->path, 'size' => $size]));
   } catch (\Throwable $e) {
    Log::warning('TranscodeVideo: failed to write success status', ['error' => $e->getMessage()]);
   }
  } catch (\Throwable $e) {
   Log::error('TranscodeVideo: exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
   if (file_exists($tmpOutput)) {
    @unlink($tmpOutput);
   }
   try {
    Storage::disk('local')->put($statusKey, json_encode(['status' => 'failed', 'error' => $e->getMessage(), 'path' => $this->path]));
   } catch (\Throwable $e) {
    Log::warning('TranscodeVideo: failed to write exception status', ['error' => $e->getMessage()]);
   }
  }
 }
}
