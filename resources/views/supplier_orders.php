<h1>Order History</h1>

<?php if(count($orders) > 0): ?>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Pharmacy</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($orders as $order):
    // Get pharmacy info safely
    $pharmacy = DB::table('pharmacies')->where('owner_id', $order->user_id)->first();
?>
<tr>
    <td><?= $order->order_number ?></td>
    <td>
        <?php if($pharmacy): ?>
            <a href="/supplier/pharmacy/<?= $pharmacy->id ?>"><?= $pharmacy->name ?></a>
        <?php else: ?>
            Unknown Pharmacy
        <?php endif; ?>
    </td>
    <td><?= $order->product_name ?></td>
    <td><?= $order->quantity ?></td>
    <td><?= $order->subtotal ?></td>
    <td><?= $order->status ?></td>
</tr>
<?php endforeach; ?>

        </tbody>
    </table>
<?php else: ?>
    <p>No orders yet.</p>
<?php endif; ?>

<a href="/dashboard/supplier">Back to Dashboard</a>
