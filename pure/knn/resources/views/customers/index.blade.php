<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers and Products</title>
</head>
<body>
    <h1>Покупатели и их товары</h1>

    @foreach ($customers as $customer)
        <h2>{{ $customer->name }}</h2>
        <ul>
            @foreach ($customer->products as $product)
                <li>
                    {{ $product->name }} - Количество: {{ $product->pivot->quantity }}
                </li>
            @endforeach
        </ul>
    @endforeach

    <hr>

    <h2>Введите имя клиента</h2>
    <form action="{{ route('customers.submit') }}" method="POST">
        @csrf
        <label for="data">Имя клиента:</label>
        <input type="text" id="data" name="data" required>
        <button type="submit">Найти рекомендации</button>
    </form>

    @if(isset($recommendedProducts))
        <h2>Рекомендованные товары для {{ $customerName }}:</h2>
        <ul>
            @foreach ($recommendedProducts as $productId)
                @php
                    $product = \App\Product::find($productId);
                @endphp
                @if($product)
                    <li>{{ $product->name }}</li>
                @endif
            @endforeach
        </ul>
    @endif
</body>
</html>