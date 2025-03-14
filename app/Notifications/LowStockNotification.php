<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $product;


    public function __construct(Product $product)
    {
        $this->product = $product;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Alerte: Stock bas pour ' . $this->product->name)
            ->line('Le produit ' . $this->product->name . ' a un stock bas.')
            ->line('Stock actuel: ' . $this->product->stock)
            ->action('Voir le produit', url('/admin/products/' . $this->product->id))
            ->line('Merci de réapprovisionner ce produit dès que possible.');
    }


    public function toArray($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'stock' => $this->product->stock,
        ];
    }
}
