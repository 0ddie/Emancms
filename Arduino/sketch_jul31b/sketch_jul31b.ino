#include <Bridge.h>
#include <Process.h>
#include <Wire.h>
#include <WiFi.h>
#include <SPI.h>
#include <Console.h>
#include <HttpClient.h>
char ssid[] = "CAT_Guest";     // the name of your network
int status = WL_IDLE_STATUS;
int j;
IPAddress ip;
 String setupHtmlString;
 String createHtmlString;
 String curlCreate(String a);
 String curlSetup(String b);

void setup() {
  // put your setup code here, to run once:
  Bridge.begin();
  Console.begin();
  //  Console.begin(9600);
  while (!Console) {
  }
  /*
   * Defintion Block
   */


  char *attribute[][5] = {
    {"0x0201", "0x0000", "0x019", "0x0", "15"},
    {"0x0201", "0x0000", "0x018", "0x0", "15"},
    {"0x0201", "0x0000", "0x017", "0x0", "15"},
    {"0x0000", "0x0000", "0x001", "0x0", "15"}
  };

  int numberOfAttributes = (sizeof attribute / 10);

  /*
   * Create Block
   */

  String createHtmlString = String("http://");
  String route = String("/OpenEMan/register/create.json?");
  String apikey = String("apikey=");
  String nodeMAC = String("&nodeMAC=");
  String fromAddress = String("&fromAddress=");
  String timeout = String("&timeout=");

  //Data entry from User
  String apikeyEntered = String("11133d815882bc03a4a97789d983d189");
  String serverAddress = String("172.16.0.158");
  String timeoutEntered = String("15");

  String MACAddressFAKE = String("01:02:03:04:05:10");
  String IPaddressFAKE = String("172.16.0.75");
  createHtmlString.concat(serverAddress);
  createHtmlString.concat(route);
  createHtmlString.concat(apikey);
  createHtmlString.concat(apikeyEntered);
  createHtmlString.concat(nodeMAC);
  createHtmlString.concat(MACAddressFAKE);
  createHtmlString.concat(fromAddress);
  createHtmlString.concat(IPaddressFAKE);
  createHtmlString.concat(timeout);
  createHtmlString.concat(timeoutEntered);
  Console.print("Create String:");
  Console.println(createHtmlString);

  String nodeid;
  
   //String nodeid = String ("100");
   nodeid=curlCreate(createHtmlString);
  
   Console.print("Nodeid: ");
   Console.println(nodeid);

  /*
   * Setup Block
   */ 

  for ( int go = 0; go < numberOfAttributes; go++) {
    delay(2000);
    Console.println(go);
    String groupIDA = attribute[go][0];
    groupIDA += ",";
    String attributeIDA = attribute[go][1];
    attributeIDA += ",";
    String attributeNumberA = attribute[go][2];
    attributeNumberA += ",";
    String attributeDefaultValueA = attribute[go][3];
    String timeoutA = attribute[go][4];

    String setupHtmlString = String("http://172.16.0.158");
    String route = String("/OpenEMan/register/setup.json?");
    //      setupHtmlString.concat(IPAddress);
    setupHtmlString.concat(route);
    String apikeyS = String("apikey=");
    apikeyS.concat(apikeyEntered);
    Console.println(apikeyS);
    String nodeidS = String("&node=");
    nodeidS.concat(nodeid);
    Console.println(nodeidS);
    String json = String("&json=");
    json.concat(groupIDA);
    json.concat(attributeIDA);
    json.concat(attributeNumberA);
    json.concat(attributeDefaultValueA);
    Console.println(json);
    String timeoutS = String("&timeout=");
    timeoutS.concat(timeoutA);
    //    timeoutS.concat(timeoutSV);

    setupHtmlString.concat(apikeyS);
    Console.println(setupHtmlString);
    Console.println("Breaks");
    setupHtmlString.concat(nodeidS);
    Console.println("Here");
    Console.println(setupHtmlString);
    setupHtmlString.concat(json);
    Console.println(setupHtmlString);
    setupHtmlString.concat(timeoutS);
    Console.println(setupHtmlString);
    String setupResponse;
    setupResponse=curlSetup(setupHtmlString);
    
       
    Console.println(setupResponse);


    //    int j = 23;
    char attributeUid[8];
    //      char setupResponse[100] = "Registered: A10-F10-I10";
    int y =  j - 13;
    int w = 0;
    while ( y > 0) {
      int f = y + 13;
      if (setupResponse[f] = '"') {
        f--;
      }
      attributeUid[w] = setupResponse[f];
      w++;
      y--;
    }
    Console.println(attributeUid);

    //    char attributeUid[8];
    char feedid[8];
    char inputid[8];
    int g = 0;
    while (j > 0) {
      if (setupResponse[g] == 'A') {
        int m = 0;
        while (setupResponse[g] != '-') {
          attributeUid[m] = setupResponse[g];
          m++;
          g++;
        }
      }
    } if (setupResponse[g] == 'F') {
      int m = 0;
      while (setupResponse[g] != '-') {
        feedid[m] = setupResponse[g];
        m++;
        g++;
      }
    } if (setupResponse[g] == 'I') {
      int m = 0;
      while (setupResponse[g] != '-') {
        inputid[m] = setupResponse[g];
        m++;
        g++;
      }
    }
    g++;
    //wales--;

  }
}

String curlCreate(String a){
  HttpClient client;
  client.getAsynchronously(a);
  int getResulte = client.ready();
  Console.print("Get Result:");
  Console.println(getResulte);
char createResponse[100];
int i;
  while (client.available()) {
    char d = client.read();
    //Console.print(d);
    createResponse[i] = d;
      i++;
      //Console.println(createResponse);
  }
      Console.flush();
  char isReg;
  isReg = createResponse[i];

  int sizeOfResponse = i - 3;

  String nodeid;
  //Console.println(createResponse);
  for (int aa = 0; aa < sizeOfResponse; aa++) {
    nodeid += createResponse[aa + 1];
  }
  //Console.print("1Nodeid: ");
  //Console.println(nodeid);
  delay(5000);
    int getResultee = client.getResult();
  Console.print("Get Result:");
  Console.println(getResultee);
  return nodeid;
}


String curlSetup(String b){
  Console.flush();

HttpClient client;
  client.get("http://172.16.0.158/OpenEMan/register/setup.json?apikey=11133d815882bc03a4a97789d983d189&node=100&json=0x0000,0x0000,0x002,0x0&timeout=15");
  char setupResponse[100];
  int i = 0;
  Console.println("Here");
  while (client.available()) {
    char c = client.read();
    Console.print(c);
    i++;
    setupResponse[i]=c;
    
    
  }
  Console.print("i = ");
  Console.println(i);
  Console.println("Here2");
  Console.flush();

  

  Console.println(setupResponse);
  delay(5000);
    return setupResponse;
}

void loop() {
  // put your main code here, to run repeatedly:

}
