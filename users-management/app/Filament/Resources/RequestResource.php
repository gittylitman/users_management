<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Request;
use App\Filament\Resources\RequestResource\Pages;
use App\Filament\Resources\RequestResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Filters\Filter;
use Closure;


class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('requests');
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('identity')
                    ->label(__('identity'))
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => __('The :attribute already exist'),
                    ])
                    ->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                $id = str_pad($value, 9, '0', STR_PAD_LEFT);
                                if (!preg_match('/^[0-9]{9}$/', $id)) 
                                    return false;
                                $sum = 0;
                                for ($i = 0; $i < 9; $i++) {
                                    $digit = (int)$id[$i];
                                    $sum += ($i % 2 === 0) ? $digit : array_sum(str_split($digit * 2));
                                }
                                if ($sum % 10 > 0)
                                {
                                    $fail(__('The :attribute is invalid.'));
                                }
                            };
                        }
                    ])
                    ->required()
                    ->maxLength(9)
                    ->minLength(7),
                TextInput::make('first_name')
                    ->label(__('first name'))
                    ->regex("/^[A-Za-z][A-Za-z ,.'-]+$/")
                    ->validationMessages([
                        'regex' => __('The :attribute must be English letters.'),
                    ])
                    ->required()
                    ->minLength(2)
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label(__('last name'))
                    ->regex("/^[A-Za-z][A-Za-z ,.'-]+$/")
                    ->validationMessages([
                        'regex' => __('The :attribute must be English letters.'),
                    ])
                    ->required()
                    ->minLength(2)
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(__('phone'))
                    ->tel()
                    ->maxLength(10)
                    ->required(),
                TextInput::make('email')
                    ->label(__('email'))
                    ->email()
                    ->maxLength(255),
                TextInput::make('unit')
                    ->label(__('unit'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('sub')
                    ->label(__('project'))
                    ->required()
                    ->maxLength(255),
                Select::make('authentication_type')
                    ->label(__('authentication type'))
                    ->options([
                        'Microsoft auth' => __('Microsoft auth'),
                        'phone call' => __('phone call')
                    ])
                    ->required(),
                Select::make('service_type')
                    ->label(__('service type'))
                    ->options([
                        'regular' => __('regular'),
                        'reserve' => __('reserve'),
                        'consultant' => __('consultant'),
                        'extenal' => __('extenal'),
                    ])
                    ->required(),
                TextInput::make('validity')
                    ->label(__('validity required'))
                    ->required()
                    ->numeric()
                    ->default(365)
                    ->maxLength(5),  
                Textarea::make('description')
                    ->label(__('description'))
                    ->required()
                    ->maxLength(1000)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $viewType = request()->input('viewType', 'Table');

        if ($viewType === 'Card') {
            $table = $table->contentGrid(['md' => 2, 'xl' => 3])->columns(
                array_merge(self::commonColumns(), [Split::make([])])
            );
        }
        else{
            $table = $table->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes())
            ->striped()
            ->columns(self::commonColumns());
        }
        return $table   
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('request status'))
                    ->multiple()
                    ->options([
                        'new' => __('new'),
                        'approve' => __('approve'),
                        'denied' => __('denied'),
                    ]),
                Filter::make('created_at')
                    ->label(__('created at'))
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('created from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('created until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                    ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\Action::make(__('approval'))
                            ->action(fn (Request $record) => self::approval($record))
                            ->icon('fluentui-approvals-app-16-o')
                            ->visible(UserResource::getUserFromAzure()->role==="Admin")
                            ->color('success'),
                        Tables\Actions\Action::make(__('deny'))
                            ->action(fn (Request $record) => self::deny($record))
                            ->icon('heroicon-o-x-circle')
                            ->visible(UserResource::getUserFromAzure()->role==="Admin")
                            ->color('danger'),
                        Tables\Actions\DeleteAction::make(),
                    ])->tooltip('Actions')
            ])
            ->query(function (Request $query) {
                if(UserResource::getUserFromAzure()->role === "User"){
                    return $query
                        ->where('submit_username', UserResource::getUserFromAzure()->name);
                }
                return $query;
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function commonColumns(): array
    {
        return [
            TextColumn::make('index')
                ->label('')
                ->rowIndex(),
            TextColumn::make('created_at')
                ->label(__('created at'))
                ->dateTime('d-m-Y')
                ->sortable(),
            TextColumn::make('submit_username')
                ->label(__('created by'))
                ->searchable()
                ->sortable(),
            TextColumn::make('identity')
                ->label(__('identity'))
                ->searchable(),
            TextColumn::make('fullname')
                ->label(__('full name'))
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query
                        ->orderBy('last_name', $direction)
                        ->orderBy('first_name', $direction);
                }),
            TextColumn::make('phone')
                ->label(__('phone'))
                ->searchable(),
            TextColumn::make('email')
                ->label(__('email'))                    
                ->copyable()
                ->copyMessage(__('Email address copied'))
                ->copyMessageDuration(1500)
                ->placeholder(__('No email')),
            TextColumn::make('status')
                ->label(__('request status'))
                ->formatStateUsing(fn (string $state): string => __($state))
                ->sortable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'new' => 'primary',
                    'approve' => 'success',
                    'denied' => 'danger',
                })
                ->icon(fn (string $state): string => match ($state) {
                    'new' => 'fluentui-status-16',
                    'approve' => 'fluentui-approvals-app-16-o',
                    'denied' => 'heroicon-o-x-circle',
                }),
        ];
    }

    public static function approval($record)
    {
        $record->status = 'approve';
        $record->save();
    }

    public static function deny($record)
    {
        $record->status = 'denied';
        $record->save();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
        ];
    }
}
