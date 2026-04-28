<?php

namespace App\Filament\Resources\VideoTvs;

use App\Filament\Resources\VideoTvs\Schemas\VideoTvForm;
use App\Filament\Resources\VideoTvs\Tables\VideoTvsTable;
use App\Models\VideoTv;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VideoTvResource extends Resource
{
    protected static ?string $model = VideoTv::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Tv Informasi';

    protected static ?string $navigationLabel = 'Video TV';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return VideoTvForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return VideoTvsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideoTvs::route('/'),
            'create' => Pages\CreateVideoTv::route('/create'),
            'edit' => Pages\EditVideoTv::route('/{record}/edit'),
        ];
    }
}
