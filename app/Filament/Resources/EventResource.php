<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                DatePicker::make('date')->required(),
                FileUpload::make('image')
                    ->disk('public')
                    ->directory('events')
                    ->image()
                    ->required()
                    ->imagePreviewHeight('100') // Optional: nice preview size
                    ->rules([
                        'required',
                        'image',
                        'max:1024', // 2MB
                    ])
                    ->preserveFilenames() // optional, or remove for unique name
                   // ->storeFileNamesIn('image') // or just handle normally
                    ->saveUploadedFileUsing(function ($file, $state) {
                        $manager = new ImageManager(new Driver());

                        $image = $manager->read($file->getRealPath());

                        $image->resize(1080, 1080);

                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                        Storage::disk('public')->put('events/' . $filename, (string) $image->encode());

                        return 'events/' . $filename;
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    // ->circular()
                    ->size(70)
                    ->label('Image'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('date')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
