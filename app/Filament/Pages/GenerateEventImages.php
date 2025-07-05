<?php
namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\User;
use App\Services\UserEventImageService;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class GenerateEventImages extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static string $view = 'filament.pages.generate-event-images';
    protected static ?string $title = 'Generate Festival Images';
    

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

    public function generate(UserEventImageService $service)
    {
        $event = Event::findOrFail($this->event_id);
        $users = User::whereIn('id', $this->user_ids)->get();

        foreach ($users as $user) {
            $service->generate($user, $event);
        }

        $this->notify('success', 'Images generated successfully!');
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

