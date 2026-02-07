<h1>Pharmacy Dashboard</h1>
<p>Welcome, <?= Session::get('user')->email ?></p>

<?php if(Session::has('admin_original')): ?>
    <a href="/admin/return">Return to Admin</a>
<?php endif; ?>
<a href="/logout">Logout</a>
