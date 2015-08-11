void setup() {
  // put your setup code here, to run once:

}

void loop() {
  // put your main code here, to run repeatedly:
    
String str = String("CCCP");
char array[20];

str.toCharArray(array, 20);
Serial.println(array);  // Prints "CCCP"
}
