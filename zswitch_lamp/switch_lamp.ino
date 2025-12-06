#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ArduinoJson.h>

// ========================
// WIFI
// ========================
const char* ssid     = "HANIF";
const char* password = "H@n1f16_";

// ========================
// API LARAVEL
// ========================
String serverURL = "http://192.168.1.2/lamp";  // ganti IP Laravel kamu

// ========================
// PIN OUTPUT (Relay/LED)
// ========================
#define L1 D1
#define L2 D2
#define L3 D3
#define L4 D4
#define L5 D5
#define L6 D6

void setup() {
  Serial.begin(115200);

  pinMode(L1, OUTPUT);
  pinMode(L2, OUTPUT);
  pinMode(L3, OUTPUT);
  pinMode(L4, OUTPUT);
  pinMode(L5, OUTPUT);
  pinMode(L6, OUTPUT);

  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(400);
  }

  Serial.println("\nWiFi Connected!");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;

    http.begin(client, serverURL);
    int httpCode = http.GET();

    if (httpCode == HTTP_CODE_OK) {
      String response = http.getString();
      Serial.println("JSON:");
      Serial.println(response);

      StaticJsonDocument<300> doc;
      DeserializationError error = deserializeJson(doc, response);

      if (!error) {
        const char* lampu1 = doc["lampu1"];
        const char* lampu2 = doc["lampu2"];
        const char* lampu3 = doc["lampu3"];
        const char* lampu4 = doc["lampu4"];
        const char* lampu5 = doc["lampu5"];
        const char* lampu6 = doc["lampu6"];

        // SET PIN LAMPU
        digitalWrite(L1, strcmp(lampu1, "on") == 0 ? HIGH : LOW);
        digitalWrite(L2, strcmp(lampu2, "on") == 0 ? HIGH : LOW);
        digitalWrite(L3, strcmp(lampu3, "on") == 0 ? HIGH : LOW);
        digitalWrite(L4, strcmp(lampu4, "on") == 0 ? HIGH : LOW);
        digitalWrite(L5, strcmp(lampu5, "on") == 0 ? HIGH : LOW);
        digitalWrite(L6, strcmp(lampu6, "on") == 0 ? HIGH : LOW);

        // ================================
        //   PRINT STATUS KE SERIAL
        // ================================
        Serial.println("Status Lampu:");
        Serial.printf("Lampu 1 : %s\n", strcmp(lampu1, "on") == 0 ? "ON" : "OFF");
        Serial.printf("Lampu 2 : %s\n", strcmp(lampu2, "on") == 0 ? "ON" : "OFF");
        Serial.printf("Lampu 3 : %s\n", strcmp(lampu3, "on") == 0 ? "ON" : "OFF");
        Serial.printf("Lampu 4 : %s\n", strcmp(lampu4, "on") == 0 ? "ON" : "OFF");
        Serial.printf("Lampu 5 : %s\n", strcmp(lampu5, "on") == 0 ? "ON" : "OFF");
        Serial.printf("Lampu 6 : %s\n", strcmp(lampu6, "on") == 0 ? "ON" : "OFF");
        Serial.println("------------------------");
      } 
      else {
        Serial.println("Gagal parsing JSON!");
      }
    }

    http.end();
  }

  delay(1000);
}
