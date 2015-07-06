/*#include <unwind-cxx.h>
#include <utility.h>
#include <StandardCplusplus.h>
#include <system_configuration.h>



  Running process using Process class.

 This sketch demonstrate how to run linux processes
 using an Arduino YÃºn.

 created 5 Jun 2013
 by Cristian Maglie

 This example code is in the public domain.

 */
//#include <iostream>
//#include <vector>
#include <Bridge.h>
#include <Process.h>
#include <Wire.h>
#include <math.h>


float vcc = 3.29;                       // only used for display purposes, if used
// set to the measured Vcc.
float pad = 9940;                       // balance/pad resistor value, set this to
// the measured resistance of your pad resistor
float thermr = 10000;                   // thermistor nominal resistance

float Thermistor(int RawADC) {
  long Resistance;
  float Temp;  // Dual-Purpose variable to save space.

  Resistance = pad * ((4095.0 / RawADC) - 1);
  Serial.println(Resistance);
  Temp = log(Resistance); // Saving the Log(resistance) so not to calculate  it 4 times later
  Serial.println(Temp);
  Temp = 1 / (0.001129148 + (0.000234125 * Temp) + (0.0000000876741 * Temp * Temp * Temp));
  Serial.println(Temp);
  Temp = Temp - 273.15;  // Convert Kelvin to Celsius
  Serial.println(Temp);

  return Temp;

}
int val = 0;
int aRead = A0;
boolean request = false;
int lastRequestedItem = 0;
//std::vector<int> tempVector;
String returnToEmoncms = String(" ");
int x = 0;

void setup() {
  // Initialize Bridge
  Bridge.begin();

  // Initialize Serial
  Serial.begin(9600);

  // Wait until a Serial Monitor is connected.
  while (!Serial);


  // run various example processes
  runCurlCreate();
}

void loop() {
  float temp;
  //int lsize;
  val = analogRead(aRead);
  temp = Thermistor(val);     // read ADC and  convert it to Celsius
  //tempVector.addElement(temp);
  /*size_t size = (lsize + 1);
  std::vector<int> array(size);*/
  delay(1000);


  if (request == true) {
    //int numberOfItemsRequested = (tempVector.size() - lastRequestedItem);
    
      char str[15];
      sprintf(str, "%d", x);
      returnToEmoncms.concat(str);
      returnToEmoncms.concat(",");

    }
    //Send back to emoncms
    //int lastRequestedItem = tempVector.size();
    boolean request = false;
  }

}



*/

void runCurlCreate() {

    
  String htmlString = String("http://localhost/OpenEMan/register/create.json?");
  String apikey = String("&apikey=7c399b2a696c8e1d3efebb7767fba593");
  String nodeMAC = String("&nodeMAC=00:14:32:01:23:45");
  String fromAddress = String("&fromAddress=127.37.25.16");
  String timeout = String("&timeout=15");

  htmlString.concat(apikey);
  htmlString.concat(nodeMAC);
  htmlString.concat(fromAddress);
  htmlString.concat(timeout);
  // Launch "curl" command and get Arduino ascii art logo from the network
  // curl is command line program for transferring data using different internet protocols
  Process p;		// Create a process and call it "p"
  p.begin("curl");	// Process that launch the "curl" command
  p.addParameter(htmlString); // Add the URL parameter to "curl"
  p.run();		// Run the process and wait for its termination

  // Print arduino logo over the Serial
  // A process output can be read with the stream methods
  while (p.available() > 0) {
    char nodeId = p.read();

  }
  // Ensure the last bit of data is sent.
  Serial.flush();
}

void runCurlSetup() {
  String htmlString = String("http://localhost/OpenEMan/register/create.json?");
  // Launch "curl" command and get Arduino ascii art logo from the network
  // curl is command line program for transferring data using different internet protocols
  Process p;		// Create a process and call it "p"
  p.begin("curl");	// Process that launch the "curl" command
  p.addParameter(htmlString); // Add the URL parameter to "curl"
  p.run();		// Run the process and wait for its termination

  // Print arduino logo over the Serial
  // A process output can be read with the stream methods
  while (p.available() > 0) {
    char ok = p.read();

  }
  // Ensure the last bit of data is sent.
  Serial.flush();
}




