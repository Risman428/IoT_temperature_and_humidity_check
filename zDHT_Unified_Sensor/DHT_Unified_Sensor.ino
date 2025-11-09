#include <ESP8266Wifi.h>
#include <ESP8266HTTPClient.h>  //connecting webserver
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>

//confiruration wifi
const char* ssid = "Y30i";
const char* password = "12345678";
//pastikan NodeMCU dan laptop memakai jaringan yang sama


#define DHTPIN D3  // Digital pin connected to the DHT sensor | perubahan 2 => D3

#define DHTTYPE DHT22  // DHT 22 (AM2302)

DHT_Unified dht(DHTPIN, DHTTYPE);

uint32_t delayMS;

void setup() {
  Serial.begin(115200);
  delay(1000);

  // Connection to Wifi
  Serial.println();
  Serial.println("Menghubungkan ke Wifi: ");
  Serial.println(ssid);
  Wifi.begin(ssid, password);

  while (Wifi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("Wifi is Connected");
  Serial.print("IP Adress : ");
  Serial.println(Wifi.localIP());  //untuk mengetahui IP dari NodeMCU nya

  // Initialize device.
  dht.begin();
  sensor_t sensor;
  dht.temperature().getSensor(&sensor);
  dht.humidity().getSensor(&sensor);
  delayMS = sensor.min_delay / 1000;
}

void loop() {
  // Delay between measurements.
  delay(delayMS);
  // Get temperature event and print its value.
  sensors_event_t event;
  // dht.temperature().getEvent(&event);
  // if (isnan(event.temperature)) {
  //   Serial.println(F("Error reading temperature!"));
  // }
  // else {
  //   Serial.print(F("Temperature: "));
  //   Serial.print(event.temperature);
  //   Serial.println(F("°C"));
  // }
  // // Get humidity event and print its value.
  // dht.humidity().getEvent(&event);
  // if (isnan(event.relative_humidity)) {
  //   Serial.println(F("Error reading humidity!"));
  // }
  // else {
  //   Serial.print(F("Humidity: "));
  //   Serial.print(event.relative_humidity);
  //   Serial.println(F("%"));
  // }

  //read temperature
  dht.temperature().getEvent(&event);
  float temperature = event.temperature;

  //read humidity
  dht.humidity().getEvent(&event);
  float humidity = event.relative_humidity;

  //pengecekan DHT
  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Gagal membaca sensor DHT");
    return;
  }

  Serial.print("Temperature: ");
  Serial.print(temperature);
  Serial.println(" °C")

  Serial.print("Humidity: ");
  Serial.print(humidity);
  Serial.println(" %");

  //sending data to Laravel
  if (Wifi.status() == WL_CONNECTED) {
    HTPPClient http;
    WiFiClient client

      //gunakan IP adress laptop sendiri
      String url = "http://192.168.1.2/dhtiot/public/update-data/";
    url += String(temperature, 1) + "/" + String(humidity, 1);

    Serial.print("Mengirim data ke : ");
    Serial.println(url);

    http.begin(client, url);
    int httpCode = http.GET();

    if (httpCode > 0) {
      Serial.printf("HTTP Respone Code : %d\n", httpCode);
      String payload = http.getString();
      Serial.println("Response : ");
      Serial.println(payload);
    } else {
      Serial.printf("Gagal Mengirim data. Error : %s\n", http.errorToString(httpCode).c_str());
    }

    http.end();
  } else {
    Serial.println("Wifi tidak terkoneksi");
    WiFi.reconnect();
  }

  delay(3000);
}
