/*
  Web client
 
 This sketch connects to a website (http://www.google.com)
 using an Arduino Wiznet Ethernet shield. 
 
 Circuit:
 * Ethernet shield attached to pins 10, 11, 12, 13
 
 created 18 Dec 2009
 by David A. Mellis
 
 */

#include <SPI.h>
#include <Ethernet.h>

// Enter a MAC address for your controller below.
// Newer Ethernet shields have a MAC address printed on a sticker on the shield
byte mac[] = {  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
char serverName[] = "boxxymays.com";
int connectionDelay = 15000;
boolean readnext = false;
int pin = 2;
int stage = 1;
int relaysOpen = 0;

int resetCount = 0;

int lastOnPin = 0;
int pinOnCount = 0;
int pinOn = 0;
int cutOffCount = 720;

// Initialize the Ethernet client library
// with the IP address and port of the server 
// that you want to connect to (port 80 is default for HTTP):
EthernetClient client;

void setup() {
  // start the serial library:
  Serial.begin(9600);
  pinMode(2, OUTPUT);   
  pinMode(3, OUTPUT);   
  pinMode(4, OUTPUT);   
  pinMode(5, OUTPUT);   
  pinMode(6, OUTPUT);   
  pinMode(7, OUTPUT);   
  pinMode(8, OUTPUT);   
  pinMode(9, OUTPUT);   
  // start the Ethernet connection:
  if (Ethernet.begin(mac) == 0) {
    Serial.println("Failed to configure Ethernet using DHCP");
    // no point in carrying on, so do nothing forevermore:
    while(Ethernet.begin(mac) == 0){
      Serial.println("Failed to configure Ethernet using DHCP");
      delay(5000);
    }
  }
  Serial.println("GOT IP!");
  delay(10000);
  
  
}

void loop()
{
  if(stage == 1){
    delay(connectionDelay);
    Serial.println("Connecting...");
    // if you get a connection, report back via serial:
    if (client.connect(serverName, 80)) {
      Serial.println("connected");
      // Make a HTTP request:
      client.println("GET /sprinklers/getSprinklers.php?pinOn=" + String(pinOn) + " HTTP/1.0");
      client.println();
    } else{
      Serial.println("connection failed");
    }
    relaysOpen = 0;
    resetCount = resetCount+1;
    stage = 2;
    pinOn = 0;
    if(resetCount > 5){
      digitalWrite(2, LOW);
      digitalWrite(3, LOW);
      digitalWrite(4, LOW);
      digitalWrite(5, LOW);
      digitalWrite(6, LOW);
      digitalWrite(7, LOW);
      digitalWrite(8, LOW);
      digitalWrite(9, LOW);
    }
  }
  
  if(stage == 2){
    if(client.available()){
      char c = client.read();
       if(readnext){
        if(c == 48){
          digitalWrite(pin, LOW);
          resetCount = 0;
        }else{
          if(relaysOpen < 1){
            resetCount = 0;
            if(lastOnPin == pin){
             pinOnCount = pinOnCount+1; 
            }else{
             lastOnPin = pin;
             pinOnCount = 0; 
            }
            if(cutOffCount > pinOnCount){
              digitalWrite(pin, HIGH);
              pinOn = pin;
            }else{
              digitalWrite(pin, LOW);
            }
            relaysOpen = 1;
          }else{
            resetCount = 0;
            digitalWrite(pin, LOW);
          }
        }
        Serial.print(c);
        readnext = false;  
       }
       if(c == 91){
         readnext = true;
       }
       if(c == 93){
         pin = pin + 1;
       }
    }
    
    if (!client.connected()) {
      pin = 2;
      Serial.println();
      Serial.println("disconnecting.");
      client.stop();
      stage = 1;
    }
  }
  
  
}



