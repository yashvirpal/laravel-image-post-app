<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\User;
use App\Services\ImageService;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;



class GenerateImages extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static string $view = 'filament.pages.generate-images';
    protected static ?string $title = 'Generate Images';



    public ?int $event_id = null;
    public array $user_ids = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('event_id')
                ->label('Select Event')
                ->options(Event::pluck('name', 'id'))
                ->required(),

            CheckboxList::make('user_ids')
                ->label('Select Users')
                ->options(User::where('role', 'user')->pluck('name', 'id'))
                ->columns(2)
                ->required(),
        ];
    }

    public function generate(ImageService $service)
    {
        $data = validator([
            'event_id' => $this->event_id,
            'user_ids' => $this->user_ids,
        ], [
            'event_id' => ['required', 'exists:events,id'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ])->validate();
        $event = Event::findOrFail($this->event_id);
        $users = User::whereIn('id', $this->user_ids)->get();

        foreach ($users as $user) {
            $service->generate($user, $event);
        }




        Notification::make()
            ->title('Success!')
            ->body('Images generated successfully!')
            ->success()
            ->send();
    }

    // protected function getFormActions(): array
    // {
    //     return [
    //         Forms\Components\Actions\ButtonAction::make('Generate & Save')
    //             ->action('generate')
    //             ->color('success')
    //             ->icon('heroicon-o-bolt'),
    //     ];
    // }
}
