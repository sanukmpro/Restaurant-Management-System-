<?php
session_start();
include '../config.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE user_id=$id");
}

$result = $conn->query("SELECT * FROM users");
?>

<h2>Manage Users</h2>

<table border="1">
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Role</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['user_id']; ?></td>
    <td><?php echo $row['username']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo $row['role']; ?></td>
    <td>
        <a href="?delete=<?php echo $row['user_id']; ?>">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

<a href="dashboard.php">Back</a>
