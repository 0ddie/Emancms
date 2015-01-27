byte serv[] = {/*enter IP here*/};
TCPClient client;
#include <string.h>
#include <stdio.h>
void setup()
{
    Serial.begin(9600);
  // start listening for clients
    while(!Serial.available()) SPARK_WLAN_Loop();
  //  client.connect(serv,23);
    if (client.connect(serv, 80))
    {
        Serial.println("connected");
        client.println("We are all connected");
    }
    else
    {
        Serial.println("connection failed");
    }
}

String getValue(String data, char separator, int index)
{
  int found = 0;
  int strIndex[] = {0, -1};
  int maxIndex = data.length()-1;

  for(int i=0; i<=maxIndex && found<=index; i++){
    if(data.charAt(i)==separator || i==maxIndex){
        found++;
        strIndex[0] = strIndex[1]+1;
        strIndex[1] = (i == maxIndex) ? i+1 : i;
    }
  }

  return found>index ? data.substring(strIndex[0], strIndex[1]) : "";
}

void loop()
{
    Serial.println("Yep");    
    int x = 0;
    if (client.connected())
    {   
        }*/if (Serial.available() > 0) {
    // read the incoming byte:
                x = Serial.read();

    // say what you got:
                Serial.print("I received: ");
                Serial.println(x);
                if ( x!=-1){
            client.write(x);
            delay(200);
            Serial.println("Here");
            Serial.println("Length is: ");
            int length = client.read();
            Serial.println(length);
            client.flush();
            String s4 = "";
            Serial.println(s4);


            for (int i=1; i <= length; i++){
                delay(1000);
                char be = client.read();
                s4 += be;
                Serial.println(s4);
                }
        Serial.println("Post for loop");

        s4 += "\0";
        String word1 = getValue(s4, ' ', 0);
        String word2 = getValue(s4, ' ', 1);
        String word3 = getValue(s4, ' ', 2);
        String word4 = getValue(s4, ' ', 3);
        String word5 = getValue(s4, ' ', 4);
        
        
        Serial.println("");
        Serial.println("The Unique ID of this packet is: ");
        Serial.print(word1);
        Serial.println("");
        Serial.println("The ZigBeeType of this packet is: ");
        Serial.println(word2);
        Serial.println(word3);
        Serial.println(word4);
        Serial.println(word5);
        
        int y = 2;
        client.write(y);
        delay(200);
        Serial.println("Packet Ready!");
        }else{delay(2000);}
    }else{Serial.println("Serial Error");delay(2000);}
        
    }else{Serial.println("Disconnecting");delay(2000);} 

}


