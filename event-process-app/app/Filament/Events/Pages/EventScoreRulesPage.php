<?php
declare(strict_types=1);

namespace App\Filament\Events\Pages;

use App\Filament\Events\Widgets\EventScoreRulesTableWidget;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class EventScoreRulesPage extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $title = 'Score Rules';
    protected static ?string $slug = 'event-score-rules';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'Events.event-score-rules-page';
    protected static ?int $navigationSort = 2;

    public array $filters = [];

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-star';
    }

    public function mount(array $filters = []): void
    {
        $this->filters = $filters;
                

        $this->form->fill([
            'active' => true,
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->statePath('filters')
            ->schema([
                Group::make()
                    ->columns(3)
                    ->schema([
                        Checkbox::make('active')
                            ->label('Active'),

                        TextInput::make('points')
                            ->label('Points')
                            ->placeholder('Enter Points'),
                    ]),
            ]);
    }


    public function updatedFilters(): void
    {
        $this->dispatch('scoresFiltersUpdated', filters: $this->filters);
    }

    /**
     * When filters are applied, we dispatch an event with the new filter values so that the widgets can listen for it and update accordingly
    */ 
    public function applyFilters(): void
    {
        $this->filters = $this->form->getState();
        $this->dispatch('scoresFiltersUpdated', filters: $this->filters);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('apply')
                ->label('Apply')
                ->action('applyFilters'),
        ];
    }


}