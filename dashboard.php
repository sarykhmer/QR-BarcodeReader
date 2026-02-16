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

        button {
            background-color: green;
            border-radius: 10px;
            color: white;
            margin: auto;
        }

        h1,
        input,
        select {
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Create new record</h1>
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
                <button type="submit">New Record</button>
            </div>
            <div class="table">
                <table style="position: relative; margin: auto;">
                    <thead>
                        <th colspan="2">Open old Record</th>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->prepare("SELECT r.recordID, r.date, t.typeID, t.type FROM tblRecord r JOIN tblType t ON r.typeID = t.typeID WHERE t.typeID = ? ORDER BY date DESC");
                        $stmt->execute([$typeID]);
                        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($records as $record) :
                        ?>
                            <tr>
                                <td><?= $record['date'] . " " . $record['type'] ?></td>
                                <td><a href="createRecord_process.php?recordID=<?= $record['recordID'] ?>&name=<?= $record['date'] . "-" . $record['type'] ?>">📷Scan </a></td>
                                <td><a href="openRecord.php?recordID=<?= $record['recordID'] ?> &date=<?= $record['date'] ?>&typeID=<?= $record['typeID'] ?>"> 📝open record</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</body>

</html>