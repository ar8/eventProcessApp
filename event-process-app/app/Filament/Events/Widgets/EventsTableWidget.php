<?php
declare(strict_types=1);

namespace App\Filament\Events\Widgets;

use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class EventsTableWidget extends TableWidget
{
    public array $filters = [];

    #[On('filtersUpdated')]
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
        $eventModel = new Event();
        $query = $eventModel->getEvents($this->filters);
    
        return $query;
    }

    protected function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
            Tables\Columns\TextColumn::make('external_id')->label('External ID')->sortable(),
            Tables\Columns\TextColumn::make('type')->label('Type')->sortable(),
            Tables\Columns\TextColumn::make('source')->label('Source')->sortable(),
            Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
            Tables\Columns\TextColumn::make('score')->label('Score')->sortable(),
            // Tables\Columns\TextColumn::make('raw_payload')
            //     ->label('Payload')
            //     ->formatStateUsing(fn ($state) => is_scalar($state) ? (string) $state : json_encode($state)),
            Tables\Columns\TextColumn::make('normalized_payload')
                ->label('Normalized Payload')
                ->formatStateUsing(fn ($state) => is_scalar($state) ? (string) $state : json_encode($state)),
            // Tables\Columns\TextColumn::make('occurred_at')->label('Occurred At')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
            // Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime()->sortable(),
        ];
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->heading('Rules List')
            ->query($this->getFilteredQuery())
            ->columns($this->getColumns())
            ->actions([
                Action::make('processScore')
                    ->label('Score Event')
                    ->icon('heroicon-o-star')
                    ->requiresConfirmation()
                    ->action(function (Event $record): void {
                        $record->processScore();

                        Notification::make()
                            ->title('Event scored successfully')
                            ->success()
                            ->send();

                        $this->resetTable();
                    }),
            ]);
    }

}