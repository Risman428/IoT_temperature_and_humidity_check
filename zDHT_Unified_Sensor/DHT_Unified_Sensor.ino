#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>
#include <Servo.h>

// WiFi config
const char* ssid = "Y30i";
const char* password = "12345678";

// Pin config
#define DHTPIN D1
#define DHTTYPE DHT22
#define LEDPIN D2
#define SERVOPIN D5

DHT_Unified dht(DHTPIN, DHTTYPE);
Servo myServo;

// Timer non-blocking
unsigned long lastSensorTime = 0;
const unsigned long sensorInterval = 500;  // 0.5 detik

void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println("Menghubungkan ke WiFi...");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(300);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected!");
  Serial.print("IP Address : ");
  Serial.println(WiFi.localIP());

  dht.begin();
  pinMode(LEDPIN, OUTPUT);
  digitalWrite(LEDPIN, LOW);

  // Servo setup
  myServo.attach(SERVOPIN);
  myServo.write(0);
}

void loop() {

  unsigned long now = millis();

  // ====================================================================
  // 1️⃣ SENSOR UPDATE SETIAP 0.5 DETIK (NON BLOCKING)
  // ====================================================================
  static float temperature = 0;
  static float humidity = 0;
  static float target = 0;
  bool finalLed = false;
  bool buzzer = false;

  if (now - lastSensorTime >= sensorInterval) {
    lastSensorTime = now;

    // ---- Baca Sensor ----
    sensors_event_t tempEvent, humEvent;
    dht.temperature().getEvent(&tempEvent);
    dht.humidity().getEvent(&humEvent);

    if (!isnan(tempEvent.temperature)) temperature = tempEvent.temperature;
    if (!isnan(humEvent.relative_humidity)) humidity = humEvent.relative_humidity;

    Serial.print("Temperature: ");
    Serial.println(temperature);
    Serial.print("Humidity: ");
    Serial.println(humidity);

    // ---- KIRIM DATA SENSOR KE LARAVEL ----
    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client;
      HTTPClient http1;

      String url = "http://10.97.96.49/dhtiot/public/update-data/";
      url += String(temperature) + "/" + String(humidity);

      http1.begin(client, url);
      http1.GET();
      http1.end();
    }

    // ---- Ambil target suhu dari Laravel ----
    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client2;
      HTTPClient http2;

      http2.begin(client2, "http://10.97.96.49/dhtiot/public/control");
      int code = http2.GET();

      if (code > 0) {
        String payload = http2.getString();

        int index = payload.indexOf("target_temperature");
        if (index != -1) {
          int colon = payload.indexOf(":", index);
          int comma = payload.indexOf(",", colon);
          String value = (comma != -1)
                       ? payload.substring(colon + 1, comma)
                       : payload.substring(colon + 1);
          value.trim();
          target = value.toFloat();
        }
      }
      http2.end();
    }

    // ---- LED dan Buzzer ----
    bool autoLed = (temperature >= 30);
    bool overrideLed = (target >= 30);
    finalLed = autoLed || overrideLed;

    digitalWrite(LEDPIN, finalLed ? HIGH : LOW);

    buzzer = finalLed;

    Serial.println("-------------------------------------");
    Serial.println(finalLed ? "LED : ON" : "LED : OFF");
    Serial.println(buzzer ? "Buzzer : ON" : "Buzzer : OFF");
    Serial.println("-------------------------------------");

    // ---- Update status LED & buzzer ke Laravel ----
    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client3;
      HTTPClient http3;

      String urlStatus = "http://10.97.96.49/dhtiot/public/update-device/";
      urlStatus += String(finalLed ? 1 : 0) + "/";
      urlStatus += String(buzzer ? 1 : 0);

      http3.begin(client3, urlStatus);
      http3.GET();
      http3.end();
    }
  }

  // ====================================================================
  // 2️⃣ SERVO REAL-TIME (TANPA DELAY)
  // ====================================================================
  int servoState = 0;

  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client4;
    HTTPClient http4;

    http4.begin(client4, "http://10.97.96.49/dhtiot/public/servo-control");
    int httpCode4 = http4.GET();

    if (httpCode4 > 0) {
      String payload = http4.getString();

      int idx = payload.indexOf("servo");
      if (idx != -1) {
        int colon = payload.indexOf(":", idx);
        servoState = payload.substring(colon + 1).toInt();
      }
    }
    http4.end();
  }

  // Gerakkan servo tanpa delay
  if (servoState == 1) {
    myServo.write(180);
  } else {
    myServo.write(0);
  }
}
