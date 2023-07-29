<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\State;
use App\Models\Country;
use App\Models\Employee;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Filament\Resources\EmployeeResource\Widgets\EmployeeStatsOverview;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('country_id')
                            ->label('Country')
                            ->options(Country::all()->pluck('name', 'id')->toArray())
                            ->required()
                            ->reactive(),

                        Select::make('state_id')
                            ->label('State')
                            ->required()
                            ->options(function (callable $get) {
                                $country = Country::find($get('country_id'));
                                return $country ? $country->states->pluck('name', 'id')->toArray() : [];
                            })
                            ->reactive()
                            ->disabled(function (callable $get) {
                                return !$get('country_id');
                            }),

                        Select::make('city_id')
                            ->label('City')
                            ->required()
                            ->options(function (callable $get) {
                                $state = State::find($get('state_id'));
                                return $state ? $state->cities->pluck('name', 'id')->toArray() : [];
                            })
                            ->reactive()
                            ->disabled(function (callable $get) {
                                return !$get('state_id');
                            }),

                        Select::make('department_id')->relationship('department', 'name')->required(),
                        TextInput::make('first_name')->required()->maxLength(255),
                        TextInput::make('last_name')->required()->maxLength(255),
                        TextInput::make('address')->required()->maxLength(255),
                        TextInput::make('zip_code')->required()->maxLength(255)
                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('00000-000')),
                        DatePicker::make('birth_date')->required(),
                        DatePicker::make('date_hired')->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('first_name')->searchable()->sortable(),
                TextColumn::make('last_name')->searchable()->sortable(),
                TextColumn::make('department.name')->sortable(),
                TextColumn::make('created_at')->sortable(),
            ])
            ->filters([
                SelectFilter::make('department_id')->relationship('department', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            EmployeeStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
