---
name: laravel-broadcasting-reverb
description: "Configures Laravel Broadcasting with Reverb and Broadcast Notifications. Activates when defining broadcast events, setting up real-time notifications, configuring channels in `routes/channels.php`, installing Reverb, writing Echo client code, creating broadcastable events or notifications, or when mentioning broadcast, websocket, realtime, echo, or push."
license: MIT
metadata:
  author: laravel
---

# Laravel Broadcasting with Reverb & Broadcast Notifications

## When to Apply

Activate this skill when:

- Enabling event broadcasting in a Laravel app
- Setting up Laravel Reverb as the broadcast driver
- Creating broadcastable events
- Defining channels and authorizing private/presence channels
- Sending broadcast notifications
- Integrating with Laravel Echo on the frontend

## Documentation

Use `search-docs` to open the **Broadcasting** and **Notifications** sections of the Laravel 12.x docs.

## Broadcasting Basics

### Quickstart

Enable broadcasting in your application using the Artisan command:

```bash
php artisan install:broadcasting

This creates config/broadcasting.php and routes/channels.php where you can define authorization callbacks. Supported broadcast drivers include Laravel Reverb, Pusher Channels, Ably, log, and null.  ￼

⸻

Laravel Reverb Setup

Server Installation

To install Reverb as your broadcasting driver:

php artisan install:broadcasting --reverb

Or manually:

composer require laravel/reverb
php artisan reverb:install


⸻

Environment Variables

Add Reverb connection settings to your .env (matching your server/websocket host and ports).

⸻

Running the Server

Start the Reverb server:

php artisan reverb:start

You can pass host, port, or debug flags if needed.  ￼

⸻

Defining Channels

Register channel authorization logic in routes/channels.php:

Broadcast::channel('orders.{orderId}', function ($user, $orderId) {
    return $user->id === Order::findOrNew($orderId)->user_id;
});

Private channels require authenticated and authorized users.  ￼

⸻

Broadcast Events

To broadcast an event, implement the ShouldBroadcast interface:

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderShipped implements ShouldBroadcast
{
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('orders.' . $this->order->id);
    }
}


⸻

Laravel Echo (Frontend)

Install Echo and Pusher client libraries:

npm install laravel-echo pusher-js

Configure Echo to use the reverb broadcaster and your host/port:

window.Echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT,
  enabledTransports: ['ws', 'wss'],
});


⸻

Broadcast Notifications

Broadcast notifications allow real-time delivery via WebSockets using the broadcast channel.  ￼

Notification Class Setup

Generate a notification:

php artisan make:notification OrderShipped

Channels

Inside your notification:

public function via(object $notifiable): array
{
    return ['broadcast', 'database'];
}


⸻

Broadcast Payload

Define a toBroadcast method:

use Illuminate\Notifications\Messages\BroadcastMessage;

public function toBroadcast(object $notifiable): BroadcastMessage
{
    return new BroadcastMessage([
        'order_id' => $this->order->id,
        'status'   => $this->order->status,
    ]);
}

—

You can customize the queue connection and queue name using ->onConnection() and ->onQueue().  ￼

⸻

Custom Notification Type

Override the broadcast type if desired:

public function broadcastType(): string
{
    return 'order.shipped';
}


⸻

Listening for Notifications

On the frontend with Echo:

Echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        console.log(notification.type, notification);
    });

Notifications are broadcast on private channels using the {notifiable}.{id} naming convention.  ￼

⸻

Authorization & Testing
	•	Ensure your broadcasting/auth route is protected for private channels.
	•	Run queue workers since broadcasts are queued by default.
	•	Test notifications using Laravel’s native testing helpers (Notification::fake()).

⸻

Common Pitfalls
	•	Forgetting to run a queue worker
	•	Misconfigured channel authorization
	•	Missing environment variables for Reverb

⸻

Cheat Sheet

Feature	Notes
Install broadcasting	php artisan install:broadcasting --reverb
Reverb server	php artisan reverb:start
Channels	Defined in routes/channels.php
Broadcast event	Implement ShouldBroadcast
Broadcast notification	Use broadcast in via()
Frontend listener	Echo.private().notification()