<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;

class CreateTransaction extends Page implements HasForms
{
    protected static string $resource = OrderResource::class;

    protected static string $view = 'filament.resources.order-resource.pages.create-transaction';
    public Order $record;

    public function getTitle(): string
    {
        return "Order: {$this->record->order_number}";
    }
}
