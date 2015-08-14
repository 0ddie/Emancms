/*
  Running process using Process class. 

 This sketch demonstrate how to run linux processes
 using an Arduino YÃºn. 

 created 5 Jun 2013
 by Cristian Maglie

 This example code is in the public domain.

 */

#include <Process.h>

void setup() {
  // Initialize Bridge
  Bridge.begin();

  // Initialize Console
  Console.begin();

  // Wait until a Console Monitor is connected.
  while (!Console);

  // run various example processes
  runCurl();
  
  runCurl2();
}

void loop() {
  // Do nothing here.
}

void runCurl() {
  // Launch "curl" command and get Arduino ascii art logo from the network
  // curl is command line program for transferring data using different internet protocols
  Process p;    // Create a process and call it "p"
  p.begin("curl");  // Process that launch the "curl" command
  p.addParameter("http://arduino.cc/asciilogo.txt"); // Add the URL parameter to "curl"
  p.run();    // Run the process and wait for its termination

  // Print arduino logo over the Console
  // A process output can be read with the stream methods
  while (p.available()>0) {
    char c = p.read();
    Console.print(c);
  }
  // Ensure the last bit of data is sent.
  Console.flush();
}

void runCurl2() {
  // Launch "curl" command and get Arduino ascii art logo from the network
  // curl is command line program for transferring data using different internet protocols
  Process p;    // Create a process and call it "p"
  p.begin("curl");  // Process that launch the "curl" command
  p.addParameter("http://arduino.cc"); // Add the URL parameter to "curl"
  p.run();    // Run the process and wait for its termination

  // Print arduino logo over the Console
  // A process output can be read with the stream methods
  while (p.available()>0) {
    char c = p.read();
    Console.print(c);
  }
  // Ensure the last bit of data is sent.
  Console.flush();
}

