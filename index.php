<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SQL Editor</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }
    table, th, td {
      border: 1px solid black;
    }
  </style>
</head>
<body>
  <h1>SQL Editor</h1>

  <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "laboratorphp";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // Handle insert action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
      $tableName = $_POST['table'];
      $columns = $_POST['columns'];
      $values = $_POST['values'];

      $sqlInsert = "INSERT INTO $tableName ($columns) VALUES ($values)";
      $resultInsert = $conn->query($sqlInsert);

      if ($resultInsert) {
        echo "<p>Record inserted successfully.</p>";
      } else {
        echo "<p>Error inserting record: " . $conn->error . "</p>";
      }
    }

    // Handle update action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
      $tableName = $_POST['table'];
      $id = $_POST['id'];
      $columns = explode(',', $_POST['columns']);
      $values = explode(',', $_POST['values']);
      $set = "";

      for ($i = 0; $i < count($columns); $i++) {
        $set .= "$columns[$i] = '$values[$i]',";
      }

      $set = rtrim($set, ','); // Remove trailing comma

      $sqlUpdate = "UPDATE $tableName SET $set WHERE id = $id";
      $resultUpdate = $conn->query($sqlUpdate);

      if ($resultUpdate) {
        echo "<p>Record updated successfully.</p>";
      } else {
        echo "<p>Error updating record: " . $conn->error . "</p>";
      }
    }

    // Fetch table names
    $sql = "SHOW TABLES";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      echo "<h2>Tables</h2>";
      echo "<ul>";
      while ($row = $result->fetch_assoc()) {
        $tableName = $row['Tables_in_' . $dbname];
        echo "<li><a href='?table=$tableName'>$tableName</a></li>";
      }
      echo "</ul>";

      if (isset($_GET['table'])) {
        $tableName = $_GET['table'];

        // Fetch table columns
        $sqlColumns = "DESCRIBE $tableName";
        $resultColumns = $conn->query($sqlColumns);

        // Display table data
        if ($resultColumns->num_rows > 0) {
          echo "<h2>$tableName</h2>";
          echo "<table>";
          echo "<tr>";
          while ($rowColumns = $resultColumns->fetch_assoc()) {
            echo "<th>{$rowColumns['Field']}</th>";
          }
          echo "<th>Action</th>"; // Column for delete button
          echo "</tr>";

          // Fetch and display table data
          $sqlData = "SELECT * FROM $tableName";
          $resultData = $conn->query($sqlData);

          while ($rowData = $resultData->fetch_assoc()) {
            echo "<tr>";
            foreach ($rowData as $value) {
              echo "<td>$value</td>";
            }
            echo "<td><a href='?table=$tableName&action=delete&id={$rowData[$rowColumns['Field']]}'>Delete</a></td>";
            echo "</tr>";
          }

          echo "</table>";

          // Form for inserting records
          echo "<h3>Insert Record</h3>";
          echo "<form method='post' action=''>";
          echo "<input type='hidden' name='action' value='insert'>";
          echo "<input type='hidden' name='table' value='$tableName'>";
          echo "<label for='columns'>Columns (comma-separated):</label>";
          echo "<input type='text' name='columns' required>";
          echo "<br>";
          echo "<label for='values'>Values (comma-separated):</label>";
          echo "<input type='text' name='values' required>";
          echo "<br>";
          echo "<button type='submit'>Insert Record</button>";
          echo "</form>";

          // Form for updating records
          echo "<h3>Update Record</h3>";
          echo "<form method='post' action=''>";
          echo "<input type='hidden' name='action' value='update'>";
          echo "<input type='hidden' name='table' value='$tableName'>";
          echo "<input type='hidden' name='id' value='$idToUpdate'>"; // Add this
          echo "<label for='columns'>Columns (comma-separated):</label>";
          echo "<input type='text' name='columns' required>";
          echo "<br>";
          echo "<label for='values'>Values (comma-separated):</label>";
          echo "<input type='text' name='values' required>";
          echo "<br>";
          echo "<button type='submit'>Update Record</button>";
          echo "</form>";
        } else {
          echo "No data found for $tableName";
        }
      }
    } else {
      echo "0 tables found";
    }

    $conn->close();

    // Handle delete action
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
      $connDelete = new mysqli($servername, $username, $password, $dbname);

      if ($connDelete->connect_error) {
        die("Connection failed: " . $connDelete->connect_error);
      }

      $tableToDeleteFrom = $_GET['table'];
      $idToDelete = $_GET['id'];

      // Fetch primary key column name
      $sqlPrimaryKey = "SHOW KEYS FROM $tableToDeleteFrom WHERE Key_name = 'PRIMARY'";
      $resultPrimaryKey = $connDelete->query($sqlPrimaryKey);
      $primaryKeyColumnName = $resultPrimaryKey->fetch_assoc()['Column_name'];

      $sqlDelete = "DELETE FROM $tableToDeleteFrom WHERE $primaryKeyColumnName = $idToDelete";
      $resultDelete = $connDelete->query($sqlDelete);

      if ($resultDelete) {
        echo "<p>Record deleted successfully.</p>";
      } else {
        echo "<p>Error deleting record: " . $connDelete->error . "</p>";
      }

      $connDelete->close();
    }
  ?>
</body>
</html>