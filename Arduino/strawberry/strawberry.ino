/*
 Project: Strawberry
 Author: Kevin Booth
 Date: 23 June 2012
 
 Purpose: Automated web controled sprinkler system
 */

#include <SPI.h>
//#include <SD.h>
#include <Ethernet.h>

byte mac[] = {  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
//IPAddress ip1(192,168,2,167);
//IPAddress dns1(192,168,2,1);
//IPAddress gateway1(192,168,2,1);
//IPAddress subnet1(255,255,255,0);
boolean ipSet = false;
boolean getDHCP = true;
char serverName[] = "boxxymays.com";
int connectionDelay = 15000; 
int zoneOn = 0; // Send back to server for sprinkler status
boolean hasIP = false; // During cmdGetIPAddress setup
int resetCount = 0; //Track how many times we failed to connect to server
int currentlyOn = 0;
const int chipSelect = 4;
boolean sprinklerOff = true;


// zone to pinOut
int zone1 = 2;
int zone2 = 3;
int zone3 = 6;
int zone4 = 7;
int zone5 = 5;
int zone6 = 8;
int zone7 = 9;
int zone8 = 11;

//Setup client class
EthernetClient client;

void setup() {
  // start the serial library:
  //Serial.begin(9600);
  //delay(5000);
  
  //Set pins to output
  pinMode(zone1, OUTPUT);   
  pinMode(zone2, OUTPUT);   
  pinMode(zone3, OUTPUT);   
  pinMode(zone4, OUTPUT);   
  pinMode(zone5, OUTPUT);   
  pinMode(zone6, OUTPUT);   
  pinMode(zone7, OUTPUT);   
  pinMode(zone8, OUTPUT);
  
  //Setup for SD Card
  pinMode(10, OUTPUT);
  
  //if (!SD.begin(chipSelect)){
  //  Serial.println("Card failed, or not present");
  //  return;
  //}
  //logData("card initialized.");
  
  //Get IP Address
  if(!ipSet){
    //logData("Get IP Address");
    cmdGetIPAddress();
  }else
  { 
    //logData("Already have IP");
  }
} //End of Setup

void loop()
{
  //logData("Connecting to server=" + String(serverName));
  if (client.connect(serverName, 80)) {
    // Make a HTTP request:
    String urlRequest = "GET /sprinklers/getSprinklers.php?pinOn=" + String(zoneOn) + " HTTP/1.0";
    String urlDisconnect = "Connection: close";
    //logData("Sending to server \r\n" + urlRequest + "\r\n" + urlDisconnect); 
    
    
    client.println(urlRequest);
    client.println(urlDisconnect);
    client.println();
    
    delay(10000); //Wait for server to respond
    } 
  else {
    //logData("connection to server failed");
    resetCount++;
    cmdShutDownConnection();
    }
  
  if(resetCount > 5){
      cmdShutDownAll("EMERGENCY SHUTDOWN!!!");
    }
  
  if(client.available()){
    //Serial.println("Returned from client");
    resetCount = 0;
    //Get the size of the page
    int pageSize = client.available();
    uint8_t returnedData[pageSize];
    
    //Read the entire page into a buffer
    client.read(returnedData ,pageSize);

    //The last 24 char are the page values we are looking for
    int intLine = pageSize - 24;
    sprinklerOff = true;
    
    while(intLine < pageSize){
      //char lineData = returnedData[intLine];
      //logData(String(lineData));
      //logData("sprinklerOff=" + String(sprinklerOff));
      if(intLine == pageSize - 23){
        if(char(returnedData[intLine]) == '1'){
            sprinklerOff = false;
            cmdTurnOnSprinkler(zone1);
          }
        }
      if(intLine == pageSize - 20){
        if(char(returnedData[intLine]) == '1'){
          sprinklerOff = false; 
          cmdTurnOnSprinkler(zone2); 
          }
        }
      if(intLine == pageSize - 17){
        if(char(returnedData[intLine]) == '1'){  
          sprinklerOff = false;
          cmdTurnOnSprinkler(zone3);
          }
        }
      if(intLine == pageSize - 14){
        if(char(returnedData[intLine]) == '1'){
            sprinklerOff = false;
            cmdTurnOnSprinkler(zone4);
          }
        }
      if(intLine == pageSize - 11){
        if(char(returnedData[intLine]) == '1'){
            sprinklerOff = false;
            cmdTurnOnSprinkler(zone5);
          }
        }
      if(intLine == pageSize - 8){
        if(char(returnedData[intLine]) == '1'){
            sprinklerOff = false;
            cmdTurnOnSprinkler(zone6);
          }
        }
      if(intLine == pageSize - 5){
        if(char(returnedData[intLine]) == '1'){
            sprinklerOff = false;
            cmdTurnOnSprinkler(zone7);
          }
        }
      if(intLine == pageSize - 2){
        if(char(returnedData[intLine]) == '1'){
            sprinklerOff = false;
            cmdTurnOnSprinkler(zone8);
          }
        }
       intLine++;
    }
    
    //logData("2sprinklerOff=" + String(sprinklerOff));
    if(sprinklerOff == true){
      cmdShutDownAll("Normal");
    }
   }
 cmdShutDownConnection();
 delay(connectionDelay);
} //End of Loop

//Get DHCP Address
void cmdGetIPAddress(){
  
  if(getDHCP == true){
    hasIP = false;
    while(hasIP == false){
      hasIP = Ethernet.begin(mac);
      if (hasIP == false){ //Did not get IP this time so wait and try again
        delay(30000);
        }
        else
        { ipSet=true;
        }
     }
  }
  else{
    //Ethernet.begin(mac, ip1, dns1, gateway1, subnet1);
  }
  //logData("GOT IP!");
  delay(8000); //Allow interface to come online completly
}

//Clean up connection
void cmdShutDownConnection(){
  //logData("Clean up connection");
  client.flush();
  delay(2000);
  client.stop();
}

//Turn on sprinkler
void cmdTurnOnSprinkler(int turnZoneOn){
  if(currentlyOn != turnZoneOn){
    digitalWrite(currentlyOn, LOW);
    delay(2000);
    currentlyOn = turnZoneOn;
    digitalWrite(turnZoneOn, HIGH);
    zoneOn = turnZoneOn;
  }
}

//Shut all zones off
void cmdShutDownAll(String reason){
  //logData("All Shutdown=" + reason);
  zoneOn = 0;
  digitalWrite(zone1, LOW); 
  digitalWrite(zone2, LOW); 
  digitalWrite(zone3, LOW); 
  digitalWrite(zone4, LOW); 
  digitalWrite(zone5, LOW); 
  digitalWrite(zone6, LOW); 
  digitalWrite(zone7, LOW); 
  digitalWrite(zone8, LOW); 
}

//Logger
void logData(String mydata){
 
 //File dataFile = SD.open("datalog1.txt", FILE_WRITE);
 
 //if(dataFile){
 // dataFile.println(mydata);
 // dataFile.close();
  //Serial.println("SD Write:" + mydata);
 //} 
 //else{
//  Serial.println("error writing to datalog.txt"); 
// }
}



