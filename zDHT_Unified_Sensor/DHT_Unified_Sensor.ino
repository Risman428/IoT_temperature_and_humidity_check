#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>

// WiFi config
const char* ssid = "HANIF";
const char* password = "H@n1f16_";

// Pin config
#define DHTPIN D1
#define DHTTYPE DHT22
#define LEDPIN D2

DHT_Unified dht(DHTPIN, DHTTYPE);
uint32_t delayMS;

void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println("Menghubungkan ke WiFi...");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected!");
  Serial.print("IP Address : ");
  Serial.println(WiFi.localIP());

  dht.begin();
  pinMode(LEDPIN, OUTPUT);
  digitalWrite(LEDPIN, LOW);

  delayMS = 2000;
}

void loop() {
  delay(delayMS);

  // ---- 1️⃣ Baca Sensor ----
  sensors_event_t tempEvent, humEvent;
  dht.temperature().getEvent(&tempEvent);
  dht.humidity().getEvent(&humEvent);

  float temperature = tempEvent.temperature;
  float humidity = humEvent.relative_humidity;

  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Gagal membaca sensor DHT");
    return;
  }

  Serial.print("Temperature: ");
  Serial.println(temperature);
  Serial.print("Humidity: ");
  Serial.println(humidity);

  // ---- 2️⃣ KIRIM DATA SENSOR KE LARAVEL ----
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http1;

    String url = "http://192.168.1.2/dhtiot/public/update-data/";
    url += String(temperature) + "/" + String(humidity);

    http1.begin(client, url);
    int code = http1.GET();

    if (code > 0) {
      Serial.println("Sensor sent to Laravel");
    } else {
      Serial.println("Gagal update-data");
    }
    http1.end();
  }

  // ---- 3️⃣ Ambil target suhu dari Laravel ----
  float target = 0;

  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client2;
    HTTPClient http2;

    http2.begin(client2, "http://192.168.1.2/dhtiot/public/control");
    int httpCode = http2.GET();

    if (httpCode > 0) {
      String payload = http2.getString();
      Serial.println("Control Data: " + payload);

      int index = payload.indexOf("target_temperature");
      if (index != -1) {
        int colon = payload.indexOf(":", index);
        int comma = payload.indexOf(",", colon);

        String value;

        if (comma != -1)
          value = payload.substring(colon + 1, comma);
        else
          value = payload.substring(colon + 1);

        value.trim();
        target = value.toFloat();

        Serial.print("Target suhu dari web: ");
        Serial.println(target);
      }
    } else {
      Serial.println("Gagal ambil control");
    }

    http2.end();
  }

  // ---- 4️⃣ Penentuan LED final ----
  bool autoLed = (temperature >= 28);   // Sensor ON ≥ 28
  bool overrideLed = (target >= 28);    // Control Laravel ON ≥ 28
  bool finalLed = autoLed || overrideLed;

  digitalWrite(LEDPIN, finalLed ? HIGH : LOW);

  Serial.println(finalLed ? "LED : ON" : "LED : OFF");
  Serial.println("-------------------------------------");
}
