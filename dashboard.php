<?php
require "checkrole.php";
// restrive type form database
$stmt = $pdo->prepare("SELECT * FROM tbltype");
$stmt->execute();
$types = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode & QR Scanner</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .container {
            position: relative;
            max-width: 600px;
            margin: auto;
            text-align: center;
        }

        .submit {
            margin-top: 30px;
            position: relative;
            text-align: center;
        }

        .input {
            margin-top: 20px;
            width: 100%;
        }

        .table {
            margin-top: 30px;
            position: relative;
            text-align: center;
        }

        .btn-submit {
            background-color: green;
            border-radius: 10px;
            color: white;
            margin: auto;
        }

        .btn-logout {
            border-radius: 10px;
            color: white;
            float: right;
            background-color: red;
            position: fixed;
            right: 30px;
            top: 10px;
        }

        h1,
        input,
        select {
            margin: auto;
        }
    </style>
</head>

<>
    <div class="container">
        <h1>Create new record</h1>
        <button class="btn-logout" onclick="window.location.href='logout.php'">logout</button>
        <p style="margin-top: 20px;">Select date and type(Departure/Arrival) to create new record.</p>
        <form action="createRecord_process.php" method="post">
            <div class="input">
                <input type="date" name="date" required>
                <select name="type" required>
                    <?php foreach ($types as $type) : ?>
                        <option value="<?= $type['typeID'] ?>"><?= $type['type'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="submit">
                <button type="submit" class="btn-submit">New Record</button>
            </div>
            <div class="table mt-5">
                <table class="table">
                    <thead>
                        <th>Record Name</th>
                        <th colspan="2">Action</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        <?php
                        switch ($roleID) {
                            case 1:
                                $query = "SELECT r.recordID, r.date, t.typeID, t.type, r.status FROM tblRecord r JOIN tblType t ON r.typeID = t.typeID ORDER BY date DESC";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();
                                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                break;
                            default:
                                $query = "SELECT r.recordID, r.date, t.typeID, t.type, r.status FROM tblRecord r JOIN tblType t ON r.typeID = t.typeID WHERE t.typeID = ? ORDER BY date DESC";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute([$typeID]);
                                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                break;
                        }
                        foreach ($records as $record) :
                        ?>
                            <?php if ($record['status'] === "enable"): ?>
                                <tr>
                                    <td><?= $record['date'] . " " . $record['type'] ?></td>
                                    <td><a href="createRecord_process.php?recordID=<?= $record['recordID'] ?>&name=<?= $record['date'] . "-" . $record['type'] ?>">📷Scan </a></td>
                                    <td><a href="openRecord.php?recordID=<?= $record['recordID'] ?> &date=<?= $record['date'] ?>&typeID=<?= $record['typeID'] ?>" disable> 📝open record</a></td>
                                    <td><button class="btn btn-sm btn-warning" onclick="update('disable',<?= $record['recordID'] ?>)">Not Complete</button></td>
                                </tr>
                            <?php else : ?>
                                <tr>
                                    <td><?= $record['date'] . " " . $record['type'] ?></td>
                                    <td colspan="2"><a href="preview.php?recordID=<?= $record['recordID'] ?>"> 📝View record</a></td>
                                    <td><button class="btn btn-sm btn-success" onclick="update('enable',<?= $record['recordID'] ?>)">Completed</button></td>
                                </tr>
                            <?php endif ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <?php if ($roleID == 1): ?>
        <script>
            async function update(status, recordID) {
                const body = new URLSearchParams({
                    status,
                    recordID,
                });
                try {
                    const response = await fetch("update_status.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: body.toString(),
                    });
                    if (!response.ok) {
                        throw new Error("Request failed");
                    }
                    const data = await response.json();
                    if (!data.success) {
                        alert(data.message || "Update failed.");
                        return;
                    }
                    window.location.reload();
                } catch (error) {
                    alert("Try failed.");
                }
            }
        </script>
    <?php endif ?>
    </body>

</html>