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
  function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "laboratorphp";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    function tableExists($conn, $tableName) {
      $checkTableQuery = "SHOW TABLES LIKE '$tableName'";
      $result = $conn->query($checkTableQuery);
      return $result->num_rows > 0;
    }


    // TODO: Comanda sa ia automat coloanele, si fara ghilimele la valori, si sa puna automat ID-urile
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
      $tableName = $_POST['table'];
      // Create arrays to store columns and values
      $columns = array();
      $values = array();

      // Iterate through POST data to get columns and values
      foreach ($_POST as $key => $value) {
          if ($key !== 'action' && $key !== 'table') {
              $columns[] = $key;
              $values[] = "'" . $conn->real_escape_string($value) . "'";
          }
      }

      // Create SQL query using prepared statement
      $sqlInsert = "INSERT INTO $tableName (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
      $stmt = $conn->prepare($sqlInsert);

      // Execute the prepared statement
      $resultInsert = $stmt->execute();

      if ($resultInsert) {
          echo "<p>Record inserted successfully.</p>";
      } else {
          echo "<p>Error inserting record: " . $stmt->error . "</p>";
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
            if ($primaryKeyColumn == '' && strpos(strtolower($columnName), 'id') !== false) {
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
            echo "<td><a href='?table=$tableName&action=delete&id={$primaryKeyColumn}:" . urlencode($rowData[$primaryKeyColumn]) . "'>Delete</a></td>";
            echo "</tr>";
        }

        echo "</table>";

        $resultData1 = $conn->query($sqlData);
        // Form for inserting records
        echo "<h3>Insert Record</h3>";
        echo "<form method='post' action=''>";
        echo "<input type='hidden' name='action' value='insert'>";
        echo "<input type='hidden' name='table' value='$tableName'>";
        while ($rowData1 = $resultData1->fetch_assoc()) {
          foreach ($rowData1 as $key => $value) {
            if(!startsWith($key, $primaryKeyColumn)) {
              echo "<label for='{$key}'>$key:</label>";
              echo "<input type='text' name='$key' required>";
              echo "</br>";
            }
          }
          break;}
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
      
      //TODO: Change to take id from column
      $tableToDeleteFrom = $_GET['table'];
      $idToDelete = $_GET['id'];
      $idToDeleteIDCol = explode(":", $idToDelete);
      $sqlDelete = "DELETE FROM $tableToDeleteFrom WHERE $idToDeleteIDCol[0]=$idToDeleteIDCol[1]";
      $resultDelete = $connDelete->query($sqlDelete);

      if ($resultDelete) {
        header("Location: {$_SERVER['PHP_SELF']}?table=$tableToDeleteFrom");
        exit();
        echo "<p>Record deleted successfully.</p>";
      } else {
        echo "<p>Error deleting record: " . $connDelete->error . "</p>";
      }

      $connDelete->close();
    }
  ?>
</body>
</html>
