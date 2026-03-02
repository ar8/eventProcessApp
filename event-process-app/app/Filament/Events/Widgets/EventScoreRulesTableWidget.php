<?php
declare(strict_types=1);

namespace App\Filament\Events\Widgets;

use App\Models\Event;
use App\Models\EventScoreRule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class EventScoreRulesTableWidget extends TableWidget
{
    public array $filters = [];

    #[On('scoresFiltersUpdated')]
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
        $this->resetTable();
    }

    /*
     * Get the filtered query for retrieving events based on the current filter state.
     * This method uses the getEvents method defined in the Event model to apply the necessary filters.
     *
     * @return Builder The Eloquent query builder instance with the applied filters.
     */
    protected function getFilteredQuery(): Builder
    {
        $eventModel = new EventScoreRule();
        $query = $eventModel->getEventScoreRules($this->filters);
    
        return $query;
    }

     protected function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('field')->label('Field')->sortable(),
            Tables\Columns\TextColumn::make('operator')->label('Operator')->sortable(),
            Tables\Columns\TextColumn::make('value')->label('Value')->sortable(),
            Tables\Columns\TextInputColumn::make('points')
            ->label('Points')
            ->type('number')
            ->rules(['nullable', 'integer', 'min:10'])
            ->extraInputAttributes(['min' => 10, 'step' => 1])
            ->afterStateUpdated(function (EventScoreRule $record, $state): void {
                $record->update(['points' => $state !== null ? (int) $state : null]);

                Notification::make()
                    ->title('Points updated')
                    ->success()
                    ->send();
            }),
            Tables\Columns\TextColumn::make('active')->label('Active')->sortable(),
        ];
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->heading('Scores List')
            ->query($this->getFilteredQuery())
            ->columns($this->getColumns());
            // ->actions([
            //     Action::make('edit')
            //         ->label('Edit')
            //         ->link()
            //         ->url(fn (EventScoreRule $record) => route('filament.resources.event-score-rules.edit', $record)),
            // ]);
    }

}