<x-filament-panels::page>
    {{ $this->form }}

    @livewire(\App\Filament\Events\Widgets\EventScoreRulesTableWidget::class, ['filters' => $this->filters])
</x-filament-panels::page>