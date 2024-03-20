<?php
// Include the autoload file for the Aranyasen\HL7 library
require_once 'C:\wamp64\www\HL7-v2\vendor\autoload.php';

// Import necessary classes from the library
use Aranyasen\HL7;
use Aranyasen\HL7\Message;

// Define the HL7 message segments
$hl7Message = new Message();
$hl7Message->addSegment(new HL7\Segments\MSH([
    'SendingApp',
    'SendingFac',
    'RecvApp',
    'RecvFac',
    date('YmdHis'),
    '',
    'ORU^R01',
    'MSG_ID',
    'P',
    '2.3'
]));

$hl7Message->addSegment(new HL7\Segments\PID([
    '2',
    '123',
    'ahmed',
    'chikhawi',
    '',
    '20011212',
    'M'
]));

$hl7Message->addSegment(new HL7\Segments\OBR([
    '1',
    'OBR_ID',
    'ORDER_DESC',
    date('YmdHis'),
    '',
    '',
    '',
    ''
]));

// Manually construct the HL7 message string
$hl7String = '';
foreach ($hl7Message->getSegments() as $segment) {
    $hl7String .= implode('|', $segment->getFields()) . "\r";
}

// Log the HL7 message sending process
error_log("HL7 message sent: $hl7String");

// Define the CT scan machine's IP address and port
$ctMachineIP = '192.168.206.128';
$ctMachinePort = 3306;

// Create a socket connection to the CT scan machine
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "Error: Unable to create socket: " . socket_strerror(socket_last_error()) . "\n";
} else {
    // Connect to the CT scan machine
    $result = socket_connect($socket, $ctMachineIP, $ctMachinePort);
    if ($result === false) {
        echo "Error: Unable to connect to CT scan machine: " . socket_strerror(socket_last_error()) . "\n";
    } else {
        // Send the HL7 message over the socket connection
        socket_write($socket, $hl7String, strlen($hl7String));
        echo "HL7 message sent successfully.\n";
    }

    // Close the socket connection
    socket_close($socket);
}
