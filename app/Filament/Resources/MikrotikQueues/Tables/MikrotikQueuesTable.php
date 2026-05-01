<?php

namespace App\Filament\Resources\MikrotikQueues\Tables;

use App\Facades\Mikrotik;
use App\Models\MikrotikQueue;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class MikrotikQueuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextInputColumn::make('name')
                    ->label('Nama Queue')
                    ->searchable()
                    ->width('280px')
                    ->extraAttributes(['style' => 'min-width: 280px;'])
                    ->extraInputAttributes(['style' => 'min-width: 260px;'])
                    ->sortable()
                    ->afterStateUpdated(function (MikrotikQueue $record, ?string $state): void {
                        self::syncNameToMikrotik($record, $state);
                    }),
                Tables\Columns\TextColumn::make('target')
                    ->label('Target')
                    ->searchable()
                    ->wrap()
                    ->limit(40),
                Tables\Columns\TextColumn::make('max_limit')
                    ->label('Max Limit')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate')
                    ->label('Rate')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('limit_at')
                    ->label('Limit At')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('parent')
                    ->label('Parent')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('queue_type')
                    ->label('Queue Type')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('bytes')
                    ->label('Bytes')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_bytes')
                    ->label('Total Bytes')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comment')
                    ->placeholder('-')
                    ->searchable()
                    ->wrap()
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('dynamic')
                    ->boolean()
                    ->label('Dynamic')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('disabled')
                    ->boolean()
                    ->label('Disabled')
                    ->sortable(),
                Tables\Columns\IconColumn::make('invalid')
                    ->boolean()
                    ->label('Invalid')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ret_id')
                    ->label('MikroTik ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    private static function syncNameToMikrotik(MikrotikQueue $record, ?string $name): void
    {
        $previousName = $record->getOriginal('name');
        $name = trim((string) $name);

        if (blank($name)) {
            self::revertNameState($record, $previousName);

            Notification::make()
                ->title('Gagal Mengubah Nama Queue')
                ->body('Nama queue tidak boleh kosong.')
                ->danger()
                ->send();

            return;
        }

        if (! $record->mikrotikConfig || ! $record->ret_id) {
            self::revertNameState($record, $previousName);

            Notification::make()
                ->title('Gagal Mengubah Nama Queue')
                ->body('Konfigurasi MikroTik atau ID queue tidak ditemukan.')
                ->danger()
                ->send();

            return;
        }

        try {
            $response = Mikrotik::updateSimpleQueue($record->mikrotikConfig, $record->ret_id, [
                'name' => $name,
            ]);

            $record->forceFill([
                'name' => $response['name'] ?? $name,
            ])->saveQuietly();

            Notification::make()
                ->title('Nama Queue Diperbarui')
                ->body('Nama queue berhasil dikirim ke MikroTik.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            self::revertNameState($record, $previousName);

            Log::error('Failed to update simple queue name from table input', [
                'queue_id' => $record->id,
                'ret_id' => $record->ret_id,
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Gagal Mengubah Nama Queue')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private static function revertNameState(MikrotikQueue $record, ?string $previousName): void
    {
        $record->forceFill([
            'name' => $previousName,
        ])->saveQuietly();
    }
}
