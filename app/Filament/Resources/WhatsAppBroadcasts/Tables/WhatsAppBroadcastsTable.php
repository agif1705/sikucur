<?php

namespace App\Filament\Resources\WhatsAppBroadcasts\Tables;

use App\Models\WhatsAppBroadcast;
use App\Services\WhatsAppBroadcastService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class WhatsAppBroadcastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Pengirim')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('has_attachment')
                    ->label('Lampiran')
                    ->boolean()
                    ->state(fn (WhatsAppBroadcast $record): bool => ! empty($record->attachment_path))
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('target_type')
                    ->label('Target')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all' => 'success',
                        'nagari' => 'info',
                        'jabatan' => 'warning',
                        'penduduk' => 'primary',
                        'custom' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all' => 'Semua',
                        'nagari' => 'Nagari',
                        'jabatan' => 'Jabatan',
                        'penduduk' => 'Penduduk',
                        'custom' => 'Manual',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('total_recipients')
                    ->label('Total Penerima')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_sent')
                    ->label('Terkirim')
                    ->numeric()
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_failed')
                    ->label('Gagal')
                    ->numeric()
                    ->sortable()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('success_rate')
                    ->label('Tingkat Keberhasilan')
                    ->state(function (WhatsAppBroadcast $record): string {
                        return $record->total_recipients > 0
                            ? round(($record->total_sent / $record->total_recipients) * 100, 1).'%'
                            : '0%';
                    })
                    ->badge()
                    ->color(function (WhatsAppBroadcast $record): string {
                        $rate = $record->total_recipients > 0
                            ? ($record->total_sent / $record->total_recipients) * 100
                            : 0;

                        return match (true) {
                            $rate >= 90 => 'success',
                            $rate >= 70 => 'warning',
                            $rate >= 50 => 'info',
                            default => 'danger',
                        };
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'sending' => 'Mengirim',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Dikirim Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sending' => 'Mengirim',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                    ]),

                Tables\Filters\SelectFilter::make('target_type')
                    ->label('Target')
                    ->options([
                        'all' => 'Semua',
                        'nagari' => 'Nagari',
                        'jabatan' => 'Jabatan',
                        'penduduk' => 'Penduduk',
                        'custom' => 'Manual',
                    ]),
            ])
            ->actions([
                Action::make('send')
                    ->label('Kirim')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('success')
                    ->visible(fn (WhatsAppBroadcast $record): bool => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Broadcast')
                    ->modalDescription('Apakah Anda yakin ingin mengirim broadcast ini? Setelah dikirim, broadcast tidak bisa dibatalkan.')
                    ->action(function (WhatsAppBroadcast $record) {
                        $broadcastService = app(WhatsAppBroadcastService::class);
                        $success = $broadcastService->sendBroadcast($record);

                        if ($success) {
                            Notification::make()
                                ->title('Broadcast berhasil dikirim!')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Broadcast gagal dikirim!')
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('view_logs')
                    ->label('Lihat Log')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn (WhatsAppBroadcast $record): string => static::getUrl('logs', ['record' => $record])),

                EditAction::make()
                    ->visible(fn (WhatsAppBroadcast $record): bool => $record->status === 'draft'),

                DeleteAction::make()
                    ->visible(fn (WhatsAppBroadcast $record): bool => $record->status === 'draft'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => true),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
