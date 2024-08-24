<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')->schema([
                    Forms\Components\TextInput::make('order_number')
                        ->required()
                        ->default(generateSequentialNumber(Order::class))
                        ->readOnly(),
                    Forms\Components\TextInput::make('order_name')
                        ->maxLength(255)
                        ->placeholder('Tulis nama pesanan'),
                    Forms\Components\TextInput::make('total')
                        ->readOnlyOn('create')
                        ->default(0)
                        ->numeric(),
                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->label('Customer (optional)')
                        ->placeholder('Pilih Customer'),

                    Forms\Components\Group::make([
                        Forms\Components\Select::make('payment_method')
                            ->enum(\App\Enums\PaymentMethod::class)
                            ->options(\App\Enums\PaymentMethod::class)
                            ->default(\App\Enums\PaymentMethod::CASH)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->enum(\App\Enums\OrderStatus::class)
                            ->options(\App\Enums\OrderStatus::class)
                            ->default(\App\Enums\OrderStatus::PENDING),
                    ])->columnSpan(2)->columns(2),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(self::getTableColumns())
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make('total')
                            ->money('IDR'),
                    ),
                Tables\Columns\TextColumn::make('profit')
                    ->numeric()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make('profit')
                            ->money('IDR'),
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state->getColor()),

                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state->format('d M Y H:i')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state->format('d M Y H:i'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('edit-transaction')
                    ->label('Edit Transaction')
                    ->icon('heroicon-o-pencil')
                    ->url(fn($record) => "/orders/{$record->order_number}"),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('order_number')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('order_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('discount')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('total')
                ->numeric()
                ->alignEnd()
                ->sortable()
                ->summarize(
                    Tables\Columns\Summarizers\Sum::make('total')
                        ->money('IDR'),
                ),
            Tables\Columns\TextColumn::make('profit')
                ->numeric()
                ->alignEnd()
                ->summarize(
                    Tables\Columns\Summarizers\Sum::make('profit')
                        ->money('IDR'),
                )
                ->sortable(),
            Tables\Columns\TextColumn::make('payment_method')
                ->badge()
                ->color('gray'),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn($state) => $state->getColor()),

            Tables\Columns\TextColumn::make('user.name')
                ->numeric()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('customer.name')
                ->numeric()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->formatStateUsing(fn($state) => $state->format('d M Y H:i')),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->formatStateUsing(fn($state) => $state->format('d M Y H:i'))
                ->toggleable(isToggledHiddenByDefault: true),
        ];
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'create-transaction' => Pages\CreateTransaction::route('{record}'),
        ];
    }
}
