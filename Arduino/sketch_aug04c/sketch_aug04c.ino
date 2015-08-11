
#include <Process.h>
#include <Wire.h>
#include <stdio.h>
#include <Time.h>

//Smart node framework
//TMP36 Pin Variables
int sensorPin = 0; //the analog pin the TMP36's Vout (sense) pin is connected to
//the resolution is 10 mV / degree centigrade with a
//500 mV offset to allow for negative temperatures

/*
 * Data storage initialiser
 */
struct TimeAndTemp
{
  time_t timestamp;
  double temp;
};

struct Station
{
  char name[2];
  struct TimeAndTemp readings[50]; // 100 is the current max number of readings this can be changed
};

struct Station stations[3]; // array of stations, each station has a name
// and contains a list of timestamped
// temperature readings.
void setup() {
  // put your setup code here, to run once:
  // Initialize Bridge
  Bridge.begin();

  // Initialize Console
  Console.begin();

  // Wait until a Console Monitor is connected.
  while (!Console);

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

int newTime(){
  time_t t = now();
}

void loop() {
  /*
   * Block 0: Thermostat in office
   */

  /*
   * Temperature Sensing
   */
   int i = 0;
   char newName[8] = {'0'};
  strcpy( stations[i].name, newName );
  printf( "Station name is %s\n", stations[i].name );

  char searchName[8] = {'0'};
  if ( strcmp( stations[i].name, searchName ) == 0 ) {
   
  }
  
  int j = 5;
  stations[i].readings[j].timestamp = newTime();
  stations[i].readings[j].temp = newTemp();

/*
  printf( "Station %s reading at time %s: %f\n",
          stations[i].name,
          ctime( &stations[i].readings[j].timestamp ),
          stations[i].readings[j].temp );
*/
  /*
   * Storage of Data
   */

  /*
   * Interpretting Server Call
   */

}
