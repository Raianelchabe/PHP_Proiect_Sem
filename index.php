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

    // TODO: Comanda sa ia automat coloanele, si fara ghilimele la valori, si sa puna automat ID-urile
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
        $primaryKeyColumn = ''; // Variable to store the primary key column name
        while ($rowColumns = $resultColumns->fetch_assoc()) {
            $columnName = $rowColumns['Field'];
            echo "<th>$columnName</th>";

            // Check if the column is the primary key (you might need to adjust this based on your database schema)
            if (strpos(strtolower($columnName), 'id') !== false) {
            $primaryKeyColumn = $columnName;
            }
        }
        echo "<th>Action</th>"; // Column for delete button
        echo "</tr>";

        // Fetch and display table data
        $sqlData = "SELECT * FROM $tableName";
        $resultData = $conn->query($sqlData);

        while ($rowData = $resultData->fetch_assoc()) {
            echo "<tr>";
            foreach ($rowData as $key => $value) {
            echo "<td>$value</td>";
            }
            echo "<td><a href='?table=$tableName&action=delete&id={$rowData[$primaryKeyColumn]}'>Delete</a></td>";
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

      $sqlDelete = "DELETE FROM $tableToDeleteFrom WHERE id = $idToDelete";
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
