// Authenticate the user
function authenticateUser() {
    // Add your authentication logic here
    // For example, you can check if the user has a valid token or session
    // If the user is not authenticated, you can return an error response or redirect them to the login page
}

// Get messages between two users before a specified time
function getMessages($userId1, $userId2, $time) {
    // Add your database connection logic here
    // For example, you can use PDO or mysqli to connect to your database

    // Prepare and execute the query to retrieve messages
    $query = "SELECT * FROM message WHERE (sender_id = :userId1 AND receiver_id = :userId2) OR (sender_id = :userId2 AND receiver_id = :userId1) AND sent_time < :time LIMIT 10";
    // Bind the parameters to prevent SQL injection
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userId1', $userId1);
    $statement->bindParam(':userId2', $userId2);
    $statement->bindParam(':time', $time);
    $statement->execute();

    // Fetch the results
    $messages = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Return the messages
    return $messages;
}

// Authenticate the user before accessing the API
authenticateUser();

// Get the user IDs and time from the request
$userId1 = $_GET['userId1'];
$userId2 = $_GET['userId2'];
$time = $_GET['time'];

// Get the messages between the two users before the specified time
$messages = getMessages($userId1, $userId2, $time);

// Return the messages as JSON
header('Content-Type: application/json');
echo json_encode($messages);
