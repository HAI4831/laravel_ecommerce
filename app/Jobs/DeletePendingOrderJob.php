<?php

namespace App\Jobs;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable; // Thêm dòng này
use Illuminate\Contracts\Queue\ShouldQueue; // Thêm dòng này
use Illuminate\Foundation\Bus\Dispatchable; // Thêm dòng này
use Illuminate\Queue\InteractsWithQueue; // Thêm dòng này
use Illuminate\Queue\SerializesModels; // Thêm dòng này

class DeletePendingOrderJob implements ShouldQueue // Implement ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; // Sử dụng các trait cần thiết

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        \Log::info('Running DeletePendingOrderJob for order ID: ' . $this->orderId);
        $order = Order::find($this->orderId);

        // Kiểm tra trong hàm handle của DeletePendingOrderJob
        if ($order && $order->status === 'pending') {
            \Log::info('Order created at: ' . $order->created_at); // Ghi log thời gian tạo
            \Log::info('Current time: ' . Carbon::now()); // Ghi log thời gian hiện tại

            if ($order->created_at < Carbon::now()->subMinutes(2)) {
                // Xoá đơn hàng
                $order->delete();
                \Log::info('Deleted order ID: ' . $this->orderId); // Ghi log khi xóa
            } else {
                \Log::info('Order ID: ' . $this->orderId . ' is not old enough to delete.');
            }
        }

    }
}
