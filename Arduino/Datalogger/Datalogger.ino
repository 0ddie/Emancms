/*
  SD card datalogger

 This example shows how to log data from three analog sensors
 to an SD card mounted on the Arduino Yún using the Bridge library.

 The circuit:
 * analog sensors on analog pins 0, 1 and 2
 * SD card attached to SD card slot of the Arduino Yún

 Prepare your SD card creating an empty folder in the SD root
 named "arduino". This will ensure that the Yún will create a link
 to the SD to the "/mnt/sd" path.

 You can remove the SD card while the Linux and the
 sketch are running but be careful not to remove it while
 the system is writing to it.

 created  24 Nov 2010
 modified 9 Apr 2012
 by Tom Igoe
 adapted to the Yún Bridge library 20 Jun 2013
 by Federico Vanzati
 modified  21 Jun 2013
 by Tom Igoe

 This example code is in the public domain.

 http://www.arduino.cc/en/Tutorial/YunDatalogger

 */

#include <FileIO.h>
int sensorPin = 0;
void setup() {
  // Initialize the Bridge and the Console
  Bridge.begin();
  Console.begin();
  FileSystem.begin();

  while (!Console); // wait for Console port to connect.
  Console.println("Filesystem datalogger\n");
}


void loop () {
  // make a string that start with a timestamp for assembling the data to log:
  String dataString;
  dataString += getTimeStamp();
  dataString += " = ";
  // add in attribute uid here
  // read three sensors and append to the string:
  int analogPin = 0;
  int sensor = analogRead(analogPin);
  dataString += String(sensor);


  // open the file. note that only one file can be open at a time,
  // so you have to close this one before opening another.
  // The FileSystem card is mounted at the following "/mnt/FileSystema1"
  File dataFile = FileSystem.open("/mnt/sd/datalog.txt", FILE_APPEND);

  // if the file is available, write to it:
  if (dataFile) {
    dataFile.println(dataString);
    dataFile.close();
    // print to the Console port too:
    Console.println(dataString);
  }
  // if the file isn't open, pop up an error:
  else {
    Console.println("error opening datalog.txt");
  }

  delay(15000);

}

// This function return a string with the time stamp
String getTimeStamp() {
  String result;
  Process time;
  // date is a command line utility to get the date and the time
  // in different formats depending on the additional parameter
  time.begin("date");
  time.addParameter("+%D-%T");  // parameters: D for the complete date mm/dd/yy
  //             T for the time hh:mm:ss
  time.run();  // run the command

  // read the output of the command
  while (time.available() > 0) {
    char c = time.read();
    if (c != '\n')
      result += c;
  }

  return result;
}


float newTemp() {
  //getting the voltage reading from the temperature sensor
  int reading = analogRead(sensorPin);
  // converting that reading to voltage, for 3.3v arduino use 3.3
  float voltage = reading * 5.0;
  voltage /= 1024.0;
  float temperatureC = (voltage - 0.5) * 100 ; //converting from 10 mv per degree wit 500 mV offset
  //to degrees ((voltage - 500mV) times 100)
  Console.print(temperatureC); Serial.println(" degrees C");

  delay(1000); //waiting a second

  return temperatureC;
}

/*  
 *   Useful code for reading from the file
 *   
 *   
 *   myFile = SD.open("test.txt");
  if (myFile) {
    Serial.println("test.txt:");
    
    // read from the file until there's nothing else in it:
    while (myFile.available()) {
        Serial.write(myFile.read());
    }
    // close the file:
    myFile.close();
  } else {
    // if the file didn't open, print an error:
    Serial.println("error opening test.txt");
  }


  *
  *Script to be used with shell command to search the file for the correct data.
  *

  
  */
  
