<?php

// Set up the socket server (replace LISTEN_IP and LISTEN_PORT with actual values)
$listenIp = 'LISTEN_IP';
$listenPort = 'LISTEN_PORT';

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: " . socket_strerror(socket_last_error()) . "\n";
} else {
    $result = socket_bind($socket, $listenIp, $listenPort);
    if ($result === false) {
        echo "socket_bind() failed: " . socket_strerror(socket_last_error($socket)) . "\n";
    } else {
        $result = socket_listen($socket, 3); // Maximum 3 pending connections
        if ($result === false) {
            echo "socket_listen() failed: " . socket_strerror(socket_last_error($socket)) . "\n";
        } else {
            echo "Socket server started and listening on $listenIp:$listenPort\n";

            // Accept incoming connections and handle HL7 messages
            while (true) {
                $clientSocket = socket_accept($socket);
                if ($clientSocket === false) {
                    echo "socket_accept() failed: " . socket_strerror(socket_last_error($socket)) . "\n";
                    break;
                } else {
                    $hl7Message = socket_read($clientSocket, 1024); // Adjust buffer size as needed
                    echo "Received HL7 message:\n$hl7Message\n";

                    // Process the received HL7 message (e.g., parse and handle the content)

                    // Send acknowledgment back to the sender if needed
                    $acknowledgment = "ACK^A01|MSG000001|P\r\n";
                    socket_write($clientSocket, $acknowledgment, strlen($acknowledgment));
                    echo "Acknowledgment sent to sender\n";

                    socket_close($clientSocket);
                }
            }
        }
    }
    socket_close($socket);
}
