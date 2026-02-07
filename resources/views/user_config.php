<h1>User Information</h1>

<p><strong>Email:</strong> <?= $user->email ?></p>
<p><strong>Role:</strong> <?= $user->role ?></p>
<p><strong>Status:</strong> <?= $user->status ?></p>

<?php if($user->role === 'pharmacy' && $pharmacy): ?>
    <h3>Pharmacy Details</h3>
    <p><strong>Name:</strong> <?= $pharmacy->name ?></p>
    <p><strong>Wilaya:</strong> <?= $pharmacy->wilaya ?></p>
    <p><strong>Commune:</strong> <?= $pharmacy->commune ?></p>
    <p><strong>Address:</strong> <?= $pharmacy->address ?></p>
<?php endif; ?>

<h3>Orders</h3>
<?php if(count($orders) > 0): ?>
<table border="1" cellpadding="5">
    <tr>
        <th>Order Number</th>
        <th>Total</th>
        <th>Status</th>
        <th>Created At</th>
    </tr>
    <?php foreach($orders as $order): ?>
    <tr>
        <td><?= $order->order_number ?></td>
        <td><?= $order->total ?></td>
        <td><?= $order->status ?></td>
        <td><?= $order->created_at ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>No orders found.</p>
<?php endif; ?>

<a href="javascript:history.back()">Back</a>
