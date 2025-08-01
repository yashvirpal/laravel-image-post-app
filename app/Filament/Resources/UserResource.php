<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {

        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            TextInput::make('phone')->required(),
            TextInput::make('website')->url(),
            Textarea::make('address')->required(),
            FileUpload::make('profile')
                ->disk('public')
                ->directory('user/profile')
                ->image()
                ->required()
                ->imagePreviewHeight('100') // Optional: nice preview size
                    ->rules([
                        'required',
                        'image',
                        'max:1024', // 2MB
                    ])
                    ->preserveFilenames() // optional, or remove for unique name
                    ->saveUploadedFileUsing(function ($file, $state) {
                        $manager = new ImageManager(new Driver());

                        $image = $manager->read($file->getRealPath());

                        $image->resize(500, 500);

                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                        Storage::disk('public')->put('users/' . $filename, (string) $image->encode());

                        return 'users/' . $filename;
                    }),



            Toggle::make('status')
                ->label('Active')
                ->default(true) // UI: toggle ON by default
                ->dehydrateStateUsing(fn($state) => $state ? 'active' : 'inactive') // Save as 'active'/'inactive'
                ->afterStateHydrated(
                    fn($component, $state) =>
                    $component->state($state === 'active' || $state === null) // Handle first load + existing data
                )
                ->dehydrated() // 🔑 Ensures it’s always saved
                ->required(),



            // TextInput::make('password')
            //     ->hidden()
            //     ->dehydrated(false), // <- you can skip saving from form

            // TextInput::make('role')
            //     ->hidden()
            //     ->dehydrated(false),



        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('website')->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->sortable()
                    ->label('Status'),

                ImageColumn::make('profile')
                    ->disk('public')
                    ->circular()
                    ->size(40)
                    ->label('Profile')

            ])
            ->filters([

                SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('setActive')
                        ->label('Mark as Active')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(
                            fn(Collection $records) =>
                            $records->each->update(['status' => 'active'])
                        )
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('setInactive')
                        ->label('Mark as Inactive')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(
                            fn(Collection $records) =>
                            $records->each->update(['status' => 'inactive'])
                        )
                        ->requiresConfirmation(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'user'); // ✅ Filter here
    }
}
