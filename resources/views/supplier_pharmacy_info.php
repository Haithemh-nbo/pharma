<h1>Pharmacy Information</h1>

<p><strong>Name:</strong> <?= $pharmacy->name ?></p>
<p><strong>Email:</strong> <?= $pharmacy->email ?></p>
<p><strong>Phone:</strong> <?= $pharmacy->phone ?></p>
<p><strong>Wilaya:</strong> <?= $pharmacy->wilaya ?></p>
<p><strong>Commune:</strong> <?= $pharmacy->commune ?></p>
<p><strong>Address:</strong> <?= $pharmacy->address ?></p>

<hr>

<h2>Orders for Your Products</h2>
<?php if(count($orders) > 0): ?>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order): ?>
            <tr>
                <td><?= $order->order_number ?></td>
                <td><?= $order->product_name ?></td>
                <td><?= $order->quantity ?></td>
                <td><?= $order->subtotal ?></td>
                <td><?= $order->status ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No orders from this pharmacy.</p>
<?php endif; ?>

<a href="/supplier/orders">Back to Orders</a> |
<a href="/dashboard/supplier">Back to Dashboard</a>
