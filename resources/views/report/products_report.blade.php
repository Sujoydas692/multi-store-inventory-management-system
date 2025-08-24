<html>
<head>
    <style>
        .products {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            font-size: 12px !important;
        }

        .products td, #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .products tr:nth-child(even){background-color: #f2f2f2;}

        .products tr:hover {background-color: #ddd;}

        .products th {
            padding-top: 12px;
            padding-bottom: 12px;
            padding-left: 6px;
            text-align: left;
            background-color: #04aa6d27;
            color: rgb(0, 0, 0);
        }
    </style>
</head>
<body>

<h3>Product Details</h3>

<table class="products" >
    <thead>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Stock Qty</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->price }}</td>
            <td>{{ $product->stock_qty }}</td>
            <td>{{ date('Y-m-d', strtotime($product->created_at)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>