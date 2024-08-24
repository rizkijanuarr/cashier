<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
//
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Enums\OrderStatus;
//
use Filament\Forms\Components\Select;

class CreateTransaction extends Page implements HasForms
{
    protected static string $resource = OrderResource::class;

    protected static string $view = 'filament.resources.order-resource.pages.create-transaction';

    public Order $record;
    public mixed $selectedProduct;
    public int $quantityValue = 1;
    public int $discount = 0;


    public function getTitle(): string
    {
        return "Order: {$this->record->order_number}";
    }

    // Form Schema
    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedProduct')
                ->label('Select Product')
                ->searchable()
                ->preload()
                ->options(Product::pluck('name', 'id')->toArray())
                ->live()
                ->afterStateUpdated(function ($state) {
                    $product = Product::find($state);
                    $this->record->orderDetails()->updateOrCreate(
                        [
                            'order_id' => $this->record->id,
                            'product_id' => $state,
                        ],
                        [
                            'product_id' => $state,
                            'quantity' => $this->quantityValue,
                            'price' => $product->price,
                            'subtotal' => $product->price * $this->quantityValue,
                        ]
                    );
                }),
        ];
    }

    // Update Quantity
    public function updateQuantity(OrderDetail $orderDetail, $quantity): void
    {
        if ($quantity > 0) {
            $orderDetail->update([
                'quantity' => $quantity,
                'subtotal' => $orderDetail->price * $quantity,
            ]);
        }
    }

    // Remove Product
    public function removeProduct(OrderDetail $orderDetail): void
    {
        $orderDetail->delete();

        $this->dispatch('productRemoved');
    }

    // Update Order
    public function updateOrder(): void
    {
        $subtotal = $this->record->orderDetails->sum('subtotal');

        $this->record->update([
            'discount' => $this->discount,
            'total' => $subtotal - $this->discount,
        ]);
    }

    // Finalize Order
    public function finalizeOrder(): void
    {
        $this->updateOrder();
        $this->record->update(['status' => OrderStatus::COMPLETED]);
        $this->redirect('/orders');
    }

    // Save as Draft
    public function saveAsDraft(): void
    {
        $this->updateOrder();
        $this->redirect('/orders');
    }
}
