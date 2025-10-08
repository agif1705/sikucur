<?php

namespace App\Filament\Resources\WhatsAppBroadcastResource\Pages;

use App\Filament\Resources\WhatsAppBroadcastResource;
use App\Models\WhatsAppBroadcast;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;

class ViewBroadcastLogs extends Page implements HasTable
{
 use InteractsWithTable;

 protected static string $resource = WhatsAppBroadcastResource::class;

 protected static string $view = 'filament.resources.whats-app-broadcast-resource.pages.view-broadcast-logs';

 public WhatsAppBroadcast $record;

 public function table(Table $table): Table
 {
  return $table
   ->query($this->record->logs()->getQuery())
   ->columns([
    Tables\Columns\TextColumn::make('recipient_name')
     ->label('Nama Penerima')
     ->getStateUsing(function ($record) {
      return $record->recipient_name;
     })
     ->searchable(['user.name', 'penduduk.name'])
     ->sortable(),

    Tables\Columns\TextColumn::make('recipient_type')
     ->label('Tipe')
     ->badge()
     ->color(fn(string $state): string => match ($state) {
      'user' => 'success',
      'penduduk' => 'primary',
      default => 'gray',
     })
     ->formatStateUsing(fn(string $state): string => match ($state) {
      'user' => 'Pegawai',
      'penduduk' => 'Warga',
      default => $state,
     }),

    Tables\Columns\TextColumn::make('nagari_name')
     ->label('Nagari')
     ->getStateUsing(function ($record) {
      if ($record->recipient_type === 'user' && $record->user) {
       return $record->user->nagari->name ?? '-';
      } elseif ($record->recipient_type === 'penduduk' && $record->penduduk) {
       return $record->penduduk->nagari->name ?? '-';
      }
      return '-';
     })
     ->searchable()
     ->sortable(),

    Tables\Columns\TextColumn::make('jabatan_info')
     ->label('Jabatan/Status')
     ->getStateUsing(function ($record) {
      if ($record->recipient_type === 'user' && $record->user) {
       return $record->user->jabatan->name ?? 'Tidak ada jabatan';
      } elseif ($record->recipient_type === 'penduduk') {
       return 'Warga';
      }
      return '-';
     }),

    Tables\Columns\TextColumn::make('phone')
     ->label('No. HP')
     ->searchable(),

    Tables\Columns\IconColumn::make('status')
     ->label('Status')
     ->boolean()
     ->trueIcon('heroicon-o-check-circle')
     ->falseIcon('heroicon-o-x-circle')
     ->trueColor('success')
     ->falseColor('danger'),

    Tables\Columns\TextColumn::make('error_message')
     ->label('Error')
     ->limit(50)
     ->tooltip(fn($record) => $record->error_message)
     ->color('danger')
     ->visible(fn($record) => !empty($record->error_message)),

    Tables\Columns\TextColumn::make('sent_at')
     ->label('Dikirim Pada')
     ->dateTime('d/m/Y H:i:s')
     ->sortable(),
   ])
   ->filters([
    Tables\Filters\SelectFilter::make('status')
     ->label('Status')
     ->options([
      true => 'Berhasil',
      false => 'Gagal',
     ]),

    Tables\Filters\SelectFilter::make('recipient_type')
     ->label('Tipe Penerima')
     ->options([
      'user' => 'Pegawai',
      'penduduk' => 'Warga',
     ]),
   ])
   ->defaultSort('created_at', 'desc')
   ->striped()
   ->paginated([10, 25, 50, 100]);
 }

 public function getTitle(): string
 {
  $targetType = match ($this->record->target_type) {
   'all' => 'Semua Pegawai',
   'nagari' => 'Berdasarkan Nagari',
   'jabatan' => 'Berdasarkan Jabatan',
   'penduduk' => 'Semua Warga/Penduduk',
   'custom' => 'Manual',
   default => $this->record->target_type
  };

  $attachment = $this->record->attachment_name ? ' (📎 ' . $this->record->attachment_name . ')' : '';

  return 'Log Broadcast: ' . $this->record->title . $attachment . ' - Target: ' . $targetType;
 }

 protected function getHeaderActions(): array
 {
  return [
   \Filament\Actions\Action::make('download_attachment')
    ->label('Download Lampiran')
    ->icon('heroicon-m-paper-clip')
    ->color('info')
    ->visible(fn() => !empty($this->record->attachment_path))
    ->url(fn() => asset('storage/' . $this->record->attachment_path))
    ->openUrlInNewTab(),

   \Filament\Actions\Action::make('back')
    ->label('Kembali')
    ->url($this->getResource()::getUrl('index'))
    ->icon('heroicon-m-arrow-left'),
  ];
 }
}
