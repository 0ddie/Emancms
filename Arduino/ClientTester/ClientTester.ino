#include <Bridge.h>
#include <HttpClient.h>

void setup() {
  pinMode(13, OUTPUT);
  digitalWrite(13, LOW);
  Bridge.begin();
  Console.begin();
  while(!Console);
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
  Console.println("Here2");
  Console.flush();

  delay(5000);
}

void loop() {

}

