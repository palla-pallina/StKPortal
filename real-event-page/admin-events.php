<?php
require_once "dbconfig.php";

// Fetch all events by date
$events = $dbh->query("SELECT * FROM events ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin – Manage Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background:#f5f5f7;
            padding: 30px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .event-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .event-table th, .event-table td {
            border-bottom: 1px solid #eee;
            padding: 14px;
            text-align: left;
        }

        .event-table th {
            background: #4a260e;
            color: white;
        }

        .actions button {
            padding: 8px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            color: white;
        }

        .edit-btn { background:#2980b9; }
        .edit-btn:hover { background:#1f5f86; }

        .delete-btn { background:#c0392b; }
        .delete-btn:hover { background:#992d22; }

        .add-form {
            background:white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
        }

        input, button {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .save-btn {
            background:#4a260e;
            color:white;
            border:none;
        }

        .save-btn:hover {
            background:#351c0c;
        }

        .modal-bg {
            display:none;
            position: fixed;
            top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.4);
            justify-content:center;
            align-items:center;
        }

        .modal-box {
            background:white;
            padding:25px;
            border-radius:10px;
            width:380px;
        }
    </style>
</head>
<body>

<h1>Manage Calendar Events</h1>

<!-- EVENTS TABLE -->
<table class="event-table">
    <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Event Text</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($events as $e): ?>
    <tr>
        <td><?= $e['id'] ?></td>
        <td><?= $e['date'] ?></td>
        <td><?= htmlspecialchars($e['text']) ?></td>
        <td class="actions">
            <button class="edit-btn" onclick="openEdit(<?= $e['id'] ?>, '<?= $e['date'] ?>', '<?= htmlspecialchars($e['text']) ?>')">Edit</button>
            <button class="delete-btn" onclick="deleteEvent(<?= $e['id'] ?>)">Delete</button>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- ADD NEW EVENT -->
<div class="add-form">
    <h2>Add New Event</h2>
    <form method="POST" action="event-actions.php">
        <input type="date" name="date" required>
        <input type="text" name="text" placeholder="Event text..." required style="width:60%">
        <button class="save-btn" name="add">Add Event</button>
    </form>
</div>

<!-- EDIT MODAL -->
<div class="modal-bg" id="modal">
    <div class="modal-box">
        <h2>Edit Event</h2>
        <form method="POST" action="event-actions.php">
            <input type="hidden" id="edit-id" name="id">
            <input type="date" id="edit-date" name="date" required><br><br>
            <input type="text" id="edit-text" name="text" required style="width:100%"><br><br>
            <button class="save-btn" name="edit">Save Changes</button>
        </form><br>
        <button onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
function openEdit(id, date, text) {
    document.getElementById("modal").style.display = "flex";
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-date").value = date;
    document.getElementById("edit-text").value = text;
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

function deleteEvent(id) {
    if (!confirm("Delete this event?")) return;

    window.location.href = "event-actions.php?delete=" + id;
}
</script>

</body>
</html>