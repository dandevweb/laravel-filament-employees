<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Country;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $br = Country::whereCountryCode('BR')->withCount('employees')->first();
        $us = Country::whereCountryCode('US')->withCount('employees')->first();
        $uk = Country::whereCountryCode('UK')->withCount('employees')->first();

        return [
            Card::make('All Employees', Employee::all()->count()),
            Card::make('BR Employees', $br ? $br->employees_count : 0),
            Card::make('US Employees', $us ? $us->employees_count : 0),
            Card::make('UK Employees', $uk ? $uk->employees_count : 0),
        ];
    }
}
