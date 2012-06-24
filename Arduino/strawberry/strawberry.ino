/*
 Project: Strawberry
 Author: Kevin Booth
 Date: 23 June 2012
 
 Purpose: Automated web controled sprinkler system
 */

#include <SPI.h>
#include <Ethernet.h>

// Enter a MAC address for your controller below.
// Newer Ethernet shields have a MAC address printed on a sticker on the shield
byte mac[] = {  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
char serverName[] = "boxxymays.com";
int connectionDelay = 15000; 
int zoneOn = 0; // Send back to server for sprinkler status
boolean hasIP = false; // During cmdGetIPAddress setup
int resetCount = 0; //Track how many times we failed to connect to server
int currentlyOn = 0;

// zone to pinOut
int zone1 = 2;
int zone2 = 3;
int zone3 = 4;
int zone4 = 5;
int zone5 = 6;
int zone6 = 7;
int zone7 = 8;
int zone8 = 9;

//Setup client class
EthernetClient client;

void setup() {
  // start the serial library:
  Serial.begin(9600);
  delay(1000);
  
  //Set pins to output
  pinMode(zone1, OUTPUT);   
  pinMode(zone2, OUTPUT);   
  pinMode(zone3, OUTPUT);   
  pinMode(zone4, OUTPUT);   
  pinMode(zone5, OUTPUT);   
  pinMode(zone6, OUTPUT);   
  pinMode(zone7, OUTPUT);   
  pinMode(zone8, OUTPUT);
  
  //Get IP Address
  cmdGetIPAddrss();

} //End of Setup

void loop()
{
  Serial.println("Connecting to server");
  Serial.println(Ethernet.localIP());
  if (client.connect(serverName, 80)) {
    Serial.println("connected to server");
    // Make a HTTP request:
    String urlRequest = "GET /sprinklers/getSprinklers.php?pinOn=" + String(zoneOn) + " HTTP/1.0";
    String urlDisconnect = "Connection: close";
    Serial.println("Sending to server \r\n" + urlRequest + "\r\n" + urlDisconnect); 
    
    client.println(urlRequest);
    client.println(urlDisconnect);
    client.println();
    
    delay(2000); //Wait for server to respond
    } 
  else {
    Serial.println("connection to server failed");
    resetCount++;
    cmdShutDownConnection();
    }
  
  if(resetCount > 5){
      cmdShutDownAll();
    }
  
  if(client.available()){
    Serial.println("Returned from client");
    resetCount = 0;
    //Get the size of the page
    int pageSize = client.available();
    uint8_t returnedData[pageSize];
    
    //Read the entire page into a buffer
    client.read(returnedData ,pageSize);

    //The last 24 char are the page values we are looking for
    int intLine = pageSize - 24;
    while(intLine < pageSize){
      if(intLine == pageSize - 23){
        if(char(returnedData[intLine]) == '1'){
            cmdTurnOnSprinkler(zone1);
          }
        }
      if(intLine == pageSize - 20){
        if(char(returnedData[intLine]) == '1'){
           cmdTurnOnSprinkler(zone2); 
          }
        }
      if(intLine == pageSize - 17){
        if(char(returnedData[intLine]) == '1'){
            cmdTurnOnSprinkler(zone3);
          }
        }
      if(intLine == pageSize - 14){
        if(char(returnedData[intLine]) == '1'){
            cmdTurnOnSprinkler(zone4);
          }
        }
      if(intLine == pageSize - 11){
        if(char(returnedData[intLine]) == '1'){
            cmdTurnOnSprinkler(zone5);
          }
        }
      if(intLine == pageSize - 8){
        if(char(returnedData[intLine]) == '1'){
            cmdTurnOnSprinkler(zone6);
          }
        }
      if(intLine == pageSize - 5){
        if(char(returnedData[intLine]) == '1'){
            cmdTurnOnSprinkler(zone7);
          }
        }
      if(intLine == pageSize - 2){
        if(char(returnedData[intLine]) == '1'){
            cmdTurnOnSprinkler(zone8);
          }
        }
       intLine++;
    }
   }
 cmdShutDownConnection();
 delay(connectionDelay);
} //End of Loop

//Get DHCP Address
void cmdGetIPAddrss(){
  while(hasIP == false){
    hasIP = Ethernet.begin(mac);
    if (hasIP == false){ //Did not get IP this time so wait and try again
      delay(30000);
      }
   }
  Serial.println("GOT IP!");
  delay(3000); //Allow interface to come online completly
}

//Clean up connection
void cmdShutDownConnection(){
  Serial.println("Clean up connection");
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
void cmdShutDownAll(){
  Serial.println("EMERGENCY SHUTDOWN!!!");
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
  



