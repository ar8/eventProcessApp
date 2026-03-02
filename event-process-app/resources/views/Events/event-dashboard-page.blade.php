<x-filament-panels::page>
    {{ $this->form }}

    @livewire(\App\Filament\Events\Widgets\EventsTableWidget::class, ['filters' => $this->filters], key('events-table-widget'))
</x-filament-panels::page>
