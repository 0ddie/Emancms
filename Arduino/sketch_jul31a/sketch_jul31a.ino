#include <Bridge.h>
#include <Process.h>
#include <Wire.h>
#include <WiFi.h>
#include <SPI.h>
#include <Console.h>

void setup() {
  Bridge.begin();
  Console.begin();
  //  Console.begin(9600);
  while (!Console) {
  }
  Console.println("Working");
  char arrayOfAttributes[100][5][33];
  //int NOA = 0;


  //Attribute 1
  //Group ID
  arrayOfAttributes[0][0]['0', 'x', '0', '2', '0', '1'];
  //Attribute ID
  arrayOfAttributes[0][1]['0', 'x', '0', '0', '0', '0'];
  //Attribute Number
  arrayOfAttributes[0][2]['0', 'x', '0', '1', '9'];
  //Attribute Default Value
  arrayOfAttributes[0][3]['0', 'x', '0'];
  //Attribute Timeout
  arrayOfAttributes[0][4]['1', '5'];
  //NOA++;

  //Attribute 2
  //Group ID
  arrayOfAttributes[1][0]['0', 'x', '0', '0', '0', '0'];
  //Attribute ID
  arrayOfAttributes[1][1]['0', 'x', '0', '0', '0', '2'];
  //Attribute Number
  arrayOfAttributes[1][2]['0', 'x', '0', '1', '9'];
  //Attribute Default Value
  arrayOfAttributes[1][3]['0', 'x', '0'];
  //Attribute Timeout
  arrayOfAttributes[1][4]['1', '5'];
  //NOA++;
  int attributeAdded = 0;
  String groupID;
    for ( int xb = 0; xb < 6; xb++ ) {
    int zb = xb -1;
      //String groupID;
      groupID += arrayOfAttributes[attributeAdded][0][zb];
    }
    for ( int xc = 0; xc < 6; xc++ ) {
    int zc = xc -1;
    String attributeID;
      attributeID += arrayOfAttributes[attributeAdded][1][zc];
    }
    for ( int xd = 0; xd < 6; xd++ ) {
    int zd = xd -1;
    String attributeNumber;
      attributeNumber += arrayOfAttributes[attributeAdded][2][zd];
    }
    for ( int xe = 0; xe < 6; xe++ ) {
    int ze = xe -1;
    String attributeDefaultValue;
      attributeDefaultValue += arrayOfAttributes[attributeAdded][3][ze];
    }
    for ( int xf = 0; xf < 6; xf++ ) {
    String timeout;
    int zf = xf -1;
      timeout += arrayOfAttributes[attributeAdded][4][zf];
    }
    Console.println(groupID);
}

void loop() {
  // put your main code here, to run repeatedly:

}
