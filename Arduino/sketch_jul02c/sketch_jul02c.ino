#include <Bridge.h>
#include <Process.h>
#include <Wire.h>


void setup() {
  // Initialize Bridge
  Bridge.begin();

  String createHtmlString = String("http://localhost/OpenEMan/register/create.json?");
  String apikey = String("&apikey=7c399b2a696c8e1d3efebb7767fba593");
  String nodeMAC = String("&nodeMAC=00:14:32:01:23:45");
  String fromAddress = String("&fromAddress=127.37.25.16");
  String timeout = String("&timeout=15");

  createHtmlString.concat(apikey);
  createHtmlString.concat(nodeMAC);
  createHtmlString.concat(fromAddress);
  createHtmlString.concat(timeout);
  // put your setup code here, to run once:
    Process p;    // Create a process and call it "p"
  p.begin("curl");  // Process that launch the "curl" command
  p.addParameter(createHtmlString); // Add the URL parameter to "curl"
  p.run();    // Run the process and wait for its termination
  int i = 0;
  char createResponse[100];
  while (p.available() > 0) {
    char c  = p.read();
    createResponse[i] = c;
    i++; 
    }
  char isReg;
  isReg = createResponse[i];

  int k = i - 1;
  int c = 0;
  char nodeid[8];
  
  while ( c < k ){
    char n = createResponse[c];
    nodeid[c] = n;
    c++;
  }

  String groupID = String("0x000");
  String attributeID = String("0x000");
  String attributeNumber = String("0x00");
  String attributeDefaultValue = String("0x0");
  String timeoutS = String ("15");
  
  if(isReg = 'Y'){
  //Y means it needs to register
  String setupHtmlString = String("http://localhost/OpenEMan/register/create.json?");
  String apikeyS = String("&apikey=7c399b2a696c8e1d3efebb7767fba593");
  String nodeidS = String("&node=");
  nodeidS.concat(nodeid);
  String json = String("&json=");
  json.concat(groupID);
  json.concat(attributeID);
  json.concat(attributeNumber);
  json.concat(attributeDefaultValue);
  String timeoutS = String("&timeout=");
  json.concat(timeoutS);

  setupHtmlString.concat(apikeyS);
  setupHtmlString.concat(nodeid);
  setupHtmlString.concat(json);
  setupHtmlString.concat(timeoutS);
  
    Process p;    // Create a process and call it "p"
  p.begin("curl");  // Process that launch the "curl" command
  p.addParameter(setupHtmlString); // Add the URL parameter to "curl"
  p.run();    // Run the process and wait for its termination
  }else if(isReg = 'N'){
  //N means it's already registered
  }else{//Serious error.
    }
  
    
}

void loop() {
  // put your main code here, to run repeatedly:

}
