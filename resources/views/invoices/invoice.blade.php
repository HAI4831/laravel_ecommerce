<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .table { width: 100%; border-collapse: collapse; }
        .table, .table th, .table td { border: 1px solid black; }
        .table th, .table td { padding: 8px; text-align: left; }
    </style>
    <script>
        window.onload = function() {
            window.print();
            // Chờ một chút để đảm bảo PDF đã tải
            setTimeout(function() {
                window.location.href = "{{ route('carts.index') }}"; // Redirect sau khi tải
            }, 3000); // Chờ 3 giây (hoặc thời gian bạn muốn)
        };
    </script>
</head>
<body>
    <div class="invoice-box">
        <h2>Invoice</h2>
        <p><strong>Invoice ID:</strong> {{ $invoiceData['id'] }}</p>
        <p><strong>Customer name:</strong> {{ $invoiceData['customer_name'] }}</p>
        <p><strong>Date:</strong> {{ $invoiceData['date'] }}</p>
        
        <h3>PRODUCT DETAILS</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>product name</th>
                    <th>quantity</th>
                    <th>price</th>
                    <th>To money</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoiceData['products'] as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product['name'] }}</td>
                        <td style="text-align: center;">{{ $product['quantity'] }}</td>
                        <td>{{ number_format($product['price'], 0, ',', '.') }} D</td>
                        <td>{{ number_format($product['price'] * $product['quantity'], 0, ',', '.') }} D</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Total: {{ number_format($invoiceData['total_amount'], 0, ',', '.') }} D</h3>
    </div>
</body>
</html>
