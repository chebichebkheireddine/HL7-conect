<?php
// Create a socket for the receiver
$receiverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($receiverSocket === false) {
    echo "Error: Unable to create socket: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

// Bind the socket to the receiver's IP address and port
$receiverIP = '192.168.114.1'; // Use '0.0.0.0' to listen on all available interfaces
$receiverPort = 3305; // Use the same port as specified in the sender code
$result = socket_bind($receiverSocket, $receiverIP, $receiverPort);
if ($result === false) {
    echo "Error: Unable to bind socket: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

// Listen for incoming connections
$result = socket_listen($receiverSocket, 3);
if ($result === false) {
    echo "Error: Unable to listen for connections: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

echo "Receiver waiting for HL7 messages on $receiverIP:$receiverPort\n";

// Accept incoming connections and handle HL7 messages
while (true) {
    // Accept a connection
    $clientSocket = socket_accept($receiverSocket);
    if ($clientSocket === false) {
        echo "Error: Unable to accept connection: " . socket_strerror(socket_last_error()) . "\n";
        continue;
    }

    // Read the HL7 message from the client
    $hl7Message = '';
    while ($buffer = socket_read($clientSocket, 1024)) {
        $hl7Message .= $buffer;
    }

    // Log the received HL7 message
    echo "Received HL7 message:\n$hl7Message\n";

    // Optionally, process the HL7 message further here

    // Close the client socket
    socket_close($clientSocket);
}

// Close the receiver socket (this code is unreachable due to the infinite loop above)
socket_close($receiverSocket);
