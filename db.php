<?php
include 'connection/conn.php';

$msg = "";
$results = [];

if(isset($_POST['run_query'])){
    $query = $_POST['sql_query'];

    if(!empty($query)){
        $query_type = strtoupper(strtok(trim($query), " "));

        if($query_type === "SELECT" || $query_type === "SHOW" || $query_type === "DESCRIBE"){
            $res = $conn->query($query);
            if($res){
                if($res->num_rows > 0){
                    while($row = $res->fetch_assoc()){
                        $results[] = $row;
                    }
                } else {
                    $msg = "Query executed successfully. No results found.";
                }
            } else {
                $msg = "Error: " . $conn->error;
            }
        } else {
            // For INSERT, UPDATE, DELETE, CREATE, etc.
            if($conn->query($query) === TRUE){
                $msg = "Query executed successfully!";
            } else {
                $msg = "Error: " . $conn->error;
            }
        }
    } else {
        $msg = "Please enter a query.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Run SQL Query</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        textarea { width: 100%; height: 150px; }
        button { padding: 10px 20px; margin-top: 10px; }
        .msg { margin-top: 15px; font-weight: bold; }
        table { border-collapse: collapse; margin-top: 15px; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Run SQL Query</h2>
    <form method="post">
        <textarea name="sql_query" placeholder="Enter your SQL query here..."><?php echo isset($_POST['sql_query']) ? htmlentities($_POST['sql_query']) : ''; ?></textarea><br>
        <button type="submit" name="run_query">Run Query</button>
    </form>
    <div class="msg"><?php echo $msg; ?></div>

    <?php if(!empty($results)): ?>
        <table>
            <tr>
                <?php foreach(array_keys($results[0]) as $col): ?>
                    <th><?php echo $col; ?></th>
                <?php endforeach; ?>
            </tr>
            <?php foreach($results as $row): ?>
                <tr>
                    <?php foreach($row as $val): ?>
                        <td><?php echo $val; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
