<?php
$user = Session::get('user');
?>
<h1>Supplier Dashboard</h1>
<p>Welcome, <?= $user->email ?></p>

<ul>
    <li><a href="/supplier/listings">My Listings</a></li>
    <li><a href="/supplier/orders">Order History</a></li>
    <li><a href="/user/<?= $u->id ?>">View Info</a></li>

</ul>

<a href="/logout">Logout</a>
