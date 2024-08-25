<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockAdjustmentResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\StockAdjustment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class StockAdjustmentResource extends Resource
{
    use \App\Traits\HasNavigationBadge;

    protected static ?string $model = StockAdjustment::class;

    protected static ?string $navigationGroup = 'Stock';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hiddenOn(RelationManagers\StockAdjustmentsRelationManager::class),
                Forms\Components\TextInput::make('quantity_adjusted')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->default('Restock.')
                    ->maxLength(65535)
                    ->placeholder('Write a reason for the stock adjustment')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') // SECARA UX BAGUS
            // ->defaultSort('product.name') // SECARA UI BAGUS
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->sortable()
                    ->hiddenOn(RelationManagers\StockAdjustmentsRelationManager::class),
                Tables\Columns\TextColumn::make('quantity_adjusted')
                    ->label('Adjusted')
                    ->numeric()
                    ->suffix(' Quantity')
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->limit(50)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->hiddenOn(RelationManagers\StockAdjustmentsRelationManager::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStockAdjustments::route('/'),
        ];
    }
}
