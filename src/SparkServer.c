#include <string.h>
TCPServer server = TCPServer(80);
TCPClient client; 

void setup()
{
    Serial.begin(9600);
  // start listening for clients
    server.begin();
    while(!Serial.available()) SPARK_WLAN_Loop();
  // Make sure your Serial Terminal app is closed before powering your Core

  // Now open your Serial Terminal, and hit any key to continue!

}

void loop()
{
   // char packet[10] = "packet";

    int y = 0;
    //Serial.println("Check");
    //char UUID[9] = "1234567 "; // int length;
    char UUID[] = {'1', '2', '3', '4', '5', '6', '7', ' '};
    char ZigBeeType[] = {'0', 'x', '0', '6', '0', '0', ' '};
    char data1[] = {'T', 'h', 'i', 's', ' '};
    char data2[] = {'i', 's', ' '};
    char data3[] = {'d', 'a', 't', 'a', '!'};
    
    char packet [28];

    memcpy(packet, UUID, 8);
    memcpy(&packet[8], ZigBeeType, 7);
    memcpy(&packet[15], data1, 5);
    memcpy(&packet[20], data2, 3);
    memcpy(&packet[23], data3, 5);
    Serial.println(packet);

        if (client.connected()) {
            while (client.available()) {
                int x = client.read();
                Serial.println("Wales");
                Serial.println(x);
                client.flush();
                if(x==49){
                int length = arraySize(packet);
                client.write(length);
                Serial.println("England");
                client.flush();
                //delay(500);
                Serial.print("It's working, promise:  ");
                client.write(packet);
                Serial.println(packet);
                client.flush();
                delay(5000);
                int x = 0;
                }else{delay(10);}
                
            }
    } else {
    // if no client is yet connected, check for a new connection
        Serial.print(" No connection here ");
        client = server.available();
        delay(200);
    } 
    //Serial.print(c);
    delay (10);

}
