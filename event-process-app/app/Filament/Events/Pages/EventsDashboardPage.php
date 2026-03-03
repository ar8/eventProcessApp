<?php
declare(strict_types=1);

namespace App\Filament\Events\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class EventsDashboardPage extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $title = 'Events Dashboard';
    protected static ?string $slug = 'events-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'Events.event-dashboard-page';
    protected static ?int $navigationSort = 1;

    public array $filters = [];
    private array $types = [
        'form_provider' => 'Form Provider',
        'payment_gateway' => 'Payment Gateway',
        'status_tracker' => 'Status Tracker',
    ];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->subDays(7)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'type' => null,
        ]);
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-table-cells';
    }

    /* *
     * Define the form schema for the filters, including event type and date range.
     * The form state is stored in the $filters property and is used to filter the events displayed in the widgets.
     *
     * @param Form $form The form instance to define the schema on.
     * @return Form The form instance with the defined schema.
     */
    public function form(Form $form): Form
    {
        return $form
            ->statePath('filters')
            ->schema([
                Group::make()
                    ->columns(3)
                    ->schema([
                        Select::make('type')
                            ->label('Event Type')
                            ->options($this->types)
                            ->placeholder('Select Event Type')
                            ->searchable(),

                        DatePicker::make('date_from')
                            ->native(false)
                            ->format('Y-m-d')
                            ->displayFormat('m-d-Y')
                            ->label('From'),

                        DatePicker::make('date_to')
                            ->native(false)
                            ->format('Y-m-d')
                            ->displayFormat('m-d-Y')
                            ->label('To'),
                    ]),
            ]);
    }

    /**
     * Define the header actions for the page, including a refresh button and an apply filters button.
     * The apply filters button triggers the applyFilters method which dispatches an event with the new filter values.
     *
     * @return array An array of Action objects representing the header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->url(url()->current()),
            
            Action::make('applyFilters')
                ->label('Apply')
                ->color('primary')
                ->icon('heroicon-o-funnel')
                ->action(fn () => $this->applyFilters()),
        ];
    }

    /**
     * When filters are applied, we dispatch an event with the new filter values so that the widgets can listen for it and update accordingly
     *  */ 
    public function applyFilters(): void
    {
        $this->filters = $this->form->getState();
        $this->dispatch('filtersUpdated', filters: $this->filters);

    }

    public function updatedFilters(): void
    {
        $this->dispatch('filtersUpdated', filters: $this->filters);
    }
}